<?php

declare(strict_types=1);

namespace Laraforge\DatabaseStudio\Agents;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;
use Throwable;

/**
 * DatabaseManagerAgent — Domain Orchestrator for Database Schema Introspection,
 * DDL Execution (Create, Alter, Drop, Truncate), Index Management, and Data Browsing.
 */
class DatabaseManagerAgent
{
    public function __construct(
        private readonly ?string $connection = null
    ) {}

    /**
     * Get DB Connection instance.
     */
    protected function db(): \Illuminate\Database\Connection
    {
        return DB::connection($this->connection);
    }

    /**
     * Get Schema builder instance.
     */
    protected function schema(): \Illuminate\Database\Schema\Builder
    {
        return Schema::connection($this->connection);
    }

    /**
     * Get a summary list of all tables in the connected MySQL database with storage metrics.
     *
     * @return array List of table summaries with row count, storage size, engine, and collation.
     */
    public function getTablesSummary(): array
    {
        $connectionName = $this->connection ?: config('database.default');
        $dbName = config("database.connections.{$connectionName}.database");

        $query = "
            SELECT 
                TABLE_NAME as name,
                ENGINE as engine,
                TABLE_ROWS as table_rows,
                DATA_LENGTH as data_length,
                INDEX_LENGTH as index_length,
                (DATA_LENGTH + INDEX_LENGTH) as total_size,
                TABLE_COLLATION as collation,
                TABLE_COMMENT as comment,
                CREATE_TIME as created_at
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = ?
            ORDER BY TABLE_NAME ASC
        ";

        $results = $this->db()->select($query, [$dbName]);

        $tables = [];
        $totalRows = 0;
        $totalBytes = 0;

        foreach ($results as $row) {
            $rowsCount = (int) $row->table_rows;
            $sizeBytes = (int) $row->total_size;

            $totalRows += $rowsCount;
            $totalBytes += $sizeBytes;

            $tables[] = [
                'name'         => $row->name,
                'engine'       => $row->engine ?? 'InnoDB',
                'rows'         => $rowsCount,
                'data_size'    => $this->formatBytes((int) $row->data_length),
                'index_size'   => $this->formatBytes((int) $row->index_length),
                'total_size'   => $this->formatBytes($sizeBytes),
                'size_bytes'   => $sizeBytes,
                'collation'    => $row->collation ?? 'utf8mb4_unicode_ci',
                'comment'      => $row->comment,
                'created_at'   => $row->created_at,
                'is_protected' => in_array($row->name, config('database-studio.protected_tables', []), true),
            ];
        }

        return [
            'tables'       => $tables,
            'tables_count' => count($tables),
            'total_rows'   => $totalRows,
            'total_size'   => $this->formatBytes($totalBytes),
            'total_bytes'  => $totalBytes,
            'database'     => $dbName,
            'driver'       => $this->db()->getDriverName(),
        ];
    }

