<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Security Gateway — Database Studio</title>

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
</head>

<body
  class="bg-slate-900 font-sans antialiased text-slate-800 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

  <!-- Background Ambient Glow -->
  <div class="absolute -top-32 -left-32 w-96 h-96 bg-brand-600/20 rounded-full blur-3xl pointer-events-none"></div>
  <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

  <div class="max-w-md w-full relative z-10">

    <!-- Logo & Security Header -->
    <div class="text-center mb-8 space-y-3">
      <div
        class="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-br from-amber-400 to-brand-600 shadow-2xl shadow-amber-500/20 mb-2 border border-white/20">
        <i class="fa-solid fa-lock text-white text-2xl"></i>
      </div>
      <h1 class="text-2xl font-black text-white tracking-tight">LaraforgeX Database Studio Gateway</h1>
      <p class="text-xs text-slate-400 font-medium">Please authenticate with your security credentials to access the
        database manager.</p>
    </div>

    <!-- Login Card -->
    <div class="bg-white border border-slate-800/80 rounded-3xl p-8 shadow-2xl space-y-6">

      @if(session('error'))
        <div
          class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-700 text-xs font-semibold flex items-center gap-3">
          <i class="fa-solid fa-circle-exclamation text-rose-600 text-base shrink-0"></i>
          <span>{{ session('error') }}</span>
        </div>
      @endif

      <form action="{{ url(config('database-studio.path', 'database-studio') . '/login') }}" method="POST"
        class="space-y-5">
        @csrf

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Username / Email *</label>
          <div class="relative">
            <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="username" required value="{{ old('username') }}"
              class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-brand-500 focus:bg-white transition"
              placeholder="admin@admin.com">
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Security Password
            *</label>
          <div class="relative">
            <i class="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="password" id="input-password" name="password" required
              class="w-full pl-11 pr-11 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-brand-500 focus:bg-white transition"
              placeholder="••••••••">
            <button type="button" id="btn-toggle-password"
              class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition cursor-pointer">
              <i class="fa-solid fa-eye text-sm" id="icon-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit"
          class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-700 hover:from-brand-500 hover:to-indigo-600 text-white font-extrabold text-xs transition shadow-lg shadow-brand-600/30 flex items-center justify-center gap-2 cursor-pointer border-0">
          <i class="fa-solid fa-right-to-bracket text-sm"></i> Unlock Database Studio
        </button>
      </form>

      <!-- Credentials Helper Box -->
      <div class="pt-4 border-t border-slate-100 text-center">
        <p class="text-[11px] text-slate-400">
          Default credentials are set in your <code
            class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-700 font-mono text-[10px]">.env</code>:
        </p>
        <div
          class="mt-2 p-2.5 bg-slate-50 rounded-xl border border-slate-200/80 font-mono text-[10px] text-slate-600 text-left space-y-1">
          <div><span class="text-slate-400">DB_STUDIO_AUTH_USERNAME=</span>admin@admin.com</div>
          <div><span class="text-slate-400">DB_STUDIO_AUTH_PASSWORD=</span>admin123</div>
        </div>
      </div>

    </div>

    <!-- Footer -->
    <p class="text-center text-xs text-slate-500 mt-6 font-medium">
      Laraforge Database Studio Security Gateway v1.0
    </p>

  </div>

  <script>
    $(document).ready(function () {
      $('#btn-toggle-password').on('click', function () {
        const $input = $('#input-password');
        const $icon = $('#icon-eye');
        if ($input.attr('type') === 'password') {
          $input.attr('type', 'text');
          $icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
          $input.attr('type', 'password');
          $icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
      });
    });
  </script>
</body>

</html>