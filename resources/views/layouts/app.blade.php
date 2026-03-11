<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Sin Kost An</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Manrope', sans-serif; }
        
        .transition-all-300 { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        
        .no-scrollbar::-webkit-scrollbar { width: 4px; }
        .no-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

        @media (min-width: 768px) {
            .sidebar-open #sidebar { transform: translateX(0); }
            .sidebar-open #mainContent { margin-left: 260px; }
            
            .sidebar-closed #sidebar { transform: translateX(-260px); }
            .sidebar-closed #mainContent { margin-left: 0; }
        }

        @media (max-width: 767px) {
            #sidebar { transform: translateX(-100%); z-index: 1050; }
            .sidebar-open #sidebar { transform: translateX(0); }
            .sidebar-open #menuOverlay { opacity: 1; pointer-events: auto; }
        }
    </style>
</head>
<body class="bg-[#f5f7fb] text-gray-800 overflow-x-hidden sidebar-open">

    <div id="menuOverlay" class="fixed inset-0 bg-black/40 opacity-0 pointer-events-none z-[1040] transition-opacity duration-300"></div>

    <nav class="fixed top-0 w-full bg-white px-6 py-4 flex items-center justify-between shadow-sm z-[1060]">
        <div class="flex items-center gap-4">
            <button id="sidebarToggle" class="group flex flex-col gap-1.5 cursor-pointer p-2 hover:bg-gray-100 rounded-lg transition-all">
                <span class="w-6 h-0.5 bg-indigo-600 rounded group-hover:w-4 transition-all"></span>
                <span class="w-6 h-0.5 bg-indigo-600 rounded"></span>
                <span class="w-6 h-0.5 bg-indigo-600 rounded group-hover:w-4 transition-all"></span>
            </button>
            <div class="flex items-center gap-2 font-extrabold text-lg text-indigo-600">
                <span class="bg-indigo-600 text-white w-9 h-9 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200">SKA</span>
                <span class="hidden md:block text-gray-800 tracking-tight">Sin Kost An</span>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="hidden md:block text-right mr-2">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Administrator</p>
                <p class="text-sm font-bold text-gray-700">{{ auth()->user()->name ?? 'Admin Owner' }}</p>
            </div>
            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 border-2 border-white shadow-sm rounded-full flex items-center justify-center font-bold">
                {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
            </div>
        </div>
    </nav>

    <div class="flex pt-20">
        <aside id="sidebar" class="fixed left-0 top-0 bottom-0 w-[260px] bg-white border-r border-gray-100 pt-24 pb-6 px-6 z-[1050] transition-all-300 overflow-y-auto no-scrollbar shadow-xl shadow-gray-200/50 md:shadow-none">
            <div class="mb-10 px-4">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Menu Utama</p>
            </div>
            
            <nav class="space-y-1.5">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'text-gray-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span>Dashboard</span>
                </a>

                @php 
                    $isManagement = request()->routeIs('admin.branches.*') || request()->routeIs('admin.tenants.*') || request()->routeIs('admin.invoices.*'); 
                @endphp
                <div class="py-2">
                    <button class="dropdown-toggle w-full flex items-center justify-between px-4 py-3.5 rounded-xl font-bold transition-all {{ $isManagement ? 'bg-indigo-50 text-indigo-600' : 'text-gray-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <span>Manajemen</span>
                        </div>
                        <svg class="arrow w-4 h-4 transition-transform duration-200 {{ $isManagement ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="dropdown-menu {{ $isManagement ? '' : 'hidden' }} pl-11 mt-1 space-y-1">
                        <a href="{{ route('admin.branches.index') }}" class="block py-2 text-sm font-semibold {{ request()->routeIs('admin.branches.*') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-600' }} transition-colors">Cabang & Kamar</a>
                        <a href="{{ route('admin.tenants.index') }}" class="block py-2 text-sm font-semibold {{ request()->routeIs('admin.tenants.*') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-600' }} transition-colors">Penyewa</a>
                        <a href="{{ route('admin.invoices.index') }}" class="block py-2 text-sm font-semibold {{ request()->routeIs('admin.invoices.*') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-600' }} transition-colors">Tagihan</a>
                    </div>
                </div>

                <a href="{{ route('admin.maintenance.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all {{ request()->routeIs('admin.maintenance.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'text-gray-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Maintenance</span>
                </a>

                <a href="{{ route('admin.notifications.index') }}" class="flex items-center justify-between px-4 py-3.5 rounded-xl font-bold transition-all {{ request()->routeIs('admin.notifications.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'text-gray-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span>Notifikasi</span>
                    </div>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="bg-rose-500 text-white text-[10px] px-2 py-0.5 rounded-full shadow-lg shadow-rose-200">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all {{ request()->routeIs('admin.reports.index') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'text-gray-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Laporan</span>
                </a>
            </nav>

            <div class="mt-10 pt-10 border-t border-gray-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold text-red-400 hover:bg-red-50 hover:text-red-600 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span>Keluar Panel</span>
                    </button>
                </form>
            </div>
        </aside>

        <main id="mainContent" class="flex-1 min-h-screen transition-all-300">
            <div class="p-4 md:p-8 lg:p-10">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        const sidebarToggle = document.getElementById("sidebarToggle");
        const body = document.body;
        const menuOverlay = document.getElementById("menuOverlay");

        sidebarToggle.addEventListener("click", () => {
            body.classList.toggle("sidebar-open");
            body.classList.toggle("sidebar-closed");
        });

        menuOverlay.addEventListener("click", () => {
            body.classList.remove("sidebar-open");
            body.classList.add("sidebar-closed");
        });

        document.querySelectorAll('.dropdown-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const menu = btn.nextElementSibling;
                const arrow = btn.querySelector('.arrow');
                
                menu.classList.toggle('hidden');
                arrow.classList.toggle('rotate-180');
            });
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth < 768) {
                body.classList.remove("sidebar-open");
                body.classList.add("sidebar-closed");
            } else {
                body.classList.add("sidebar-open");
                body.classList.remove("sidebar-closed");
            }
        });
    </script>
</body>
</html>