    /**
     * Inspect a specific table's detailed schema (columns, indexes, foreign keys).
     */
    public function getTableDetails(string $table): array
    {
        $this->ensureTableExists($table);
        $connectionName = $this->connection ?: config('database.default');
        $dbName = config("database.connections.{$connectionName}.database");

        // Columns Inspection
        $columnsRaw = $this->db()->select("
            SELECT 
                COLUMN_NAME, DATA_TYPE, COLUMN_TYPE, IS_NULLABLE, 
                COLUMN_DEFAULT, EXTRA, COLUMN_KEY, COLUMN_COMMENT
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
            ORDER BY ORDINAL_POSITION ASC
        ", [$dbName, $table]);

        $columns = array_map(function ($col) {
            return [
                'name'           => $col->COLUMN_NAME,
                'type'           => $col->DATA_TYPE,
                'full_type'      => $col->COLUMN_TYPE,
                'nullable'       => $col->IS_NULLABLE === 'YES',
                'default'        => $col->COLUMN_DEFAULT,
                'key'            => $col->COLUMN_KEY,
                'extra'          => $col->EXTRA,
                'auto_increment' => str_contains(strtolower($col->EXTRA), 'auto_increment'),
                'comment'        => $col->COLUMN_COMMENT,
            ];
        }, $columnsRaw);

        // Indexes Inspection
        $indexesRaw = $this->db()->select("SHOW INDEX FROM `{$table}`");
        $indexesGrouped = [];
        foreach ($indexesRaw as $idx) {
            $keyName = $idx->Key_name;
            if (!isset($indexesGrouped[$keyName])) {
                $indexesGrouped[$keyName] = [
                    'name'     => $keyName,
                    'unique'   => (int) $idx->Non_unique === 0,
                    'primary'  => $keyName === 'PRIMARY',
                    'type'     => $idx->Index_type,
                    'columns'  => [],
                ];
            }
            $indexesGrouped[$keyName]['columns'][] = $idx->Column_name;
        }

        // Foreign Keys Inspection
        $fkRaw = $this->db()->select("
            SELECT 
                CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$dbName, $table]);

        $foreignKeys = array_map(function ($fk) {
            return [
                'name'             => $fk->CONSTRAINT_NAME,
                'column'           => $fk->COLUMN_NAME,
                'referenced_table' => $fk->REFERENCED_TABLE_NAME,
                'referenced_column'=> $fk->REFERENCED_COLUMN_NAME,
            ];
        }, $fkRaw);

        return [
            'table'        => $table,
            'columns'      => $columns,
            'indexes'      => array_values($indexesGrouped),
            'foreign_keys' => $foreignKeys,
            'is_protected' => in_array($table, config('database-studio.protected_tables', []), true),
        ];
    }

    /**
     * Fetch paginated data rows from a specific table with optional column search & field filtering.
     */
    public function getTableData(
        string $table,
        int $page = 1,
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null
    ): array {
        $this->ensureTableExists($table);

        $query = $this->db()->table($table);

        // Retrieve Columns list
        $columns = $this->schema()->getColumnListing($table);

        // Search across string columns
        if (!empty($search)) {
            $query->where(function ($q) use ($columns, $search) {
                foreach ($columns as $col) {
                    $q->orWhere($col, 'LIKE', "%{$search}%");
                }
            });
        }

        // Structured Filters
        if (!empty($filters) && is_array($filters)) {
            foreach ($filters as $col => $val) {
                if (in_array($col, $columns, true) && $val !== null && $val !== '') {
                    $query->where($col, '=', $val);
                }
            }
        }

        $total = $query->count();
        $records = $query->forPage($page, $perPage)->get();

        return [
            'table'        => $table,
            'columns'      => $columns,
            'current_page' => $page,
            'per_page'     => $perPage,
            'total'        => $total,
            'last_page'    => (int) ceil($total / max(1, $perPage)),
            'data'         => $records,
        ];
    }

    /**
     * Export all rows matching current filters/search for CSV or Excel export.
     */
    public function exportTableData(string $table, ?string $search = null, ?array $filters = null): array
    {
        $this->ensureTableExists($table);

        $query = $this->db()->table($table);
        $columns = $this->schema()->getColumnListing($table);

        if (!empty($search)) {
            $query->where(function ($q) use ($columns, $search) {
                foreach ($columns as $col) {
                    $q->orWhere($col, 'LIKE', "%{$search}%");
                }
            });
        }

        if (!empty($filters) && is_array($filters)) {
            foreach ($filters as $col => $val) {
                if (in_array($col, $columns, true) && $val !== null && $val !== '') {
                    $query->where($col, '=', $val);
                }
            }
        }

        return [
            'columns' => $columns,
            'rows'    => $query->get(),
        ];
    }

