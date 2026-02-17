@vite(['resources/css/app.css', 'resources/js/app.js'])
<div class="max-w-4xl mx-auto p-6 bg-white rounded-2xl shadow-lg mt-10">
    <h1 class="text-2xl font-bold mb-6">Kamar Saya</h1>

    @if(!$myRoom)
        <div class="text-center py-10">
            <p class="text-gray-500">Anda belum memiliki pesanan kamar aktif.</p>
        </div>
    @else
    <div class="flex flex-col md:flex-row gap-8">
        <div class="flex-1">
            <img src="{{ $myRoom->display_image }}" class="w-full h-64 object-cover rounded-xl mb-4" alt="Foto Kamar">
            
            <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                @if($nextBill)
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-500">Status Tagihan</span>
                    <span class="text-sm font-medium text-red-500">Jatuh tempo: {{ $nextBill->due_date_formatted }}</span>
                </div>
                
                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold uppercase">
                    {{ $nextBill->status }}
                </span>

                <div class="grid grid-cols-2 mt-6">
                    <div>
                        <p class="text-gray-400 text-sm">Periode Kost</p>
                        <p class="font-semibold text-xs md:text-sm">{{ $nextBill->periode_start }} - {{ $nextBill->periode_end }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-400 text-sm">Total Tagihan</p>
                        <p class="font-bold text-blue-600 text-xl">Rp {{ number_format($nextBill->amount, 0, ',', '.') }}</p>
                    </div>
                </div>

                <a href="#" class="block w-full text-center bg-indigo-600 text-white font-bold py-3 rounded-xl mt-6 hover:bg-indigo-700 transition">
                    Bayar Tagihan
                </a>
                @else
                <div class="text-center p-4">
                    <span class="text-green-600 font-bold">✅ Semua tagihan sudah lunas!</span>
                </div>
                @endif
            </div>
        </div>

        <div class="w-full md:w-1/3">
            <div class="mb-4">
                <h2 class="text-xl font-bold">{{ $myRoom->kost_name }} - Lantai {{ $myRoom->floor }}</h2>
                <p class="text-gray-500">Kamar No. {{ $myRoom->room_number }} • Tipe {{ $myRoom->type }}</p>
            </div>

            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold uppercase">
                {{ $myRoom->status }}
            </span>

            <div class="grid grid-cols-2 gap-4 mt-6 border-t pt-4">
                <div>
                    <p class="text-gray-400 text-xs uppercase">Nomor Kamar</p>
                    <p class="font-bold text-gray-700">{{ $myRoom->room_number }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs uppercase">Lantai</p>
                    <p class="font-bold text-gray-700">{{ $myRoom->floor }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs uppercase">Harga / Bulan</p>
                    <p class="font-bold text-blue-600 text-sm">Rp {{ number_format($myRoom->price, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs uppercase">Tipe</p>
                    <p class="font-bold text-gray-700">{{ $myRoom->type }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 mt-4">
                @foreach(explode(',', $myRoom->facilities) as $facility)
                    <span class="bg-indigo-50 text-indigo-600 px-2 py-1 rounded-md text-[10px] font-medium uppercase">{{ trim($facility) }}</span>
                @endforeach
            </div>

            <div class="mt-8 bg-blue-50 p-4 rounded-xl">
                <h3 class="font-bold text-gray-700 mb-3">Masa Ngekost</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Mulai</span>
                        <span class="font-semibold text-blue-800">{{ $myRoom->start_date_formatted }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Berakhir</span>
                        <span class="font-semibold text-blue-800">{{ $myRoom->end_date_formatted }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-2 mt-2">
                        <span class="text-gray-500">Durasi Sewa</span>
                        <span class="font-bold text-blue-600">{{ $myRoom->duration_months }} Bulan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>