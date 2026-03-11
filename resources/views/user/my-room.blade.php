@extends('layouts.user')

@vite(['resources/css/app.css', 'resources/js/app.js'])

<div class="max-w-4xl mx-auto p-6 bg-white rounded-2xl shadow-lg mt-10" 
     x-data="{ 
        showExtendModal: false, 
        extendDuration: 1, 
        pricePerMonth: {{ $myRoom->price ?? 0 }},
        get totalExtension() { return new Intl.NumberFormat('id-ID').format(this.extendDuration * this.pricePerMonth) }
     }">
     
    <h1 class="text-2xl font-bold mb-6">Kamar Saya</h1>

    @if(!$myRoom)
        <div class="text-center py-10">
            <p class="text-gray-500">Anda belum memiliki pesanan kamar aktif.</p>
        </div>
    @else
    <div class="flex flex-col md:flex-row gap-8">
        {{-- Sisi Kiri: Gambar & Tagihan --}}
        <div class="flex-1">
            <img src="{{ $myRoom->display_image }}" class="w-full h-64 object-cover rounded-xl mb-4 shadow-sm" alt="Foto Kamar">
            
            <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                @if($nextBill)
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-500 font-medium">Status Tagihan</span>
                        <span class="text-sm font-bold text-red-500">Jatuh tempo: {{ $nextBill->due_date_formatted }}</span>
                    </div>
                    
                    <span class="bg-yellow-100 text-yellow-700 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider">
                        {{ $nextBill->status }}
                    </span>

                    <div class="grid grid-cols-2 mt-6">
                        <div>
                            <p class="text-gray-400 text-sm">Periode Kost</p>
                            <p class="font-semibold text-xs md:text-sm">{{ $nextBill->periode_start }} - {{ $nextBill->periode_end }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-400 text-sm">Total Tagihan</p>
                            <p class="font-bold text-indigo-600 text-xl">Rp {{ number_format($nextBill->amount, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <a href="#" class="block w-full text-center bg-indigo-600 text-white font-bold py-3 rounded-xl mt-6 hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 active:scale-95">
                        Bayar Tagihan
                    </a>
                @else
                    <div class="text-center p-4">
                        <span class="text-green-600 font-bold flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            Semua tagihan sudah lunas!
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sisi Kanan: Detail Kamar --}}
        <div class="w-full md:w-1/3">
            <div class="mb-4">
                <h2 class="text-xl font-bold text-slate-800">{{ $myRoom->kost_name }} - Lantai {{ $myRoom->floor }}</h2>
                <p class="text-gray-500 text-sm">Kamar No. {{ $myRoom->room_number }} • Tipe {{ $myRoom->type }}</p>
            </div>

            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-tight">
                {{ $myRoom->status }}
            </span>

            <div class="grid grid-cols-2 gap-4 mt-6 border-t pt-4">
                <div>
                    <p class="text-gray-400 text-[10px] uppercase font-bold tracking-widest">Nomor Kamar</p>
                    <p class="font-bold text-slate-700">{{ $myRoom->room_number }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-[10px] uppercase font-bold tracking-widest">Lantai</p>
                    <p class="font-bold text-slate-700">{{ $myRoom->floor }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-[10px] uppercase font-bold tracking-widest">Harga / Bulan</p>
                    <p class="font-bold text-indigo-600 text-sm">Rp {{ number_format($myRoom->price, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-[10px] uppercase font-bold tracking-widest">Tipe</p>
                    <p class="font-bold text-slate-700">{{ $myRoom->type }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 mt-4">
                @foreach(explode(',', $myRoom->facilities) as $facility)
                    <span class="bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-md text-[9px] font-bold border border-indigo-100 uppercase">{{ trim($facility) }}</span>
                @endforeach
            </div>

            <div class="mt-8 bg-blue-50/50 p-5 rounded-xl border border-blue-100">
                <h3 class="font-bold text-slate-700 mb-3 text-sm">Masa Ngekost</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Mulai</span>
                        <span class="font-semibold text-blue-900">{{ $myRoom->start_date_formatted }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Berakhir</span>
                        <span class="font-semibold text-blue-900">{{ $myRoom->end_date_formatted }}</span>
                    </div>
                    <div class="flex justify-between border-t border-blue-200 pt-2 mt-2">
                        <span class="text-gray-500 font-medium">Durasi Sewa</span>
                        <span class="font-bold text-indigo-600">{{ $myRoom->duration_months }} Bulan</span>
                    </div>
                </div>

                {{-- TOMBOL PERPANJANG (Logika 7 Hari) --}}
                @php
                    $endDate = \Carbon\Carbon::parse($myRoom->end_date);
                    $isExpiringSoon = now()->diffInDays($endDate, false) <= 7;
                @endphp

                @if($isExpiringSoon && $myRoom->status != 'Non-Aktif')
                    <button @click="showExtendModal = true" class="w-full bg-orange-500 text-white font-bold py-3 rounded-xl mt-4 hover:bg-orange-600 transition-all shadow-md active:scale-95">
                        Perpanjang Masa Sewa
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL PERPANJANGAN (Gaya sesuai gambar kamu) --}}
    <div x-show="showExtendModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-cloak>
         
        <div class="bg-white w-full max-w-md rounded-[2.5rem] p-8 shadow-2xl relative transform transition-all"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="scale-90 opacity-0"
             x-transition:enter-end="scale-100 opacity-100"
             @click.away="showExtendModal = false">
             
            <h2 class="text-2xl font-bold text-slate-800 mb-2">Perpanjangan</h2>
            <p class="text-gray-500 text-sm mb-8">Pilih durasi perpanjangan kost</p>

            <div class="space-y-4">
                {{-- Metode Pembayaran (Visual Only sesuai gambar) --}}
                <div class="border-2 border-indigo-600 rounded-2xl p-4 flex justify-between items-center bg-indigo-50/30">
                    <div class="flex items-center gap-3">
                        <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-100">💳</div>
                        <div>
                            <p class="font-bold text-slate-800 text-sm">Transfer Bank</p>
                            <p class="text-[10px] text-gray-500">BCA / BRI / Mandiri</p>
                        </div>
                    </div>
                    <div class="w-5 h-5 rounded-full border-4 border-indigo-600 bg-white"></div>
                </div>

                {{-- Durasi Perpanjangan --}}
                <div>
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Durasi Perpanjangan</label>
                    <select x-model="extendDuration" class="w-full bg-gray-50 border-none rounded-2xl py-4 px-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 appearance-none shadow-sm">
                        <option value="1">1 Bulan</option>
                        <option value="3">3 Bulan</option>
                        <option value="6">6 Bulan</option>
                        <option value="12">1 Tahun</option>
                    </select>
                </div>

                {{-- Detail Harga --}}
                <div class="space-y-2 pt-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Harga per Bulan</span>
                        <span class="font-bold">Rp {{ number_format($myRoom->price ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Durasi Perpanjangan</span>
                        <span class="font-bold" x-text="extendDuration + ' Bulan'">-</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-dashed pt-4 mt-2">
                        <span class="text-xl font-bold text-slate-800">Total</span>
                        <span class="text-xl font-black text-indigo-600" x-text="'Rp ' + totalExtension"></span>
                    </div>
                </div>

                {{-- Form Submit --}}
                <form action="{{ route('extend.room', $myRoom->id ?? 0) }}" method="POST">
                    @csrf
                    <input type="hidden" name="months" :value="extendDuration">
                    <button type="submit" class="w-full bg-indigo-600 text-white font-black py-5 rounded-[1.5rem] mt-6 shadow-xl shadow-indigo-200 hover:bg-indigo-700 active:scale-95 transition-all">
                        Bayar Perpanjangan
                    </button>
                </form>

                <button @click="showExtendModal = false" class="w-full text-center text-gray-400 font-bold text-xs mt-4 hover:text-gray-600 transition-colors">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>