    /**
     * Execute DDL to create a new database table dynamically.
     */
    public function createTable(array $payload): string
    {
        $tableName = $payload['table_name'];
        if ($this->schema()->hasTable($tableName)) {
            throw new Exception("Table `{$tableName}` already exists.");
        }

        $this->schema()->create($tableName, function (\Illuminate\Database\Schema\Blueprint $table) use ($payload) {
            foreach ($payload['columns'] as $col) {
                $type   = strtolower($col['type']);
                $name   = $col['name'];
                $length = isset($col['length']) && $col['length'] !== '' ? (int) $col['length'] : null;

                if (!empty($col['primary']) && !empty($col['auto_increment'])) {
                    $table->id($name);
                    continue;
                }

                $columnObj = match ($type) {
                    'integer', 'int'    => $table->integer($name),
                    'biginteger', 'bigint' => $table->bigInteger($name),
                    'smallinteger'      => $table->smallInteger($name),
                    'tinyinteger'       => $table->tinyInteger($name),
                    'string', 'varchar' => $table->string($name, $length ?: 255),
                    'text'              => $table->text($name),
                    'longtext'          => $table->longText($name),
                    'boolean', 'bool'   => $table->boolean($name),
                    'date'              => $table->date($name),
                    'datetime'          => $table->dateTime($name),
                    'timestamp'         => $table->timestamp($name),
                    'decimal'           => $table->decimal($name, 10, 2),
                    'float'             => $table->float($name),
                    'json'              => $table->json($name),
                    'enum'              => $table->string($name),
                    default             => $table->string($name),
                };

                if (!empty($col['nullable'])) {
                    $columnObj->nullable();
                }

                if (array_key_exists('default', $col) && $col['default'] !== null && $col['default'] !== '') {
                    $columnObj->default($col['default']);
                }
            }

            // Timestamps helper
            if (!empty($payload['add_timestamps'])) {
                $table->timestamps();
            }
        });

        return "Table `{$tableName}` created successfully.";
    }

    /**
     * Add an index to an existing table.
     */
    public function addIndex(string $tableName, string $indexName, string $type, array $columns): string
    {
        $this->ensureTableExists($tableName);

        $this->schema()->table($tableName, function (\Illuminate\Database\Schema\Blueprint $table) use ($indexName, $type, $columns) {
            match (strtoupper($type)) {
                'UNIQUE'   => $table->unique($columns, $indexName),
                'PRIMARY'  => $table->primary($columns, $indexName),
                'FULLTEXT' => $table->fullText($columns, $indexName),
                default    => $table->index($columns, $indexName),
            };
        });

        return "Index `{$indexName}` created successfully on `{$tableName}`.";
    }

    /**
     * Drop an index from an existing table.
     */
    public function dropIndex(string $tableName, string $indexName): string
    {
        $this->ensureTableExists($tableName);

        $this->schema()->table($tableName, function (\Illuminate\Database\Schema\Blueprint $table) use ($indexName) {
            $table->dropIndex($indexName);
        });

        return "Index `{$indexName}` dropped successfully from `{$tableName}`.";
    }

    /**
     * Truncate a table safely.
     */
    public function truncateTable(string $tableName): string
    {
        $this->ensureTableExists($tableName);
        $this->ensureNotProtected($tableName);

        $this->db()->statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->db()->table($tableName)->truncate();
        $this->db()->statement('SET FOREIGN_KEY_CHECKS=1;');

        return "Table `{$tableName}` truncated successfully.";
    }

    /**
     * Drop a table safely.
     */
    public function dropTable(string $tableName): string
    {
        $this->ensureTableExists($tableName);
        $this->ensureNotProtected($tableName);

        $this->db()->statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->schema()->drop($tableName);
        $this->db()->statement('SET FOREIGN_KEY_CHECKS=1;');

        return "Table `{$tableName}` dropped successfully.";
    }

    /**
     * Bulk truncate multiple tables.
     */
    public function bulkTruncate(array $tables): string
    {
        $count = 0;
        $this->db()->statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($tables as $t) {
            if ($this->schema()->hasTable($t) && !in_array($t, config('database-studio.protected_tables', []), true)) {
                $this->db()->table($t)->truncate();
                $count++;
            }
        }
        $this->db()->statement('SET FOREIGN_KEY_CHECKS=1;');

        return "Successfully truncated {$count} table(s).";
    }

