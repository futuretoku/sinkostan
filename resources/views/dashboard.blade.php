@extends('layouts.user')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" 
     x-data="{ 
        search: '', 
        minPrice: '', 
        maxPrice: '' 
     }">
    
    @if(auth()->check())
    <div 
        x-data="{ show: true }" 
        x-show="show"
        x-init="setTimeout(() => show = false, 8000)"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-[-20px]"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed top-20 right-6 z-[60] bg-white/90 backdrop-blur-sm border border-indigo-100 shadow-lg p-3 rounded-2xl flex items-center gap-3 max-w-xs"
    >
        <div class="bg-indigo-600 text-white w-8 h-8 flex items-center justify-center rounded-full text-xs shrink-0">
            <i class="fas fa-user"></i>
        </div>
        <div class="overflow-hidden">
            <p class="text-[11px] font-bold text-slate-800 leading-tight truncate">Halo, {{ auth()->user()->name }}!</p>
            <p class="text-[10px] text-slate-500 leading-tight">Mau cari kost harga berapa hari ini?</p>
        </div>
        <button @click="show = false" class="text-slate-400 hover:text-slate-600 ml-1">
            <i class="fas fa-times text-[10px]"></i>
        </button>
    </div>
    @endif

    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-[#4338ca] mb-2">Sin Kost An</h1>
        <p class="text-slate-500 text-sm mb-6">Cari berdasarkan fasilitas, lokasi, atau rentang harga.</p>
        
        <div class="max-w-3xl mx-auto px-4">
            {{-- Search Bar --}}
            <div class="flex items-center bg-white rounded-xl shadow-sm border border-slate-200 p-1 focus-within:border-indigo-400 focus-within:ring-4 focus-within:ring-indigo-50 transition-all duration-300 mb-3">
                <div class="pl-4 pr-2 text-slate-400">
                    <i class="fas fa-search text-xs"></i>
                </div>
                <input 
                    type="text" 
                    x-model.debounce.100ms="search"
                    placeholder="Cari lokasi, fasilitas (Smart TV), dll..." 
                    class="w-full py-2 text-sm text-slate-600 outline-none border-none focus:ring-0 bg-transparent placeholder:text-slate-400"
                >
            </div>

            {{-- Filter Price --}}
            <div class="flex flex-wrap md:flex-nowrap gap-3 items-center justify-center">
    <div class="flex items-center bg-white rounded-lg border border-slate-200 px-3 py-1.5 shadow-sm w-full md:w-44">
        <span class="text-[10px] font-bold text-slate-400 mr-2">MIN</span>
        <input type="number" x-model="minPrice" placeholder="Contoh: 500" class="w-full text-xs outline-none border-none focus:ring-0 p-0 text-slate-600">
        <span class="text-[10px] font-bold text-slate-400 ml-1">RB</span>
    </div>

    <div class="text-slate-300 hidden md:block">—</div>

    <div class="flex items-center bg-white rounded-lg border border-slate-200 px-3 py-1.5 shadow-sm w-full md:w-44">
        <span class="text-[10px] font-bold text-slate-400 mr-2">MAX</span>
        <input type="number" step="0.1" x-model="maxPrice" placeholder="Contoh: 2.5" class="w-full text-xs outline-none border-none focus:ring-0 p-0 text-slate-600">
        <span class="text-[10px] font-bold text-slate-400 ml-1">JT</span>
    </div>
    
    <button @click="minPrice = ''; maxPrice = ''; search = ''" class="text-[10px] font-bold text-rose-500 uppercase hover:text-rose-700 transition-colors px-2">
        Reset Filter
    </button>
</div>
        </div>
    </div>

    {{-- Grid Kost --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach($dataCabang as $branch)
        <div 
            x-show="(search === '' || `{{ strtolower($branch->name) }} {{ strtolower($branch->address) }} {{ strtolower($branch->daftar_tipe) }} {{ strtolower($branch->description) }}`.includes(search.toLowerCase().trim())) &&
        (minPrice === '' || {{ $branch->harga_terendah ?? 0 }} >= (minPrice * 1000)) &&
        (maxPrice === '' || {{ $branch->harga_terendah ?? 0 }} <= (maxPrice * 1000000))"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-[#e2e8f0] rounded-[3rem] p-4 shadow-sm border border-white/50 hover:transform hover:scale-105 transition-all duration-300"
        >
            
            <div class="relative mb-4">
                @if($branch->image)
                    @php $images = explode(', ', $branch->image); @endphp
                    <img src="{{ asset('uploads/kosts/' . $images[0]) }}" 
                         class="w-full h-64 object-cover rounded-[2.5rem]" alt="{{ $branch->name }}">
                @else
                    <div class="w-full h-64 bg-slate-300 flex items-center justify-center rounded-[2.5rem]">
                        <span class="text-slate-500 font-bold uppercase tracking-tighter italic text-xs">Belum Ada Foto</span>
                    </div>
                @endif

                <span class="absolute top-4 right-6 bg-[#16a34a] text-white text-[10px] px-3 py-1 rounded-full font-semibold shadow-sm">
                    {{ $branch->tersedia ?? 0 }} kamar tersedia
                </span>
            </div>

            <div class="flex gap-2 -mt-12 relative z-10 px-4 mb-4">
                <div class="bg-white/90 backdrop-blur rounded-2xl p-3 flex-1 text-center shadow-md border border-white/50">
                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Okupansi</p>
                    @php
                        $total = $branch->total_kamar ?? 0;
                        $terisi = $branch->terisi_okupansi ?? 0;
                        $persen = $total > 0 ? round(($terisi / $total) * 100) : 0;
                    @endphp
                    <p class="text-base font-bold text-indigo-600">{{ $persen }}%</p>
                </div>

                <div class="bg-white/90 backdrop-blur rounded-2xl p-3 flex-1 text-center shadow-md border border-white/50">
                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">Total Kamar</p>
                    <p class="text-base font-bold text-slate-700">{{ $branch->total_kamar ?? 0 }}</p>
                </div>
            </div>

            <div class="px-4 pb-4">
                <h3 class="text-lg font-bold text-slate-800 truncate">Sin Kost An - {{ $branch->name }}</h3>
                <p class="text-slate-500 text-[10px] mt-1 leading-relaxed">
                    <i class="fas fa-map-marker-alt"></i> {{ Str::limit($branch->address, 55) }}
                </p>

                <div class="flex flex-wrap gap-1.5 mt-4">
                    @if($branch->daftar_tipe)
                        @foreach(explode(',', $branch->daftar_tipe) as $roomtype)
                            <span class="bg-indigo-600 text-white text-[8px] font-extrabold px-2.5 py-0.5 rounded-md uppercase">
                                {{ trim($roomtype) }}
                            </span>
                        @endforeach
                    @endif

                    @if($branch->description)
                        @foreach(explode(',', $branch->description) as $feature)
                            <span class="bg-blue-100 text-indigo-600 text-[8px] font-extrabold px-2.5 py-0.5 rounded-md uppercase">
                                {{ trim($feature) }}
                            </span>
                        @endforeach
                    @endif
                </div>

                <div class="flex justify-between items-center mt-6">
                    <div class="flex flex-col">
                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">Mulai Dari</span>
                        <span class="text-[#4338ca] font-extrabold text-base">
                            @if($branch->harga_terendah)
                                @if($branch->harga_terendah >= 1000000)
                                    Rp {{ number_format($branch->harga_terendah / 1000000, 1, ',', '.') }} jt
                                @else
                                    Rp {{ number_format($branch->harga_terendah / 1000, 0, ',', '.') }} rb
                                @endif
                            @else 
                                <span class="text-slate-400 italic text-xs">N/A</span>
                            @endif
                        </span>
                    </div>
                    <a href="{{ route('branch.show', $branch->id) }}" class="bg-[#4338ca] text-white px-5 py-2.5 rounded-xl text-xs font-bold hover:bg-[#3730a3] transition-all shadow-md active:scale-95">
                        Detail
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection