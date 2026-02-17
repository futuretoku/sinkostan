<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Maintenance - Sin Kost An</title>
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

    @include('partials.navbar')

    <div class="flex pt-20"> {{-- Pembuka Kontainer Flex --}}
        @include('partials.sidebar')

        <main class="flex-1 transition-all-300">
    <div class="max-w-6xl mx-auto mt-10 px-4 mb-10">
        <a href="{{ route('admin.dashboard') }}" class="inline-block bg-[#7c3aed] hover:bg-indigo-700 text-white px-8 py-2 rounded-xl font-medium mb-8 transition">
            Kembali
        </a>

        <div class="bg-white rounded-[32px] p-8 shadow-sm border border-gray-100 min-h-[500px]">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <h1 class="text-2xl font-extrabold">Daftar Laporan Maintenance</h1>
                
                <form action="{{ route('admin.maintenance.index') }}" method="GET" id="filterMaintenance">
                    <select name="kost_id" onchange="document.getElementById('filterMaintenance').submit()" 
                            class="bg-[#f5f7fb] border border-gray-200 p-3 px-5 rounded-2xl font-bold text-sm outline-none shadow-sm cursor-pointer hover:border-indigo-300 transition-all text-gray-700">
                        <option value="">Semua Lokasi Kost</option>
                        @foreach($kosts as $k)
                            <option value="{{ $k->id }}" {{ request('kost_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="space-y-6">
                @forelse ($laporans as $item)
    <div class="border border-gray-200 rounded-2xl p-6 relative bg-white hover:border-indigo-300 transition-all">
        <button onclick="openModal('{{ $item->id }}', '{{ $item->status }}')" 
            class="absolute top-6 right-6 px-4 py-1.5 rounded-full text-xs font-semibold transition cursor-pointer
            {{ $item->status == 'Selesai' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
            {{ $item->status }}
        </button>

        {{-- Label Kecil di Atas --}}
        <div class="flex items-center gap-2 mb-2">
            <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 rounded">
                {{ $item->kost->name ?? 'Sin Kost' }}
            </span>
        </div>

        {{-- Nama Cabang yang Besar --}}
        <div class="text-xl font-bold text-indigo-600 mb-1">
            {{ $item->kost->name ?? 'Lokasi Tidak Diketahui' }}
        </div>

        <h2 class="text-lg font-bold text-gray-800 mb-2">{{ $item->judul }}</h2>
        
        <p class="text-gray-500 text-sm mb-4">
            Kategori: <span class="font-semibold text-gray-700">{{ $item->kategori }}</span>
        </p>
        
        <p class="text-gray-600 leading-relaxed mb-4">
            {{ $item->deskripsi }}
        </p>
        
        <div class="flex items-center text-gray-500 font-medium text-sm">
            📍 Kamar: <span class="ml-1 text-gray-900">{{ $item->nomor_kamar }}</span>
        </div>
    </div>
                @empty
                    <div class="text-center py-24 bg-gray-50 rounded-[24px] border-2 border-dashed border-gray-100">
                        <p class="text-gray-400 font-bold">Belum ada laporan maintenance untuk kategori/lokasi ini.</p>
                    </div>
                @endforelse     
            </div>
        </div>
    </div>
</main>
    </div> {{-- Penutup Kontainer Flex (Baris 20) --}}

    {{-- Modal --}}
    <div id="statusModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden flex items-center justify-center p-4 z-[2000]">
        <div class="bg-white w-full max-w-md rounded-[32px] p-8 shadow-2xl">
            <h3 class="text-2xl font-bold text-slate-700 mb-6">Edit Status Maintenance</h3>
            
            <form id="updateStatusForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-2">
                    <label class="block text-slate-700 font-bold text-sm">Status</label>
                    <div class="relative"> {{-- Tambahan div biar rapi --}}
                        <select name="status" id="statusSelect" class="w-full border border-gray-200 rounded-2xl px-4 py-3 appearance-none focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                            <option value="Dalam Proses">Dalam Proses</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                            ▼
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="closeModal()" class="flex-1 py-3 text-gray-500 font-semibold hover:bg-gray-50 rounded-xl transition">Batal</button>
                    <button type="submit" class="flex-1 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id, currentStatus) {
            const modal = document.getElementById('statusModal');
            const form = document.getElementById('updateStatusForm');
            const select = document.getElementById('statusSelect');

            form.action = `/maintenance/${id}/update-status`;
            select.value = currentStatus;
            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }

        // Script tambahan untuk Sidebar Toggle (jika belum ada)
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('menuOverlay');
            sidebar.classList.toggle('left-0');
            sidebar.classList.toggle('left-[-260px]');
            overlay.classList.toggle('opacity-0');
            overlay.classList.toggle('pointer-events-none');
        });
    </script>
</body>
</html>