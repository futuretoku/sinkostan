<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance – Sin Kost An</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Manrope', sans-serif; }</style>
</head>
<body class="bg-[#f5f7fb] text-gray-800">

    <nav class="bg-white px-8 py-4 flex justify-between items-center shadow-sm">
        <div class="flex items-center gap-2">
            <div class="bg-indigo-600 text-white font-bold p-2 rounded-lg text-sm">SKA</div>
            <span class="text-indigo-800 font-bold text-xl">Sin Kost An</span>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto mt-10 px-4 mb-10">
        <a href="{{ route('admin.dashboard') }}" class="inline-block bg-[#7c3aed] hover:bg-indigo-700 text-white px-8 py-2 rounded-xl font-medium mb-8 transition">
            Kembali
        </a>

        <div class="bg-white rounded-[32px] p-8 shadow-sm border border-gray-100 min-h-[500px]">
            <h1 class="text-2xl font-extrabold mb-8">Daftar Laporan Maintenance</h1>

            <div class="space-y-6">
                @forelse ($laporans as $item)
                    <div class="border border-gray-200 rounded-2xl p-6 relative bg-white hover:border-indigo-300 transition-all">
                        <button onclick="openModal('{{ $item->id }}', '{{ $item->status }}')" 
                            class="absolute top-6 right-6 px-4 py-1.5 rounded-full text-xs font-semibold transition cursor-pointer
                            {{ $item->status == 'Selesai' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                            {{ $item->status }}
                        </button>

                        <h2 class="text-xl font-bold text-gray-800 mb-2">{{ $item->judul }}</h2>
                        <p class="text-gray-500 text-sm mb-4">
                            Kategori: <span class="font-semibold text-gray-700">{{ $item->kategori }}</span>
                        </p>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            {{ $item->deskripsi }}
                        </p>
                        <div class="flex items-center text-indigo-600 font-medium text-sm">
                            📍 {{ $item->nomor_kamar }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20">
                        <p class="text-gray-400">Belum ada laporan maintenance yang masuk.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div id="statusModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden flex items-center justify-center p-4 z-[2000]">
        <div class="bg-white w-full max-w-md rounded-[32px] p-8 shadow-2xl">
            <h3 class="text-2xl font-bold text-slate-700 mb-6">Edit Status Maintenance</h3>
            
            <form id="updateStatusForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-2">
                    <label class="block text-slate-700 font-bold text-sm">Status</label>
                    <select name="status" id="statusSelect" class="w-full border border-gray-200 rounded-2xl px-4 py-3 appearance-none focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="Dalam Proses">Dalam Proses</option>
                        <option value="Selesai">Selesai</option>
                    </select>
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

            form.action = `/admin/maintenance/${id}/update-status`;
            select.value = currentStatus;
            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }
    </script>
</body>
</html>