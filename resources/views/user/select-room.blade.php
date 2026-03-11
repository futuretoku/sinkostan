@extends('layouts.user')

@section('content')
<style>
    /* Animasi Modal Custom */
    .modal-enter { opacity: 0; transform: scale(0.9); }
    .modal-enter-active { opacity: 1; transform: scale(1); transition: all 0.3s ease-out; }
    
    /* Smooth Scroll & Transitions */
    .room-card { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }

    /* Hide Scrollbar for Slider */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div class="bg-[#f8fafc] min-h-screen p-4 md:p-8 text-slate-800">
    <div class="max-w-6xl mx-auto space-y-6">
        
        <a href="{{ route('dashboard') }}" class="inline-flex items-center bg-[#6366f1] text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-[#4f46e5] transition-all shadow-md mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            <h1 class="text-xl font-bold text-slate-800">Kost {{ $branch->name }}</h1>
            <p class="text-xs text-slate-400 mt-1">{{ $branch->address }}</p>
            
            <div class="mt-8">
                <p class="text-sm font-bold text-slate-700 mb-4">Keterangan Status</p>
                <div class="flex flex-wrap gap-4 text-[11px] font-medium text-slate-600">
                    <div class="flex items-center gap-2"><div class="w-4 h-4 bg-emerald-400 rounded shadow-sm"></div><span>Tersedia</span></div>
                    <div class="flex items-center gap-2"><div class="w-4 h-4 bg-red-500 rounded shadow-sm"></div><span>Terisi</span></div>
                    <div class="flex items-center gap-2"><div class="w-4 h-4 bg-orange-400 rounded shadow-sm"></div><span>Dibooking</span></div>
                    <div class="flex items-center gap-2"><div class="w-4 h-4 bg-slate-400 rounded shadow-sm"></div><span>Maintenance</span></div>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 text-center">
            <h2 class="text-base font-bold text-slate-800">Daftar Kamar</h2>
            <p class="text-[10px] text-slate-400 mb-10 uppercase tracking-widest">
                {{ $rooms->where('status', 'available')->count() }} kamar tersedia
            </p>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-6 max-w-4xl mx-auto">
                @forelse($rooms as $room)
                    @php
                        $dbStatus = strtolower($room->status ?? 'occupied');
                        $statusConfigs = [
                            'available'   => ['bg' => 'bg-emerald-500/60', 'label' => 'Tersedia'],
                            'occupied'    => ['bg' => 'bg-red-600/70',     'label' => 'Terisi'],
                            'booked'      => ['bg' => 'bg-orange-500/70',  'label' => 'Dibooking'],
                            'maintenance' => ['bg' => 'bg-slate-500/80',   'label' => 'Maintenance'],
                        ];

                        $config = $statusConfigs[$dbStatus] ?? $statusConfigs['occupied'];
                        $hargaFormatted = 'Rp ' . number_format($room->price, 0, ',', '.');

                        // Logika Gambar
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
                        
                        // Encode Data untuk JS (Menghubungkan fasilitas dari admin ke modal)
                        $safeImagesData = base64_encode(json_encode($processedImages));
                        $safeFasilitas = base64_encode($room->facilities ?? 'Fasilitas standar');
                    @endphp

                    <div onclick="openModal('{{ $room->id }}', '{{ $room->room_number }}', '{{ $hargaFormatted }}', '{{ $config['label'] }}', '{{ $room->type }}', '{{ $safeImagesData }}', '{{ $safeFasilitas }}')" 
                         class="room-card relative group cursor-pointer overflow-hidden rounded-2xl aspect-[4/3] shadow-md hover:shadow-2xl hover:-translate-y-2 ring-1 ring-slate-100 hover:ring-4 hover:ring-indigo-500/20">
                        
                        <img src="{{ $processedImages[0] }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        
                        <div class="absolute inset-0 {{ $config['bg'] }} flex flex-col items-center justify-center text-white transition-all duration-300 backdrop-blur-[0px] group-hover:backdrop-blur-[2px]">
                            <span class="font-black text-xl leading-tight drop-shadow-lg transition-transform duration-300 group-hover:scale-110">
                                {{ $room->room_number }}
                            </span>
                            <span class="text-[9px] font-bold uppercase tracking-wider mt-1 opacity-90">
                                {{ $dbStatus == 'available' ? $room->type : $config['label'] }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16">
                        <p class="text-slate-400 text-sm italic">Belum ada data kamar.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
    <div class="flex items-center gap-2 mb-2">
        <span class="text-red-500">📍</span>
        <h3 class="text-sm font-bold text-slate-800">Lokasi Kost</h3>
    </div>
    <p class="text-[10px] text-slate-500 mb-4">{{ $branch->address }}</p>
    
    <div class="mt-2 w-full h-80 bg-slate-100 rounded-xl overflow-hidden border border-slate-200 shadow-inner relative">
        <iframe 
            width="100%" 
            height="100%" 
            frameborder="0" 
            style="border:0"
            src="https://www.google.com/maps?q={{ urlencode($branch->address) }}&output=embed" 
            allowfullscreen>
        </iframe>
    </div>
    
    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($branch->address) }}" target="_blank" class="inline-block mt-4 text-[10px] font-bold text-indigo-500 hover:text-indigo-700 transition-colors">
        Buka di Google Maps →
    </a>
