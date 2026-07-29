<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Database Studio & Table Manager')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              brand: {
                50: '#f0f7ff',
                100: '#e0effe',
                200: '#bae0fd',
                300: '#7cc8fc',
                400: '#36b0fa',
                500: '#0c95eb',
                600: '#0076c9',
                700: '#025ea3',
                800: '#065086',
                900: '#0b436f',
              }
            }
          }
        }
      }
    </script>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- CodeMirror for SQL Console -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/theme/dracula.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/sql/sql.min.js"></script>

    <style>
      [x-cloak] { display: none !important; }
      /* Custom scrollbar for sidebar */
      .sidebar-scroll::-webkit-scrollbar {
        width: 5px;
      }
      .sidebar-scroll::-webkit-scrollbar-track {
        background: transparent;
      }
      .sidebar-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 9999px;
      }
      .sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
      }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800 min-h-screen">
    
    <!-- Top Navigation Header (Full Width & Responsive) -->
    <header class="bg-slate-900 text-white border-b border-slate-800 sticky top-0 z-50">
      <div class="w-full px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
        
        <!-- Left Brand & Mobile Sidebar Toggle -->
        <div class="flex items-center gap-3">
          <!-- Mobile Sidebar Drawer Trigger Button -->
          <button type="button" id="btn-toggle-mobile-sidebar" 
            class="lg:hidden p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition cursor-pointer border border-slate-700">
            <i class="fa-solid fa-bars text-sm"></i>
          </button>

          <a href="{{ url(config('database-studio.path', 'database-studio')) }}" class="flex items-center gap-2.5 text-white no-underline">
            <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-amber-400 to-brand-600 flex items-center justify-center shadow-lg shadow-amber-500/20 shrink-0">
              <i class="fa-solid fa-database text-white text-base"></i>
            </div>
            <div>
              <span class="font-black text-lg tracking-tight bg-gradient-to-r from-white via-slate-100 to-slate-300 bg-clip-text text-transparent">LaraforgeX Database Studio</span>
              <span class="text-[10px] uppercase tracking-widest font-extrabold px-1.5 py-0.5 rounded bg-amber-400/20 text-amber-300 border border-amber-400/30 ml-1.5">v1.0</span>
            </div>
          </a>
        </div>

        <!-- Right Quick Action Links -->
        <div class="flex items-center gap-2 sm:gap-3">
          <a href="{{ url(config('database-studio.path', 'database-studio') . '/create') }}" 
             class="px-3 py-2 sm:px-3.5 sm:py-2 rounded-xl text-xs font-bold text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center gap-1.5 sm:gap-2 no-underline">
            <i class="fa-solid fa-plus-circle text-emerald-400"></i>
            <span class="hidden sm:inline">New Table</span>
          </a>
          <a href="{{ url(config('database-studio.path', 'database-studio') . '/console') }}" 
             class="px-3 py-2 sm:px-3.5 sm:py-2 rounded-xl text-xs font-bold text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center gap-1.5 sm:gap-2 no-underline">
            <i class="fa-solid fa-terminal text-amber-400"></i>
            <span class="hidden sm:inline">SQL Console</span>
          </a>
          @if(config('database-studio.auth.enabled', true))
            <a href="{{ url(config('database-studio.path', 'database-studio') . '/logout') }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold text-rose-300 bg-rose-950/40 hover:bg-rose-900/60 border border-rose-800/50 transition flex items-center gap-1.5 no-underline ml-1" title="Sign out of Database Studio session">
              <i class="fa-solid fa-right-from-bracket text-rose-400 text-xs"></i>
              <span class="hidden sm:inline">Logout</span>
            </a>
          @endif
        </div>

      </div>
    </header>

    <!-- Mobile Slide-Over Sidebar Backdrop & Drawer -->
    <div id="mobile-sidebar-backdrop" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden lg:hidden">
      <div id="mobile-sidebar-panel" class="fixed inset-y-0 left-0 w-80 max-w-[85vw] bg-white shadow-2xl p-5 space-y-4 overflow-y-auto font-sans border-r border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2">
            <i class="fa-solid fa-database text-brand-600 text-sm"></i>
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-800">Database Tables</h3>
          </div>
          <button type="button" id="btn-close-mobile-sidebar" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
            <i class="fa-solid fa-xmark text-base"></i>
          </button>
        </div>

        <div class="space-y-1">
          <a href="{{ url(config('database-studio.path', 'database-studio')) }}" 
             class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-brand-600 transition no-underline">
            <i class="fa-solid fa-table-cells text-slate-400 text-xs w-4"></i> Dashboard Explorer
          </a>
          <a href="{{ url(config('database-studio.path', 'database-studio') . '/create') }}" 
             class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-emerald-600 transition no-underline">
            <i class="fa-solid fa-plus text-emerald-500 text-xs w-4"></i> Create New Table
          </a>
          <a href="{{ url(config('database-studio.path', 'database-studio') . '/console') }}" 
             class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-amber-600 transition no-underline">
            <i class="fa-solid fa-terminal text-amber-500 text-xs w-4"></i> SQL Console
          </a>
        </div>

        <div class="relative pt-1">
          <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          <input type="text" class="mobile-sidebar-table-search w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-brand-500 transition"
            placeholder="Search tables…">
        </div>

        <div class="mobile-sidebar-tables-container sidebar-scroll max-h-[calc(100vh-250px)] overflow-y-auto space-y-1 pr-1 font-mono text-xs">
          <div class="p-4 text-center text-slate-400 font-sans text-xs">
            <i class="fa-solid fa-circle-notch fa-spin text-sm mb-1"></i>
            <p>Loading schema…</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Container: Responsive 2-Column Sidebar + Content Layout -->
    <div class="w-full px-4 sm:px-6 py-6 flex gap-6 items-start">
      
      <!-- Desktop Left Sticky Table Explorer Sidebar -->
      <aside class="hidden lg:block w-64 lg:w-72 shrink-0 sticky top-20 bg-white border border-slate-200/90 rounded-2xl p-4 shadow-sm space-y-4 font-sans">
        
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2">
            <i class="fa-solid fa-database text-brand-600 text-sm"></i>
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-800">Database Tables</h3>
          </div>
          <span class="sb-tables-badge px-2 py-0.5 rounded-full text-[10px] font-black bg-brand-50 text-brand-600 border border-brand-200">0</span>
        </div>

        <!-- Quick Nav Shortcuts -->
        <div class="space-y-1">
          <a href="{{ url(config('database-studio.path', 'database-studio')) }}" 
             class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-brand-600 transition no-underline">
            <i class="fa-solid fa-table-cells text-slate-400 text-xs w-4"></i> Dashboard Explorer
          </a>
          <a href="{{ url(config('database-studio.path', 'database-studio') . '/create') }}" 
             class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-emerald-600 transition no-underline">
            <i class="fa-solid fa-plus text-emerald-500 text-xs w-4"></i> Create New Table
          </a>
          <a href="{{ url(config('database-studio.path', 'database-studio') . '/console') }}" 
             class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-amber-600 transition no-underline">
            <i class="fa-solid fa-terminal text-amber-500 text-xs w-4"></i> SQL Console
          </a>
        </div>

        <!-- Table Search Box -->
        <div class="relative pt-1">
          <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          <input type="text" class="desktop-sidebar-table-search w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:border-brand-500 transition"
            placeholder="Search tables…">
        </div>

        <!-- Scrollable Tables List -->
        <div class="desktop-sidebar-tables-container sidebar-scroll max-h-[calc(100vh-280px)] overflow-y-auto space-y-1 pr-1 font-mono text-xs">
          <div class="p-4 text-center text-slate-400 font-sans text-xs">
            <i class="fa-solid fa-circle-notch fa-spin text-sm mb-1"></i>
            <p>Loading schema…</p>
          </div>
        </div>

      </aside>

      <!-- Right Main Workspace Content Area -->
      <main class="flex-1 min-w-0">
          @yield('content')
      </main>

    </div>

    <!-- Footer (Full Width) -->
    <footer class="border-t border-slate-200 mt-12 py-6 text-center text-xs text-slate-400">
      <div class="w-full px-6">
        <p>Powered by <strong>Laraforge Database Studio</strong> — Navicat & phpMyAdmin Grade GUI Package for Laravel.</p>
      </div>
    </footer>

    <script>
      window.showToast = function(type, message) {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: type,
          title: message,
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        });
      };

      window.handleAjaxError = function(xhr, defaultMsg) {
        let msg = defaultMsg || 'An unexpected error occurred.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        Swal.fire({
          icon: 'error',
          title: 'Action Failed',
          text: msg,
          confirmButtonColor: '#025ea3'
        });
      };

      // Responsive Tables Explorer Sidebar Loader & Filter
      $(document).ready(function() {
        const webBasePath = "{{ url(config('database-studio.path', 'database-studio')) }}";
        const apiBasePath = "{{ url(config('database-studio.api_prefix', 'api/v1/database-manager')) }}";
        const currentPath = window.location.pathname;
        let _sidebarTables = [];

        // Mobile drawer toggles
        $('#btn-toggle-mobile-sidebar').on('click', function() {
          $('#mobile-sidebar-backdrop').removeClass('hidden');
        });

        $('#btn-close-mobile-sidebar, #mobile-sidebar-backdrop').on('click', function(e) {
          if (e.target === this || $(e.target).closest('#btn-close-mobile-sidebar').length) {
            $('#mobile-sidebar-backdrop').addClass('hidden');
          }
        });

        $.ajax({
          url: apiBasePath,
          type: 'GET',
          success: function(res) {
            if (res.success && res.data && res.data.tables) {
              _sidebarTables = res.data.tables;
              $('.sb-tables-badge').text(_sidebarTables.length);
              renderSidebarTables(_sidebarTables);
            }
          },
          error: function() {
            $('.desktop-sidebar-tables-container, .mobile-sidebar-tables-container').html('<div class="p-3 text-center text-slate-400 font-sans text-xs">Failed to load tables list.</div>');
          }
        });

        $('.desktop-sidebar-table-search, .mobile-sidebar-table-search').on('keyup input', function() {
          const q = $(this).val().toLowerCase().trim();
          if (!q) {
            renderSidebarTables(_sidebarTables);
            return;
          }
          const filtered = $.grep(_sidebarTables, function(t) {
            return t.name.toLowerCase().indexOf(q) !== -1;
          });
          renderSidebarTables(filtered);
        });

        function renderSidebarTables(tables) {
          const $desktop = $('.desktop-sidebar-tables-container');
          const $mobile  = $('.mobile-sidebar-tables-container');

          if (tables.length === 0) {
            const emptyHtml = '<div class="p-3 text-center text-slate-400 font-sans text-xs italic">No matching tables.</div>';
            $desktop.html(emptyHtml);
            $mobile.html(emptyHtml);
            return;
          }

          let html = '';
          $.each(tables, function(i, t) {
            const tableUrl = `${webBasePath}/manage/${t.name}`;
            const isActive = currentPath.indexOf(`/manage/${t.name}`) !== -1;
            const activeClasses = isActive 
              ? 'bg-brand-50 text-brand-700 font-bold border-brand-200' 
              : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900 border-transparent';

            html += `
              <a href="${tableUrl}" class="flex items-center justify-between px-3 py-2 rounded-xl border ${activeClasses} transition text-xs group no-underline">
                <span class="truncate flex items-center gap-2">
                  <i class="fa-solid fa-table text-[11px] ${isActive ? 'text-brand-600' : 'text-slate-400 group-hover:text-slate-600'}"></i>
                  ${t.name}
                </span>
                <span class="text-[10px] text-slate-400 font-sans ml-1 shrink-0 font-medium">${(t.rows ?? 0).toLocaleString()}</span>
              </a>
            `;
          });

          $desktop.html(html);
          $mobile.html(html);
        }
      });
    </script>

    @yield('scripts')
</body>
</html>
