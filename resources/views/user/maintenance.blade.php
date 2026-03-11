@extends('layouts.user')

@section('content')

{{-- SweetAlert tetap ditaruh di sini agar muncul saat ada session success --}}
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        confirmButtonColor: '#7c3aed',
        borderRadius: '20px'
    });
</script>
@endif

<div class="max-w-4xl mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-8">
        <a href="/dashboard" class="inline-flex items-center bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-6 py-2 rounded-lg font-bold text-sm transition-all">
            Kembali
        </a>
        
        <a href="{{ route('user.maintenance.history') }}" 
           class="inline-flex items-center gap-2 text-indigo-600 font-bold text-sm hover:underline">
            <span>Lihat Riwayat Selesai</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
            <h2 class="text-2xl font-extrabold text-gray-800 mb-8 italic">Complain / Keluhan</h2>

            <form action="{{ route('user.maintenance.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="kost_id" id="kost_id_input">
                <input type="hidden" name="nomor_kamar" id="nomor_kamar_input">

                <div class="space-y-2">
                    <label class="block text-sm font-extrabold text-gray-700">Pilih Kamar</label>
                    <select name="room_id" id="room_select" required class="w-full px-5 py-4 border border-gray-200 rounded-2xl outline-none bg-white cursor-pointer">
                        <option value="" disabled selected>-- Pilih Kamar Anda --</option>
                        @foreach($userBookings as $booking)
                            <option value="{{ $booking->room->id }}" 
                                    data-kost="{{ $booking->room->kost_id }}"
                                    data-nomor="{{ $booking->room->room_number }}">
                                {{ $booking->room->room_number }} - {{ $booking->room->kost->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-extrabold text-gray-700">Judul Masalah</label>
                        <input type="text" name="judul" placeholder="Contoh: Lampu Mati" required class="w-full px-5 py-4 border border-gray-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-500/20">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-extrabold text-gray-700">Kategori</label>
                        <select name="kategori" required class="w-full px-5 py-4 border border-gray-200 rounded-2xl outline-none bg-white">
                            <option value="Fasilitas">Fasilitas</option>
                            <option value="Kelistrikan">Kelistrikan</option>
                            <option value="Kebersihan">Kebersihan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-extrabold text-gray-700">Deskripsi Detail</label>
                    <textarea name="deskripsi" rows="4" placeholder="Jelaskan apa yang terjadi..." required class="w-full px-5 py-4 border border-gray-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-500/20"></textarea>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-extrabold text-gray-700">Foto Bukti (Opsional)</label>
                    <input type="file" name="foto" accept="image/*" class="w-full px-5 py-3 border border-gray-200 rounded-2xl text-xs text-gray-400">
                </div>

                <button type="submit" class="w-full bg-[#7c3aed] text-white font-bold py-4 rounded-2xl shadow-lg hover:bg-indigo-700 transition-all">
                    Kirim Laporan
                </button>
            </form>
        </div>

        <div class="space-y-6">
            <h3 class="font-extrabold text-gray-800 flex items-center gap-2">
                <span class="w-2 h-6 bg-indigo-600 rounded-full"></span>
                Sedang Diproses
            </h3>

            @forelse($onProgressMaintenances as $item)
                <div class="bg-white border border-indigo-100 p-5 rounded-3xl shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 px-3 py-1 bg-orange-100 text-orange-600 text-[10px] font-black uppercase rounded-bl-xl">
                        Pending
                    </div>
                    <p class="text-[10px] font-bold text-indigo-500 uppercase">{{ $item->kategori }}</p>
                    <h4 class="font-bold text-gray-800 mt-1">{{ $item->judul }}</h4>
                    <p class="text-xs text-gray-400 mt-2 line-clamp-2 italic">"{{ $item->deskripsi }}"</p>
                    
                    <div class="mt-4 flex items-center justify-between border-t border-gray-50 pt-3">
                        <span class="text-[10px] font-bold text-gray-400">{{ $item->created_at->diffForHumans() }}</span>
                        <span class="text-[10px] font-black text-indigo-600">Kamar {{ $item->nomor_kamar }}</span>
                    </div>
                </div>
            @empty
                <div class="bg-gray-50 border-2 border-dashed border-gray-200 p-8 rounded-[2rem] text-center">
                    <p class="text-xs font-bold text-gray-400 italic">Belum ada keluhan aktif.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    document.getElementById('room_select').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        document.getElementById('kost_id_input').value = selected.getAttribute('data-kost');
        document.getElementById('nomor_kamar_input').value = selected.getAttribute('data-nomor');
    });
</script>

@endsection