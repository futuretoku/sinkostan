<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Keluhan – Sin Kost An</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Manrope', sans-serif; }</style>
</head>
<body class="bg-[#f5f7fb] text-gray-800">

    <nav class="bg-white px-6 py-4 flex items-center justify-between shadow-sm border-b border-gray-100">
        <div class="flex items-center gap-2 font-extrabold text-lg text-indigo-600">
            <span class="bg-indigo-600 text-white w-9 h-9 rounded-full flex items-center justify-center text-sm">SKA</span>
            <span class="text-gray-700">Sin Kost An</span>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-6 py-8">
        <div class="flex items-center justify-between mb-8">
            <a href="/maintenance" class="inline-flex items-center bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-6 py-2 rounded-lg font-bold text-sm transition-all">
                Kembali
            </a>
            <h1 class="text-xl font-black text-gray-800">Riwayat Perbaikan</h1>
        </div>

        <div class="flex border-b border-gray-200 mb-8 gap-8">
            <button class="pb-4 px-2 font-black text-indigo-600 border-b-2 border-indigo-600">Selesai</button>
        </div>

        <div class="space-y-6">
            @forelse ($laporans->where('status', 'Selesai') as $item)
                <div class="bg-white border border-green-100 rounded-[2rem] p-8 shadow-sm relative overflow-hidden">
                    <div class="absolute top-8 right-8">
                        <span class="px-4 py-1.5 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-wider">
                            Selesai
                        </span>
                    </div>

                    <div class="mb-6">
                        <h2 class="text-xl font-extrabold text-gray-800">Kamar {{ $item->nomor_kamar }} - {{ $item->judul }}</h2>
                        <p class="text-xs font-bold text-gray-400 mt-1">{{ $item->updated_at->format('d M Y, H:i') }}</p>
                    </div>

                    <div class="space-y-3">
                        <p class="text-[10px] font-black text-green-600 uppercase tracking-widest">Bukti Perbaikan Admin:</p>
                        
                        <div class="flex flex-wrap gap-3">
                            @if($item->foto_selesai)
                                @foreach(json_decode($item->foto_selesai) as $foto)
                                    <div class="group relative">
                                        <a href="{{ asset('storage/' . $foto) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $foto) }}" 
                                                 class="w-24 h-24 object-cover rounded-2xl border border-gray-100 shadow-sm hover:scale-105 transition-transform duration-300" 
                                                 alt="Bukti Perbaikan">
                                        </a>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-sm text-gray-400 italic">Tidak ada foto bukti.</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-50 flex items-center gap-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                        <p class="text-[11px] font-bold text-gray-500 uppercase">Sudah diperiksa oleh tim maintenance</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-20 bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-200">
                    <p class="text-gray-400 font-bold italic">Belum ada riwayat perbaikan yang selesai.</p>
                </div>
            @endforelse
        </div>
    </div>

</body>
</html>