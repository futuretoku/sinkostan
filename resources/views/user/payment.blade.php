@extends('layouts.user')

@section('content')
<style>
    /* Menyembunyikan scrollbar tapi tetap bisa di-scroll */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    
    /* Smooth transition untuk indikator dot slider */
    [id^="dot-"] { transition: all 0.3s ease; }

    /* Animasi loading untuk tombol */
    .btn-loading {
        position: relative;
        color: transparent !important;
        pointer-events: none;
    }
    .btn-loading::after {
        content: "";
        position: absolute;
        width: 20px;
        height: 20px;
        top: 50%;
        left: 50%;
        margin: -10px 0 0 -10px;
        border: 3px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 0.8s ease-in-out infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<div class="bg-[#f8fafc] min-h-screen p-4 md:p-12 text-slate-800">
    <div class="max-w-6xl mx-auto">
        
        {{-- Tombol Kembali --}}
        <a href="javascript:history.back()" class="inline-flex items-center bg-[#6366f1] text-white px-5 py-2 rounded-xl text-xs font-bold hover:bg-[#4f46e5] transition-all shadow-lg shadow-indigo-100 mb-8">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- Sisi Kiri: Detail Kamar --}}
            <div class="lg:col-span-7 bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                <h2 class="text-2xl font-black text-slate-800 mb-1">Detail Kamar</h2>
                <p class="text-sm text-slate-400 mb-6">
                    {{ $room->branch_name ?? 'Cabang Tidak Terdeteksi' }} – Lantai {{ $room->floor }}
                </p>

                {{-- Image Slider --}}
                <div class="relative group mb-8">
                    @php
                        $imgString = $room->image ?? ''; 
                        $roomImagesRaw = explode(', ', $imgString);
                        $processedImages = [];
                        foreach($roomImagesRaw as $img) {
                            $cleanImg = trim($img);
                            if(!empty($cleanImg) && file_exists(public_path('uploads/rooms/' . $cleanImg))) {
                                $processedImages[] = asset('uploads/rooms/' . $cleanImg);
                            }
                        }
                        if(empty($processedImages)) {
                            $processedImages[] = 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85';
                        }
                    @endphp

                    <div id="imageSlider" class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth rounded-[2rem] shadow-md no-scrollbar">
                        @foreach($processedImages as $imageUrl)
                            <div class="min-w-full snap-center flex-shrink-0">
                                <img src="{{ $imageUrl }}" class="w-full h-80 object-cover transition-transform duration-700 hover:scale-105">
                            </div>
                        @endforeach
                    </div>

                    @if(count($processedImages) > 1)
                        <button onclick="slideImage('prev')" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 backdrop-blur-md p-2 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity z-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button onclick="slideImage('next')" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 backdrop-blur-md p-2 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity z-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div class="flex justify-center gap-2 mt-4">
                            @foreach($processedImages as $index => $url)
                                <div class="h-1.5 {{ $index === 0 ? 'w-6 bg-indigo-600' : 'w-1.5 bg-slate-200' }} rounded-full transition-all duration-300" id="dot-{{ $index }}"></div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-y-8 mb-8">
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-widest font-bold mb-1">Nomor Kamar</p>
                        <p class="text-xl font-bold text-slate-800">{{ $room->room_number }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-widest font-bold mb-1">Tipe</p>
                        <p class="text-xl font-bold text-slate-800">{{ $room->type }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-widest font-bold mb-1">Harga / Bulan</p>
                        <p class="text-2xl font-black text-indigo-600">Rp {{ number_format($room->price, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-widest font-bold mb-1">Status</p>
                        <p class="text-xl font-bold text-slate-800">Tersedia</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach(['Kasur', 'Lemari', 'Meja', 'WiFi', 'Kamar Mandi Dalam'] as $tag)
                    <span class="bg-indigo-50 text-indigo-600 px-4 py-1.5 rounded-full text-[11px] font-bold border border-indigo-100">
                        {{ $tag }}
                    </span>
                    @endforeach
                </div>
            </div>

            {{-- Sisi Kanan: Form Pembayaran --}}
            <div class="lg:col-span-5 bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 flex flex-col h-full">
                <h2 class="text-2xl font-black text-slate-800 mb-1">Pembayaran</h2>
                <p class="text-sm text-slate-400 mb-8">Pilih metode pembayaran</p>

                <form action="{{ route('booking.store') }}" method="POST" id="paymentForm" class="space-y-4 flex-grow">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    <input type="hidden" name="start_date" value="{{ now()->format('Y-m-d') }}">
                    <input type="hidden" name="total_price" id="hidden_total" value="{{ $room->price }}">
                    
                    {{-- Metode Pembayaran --}} 
                    <div class="space-y-3">
                        @foreach([
                            ['id' => 'transfer', 'title' => 'Transfer Bank', 'desc' => 'BCA / BRI / Mandiri', 'icon' => '💳'],
                            ['id' => 'ewallet', 'title' => 'E-Wallet', 'desc' => 'OVO, DANA, GoPay (QRIS)', 'icon' => '📱'],
                            ['id' => 'cod', 'title' => 'Bayar di Tempat', 'desc' => 'Saat check-in', 'icon' => '🏠']
                        ] as $method)
                        <label class="payment-card flex items-center justify-between p-4 rounded-2xl border-2 border-slate-50 bg-slate-50/30 cursor-pointer transition-all hover:border-indigo-100 group has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/30">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm text-lg">
                                    {{ $method['icon'] }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-700">{{ $method['title'] }}</span>
                                    <span class="text-xs text-slate-400">{{ $method['desc'] }}</span>
                                </div>
                            </div>
                            <input type="radio" name="payment_method" value="{{ $method['id'] }}" required class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-slate-300 cursor-pointer">
                        </label>
                        @endforeach
                    </div>

                    {{-- Pilihan Durasi --}}
                    <div class="pt-6">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Durasi Sewa</label>
                        <div class="relative">
                            <select id="duration_select" name="duration_months" class="w-full p-4 rounded-2xl border-2 border-slate-50 bg-slate-50/50 font-bold text-slate-700 outline-none focus:border-indigo-200 transition-all appearance-none cursor-pointer">
                                <option value="1">1 Bulan</option>
                                <option value="3">3 Bulan</option>
                                <option value="6">6 Bulan</option>
                                <option value="12">1 Tahun</option>
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Ringkasan Biaya --}}
                    <div class="pt-8 space-y-3 border-t border-dashed border-slate-200 mt-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400 font-medium">Harga per Bulan</span>
                            <span class="text-slate-700 font-bold text-right">Rp {{ number_format($room->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-400 font-medium">Durasi</span>
                            <span id="display_duration" class="text-slate-700 font-bold">1 Bulan</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-lg font-black text-slate-800">Total</span>
                            <span id="display_total" class="text-lg font-black text-indigo-600">Rp {{ number_format($room->price, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button type="submit" id="btnSubmit" class="w-full bg-[#6366f1] hover:bg-[#4f46e5] text-white font-black py-5 rounded-2xl transition-all shadow-xl shadow-indigo-100 mt-8 active:scale-[0.98] flex items-center justify-center">
                        <span>Bayar Sekarang</span>
                    </button>
                    <p class="text-[10px] text-slate-400 text-center mt-4 uppercase tracking-widest font-bold">
                        Pemesanan akan diverifikasi secara otomatis
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const pricePerMonth = {{ $room->price ?? 0 }};
    const durationSelect = document.getElementById('duration_select');
    const displayDuration = document.getElementById('display_duration');
    const displayTotal = document.getElementById('display_total');
    const hiddenTotal = document.getElementById('hidden_total');
    const paymentForm = document.getElementById('paymentForm');
    const btnSubmit = document.getElementById('btnSubmit');

    // 1. Logika Perhitungan Harga
    if(durationSelect) {
        durationSelect.addEventListener('change', function() {
            const months = parseInt(this.value);
            const total = pricePerMonth * months;
            
            displayDuration.innerText = months >= 12 ? (months/12) + ' Tahun' : months + ' Bulan';
            displayTotal.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            hiddenTotal.value = total; // Update input hidden untuk dikirim ke backend
        });
    }

    // 2. Loading State saat Submit
    paymentForm.addEventListener('submit', function() {
        btnSubmit.classList.add('btn-loading');
        btnSubmit.querySelector('span').style.opacity = '0';
    });

    // 3. Slider Logic (Tetap Seperti Semula)
    function slideImage(direction) {
        const slider = document.getElementById('imageSlider');
        if(!slider) return;
        const scrollAmount = slider.clientWidth;
        slider.scrollBy({ left: direction === 'next' ? scrollAmount : -scrollAmount, behavior: 'smooth' });
    }

    const sliderElem = document.getElementById('imageSlider');
    if(sliderElem) {
        sliderElem.addEventListener('scroll', function() {
            const index = Math.round(this.scrollLeft / this.clientWidth);
            document.querySelectorAll('[id^="dot-"]').forEach((dot, i) => {
                if (i === index) {
                    dot.classList.add('w-6', 'bg-indigo-600');
                    dot.classList.remove('w-1.5', 'bg-slate-200');
                } else {
                    dot.classList.remove('w-6', 'bg-indigo-600');
                    dot.classList.add('w-1.5', 'bg-slate-200');
                }
            });
        });
    }
</script>
@endsection