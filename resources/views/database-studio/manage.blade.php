@extends('database-studio::layouts.app')

@section('title', 'Inspect Table ' . $tableName . ' — Database Studio')

@section('content')
<div class="space-y-6 font-sans relative" x-data="manageTableApp('{{ $tableName }}')">

  <!-- Header Banner -->
  <div class="bg-gradient-to-r from-slate-900 via-indigo-900 to-slate-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <div class="flex items-center gap-2 text-indigo-300 text-xs font-bold uppercase tracking-widest mb-1">
          <i class="fa-solid fa-table"></i> Table Inspector & Data Grid
        </div>
        <h1 class="text-2xl font-extrabold tracking-tight flex items-center gap-3">
          <span class="font-mono text-amber-300">{{ $tableName }}</span>
        </h1>
        <p class="text-sm text-slate-200 mt-1 max-w-2xl">
          Inspect schema structure, indexes, foreign keys, alter columns, filter records, and export table data.
        </p>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ url(config('database-studio.path', 'database-studio')) }}" 
           class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/20 inline-flex items-center gap-2 no-underline">
          <i class="fa-solid fa-arrow-left text-xs"></i> Back to Tables
        </a>
      </div>
    </div>
  </div>

  <!-- Navigation Tabs -->
  <div class="flex border-b border-slate-200 gap-2">
    <button type="button" @click="tab = 'data'" :class="tab === 'data' ? 'border-brand-600 text-brand-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700 font-bold'"
      class="px-4 py-3 text-xs border-b-2 transition flex items-center gap-2 cursor-pointer">
      <i class="fa-solid fa-database"></i> Browse Records
    </button>
    <button type="button" @click="tab = 'structure'" :class="tab === 'structure' ? 'border-brand-600 text-brand-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700 font-bold'"
      class="px-4 py-3 text-xs border-b-2 transition flex items-center gap-2 cursor-pointer">
      <i class="fa-solid fa-layer-group"></i> Schema Columns
    </button>
    <button type="button" @click="tab = 'indexes'" :class="tab === 'indexes' ? 'border-brand-600 text-brand-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700 font-bold'"
      class="px-4 py-3 text-xs border-b-2 transition flex items-center gap-2 cursor-pointer">
      <i class="fa-solid fa-key"></i> Indexes & Keys
    </button>
  </div>

  <!-- TAB 1: BROWSE RECORDS -->
  <div x-show="tab === 'data'" class="space-y-4">
    <!-- Controls Bar -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3">
      <div class="relative w-full md:w-80">
        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
        <input type="text" x-model="searchQuery" @keyup.enter="fetchRecords(1)" placeholder="Search records in this table..."
          class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
      </div>

      <div class="flex items-center gap-2 w-full md:w-auto justify-end">
        <a :href="apiPrefix + '/' + tableName + '/export?format=csv&search=' + searchQuery" download
          class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-200 flex items-center gap-1.5 no-underline">
          <i class="fa-solid fa-file-csv text-emerald-600"></i> Export CSV
        </a>
        <a :href="apiPrefix + '/' + tableName + '/export?format=excel&search=' + searchQuery" download
          class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-200 flex items-center gap-1.5 no-underline">
          <i class="fa-solid fa-file-excel text-teal-600"></i> Export Excel
        </a>
      </div>
    </div>

    <!-- Data Grid -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs font-mono">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-600 uppercase">
              <template x-for="col in gridColumns" :key="col">
                <th class="py-2.5 px-3 border-r border-slate-200 last:border-r-0" x-text="col"></th>
              </template>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <template x-for="(row, idx) in records" :key="idx">
              <tr class="hover:bg-slate-50">
                <template x-for="col in gridColumns" :key="col">
                  <td class="py-2 px-3 border-r border-slate-100 last:border-r-0 truncate max-w-xs text-slate-800"
                      x-text="row[col] !== null ? row[col] : 'NULL'"></td>
                </template>
              </tr>
            </template>
            <tr x-show="records.length === 0">
              <td :colspan="gridColumns.length || 1" class="py-8 text-center text-slate-400 font-sans">
                No records found in this table.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer -->
      <div class="p-4 border-t border-slate-100 flex items-center justify-between font-sans text-xs">
        <div class="text-slate-500">
          Page <strong x-text="currentPage"></strong> of <strong x-text="lastPage"></strong> (Total <span x-text="totalRecords"></span> rows)
        </div>
        <div class="flex items-center gap-2">
          <button type="button" @click="fetchRecords(currentPage - 1)" :disabled="currentPage <= 1"
            class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold disabled:opacity-30">
            Previous
          </button>
          <button type="button" @click="fetchRecords(currentPage + 1)" :disabled="currentPage >= lastPage"
            class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold disabled:opacity-30">
            Next
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- TAB 2: SCHEMA COLUMNS -->
  <div x-show="tab === 'structure'" class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden p-6 space-y-4">
    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Columns Definition</h3>
    <div class="overflow-x-auto border border-slate-200 rounded-xl">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-600 uppercase text-[10px]">
            <th class="py-2.5 px-3">Column Name</th>
            <th class="py-2.5 px-3">Full Type</th>
            <th class="py-2.5 px-3 text-center">Nullable</th>
            <th class="py-2.5 px-3">Default</th>
            <th class="py-2.5 px-3">Key</th>
            <th class="py-2.5 px-3">Extra</th>
            <th class="py-2.5 px-3">Comment</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-mono">
          <template x-for="col in schemaColumns" :key="col.name">
            <tr class="hover:bg-slate-50">
              <td class="py-2.5 px-3 font-bold text-slate-900" x-text="col.name"></td>
              <td class="py-2.5 px-3 text-brand-600 font-bold" x-text="col.full_type"></td>
              <td class="py-2.5 px-3 text-center">
                <span :class="col.nullable ? 'text-emerald-600 font-bold' : 'text-slate-400'" x-text="col.nullable ? 'YES' : 'NO'"></span>
              </td>
              <td class="py-2.5 px-3 text-slate-600" x-text="col.default !== null ? col.default : 'NULL'"></td>
              <td class="py-2.5 px-3">
                <span x-show="col.key" class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase bg-amber-100 text-amber-800" x-text="col.key"></span>
              </td>
              <td class="py-2.5 px-3 text-slate-500" x-text="col.extra"></td>
              <td class="py-2.5 px-3 text-slate-400 font-sans" x-text="col.comment"></td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>

  <!-- TAB 3: INDEXES -->
  <div x-show="tab === 'indexes'" class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden p-6 space-y-4">
    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Table Indexes & Keys</h3>
    <div class="overflow-x-auto border border-slate-200 rounded-xl">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-600 uppercase text-[10px]">
            <th class="py-2.5 px-3">Index Name</th>
            <th class="py-2.5 px-3">Type</th>
            <th class="py-2.5 px-3">Indexed Columns</th>
            <th class="py-2.5 px-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-mono">
          <template x-for="idx in indexes" :key="idx.name">
            <tr class="hover:bg-slate-50">
              <td class="py-2.5 px-3 font-bold text-slate-900" x-text="idx.name"></td>
              <td class="py-2.5 px-3 font-bold text-indigo-600" x-text="idx.type"></td>
              <td class="py-2.5 px-3 text-slate-700" x-text="idx.columns.join(', ')"></td>
              <td class="py-2.5 px-3 text-right font-sans">
                <button type="button" @click="dropIndex(idx.name)" :disabled="idx.primary"
                  class="px-2 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] disabled:opacity-30">
                  Drop Index
                </button>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>
function manageTableApp(tableName) {
  return {
    tableName: tableName,
    tab: 'data',
    searchQuery: '',
    currentPage: 1,
    lastPage: 1,
    totalRecords: 0,
    gridColumns: [],
    records: [],
    schemaColumns: [],
    indexes: [],
    apiPrefix: "{{ url(config('database-studio.api_prefix', 'api/v1/database-manager')) }}",

    init() {
      this.fetchSchema();
      this.fetchRecords(1);
    },

    async fetchSchema() {
      try {
        const res = await fetch(`${this.apiPrefix}/${this.tableName}`);
        const json = await res.json();
        if (json.success) {
          this.schemaColumns = json.data.columns || [];
          this.indexes = json.data.indexes || [];
        }
      } catch (err) {
        alert('Failed to load table schema.');
      }
    },

    async fetchRecords(page = 1) {
      this.currentPage = page;
      try {
        const res = await fetch(`${this.apiPrefix}/${this.tableName}/data?page=${page}&search=${this.searchQuery}`);
        const json = await res.json();
        if (json.success) {
          this.gridColumns = json.data.columns || [];
          this.records = json.data.data || [];
          this.totalRecords = json.data.total;
          this.lastPage = json.data.last_page;
        }
      } catch (err) {
        alert('Failed to load records.');
      }
    },

    async dropIndex(indexName) {
      if (!confirm(`Drop index "${indexName}" from table "${this.tableName}"?`)) return;
      try {
        const res = await fetch(`${this.apiPrefix}/${this.tableName}/indexes/${indexName}`, { method: 'DELETE' });
        const json = await res.json();
        alert(json.message);
        this.fetchSchema();
      } catch (e) {
        alert('Failed to drop index.');
      }
    }
  }
}
</script>
@endsection
