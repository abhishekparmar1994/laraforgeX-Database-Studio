@extends('database-studio::layouts.app')

@section('title', 'Laraforge — Database Studio & Table Manager')

@section('content')
  <div class="space-y-6 font-sans relative">

    <!-- Header Hero Banner -->
    <div class="theme-hero-banner bg-gradient-to-r from-brand-900 via-brand-700 to-indigo-800 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
      <div class="absolute right-0 top-0 translate-x-8 -translate-y-8 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
      <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-brand-200 text-xs font-bold uppercase tracking-widest mb-1">
            <i class="fa-solid fa-table-cells text-amber-300"></i> NAVICAT & PHPMYADMIN GRADE GUI
          </div>
          <h1 class="text-2xl font-extrabold tracking-tight">Database Studio & Table Explorer</h1>
          <p class="text-sm text-brand-100 mt-1 max-w-2xl">
            Inspect, create, alter database tables, execute custom SQL queries, perform bulk actions, and browse live records.
          </p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
          <a href="{{ url(config('database-studio.path', 'database-studio') . '/console') }}"
            class="px-3.5 py-2.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-200 font-bold text-xs transition border border-amber-400/30 inline-flex items-center gap-2 cursor-pointer no-underline">
            <i class="fa-solid fa-terminal text-xs"></i> SQL Console Page
          </a>
          <button type="button" id="btn-refresh-tables"
            class="px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/20 inline-flex items-center gap-2 cursor-pointer">
            <i class="fa-solid fa-rotate text-xs"></i> Refresh
          </button>
          <a href="{{ url(config('database-studio.path', 'database-studio') . '/create') }}"
            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-extrabold text-xs transition shadow-lg shadow-emerald-900/30 inline-flex items-center gap-2 cursor-pointer no-underline border-0">
            <i class="fa-solid fa-plus text-sm"></i> Create New Table
          </a>
        </div>
      </div>
    </div>

    <!-- Summary Metrics Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="bg-white border border-slate-200/80 p-4 rounded-2xl shadow-sm flex items-center justify-between">
        <div>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Database Tables</p>
          <h3 class="text-2xl font-black text-slate-900 mt-0.5" id="metric-tables-count">—</h3>
        </div>
        <div class="h-10 w-10 rounded-xl bg-cyan-50 border border-cyan-100 flex items-center justify-center text-cyan-600">
          <i class="fa-solid fa-table-cells text-base"></i>
        </div>
      </div>

      <div class="bg-white border border-slate-200/80 p-4 rounded-2xl shadow-sm flex items-center justify-between">
        <div>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Database Rows</p>
          <h3 class="text-2xl font-black text-slate-900 mt-0.5" id="metric-total-rows">—</h3>
        </div>
        <div class="h-10 w-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
          <i class="fa-solid fa-list-ol text-base"></i>
        </div>
      </div>

      <div class="bg-white border border-slate-200/80 p-4 rounded-2xl shadow-sm flex items-center justify-between">
        <div>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Storage Size</p>
          <h3 class="text-2xl font-black text-slate-900 mt-0.5" id="metric-total-size">—</h3>
        </div>
        <div class="h-10 w-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
          <i class="fa-solid fa-hard-drive text-base"></i>
        </div>
      </div>

      <div class="bg-white border border-slate-200/80 p-4 rounded-2xl shadow-sm flex items-center justify-between">
        <div>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Database Engine</p>
          <h3 class="text-sm font-black text-slate-900 mt-1" id="metric-db-name">MySQL InnoDB</h3>
        </div>
        <div class="h-10 w-10 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
          <i class="fa-solid fa-database text-base"></i>
        </div>
      </div>
    </div>

    <!-- Filter & Explorer Bar -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
      <div class="relative w-full md:w-80">
        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
        <input type="text" id="search-table-input"
          class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-brand-500 transition"
          placeholder="Filter database tables by name…">
      </div>
      <div class="flex items-center gap-3 text-xs font-semibold text-slate-500 w-full md:w-auto justify-end">
        <span id="tables-filtered-count" class="text-slate-400 font-mono text-[11px]">Showing all tables</span>
      </div>
    </div>

    <!-- Sticky Bulk Action Bar for Tables (Clean White Theme) -->
    <div id="table-bulk-bar" class="hidden sticky top-4 z-30 bg-white border border-slate-200 rounded-2xl p-4 shadow-xl text-slate-800 flex items-center justify-between gap-4 transition-all">
      <div class="flex items-center gap-3">
        <div class="h-8 w-8 rounded-xl bg-brand-50 border border-brand-200 flex items-center justify-center text-brand-600">
          <i class="fa-solid fa-square-check text-sm"></i>
        </div>
        <span class="text-xs font-bold text-slate-700">
          <span id="bulk-tables-selected-count" class="font-extrabold text-brand-600 text-sm">0</span> tables selected
        </span>
      </div>

      <div class="flex items-center gap-2">
        <button type="button" id="btn-bulk-truncate"
          class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-xs border border-amber-200 transition cursor-pointer inline-flex items-center gap-1.5">
          <i class="fa-solid fa-eraser text-xs"></i> Bulk Truncate
        </button>

        <button type="button" id="btn-bulk-drop"
          class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs border border-rose-200 transition cursor-pointer inline-flex items-center gap-1.5">
          <i class="fa-solid fa-trash-can text-xs"></i> Bulk Drop Tables
        </button>

        <button type="button" id="btn-cancel-bulk-selection"
          class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs border border-slate-200 transition cursor-pointer">
          Cancel
        </button>
      </div>
    </div>

    <!-- Table Explorer Grid -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 select-none">
              <th class="px-4 py-3.5 w-10 text-center">
                <input type="checkbox" id="cb-select-all-tables" class="h-4 w-4 rounded border-slate-300 text-brand-600 cursor-pointer">
              </th>
              <th class="px-5 py-3.5">Table Name</th>
              <th class="px-4 py-3.5">Engine</th>
              <th class="px-4 py-3.5 text-right">Rows</th>
              <th class="px-4 py-3.5 text-right">Data Size</th>
              <th class="px-4 py-3.5 text-right">Index Size</th>
              <th class="px-4 py-3.5">Collation</th>
              <th class="px-5 py-3.5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody id="tables-list-tbody" class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
            <tr>
              <td colspan="8" class="p-8 text-center text-slate-400">
                <i class="fa-solid fa-circle-notch fa-spin text-lg mb-2"></i>
                <p class="font-semibold">Loading database schema tables…</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
@endsection

@section('scripts')
<script>
  $(document.ready ? $(document) : $(window)).ready(function () {
    let _tablesData = [];
    const webBasePath = "{{ url(config('database-studio.path', 'database-studio')) }}";
    const apiBasePath = "{{ url(config('database-studio.api_prefix', 'api/v1/database-manager')) }}";

    // Setup CSRF header for jQuery AJAX
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    // Initial Load
    loadTables();

    // Event Listeners
    $('#btn-refresh-tables').on('click', loadTables);
    $('#search-table-input').on('keyup', filterTables);
    
    // Select All Tables
    $('#cb-select-all-tables').on('change', function () {
      $('.table-cb').prop('checked', $(this).is(':checked'));
      updateTableSelectionUI();
    });

    // Individual Table Checkbox Delegate
    $(document).on('change', '.table-cb', function () {
      updateTableSelectionUI();
    });

    $('#btn-cancel-bulk-selection').on('click', clearTableSelection);

    $('#btn-bulk-truncate').on('click', function () {
      executeBulkTablesAction('truncate');
    });

    $('#btn-bulk-drop').on('click', function () {
      executeBulkTablesAction('drop');
    });

    // Row Actions Delegate
    $(document).on('click', '.btn-truncate-table', function () {
      const name = $(this).data('name');
      confirmTruncateTable(name);
    });

    $(document).on('click', '.btn-drop-table', function () {
      const name = $(this).data('name');
      confirmDropTable(name);
    });

    /**
     * Load tables via jQuery AJAX
     */
    function loadTables() {
      const $tbody = $('#tables-list-tbody');
      $tbody.html(`
        <tr>
          <td colspan="8" class="p-8 text-center text-slate-400">
            <i class="fa-solid fa-circle-notch fa-spin text-lg mb-2"></i>
            <p class="font-semibold">Loading database schema tables…</p>
          </td>
        </tr>
      `);

      $.ajax({
        url: apiBasePath,
        type: 'GET',
        success: function (res) {
          if (res.success) {
            const data = res.data;
            _tablesData = data.tables || [];

            $('#metric-tables-count').text(data.tables_count ?? 0);
            $('#metric-total-rows').text((data.total_rows ?? 0).toLocaleString());
            $('#metric-total-size').text(data.total_size ?? '0 B');
            $('#metric-db-name').text((data.database || 'MySQL').toUpperCase());

            renderTablesList(_tablesData);
          }
        },
        error: function (xhr) {
          window.handleAjaxError(xhr, 'Failed to load database tables.');
        }
      });
    }

    function renderTablesList(tables) {
      const $tbody = $('#tables-list-tbody');
      $('#tables-filtered-count').text(`Showing ${tables.length} of ${_tablesData.length} tables`);
      $('#cb-select-all-tables').prop('checked', false);
      updateTableSelectionUI();

      if (tables.length === 0) {
        $tbody.html(`
          <tr>
            <td colspan="8" class="p-8 text-center text-slate-400">
              <i class="fa-solid fa-folder-open text-2xl mb-2"></i>
              <p class="font-semibold">No database tables found matching your filter.</p>
            </td>
          </tr>
        `);
        return;
      }

      let html = '';
      $.each(tables, function (i, t) {
        html += `
          <tr class="hover:bg-slate-50/80 transition">
            <td class="px-4 py-3.5 text-center">
              <input type="checkbox" value="${t.name}" class="table-cb h-4 w-4 rounded border-slate-300 text-brand-600 cursor-pointer">
            </td>
            <td class="px-5 py-3.5 font-mono font-bold text-slate-900">
              <a href="${webBasePath}/manage/${t.name}" class="hover:text-brand-600 hover:underline border-0 bg-transparent cursor-pointer flex items-center gap-2 text-left text-slate-900 no-underline">
                <i class="fa-solid fa-table text-slate-400 text-xs"></i> ${t.name}
              </a>
            </td>
            <td class="px-4 py-3.5">
              <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-bold font-mono border border-slate-200">${t.engine}</span>
            </td>
            <td class="px-4 py-3.5 text-right font-mono font-bold text-slate-700">${t.rows.toLocaleString()}</td>
            <td class="px-4 py-3.5 text-right font-mono text-slate-600">${t.data_size}</td>
            <td class="px-4 py-3.5 text-right font-mono text-slate-500">${t.index_size}</td>
            <td class="px-4 py-3.5 text-slate-500 text-[11px] font-mono">${t.collation}</td>
            <td class="px-5 py-3.5 text-right space-x-1.5 whitespace-nowrap">
              <a href="${webBasePath}/manage/${t.name}" class="px-2.5 py-1 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-600 font-bold text-[11px] transition border border-brand-200 inline-flex items-center gap-1 no-underline">
                <i class="fa-solid fa-sliders text-[10px]"></i> Manage
              </a>
              <a href="${webBasePath}/manage/${t.name}?tab=data" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition border border-slate-200 inline-flex items-center gap-1 no-underline">
                <i class="fa-solid fa-table-list text-[10px]"></i> Browse
              </a>
              <button data-name="${t.name}" class="btn-truncate-table px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-[11px] transition border border-amber-200 inline-flex items-center gap-1 cursor-pointer">
                <i class="fa-solid fa-eraser text-[10px]"></i> Truncate
              </button>
              <button data-name="${t.name}" class="btn-drop-table px-2 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-[11px] transition border border-rose-200 inline-flex items-center gap-1 cursor-pointer">
                <i class="fa-solid fa-trash-can text-[10px]"></i> Drop
              </button>
            </td>
          </tr>
        `;
      });

      $tbody.html(html);
    }

    function filterTables() {
      const q = $('#search-table-input').val().toLowerCase().trim();
      if (!q) {
        renderTablesList(_tablesData);
        return;
      }
      const filtered = $.grep(_tablesData, function (t) {
        return t.name.toLowerCase().indexOf(q) !== -1;
      });
      renderTablesList(filtered);
    }

    function updateTableSelectionUI() {
      const checked = $('.table-cb:checked').map(function () { return $(this).val(); }).get();
      const $bulkBar = $('#table-bulk-bar');

      if (checked.length > 0) {
        $('#bulk-tables-selected-count').text(checked.length);
        $bulkBar.removeClass('hidden');
      } else {
        $bulkBar.addClass('hidden');
      }
    }

    function clearTableSelection() {
      $('#cb-select-all-tables').prop('checked', false);
      $('.table-cb').prop('checked', false);
      updateTableSelectionUI();
    }

    function executeBulkTablesAction(action) {
      const checked = $('.table-cb:checked').map(function () { return $(this).val(); }).get();
      if (checked.length === 0) return;

      const actionText = action === 'truncate' ? 'TRUNCATE ALL ROWS' : 'PERMANENTLY DROP';
      const color = action === 'truncate' ? '#d97706' : '#be123c';

      Swal.fire({
        title: `${action.toUpperCase()} ${checked.length} Selected Tables?`,
        html: `Are you sure you want to <b>${actionText}</b> the following ${checked.length} tables?<br><br><span class="font-mono text-xs text-rose-600 font-bold">${checked.join(', ')}</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: color,
        confirmButtonText: `Yes, ${action.toUpperCase()} ${checked.length} Tables`,
      }).then(function (result) {
        if (result.isConfirmed) {
          $.ajax({
            url: `${apiBasePath}/bulk-action`,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ action: action, tables: checked }),
            success: function (res) {
              if (res.success) {
                window.showToast('success', res.message);
                clearTableSelection();
                loadTables();
              }
            },
            error: function (xhr) {
              window.handleAjaxError(xhr, `Failed to execute bulk ${action}.`);
            }
          });
        }
      });
    }

    function confirmTruncateTable(tableName) {
      Swal.fire({
        title: `Truncate Table '${tableName}'?`,
        html: `
          <div class="text-left space-y-2.5 font-sans text-xs text-slate-600">
            <p class="font-semibold text-slate-800 text-sm">Are you sure you want to erase all records in table <b class="font-mono text-amber-600">${tableName}</b>?</p>
            <div class="p-2.5 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-xs">
              <i class="fa-solid fa-triangle-exclamation mr-1 text-amber-600"></i>
              <b>Warning:</b> All rows will be permanently deleted and auto-increment sequences reset.
            </div>
            <p class="text-[11px] font-bold text-slate-500 pt-1">Please type <span class="font-mono text-slate-900 bg-slate-100 px-1.5 py-0.5 rounded">${tableName}</span> below to confirm:</p>
          </div>
        `,
        input: 'text',
        inputPlaceholder: `Type '${tableName}' to confirm`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d97706',
        confirmButtonText: '<i class="fa-solid fa-eraser"></i> Truncate Table',
        cancelButtonText: 'Cancel',
        inputValidator: (value) => {
          if (value !== tableName) {
            return 'Table name does not match!';
          }
        }
      }).then(function (result) {
        if (result.isConfirmed) {
          $.ajax({
            url: `${apiBasePath}/${tableName}/truncate`,
            type: 'POST',
            success: function (res) {
              if (res.success) {
                window.showToast('success', res.message);
                loadTables();
              }
            },
            error: function (xhr) {
              window.handleAjaxError(xhr, 'Failed to truncate table.');
            }
          });
        }
      });
    }

    function confirmDropTable(tableName) {
      Swal.fire({
        title: `DROP TABLE '${tableName}'?`,
        html: `
          <div class="text-left space-y-2.5 font-sans text-xs text-slate-600">
            <p class="font-semibold text-slate-800 text-sm">You are about to permanently delete table <b class="font-mono text-rose-600">${tableName}</b>!</p>
            <div class="p-2.5 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs">
              <i class="fa-solid fa-radiation mr-1 text-rose-600"></i>
              <b>Critical Warning:</b> The table structure, all columns, indexes, and records will be deleted forever.
            </div>
            <p class="text-[11px] font-bold text-slate-500 pt-1">Please type <span class="font-mono text-slate-900 bg-slate-100 px-1.5 py-0.5 rounded">${tableName}</span> to confirm drop:</p>
          </div>
        `,
        input: 'text',
        inputPlaceholder: `Type '${tableName}' to confirm`,
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#be123c',
        confirmButtonText: '<i class="fa-solid fa-trash-can"></i> Permanently Drop Table',
        cancelButtonText: 'Cancel',
        inputValidator: (value) => {
          if (value !== tableName) {
            return 'Table name does not match!';
          }
        }
      }).then(function (result) {
        if (result.isConfirmed) {
          $.ajax({
            url: `${apiBasePath}/${tableName}`,
            type: 'DELETE',
            success: function (res) {
              if (res.success) {
                window.showToast('success', res.message);
                loadTables();
              }
            },
            error: function (xhr) {
              window.handleAjaxError(xhr, 'Failed to drop table.');
            }
          });
        }
      });
    }
  });
</script>
@endsection
