@extends('database-studio::layouts.app')

@section('title', "Manage Table {$tableName} — Database Studio")

@section('content')
  <div class="space-y-6 font-sans w-full relative">

    <!-- Header Hero Banner -->
    <div
      class="theme-hero-banner bg-gradient-to-r from-brand-900 via-brand-700 to-indigo-800 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
      <div
        class="absolute right-0 top-0 translate-x-8 -translate-y-8 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none">
      </div>
      <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-brand-200 text-xs font-bold uppercase tracking-widest mb-1">
            <i class="fa-solid fa-table text-amber-300"></i> Table Schema & Data Manager
          </div>
          <h1 class="text-2xl font-extrabold tracking-tight font-mono">Table: {{ $tableName }}</h1>
          <p class="text-sm text-brand-100 mt-1 max-w-2xl">
            Inspect column types, configure indexes, manage fields in bulk, view foreign key constraints, and browse live
            records.
          </p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
          <a href="{{ url(config('database-studio.path', 'database-studio')) }}"
            class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/20 inline-flex items-center gap-2 no-underline">
            <i class="fa-solid fa-arrow-left text-xs"></i> All Tables
          </a>
          <button type="button" id="btn-truncate-table"
            class="px-3.5 py-2.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-200 font-bold text-xs transition border border-amber-400/30 inline-flex items-center gap-1.5 cursor-pointer">
            <i class="fa-solid fa-eraser text-xs"></i> Truncate
          </button>
          <button type="button" id="btn-drop-table"
            class="px-3.5 py-2.5 rounded-xl bg-rose-600/30 hover:bg-rose-600/40 text-rose-200 font-bold text-xs transition border border-rose-400/40 inline-flex items-center gap-1.5 cursor-pointer">
            <i class="fa-solid fa-trash-can text-xs"></i> Drop Table
          </button>
        </div>
      </div>
    </div>

    <!-- Sticky Bulk Action Bar for Fields / Columns -->
    <div id="column-bulk-bar"
      class="hidden sticky top-4 z-30 bg-white border border-slate-200 rounded-2xl p-4 shadow-xl text-slate-800 flex items-center justify-between gap-4 transition-all">
      <div class="flex items-center gap-3">
        <div
          class="h-8 w-8 rounded-xl bg-brand-50 border border-brand-200 flex items-center justify-center text-brand-600">
          <i class="fa-solid fa-square-check text-sm"></i>
        </div>
        <span class="text-xs font-bold text-slate-700">
          <span id="bulk-cols-selected-count" class="font-extrabold text-brand-600 text-sm">0</span> fields selected
        </span>
      </div>

      <div class="flex items-center gap-2">
        <button type="button" id="btn-bulk-add-index"
          class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-xs border border-amber-200 transition cursor-pointer inline-flex items-center gap-1.5">
          <i class="fa-solid fa-key text-xs"></i> Add Index on Fields
        </button>

        <button type="button" id="btn-bulk-modify-col"
          class="hidden px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs border border-indigo-200 transition cursor-pointer inline-flex items-center gap-1.5">
          <i class="fa-solid fa-pen-to-square text-xs"></i> Modify Column
        </button>

        <button type="button" id="btn-bulk-drop-cols"
          class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs border border-rose-200 transition cursor-pointer inline-flex items-center gap-1.5">
          <i class="fa-solid fa-trash-can text-xs"></i> Drop Selected Fields
        </button>

        <button type="button" id="btn-cancel-col-selection"
          class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs border border-slate-200 transition cursor-pointer">
          Cancel
        </button>
      </div>
    </div>

    <!-- Main Studio Card -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden p-6 space-y-6">

      <!-- Navigation Tabs -->
      <div class="flex items-center gap-2 border-b border-slate-200 text-xs font-bold text-slate-500">
        <button type="button"
          class="tab-btn px-5 py-3 border-b-2 border-brand-500 text-brand-600 flex items-center gap-2 font-bold cursor-pointer"
          data-tab="columns" id="tab-btn-columns">
          <i class="fa-solid fa-columns"></i> Columns / Fields (<span id="count-columns">0</span>)
        </button>
        <button type="button"
          class="tab-btn px-5 py-3 border-b-2 border-transparent hover:text-slate-800 flex items-center gap-2 cursor-pointer"
          data-tab="indexes" id="tab-btn-indexes">
          <i class="fa-solid fa-key"></i> Indexes (<span id="count-indexes">0</span>)
        </button>
        <button type="button"
          class="tab-btn px-5 py-3 border-b-2 border-transparent hover:text-slate-800 flex items-center gap-2 cursor-pointer"
          data-tab="fks" id="tab-btn-fks">
          <i class="fa-solid fa-link"></i> Foreign Keys (<span id="count-fks">0</span>)
        </button>
        <button type="button"
          class="tab-btn px-5 py-3 border-b-2 border-transparent hover:text-slate-800 flex items-center gap-2 cursor-pointer"
          data-tab="data" id="tab-btn-data">
          <i class="fa-solid fa-table-list"></i> Browse Data
        </button>
        <button type="button"
          class="tab-btn px-5 py-3 border-b-2 border-transparent hover:text-slate-800 flex items-center gap-2 cursor-pointer"
          data-tab="sql" id="tab-btn-sql">
          <i class="fa-solid fa-code"></i> DDL & SQL Console
        </button>
      </div>

      <!-- Tab 1: Columns / Fields -->
      <div id="tab-content-columns" class="tab-content space-y-4">
        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-xs">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr
                class="bg-slate-50 border-b border-slate-200 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                <th class="p-3.5 w-10 text-center">
                  <input type="checkbox" id="cb-select-all-columns"
                    class="h-4 w-4 rounded border-slate-300 text-brand-600 cursor-pointer">
                </th>
                <th class="p-3.5">Field Name</th>
                <th class="p-3.5">Data Type</th>
                <th class="p-3.5 text-center">Nullable</th>
                <th class="p-3.5">Key</th>
                <th class="p-3.5">Default Value</th>
                <th class="p-3.5">Extra / Attributes</th>
                <th class="p-3.5">Comment</th>
                <th class="p-3.5 text-right">Action</th>
              </tr>
            </thead>
            <tbody id="tbody-columns" class="divide-y divide-slate-100 font-mono text-xs text-slate-700">
              <tr>
                <td colspan="9" class="p-6 text-center text-slate-400"><i class="fa-solid fa-circle-notch fa-spin"></i>
                  Loading columns…</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tab 2: Indexes -->
      <div id="tab-content-indexes" class="tab-content hidden space-y-4">
        <div class="flex items-center justify-between">
          <p class="text-xs text-slate-500 font-medium">Index and unique constraints defined on `{{ $tableName }}`.</p>
          <button type="button" id="btn-open-add-index"
            class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition shadow-sm inline-flex items-center gap-1.5 cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i> Add New Index
          </button>
        </div>

        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-xs">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr
                class="bg-slate-50 border-b border-slate-200 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                <th class="p-3.5">Index Name</th>
                <th class="p-3.5">Type</th>
                <th class="p-3.5">Target Columns</th>
                <th class="p-3.5 text-right">Cardinality</th>
                <th class="p-3.5 text-right">Action</th>
              </tr>
            </thead>
            <tbody id="tbody-indexes" class="divide-y divide-slate-100 font-mono text-xs text-slate-700">
              <tr>
                <td colspan="5" class="p-6 text-center text-slate-400"><i class="fa-solid fa-circle-notch fa-spin"></i>
                  Loading indexes…</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tab 3: Foreign Keys -->
      <div id="tab-content-fks" class="tab-content hidden space-y-4">
        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-xs">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr
                class="bg-slate-50 border-b border-slate-200 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                <th class="p-3.5">Constraint Name</th>
                <th class="p-3.5">Local Column</th>
                <th class="p-3.5">Foreign Table</th>
                <th class="p-3.5">Foreign Column</th>
                <th class="p-3.5">On Delete</th>
                <th class="p-3.5">On Update</th>
              </tr>
            </thead>
            <tbody id="tbody-fks" class="divide-y divide-slate-100 font-mono text-xs text-slate-700">
              <tr>
                <td colspan="6" class="p-6 text-center text-slate-400"><i class="fa-solid fa-circle-notch fa-spin"></i>
                  Loading foreign keys…</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tab 4: Browse Data -->
      <div id="tab-content-data" class="tab-content hidden space-y-3">

        <!-- Toolbar: Search + Filter Toggle + Pagination Info -->
        <div class="flex flex-col sm:flex-row items-center gap-3">
          <!-- Global Search -->
          <div class="relative w-full sm:w-72 flex-shrink-0">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="data-search-input"
              class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-brand-500"
              placeholder="Quick search all columns…">
          </div>

          <!-- Query Filter Toggle Button -->
          <button type="button" id="btn-toggle-query-filter"
            class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-300 text-slate-600 hover:text-indigo-700 text-xs font-bold transition cursor-pointer group"
            title="Toggle Advanced Filters">
            <i class="fa-solid fa-filter text-xs group-hover:text-indigo-600 transition"></i>
            <span>Filter</span>
            <span id="filter-active-badge" class="hidden items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-indigo-600 text-white text-[10px] font-extrabold">0</span>
          </button>

          <!-- Spacer -->
          <div class="flex-1"></div>

          <!-- Export Dropdown -->
          <div class="relative flex-shrink-0" id="export-dropdown-wrapper">
            <button type="button" id="btn-export-dropdown"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold transition cursor-pointer"
              title="Export filtered results">
              <i class="fa-solid fa-file-arrow-down text-xs"></i>
              Export
              <i class="fa-solid fa-chevron-down text-[9px]"></i>
            </button>
            <!-- Dropdown Menu -->
            <div id="export-dropdown-menu"
              class="hidden absolute right-0 top-full mt-1.5 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 overflow-hidden">
              <button type="button" id="btn-export-csv"
                class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition cursor-pointer border-b border-slate-100">
                <i class="fa-solid fa-file-csv text-base text-emerald-600"></i>
                <div class="text-left">
                  <div class="font-bold">Export as CSV</div>
                  <div class="text-[10px] text-slate-400 font-normal">All filtered rows · UTF-8</div>
                </div>
              </button>
              <button type="button" id="btn-export-excel"
                class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition cursor-pointer">
                <i class="fa-solid fa-file-excel text-base text-green-600"></i>
                <div class="text-left">
                  <div class="font-bold">Export as Excel</div>
                  <div class="text-[10px] text-slate-400 font-normal">All filtered rows · XLS</div>
                </div>
              </button>
            </div>
          </div>

          <!-- Pagination info -->
          <span id="data-pagination-info" class="text-xs font-semibold text-slate-500 flex-shrink-0">Loading data…</span>
        </div>

        <!-- Query Filter Builder Bar -->
        <div id="query-filter-bar" class="hidden border border-indigo-200 bg-indigo-50/60 rounded-2xl p-4 space-y-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <i class="fa-solid fa-filter text-indigo-600 text-xs"></i>
              <span class="text-xs font-extrabold text-indigo-800 uppercase tracking-wider">Advanced Filters</span>
              <span class="text-[10px] text-indigo-500 font-semibold">(AND logic — all active rules must match)</span>
            </div>
            <div class="flex items-center gap-2">
              <button type="button" id="btn-add-filter-rule"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition cursor-pointer shadow-sm">
                <i class="fa-solid fa-plus text-[10px]"></i> Add Rule
              </button>
              <button type="button" id="btn-apply-filters"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition cursor-pointer shadow-sm">
                <i class="fa-solid fa-check text-[10px]"></i> Apply
              </button>
              <button type="button" id="btn-reset-filters"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition cursor-pointer">
                <i class="fa-solid fa-xmark text-[10px]"></i> Reset
              </button>
            </div>
          </div>

          <div id="filter-rules-container" class="space-y-2"></div>

          <div id="filter-rules-empty" class="flex items-center justify-center py-4 text-xs text-indigo-400 font-semibold">
            <i class="fa-solid fa-circle-plus mr-2 text-indigo-300"></i>
            Click <strong class="mx-1 text-indigo-600">Add Rule</strong> to build a filter condition
          </div>
        </div>

        <!-- Data Grid -->
        <div class="border border-slate-200 rounded-xl overflow-x-auto shadow-xs" style="max-height:520px; overflow-y:auto;">
          <table class="w-full text-left border-collapse text-xs font-mono" id="data-grid-table"></table>
        </div>

        <!-- Pagination Controls -->
        <div class="flex items-center justify-between text-xs font-bold text-slate-600">
          <button type="button" id="btn-data-prev"
            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 transition cursor-pointer">
            <i class="fa-solid fa-chevron-left text-[10px]"></i> Previous
          </button>
          <span id="data-current-page-text">Page 1</span>
          <button type="button" id="btn-data-next"
            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 transition cursor-pointer">
            Next <i class="fa-solid fa-chevron-right text-[10px]"></i>
          </button>
        </div>
      </div>

      <!-- Tab 5: DDL & SQL Console -->
      <div id="tab-content-sql" class="tab-content hidden space-y-6">
        <!-- Section A: Interactive SQL Console -->
        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 space-y-4">
          <div class="flex items-center justify-between">
            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-800 flex items-center gap-2">
              <i class="fa-solid fa-terminal text-brand-600"></i> Interactive Query Console (`{{ $tableName }}`)
            </h4>
            <span class="text-[11px] text-slate-400 font-mono">Press <b class="text-slate-700">Ctrl + Enter</b> to execute</span>
          </div>

          <div class="relative">
            <textarea id="page-sql-editor" rows="4"
              class="w-full bg-slate-900 text-emerald-400 font-mono text-xs p-4 rounded-xl leading-relaxed focus:outline-none focus:ring-2 focus:ring-brand-500 shadow-inner resize-y"
              placeholder="SELECT * FROM {{ $tableName }} LIMIT 25;"></textarea>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs">
              <button type="button" id="btn-snippet-select-all"
                class="px-2.5 py-1 rounded bg-white hover:bg-slate-100 text-slate-700 font-mono border border-slate-200 text-[11px] cursor-pointer">
                SELECT ALL
              </button>
              <button type="button" id="btn-snippet-explain"
                class="px-2.5 py-1 rounded bg-white hover:bg-slate-100 text-slate-700 font-mono border border-slate-200 text-[11px] cursor-pointer">
                EXPLAIN
              </button>
            </div>
            <button type="button" id="btn-page-sql-exec"
              class="px-5 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-extrabold text-xs transition shadow-md inline-flex items-center gap-2 cursor-pointer">
              <i class="fa-solid fa-play text-xs"></i> Execute Query
            </button>
          </div>

          <div id="page-sql-output-container"
            class="hidden border border-slate-200 rounded-xl overflow-hidden max-h-72 overflow-auto text-xs font-mono bg-white">
          </div>
        </div>

        <!-- Section B: Raw Create Table DDL SQL -->
        <div class="space-y-2">
          <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-code text-indigo-500"></i> Raw CREATE TABLE DDL Definition
          </h4>
          <div class="relative">
            <textarea id="sql-textarea" readonly rows="8"
              class="w-full bg-slate-900 text-emerald-400 font-mono text-xs p-5 rounded-2xl leading-relaxed focus:outline-none select-all resize-none shadow-inner"></textarea>
            <button type="button" id="btn-copy-ddl"
              class="absolute right-4 top-4 px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/20 transition cursor-pointer">
              <i class="fa-regular fa-copy"></i> Copy DDL SQL
            </button>
          </div>
        </div>
      </div>

    </div>

  </div>

  <!-- Add Index Modal -->
  <div id="modal-add-index" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" id="btn-close-ai-bg"></div>
      <form id="form-add-index"
        class="relative bg-white border border-slate-200 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-5 font-sans">
        <div class="flex items-center gap-3">
          <div
            class="h-10 w-10 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
            <i class="fa-solid fa-key"></i>
          </div>
          <div>
            <h3 class="font-bold text-slate-900 text-base">Add Table Index</h3>
            <p class="text-xs text-slate-400">Create a key or unique constraint on `{{ $tableName }}`</p>
          </div>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Index Name *</label>
            <input type="text" id="ai-index-name" required pattern="[a-zA-Z0-9_]+"
              class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:border-brand-500"
              placeholder="e.g. idx_title">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Index Type *</label>
            <select id="ai-index-type"
              class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-semibold text-slate-800">
              <option value="INDEX" selected>INDEX (Standard Key)</option>
              <option value="UNIQUE">UNIQUE (Unique Constraint)</option>
              <option value="FULLTEXT">FULLTEXT (Text Search)</option>
              <option value="PRIMARY">PRIMARY KEY</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Target Columns *</label>
            <div id="ai-columns-checkboxes"
              class="space-y-1.5 max-h-40 overflow-y-auto bg-slate-50 p-3 rounded-lg border border-slate-200">
            </div>
          </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-3">
          <button type="button" id="btn-cancel-ai"
            class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition">
            Cancel
          </button>
          <button type="submit"
            class="px-5 py-2 rounded-lg bg-brand-600 hover:bg-brand-500 text-white font-extrabold text-xs transition shadow-sm shadow-brand-600/20">
            Add Index
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modify Column Modal -->
  <div id="modal-modify-col" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
      <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" id="btn-close-mc-bg"></div>
      <form id="form-modify-col"
        class="relative bg-white border border-slate-200 rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 font-sans">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
            <i class="fa-solid fa-pen-to-square"></i>
          </div>
          <div>
            <h3 class="font-bold text-slate-900 text-base">Modify Column</h3>
            <p class="text-xs text-slate-400">Alter definition of <code id="mc-col-label" class="font-mono font-bold text-indigo-700"></code> on <code class="font-mono text-slate-600">`{{ $tableName }}`</code></p>
          </div>
        </div>

        <input type="hidden" id="mc-original-name">

        <div class="grid grid-cols-2 gap-4">
          <div class="col-span-2">
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Rename To <span class="text-slate-300 font-normal">(leave blank to keep)</span></label>
            <input type="text" id="mc-new-name" pattern="[a-zA-Z0-9_]*"
              class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-mono font-semibold text-slate-800 focus:outline-none focus:border-indigo-400"
              placeholder="new_column_name">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Data Type *</label>
            <select id="mc-type"
              class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:border-indigo-400">
              <optgroup label="String">
                <option value="VARCHAR">VARCHAR</option>
                <option value="CHAR">CHAR</option>
                <option value="TEXT">TEXT</option>
                <option value="TINYTEXT">TINYTEXT</option>
                <option value="MEDIUMTEXT">MEDIUMTEXT</option>
                <option value="LONGTEXT">LONGTEXT</option>
              </optgroup>
              <optgroup label="Numeric">
                <option value="INT">INT</option>
                <option value="BIGINT">BIGINT</option>
                <option value="TINYINT">TINYINT</option>
                <option value="SMALLINT">SMALLINT</option>
                <option value="MEDIUMINT">MEDIUMINT</option>
                <option value="DECIMAL">DECIMAL</option>
                <option value="FLOAT">FLOAT</option>
                <option value="DOUBLE">DOUBLE</option>
                <option value="BOOLEAN">BOOLEAN</option>
              </optgroup>
              <optgroup label="Date / Time">
                <option value="DATE">DATE</option>
                <option value="DATETIME">DATETIME</option>
                <option value="TIMESTAMP">TIMESTAMP</option>
                <option value="TIME">TIME</option>
                <option value="YEAR">YEAR</option>
              </optgroup>
              <optgroup label="Binary / Blob">
                <option value="BLOB">BLOB</option>
                <option value="MEDIUMBLOB">MEDIUMBLOB</option>
                <option value="LONGBLOB">LONGBLOB</option>
                <option value="BINARY">BINARY</option>
                <option value="VARBINARY">VARBINARY</option>
              </optgroup>
              <optgroup label="Other">
                <option value="JSON">JSON</option>
                <option value="ENUM">ENUM</option>
                <option value="SET">SET</option>
              </optgroup>
            </select>
          </div>

          <div id="mc-length-wrap">
            <label id="mc-length-label" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Length / Precision</label>
            <input type="text" id="mc-length" pattern="[0-9,]*"
              class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-mono font-semibold text-slate-800 focus:outline-none focus:border-indigo-400"
              placeholder="e.g. 255 or 10,2">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Default Value</label>
            <input type="text" id="mc-default"
              class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-mono font-semibold text-slate-800 focus:outline-none focus:border-indigo-400"
              placeholder="Leave blank for none">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Comment</label>
            <input type="text" id="mc-comment" maxlength="255"
              class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:border-indigo-400"
              placeholder="Optional comment">
          </div>

          <div class="col-span-2 flex items-center gap-6 pt-1">
            <label class="flex items-center gap-2.5 cursor-pointer select-none">
              <input type="checkbox" id="mc-nullable" class="h-4 w-4 rounded border-slate-300 text-indigo-600 cursor-pointer">
              <span class="text-xs font-bold text-slate-700">Allow NULL</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer select-none">
              <input type="checkbox" id="mc-unsigned" class="h-4 w-4 rounded border-slate-300 text-indigo-600 cursor-pointer">
              <span class="text-xs font-bold text-slate-700">UNSIGNED</span>
            </label>
          </div>
        </div>

        <div class="flex items-start gap-2.5 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
          <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5 flex-shrink-0"></i>
          <span><b>Warning:</b> Changing a column's type may truncate or transform existing data.</span>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-3">
          <button type="button" id="btn-cancel-mc"
            class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition cursor-pointer">
            Cancel
          </button>
          <button type="submit"
            class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs transition shadow-sm shadow-indigo-600/20 inline-flex items-center gap-2 cursor-pointer">
            <i class="fa-solid fa-pen-to-square text-xs"></i> Apply Modification
          </button>
        </div>
      </form>
    </div>
  </div>

@endsection

@section('scripts')
  <script>
    $(document.ready ? $(document) : $(window)).ready(function () {
      const _tableName = "{{ $tableName }}";
      const webBasePath = "{{ url(config('database-studio.path', 'database-studio')) }}";
      const apiBasePath = "{{ url(config('database-studio.api_prefix', 'api/v1/database-manager')) }}";
      let _tableDetails = null;
      let _currentPage = 1;

      // Setup CSRF header for jQuery AJAX
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      loadTableSchema();

      const TYPE_LENGTH_RULES = {
        JSON:       { mode: 'none' },
        TEXT:       { mode: 'none' },
        TINYTEXT:   { mode: 'none' },
        MEDIUMTEXT: { mode: 'none' },
        LONGTEXT:   { mode: 'none' },
        BOOLEAN:    { mode: 'none' },
        DATE:       { mode: 'none' },
        DATETIME:   { mode: 'none' },
        TIMESTAMP:  { mode: 'none' },
        TIME:       { mode: 'none' },
        YEAR:       { mode: 'none' },
        BLOB:       { mode: 'none' },
        TINYBLOB:   { mode: 'none' },
        MEDIUMBLOB: { mode: 'none' },
        LONGBLOB:   { mode: 'none' },
        VARCHAR:    { mode: 'length',    label: 'Length',           placeholder: '255'       },
        CHAR:       { mode: 'length',    label: 'Length',           placeholder: '36'        },
        BINARY:     { mode: 'length',    label: 'Length',           placeholder: '16'        },
        VARBINARY:  { mode: 'length',    label: 'Length',           placeholder: '255'       },
        INT:        { mode: 'width',     label: 'Display Width',    placeholder: '11'        },
        INTEGER:    { mode: 'width',     label: 'Display Width',    placeholder: '11'        },
        BIGINT:     { mode: 'width',     label: 'Display Width',    placeholder: '20'        },
        TINYINT:    { mode: 'width',     label: 'Display Width',    placeholder: '4'         },
        SMALLINT:   { mode: 'width',     label: 'Display Width',    placeholder: '6'         },
        MEDIUMINT:  { mode: 'width',     label: 'Display Width',    placeholder: '9'         },
        FLOAT:      { mode: 'width',     label: 'Precision',        placeholder: ''          },
        DOUBLE:     { mode: 'width',     label: 'Precision',        placeholder: ''          },
        DECIMAL:    { mode: 'precision', label: 'Precision, Scale', placeholder: '10,2'      },
        ENUM:       { mode: 'values',    label: 'Values',           placeholder: "'a','b','c'" },
        SET:        { mode: 'values',    label: 'Values',           placeholder: "'x','y','z'" },
      };

      function applyModalLengthRules($select) {
        const type   = ($select.val() || '').toUpperCase();
        const rule   = TYPE_LENGTH_RULES[type] || { mode: 'length', label: 'Length / Precision', placeholder: '' };
        const $wrap  = $('#mc-length-wrap');
        const $label = $('#mc-length-label');
        const $input = $('#mc-length');

        if (rule.mode === 'none') {
          $wrap.addClass('opacity-40 pointer-events-none');
          $input.val('').prop('disabled', true).attr('placeholder', 'N/A — not applicable for this type');
          $label.text('Length / Precision');
        } else {
          $wrap.removeClass('opacity-40 pointer-events-none');
          $input.prop('disabled', false).attr('placeholder', rule.placeholder);
          $label.text(rule.label);
        }
      }

      const params = new URLSearchParams(window.location.search);
      const initialTab = params.get('tab') || 'columns';
      switchTab(initialTab);

      $('.tab-btn').on('click', function () {
        const tab = $(this).data('tab');
        switchTab(tab);
      });

      $('#btn-truncate-table').on('click', function () { confirmTruncateTable(_tableName); });
      $('#btn-drop-table').on('click', function () { confirmDropTable(_tableName); });

      $('#cb-select-all-columns').on('change', function () {
        $('.col-cb').prop('checked', $(this).is(':checked'));
        updateColumnSelectionUI();
      });

      $(document).on('change', '.col-cb', function () {
        updateColumnSelectionUI();
      });

      $('#btn-cancel-col-selection').on('click', clearColumnSelection);

      $('#btn-bulk-add-index').on('click', function () {
        const checked = $('.col-cb:checked').map(function () { return $(this).val(); }).get();
        openAddIndexModal(checked);
      });

      $('#btn-bulk-modify-col').on('click', function () {
        const checked = $('.col-cb:checked').map(function () { return $(this).val(); }).get();
        if (checked.length !== 1) return;
        const colName = checked[0];
        const colData = $.grep(_tableDetails.columns || [], function (c) { return c.name === colName; })[0] || {};
        openModifyColModal(colName, colData);
      });

      $(document).on('click', '.btn-edit-single-col', function () {
        const $btn    = $(this);
        const colName = $btn.data('col');
        const colData = {
          type:     $btn.data('type') || '',
          nullable: $btn.data('nullable') === 1 || $btn.data('nullable') === '1',
          default:  $btn.data('default') || '',
          comment:  $btn.data('comment') || '',
        };
        openModifyColModal(colName, colData);
      });

      $('#btn-close-mc-bg, #btn-cancel-mc').on('click', closeModifyColModal);

      $('#mc-type').on('change', function () {
        applyModalLengthRules($(this));
      });

      $('#form-modify-col').on('submit', function (e) {
        e.preventDefault();
        submitModifyCol();
      });

      $('#btn-bulk-drop-cols').on('click', executeBulkDropColumns);

      $(document).on('click', '.btn-drop-single-col', function () {
        const col = $(this).data('col');
        confirmSingleDropColumn(col);
      });

      $(document).on('click', '.btn-drop-index', function () {
        const idx = $(this).data('index');
        confirmDropIndex(idx);
      });

      $('#btn-open-add-index').on('click', function () { openAddIndexModal(); });
      $('#btn-close-ai-bg, #btn-cancel-ai').on('click', closeAddIndexModal);

      $('#form-add-index').on('submit', function (e) {
        e.preventDefault();
        submitAddIndex();
      });

      $('#data-search-input').on('keyup', function () {
        loadDataRows(1);
      });

      $('#btn-data-prev').on('click', function () { changeDataPage(-1); });
      $('#btn-data-next').on('click', function () { changeDataPage(1); });

      /* Filter Bar Events */
      $('#btn-toggle-query-filter').on('click', function () {
        const $bar = $('#query-filter-bar');
        const isHidden = $bar.hasClass('hidden');
        $bar.toggleClass('hidden', !isHidden);
        $(this).toggleClass('bg-indigo-100 border-indigo-400 text-indigo-700', isHidden);
      });

      $('#btn-add-filter-rule').on('click', function () {
        addFilterRule();
      });

      $(document).on('click', '.btn-remove-filter-rule', function () {
        $(this).closest('.filter-rule-row').remove();
        syncFilterBadge();
        syncFirstRowAndBadge();
      });

      $(document).on('change', '.filter-op-select', function () {
        const $row = $(this).closest('.filter-rule-row');
        const $selectedOpt = $(this).find('option:selected');
        const hasValue = $selectedOpt.data('has-value') === true || $selectedOpt.data('has-value') === 'true';
        const $valWrap = $row.find('.filter-val-wrap');
        if (hasValue) {
          $valWrap.css('visibility', 'visible');
        } else {
          $valWrap.css('visibility', 'hidden');
          $row.find('.filter-val-input').val('');
        }
      });

      $(document).on('change', '.filter-rule-enabled', function () {
        syncFilterBadge();
      });

      $('#btn-apply-filters').on('click', function () {
        loadDataRows(1);
      });

      $('#btn-reset-filters').on('click', function () {
        $('#filter-rules-container').empty();
        $('#filter-rules-empty').show();
        syncFilterBadge();
        loadDataRows(1);
      });

      $(document).on('keyup', '.filter-val-input', function (e) {
        if (e.key === 'Enter') {
          $('#btn-apply-filters').trigger('click');
        }
      });

      /* Export Dropdown */
      $('#btn-export-dropdown').on('click', function (e) {
        e.stopPropagation();
        $('#export-dropdown-menu').toggleClass('hidden');
      });

      $(document).on('click', function (e) {
        if (!$(e.target).closest('#export-dropdown-wrapper').length) {
          $('#export-dropdown-menu').addClass('hidden');
        }
      });

      function buildExportUrl(format) {
        const search  = $('#data-search-input').val().trim();
        const rules   = collectFilterRules();
        const active  = rules.filter(function (r) { return r.enabled && r.column; });

        const params = new URLSearchParams();
        params.set('format', format);
        if (search) params.set('search', search);
        if (active.length > 0) params.set('filters', JSON.stringify(active));

        return `${apiBasePath}/${_tableName}/export?${params.toString()}`;
      }

      $('#btn-export-csv').on('click', function () {
        $('#export-dropdown-menu').addClass('hidden');
        const url = buildExportUrl('csv');
        const $a = $('<a>').attr({ href: url, download: '' }).appendTo('body');
        $a[0].click();
        $a.remove();
        window.showToast('success', 'CSV export started — check your downloads!');
      });

      $('#btn-export-excel').on('click', function () {
        $('#export-dropdown-menu').addClass('hidden');
        const url = buildExportUrl('excel');
        const $a = $('<a>').attr({ href: url, download: '' }).appendTo('body');
        $a[0].click();
        $a.remove();
        window.showToast('success', 'Excel export started — check your downloads!');
      });

      $('#btn-snippet-select-all').on('click', function () {
        $('#page-sql-editor').val(`SELECT * FROM ${_tableName} LIMIT 25;`);
      });

      $('#btn-snippet-explain').on('click', function () {
        $('#page-sql-editor').val(`EXPLAIN SELECT * FROM ${_tableName};`);
      });

      $('#btn-page-sql-exec').on('click', executePageSqlQuery);

      $('#btn-copy-ddl').on('click', function () {
        navigator.clipboard.writeText($('#sql-textarea').val());
        window.showToast('success', 'SQL copied to clipboard!');
      });

      function loadTableSchema() {
        $.ajax({
          url: `${apiBasePath}/${_tableName}`,
          type: 'GET',
          success: function (res) {
            if (res.success) {
              _tableDetails = res.data;

              renderColumns(_tableDetails.columns || []);
              renderIndexes(_tableDetails.indexes || []);
              renderFks(_tableDetails.foreign_keys || []);
              $('#sql-textarea').val(_tableDetails.create_sql || '');
              if (!$('#page-sql-editor').val()) {
                $('#page-sql-editor').val(`SELECT * FROM ${_tableName} LIMIT 25;`);
              }

              $('#count-columns').text((_tableDetails.columns || []).length);
              $('#count-indexes').text((_tableDetails.indexes || []).length);
              $('#count-fks').text((_tableDetails.foreign_keys || []).length);
            }
          },
          error: function (xhr) {
            window.handleAjaxError(xhr, 'Failed to inspect table details.');
          }
        });
      }

      function switchTab(tab) {
        $('.tab-btn').each(function () {
          const t = $(this).data('tab');
          if (t === tab) {
            $(this).attr('class', 'tab-btn px-5 py-3 border-b-2 border-brand-500 text-brand-600 flex items-center gap-2 font-bold cursor-pointer');
            $(`#tab-content-${t}`).removeClass('hidden');
          } else {
            $(this).attr('class', 'tab-btn px-5 py-3 border-b-2 border-transparent hover:text-slate-800 flex items-center gap-2 cursor-pointer');
            $(`#tab-content-${t}`).addClass('hidden');
          }
        });

        if (tab === 'data') {
          loadDataRows(1);
        }
      }

      function renderColumns(cols) {
        const $tbody = $('#tbody-columns');
        $('#cb-select-all-columns').prop('checked', false);
        updateColumnSelectionUI();

        let html = '';
        $.each(cols, function (i, c) {
          html += `
            <tr class="hover:bg-slate-50 transition">
              <td class="p-3.5 text-center">
                <input type="checkbox" value="${c.name}" class="col-cb h-4 w-4 rounded border-slate-300 text-brand-600 cursor-pointer">
              </td>
              <td class="p-3.5 font-bold text-slate-900 font-mono">${c.name}</td>
              <td class="p-3.5 text-slate-600">${c.type}</td>
              <td class="p-3.5 text-center">${c.nullable ? '<span class="text-amber-600 font-bold">YES</span>' : '<span class="text-slate-400">NO</span>'}</td>
              <td class="p-3.5 font-bold ${c.key === 'PRI' ? 'text-rose-600' : 'text-slate-500'}">${c.key || '-'}</td>
              <td class="p-3.5 text-slate-500">${c.default !== null ? c.default : '<span class="text-slate-300 italic">NULL</span>'}</td>
              <td class="p-3.5 text-brand-600 font-semibold">${c.extra || '-'}</td>
              <td class="p-3.5 text-slate-400 italic font-sans">${c.comment || '-'}</td>
              <td class="p-3.5 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button data-col="${c.name}" data-type="${c.type}" data-nullable="${c.nullable ? 1 : 0}" data-default="${c.default !== null ? c.default : ''}" data-comment="${c.comment || ''}" class="btn-edit-single-col px-2.5 py-1 rounded bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-bold text-xs transition border border-indigo-200 cursor-pointer">
                    <i class="fa-solid fa-pen-to-square text-[10px]"></i> Edit
                  </button>
                  <button data-col="${c.name}" class="btn-drop-single-col px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition border border-rose-200 cursor-pointer">
                    <i class="fa-solid fa-trash-can text-[10px]"></i> Drop
                  </button>
                </div>
              </td>
            </tr>
          `;
        });
        $tbody.html(html);
      }

      function updateColumnSelectionUI() {
        const checked = $('.col-cb:checked').map(function () { return $(this).val(); }).get();
        const $bulkBar = $('#column-bulk-bar');

        if (checked.length > 0) {
          $('#bulk-cols-selected-count').text(checked.length);
          $bulkBar.removeClass('hidden');
          if (checked.length === 1) {
            $('#btn-bulk-modify-col').removeClass('hidden').css('display', 'inline-flex');
          } else {
            $('#btn-bulk-modify-col').addClass('hidden').hide();
          }
        } else {
          $bulkBar.addClass('hidden');
          $('#btn-bulk-modify-col').addClass('hidden').hide();
        }
      }

      function clearColumnSelection() {
        $('#cb-select-all-columns').prop('checked', false);
        $('.col-cb').prop('checked', false);
        updateColumnSelectionUI();
      }

      function executeBulkDropColumns() {
        const checked = $('.col-cb:checked').map(function () { return $(this).val(); }).get();
        if (checked.length === 0) return;

        Swal.fire({
          title: `Drop ${checked.length} Selected Column(s)?`,
          html: `Are you sure you want to drop column(s) from table <b>${_tableName}</b>?<br><br><span class="font-mono text-xs text-rose-600 font-bold">${checked.join(', ')}</span>`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#be123c',
          confirmButtonText: `Yes, Drop ${checked.length} Column(s)`,
        }).then(function (result) {
          if (result.isConfirmed) {
            $.ajax({
              url: `${apiBasePath}/${_tableName}/drop-columns`,
              type: 'POST',
              contentType: 'application/json',
              data: JSON.stringify({ columns: checked }),
              success: function (res) {
                if (res.success) {
                  window.showToast('success', res.message);
                  clearColumnSelection();
                  loadTableSchema();
                }
              },
              error: function (xhr) {
                window.handleAjaxError(xhr, 'Failed to drop columns.');
              }
            });
          }
        });
      }

      function confirmSingleDropColumn(columnName) {
        Swal.fire({
          title: `Drop Column '${columnName}'?`,
          text: `Column '${columnName}' and its stored data will be permanently removed from '${_tableName}'!`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#be123c',
          confirmButtonText: 'Yes, Drop Column',
        }).then(function (result) {
          if (result.isConfirmed) {
            $.ajax({
              url: `${apiBasePath}/${_tableName}/drop-columns`,
              type: 'POST',
              contentType: 'application/json',
              data: JSON.stringify({ columns: [columnName] }),
              success: function (res) {
                if (res.success) {
                  window.showToast('success', res.message);
                  loadTableSchema();
                }
              },
              error: function (xhr) {
                window.handleAjaxError(xhr, 'Failed to drop column.');
              }
            });
          }
        });
      }

      function openModifyColModal(colName, colData) {
        $('#mc-original-name').val(colName);
        $('#mc-col-label').text('`' + colName + '`');
        $('#mc-new-name').val('');

        const rawType  = (colData.type || 'VARCHAR').toUpperCase();
        const typeMatch = rawType.match(/^([A-Z]+)\(?([^)]*)\)?/);
        const baseType  = typeMatch ? typeMatch[1] : 'VARCHAR';
        const baseLen   = typeMatch && typeMatch[2] ? typeMatch[2] : '';

        const $typeSelect = $('#mc-type');
        const matchOpt = $typeSelect.find('option').filter(function () {
          return $(this).val() === baseType;
        });
        if (matchOpt.length) {
          $typeSelect.val(baseType);
        } else {
          $typeSelect.val('VARCHAR');
        }

        $('#mc-length').val(baseLen);
        $('#mc-nullable').prop('checked', !!colData.nullable);
        $('#mc-unsigned').prop('checked', false);
        $('#mc-default').val(colData.default !== null && colData.default !== undefined ? colData.default : '');
        $('#mc-comment').val(colData.comment || '');

        applyModalLengthRules($('#mc-type'));

        $('#modal-modify-col').removeClass('hidden');
        setTimeout(function () { $('#mc-new-name').focus(); }, 100);
      }

      function closeModifyColModal() {
        $('#modal-modify-col').addClass('hidden');
        $('#form-modify-col')[0].reset();
      }

      function submitModifyCol() {
        const originalName = $('#mc-original-name').val();
        const newName      = $('#mc-new-name').val().trim();
        const $submitBtn   = $('#form-modify-col button[type="submit"]');

        const payload = {
          type:     $('#mc-type').val(),
          length:   $('#mc-length').val().trim(),
          nullable: $('#mc-nullable').is(':checked') ? 1 : 0,
          unsigned: $('#mc-unsigned').is(':checked') ? 1 : 0,
          default:  $('#mc-default').val(),
          comment:  $('#mc-comment').val().trim(),
        };

        if (newName && newName !== originalName) {
          payload.new_name = newName;
        }

        $submitBtn.prop('disabled', true).html('<i class="fa-solid fa-circle-notch fa-spin text-xs"></i> Applying…');

        $.ajax({
          url: `${apiBasePath}/${_tableName}/columns/${originalName}`,
          type: 'PUT',
          contentType: 'application/json',
          data: JSON.stringify(payload),
          success: function (res) {
            $submitBtn.prop('disabled', false).html('<i class="fa-solid fa-pen-to-square text-xs"></i> Apply Modification');
            if (res.success) {
              window.showToast('success', res.message);
              closeModifyColModal();
              clearColumnSelection();
              loadTableSchema();
            }
          },
          error: function (xhr) {
            $submitBtn.prop('disabled', false).html('<i class="fa-solid fa-pen-to-square text-xs"></i> Apply Modification');
            window.handleAjaxError(xhr, 'Failed to modify column.');
          }
        });
      }

      function renderIndexes(indexes) {
        const $tbody = $('#tbody-indexes');
        let html = '';
        $.each(indexes, function (i, idx) {
          const colsStr = idx.columns.join(', ');
          html += `
            <tr class="hover:bg-slate-50 transition">
              <td class="p-3.5 font-bold text-slate-900 font-mono">${idx.name}</td>
              <td class="p-3.5"><span class="px-2.5 py-1 rounded-md text-[10px] font-bold ${idx.type === 'PRIMARY' ? 'bg-rose-50 text-rose-600 border border-rose-200' : (idx.unique ? 'bg-amber-50 text-amber-600 border border-amber-200' : 'bg-slate-100 text-slate-600')}">${idx.type}</span></td>
              <td class="p-3.5 font-bold text-slate-700 font-mono">${colsStr}</td>
              <td class="p-3.5 text-right font-mono text-slate-500">${idx.cardinality || '-'}</td>
              <td class="p-3.5 text-right">
                <button data-index="${idx.name}" class="btn-drop-index px-3 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition border border-rose-200 cursor-pointer">
                  Drop Index
                </button>
              </td>
            </tr>
          `;
        });
        $tbody.html(html);
      }

      function renderFks(fks) {
        const $tbody = $('#tbody-fks');
        if (fks.length === 0) {
          $tbody.html(`<tr><td colspan="6" class="p-6 text-center text-slate-400 italic">No foreign key constraints defined on table \`${_tableName}\`.</td></tr>`);
          return;
        }
        let html = '';
        $.each(fks, function (i, fk) {
          html += `
            <tr class="hover:bg-slate-50 transition">
              <td class="p-3.5 font-bold text-slate-900 font-mono">${fk.name || fk.constraint_name}</td>
              <td class="p-3.5 text-brand-600 font-bold font-mono">${fk.column}</td>
              <td class="p-3.5 text-slate-700 font-mono">${fk.referenced_table || fk.foreign_table}</td>
              <td class="p-3.5 text-emerald-600 font-bold font-mono">${fk.referenced_column || fk.foreign_column}</td>
              <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-slate-100 text-[10px] font-bold text-slate-600">${fk.on_delete || 'CASCADE'}</span></td>
              <td class="p-3.5"><span class="px-2 py-0.5 rounded bg-slate-100 text-[10px] font-bold text-slate-600">${fk.on_update || 'CASCADE'}</span></td>
            </tr>
          `;
        });
        $tbody.html(html);
      }

      const FILTER_OPERATORS = [
        { value: 'contains',              label: 'contains',              hasValue: true  },
        { value: 'does_not_contain',      label: 'does not contain',      hasValue: true  },
        { value: '=',                     label: '= equals',              hasValue: true  },
        { value: '!=',                    label: '≠ not equals',          hasValue: true  },
        { value: 'begins_with',           label: 'begins with',           hasValue: true  },
        { value: 'does_not_begin_with',   label: 'does not begin with',   hasValue: true  },
        { value: 'ends_with',             label: 'ends with',             hasValue: true  },
        { value: 'does_not_end_with',     label: 'does not end with',     hasValue: true  },
        { value: '<',                     label: '< less than',           hasValue: true  },
        { value: '<=',                    label: '≤ less or equal',       hasValue: true  },
        { value: '>',                     label: '> greater than',        hasValue: true  },
        { value: '>=',                    label: '≥ greater or equal',    hasValue: true  },
        { value: 'is_null',               label: 'is null',               hasValue: false },
        { value: 'is_not_null',           label: 'is not null',           hasValue: false },
        { value: 'is_empty',              label: 'is empty',              hasValue: false },
        { value: 'is_not_empty',          label: 'is not empty',          hasValue: false },
      ];

      let _filterRuleCount = 0;

      function buildOperatorOptions(selected) {
        let html = '';
        $.each(FILTER_OPERATORS, function (i, op) {
          const sel = (selected === op.value) ? 'selected' : '';
          html += `<option value="${op.value}" data-has-value="${op.hasValue}" ${sel}>${op.label}</option>`;
        });
        return html;
      }

      function buildColumnOptions(selected) {
        if (!_tableDetails || !_tableDetails.columns) return '<option value="">— column —</option>';
        let html = '';
        $.each(_tableDetails.columns, function (i, col) {
          const sel = (selected === col.name) ? 'selected' : '';
          html += `<option value="${col.name}" ${sel}>${col.name}</option>`;
        });
        return html;
      }

      function addFilterRule(preset) {
        _filterRuleCount++;
        const ruleId = 'filter-rule-' + _filterRuleCount;
        const defaultCol  = (preset && preset.column)   || (_tableDetails && _tableDetails.columns && _tableDetails.columns[0] ? _tableDetails.columns[0].name : '');
        const defaultOp   = (preset && preset.operator)  || 'contains';
        const defaultVal  = (preset && preset.value)     || '';
        const isEnabled   = (preset && preset.enabled === false) ? false : true;

        const firstOpObj = FILTER_OPERATORS.find(function (o) { return o.value === defaultOp; }) || FILTER_OPERATORS[0];
        const valHidden  = !firstOpObj.hasValue ? 'style="visibility:hidden;"' : '';

        const row = `
          <div class="filter-rule-row flex items-center gap-2 bg-white border border-indigo-100 rounded-xl px-3 py-2 shadow-xs" id="${ruleId}">
            <input type="checkbox" class="filter-rule-enabled w-3.5 h-3.5 rounded border-indigo-300 text-indigo-600 cursor-pointer flex-shrink-0" ${isEnabled ? 'checked' : ''} title="Enable this rule">
            <span class="filter-and-badge text-[9px] font-extrabold uppercase tracking-wider text-indigo-400 w-7 flex-shrink-0 text-center">AND</span>
            <select class="filter-col-select bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs font-semibold text-slate-800 focus:outline-none focus:border-indigo-400 min-w-[110px] cursor-pointer">
              ${buildColumnOptions(defaultCol)}
            </select>
            <select class="filter-op-select bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-xs font-semibold text-slate-700 focus:outline-none focus:border-indigo-400 min-w-[150px] cursor-pointer">
              ${buildOperatorOptions(defaultOp)}
            </select>
            <div class="filter-val-wrap flex-1 min-w-[100px]" ${valHidden}>
              <input type="text" class="filter-val-input w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-mono text-slate-800 focus:outline-none focus:border-indigo-400 placeholder-slate-300" value="${defaultVal}" placeholder="<?> value…">
            </div>
            <button type="button" class="btn-remove-filter-rule flex-shrink-0 w-6 h-6 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-500 hover:text-rose-700 flex items-center justify-center transition cursor-pointer border border-rose-200" title="Remove rule">
              <i class="fa-solid fa-xmark text-[9px]"></i>
            </button>
          </div>`;

        $('#filter-rules-container').append(row);
        $('#filter-rules-empty').hide();
        syncFilterBadge();
        syncFirstRowAndBadge();
      }

      function syncFirstRowAndBadge() {
        const $rows = $('#filter-rules-container .filter-rule-row');
        $rows.find('.filter-and-badge').show();
        $rows.first().find('.filter-and-badge').hide();
      }

      function syncFilterBadge() {
        const total = $('#filter-rules-container .filter-rule-row').length;
        const active = $('#filter-rules-container .filter-rule-enabled:checked').length;
        const $badge = $('#filter-active-badge');
        if (total > 0 && active > 0) {
          $badge.text(active).removeClass('hidden').css('display', 'inline-flex');
        } else {
          $badge.addClass('hidden').hide();
        }
        if (total === 0) {
          $('#filter-rules-empty').show();
        }
      }

      function collectFilterRules() {
        const rules = [];
        $('#filter-rules-container .filter-rule-row').each(function () {
          const $row    = $(this);
          const enabled = $row.find('.filter-rule-enabled').is(':checked');
          const column   = $row.find('.filter-col-select').val();
          const operator = $row.find('.filter-op-select').val();
          const value    = $row.find('.filter-val-input').val().trim();
          rules.push({ enabled: enabled, column: column, operator: operator, value: value });
        });
        return rules;
      }

      function loadDataRows(page = 1) {
        _currentPage = page;
        const $grid  = $('#data-grid-table');
        const search = $('#data-search-input').val().trim();
        const rules  = collectFilterRules();
        const activeFilters = rules.filter(function (r) { return r.enabled && r.column; });

        $grid.html(`<thead><tr><td class="p-6 text-center text-slate-400 font-sans"><i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Loading records…</td></tr></thead>`);

        const reqData = { page: page, per_page: 25, search: search };
        if (activeFilters.length > 0) {
          reqData.filters = JSON.stringify(activeFilters);
        }

        $.ajax({
          url: `${apiBasePath}/${_tableName}/data`,
          type: 'GET',
          data: reqData,
          success: function (res) {
            if (res.success) {
              const d = res.data;
              const activeCount = activeFilters.length;
              const filterHint = activeCount > 0 ? ` · <span class="text-indigo-600 font-extrabold">${activeCount} filter${activeCount > 1 ? 's' : ''} active</span>` : '';
              $('#data-pagination-info').html(`Total <b>${d.total}</b> records (Page ${d.current_page} of ${d.last_page})${filterHint}`);
              $('#data-current-page-text').text(`Page ${d.current_page} of ${d.last_page}`);

              const rows = d.data || [];
              if (rows.length === 0) {
                const emptyMsg = activeCount > 0
                  ? `<tr><td colspan="999" class="p-8 text-center font-sans"><div class="inline-flex flex-col items-center gap-2 text-slate-400"><i class="fa-solid fa-filter text-2xl text-indigo-200"></i><span class="text-sm font-semibold text-slate-500">No records match your filter criteria</span><span class="text-xs text-slate-400">Try adjusting the filter rules or click Reset.</span></div></td></tr>`
                  : `<tr><td class="p-6 text-center text-slate-400 italic font-sans">No records found in table \`${_tableName}\`.</td></tr>`;
                $grid.html(emptyMsg);
                return;
              }

              const cols = d.columns || Object.keys(rows[0]);

              let headerHtml = `<thead class="bg-slate-100 border-b border-slate-200 text-[10px] uppercase font-bold text-slate-500 sticky top-0 z-10"><tr>`;
              $.each(cols, function (i, c) {
                headerHtml += `<th class="p-3 whitespace-nowrap">${c}</th>`;
              });
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
                  bodyHtml += `<td class="p-3 max-w-[200px] truncate" title="${val !== null ? String(val).replace(/"/g, '&quot;') : 'NULL'}">${display}</td>`;
                });
                bodyHtml += `</tr>`;
              });
              bodyHtml += `</tbody>`;

              $grid.html(headerHtml + bodyHtml);
            }
          },
          error: function (xhr) {
            window.handleAjaxError(xhr, 'Failed to fetch table rows.');
          }
        });
      }

      function changeDataPage(delta) {
        const newPage = _currentPage + delta;
        if (newPage >= 1) {
          loadDataRows(newPage);
        }
      }

      function openAddIndexModal(preselectedCols = []) {
        if (!_tableDetails) return;
        $('#form-add-index')[0].reset();
        $('#ai-index-name').val(`idx_${_tableName}_` + Math.floor(Math.random() * 100));

        const $container = $('#ai-columns-checkboxes');
        let html = '';
        $.each(_tableDetails.columns || [], function (i, c) {
          const isChecked = $.inArray(c.name, preselectedCols) !== -1 ? 'checked' : '';
          html += `
            <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
              <input type="checkbox" name="ai_col" value="${c.name}" ${isChecked} class="rounded border-slate-300 text-brand-600">
              <span class="font-mono">${c.name}</span> <span class="text-[10px] text-slate-400">(${c.type})</span>
            </label>
          `;
        });
        $container.html(html);

        $('#modal-add-index').removeClass('hidden');
      }

      function closeAddIndexModal() {
        $('#modal-add-index').addClass('hidden');
      }

      function submitAddIndex() {
        const idxName = $('#ai-index-name').val().trim();
        const idxType = $('#ai-index-type').val();
        const checked = $('input[name="ai_col"]:checked').map(function () { return $(this).val(); }).get();

        if (checked.length === 0) {
          window.showToast('warning', 'Please select at least one column for the index.');
          return;
        }

        $.ajax({
          url: `${apiBasePath}/${_tableName}/indexes`,
          type: 'POST',
          contentType: 'application/json',
          data: JSON.stringify({
            index_name: idxName,
            index_type: idxType,
            columns: checked,
          }),
          success: function (res) {
            if (res.success) {
              window.showToast('success', res.message);
              closeAddIndexModal();
              loadTableSchema();
              switchTab('indexes');
            }
          },
          error: function (xhr) {
            window.handleAjaxError(xhr, 'Failed to add index.');
          }
        });
      }

      function confirmDropIndex(indexName) {
        Swal.fire({
          title: `Drop Index '${indexName}'?`,
          text: `Are you sure you want to drop index '${indexName}' from table '${_tableName}'?`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#e11d48',
          confirmButtonText: 'Yes, Drop Index',
        }).then(function (result) {
          if (result.isConfirmed) {
            $.ajax({
              url: `${apiBasePath}/${_tableName}/indexes/${indexName}`,
              type: 'DELETE',
              success: function (res) {
                if (res.success) {
                  window.showToast('success', res.message);
                  loadTableSchema();
                  switchTab('indexes');
                }
              },
              error: function (xhr) {
                window.handleAjaxError(xhr, 'Failed to drop index.');
              }
            });
          }
        });
      }

      function executePageSqlQuery() {
        const sql = $('#page-sql-editor').val().trim();
        if (!sql) {
          window.showToast('warning', 'Please enter a SQL query.');
          return;
        }

        const $output = $('#page-sql-output-container');
        const $btn = $('#btn-page-sql-exec');

        $btn.prop('disabled', true).html(`<i class="fa-solid fa-circle-notch fa-spin"></i> Executing…`);
        $output.removeClass('hidden').html(`<div class="p-6 text-center text-slate-400"><i class="fa-solid fa-circle-notch fa-spin"></i> Running SQL query…</div>`);

        $.ajax({
          url: `${apiBasePath}/execute-sql`,
          type: 'POST',
          contentType: 'application/json',
          data: JSON.stringify({ sql: sql }),
          success: function (res) {
            $btn.prop('disabled', false).html(`<i class="fa-solid fa-play text-xs"></i> Execute Query`);

            if (res.success) {
              const d = res.data;
              if (d.type === 'SELECT' || d.type === 'SHOW' || d.type === 'EXPLAIN' || d.type === 'DESCRIBE') {
                const rows = d.rows || [];
                if (rows.length === 0) {
                  $output.html(`<div class="p-4 text-center text-slate-400 italic font-sans">Query executed in ${d.execution_time}. 0 rows returned.</div>`);
                  return;
                }

                const cols = d.columns || Object.keys(rows[0]);
                let headerHtml = `<thead class="bg-slate-100 border-b border-slate-200 text-[10px] uppercase font-bold text-slate-500"><tr>`;
                $.each(cols, function (i, c) { headerHtml += `<th class="p-2.5">${c}</th>`; });
                headerHtml += `</tr></thead>`;

                let bodyHtml = `<tbody class="divide-y divide-slate-100 text-[11px]">`;
                $.each(rows, function (i, r) {
                  bodyHtml += `<tr class="hover:bg-slate-50 transition">`;
                  $.each(cols, function (j, c) {
                    const val = r[c];
                    bodyHtml += `<td class="p-2.5 max-w-xs truncate" title="${val}">${val !== null ? val : '<span class="text-slate-300 font-sans italic">NULL</span>'}</td>`;
                  });
                  bodyHtml += `</tr>`;
                });
                bodyHtml += `</tbody>`;

                $output.html(`<table class="w-full text-left border-collapse">${headerHtml}${bodyHtml}</table>`);
              } else {
                $output.html(`
                  <div class="p-4 bg-emerald-50 text-emerald-800 text-xs font-sans font-medium flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                    <span>Query executed successfully. Affected rows: ${d.affected_rows}</span>
                  </div>
                `);
              }
            }
          },
          error: function (xhr) {
            $btn.prop('disabled', false).html(`<i class="fa-solid fa-play text-xs"></i> Execute Query`);
            const errMsg = xhr.responseJSON?.message || 'Database query error.';
            $output.html(`
              <div class="p-4 bg-rose-50 text-rose-700 text-xs font-mono leading-relaxed">
                <i class="fa-solid fa-circle-exclamation mr-1 text-rose-600"></i> ${errMsg}
              </div>
            `);
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
                  loadTableSchema();
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
                  window.location.href = webBasePath;
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
