@extends('database-studio::layouts.app')

@section('title', 'SQL Query Console — Database Studio')

@section('content')
<div class="space-y-6 font-sans relative" x-data="sqlConsoleApp()">

  <!-- Header Banner -->
  <div class="bg-gradient-to-r from-slate-900 via-amber-900 to-slate-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <div class="flex items-center gap-2 text-amber-300 text-xs font-bold uppercase tracking-widest mb-1">
          <i class="fa-solid fa-terminal"></i> Direct Execution Console
        </div>
        <h1 class="text-2xl font-extrabold tracking-tight">Interactive SQL Console</h1>
        <p class="text-sm text-slate-200 mt-1 max-w-2xl">
          Execute custom raw DDL/DML queries (<code class="font-mono text-amber-200">SELECT</code>, <code class="font-mono text-amber-200">INSERT</code>, <code class="font-mono text-amber-200">UPDATE</code>, <code class="font-mono text-amber-200">ALTER</code>, <code class="font-mono text-amber-200">CREATE</code>) in real-time.
        </p>
      </div>
      <div>
        <a href="{{ url(config('database-studio.path', 'database-studio')) }}" 
           class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/20 inline-flex items-center gap-2 no-underline">
          <i class="fa-solid fa-arrow-left text-xs"></i> Back to Tables
        </a>
      </div>
    </div>
  </div>

  <!-- Console Input Area -->
  <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-4">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
        <i class="fa-solid fa-code text-amber-400"></i> SQL Query Input
      </div>
      <div class="flex items-center gap-2">
        <button type="button" @click="sqlQuery = 'SELECT * FROM users LIMIT 10;'"
          class="px-2.5 py-1 text-[11px] font-bold rounded bg-slate-800 text-slate-300 hover:text-white border border-slate-700 transition">
          Preset: Sample Select
        </button>
        <button type="button" @click="sqlQuery = 'SHOW TABLES;'"
          class="px-2.5 py-1 text-[11px] font-bold rounded bg-slate-800 text-slate-300 hover:text-white border border-slate-700 transition">
          Preset: Show Tables
        </button>
      </div>
    </div>

    <div>
      <textarea x-model="sqlQuery" rows="5" placeholder="Enter raw SQL statement here... (e.g. SELECT * FROM users LIMIT 10;)"
        class="w-full p-4 rounded-xl bg-slate-950 text-slate-100 font-mono text-xs border border-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"></textarea>
    </div>

    <div class="flex items-center justify-between">
      <p class="text-[11px] text-slate-500 font-mono">Tip: Press Run Query to execute directly against default connection.</p>

      <button type="button" @click="runQuery()" :disabled="executing"
        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs transition shadow-lg shadow-amber-500/20 flex items-center gap-2 border-0 cursor-pointer">
        <i class="fa-solid fa-play" :class="{'fa-spin': executing}"></i>
        <span x-text="executing ? 'Executing...' : 'Run SQL Query'"></span>
      </button>
    </div>
  </div>

  <!-- Results Output Area -->
  <div x-show="result" x-cloak class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden p-6 space-y-4">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <span class="px-2.5 py-1 rounded text-xs font-black uppercase tracking-wider"
              :class="result.type === 'SELECT' ? 'bg-cyan-100 text-cyan-800' : 'bg-emerald-100 text-emerald-800'"
              x-text="result.type"></span>
        <span class="text-xs font-bold text-slate-600" x-text="result.affected_rows + ' record(s) returned/affected'"></span>
      </div>
      <div class="text-xs font-mono font-bold text-slate-400 flex items-center gap-1">
        <i class="fa-regular fa-clock"></i> Execution time: <span x-text="result.execution_time" class="text-slate-700"></span>
      </div>
    </div>

    <!-- Data Table Output for Select Queries -->
    <template x-if="result && result.columns && result.columns.length > 0">
      <div class="overflow-x-auto border border-slate-200 rounded-xl">
        <table class="w-full text-left border-collapse text-xs font-mono">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-700 uppercase">
              <template x-for="col in result.columns" :key="col">
                <th class="py-2.5 px-3 border-r border-slate-200 last:border-r-0" x-text="col"></th>
              </template>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <template x-for="(row, idx) in result.rows" :key="idx">
              <tr class="hover:bg-slate-50">
                <template x-for="col in result.columns" :key="col">
                  <td class="py-2 px-3 border-r border-slate-100 last:border-r-0 truncate max-w-xs text-slate-800"
                      x-text="row[col] !== null ? row[col] : 'NULL'"></td>
                </template>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </template>

    <!-- Message for Non-Select Queries -->
    <template x-if="result && (!result.columns || result.columns.length === 0)">
      <div class="p-4 rounded-xl bg-emerald-50 text-emerald-800 font-bold text-xs flex items-center gap-2 border border-emerald-200">
        <i class="fa-solid fa-circle-check text-emerald-600"></i>
        <span>Query executed successfully. Affected rows: <strong x-text="result.affected_rows"></strong></span>
      </div>
    </template>
  </div>

</div>
@endsection

@section('scripts')
<script>
function sqlConsoleApp() {
  return {
    sqlQuery: 'SELECT * FROM users LIMIT 10;',
    executing: false,
    result: null,
    apiPrefix: "{{ url(config('database-studio.api_prefix', 'api/v1/database-manager')) }}",

    async runQuery() {
      if (!this.sqlQuery.trim()) {
        alert('Please enter an SQL statement.');
        return;
      }

      this.executing = true;
      this.result = null;

      try {
        const res = await fetch(`${this.apiPrefix}/execute-sql`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ sql: this.sqlQuery })
        });

        const json = await res.json();
        if (json.success) {
          this.result = json.data;
        } else {
          alert('Query Error: ' + json.message);
        }
      } catch (err) {
        alert('Failed to execute SQL query.');
      } finally {
        this.executing = false;
      }
    }
  }
}
</script>
@endsection
