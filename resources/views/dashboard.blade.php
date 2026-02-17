@extends('layouts.user')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-[#4338ca] mb-2">Sin Kost An</h1>
        <p class="text-slate-500 text-sm">Lihat informasi semua cabang dan ketersediaan kamar secara real-time</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-[#2563eb] text-white rounded-[2rem] p-8 shadow-lg relative overflow-hidden">
            <p class="font-semibold opacity-80 text-lg">Total Cabang</p>
            <h2 class="text-7xl font-bold mt-2">{{ $dataCabang->count() }}</h2>
        </div>

        <div class="bg-[#16a34a] text-white rounded-[2rem] p-8 shadow-lg">
            <p class="font-semibold opacity-80 text-lg">Kamar Terisi</p>
            <h2 class="text-7xl font-bold mt-2">{{ $totalKamarTerisi }}</h2> 
        </div>

        <div class="bg-[#ca8a04] text-white rounded-[2rem] p-8 shadow-lg">
            <p class="font-semibold opacity-80 text-lg">Tersedia</p>
            <h2 class="text-7xl font-bold mt-2">{{ $totalKamarTersedia }}</h2> 
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach($dataCabang as $branch)
        <div class="bg-[#e2e8f0] rounded-[3rem] p-4 shadow-sm border border-white/50 hover:transform hover:scale-105 transition-all duration-300">
            
            <div class="relative mb-4">
                @if($branch->image)
                    @php $images = explode(', ', $branch->image); @endphp
                    <img src="{{ asset('uploads/kosts/' . $images[0]) }}" 
                         class="w-full h-64 object-cover rounded-[2.5rem]" alt="{{ $branch->name }}">
                @else
                    <div class="w-full h-64 bg-slate-300 flex items-center justify-center rounded-[2.5rem]">
                        <span class="text-slate-500 font-bold uppercase tracking-tighter italic">Belum Ada Foto</span>
                    </div>
                @endif

                <span class="absolute top-4 right-6 bg-[#16a34a] text-white text-[11px] px-4 py-1.5 rounded-full font-semibold shadow-sm">
                    {{ $branch->rooms_available_count ?? $branch->tersedia }} kamar tersedia
                </span>
            </div>

            <div class="flex gap-2 -mt-12 relative z-10 px-4 mb-4">
                <div class="bg-white/90 backdrop-blur rounded-2xl p-3 flex-1 text-center shadow-md">
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider"> Okupansi</p>
                    @php
                    
    $total = $branch->total_kamar ?? 0;
    $occupied = $branch->terisi_okupansi ?? 0; // Menggunakan alias baru dari controller
    $percent = $total > 0 ? round(($occupied / $total) * 100) : 0;
@endphp
<p class="text-lg font-bold text-indigo-600">{{ $percent }}%</p>
                </div>
                <div class="bg-white/90 backdrop-blur rounded-2xl p-3 flex-1 text-center shadow-md">
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Total Kamar</p>
                    <p class="text-lg font-bold text-slate-700">{{ $branch->rooms_count ?? $branch->total_kamar }}</p>
                </div>
            </div>

            <div class="px-4 pb-4">
                <h3 class="text-xl font-bold text-slate-800">Sin Kost An - {{ $branch->name }}</h3>
                <p class="text-slate-500 text-[11px] mt-1 leading-relaxed">
                    <i class="fas fa-map-marker-alt"></i> {{ Str::limit($branch->address, 60) }}
                </p>

  <div class="flex flex-wrap gap-2 mt-4">
    @if (isset($branch->daftar_tipe) && $branch->daftar_tipe !== null ) 
    
    @foreach (explode(',' , $branch->daftar_tipe) as $roomtype)
        <span class="bg-indigo  text-white text-[9px] font-extrabold px-3 py-1  rounded-lg uppercase shadow-sm ">
            {{$roomtype}}
        </span>
        @endforeach 
    
    @else
    <span class="bg-slate-500  text-slate-200 text-[9px] px-3 py-1 rounded-lg uppercase ">
        STANDARRRR
    </span> 
    @endif 

    @if($branch->description)
    @foreach(explode(',' , $branch->description) as $feature)
    <span class=" bg-[#dbeafe] text-[#4338ca] text-[9px] font-extrabold px-3 py-1 rounded-g uppercase ">
        {{ $feature }}
    </span>
@endforeach
@endif
</div>

                <div class="flex justify-between items-center mt-6">
                    <div class="flex flex-col">
    <span class="text-[10px] text-slate-400 font-bold uppercase">Price Range</span>
    <span class="text-[#4338ca] font-extrabold text-lg">
        @if($branch->harga_terendah )
            RP {{ number_format($branch->harga_terendah/100000, 2) }} rb - {{ number_format ($branch->harga_tertinggi/1000000, 0) }} jt
        @else 
            <span class="text-slate-400 italic text-sm">Belum ada kamar</span>
        @endif
    </span>
</div>
                    <a href="{{ route('branch.show', $branch->id) }}" class="bg-[#4338ca] text-white px-6 py-3 rounded-2xl text-sm font-bold hover:bg-[#3730a3] transition-all shadow-md active:scale-95">
                        Detail
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection