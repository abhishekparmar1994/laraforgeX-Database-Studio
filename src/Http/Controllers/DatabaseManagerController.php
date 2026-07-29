<?php

declare(strict_types=1);

namespace Laraforge\DatabaseStudio\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laraforge\DatabaseStudio\Agents\DatabaseManagerAgent;
use Laraforge\DatabaseStudio\Http\Requests\CreateTableRequest;
use Laraforge\DatabaseStudio\Http\Requests\ExecuteQueryRequest;
use Laraforge\DatabaseStudio\Http\Requests\ManageIndexRequest;
use Throwable;

/**
 * DatabaseManagerController — API Controller for managing MySQL Database Tables,
 * Schema Inspection, Index Operations, and Record Browsing.
 */
class DatabaseManagerController extends Controller
{
    public function __construct(
        private readonly DatabaseManagerAgent $agent
    ) {}

    /**
     * List all database tables and metrics.
     */
    public function index(): JsonResponse
    {
        try {
            $summary = $this->agent->getTablesSummary();
            return response()->json([
                'success' => true,
                'data'    => $summary,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Inspect schema, indexes, and foreign keys for a single table.
     */
    public function show(string $table): JsonResponse
    {
        try {
            $details = $this->agent->getTableDetails($table);
            return response()->json([
                'success' => true,
                'data'    => $details,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Fetch paginated records from a specific table.
     */
    public function data(string $table, Request $request): JsonResponse
    {
        try {
            $page = (int) $request->query('page', 1);
            $perPage = (int) $request->query('per_page', 15);
            $search = $request->query('search');

            $rawFilters = $request->query('filters') ?? $request->input('filters');
            $filters = null;

            if (!empty($rawFilters)) {
                if (is_string($rawFilters)) {
                    $filters = json_decode($rawFilters, true);
                } elseif (is_array($rawFilters)) {
                    $filters = $rawFilters;
                }
            }

            $data = $this->agent->getTableData($table, $page, $perPage, $search, $filters);

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export all filtered table rows as CSV or Excel stream download.
     */
    public function exportData(string $table, Request $request)
    {
        $format = strtolower($request->query('format', 'csv'));
        $search = $request->query('search');

        $rawFilters = $request->query('filters');
        $filters = null;
        if (!empty($rawFilters)) {
            $decoded = json_decode($rawFilters, true);
            if (is_array($decoded)) {
                $filters = $decoded;
            }
        }

        try {
            $export   = $this->agent->exportTableData($table, $search, $filters);
            $columns  = $export['columns'];
            $rows     = $export['rows'];
            $filename = $table . '_export_' . date('Ymd_His');

            if ($format === 'excel') {
                $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                $xml .= "<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"";
                $xml .= " xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\">\n";
                $xml .= "<Worksheet ss:Name=\"" . htmlspecialchars($table) . "\">\n<Table>\n";

                $xml .= "<Row>";
                foreach ($columns as $col) {
                    $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($col) . '</Data></Cell>';
                }
                $xml .= "</Row>\n";

                foreach ($rows as $row) {
                    $arr  = (array) $row;
                    $xml .= "<Row>";
                    foreach ($columns as $col) {
                        $val  = $arr[$col] ?? '';
                        $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars((string) $val) . '</Data></Cell>';
                    }
                    $xml .= "</Row>\n";
                }

                $xml .= "</Table>\n</Worksheet>\n</Workbook>";

                return response($xml, 200, [
                    'Content-Type'        => 'application/vnd.ms-excel',
                    'Content-Disposition' => "attachment; filename=\"{$filename}.xls\"",
                    'Cache-Control'       => 'max-age=0',
                ]);
            }

            // Default: CSV
            $handle = fopen('php://temp', 'r+');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $columns);
            foreach ($rows as $row) {
                $arr = (array) $row;
                fputcsv($handle, array_map(fn ($col) => $arr[$col] ?? '', $columns));
            }
            rewind($handle);
            $csv = stream_get_contents($handle);
            fclose($handle);

            return response($csv, 200, [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
                'Cache-Control'       => 'max-age=0',
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new table via DDL.
     */
    public function store(CreateTableRequest $request): JsonResponse
    {
        try {
            $message = $this->agent->createTable($request->validated());
            return response()->json([
                'success' => true,
                'message' => $message,
            ], 201);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Add an index to an existing table.
     */
    public function addIndex(string $table, ManageIndexRequest $request): JsonResponse
    {
        try {
            $val = $request->validated();
            $message = $this->agent->addIndex($table, $val['index_name'], $val['index_type'], $val['columns']);
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Drop an index from an existing table.
     */
    public function dropIndex(string $table, string $indexName): JsonResponse
    {
        try {
            $message = $this->agent->dropIndex($table, $indexName);
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Truncate a table.
     */
    public function truncate(string $table): JsonResponse
    {
        try {
            $message = $this->agent->truncateTable($table);
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Drop a table completely.
     */
    public function destroy(string $table): JsonResponse
    {
        try {
            $message = $this->agent->dropTable($table);
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Perform bulk action (truncate / drop) on selected tables.
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => ['required', 'string', 'in:truncate,drop'],
            'tables' => ['required', 'array', 'min:1'],
            'tables.*' => ['required', 'string', 'regex:/^[a-zA-Z0-9_]+$/'],
        ]);

        try {
            $action = $request->input('action');
            $tables = $request->input('tables');

            if ($action === 'truncate') {
                $msg = $this->agent->bulkTruncate($tables);
            } else {
                $msg = $this->agent->bulkDrop($tables);
            }

            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Drop selected columns from a table.
     */
    public function dropColumns(string $table, Request $request): JsonResponse
    {
        $request->validate([
            'columns' => ['required', 'array', 'min:1'],
            'columns.*' => ['required', 'string', 'regex:/^[a-zA-Z0-9_]+$/'],
        ]);

        try {
            $msg = $this->agent->dropColumns($table, $request->input('columns'));
            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Modify an existing column's definition.
     */
    public function modifyColumn(string $table, string $column, Request $request): JsonResponse
    {
        $request->validate([
            'type'     => ['required', 'string', 'regex:/^[a-zA-Z]+$/'],
            'length'   => ['nullable', 'string', 'regex:/^[0-9,]+$/'],
            'nullable' => ['nullable', 'boolean'],
            'unsigned' => ['nullable', 'boolean'],
            'default'  => ['nullable', 'string', 'max:500'],
            'new_name' => ['nullable', 'string', 'regex:/^[a-zA-Z0-9_]+$/'],
            'comment'  => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $msg = $this->agent->modifyColumn($table, $column, $request->only([
                'type', 'length', 'nullable', 'unsigned', 'default', 'new_name', 'comment',
            ]));

            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Execute custom raw SQL query.
     */
    public function executeSql(ExecuteQueryRequest $request): JsonResponse
    {
        try {
            $res = $this->agent->executeQuery($request->input('sql'));
            return response()->json([
                'success' => true,
                'data'    => $res,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
