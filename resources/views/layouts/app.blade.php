<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SISPRAK - Sistem Informasi Praktikum' }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace']
                    }
                }
            }
        }
    </script>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased">

@auth
@php $currentUser = auth()->user(); @endphp
<!-- Navbar Utama -->
<header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Brand & Navigasi -->
            <div class="flex items-center space-x-6">
                <a href="{{ route($currentUser->getDashboardRoute()) }}" class="flex items-center space-x-3 group">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold shadow-xs group-hover:bg-indigo-700 transition">
                        <i class="fa-solid fa-graduation-cap text-base"></i>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="font-bold text-lg text-slate-900 tracking-tight">SISPRAK</span>
                        </div>
                        <span class="text-[11px] text-slate-500 font-medium">Sistem Informasi Praktikum</span>
                    </div>
                </a>

                <nav class="hidden md:flex items-center space-x-2 pl-4 border-l border-slate-200 text-sm font-semibold">
                    <a href="{{ route($currentUser->getDashboardRoute()) }}" 
                       class="px-3 py-1.5 rounded-lg text-slate-700 hover:text-indigo-600 hover:bg-slate-100 transition flex items-center space-x-2">
                        <i class="fa-solid fa-table-columns text-slate-400"></i>
                        <span>Dashboard</span>
                    </a>
                </nav>
            </div>

            <!-- User Menu & Role -->
            <div class="flex items-center space-x-3">
                <!-- Role Switcher Button (Untuk Akun Non-Dosen) -->
                @if ($currentUser->canSwitchRole())
                    @if ($currentUser->getActiveRole() === 'mahasiswa')
                        <a href="{{ route('role.switch.direct', 'asisten_dosen') }}" 
                           class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 shadow-2xs transition group"
                           title="Klik untuk berganti ke Mode Asisten Dosen (Asprak)">
                            <i class="fa-solid fa-repeat text-amber-600 group-hover:rotate-180 transition-transform duration-300"></i>
                            <span class="hidden sm:inline">Mode Asprak</span>
                        </a>
                    @else
                        <a href="{{ route('role.switch.direct', 'mahasiswa') }}" 
                           class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-2xs transition group"
                           title="Klik untuk berganti ke Mode Mahasiswa">
                            <i class="fa-solid fa-repeat text-emerald-600 group-hover:rotate-180 transition-transform duration-300"></i>
                            <span class="hidden sm:inline">Mode Mahasiswa</span>
                        </a>
                    @endif
                @endif

                <div class="flex items-center space-x-3 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr($currentUser->nama_lengkap, 0, 1)) }}
                    </div>
                    <div class="text-left hidden sm:block">
                        <p class="text-xs font-semibold text-slate-900 leading-tight">{{ $currentUser->nama_lengkap }}</p>
                        <p class="text-[10px] text-slate-500 font-mono">{{ $currentUser->username }}</p>
                    </div>

                    <!-- Role Badge -->
                    @if ($currentUser->getActiveRole() === 'dosen')
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            Dosen
                        </span>
                    @elseif ($currentUser->getActiveRole() === 'asisten_dosen')
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 flex items-center space-x-1">
                            <i class="fa-solid fa-chalkboard-user text-[9px]"></i>
                            <span>Asprak</span>
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center space-x-1">
                            <i class="fa-solid fa-graduation-cap text-[9px]"></i>
                            <span>Mahasiswa</span>
                        </span>
                    @endif
                </div>

                <!-- Tombol Logout -->
                <form action="{{ route('logout') }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari akun?');">
                    @csrf
                    <button type="submit" 
                            class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition border border-transparent hover:border-rose-200" 
                            title="Keluar Akun">
                        <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
@endauth

<!-- Flash Messages -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
    @if ($successMsg = session('success'))
        <div class="p-3.5 mb-4 text-sm rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-2.5">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span class="font-medium">{{ $successMsg }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 p-1"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if ($errorMsg = session('error'))
        <div class="p-3.5 mb-4 text-sm rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-2.5">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
                <span class="font-medium">{{ $errorMsg }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 p-1"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-3.5 mb-4 text-sm rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-xs">
            <div class="flex items-center space-x-2 mb-1.5 font-semibold">
                <i class="fa-solid fa-circle-xmark text-rose-600"></i>
                <span>Periksa kembali data yang dimasukkan:</span>
            </div>
            <ul class="list-disc pl-6 space-y-0.5 text-xs text-rose-700">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<!-- Main Content Area -->
<main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-white border-t border-slate-200 py-6 mt-auto text-center text-xs text-slate-500">
    <div class="max-w-7xl mx-auto px-4">
        <p>&copy; {{ date('Y') }} SISPRAK &bull; Sistem Informasi Praktikum Akademik</p>
    </div>
</footer>

<!-- Toast Container & Global Script Helpers -->
<div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col space-y-2 pointer-events-none"></div>

<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `px-4 py-2.5 rounded-xl shadow-lg text-xs font-semibold text-white flex items-center space-x-2 pointer-events-auto transition duration-200 transform translate-y-2 ${
            type === 'success' ? 'bg-slate-900' : 'bg-rose-600'
        }`;
        toast.innerHTML = `<i class="fa-solid ${type === 'success' ? 'fa-check' : 'fa-exclamation'}"></i><span>${message}</span>`;
        container.appendChild(toast);

        setTimeout(() => toast.classList.remove('translate-y-2'), 10);
        setTimeout(() => {
            toast.classList.add('opacity-0');
            setTimeout(() => toast.remove(), 200);
        }, 2500);
    }

    function copyCode(text, buttonElement) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Kode gabung disalin: ' + text);
            if (buttonElement) {
                const orig = buttonElement.innerHTML;
                buttonElement.innerHTML = '<i class="fa-solid fa-check text-emerald-500"></i>';
                setTimeout(() => buttonElement.innerHTML = orig, 1500);
            }
        }).catch(() => {
            showToast('Gagal menyalin kode', 'error');
        });
    }
</script>
</body>
</html>
