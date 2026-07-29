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
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800 min-h-screen">
    
    <!-- Top Navigation Header (Full Width) -->
    <header class="bg-slate-900 text-white border-b border-slate-800 sticky top-0 z-50">
      <div class="w-full px-6 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <a href="{{ url(config('database-studio.path', 'database-studio')) }}" class="flex items-center gap-2.5 text-white no-underline">
            <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-amber-400 to-brand-600 flex items-center justify-center shadow-lg shadow-amber-500/20">
              <i class="fa-solid fa-database text-white text-base"></i>
            </div>
            <div>
              <span class="font-black text-lg tracking-tight bg-gradient-to-r from-white via-slate-100 to-slate-300 bg-clip-text text-transparent">Database Studio</span>
              <span class="text-[10px] uppercase tracking-widest font-extrabold px-1.5 py-0.5 rounded bg-amber-400/20 text-amber-300 border border-amber-400/30 ml-1.5">v1.0</span>
            </div>
          </a>
        </div>

        <div>
          @if(config('database-studio.auth.enabled', true))
            <a href="{{ url(config('database-studio.path', 'database-studio') . '/logout') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold text-rose-300 bg-rose-950/40 hover:bg-rose-900/60 border border-rose-800/50 transition flex items-center gap-2 no-underline" title="Sign out of Database Studio session">
              <i class="fa-solid fa-right-from-bracket text-rose-400 text-xs"></i> Logout
            </a>
          @endif
        </div>
      </div>
    </header>

    <!-- Main Container (Full Width Full Screen) -->
    <main class="w-full px-6 py-6">
        @yield('content')
    </main>

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
    </script>

    @yield('scripts')
</body>
</html>
