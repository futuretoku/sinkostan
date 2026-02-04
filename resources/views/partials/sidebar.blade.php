<aside id="sidebar" class="fixed left-[-260px] top-20 bottom-0 w-[260px] bg-white border-r border-gray-200 p-6 z-[1000] transition-all-300 overflow-y-auto no-scrollbar">
    <h2 class="text-indigo-600 font-extrabold text-lg mb-8 uppercase tracking-wider">Menu Utama</h2>
    <nav class="space-y-2">
        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 rounded-xl font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Dashboard</a>
        
        <div class="group">
            <button class="dropdown-toggle w-full flex items-center justify-between px-4 py-3 rounded-xl bg-indigo-50 text-indigo-600 font-bold">
                <span>Manajemen</span>
                <span class="arrow transition-transform duration-200 text-xs rotate-180">▼</span>
            </button>
            <div class="dropdown-menu pl-4 mt-2 space-y-1">
    {{-- Link Cabang & Kamar --}}
    <a href="{{ route('admin.branches.index') }}" 
       class="block px-4 py-2 text-sm {{ request()->routeIs('admin.branches.*') ? 'text-indigo-600 font-bold' : 'text-gray-600 hover:text-indigo-600' }}">
       Cabang & Kamar
    </a>

    {{-- Link Penyewa --}}
    <a href="{{ route('admin.tenants.index') }}" 
       class="block px-4 py-2 text-sm {{ request()->routeIs('admin.tenants.*') ? 'text-indigo-600 font-bold' : 'text-gray-600 hover:text-indigo-600' }}">
       Penyewa
    </a>

    {{-- Link Tagihan --}}
    <a href="{{ route('admin.invoices.index') }}" 
       class="block px-4 py-2 text-sm {{ request()->routeIs('admin.invoices.*') ? 'text-indigo-600 font-bold' : 'text-gray-600 hover:text-indigo-600' }}">
       Tagihan
    </a>
</div>
        </div>

        <a href="{{ route('admin.maintenance.index') }}" class="block px-4 py-3 rounded-xl font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Maintenance</a>
        <a href="{{ route('admin.notifications.index') }}" class="block px-4 py-3 rounded-xl font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Notifikasi</a>
        <a href="{{ route('admin.reports.index') }}" class="block px-4 py-3 rounded-xl font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Laporan</a>
    </nav>
</aside>