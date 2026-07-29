@extends('database-studio::layouts.app')

@section('title', 'Create Database Table Architect — Database Studio')

@section('content')
  <div class="space-y-6 font-sans w-full">

    <!-- Header Hero Banner -->
    <div
      class="theme-hero-banner bg-gradient-to-r from-brand-900 via-brand-700 to-indigo-800 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
      <div
        class="absolute right-0 top-0 translate-x-8 -translate-y-8 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none">
      </div>
      <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-brand-200 text-xs font-bold uppercase tracking-widest mb-1">
            <i class="fa-solid fa-wand-magic-sparkles text-amber-300"></i> Full-Screen Table Architect
          </div>
          <h1 class="text-2xl font-extrabold tracking-tight">Create Database Table</h1>
          <p class="text-sm text-brand-100 mt-1 max-w-2xl">
            Design your database schema with column types, length specifications, foreign key relations, and custom
            indexes.
          </p>
        </div>
        <div class="shrink-0">
          <a href="{{ url(config('database-studio.path', 'database-studio')) }}"
            class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/20 inline-flex items-center gap-2 no-underline">
            <i class="fa-solid fa-arrow-left text-xs"></i> Back to Tables List
          </a>
        </div>
      </div>
    </div>

    <!-- Main Creation Form -->
    <form id="form-create-table-page" class="space-y-6">

      <!-- 1. General Table Settings -->
      <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-4">
        <h3
          class="text-xs font-extrabold uppercase tracking-wider text-slate-800 flex items-center gap-2 border-b border-slate-100 pb-3">
          <i class="fa-solid fa-sliders text-brand-500"></i> General Configuration
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Table Name *</label>
            <input type="text" id="ct-table-name" name="table_name" required pattern="[a-zA-Z0-9_]+"
              class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:border-brand-500 focus:bg-white transition"
              placeholder="e.g. orders">
            <span class="text-[10px] text-slate-400 block mt-1">Only alphanumeric characters and underscores</span>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Storage Engine</label>
            <select id="ct-engine" name="engine"
              class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:border-brand-500 focus:bg-white transition">
              <option value="InnoDB" selected>InnoDB (Recommended, Full Foreign Key & Transaction Support)</option>
              <option value="MyISAM">MyISAM (High Speed Read Only)</option>
              <option value="MEMORY">MEMORY (In-Memory Heap Table)</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Default Collation</label>
            <select id="ct-collation" name="collation"
              class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:border-brand-500 focus:bg-white transition">
              <option value="utf8mb4_unicode_ci" selected>utf8mb4_unicode_ci (Universal Multilingual & Emoji)</option>
              <option value="utf8mb4_general_ci">utf8mb4_general_ci</option>
              <option value="utf8_general_ci">utf8_general_ci</option>
            </select>
          </div>
        </div>
      </div>

      <!-- 2. Columns Definition Architect -->
      <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-800 flex items-center gap-2">
            <i class="fa-solid fa-columns text-brand-500"></i> Columns Definition (<span
              id="columns-count-badge">3</span>)
          </h3>
          <button type="button" id="btn-add-column-row"
            class="px-4 py-2 rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-600 font-bold text-xs transition border border-brand-200 inline-flex items-center gap-1.5 cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i> Add Column
          </button>
        </div>

        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-xs">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr
                class="bg-slate-100/80 border-b border-slate-200 text-[10px] font-extrabold uppercase tracking-wider text-slate-500 select-none">
                <th class="p-3">Column Name *</th>
                <th class="p-3">Data Type *</th>
                <th class="p-3">Length / Values</th>
                <th class="p-3 text-center">Nullable</th>
                <th class="p-3">Default Value</th>
                <th class="p-3 text-center">Auto Inc</th>
                <th class="p-3 text-center">Primary Key</th>
                <th class="p-3 text-center">Action</th>
              </tr>
            </thead>
            <tbody id="ct-columns-tbody" class="divide-y divide-slate-100">
            </tbody>
          </table>
        </div>
      </div>

      <!-- 3. Foreign Keys & Indexes Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Foreign Keys Constraint Builder -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-4">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-800 flex items-center gap-2">
              <i class="fa-solid fa-link text-indigo-500"></i> Foreign Key Constraints
            </h3>
            <button type="button" id="btn-add-fk-row"
              class="px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-bold text-xs border border-indigo-200 transition cursor-pointer">
              + Add Foreign Key
            </button>
          </div>
          <div id="ct-fk-container" class="space-y-3">
            <p class="text-xs text-slate-400 italic p-3 bg-slate-50 rounded-xl border border-slate-200/60" id="no-fk-msg">
              No foreign key constraints defined yet. Click "+ Add Foreign Key" to link with existing tables.
            </p>
          </div>
        </div>

        <!-- Custom Indexes Builder -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-4">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-800 flex items-center gap-2">
              <i class="fa-solid fa-key text-amber-500"></i> Custom Table Indexes
            </h3>
            <button type="button" id="btn-add-idx-row"
              class="px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 font-bold text-xs border border-amber-200 transition cursor-pointer">
              + Add Custom Index
            </button>
          </div>
          <div id="ct-idx-container" class="space-y-3">
            <p class="text-xs text-slate-400 italic p-3 bg-slate-50 rounded-xl border border-slate-200/60"
              id="no-idx-msg">
              No custom indexes defined yet. Click "+ Add Custom Index" to optimize queries.
            </p>
          </div>
        </div>

      </div>

      <!-- Action Footer -->
      <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm flex items-center justify-between">
        <a href="{{ url(config('database-studio.path', 'database-studio')) }}"
          class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition border border-slate-200 no-underline">
          Cancel
        </a>
        <button type="submit" id="btn-submit-create-table"
          class="px-8 py-3 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-extrabold text-xs transition shadow-lg shadow-brand-600/30 cursor-pointer">
          <i class="fa-solid fa-check text-sm mr-1.5"></i> Execute & Create Table
        </button>
      </div>

    </form>

  </div>
