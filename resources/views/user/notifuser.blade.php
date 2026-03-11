@extends('layouts.user')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-[#4338ca]">Notifikasi Saya</h1>
        <p class="text-slate-500 text-xs">Pemberitahuan terbaru dari Sin Kost An</p>
    </div>

    <div class="space-y-4">
        @forelse ($notifications as $notification)
            @php
                // Kita bongkar paksa JSON-nya di sini agar tidak 'blank'
                $data = json_decode($notification->data, true);
            @endphp

            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex items-start gap-4">
                <div class="mt-1">
                    @if(($data['type'] ?? '') === 'success')
                        <div class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-xs">
                            <i class="fas fa-check"></i>
                        </div>
                    @else
                        <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-xs">
                            <i class="fas fa-info"></i>
                        </div>
                    @endif
                </div>

                <div class="flex-1">
                    <div class="flex justify-between">
                        <h3 class="font-bold text-slate-800 text-sm">{{ $data['title'] ?? 'Info' }}</h3>
                        <span class="text-[9px] text-slate-400 italic">
                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                        {{ $data['message'] ?? 'Tidak ada pesan.' }}
                    </p>
                    
                    @if(isset($data['booking_id']))
                    <div class="mt-2">
                        <a href="{{ route('booking.payment_detail', $data['booking_id']) }}" class="text-[10px] font-bold text-indigo-600 underline">
                            Cek Detail
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-200">
                <p class="text-slate-400 text-sm font-medium italic">Belum ada notifikasi masuk.</p>
            </div>
        @endforelse

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection