@extends('layouts.user')


@section('content')
<style>
    [x-cloak] { display: none !important; }
    .sketsa-card {
        border: 2px solid #e2e8f0;
        border-radius: 2rem;
        background: white;
    }
    /* Animasi halus untuk hover kartu kamar */
    .room-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
</style>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="max-w-md mx-auto py-10 px-4" x-data="{ openEdit: false }">
    
    {{-- Notifikasi Sukses --}}
    @if (session('status') === 'profile-updated')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="fixed top-5 right-5 z-[120] p-4 bg-indigo-600 text-white rounded-2xl shadow-xl flex items-center gap-3">
            <span class="font-bold">✨ Profil Berhasil Diperbarui!</span>
        </div>
    @endif

    {{-- Container Utama --}}
    <div class="sketsa-card overflow-hidden shadow-sm">
        {{-- Header & Bio (Tetap sama seperti sebelumnya) --}}
        <div class="p-8 text-center border-b border-gray-100 bg-slate-50/50">
            <div class="relative inline-block mb-4">
                <img class="h-24 w-24 rounded-full border-4 border-white shadow-md object-cover mx-auto" 
                     src="{{ $user->avatar ? asset('storage/avatars/' . $user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=6366f1&color=fff' }}" 
                     alt="Avatar">
                <button @click="openEdit = true" class="absolute bottom-0 right-0 bg-indigo-600 text-white p-1.5 rounded-full shadow-lg hover:scale-110 transition">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </button>
            </div>

            <div class="space-y-1 mb-4">
                <h3 class="text-xl font-black text-slate-800 tracking-tight">{{ $user->name }}</h3>
                <div class="inline-block px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-[10px] font-bold uppercase tracking-widest">
                    {{ ucfirst($user->role) }} Member
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-dashed border-indigo-200">
                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1 text-left">Bio</p>
                <p class="text-slate-600 text-xs italic leading-relaxed text-left">
                    "{{ $user->description ?? 'Belum ada deskripsi profil.' }}"
                </p>
            </div>
        </div>

        {{-- Detail Informasi (Nama, WA, Email) --}}
        <div class="p-8 space-y-6">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center">👤</div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Lengkap</p>
                    <p class="text-sm font-bold text-slate-700">{{ $user->name }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center">📞</div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">No. WhatsApp</p>
                    <p class="text-sm font-bold text-slate-700">{{ $user->phone ?? '-' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center">📧</div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Email</p>
                    <p class="text-sm font-bold text-slate-700">{{ $user->email }}</p>
                </div>
            </div>
        </div>

        {{-- BAGIAN KAMAR (BISA BANYAK) --}}
        <div class="p-8 pt-0 border-t border-gray-50 mt-4">
            <h4 class="text-sm font-black text-slate-800 mb-4 pt-6 tracking-tight flex items-center gap-2">
                🏠 Kamar Yang Dimiliki:
                <span class="bg-slate-200 text-slate-600 px-2 py-0.5 rounded-md text-[10px]">{{ count($bookings) }}</span>
            </h4>
            
            <div class="space-y-3">
                @forelse($bookings as $booking)
                    <div class="room-card flex items-center justify-between p-4 bg-white border border-slate-200 rounded-2xl transition-all">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-indigo-50 rounded-lg">
                                <span class="font-black text-indigo-600 text-sm">{{ $booking->room->room_number }}</span>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase leading-none">Nomor Kamar</p>
                                <p class="text-xs font-bold text-slate-700">{{ $booking->room->kost->name }}</p>
                            </div>
                        </div>
                        
                        {{-- Tombol Rincian mengarah ke ID Booking/Kamar Spesifik --}}
                        <a href="{{ route('user.my-room', $booking->id) }}" 
                           class="bg-indigo-600 text-white text-[10px] font-black px-4 py-2 rounded-xl shadow-md hover:bg-indigo-700 transition-colors uppercase">
                            Rincian
                        </a>
                    </div>
                @empty
                    <div class="p-6 border-2 border-dashed border-slate-100 rounded-2xl text-center">
                        <p class="text-xs font-bold text-slate-300 uppercase tracking-widest">Tidak ada sewa aktif</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Tombol Edit --}}
    <div class="mt-6">
        <button @click="openEdit = true" class="w-full bg-slate-800 text-white py-4 rounded-2xl font-black text-sm shadow-lg hover:bg-slate-900 active:scale-95 transition-all">
            EDIT PROFIL SAYA
        </button>
    </div>

    {{-- MODAL EDIT PROFIL (Tetap sama fungsinya) --}}
    <div x-show="openEdit" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="openEdit = false"></div>
            <div class="relative bg-white w-full max-w-sm p-8 rounded-[2.5rem] shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-black text-slate-800">Update Data</h2>
                    <button @click="openEdit = false" class="text-slate-400 hover:text-red-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('patch')
                    <div class="flex flex-col items-center p-4 bg-slate-50 rounded-2xl border border-slate-200">
                        <label class="text-[9px] font-black text-indigo-500 uppercase tracking-widest mb-2">Ganti Foto Profil</label>
                        <input type="file" name="avatar" class="text-[10px] file:bg-indigo-600 file:text-white file:border-0 file:rounded-full file:px-4 file:py-1 file:font-bold">
                    </div>
                    <div class="space-y-3 text-left">
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Nama</label>
                            <input type="text" name="name" value="{{ $user->name }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold" required>
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">WhatsApp</label>
                            <input type="text" name="phone" value="{{ $user->phone }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold">
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Email</label>
                            <input type="email" name="email" value="{{ $user->email }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold" required>
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Bio Singkat</label>
                            <textarea name="description" rows="2" class="w-full rounded-xl border-slate-200 bg-slate-50 text-xs font-bold">{{ $user->description }}</textarea>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white font-black py-4 rounded-2xl shadow-lg hover:bg-indigo-700 transition-all uppercase tracking-tighter mt-4">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection