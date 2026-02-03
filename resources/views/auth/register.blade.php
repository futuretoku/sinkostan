<x-guest-layout>
    <div class="flex flex-col items-center">
        <div class="flex items-center justify-center gap-3 mb-6">
            <div class="bg-indigo-600 w-12 h-12 rounded-full flex items-center justify-center shadow-lg">
                <span class="text-white font-bold text-lg">SKA</span>
            </div>
            <h1 class="text-2xl font-bold text-indigo-700">Sin Kost An</h1>
        </div>

        <h2 class="text-xl font-bold text-gray-700 mb-6">Daftar Akun</h2>

        {{-- Menampilkan Error --}}
        @if ($errors->any())
            <div class="w-full bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="w-full">
            @csrf

            {{-- Nama --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama" class="w-full px-4 py-3 rounded-xl border border-indigo-200 focus:ring-indigo-200 focus:border-indigo-500" required autofocus />
            </div>

            {{-- Nomor WhatsApp --}}
            <div class="mb-4">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon</label>
    <input type="text" name="phone" placeholder="0812..." class="w-full px-4 py-3 rounded-xl border border-indigo-200" required />
</div>

            {{-- Email --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email" class="w-full px-4 py-3 rounded-xl border border-indigo-200 focus:ring-indigo-200 focus:border-indigo-500" required />
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <input id="password" type="password" name="password" placeholder="Buat password" class="w-full px-4 py-3 rounded-xl border border-indigo-200 focus:ring-indigo-200 focus:border-indigo-500" required autocomplete="new-password" />
            </div>

            {{-- Konfirmasi Password (INI YANG TADI KURANG) --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Konfirmasi password" class="w-full px-4 py-3 rounded-xl border border-indigo-200 focus:ring-indigo-200 focus:border-indigo-500" required autocomplete="new-password" />
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-md transition duration-150 transform active:scale-95">
                Daftar Sekarang
            </button>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    Sudah punya akun? 
                    <a class="font-bold text-indigo-600 hover:text-indigo-800 underline transition" href="{{ route('login') }}">
                        Login di sini
                    </a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>