@extends('layouts.user')

@section('content')
<div class="bg-[#f8fafc] min-h-screen p-4 md:p-12">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-2xl font-black text-slate-800 mb-6">Riwayat Sewa Saya</h2>

        <div class="space-y-4">
            @forelse($bookings as $booking)
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-2xl">
                        🏠
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800">{{ $booking->room->branch->name ?? 'Cabang' }}</h3>
                        <p class="text-sm text-slate-400">Kamar {{ $booking->room->room_number }} • {{ $booking->duration_months }} Bulan</p>
                    </div>
                </div>

                <div class="text-center md:text-right">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Status</p>
                    @if($booking->status == 'pending')
                        <span class="bg-amber-100 text-amber-600 px-4 py-1 rounded-full text-[10px] font-black uppercase">Menunggu Verifikasi</span>
                    @elseif($booking->status == 'paid')
                        <span class="bg-emerald-100 text-emerald-600 px-4 py-1 rounded-full text-[10px] font-black uppercase">Lunas</span>
                    @else
                        <span class="bg-slate-100 text-slate-500 px-4 py-1 rounded-full text-[10px] font-black uppercase">Belum Bayar</span>
                    @endif
                </div>

                <a href="{{ route('user.my-room', $booking->id) }}" class="bg-slate-50 hover:bg-slate-100 text-slate-600 px-6 py-2 rounded-xl text-xs font-bold transition-all">
                    Detail
                </a>
            </div>
            @empty
            <div class="text-center py-20 bg-white rounded-[2.5rem] border border-dashed border-slate-200">
                <p class="text-slate-400 font-medium">Belum ada riwayat pemesanan.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection