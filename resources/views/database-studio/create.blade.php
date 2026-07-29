@extends('database-studio::layouts.app')

@section('title', 'Create New Table — Database Studio')

@section('content')
<div class="space-y-6 font-sans relative" x-data="createTableWizard()">

  <!-- Header Banner -->
  <div class="bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <div class="flex items-center gap-2 text-emerald-300 text-xs font-bold uppercase tracking-widest mb-1">
          <i class="fa-solid fa-plus-circle"></i> Schema Builder Wizard
        </div>
        <h1 class="text-2xl font-extrabold tracking-tight">Visual Table Creator</h1>
        <p class="text-sm text-emerald-100 mt-1 max-w-2xl">
          Design table structure, define columns, data types, indexes, and constraints interactively.
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

  <!-- Form Container -->
  <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-6">
    
    <!-- Step 1: Table Options -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-b border-slate-100 pb-6">
      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Table Name *</label>
        <input type="text" x-model="tableName" placeholder="e.g. products, orders"
          class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-mono">
      </div>
      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Storage Engine</label>
        <select x-model="engine" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-white">
          <option value="InnoDB">InnoDB (Transactional)</option>
          <option value="MyISAM">MyISAM (Fast Read)</option>
          <option value="MEMORY">MEMORY (In-Ram)</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Collation</label>
        <select x-model="collation" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-white">
          <option value="utf8mb4_unicode_ci">utf8mb4_unicode_ci (Recommended)</option>
          <option value="utf8mb4_general_ci">utf8mb4_general_ci</option>
          <option value="utf8_general_ci">utf8_general_ci</option>
        </select>
      </div>
    </div>

    <!-- Step 2: Columns Editor -->
    <div>
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Columns Definition</h3>
        <button type="button" @click="addColumn()"
          class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs border border-emerald-200 flex items-center gap-1.5 cursor-pointer">
          <i class="fa-solid fa-plus"></i> Add Column
        </button>
      </div>

      <div class="overflow-x-auto border border-slate-200 rounded-xl">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-600 uppercase text-[10px]">
              <th class="py-2.5 px-3">Column Name</th>
              <th class="py-2.5 px-3 w-40">Data Type</th>
              <th class="py-2.5 px-3 w-24">Length</th>
              <th class="py-2.5 px-3 w-20 text-center">Nullable</th>
              <th class="py-2.5 px-3">Default</th>
              <th class="py-2.5 px-3 w-16 text-center">A_I</th>
              <th class="py-2.5 px-3 w-16 text-center">PK</th>
              <th class="py-2.5 px-3 w-10 text-center"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <template x-for="(col, idx) in columns" :key="idx">
              <tr class="hover:bg-slate-50/50">
                <td class="py-2 px-3">
                  <input type="text" x-model="col.name" placeholder="column_name"
                    class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 font-mono">
                </td>
                <td class="py-2 px-3">
                  <select x-model="col.type" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 bg-white">
                    <option value="biginteger">BIGINT</option>
                    <option value="integer">INT</option>
                    <option value="string">VARCHAR</option>
                    <option value="text">TEXT</option>
                    <option value="boolean">BOOLEAN</option>
                    <option value="datetime">DATETIME</option>
                    <option value="date">DATE</option>
                    <option value="decimal">DECIMAL</option>
                    <option value="json">JSON</option>
                  </select>
                </td>
                <td class="py-2 px-3">
                  <input type="text" x-model="col.length" placeholder="255"
                    class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 font-mono">
                </td>
                <td class="py-2 px-3 text-center">
                  <input type="checkbox" x-model="col.nullable" class="rounded border-slate-300">
                </td>
                <td class="py-2 px-3">
                  <input type="text" x-model="col.default" placeholder="NULL"
                    class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200">
                </td>
                <td class="py-2 px-3 text-center">
                  <input type="checkbox" x-model="col.auto_increment" class="rounded border-slate-300">
                </td>
                <td class="py-2 px-3 text-center">
                  <input type="checkbox" x-model="col.primary" class="rounded border-slate-300">
                </td>
                <td class="py-2 px-3 text-center">
                  <button type="button" @click="removeColumn(idx)" :disabled="columns.length === 1"
                    class="text-rose-500 hover:text-rose-700 disabled:opacity-30">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Step 3: Options & Submit -->
    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
      <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer">
        <input type="checkbox" x-model="addTimestamps" class="rounded border-slate-300">
        Auto-append Standard Timestamps (<code class="text-slate-500 font-mono">created_at</code>, <code class="text-slate-500 font-mono">updated_at</code>)
      </label>

      <button type="button" @click="submitTable()" :disabled="saving"
        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-extrabold text-xs transition shadow-lg shadow-emerald-900/30 flex items-center gap-2 border-0">
        <i class="fa-solid fa-check" :class="{'fa-spin': saving}"></i>
        <span x-text="saving ? 'Creating Table...' : 'Execute & Create Table'"></span>
      </button>
    </div>

  </div>

</div>
@endsection

@section('scripts')
<script>
function createTableWizard() {
  return {
    tableName: '',
    engine: 'InnoDB',
    collation: 'utf8mb4_unicode_ci',
    addTimestamps: true,
    saving: false,
    apiPrefix: "{{ url(config('database-studio.api_prefix', 'api/v1/database-manager')) }}",
    columns: [
      { name: 'id', type: 'biginteger', length: '', nullable: false, default: '', auto_increment: true, primary: true },
      { name: 'name', type: 'string', length: '255', nullable: false, default: '', auto_increment: false, primary: false }
    ],

    addColumn() {
      this.columns.push({ name: '', type: 'string', length: '255', nullable: true, default: '', auto_increment: false, primary: false });
    },

    removeColumn(idx) {
      if (this.columns.length > 1) {
        this.columns.splice(idx, 1);
      }
    },

    async submitTable() {
      if (!this.tableName) {
        alert('Please enter a table name.');
        return;
      }

      this.saving = true;
      try {
        const payload = {
          table_name: this.tableName,
          engine: this.engine,
          collation: this.collation,
          columns: this.columns,
          add_timestamps: this.addTimestamps
        };

        const res = await fetch(`${this.apiPrefix}/create`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });

        const json = await res.json();
        if (json.success) {
          alert(json.message);
          window.location.href = "{{ url(config('database-studio.path', 'database-studio')) }}";
        } else {
          alert('Error: ' + json.message);
        }
      } catch (err) {
        alert('Failed to create table.');
      } finally {
        this.saving = false;
      }
    }
  }
}
</script>
@endsection
