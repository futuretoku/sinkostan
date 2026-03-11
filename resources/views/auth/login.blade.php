<x-guest-layout>
    <div class="flex flex-col items-center">
        <div class="flex items-center justify-center gap-3 mb-6">
            <div class="bg-indigo-600 w-12 h-12 rounded-full flex items-center justify-center shadow-lg">
                <span class="text-white font-bold text-lg">SKA</span>
            </div>
            <h1 class="text-2xl font-bold text-indigo-700">Sin Kost An</h1>
        </div>

        <h2 class="text-xl font-bold text-gray-700 mb-6">Login</h2>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="w-full">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                <input id="email" 
                       type="email" 
                       name="email" 
                       placeholder="Masukkan username" 
                       class="w-full px-4 py-3 rounded-xl border border-indigo-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-200"
                       value="{{ old('email') }}" 
                       required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <input id="password" 
                       type="password" 
                       name="password" 
                       placeholder="Masukkan password" 
                       class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-200"
                       required />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-md transform active:scale-95 transition duration-150">
                Masuk
            </button>

            <div class="mt-4 text-center">
                @if (Route::has('password.request'))
                    <a class="text-xs text-gray-500 hover:text-indigo-600" href="{{ route('password.request') }}">
                        Lupa password?
                    </a>
                @endif
            </div>

            <div class="mt-4 text-center">
                <span class="text-sm text-gray-500">Belum punya akun?</span>
                <a class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold" href="{{ route('register') }}">
                    Daftar sekarang
                </a>

            </div>
        </form>
    </div>
</x-guest-layout>