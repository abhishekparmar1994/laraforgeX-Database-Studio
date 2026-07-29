<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    
    <!-- Top Navigation Header -->
    <header class="bg-slate-900 text-white border-b border-slate-800 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
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

        <nav class="flex items-center gap-2">
          <a href="{{ url(config('database-studio.path', 'database-studio')) }}" 
             class="px-3.5 py-2 rounded-lg text-xs font-bold text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center gap-2">
            <i class="fa-solid fa-table-cells text-slate-400"></i> Tables Explorer
          </a>
          <a href="{{ url(config('database-studio.path', 'database-studio') . '/create') }}" 
             class="px-3.5 py-2 rounded-lg text-xs font-bold text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center gap-2">
            <i class="fa-solid fa-plus-circle text-emerald-400"></i> New Table
          </a>
          <a href="{{ url(config('database-studio.path', 'database-studio') . '/console') }}" 
             class="px-3.5 py-2 rounded-lg text-xs font-bold text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center gap-2">
            <i class="fa-solid fa-terminal text-amber-400"></i> SQL Console
          </a>
        </nav>
      </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 mt-12 py-6 text-center text-xs text-slate-400">
      <div class="max-w-7xl mx-auto px-4">
        <p>Powered by <strong>Laraforge Database Studio</strong> — Navicat & phpMyAdmin Grade GUI Package for Laravel.</p>
      </div>
    </footer>

    @yield('scripts')
</body>
</html>
