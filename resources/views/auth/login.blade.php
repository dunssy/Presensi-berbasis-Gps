<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — PT Technodrives Presensi GPS & Logbook</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 900: '#1e3a8a'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="flex justify-center">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-600 text-white font-extrabold text-2xl shadow-lg shadow-primary-600/30">
                TD
            </div>
        </div>
        <h2 class="mt-6 text-center text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
            PT Technodrives
        </h2>
        <p class="mt-2 text-center text-sm font-medium text-slate-600">
            Sistem Presensi Berbasis GPS & Logbook Kegiatan
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white py-8 px-6 shadow-xl shadow-slate-200/50 rounded-2xl sm:px-10 border border-slate-100">
            
            {{-- Flash messages --}}
            @if(session('success'))
                <div class="mb-5 rounded-xl bg-emerald-50 p-4 border border-emerald-200 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 rounded-xl bg-rose-50 p-4 border border-rose-200 text-sm text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            <form class="space-y-5" action="{{ route('login.post') }}" method="POST">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700">
                        Alamat Email
                    </label>
                    <div class="mt-1.5">
                        <input id="email" name="email" type="email" autocomplete="email" required
                            value="{{ old('email') }}"
                            placeholder="nama@technodrives.com"
                            class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 sm:text-sm transition-all @error('email') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700">
                        Kata Sandi
                    </label>
                    <div class="mt-1.5">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            placeholder="••••••••"
                            class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 sm:text-sm transition-all @error('password') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror">
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <label for="remember" class="ml-2 block text-sm text-slate-600">
                            Ingat saya di perangkat ini
                        </label>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="flex w-full justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-colors">
                        Masuk ke Akun
                    </button>
                </div>
            </form>

            {{-- Quick Demo Credentials Helper --}}
            <div class="mt-6 pt-6 border-t border-slate-100">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Akun Pengujian Demo:</p>
                <div class="grid grid-cols-3 gap-2 text-xs">
                    <button type="button" onclick="fillCreds('admin@technodrives.com', 'password')"
                        class="p-2 bg-slate-50 hover:bg-slate-100 rounded-lg border border-slate-200 text-slate-700 font-medium text-center">
                        Admin
                    </button>
                    <button type="button" onclick="fillCreds('manager@technodrives.com', 'password')"
                        class="p-2 bg-slate-50 hover:bg-slate-100 rounded-lg border border-slate-200 text-slate-700 font-medium text-center">
                        Manager
                    </button>
                    <button type="button" onclick="fillCreds('karyawan@technodrives.com', 'password')"
                        class="p-2 bg-slate-50 hover:bg-slate-100 rounded-lg border border-slate-200 text-slate-700 font-medium text-center">
                        Karyawan
                    </button>
                </div>
                <p class="text-[11px] text-slate-400 text-center mt-2">Password default: <span class="font-mono">password</span></p>
            </div>
        </div>
    </div>

    <script>
        function fillCreds(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
