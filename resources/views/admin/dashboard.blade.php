<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Sistem Pengelolaan Kost</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Manrope', sans-serif; }
        .transition-all-300 { transition: all 0.3s ease; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-[#f5f7fb] text-gray-800 overflow-x-hidden">

    <div id="menuOverlay" class="fixed inset-0 bg-black/20 opacity-0 pointer-events-none z-[998] transition-opacity duration-300"></div>

    <nav class="fixed top-0 w-full bg-white px-6 py-4 flex items-center justify-between shadow-sm z-[1001]">
        <div class="flex items-center gap-4">
            <button id="sidebarToggle" class="flex flex-col gap-1.5 cursor-pointer p-2 hover:bg-gray-100 rounded-lg">
                <span class="w-6 h-0.5 bg-indigo-600 rounded"></span>
                <span class="w-6 h-0.5 bg-indigo-600 rounded"></span>
                <span class="w-6 h-0.5 bg-indigo-600 rounded"></span>
            </button>
            <div class="flex items-center gap-2 font-extrabold text-lg text-indigo-600">
                <span class="bg-indigo-600 text-white w-9 h-9 rounded-full flex items-center justify-center">SKA</span>
                <span class="hidden md:block text-gray-700">Sin Kost An</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm font-semibold text-gray-600 hidden md:block">Admin Owner</span>
            <div class="w-9 h-9 bg-gray-200 rounded-full flex items-center justify-center font-bold">A</div>
        </div>
    </nav>

    <div class="flex pt-20">
        <aside id="sidebar" class="fixed left-[-260px] top-20 bottom-0 w-[260px] bg-white border-r border-gray-200 p-6 z-[1000] transition-all-300 overflow-y-auto no-scrollbar">
            <h2 class="text-indigo-600 font-extrabold text-lg mb-8 uppercase tracking-wider">Menu Utama</h2>
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 rounded-xl bg-indigo-50 text-indigo-600 font-bold">Dashboard</a>
                <div class="group">
                    <button class="dropdown-toggle w-full flex items-center justify-between px-4 py-3 rounded-xl font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-all">
                        <span>Manajemen</span>
                        <span class="arrow transition-transform duration-200 text-xs">▼</span>
                    </button>
                    <div class="dropdown-menu hidden pl-4 mt-2 space-y-1">
                        <a href="{{ route('admin.branches.index') }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-indigo-600">Cabang & Kamar</a>
                        <a href="{{ route('admin.tenants.index') }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-indigo-600">Penyewa</a>
                        <a href="{{ route('admin.invoices.index') }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-indigo-600">Tagihan</a>
                    </div>
                </div>
                <a href="{{ route('admin.maintenance.index') }}" class="block px-4 py-3 rounded-xl font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Maintenance</a>
                <a href="{{ route('admin.notifications.index') }}" class="block px-4 py-3 rounded-xl font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Notifikasi</a>
                <a href="{{ route('admin.reports.index') }}" class="block px-4 py-3 rounded-xl font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Laporan</a>
            </nav>
        </aside>

        <main id="mainContent" class="flex-1 p-6 md:p-10 transition-all-300">
            {{-- FILTER --}}
            <form action="{{ route('admin.dashboard') }}" method="GET" id="filterDashboard" class="mb-6">
                <input type="hidden" name="range" id="rangeInput" value="{{ $range ?? 'bulan' }}">
                <select name="kost_id" onchange="document.getElementById('filterDashboard').submit()" 
                        class="bg-white border border-gray-200 p-3 rounded-xl font-bold text-sm outline-none shadow-sm cursor-pointer hover:border-indigo-300 transition-all">
                    <option value="">Semua Cabang</option>
                    @foreach($kosts as $k)
                        <option value="{{ $k->id }}" {{ ($selectedKostId ?? '') == $k->id ? 'selected' : '' }}>
                            {{ $k->name }}
                        </option>
                    @endforeach
                </select>
            </form>
            
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <h1 class="text-2xl font-extrabold">Dashboard Overview</h1>
                <div class="flex bg-gray-200 p-1 rounded-xl">
                    <button type="button" onclick="updateRange('minggu')" class="px-4 py-1.5 text-sm font-bold rounded-lg transition-all {{ ($range ?? 'bulan') == 'minggu' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-indigo-600' }}">Minggu</button>
                    <button type="button" onclick="updateRange('bulan')" class="px-4 py-1.5 text-sm font-bold rounded-lg transition-all {{ ($range ?? 'bulan') == 'bulan' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-indigo-600' }}">Bulan</button>
                    <button type="button" onclick="updateRange('tahun')" class="px-4 py-1.5 text-sm font-bold rounded-lg transition-all {{ ($range ?? 'bulan') == 'tahun' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:text-indigo-600' }}">Tahun</button>
                </div>
            </div>

            {{-- STATS CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                    <h4 class="text-gray-500 text-sm font-semibold mb-2">Total Kamar</h4>
                    <div class="text-3xl font-extrabold mb-1">{{ $totalKamar ?? 0 }}</div>
                    <small class="text-gray-400 font-medium">Semua unit</small>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                    <h4 class="text-gray-500 text-sm font-semibold mb-2">Kamar Terisi</h4>
                    <div class="text-3xl font-extrabold mb-1">{{ $kamarTerisi ?? 0 }}</div>
                    <small class="text-gray-400 font-medium">{{ $okupansi ?? 0 }}% Okupansi</small>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                    <h4 class="text-gray-500 text-sm font-semibold mb-2">Kamar Kosong</h4>
                    <div class="text-3xl font-extrabold mb-1">{{ $kamarKosong ?? 0 }}</div>
                    <small class="text-gray-400 font-medium">Tersedia</small>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                    <h4 class="text-gray-500 text-sm font-semibold mb-2">Pemasukan</h4>
                    <div class="text-3xl font-extrabold mb-1 text-green-600">Rp {{ number_format($pemasukan ?? 0, 0, ',', '.') }}</div>
                    <small class="text-gray-400 font-medium">Periode {{ ucfirst($range ?? 'bulan') }}</small>
                </div>
            </div>

            {{-- VISUALISASI CABANG KOST --}}
            <h2 class="text-xl font-extrabold mb-6">Visualisasi Cabang Kost</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                @foreach($kosts as $kost)
                <div class="bg-white rounded-[32px] overflow-hidden shadow-sm border border-gray-100 group hover:shadow-xl transition-all duration-500">
                    <div class="relative h-64 overflow-hidden">
                        @if($kost->image)
                            <img src="{{ asset('uploads/kosts/' . explode(', ', $kost->image)[0]) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        @else
                            <div class="w-full h-full bg-indigo-100 flex items-center justify-center text-indigo-400 font-bold italic">Tanpa Foto</div>
                        @endif

                        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-md px-4 py-2 rounded-2xl shadow-sm">
                            <span class="text-xs font-extrabold text-indigo-600 tracking-wider uppercase">CABANG {{ $kost->name }}</span>
                        </div>

                        @php
                            $totalR = $kost->rooms ? $kost->rooms->count() : 0;
                            $occR = $kost->rooms ? $kost->rooms->where('status', 'occupied')->count() : 0;
                            $perc = $totalR > 0 ? round(($occR / $totalR) * 100) : 0;
                        @endphp
                        
                        <div class="absolute bottom-4 right-4 bg-indigo-600 text-white px-3 py-1 rounded-full text-[10px] font-bold shadow-lg">
                            {{ $perc }}% Terisi ({{ $occR }}/{{ $totalR }})
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-bold text-gray-800">{{ $kost->name }}</h3>
                            <span class="text-indigo-600 font-bold text-sm">
                                @php $minP = $kost->rooms ? $kost->rooms->min('price') : 0; @endphp
                                Rp {{ number_format($minP, 0, ',', '.') }}<span class="text-gray-400 font-normal text-xs">/bln</span>
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-4 italic line-clamp-1">{{ $kost->address }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- ANALISIS TRANSAKSI --}}
            <h2 class="text-xl font-extrabold mb-6">Analisis Transaksi & Pendapatan</h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
                <div class="lg:col-span-2 bg-white p-8 rounded-[32px] shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-6">Grafik Penghasilan (IDR)</h3>
                    <canvas id="incomeChart" height="120"></canvas>
                </div>
                <div class="bg-white p-8 rounded-[32px] shadow-sm border border-gray-100 text-center">
                    <h3 class="font-bold text-gray-800 mb-6">Sumber Pembayaran</h3>
                    <div class="relative h-48">
                        <canvas id="paymentMethodChart"></canvas>
                    </div>
                </div>
            </div>

            

            {{-- CHART OKUPANSI & AKTIVITAS --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-extrabold mb-6">Tingkat Hunian Mingguan (%)</h3>
                    <canvas id="occupancyChart" height="150"></canvas>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-extrabold mb-6">Aktivitas Terbaru</h3>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-sm text-gray-400 border-b border-gray-50 pb-3 italic">
                            <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                            Belum ada laporan aktivitas di cabang ini.
                        </li>
                    </ul>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function updateRange(val) {
            document.getElementById('rangeInput').value = val;
            document.getElementById('filterDashboard').submit();
        }

        document.addEventListener('DOMContentLoaded', () => {
            const sidebarToggle = document.getElementById("sidebarToggle");
            const sidebar = document.getElementById("sidebar");
            const mainContent = document.getElementById("mainContent");
            const menuOverlay = document.getElementById("menuOverlay");

            sidebarToggle.onclick = () => {
                const isOpen = sidebar.style.left === "0px";
                sidebar.style.left = isOpen ? "-260px" : "0px";
                if (window.innerWidth > 768) {
                    mainContent.style.marginLeft = isOpen ? "0" : "260px";
                }
                menuOverlay.classList.toggle("opacity-0");
                menuOverlay.classList.toggle("opacity-100");
                menuOverlay.classList.toggle("pointer-events-none");
            };

            document.querySelectorAll('.dropdown-toggle').forEach(btn => {
                btn.addEventListener('click', () => {
                    btn.nextElementSibling.classList.toggle('hidden');
                    btn.querySelector('.arrow').classList.toggle('rotate-180');
                });
            });

            // CHART INCOME
            new Chart(document.getElementById('incomeChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($incomeLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun']) !!},
                    datasets: [{
                        label: 'Penghasilan',
                        data: {!! json_encode($incomeValues ?? [0,0,0,0,0,0]) !!},
                        backgroundColor: '#4f46e5',
                        borderRadius: 10,
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } } }
                }
            });

            // CHART PAYMENT
            new Chart(document.getElementById('paymentMethodChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Transfer', 'E-Wallet', 'Tunai'],
                    datasets: [{
                        data: {!! json_encode($paymentMethods ?? [0,0,0]) !!},
                        backgroundColor: ['#4f46e5', '#38bdf8', '#fbbf24'],
                        borderWidth: 0
                    }]
                },
                options: {
                    plugins: { legend: { position: 'bottom' } },
                    maintainAspectRatio: false
                }
            });

            // CHART OCCUPANCY
            new Chart(document.getElementById('occupancyChart'), {
                type: 'line',
                data: {
                    labels: ['Sen','Sel','Rab','Kam','Jum','Sab','Min'],
                    datasets: [{
                        data: [10, 25, 15, 40, 35, 60, 45],
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#4f46e5'
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: { 
                        y: { beginAtZero: true, max: 100, display: true, grid: { color: '#f3f4f6' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
</body>
</html>