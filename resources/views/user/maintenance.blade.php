<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Keluhan – Sin Kost An</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Manrope', sans-serif; }
    </style>
</head>
<body class="bg-[#f5f7fb] text-gray-800">

    <nav class="bg-white px-6 py-4 flex items-center justify-between shadow-sm border-b border-gray-100">
        <div class="flex items-center gap-2 font-extrabold text-lg text-indigo-600">
            <span class="bg-indigo-600 text-white w-9 h-9 rounded-full flex items-center justify-center text-sm">SKA</span>
            <span class="text-gray-700">Sin Kost An</span>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-6 py-8">
        {{-- Mengarahkan ke dashboard user secara eksplisit lebih aman daripada url()->previous() --}}
        <a href="/dashboard" class="inline-flex items-center bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-6 py-2 rounded-lg font-bold text-sm transition-all mb-8 shadow-sm">
            Kembali
        </a>

        @if(session('success'))
            <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 md:p-12">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800 mb-8 italic">Complain / Keluhan</h2>

            {{-- 1. Perbaikan: Tambahkan enctype="multipart/form-data" agar file foto bisa dikirim --}}
            <form action="{{ route('user.maintenance.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <input type="hidden" name="kost_id" id="kost_id_input">

<div class="space-y-2">
    <label class="block text-sm font-extrabold text-gray-700">Pilih Kamar</label>
    <div class="relative">
        <select name="nomor_kamar" id="nomor_kamar_select" required
            class="w-full px-5 py-4 bg-white border border-gray-200 rounded-2xl outline-none appearance-none cursor-pointer">
            <option value="" disabled selected>-- Pilih Kamar Anda --</option>
            
            @forelse($userBookings as $booking)
                {{-- Simpan kost_id di data-attribute --}}
                <option value="{{ $booking->room->room_number }}" data-kost="{{ $booking->room->kost_id }}">
                    {{ $booking->room->room_number }} - {{ $booking->room->room_name ?? $booking->room->type }} 
                    ({{ $booking->room->kost->name }})
                </option>
            @empty
                <option value="" disabled>Anda belum memiliki pesanan kamar aktif</option>
            @endforelse
        {{-- Icon Panah Dropdown --}}
        <div class="absolute inset-y-0 right-5 flex items-center pointer-events-none text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </div>
</div>

                <div class="space-y-2">
                    <label class="block text-sm font-extrabold text-gray-700">Judul Keluhan</label>
                    <input type="text" name="judul" placeholder="Contoh: AC Bocor" required
                        class="w-full px-5 py-4 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all placeholder:text-gray-300">
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-extrabold text-gray-700">Kategori</label>
                    <select name="kategori" required
                        class="w-full px-5 py-4 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all appearance-none cursor-pointer">
                        <option value="Fasilitas">Fasilitas</option>
                        <option value="Kelistrikan">Kelistrikan</option>
                        <option value="Kebersihan">Kebersihan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-extrabold text-gray-700">Deskripsi Keluhan</label>
                    <textarea name="deskripsi" rows="4" placeholder="Jelaskan detail masalahnya..." required
                        class="w-full px-5 py-4 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all placeholder:text-gray-300"></textarea>
                </div>

                {{-- 2. Perbaikan: Tambahkan input untuk kolom 'foto' yang kamu buat di MySQL --}}
                <div class="space-y-2">
                    <label class="block text-sm font-extrabold text-gray-700">Bukti Foto (Opsional)</label>
                    <div class="w-full px-5 py-3 bg-white border border-gray-200 rounded-2xl flex items-center shadow-sm">
                        <input type="file" name="foto" accept="image/*" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer w-full" />
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" 
                        class="w-full bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-indigo-200">
                        Kirim Keluhan
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('nomor_kamar_select').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const kostId = selectedOption.getAttribute('data-kost');
        document.getElementById('kost_id_input').value = kostId;
    });
    </script>
</body>
</html>