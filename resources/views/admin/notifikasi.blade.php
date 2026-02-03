<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Pembayaran – Sin Kost An</title>
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
        <aside id="sidebar" class="fixed left-0 top-20 bottom-0 w-[260px] bg-white border-r border-gray-200 p-6 z-[1000] transition-all-300 overflow-y-auto no-scrollbar">
            <h2 class="text-indigo-600 font-extrabold text-lg mb-8 uppercase tracking-wider">Menu Utama</h2>
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 rounded-xl font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Dashboard</a>
                
                <div class="group">
                    <button class="dropdown-toggle w-full flex items-center justify-between px-4 py-3 rounded-xl bg-indigo-50 text-indigo-600 font-bold">
                        <span>Manajemen</span>
                        <span class="arrow transition-transform duration-200 text-xs rotate-180">▼</span>
                    </button>
                    <div class="dropdown-menu pl-4 mt-2 space-y-1">
                        <a href="{{ route('admin.branches.index') }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-indigo-600">Cabang & Kamar</a>
                        <a href="{{ route('admin.tenants.index') }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-indigo-600">Penyewa</a>
                        <a href="{{ route('admin.invoices.index') }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-indigo-600">Tagihan</a>
                    </div>
                </div>

                <a href="{{ route('admin.maintenance.index') }}" class="block px-4 py-3 rounded-xl font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Maintenance</a>
                <a href="{{ route('admin.notifications.index') }}" class="block px-4 py-3 rounded-xl bg-indigo-50 text-indigo-600 font-bold">Notifikasi</a>
                <a href="{{ route('admin.reports.index') }}" class="block px-4 py-3 rounded-xl font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Laporan</a>
            </nav>
        </aside>

        <main class="flex-1 ml-[260px] p-8 transition-all-300">
            <div class="max-w-7xl mx-auto">
                <h3 class="text-gray-800 text-3xl font-extrabold">Notifikasi Pembayaran Baru</h3>
                <p class="text-gray-500 mt-2 mb-8">Konfirmasi pembayaran untuk mengaktifkan status pesanan user.</p>

                @if(session('success'))
                    <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                        <div class="flex items-center">
                            <span class="font-bold mr-2">Berhasil!</span> {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                        <div class="flex items-center">
                            <span class="font-bold mr-2">Gagal!</span> {{ session('error') }}
                        </div>
                    </div>
                @endif

                <div class="mt-4">
                    @if($pendingPayments->isEmpty())
                        <div class="bg-white border border-dashed border-gray-300 rounded-2xl p-12 text-center">
                            <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">🔔</div>
                            <p class="text-gray-500 font-medium">Tidak ada pemberitahuan pembayaran baru saat ini.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($pendingPayments as $payment)
                            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-all-300 overflow-hidden border border-gray-100">
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="px-3 py-1 text-[10px] uppercase tracking-wider font-bold text-orange-600 bg-orange-100 rounded-full">
                                            Pending Verification
                                        </span>
                                        <span class="text-xs text-gray-400">{{ $payment->created_at->diffForHumans() }}</span>
                                    </div>
                                    
                                    <h4 class="text-xl font-bold text-gray-800">{{ $payment->bill->booking->user->name ?? 'User' }}</h4>
                                    <div class="mt-2 space-y-1">
                                        <p class="text-sm text-gray-600 flex justify-between"><span>Nomor Kamar:</span> <span class="font-bold text-gray-800">{{ $payment->bill->booking->room->room_number }}</span></p>
                                        <p class="text-sm text-gray-600 flex justify-between"><span>Total Bayar:</span> <span class="font-bold text-indigo-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span></p>
                                    </div>
                                    
                                    <div class="mt-5">
                                        <p class="text-xs font-bold text-gray-400 uppercase mb-2">Bukti Pembayaran:</p>
                                        <a href="{{ asset('storage/' . $payment->proof) }}" target="_blank" class="block group relative">
                                            <img src="{{ asset('storage/' . $payment->proof) }}" class="w-full h-40 object-cover rounded-xl border border-gray-100 group-hover:brightness-75 transition-all">
                                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <span class="bg-black/50 text-white text-xs px-3 py-1 rounded-full">Lihat Detail</span>
                                            </div>
                                        </a>
                                    </div>

                                    <div class="mt-6 flex gap-3">
                                        <form action="{{ route('admin.payments.approve', $payment->id) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl transition shadow-lg shadow-indigo-100 text-sm">
                                                Konfirmasi
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full bg-white border border-red-200 text-red-500 hover:bg-red-50 font-bold py-2.5 rounded-xl transition text-sm">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const overlay = document.getElementById('menuOverlay');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('left-0');
            sidebar.classList.toggle('left-[-260px]');
        });
    </script>
</body>
</html>