</div>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div id="roomModal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0">
    <div class="bg-white w-full max-w-[380px] rounded-[2.5rem] shadow-2xl relative p-6 transform transition-all scale-95 duration-300" id="modalContent">
        <button onclick="closeModal()" class="absolute top-6 right-6 z-20 bg-white/90 backdrop-blur-md hover:bg-gray-100 p-2 rounded-full shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>

        <h2 class="text-xl font-bold text-slate-800 mb-5 px-1">Detail Kamar <span id="modalTitleNumber" class="text-indigo-600"></span></h2>
        
        <div class="relative group overflow-hidden rounded-[1.5rem] mb-6 shadow-md border border-slate-50">
            <div id="modalSlider" class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth no-scrollbar"></div>
            <button onclick="modalSlide('prev')" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/70 backdrop-blur-sm p-1.5 rounded-full shadow-sm z-10"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" /></svg></button>
            <button onclick="modalSlide('next')" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/70 backdrop-blur-sm p-1.5 rounded-full shadow-sm z-10"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg></button>
        </div>

        <div class="space-y-4 mb-8 px-1">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-400 text-[10px] uppercase font-bold tracking-widest">Tipe</p>
                    <p id="modalRoomType" class="font-bold text-slate-700 text-sm">-</p>
                </div>
                <div>
                    <p class="text-gray-400 text-[10px] uppercase font-bold tracking-widest">Harga/Bulan</p>
                    <p id="modalPrice" class="font-black text-indigo-600 text-base">-</p>
                </div>
            </div>

            <div>
                <p class="text-gray-400 text-[10px] uppercase font-bold tracking-widest mb-2">Fasilitas Kamar</p>
                <div id="modalFacilitiesList" class="flex flex-wrap gap-2">
                    </div>
            </div>
        </div>

        <form id="bookingForm" method="GET">
            <button type="submit" id="bookingBtn" class="w-full bg-[#6366f1] text-white font-bold py-4 rounded-2xl shadow-xl active:scale-[0.98] transition-all">
                Booking Kamar Sekarang
            </button>
        </form> 
    </div>
</div>

<script>
    let autoSlideTimer;

    function openModal(id, nomor, harga, status, tipe, safeImagesData, safeFasilitas) {
        const modal = document.getElementById('roomModal');
        const content = document.getElementById('modalContent');
        const btn = document.getElementById('bookingBtn');
        const slider = document.getElementById('modalSlider');
        const facilitiesList = document.getElementById('modalFacilitiesList');
        
        // 1. Render Gambar
        const images = JSON.parse(atob(safeImagesData));
        slider.innerHTML = '';
        images.forEach(src => {
            slider.innerHTML += `<div class="min-w-full snap-center flex-shrink-0"><img src="${src}" class="w-full h-48 object-cover"></div>`;
        });

        // 2. Render Fasilitas (Menghubungkan data dari Admin)
        const fasilitasString = atob(safeFasilitas);
        facilitiesList.innerHTML = '';
        if(fasilitasString && fasilitasString !== 'Fasilitas standar') {
            // Memecah string fasilitas yang dipisahkan koma menjadi badge
            fasilitasString.split(',').forEach(f => {
                if(f.trim() !== "") {
                    facilitiesList.innerHTML += `<span class="bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-full text-[10px] font-bold border border-indigo-100 uppercase tracking-wider">${f.trim()}</span>`;
                }
            });
        } else {
            facilitiesList.innerHTML = '<span class="text-slate-400 text-[11px] italic">Fasilitas standar tersedia</span>';
        }

        // 3. Set Data Text
        document.getElementById('bookingForm').action = "/payment/" + id;
        document.getElementById('modalTitleNumber').innerText = nomor;
        document.getElementById('modalPrice').innerText = harga;
        document.getElementById('modalRoomType').innerText = tipe;

        // 4. Logika Button
        if(status !== 'Tersedia') {
            btn.innerText = 'Kamar ' + status;
            btn.className = "w-full bg-slate-200 text-slate-500 font-bold py-4 rounded-2xl cursor-not-allowed";
            btn.disabled = true;
        } else {
            btn.innerText = 'Booking Kamar Sekarang';
            btn.className = "w-full bg-[#6366f1] hover:bg-[#4f46e5] text-white font-bold py-4 rounded-2xl transition-all shadow-xl shadow-indigo-200";
            btn.disabled = false;
        }

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.add('opacity-100');
            content.classList.replace('scale-95', 'scale-100');
        }, 10);
        startAutoSlide();
    }

    function modalSlide(direction) {
        const slider = document.getElementById('modalSlider');
        const scrollAmount = slider.clientWidth;
        stopAutoSlide();
        if (direction === 'next') {
            if (Math.ceil(slider.scrollLeft + scrollAmount) >= slider.scrollWidth) {
                slider.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                slider.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            }
        } else {
            if (slider.scrollLeft <= 0) {
                slider.scrollTo({ left: slider.scrollWidth, behavior: 'smooth' });
            } else {
                slider.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            }
        }
        startAutoSlide();
    }

    function startAutoSlide() {
        stopAutoSlide();
        autoSlideTimer = setInterval(() => modalSlide('next'), 4000);
    }

    function stopAutoSlide() {
        if(autoSlideTimer) clearInterval(autoSlideTimer);
    }

    function closeModal() {
        const modal = document.getElementById('roomModal');
        const content = document.getElementById('modalContent');
        stopAutoSlide();
        modal.classList.remove('opacity-100');
        content.classList.replace('scale-100', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }
</script>
@endsection