    /**
     * Bulk drop multiple tables.
     */
    public function bulkDrop(array $tables): string
    {
        $count = 0;
        $this->db()->statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($tables as $t) {
            if ($this->schema()->hasTable($t) && !in_array($t, config('database-studio.protected_tables', []), true)) {
                $this->schema()->drop($t);
                $count++;
            }
        }
        $this->db()->statement('SET FOREIGN_KEY_CHECKS=1;');

        return "Successfully dropped {$count} table(s).";
    }

    /**
     * Drop specific columns from a table.
     */
    public function dropColumns(string $table, array $columns): string
    {
        $this->ensureTableExists($table);
        $this->ensureNotProtected($table);

        $this->schema()->table($table, function (\Illuminate\Database\Schema\Blueprint $t) use ($columns) {
            $t->dropColumn($columns);
        });

        return "Columns dropped successfully from `{$table}`.";
    }

    /**
     * Modify column definition via raw SQL ALTER TABLE statement.
     */
    public function modifyColumn(string $table, string $column, array $data): string
    {
        $this->ensureTableExists($table);

        $targetName = !empty($data['new_name']) ? $data['new_name'] : $column;
        $type       = strtoupper($data['type']);
        $length     = !empty($data['length']) ? "({$data['length']})" : '';
        $unsigned   = !empty($data['unsigned']) ? 'UNSIGNED' : '';
        $nullable   = !empty($data['nullable']) ? 'NULL' : 'NOT NULL';

        $defaultStr = '';
        if (array_key_exists('default', $data) && $data['default'] !== null && $data['default'] !== '') {
            $defVal = $data['default'];
            if (strtoupper($defVal) === 'NULL') {
                $defaultStr = 'DEFAULT NULL';
            } elseif (in_array(strtoupper($defVal), ['CURRENT_TIMESTAMP', 'NOW()'], true)) {
                $defaultStr = 'DEFAULT CURRENT_TIMESTAMP';
            } else {
                $defaultStr = "DEFAULT " . $this->db()->getPdo()->quote($defVal);
            }
        }

        $commentStr = '';
        if (!empty($data['comment'])) {
            $commentStr = "COMMENT " . $this->db()->getPdo()->quote($data['comment']);
        }

        $sql = "ALTER TABLE `{$table}` CHANGE COLUMN `{$column}` `{$targetName}` {$type}{$length} {$unsigned} {$nullable} {$defaultStr} {$commentStr}";
        $this->db()->statement($sql);

        return "Column `{$column}` modified successfully in table `{$table}`.";
    }

    /**
     * Execute arbitrary raw SQL query and capture tabular or mutation results.
     */
    public function executeQuery(string $sql): array
    {
        $trimmed = trim($sql);
        $firstWord = strtoupper(strtok($trimmed, " \n\r\t"));

        $startTime = microtime(true);

        if (in_array($firstWord, ['SELECT', 'SHOW', 'EXPLAIN', 'DESCRIBE', 'PRAGMA'], true)) {
            $results = $this->db()->select($sql);
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            $columns = [];
            if (!empty($results)) {
                $columns = array_keys((array) $results[0]);
            }

            return [
                'type'           => 'SELECT',
                'columns'        => $columns,
                'rows'           => $results,
                'affected_rows'  => count($results),
                'execution_time' => "{$executionTime} ms",
            ];
        }

        // Mutation / DDL queries
        $affected = $this->db()->affectingStatement($sql);
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        return [
            'type'           => $firstWord,
            'columns'        => [],
            'rows'           => [],
            'affected_rows'  => $affected,
            'execution_time' => "{$executionTime} ms",
        ];
    }

    /**
     * Ensure table exists or throw 404 Exception.
     */
    protected function ensureTableExists(string $table): void
    {
        if (!$this->schema()->hasTable($table)) {
            throw new Exception("Table `{$table}` does not exist.");
        }
    }

    /**
     * Guard protected tables.
     */
    protected function ensureNotProtected(string $table): void
    {
        if (in_array($table, config('database-studio.protected_tables', []), true)) {
            throw new Exception("Action denied. Table `{$table}` is a protected system table.");
        }
    }

    /**
     * Human readable byte formatting.
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow   = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
