<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sin Kost An</title>
    @vite('resources/css/app.css')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#f3f4f6] text-slate-800" x-data="{ open: false }">

    <div class="relative min-h-screen flex flex-col">
        
        <header class="flex justify-between items-center px-8 py-4 bg-transparent">
            <div class="flex items-center gap-2">
                <div class="bg-indigo-700 p-2 rounded-lg text-white font-bold text-xs">SKA</div>
                <span class="text-indigo-900 font-bold">Sin Kost An</span>
            </div>
            
            <button @click="open = !open" class="p-2 text-slate-600 hover:bg-slate-200 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>
        </header>

        <main class="flex-1 px-8 pb-12">
            @yield('content')
        </main>

        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="fixed inset-y-0 right-0 w-72 bg-white shadow-2xl z-50 p-6 border-l border-slate-100">
            
            <div class="flex justify-between items-center mb-8">
    <a href="{{ route('user.notifications') }}" class="relative group">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400 group-hover:text-indigo-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        {{-- Gunakan cara Laravel ini, otomatis mencari ke notifiable_id & read_at --}}
        @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full border-2 border-white font-bold">
                {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
            </span>
        @endif
    </a>
    
    <button @click="open = false" class="text-slate-400 hover:text-red-500 text-sm font-medium transition">
        &times; Tutup
    </button>
</div>

            <a href="{{ route('profile.index') }}" class="flex items-center gap-3 mb-10 border-b pb-6 hover:bg-slate-50 transition-all duration-200 cursor-pointer">
    @if(Auth::user()->avatar && Auth::user()->avatar !== 'avatar.png')
        <img src="{{ asset('storage/avatars/'.Auth::user()->avatar) }}" class="w-10 h-10 rounded-full object-cover border border-slate-200">
    @else
        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-bold text-sm">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
    @endif
    
    <div class="flex flex-col">
        <span class="text-sm font-bold text-slate-700">{{ Auth::user()->name }}</span>
        <span class="text-[10px] text-slate-400">Penyewa Aktif</span>
    </div>
</a>

            <nav class="space-y-6">
                <a href="/dashboard" class="block text-slate-700 font-medium hover:text-indigo-600 transition">Daftar Kost</a>
                <a href="{{ route('booking.history') }}" class="block text-slate-700 font-medium hover:text-indigo-600 transition">Kamar Saya</a>
                <a href="{{ route('user.maintenance.create') }}" class="block text-slate-700 font-medium hover:text-indigo-600 transition">Keluhan</a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left text-red-500 font-medium hover:text-red-700 transition">Keluar</button>
                </form>
            </nav>
        </div>

        <div x-show="open" @click="open = false" class="fixed inset-0 bg-black/20 z-40" x-cloak></div>
    </div>

</body>
</html>