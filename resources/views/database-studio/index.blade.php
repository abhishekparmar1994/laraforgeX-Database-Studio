@extends('database-studio::layouts.app')

@section('title', 'Database Studio & Table Explorer')

@section('content')
<div class="space-y-6 font-sans relative" x-data="databaseStudioIndex()">

    <!-- Header Hero Banner -->
    <div class="theme-hero-banner bg-gradient-to-r from-slate-900 via-brand-900 to-indigo-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
      <div class="absolute right-0 top-0 translate-x-8 -translate-y-8 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
      <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-amber-300 text-xs font-bold uppercase tracking-widest mb-1">
            <i class="fa-solid fa-table-cells"></i> Database Studio Dashboard
          </div>
          <h1 class="text-2xl font-extrabold tracking-tight">Database Tables & Metrics</h1>
          <p class="text-sm text-slate-200 mt-1 max-w-2xl">
            Inspect, create, alter database tables, execute custom SQL queries, perform bulk actions, and browse live records.
          </p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
          <a href="{{ url(config('database-studio.path', 'database-studio') . '/console') }}"
            class="px-3.5 py-2.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-200 font-bold text-xs transition border border-amber-400/30 inline-flex items-center gap-2 cursor-pointer no-underline">
            <i class="fa-solid fa-terminal text-xs"></i> SQL Console
          </a>
          <button type="button" @click="fetchTables()"
            class="px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/20 inline-flex items-center gap-2 cursor-pointer">
            <i class="fa-solid fa-rotate text-xs" :class="{'fa-spin': loading}"></i> Refresh
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
          <h3 class="text-2xl font-black text-slate-900 mt-0.5" x-text="metrics.tables_count || '—'"></h3>
        </div>
        <div class="h-10 w-10 rounded-xl bg-cyan-50 border border-cyan-100 flex items-center justify-center text-cyan-600">
          <i class="fa-solid fa-table-cells text-base"></i>
        </div>
      </div>

      <div class="bg-white border border-slate-200/80 p-4 rounded-2xl shadow-sm flex items-center justify-between">
        <div>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Database Rows</p>
          <h3 class="text-2xl font-black text-slate-900 mt-0.5" x-text="metrics.total_rows ? metrics.total_rows.toLocaleString() : '—'"></h3>
        </div>
        <div class="h-10 w-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
          <i class="fa-solid fa-database text-base"></i>
        </div>
      </div>

      <div class="bg-white border border-slate-200/80 p-4 rounded-2xl shadow-sm flex items-center justify-between">
        <div>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Disk Size</p>
          <h3 class="text-2xl font-black text-slate-900 mt-0.5" x-text="metrics.total_size || '—'"></h3>
        </div>
        <div class="h-10 w-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
          <i class="fa-solid fa-hard-drive text-base"></i>
        </div>
      </div>

      <div class="bg-white border border-slate-200/80 p-4 rounded-2xl shadow-sm flex items-center justify-between">
        <div>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Database Connection</p>
          <h3 class="text-lg font-bold text-slate-800 mt-0.5 truncate max-w-[130px]" x-text="metrics.database || '—'"></h3>
        </div>
        <div class="h-10 w-10 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
          <i class="fa-solid fa-server text-base"></i>
        </div>
      </div>
    </div>

    <!-- Controls & Filter Bar -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3">
      <div class="relative w-full md:w-80">
        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
        <input type="text" x-model="searchQuery" placeholder="Filter tables by name..."
          class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
      </div>

      <div class="flex items-center gap-2 w-full md:w-auto justify-end">
        <button type="button" @click="toggleSelectAll()"
          class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-200">
          <span x-text="allSelected ? 'Deselect All' : 'Select All'"></span>
        </button>

        <button type="button" @click="runBulkAction('truncate')" :disabled="selectedTables.length === 0"
          class="px-3.5 py-2 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 font-bold text-xs transition border border-amber-200 disabled:opacity-50 disabled:cursor-not-allowed">
          <i class="fa-solid fa-eraser text-xs"></i> Truncate Selected (<span x-text="selectedTables.length"></span>)
        </button>

        <button type="button" @click="runBulkAction('drop')" :disabled="selectedTables.length === 0"
          class="px-3.5 py-2 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-xs transition border border-rose-200 disabled:opacity-50 disabled:cursor-not-allowed">
          <i class="fa-solid fa-trash-can text-xs"></i> Drop Selected (<span x-text="selectedTables.length"></span>)
        </button>
      </div>
    </div>

    <!-- Table Data Grid -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
              <th class="py-3 px-4 w-10 text-center">
                <input type="checkbox" @change="toggleSelectAll()" :checked="allSelected" class="rounded border-slate-300">
              </th>
              <th class="py-3 px-4">Table Name</th>
              <th class="py-3 px-4">Engine</th>
              <th class="py-3 px-4 text-right">Est. Rows</th>
              <th class="py-3 px-4 text-right">Data Size</th>
              <th class="py-3 px-4 text-right">Index Size</th>
              <th class="py-3 px-4 text-right">Total Disk</th>
              <th class="py-3 px-4 text-center">Collation</th>
              <th class="py-3 px-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs">
            <template x-for="table in filteredTables" :key="table.name">
              <tr class="hover:bg-slate-50/80 transition group">
                <td class="py-3 px-4 text-center">
                  <input type="checkbox" :value="table.name" x-model="selectedTables" :disabled="table.is_protected" class="rounded border-slate-300">
                </td>
                <td class="py-3 px-4 font-bold text-slate-900">
                  <div class="flex items-center gap-2">
                    <i class="fa-solid fa-table text-brand-500"></i>
                    <a :href="'{{ url(config('database-studio.path', 'database-studio')) }}/manage/' + table.name" 
                       class="hover:text-brand-600 no-underline font-extrabold text-slate-900" x-text="table.name"></a>
                    <span x-if="table.is_protected" class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">System</span>
                  </div>
                </td>
                <td class="py-3 px-4 text-slate-600" x-text="table.engine"></td>
                <td class="py-3 px-4 text-right font-mono text-slate-700" x-text="table.rows.toLocaleString()"></td>
                <td class="py-3 px-4 text-right font-mono text-slate-600" x-text="table.data_size"></td>
                <td class="py-3 px-4 text-right font-mono text-slate-600" x-text="table.index_size"></td>
                <td class="py-3 px-4 text-right font-mono font-bold text-brand-600" x-text="table.total_size"></td>
                <td class="py-3 px-4 text-center text-slate-500 text-[11px]" x-text="table.collation"></td>
                <td class="py-3 px-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <a :href="'{{ url(config('database-studio.path', 'database-studio')) }}/manage/' + table.name"
                      class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition inline-flex items-center gap-1 no-underline">
                      <i class="fa-solid fa-eye text-xs"></i> Inspect
                    </a>
                    <button type="button" @click="truncateSingle(table.name)" :disabled="table.is_protected"
                      class="px-2 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-[11px] transition border border-amber-200/60 disabled:opacity-30">
                      <i class="fa-solid fa-eraser"></i>
                    </button>
                    <button type="button" @click="dropSingle(table.name)" :disabled="table.is_protected"
                      class="px-2 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] transition border border-rose-200/60 disabled:opacity-30">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </template>
            <tr x-show="filteredTables.length === 0">
              <td colspan="9" class="py-8 text-center text-slate-400">
                <i class="fa-solid fa-folder-open text-2xl mb-2 block"></i>
                No database tables found matching your search.
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
function databaseStudioIndex() {
  return {
    loading: false,
    metrics: {},
    tables: [],
    searchQuery: '',
    selectedTables: [],
    apiPrefix: "{{ url(config('database-studio.api_prefix', 'api/v1/database-manager')) }}",

    init() {
      this.fetchTables();
    },

    get filteredTables() {
      if (!this.searchQuery) return this.tables;
      return this.tables.filter(t => t.name.toLowerCase().includes(this.searchQuery.toLowerCase()));
    },

    get allSelected() {
      return this.tables.length > 0 && this.selectedTables.length === this.tables.filter(t => !t.is_protected).length;
    },

    toggleSelectAll() {
      if (this.allSelected) {
        this.selectedTables = [];
      } else {
        this.selectedTables = this.tables.filter(t => !t.is_protected).map(t => t.name);
      }
    },

    async fetchTables() {
      this.loading = true;
      try {
        const res = await fetch(this.apiPrefix);
        const json = await res.json();
        if (json.success) {
          this.metrics = json.data;
          this.tables = json.data.tables || [];
        }
      } catch (err) {
        alert('Failed to load database tables.');
      } finally {
        this.loading = false;
      }
    },

    async truncateSingle(table) {
      if (!confirm(`Are you sure you want to truncate table "${table}"? All records will be wiped.`)) return;
      try {
        const res = await fetch(`${this.apiPrefix}/${table}/truncate`, { method: 'POST' });
        const json = await res.json();
        alert(json.message);
        this.fetchTables();
      } catch (e) {
        alert('Error truncating table.');
      }
    },

    async dropSingle(table) {
      if (!confirm(`CRITICAL WARNING: Drop table "${table}" completely? This action cannot be undone.`)) return;
      try {
        const res = await fetch(`${this.apiPrefix}/${table}`, { method: 'DELETE' });
        const json = await res.json();
        alert(json.message);
        this.fetchTables();
      } catch (e) {
        alert('Error dropping table.');
      }
    },

    async runBulkAction(action) {
      if (this.selectedTables.length === 0) return;
      if (!confirm(`Are you sure you want to ${action} ${this.selectedTables.length} selected table(s)?`)) return;

      try {
        const res = await fetch(`${this.apiPrefix}/bulk-action`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action, tables: this.selectedTables })
        });
        const json = await res.json();
        alert(json.message);
        this.selectedTables = [];
        this.fetchTables();
      } catch (e) {
        alert('Bulk action failed.');
      }
    }
  }
}
</script>
@endsection
