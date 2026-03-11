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
    </style>
</head>
<body class="bg-[#f5f7fb] text-gray-800 overflow-x-hidden">

    <div id="menuOverlay" class="fixed inset-0 bg-black/20 opacity-0 pointer-events-none z-[998] transition-opacity duration-300"></div>

    @include('partials.navbar')

    <div class="flex pt-20">
        @include('partials.sidebar')

        <main class="flex-1 transition-all-300">
            <div class="max-w-6xl mx-auto mt-10 px-4 mb-10">
                <a href="{{ route('admin.dashboard') }}" class="inline-block bg-[#7c3aed] hover:bg-indigo-700 text-white px-8 py-2 rounded-xl font-medium mb-8 transition">
                    Kembali
                </a>

                <div class="bg-white rounded-[32px] p-8 shadow-sm border border-gray-100 min-h-[500px]">
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                        <h1 class="text-2xl font-extrabold">Daftar Maintenance</h1>
                        
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

                    <div class="flex border-b border-gray-200 mb-8 gap-8">
                        <button onclick="switchTab('pending')" id="tab-pending" class="pb-4 px-2 font-black text-indigo-600 border-b-2 border-indigo-600 transition-all">
                            Perlu Ditangani ({{ $laporans->where('status', 'Dalam Proses')->count() }})
                        </button>
                        <button onclick="switchTab('resolved')" id="tab-resolved" class="pb-4 px-2 font-black text-gray-400 hover:text-indigo-600 transition-all">
                            Selesai ({{ $laporans->where('status', 'Selesai')->count() }})
                        </button>
                    </div>

                    <div id="content-pending" class="space-y-6">
                        @forelse ($laporans->where('status', 'Dalam Proses') as $item)
    <div class="border border-gray-200 rounded-2xl p-6 relative bg-white hover:border-indigo-300 transition-all shadow-sm">
        <button onclick="openModal('{{ $item->id }}', '{{ $item->status }}')" 
            class="absolute top-6 right-6 px-6 py-2 rounded-xl text-xs font-bold bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm transition-all active:scale-95">
            <i class="fas fa-edit mr-1"></i> Update Status
        </button>

        <div class="mb-4">
            <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 rounded inline-block mb-1">
                {{ $item->room->kost->name ?? 'Lokasi Kost' }}
            </span>
            <h2 class="text-xl font-bold text-gray-800">Kamar {{ $item->nomor_kamar }}</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-bold text-gray-700 mb-1">{{ $item->judul }}</h3>
                <p class="text-gray-500 text-sm mb-2">Kategori: <span class="font-bold">{{ $item->kategori }}</span></p>
                <p class="text-gray-600 text-sm mb-4 italic">"{{ $item->deskripsi }}"</p>
                
                <div class="mb-4">
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase {{ $item->status == 'Selesai' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                        Status: {{ $item->status }}
                    </span>
                </div>

                @if($item->foto)
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Foto dari User:</p>
                    <a href="{{ asset('storage/' . $item->foto) }}" target="_blank" class="inline-block group">
                        <img src="{{ asset('storage/' . $item->foto) }}" class="w-24 h-24 object-cover rounded-xl border border-gray-100 group-hover:opacity-80 transition">
                    </a>
                @endif
            </div>
            <div class="flex items-end justify-end">
                <p class="text-[11px] text-gray-400">Dilaporkan {{ $item->created_at->locale('id')->diffForHumans() }}</p>
            </div>
        </div>
    </div>
@empty
    @endforelse
                    </div>

                    <div id="content-resolved" class="space-y-6 hidden">
                        @forelse ($laporans->where('status', 'Selesai') as $item)
                            <div class="border border-green-100 rounded-2xl p-6 bg-white shadow-sm opacity-80">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h2 class="text-lg font-bold text-gray-800">Kamar {{ $item->nomor_kamar }} - {{ $item->judul }}</h2>
                                        <p class="text-xs text-gray-400">{{ $item->updated_at->format('d M Y, H:i') }}</p>
                                    </div>
                                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase">Selesai</span>
                                </div>
                                
                                @if($item->foto_selesai)
                                    <p class="text-[10px] font-bold text-green-600 uppercase mb-2">Bukti Perbaikan Admin:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach(json_decode($item->foto_selesai) as $foto)
                                            <a href="{{ asset('storage/' . $foto) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $foto) }}" class="w-16 h-16 object-cover rounded-lg border">
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-20 text-gray-400 italic">Belum ada riwayat keluhan selesai.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="statusModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden flex items-center justify-center p-4 z-[2000]">
        <div class="bg-white w-full max-w-md rounded-[32px] p-8 shadow-2xl">
            <h3 class="text-2xl font-bold text-slate-700 mb-6">Update Progres</h3>
            <form id="updateStatusForm" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT') <div class="space-y-4">
        <label class="block text-sm font-bold text-gray-700">Pilih Status</label>
        <select name="status" id="statusSelect" class="w-full border border-gray-200 rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
            <option value="Dalam Proses">Dalam Proses</option>
            <option value="Selesai">Selesai</option>
        </select>

        <div id="buktiSelesaiContainer" class="hidden">
            <label class="block text-sm font-bold text-gray-700 mb-2">Upload Bukti Perbaikan</label>
            <input type="file" name="foto_selesai[]" multiple accept="image/*" class="text-xs text-gray-500 w-full">
        </div>
    </div>

    <div class="mt-8 flex gap-3">
        <button type="button" onclick="closeModal()" class="flex-1 py-3 text-gray-500 font-bold hover:bg-gray-50 rounded-xl transition">Batal</button>
        <button type="submit" class="flex-1 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition">Simpan</button>
    </div>
</form>
        </div>
    </div>

    <script>
        function switchTab(type) {
            const pTab = document.getElementById('tab-pending');
            const rTab = document.getElementById('tab-resolved');
            const pCont = document.getElementById('content-pending');
            const rCont = document.getElementById('content-resolved');

            if(type === 'pending') {
                pTab.classList.add('text-indigo-600', 'border-indigo-600');
                pTab.classList.remove('text-gray-400');
                rTab.classList.add('text-gray-400');
                rTab.classList.remove('text-indigo-600', 'border-indigo-600');
                pCont.classList.remove('hidden'); rCont.classList.add('hidden');
            } else {
                rTab.classList.add('text-indigo-600', 'border-indigo-600');
                rTab.classList.remove('text-gray-400');
                pTab.classList.add('text-gray-400');
                pTab.classList.remove('text-indigo-600', 'border-indigo-600');
                rCont.classList.remove('hidden'); pCont.classList.add('hidden');
            }
        }

        function openModal(id, status) {
    const modal = document.getElementById('statusModal');
    const form = document.getElementById('updateStatusForm');
    
    // Gunakan window.location.origin agar URL-nya lengkap (http://localhost:8000/...)
    form.action = window.location.origin + `/maintenance/${id}/update-status`; 
    
    document.getElementById('statusSelect').value = status;
    toggleBukti(status);
    modal.classList.remove('hidden');
}

        function closeModal() { document.getElementById('statusModal').classList.add('hidden'); }

        document.getElementById('statusSelect').addEventListener('change', function() {
            toggleBukti(this.value);
        });

        function toggleBukti(status) {
            const container = document.getElementById('buktiSelesaiContainer');
            status === 'Selesai' ? container.classList.remove('hidden') : container.classList.add('hidden');
        }
    </script>
</body>
</html>