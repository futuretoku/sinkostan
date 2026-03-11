@extends('layouts.user')

@section('content')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<div class="bg-[#fcfdfe] min-h-screen p-4 md:p-8" x-data="{ filter: 'all' }">
    <div class="max-w-4xl mx-auto">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Riwayat Sewa</h2>
            </div>

            <div class="flex bg-slate-100 p-1 rounded-xl w-fit">
                <button @click="filter = 'all'" 
                    :class="filter === 'all' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'"
                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all">
                    Semua
                </button>
                <button @click="filter = 'active'" 
                    :class="filter === 'active' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'"
                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all">
                    Aktif
                </button>
                <button @click="filter = 'expired'" 
                    :class="filter === 'expired' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'"
                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all">
                    Selesai
                </button>
            </div>
        </div>

        <div class="space-y-3">
            @forelse($bookings as $booking)
            @php
                // Logika Penentuan Aktif/Expired
                $isExpired = \Carbon\Carbon::parse($booking->end_date)->isPast();
                $statusFilter = $isExpired ? 'expired' : 'active';
            @endphp

            <div x-show="filter === 'all' || filter === '{{ $statusFilter }}'" 
                 x-transition
                 class="bg-[#e3e6ea] p-4 rounded-2xl border border-slate-100 flex items-center justify-between hover:border-indigo-200 transition-all shadow-sm">
                
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 {{ $isExpired ? 'bg-slate-50' : 'bg-indigo-50' }} rounded-xl flex items-center justify-center text-xl">
                        {{ $isExpired ? '⌛' : '🏠' }}
                    </div>

                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">
                            {{ $booking->room->kost->name ?? 'Cabang Kost' }} - Kamar {{ $booking->room->room_number }}
                        </h3>
                        <p class="text-[11px] text-slate-400 font-medium">
                            {{ \Carbon\Carbon::parse($booking->start_date)->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($booking->end_date)->translatedFormat('d M Y') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <div class="hidden md:block text-right">
                        @if($isExpired)
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter">Sudah Selesai</span>
                        @else
                            <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-tighter">Masih Aktif</span>
                        @endif
                    </div>

                <a href="{{ route('user.my-room.current', $booking->id) }}" 
   class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-600 text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:scale-110 active:scale-95 transition-all duration-300 group/btn">
    <svg class="w-5 h-5 transform group-hover/btn:translate-x-1 transition-transform" 
         fill="none" 
         stroke="currentColor" 
         viewBox="0 0 24 24">
        <path stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="3" 
              d="M9 5l7 7-7 7">
        </path>
    </svg>
</a>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <p class="text-slate-400 text-sm">Tidak ada riwayat sewa.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection