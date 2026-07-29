@extends('database-studio::layouts.app')

@section('title', 'Laraforge — Navicat Grade SQL Query Studio')

@section('content')
  <div class="space-y-4 font-sans w-full select-none">

    <!-- Header Hero Banner -->
    <div
      class="theme-hero-banner bg-gradient-to-r from-slate-900 via-brand-900 to-indigo-900 rounded-2xl p-5 text-white shadow-xl relative overflow-hidden">
      <div
        class="absolute right-0 top-0 translate-x-8 -translate-y-8 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none">
      </div>
      <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-brand-300 text-xs font-bold uppercase tracking-widest mb-1">
            <i class="fa-solid fa-laptop-code text-amber-300"></i> NAVICAT & HEIDISQL GRADE DESKTOP STUDIO
          </div>
          <h1 class="text-xl font-extrabold tracking-tight flex items-center gap-2">
            <i class="fa-solid fa-terminal text-emerald-400"></i> Navicat SQL Query Console
          </h1>
          <p class="text-xs text-slate-300 mt-0.5">
            Full-featured SQL Query Editor with connection selectors, query formatter, row selector, and CSV/Excel
            exporter.
          </p>
        </div>
        <div class="shrink-0 flex items-center gap-2">
          <a href="{{ url(config('database-studio.path', 'database-studio')) }}"
            class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/20 inline-flex items-center gap-2 no-underline">
            <i class="fa-solid fa-arrow-left text-xs"></i> Back to Database Studio
          </a>
        </div>
      </div>
    </div>

    <!-- Navicat Top IDE Toolbar -->
    <div
      class="bg-white border border-slate-200/90 rounded-2xl p-3 shadow-sm flex flex-wrap items-center justify-between gap-3 text-xs font-sans relative">

      <!-- Connection & Database Selectors -->
      <div class="flex items-center gap-2.5 flex-wrap">
        <div
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 font-mono font-bold text-xs">
          <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
          <i class="fa-solid fa-server text-slate-400 text-[11px]"></i>
          <span>localhost_3306</span>
        </div>

        <div
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 font-mono font-bold text-xs">
          <i class="fa-solid fa-database text-brand-600 text-[11px]"></i>
          <span id="active-db-name-label">Default DB</span>
        </div>
      </div>

      <!-- Action Buttons Bar -->
      <div class="flex items-center gap-2 flex-wrap">
        <button type="button" id="btn-navicat-run"
          class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs transition shadow-sm inline-flex items-center gap-1.5 cursor-pointer border-0">
          <i class="fa-solid fa-play text-xs text-emerald-200"></i> Run Query
        </button>

        <button type="button" id="btn-navicat-stop"
          class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition border border-slate-200 inline-flex items-center gap-1.5 cursor-pointer">
          <i class="fa-solid fa-square text-rose-500 text-[10px]"></i> Stop
        </button>

        <div class="h-4 w-px bg-slate-200 mx-1"></div>

        <button type="button" id="btn-navicat-explain"
          class="px-3 py-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold text-xs border border-slate-200 transition cursor-pointer inline-flex items-center gap-1.5">
          <i class="fa-solid fa-sitemap text-indigo-500 text-xs"></i> Explain
        </button>

        <button type="button" id="btn-navicat-beautify"
          class="px-3 py-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold text-xs border border-slate-200 transition cursor-pointer inline-flex items-center gap-1.5">
          <i class="fa-solid fa-wand-magic-sparkles text-amber-500 text-xs"></i> Beautify SQL
        </button>

        <!-- Export Result Dropdown Button -->
        <div class="relative inline-block text-left" id="export-dropdown-wrapper">
          <button type="button" id="btn-toggle-export-menu"
            class="px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs border border-emerald-300 transition cursor-pointer inline-flex items-center gap-1.5">
            <i class="fa-solid fa-file-export text-emerald-600 text-xs"></i> Export Result <i
              class="fa-solid fa-chevron-down text-[10px] text-emerald-500"></i>
          </button>

          <div id="export-menu-dropdown"
            class="hidden absolute right-0 mt-2 w-52 rounded-xl bg-white border border-slate-200 shadow-xl z-50 font-sans text-xs divide-y divide-slate-100 overflow-hidden">
            <div
              class="p-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50 border-b border-slate-100">
              Export Format Options
            </div>
            <div class="py-1">
              <button type="button"
                class="btn-do-export w-full text-left flex items-center gap-2.5 px-4 py-2.5 text-slate-700 hover:bg-slate-50 font-semibold transition cursor-pointer"
                data-format="csv">
                <i class="fa-solid fa-file-csv text-emerald-600 text-base"></i>
                <div>
                  <span class="block text-xs">Export to CSV</span>
                  <span class="text-[10px] text-slate-400 font-normal">Comma Separated (.csv)</span>
                </div>
              </button>
              <button type="button"
                class="btn-do-export w-full text-left flex items-center gap-2.5 px-4 py-2.5 text-slate-700 hover:bg-slate-50 font-semibold transition cursor-pointer"
                data-format="excel">
                <i class="fa-solid fa-file-excel text-emerald-700 text-base"></i>
                <div>
                  <span class="block text-xs">Export to Excel</span>
                  <span class="text-[10px] text-slate-400 font-normal">Excel Workbook (.csv)</span>
                </div>
              </button>
            </div>
          </div>
        </div>

        <button type="button" id="btn-navicat-save"
          class="px-3 py-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold text-xs border border-slate-200 transition cursor-pointer inline-flex items-center gap-1.5">
          <i class="fa-regular fa-floppy-disk text-slate-500 text-xs"></i> Save .SQL
        </button>

        <button type="button" id="btn-navicat-clear"
          class="px-3 py-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold text-xs border border-slate-200 transition cursor-pointer inline-flex items-center gap-1.5">
          <i class="fa-solid fa-eraser text-slate-400 text-xs"></i> Clear
        </button>
      </div>
    </div>

    <!-- Main Navicat 2-Column Workspace -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 items-start">
      <!-- LEFT COLUMN -->
      <div class="lg:col-span-3 space-y-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-lg relative">
          <div
            class="bg-slate-950 px-4 py-2 border-b border-slate-800 flex items-center justify-between text-[11px] font-mono text-slate-400">
            <span class="flex items-center gap-2">
              <i class="fa-solid fa-file-code text-brand-400"></i> Query_1.sql
            </span>
            <span class="text-slate-500">Press <b class="text-slate-300">Ctrl + Enter</b> to execute query</span>
          </div>

          <div class="flex min-h-[220px]">
            <div id="editor-gutter"
              class="w-10 bg-slate-950/70 border-r border-slate-800 py-4 text-center font-mono text-[11px] text-slate-600 select-none leading-relaxed">
              1
            </div>
            <textarea id="sql-console-editor" rows="9" spellcheck="false"
              class="w-full bg-slate-900 text-emerald-300 font-mono text-xs p-4 leading-relaxed focus:outline-none focus:ring-0 resize-y border-0"
              placeholder="SELECT * FROM users LIMIT 25;"></textarea>
          </div>
        </div>

        <div id="sql-console-output" class="bg-white border border-slate-200/90 rounded-2xl p-5 space-y-4 shadow-sm">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-3">
              <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-table-list text-brand-600"></i> Query Execution Output
              </h3>
              <span id="results-selected-count-badge"
                class="hidden px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-sky-100 text-sky-700 border border-sky-200">
                0 rows selected
              </span>
            </div>
            <div class="flex items-center gap-2">
              <span id="sql-exec-badge"
                class="px-2.5 py-1 rounded-md text-[11px] font-bold font-mono bg-slate-100 text-slate-600 border border-slate-200">
                Ready
              </span>
            </div>
          </div>

          <div id="sql-console-result-container"
            class="border border-slate-200 rounded-xl overflow-hidden overflow-x-auto shadow-xs text-xs font-mono bg-white min-h-[120px]">
            <div class="p-8 text-center text-slate-400 font-sans italic">
              Click <b class="text-emerald-600">"▶ Run Query"</b> or press <b class="text-slate-700 font-mono">Ctrl + Enter</b> to view SQL execution results.
            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT COLUMN: Code Snippets Sidebar -->
      <div class="bg-white border border-slate-200/90 rounded-2xl p-4 space-y-4 shadow-sm">
        <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
          <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
            <i class="fa-solid fa-tags text-brand-500"></i> Code Snippets Library
          </h3>
        </div>

        <div class="space-y-2">
          <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="snippet-search-input"
              class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-brand-500 transition"
              placeholder="Search snippets…">
          </div>

          <select id="snippet-category-select"
            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 focus:outline-none">
            <option value="ALL">All Labels & Categories</option>
            <option value="DML">DML (Data Queries)</option>
            <option value="DDL">DDL (Schema Definition)</option>
            <option value="FLOW">Flow Control</option>
            <option value="SYSTEM">System & Maintenance</option>
          </select>
        </div>

        <div id="snippets-list-container" class="space-y-2.5 max-h-[500px] overflow-y-auto pr-1">
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script>
  $(document.ready ? $(document) : $(window)).ready(function () {
    const apiBasePath = "{{ url(config('database-studio.api_prefix', 'api/v1/database-manager')) }}";
    let _lastResultData = null;

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    const SNIPPETS = [
      { label: 'SELECT Syntax', category: 'DML', desc: 'Retrieve rows selected from one or more tables', sql: 'SELECT * FROM table_name WHERE 1=1 ORDER BY id DESC LIMIT 25;' },
      { label: 'INSERT Syntax', category: 'DML', desc: 'Insert new rows into an existing table', sql: 'INSERT INTO table_name (col1, col2) VALUES (\'val1\', \'val2\');' },
      { label: 'UPDATE Syntax', category: 'DML', desc: 'Updates columns of existing rows in the named table', sql: 'UPDATE table_name SET col1 = \'val1\' WHERE id = 1;' },
      { label: 'DELETE Syntax', category: 'DML', desc: 'Delete rows from specified table', sql: 'DELETE FROM table_name WHERE id = 1;' },
      { label: 'INNER JOIN Syntax', category: 'DML', desc: 'Join two tables on foreign key match', sql: 'SELECT t1.*, t2.name FROM table1 t1\nINNER JOIN table2 t2 ON t1.t2_id = t2.id\nLIMIT 25;' },
      { label: 'CREATE TABLE Syntax', category: 'DDL', desc: 'Create a new table definition', sql: 'CREATE TABLE new_table (\n  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,\n  name VARCHAR(255) NOT NULL,\n  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP\n) ENGINE=InnoDB;' },
      { label: 'ALTER TABLE Add Column', category: 'DDL', desc: 'Add a new column to existing table', sql: 'ALTER TABLE table_name ADD COLUMN new_col VARCHAR(255) NULL AFTER id;' },
      { label: 'DROP TABLE Syntax', category: 'DDL', desc: 'Drop table completely', sql: 'DROP TABLE IF EXISTS table_name;' },
      { label: 'SHOW TABLES', category: 'SYSTEM', desc: 'List all database tables', sql: 'SHOW TABLES;' },
      { label: 'SHOW PROCESSLIST', category: 'SYSTEM', desc: 'List active database worker threads', sql: 'SHOW PROCESSLIST;' }
    ];

    renderSnippets(SNIPPETS);

    $('#snippet-search-input, #snippet-category-select').on('input change', function () {
      const q = $('#snippet-search-input').val().toLowerCase().trim();
      const cat = $('#snippet-category-select').val();

      const filtered = $.grep(SNIPPETS, function (s) {
        const matchCat = (cat === 'ALL' || s.category === cat);
        const matchQ = (!q || s.label.toLowerCase().includes(q) || s.desc.toLowerCase().includes(q));
        return matchCat && matchQ;
      });

      renderSnippets(filtered);
    });

    $(document).on('click', '.snippet-item', function () {
      const sql = $(this).data('sql');
      $('#sql-console-editor').val(sql);
      updateLineNumbers();
    });

    $('#btn-navicat-run').on('click', executeQuery);
    $('#btn-navicat-explain').on('click', function () {
      const sql = $('#sql-console-editor').val().trim();
      if (!sql) return;
      $('#sql-console-editor').val(`EXPLAIN ${sql}`);
      executeQuery();
    });

    $('#btn-navicat-beautify').on('click', function () {
      let sql = $('#sql-console-editor').val();
      sql = sql.replace(/\s+/g, ' ')
               .replace(/\s*,/g, ',')
               .replace(/\s*SELECT\s*/gi, 'SELECT\n  ')
               .replace(/\s*FROM\s*/gi, '\nFROM\n  ')
               .replace(/\s*WHERE\s*/gi, '\nWHERE\n  ')
               .replace(/\s*ORDER BY\s*/gi, '\nORDER BY\n  ')
               .replace(/\s*LIMIT\s*/gi, '\nLIMIT ');
      $('#sql-console-editor').val(sql.trim());
      updateLineNumbers();
    });

    $('#btn-navicat-clear').on('click', function () {
      $('#sql-console-editor').val('');
      updateLineNumbers();
    });

    $('#btn-toggle-export-menu').on('click', function (e) {
      e.stopPropagation();
      $('#export-menu-dropdown').toggleClass('hidden');
    });

    $(document).on('click', function (e) {
      if (!$(e.target).closest('#export-dropdown-wrapper').length) {
        $('#export-menu-dropdown').addClass('hidden');
      }
    });

    $('.btn-do-export').on('click', function () {
      const format = $(this).data('format');
      $('#export-menu-dropdown').addClass('hidden');
      if (!_lastResultData || !_lastResultData.rows || _lastResultData.rows.length === 0) {
        window.showToast('warning', 'No query results available to export.');
        return;
      }
      exportResultData(_lastResultData, format);
    });

    $('#sql-console-editor').on('input keyup', updateLineNumbers);

    function updateLineNumbers() {
      const lines = $('#sql-console-editor').val().split('\n').length;
      let html = '';
      for (let i = 1; i <= Math.max(1, lines); i++) {
        html += i + '<br>';
      }
      $('#editor-gutter').html(html);
    }

    function renderSnippets(list) {
      const $c = $('#snippets-list-container');
      if (list.length === 0) {
        $c.html('<div class="p-4 text-center text-xs text-slate-400 italic">No snippets found matching filters.</div>');
        return;
      }
      let html = '';
      $.each(list, function (i, s) {
        html += `
          <div data-sql="${s.sql.replace(/"/g, '&quot;')}" class="snippet-item p-2.5 rounded-xl border border-slate-200 hover:border-brand-300 hover:bg-brand-50/50 transition cursor-pointer group">
            <div class="flex items-center justify-between mb-1">
              <span class="text-xs font-bold text-slate-800 group-hover:text-brand-600 transition">${s.label}</span>
              <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase bg-slate-100 text-slate-600 border border-slate-200">${s.category}</span>
            </div>
            <p class="text-[11px] text-slate-500 font-sans line-clamp-1">${s.desc}</p>
          </div>
        `;
      });
      $c.html(html);
    }

    function executeQuery() {
      const sql = $('#sql-console-editor').val().trim();
      if (!sql) {
        window.showToast('warning', 'Please enter an SQL query to execute.');
        return;
      }

      const $container = $('#sql-console-result-container');
      const $badge = $('#sql-exec-badge');

      $badge.attr('class', 'px-2.5 py-1 rounded-md text-[11px] font-bold font-mono bg-amber-100 text-amber-700 border border-amber-200')
            .html('<i class="fa-solid fa-circle-notch fa-spin text-xs"></i> Executing');

      $container.html('<div class="p-8 text-center text-slate-400 font-sans"><i class="fa-solid fa-circle-notch fa-spin text-lg mb-2"></i><p>Executing SQL query on database…</p></div>');

      $.ajax({
        url: `${apiBasePath}/execute-sql`,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ sql: sql }),
        success: function (res) {
          if (res.success) {
            _lastResultData = res.data;
            $badge.attr('class', 'px-2.5 py-1 rounded-md text-[11px] font-bold font-mono bg-emerald-100 text-emerald-700 border border-emerald-200')
                  .text(`OK (${res.data.execution_time})`);

            renderQueryResult(res.data);
          }
        },
        error: function (xhr) {
          $badge.attr('class', 'px-2.5 py-1 rounded-md text-[11px] font-bold font-mono bg-rose-100 text-rose-700 border border-rose-200')
                .text('ERROR');
          const err = xhr.responseJSON?.message || 'Query execution failed.';
          $container.html(`
            <div class="p-5 bg-rose-50 text-rose-700 font-mono text-xs leading-relaxed">
              <i class="fa-solid fa-triangle-exclamation text-rose-600 mr-2 text-sm"></i> ${err}
            </div>
          `);
        }
      });
    }

    function renderQueryResult(d) {
      const $container = $('#sql-console-result-container');

      if (d.type === 'SELECT' || d.type === 'SHOW' || d.type === 'EXPLAIN' || d.type === 'DESCRIBE') {
        const rows = d.rows || [];
        if (rows.length === 0) {
          $container.html(`<div class="p-6 text-center text-slate-400 italic font-sans">Query executed successfully in ${d.execution_time}. 0 rows returned.</div>`);
          return;
        }

        const cols = d.columns || Object.keys(rows[0]);

        let headerHtml = `<thead class="bg-slate-100 border-b border-slate-200 text-[10px] uppercase font-bold text-slate-500 sticky top-0"><tr>`;
        $.each(cols, function (i, c) { headerHtml += `<th class="p-3 font-mono">${c}</th>`; });
        headerHtml += `</tr></thead>`;

        let bodyHtml = `<tbody class="divide-y divide-slate-100 text-xs">`;
        $.each(rows, function (i, r) {
          bodyHtml += `<tr class="hover:bg-indigo-50/30 transition">`;
          $.each(cols, function (j, c) {
            const val = r[c];
            const display = (val === null || val === undefined)
              ? `<span class="text-slate-300 font-sans italic">NULL</span>`
              : String(val).length > 120
                ? `<span title="${String(val).replace(/"/g, '&quot;')}">${String(val).substring(0, 120)}…</span>`
                : val;
            bodyHtml += `<td class="p-3 max-w-[200px] truncate">${display}</td>`;
          });
          bodyHtml += `</tr>`;
        });
        bodyHtml += `</tbody>`;

        $container.html(`<table class="w-full text-left border-collapse">${headerHtml}${bodyHtml}</table>`);
      } else {
        $container.html(`
          <div class="p-5 bg-emerald-50 text-emerald-800 text-xs font-sans font-medium flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
            <div>
              <div class="font-bold text-sm">Query Executed Successfully</div>
              <div class="text-emerald-700 text-xs mt-0.5">Affected rows: <b>${d.affected_rows}</b> · Time: <b>${d.execution_time}</b></div>
            </div>
          </div>
        `);
      }
    }

    function exportResultData(data, format) {
      const rows = data.rows || [];
      const cols = data.columns || (rows[0] ? Object.keys(rows[0]) : []);
      if (rows.length === 0) return;

      if (format === 'csv') {
        let csv = cols.join(',') + '\n';
        $.each(rows, function (i, r) {
          const rowVals = cols.map(function (c) {
            let v = r[c] !== null && r[c] !== undefined ? String(r[c]) : '';
            if (v.includes(',') || v.includes('"') || v.includes('\n')) {
              v = '"' + v.replace(/"/g, '""') + '"';
            }
            return v;
          });
          csv += rowVals.join(',') + '\n';
        });

        const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const $a = $('<a>').attr({ href: url, download: `query_export_${Date.now()}.csv` }).appendTo('body');
        $a[0].click();
        $a.remove();
      }
    }
  });
</script>
@endsection