@endsection

@section('scripts')
  <script>
    $(document.ready ? $(document) : $(window)).ready(function () {
      const webBasePath = "{{ url(config('database-studio.path', 'database-studio')) }}";
      const apiBasePath = "{{ url(config('database-studio.api_prefix', 'api/v1/database-manager')) }}";
      let _existingTables = [];

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      const TYPE_LENGTH_RULES = {
        JSON: { mode: 'none' },
        TEXT: { mode: 'none' },
        TINYTEXT: { mode: 'none' },
        MEDIUMTEXT: { mode: 'none' },
        LONGTEXT: { mode: 'none' },
        BOOLEAN: { mode: 'none' },
        DATE: { mode: 'none' },
        DATETIME: { mode: 'none' },
        TIMESTAMP: { mode: 'none' },
        TIME: { mode: 'none' },
        YEAR: { mode: 'none' },
        BLOB: { mode: 'none' },
        TINYBLOB: { mode: 'none' },
        MEDIUMBLOB: { mode: 'none' },
        LONGBLOB: { mode: 'none' },
        VARCHAR: { mode: 'length', label: 'Length', placeholder: '255', default: '255' },
        CHAR: { mode: 'length', label: 'Length', placeholder: '36', default: '36' },
        BINARY: { mode: 'length', label: 'Length', placeholder: '16', default: '' },
        VARBINARY: { mode: 'length', label: 'Length', placeholder: '255', default: '' },
        INT: { mode: 'width', label: 'Display Width', placeholder: '11', default: '' },
        INTEGER: { mode: 'width', label: 'Display Width', placeholder: '11', default: '' },
        BIGINT: { mode: 'width', label: 'Display Width', placeholder: '20', default: '' },
        TINYINT: { mode: 'width', label: 'Display Width', placeholder: '4', default: '' },
        SMALLINT: { mode: 'width', label: 'Display Width', placeholder: '6', default: '' },
        MEDIUMINT: { mode: 'width', label: 'Display Width', placeholder: '9', default: '' },
        FLOAT: { mode: 'width', label: 'Precision', placeholder: '', default: '' },
        DOUBLE: { mode: 'width', label: 'Precision', placeholder: '', default: '' },
        DECIMAL: { mode: 'precision', label: 'Precision, Scale', placeholder: '10,2', default: '10,2' },
        ENUM: { mode: 'values', label: 'Values', placeholder: "'a','b','c'", default: '' },
        SET: { mode: 'values', label: 'Values', placeholder: "'x','y','z'", default: '' },
      };

      function applyLengthRules($select) {
        const type = ($select.val() || '').toUpperCase();
        const rule = TYPE_LENGTH_RULES[type] || { mode: 'length', label: 'Length', placeholder: '', default: '' };
        const $tr = $select.closest('tr');
        const $input = $tr.find('.col-length-input');
        const $na = $tr.find('.col-length-na');

        if (rule.mode === 'none') {
          $input.hide().prop('disabled', true).val('');
          $na.removeClass('hidden');
        } else {
          $na.addClass('hidden');
          $input.show().prop('disabled', false).attr('placeholder', rule.placeholder);
          if ($input.val() === '' && rule.default) {
            $input.val(rule.default);
          }
        }
      }

      $(document).on('change', '.col-type-select', function () {
        applyLengthRules($(this));
      });

      fetchExistingTables();

      addBuilderColumnRow('id', 'BIGINT', '20', false, '', true, true);
      addBuilderColumnRow('name', 'VARCHAR', '255', false, '', false, false);
      addBuilderColumnRow('created_at', 'TIMESTAMP', '', true, 'CURRENT_TIMESTAMP', false, false);

      $('#btn-add-column-row').on('click', function () {
        addBuilderColumnRow();
      });

      $('#btn-add-fk-row').on('click', function () {
        addBuilderFkRow();
      });

      $('#btn-add-idx-row').on('click', function () {
        addBuilderIdxRow();
      });

      $(document).on('click', '.btn-remove-col-row', function () {
        $(this).closest('tr').remove();
        updateColumnsBadge();
      });

      $(document).on('click', '.btn-remove-fk-row', function () {
        $(this).closest('.fk-card-item').remove();
        if ($('#ct-fk-container .fk-card-item').length === 0) {
          $('#ct-fk-container').html('<p class="text-xs text-slate-400 italic p-3 bg-slate-50 rounded-xl border border-slate-200/60" id="no-fk-msg">No foreign key constraints defined yet.</p>');
        }
      });

      $(document).on('click', '.btn-remove-idx-row', function () {
        $(this).closest('.idx-card-item').remove();
        if ($('#ct-idx-container .idx-card-item').length === 0) {
          $('#ct-idx-container').html('<p class="text-xs text-slate-400 italic p-3 bg-slate-50 rounded-xl border border-slate-200/60" id="no-idx-msg">No custom indexes defined yet.</p>');
        }
      });

      $('#form-create-table-page').on('submit', function (e) {
        e.preventDefault();
        executeTableCreation();
      });

      function fetchExistingTables() {
        $.ajax({
          url: apiBasePath,
          type: 'GET',
          success: function (res) {
            if (res.success) {
              _existingTables = $.map(res.data.tables || [], function (t) { return t.name; });
            }
          }
        });
      }

      function updateColumnsBadge() {
        const count = $('#ct-columns-tbody tr').length;
        $('#columns-count-badge').text(count);
      }

      function addBuilderColumnRow(name = '', type = 'VARCHAR', length = '255', nullable = false, defVal = '', autoInc = false, primary = false) {
        const rowId = 'col-row-' + Date.now() + '-' + Math.floor(Math.random() * 1000);

        const html = `
            <tr id="${rowId}" class="hover:bg-slate-50 transition">
              <td class="p-3">
                <input type="text" name="col_name" value="${name}" required pattern="[a-zA-Z0-9_]+"
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-mono font-bold text-slate-800 focus:outline-none focus:border-brand-500 focus:bg-white" placeholder="column_name">
              </td>
              <td class="p-3">
                <select name="col_type" class="col-type-select w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-mono font-semibold text-slate-800 focus:bg-white">
                  <option value="INT" ${type === 'INT' ? 'selected' : ''}>INT (Integer)</option>
                  <option value="BIGINT" ${type === 'BIGINT' ? 'selected' : ''}>BIGINT (64-bit Int)</option>
                  <option value="TINYINT" ${type === 'TINYINT' ? 'selected' : ''}>TINYINT</option>
                  <option value="SMALLINT" ${type === 'SMALLINT' ? 'selected' : ''}>SMALLINT</option>
                  <option value="VARCHAR" ${type === 'VARCHAR' ? 'selected' : ''}>VARCHAR (String)</option>
                  <option value="CHAR" ${type === 'CHAR' ? 'selected' : ''}>CHAR (Fixed String)</option>
                  <option value="TEXT" ${type === 'TEXT' ? 'selected' : ''}>TEXT</option>
                  <option value="TINYTEXT" ${type === 'TINYTEXT' ? 'selected' : ''}>TINYTEXT</option>
                  <option value="MEDIUMTEXT" ${type === 'MEDIUMTEXT' ? 'selected' : ''}>MEDIUMTEXT</option>
                  <option value="LONGTEXT" ${type === 'LONGTEXT' ? 'selected' : ''}>LONGTEXT</option>
                  <option value="DECIMAL" ${type === 'DECIMAL' ? 'selected' : ''}>DECIMAL (Fixed Precision)</option>
                  <option value="FLOAT" ${type === 'FLOAT' ? 'selected' : ''}>FLOAT</option>
                  <option value="DOUBLE" ${type === 'DOUBLE' ? 'selected' : ''}>DOUBLE</option>
                  <option value="BOOLEAN" ${type === 'BOOLEAN' ? 'selected' : ''}>BOOLEAN</option>
                  <option value="DATE" ${type === 'DATE' ? 'selected' : ''}>DATE</option>
                  <option value="DATETIME" ${type === 'DATETIME' ? 'selected' : ''}>DATETIME</option>
                  <option value="TIMESTAMP" ${type === 'TIMESTAMP' ? 'selected' : ''}>TIMESTAMP</option>
                  <option value="TIME" ${type === 'TIME' ? 'selected' : ''}>TIME</option>
                  <option value="YEAR" ${type === 'YEAR' ? 'selected' : ''}>YEAR</option>
                  <option value="JSON" ${type === 'JSON' ? 'selected' : ''}>JSON</option>
                  <option value="BLOB" ${type === 'BLOB' ? 'selected' : ''}>BLOB</option>
                  <option value="LONGBLOB" ${type === 'LONGBLOB' ? 'selected' : ''}>LONGBLOB</option>
                  <option value="ENUM" ${type === 'ENUM' ? 'selected' : ''}>ENUM</option>
                  <option value="SET" ${type === 'SET' ? 'selected' : ''}>SET</option>
                </select>
              </td>
              <td class="p-3 col-length-cell">
                <input type="text" name="col_length" value="${length}"
                  class="col-length-input w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-mono text-slate-800 focus:outline-none focus:bg-white" placeholder="e.g. 255">
                <span class="col-length-na hidden text-[10px] text-slate-300 font-semibold italic">— N/A</span>
              </td>
              <td class="p-3 text-center">
                <input type="checkbox" name="col_nullable" ${nullable ? 'checked' : ''} class="h-4 w-4 rounded border-slate-300 text-brand-600 cursor-pointer">
              </td>
              <td class="p-3">
                <input type="text" name="col_default" value="${defVal}"
                  class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs font-mono text-slate-800 focus:outline-none focus:bg-white" placeholder="DEFAULT">
              </td>
              <td class="p-3 text-center">
                <input type="checkbox" name="col_auto_inc" ${autoInc ? 'checked' : ''} class="h-4 w-4 rounded border-slate-300 text-brand-600 cursor-pointer">
              </td>
              <td class="p-3 text-center">
                <input type="checkbox" name="col_primary" ${primary ? 'checked' : ''} class="h-4 w-4 rounded border-slate-300 text-brand-600 cursor-pointer">
              </td>
              <td class="p-3 text-center">
                <button type="button" class="btn-remove-col-row h-8 w-8 rounded-lg text-rose-500 hover:bg-rose-50 transition cursor-pointer">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
              </td>
            </tr>
          `;

        $('#ct-columns-tbody').append(html);
        updateColumnsBadge();

        const $newSelect = $('#' + rowId).find('.col-type-select');
        applyLengthRules($newSelect);
      }

      function addBuilderFkRow() {
        $('#no-fk-msg').remove();

        let tableOpts = '';
        $.each(_existingTables, function (i, t) { tableOpts += `<option value="${t}">${t}</option>`; });

        const html = `
            <div class="fk-card-item p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3 relative shadow-xs">
              <button type="button" class="btn-remove-fk-row absolute right-3 top-3 text-rose-500 hover:text-rose-700 cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
              </button>
              <div class="grid grid-cols-3 gap-3">
                <div>
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">Local Column</label>
                  <input type="text" name="fk_column" required class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 font-mono text-xs font-bold" placeholder="user_id">
                </div>
                <div>
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">Foreign Table</label>
                  <select name="fk_foreign_table" required class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 font-mono text-xs">
                    ${tableOpts}
                  </select>
                </div>
                <div>
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">Foreign Column</label>
                  <input type="text" name="fk_foreign_column" required value="id" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 font-mono text-xs">
                </div>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">On Delete Action</label>
                  <select name="fk_on_delete" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 font-mono text-xs">
                    <option value="CASCADE" selected>CASCADE (Cascade Delete)</option>
                    <option value="SET NULL">SET NULL</option>
                    <option value="RESTRICT">RESTRICT</option>
                  </select>
                </div>
                <div>
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">On Update Action</label>
                  <select name="fk_on_update" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 font-mono text-xs">
                    <option value="CASCADE" selected>CASCADE</option>
                    <option value="SET NULL">SET NULL</option>
                    <option value="RESTRICT">RESTRICT</option>
                  </select>
                </div>
              </div>
            </div>
          `;

        $('#ct-fk-container').append(html);
      }

      function addBuilderIdxRow() {
        $('#no-idx-msg').remove();

        const html = `
            <div class="idx-card-item p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3 relative shadow-xs">
              <button type="button" class="btn-remove-idx-row absolute right-3 top-3 text-rose-500 hover:text-rose-700 cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
              </button>
              <div class="grid grid-cols-3 gap-3">
                <div>
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">Index Name</label>
                  <input type="text" name="idx_name" required class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 font-mono text-xs font-bold" placeholder="idx_column">
                </div>
                <div>
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">Index Type</label>
                  <select name="idx_type" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 font-mono text-xs">
                    <option value="INDEX" selected>INDEX (Key)</option>
                    <option value="UNIQUE">UNIQUE</option>
                    <option value="FULLTEXT">FULLTEXT</option>
                  </select>
                </div>
                <div>
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">Columns (comma sep)</label>
                  <input type="text" name="idx_columns" required class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 font-mono text-xs" placeholder="name, email">
                </div>
              </div>
            </div>
          `;

        $('#ct-idx-container').append(html);
      }

      function executeTableCreation() {
        const tableName = $('#ct-table-name').val().trim();
        const engine = $('#ct-engine').val();
        const collation = $('#ct-collation').val();

        const columns = [];
        $('#ct-columns-tbody tr').each(function () {
          const $tr = $(this);
          columns.push({
            name: $tr.find('input[name="col_name"]').val().trim(),
            type: $tr.find('select[name="col_type"]').val(),
            length: $tr.find('input[name="col_length"]').val().trim(),
            nullable: $tr.find('input[name="col_nullable"]').is(':checked'),
            default: $tr.find('input[name="col_default"]').val().trim(),
            auto_increment: $tr.find('input[name="col_auto_inc"]').is(':checked'),
            primary: $tr.find('input[name="col_primary"]').is(':checked'),
          });
        });

        if (columns.length === 0) {
          window.showToast('warning', 'Please add at least one column definition.');
          return;
        }

        const foreign_keys = [];
        $('#ct-fk-container .fk-card-item').each(function () {
          const $div = $(this);
          foreign_keys.push({
            column: $div.find('input[name="fk_column"]').val().trim(),
            foreign_table: $div.find('select[name="fk_foreign_table"]').val(),
            foreign_column: $div.find('input[name="fk_foreign_column"]').val().trim(),
            on_delete: $div.find('select[name="fk_on_delete"]').val(),
            on_update: $div.find('select[name="fk_on_update"]').val(),
          });
        });

        const indexes = [];
        $('#ct-idx-container .idx-card-item').each(function () {
          const $div = $(this);
          const colsRaw = $div.find('input[name="idx_columns"]').val();
          const cols = $.map(colsRaw.split(','), function (s) { return $.trim(s); });
          indexes.push({
            name: $div.find('input[name="idx_name"]').val().trim(),
            type: $div.find('select[name="idx_type"]').val(),
            columns: $.grep(cols, function (n) { return n; }),
          });
        });

        const payload = {
          table_name: tableName,
          engine: engine,
          collation: collation,
          columns: columns,
          foreign_keys: foreign_keys,
          indexes: indexes,
        };

        $('#btn-submit-create-table').prop('disabled', true).html('<i class="fa-solid fa-circle-notch fa-spin mr-1.5"></i> Creating Table…');

        $.ajax({
          url: `${apiBasePath}/create`,
          type: 'POST',
          contentType: 'application/json',
          data: JSON.stringify(payload),
          success: function (res) {
            if (res.success) {
              window.showToast('success', res.message || 'Table created successfully!');
              setTimeout(function () {
                window.location.href = `${webBasePath}/manage/${tableName}`;
              }, 1000);
            }
          },
          error: function (xhr) {
            $('#btn-submit-create-table').prop('disabled', false).html('<i class="fa-solid fa-check text-sm mr-1.5"></i> Execute & Create Table');
            window.handleAjaxError(xhr, 'Failed to create table. Check syntax and column definitions.');
          }
        });
      }
    });
  </script>
@endsection
