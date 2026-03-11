@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    {{-- Tombol Kembali --}}
    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-6 py-2.5 rounded-xl font-bold shadow-sm hover:bg-indigo-700 transition-all mb-8">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali
    </a>

    <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
        <h1 class="text-3xl font-black text-gray-800 mb-2 text-center md:text-left">Pusat Laporan</h1>
        <p class="text-gray-500 mb-10 text-center md:text-left font-medium">Pilih jenis laporan yang ingin Anda lihat atau cetak.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Opsi 1: Laporan Keuangan --}}
            <a href="{{ route('admin.laporankeuangan-detail') }}" class="group bg-white border-2 border-gray-50 rounded-[2rem] p-8 shadow-sm hover:border-indigo-500 hover:shadow-xl hover:shadow-indigo-100 transition-all text-center">
                <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-xl font-black text-gray-800 mb-2">Laporan Keuangan</h3>
                <p class="text-gray-500 text-sm">Ringkasan pemasukan, riwayat transaksi, dan total pendapatan.</p>
            </a>

            {{-- Opsi 2: Laporan Kamar & Penyewa (Digabung) --}}
            <a href="{{ route('admin.laporankamardanpenyewa-detail') }}" class="group bg-white border-2 border-gray-50 rounded-[2rem] p-8 shadow-sm hover:border-indigo-500 hover:shadow-xl hover:shadow-indigo-100 transition-all text-center">
                <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="text-xl font-black text-gray-800 mb-2">Laporan Kamar & Penyewa</h3>
                <p class="text-gray-500 text-sm">Status keterisian kamar dan detail data penyewa aktif.</p>
            </a>
        </div>
    </div>
</div>
@endsection