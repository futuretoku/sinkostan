<x-app-layout>
    <div class="min-h-screen bg-[#F2F4F7] p-8" x-data="{ openModal: null }">
        <a href="{{ route('admin.dashboard') }}" class="inline-block bg-[#7C3AED] text-white px-6 py-2 rounded-lg font-medium shadow-sm hover:bg-[#6D28D9] mb-8">
            Kembali
        </a>

        <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
            <h1 class="text-2xl font-bold text-[#344054] mb-8">Laporan</h1>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div @click="openModal = 'keuangan'" class="cursor-pointer bg-white border border-gray-100 rounded-3xl p-8 shadow-sm hover:shadow-md transition-all group">
                    <h3 class="text-xl font-bold text-[#101828] mb-2">Laporan Keuangan</h3>
                    <p class="text-[#667085]">Ringkasan pemasukan bulanan</p>
                </div>

                <div @click="openModal = 'kamar'" class="cursor-pointer bg-white border border-gray-100 rounded-3xl p-8 shadow-sm hover:shadow-md transition-all group">
                    <h3 class="text-xl font-bold text-[#101828] mb-2">Laporan Kamar</h3>
                    <p class="text-[#667085]">Status kamar</p>
                </div>

                <div @click="openModal = 'penyewa'" class="cursor-pointer bg-white border border-gray-100 rounded-3xl p-8 shadow-sm hover:shadow-md transition-all group">
                    <h3 class="text-xl font-bold text-[#101828] mb-2">Laporan Penyewa</h3>
                    <p class="text-[#667085]">Data penyewa aktif & riwayat</p>
                </div>
            </div>
        </div>

        <div 
    x-show="openModal" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
    style="display: none;" 
>
    <div 
        @click.away="openModal = null" 
        class="bg-white rounded-[2.5rem] w-full max-w-md p-10 shadow-2xl"
    >
        
        <div x-show="openModal === 'keuangan'">
            <h2 class="text-2xl font-bold text-[#101828] mb-6">Detail Keuangan</h2>
            <div class="space-y-4">
                <div class="p-4 bg-green-50 rounded-2xl mb-4">
                    <p class="text-sm text-green-600 font-medium">Total Pemasukan</p>
                    <p class="text-2xl font-bold text-green-700">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
                </div>
                
                <p class="text-sm font-bold text-gray-500 uppercase">5 Transaksi Terakhir</p>
                <div class="divide-y divide-gray-100 max-h-40 overflow-y-auto">
                    @forelse($pemasukanTerbaru as $bill)
                    <div class="py-3 flex justify-between">
                        <span class="text-gray-600">{{ $bill->booking->user->name ?? 'User' }}</span>
                        <span class="font-semibold text-gray-800">Rp {{ number_format($bill->amount, 0, ',', '.') }}</span>
                    </div>
                    @empty
                    <p class="text-gray-400 text-sm py-2">Belum ada transaksi.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div x-show="openModal === 'kamar'">
            <h2 class="text-2xl font-bold text-[#101828] mb-6">Status Detail Kamar</h2>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="p-4 bg-blue-50 rounded-2xl">
                    <p class="text-xs text-blue-600 font-semibold uppercase">Tersedia</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $kamarTersedia }}</p>
                </div>
                <div class="p-4 bg-orange-50 rounded-2xl">
                    <p class="text-xs text-orange-600 font-semibold uppercase">Terisi</p>
                    <p class="text-2xl font-bold text-orange-700">{{ $kamarTerisi }}</p>
                </div>
            </div>

            <p class="text-sm font-bold text-gray-500 mb-3 uppercase">Daftar Kamar</p>
            <div class="max-h-[250px] overflow-y-auto pr-2 space-y-2">
                @foreach($daftarKamar as $room)
                <div class="flex justify-between items-center p-3 border border-gray-100 rounded-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold {{ $room->status == 'available' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600' }}">
                            {{ $room->room_number }}
                        </div>
                        <span class="font-bold text-gray-800 text-sm">Kamar {{ $room->room_number }}</span>
                    </div>
                    <span class="px-2 py-1 rounded-full text-[9px] font-bold uppercase {{ $room->status == 'available' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                        {{ $room->status == 'available' ? 'Kosong' : 'Isi' }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <div x-show="openModal === 'penyewa'">
    <h2 class="text-2xl font-bold text-[#101828] mb-6">Penyewa Aktif</h2>
    <div class="max-h-[300px] overflow-y-auto space-y-3">
        @forelse($daftarPenyewaAktif as $sewa)
        <div class="p-4 border border-gray-100 rounded-2xl flex justify-between items-center">
            <div>
                <p class="font-bold text-[#101828] text-sm">{{ $sewa->user->name ?? 'User N/A' }}</p>
                <p class="text-xs text-gray-500">
                    Kamar: {{ $sewa->room->room_number ?? '-' }} 
                    <span class="ml-2 text-gray-400">| Selesai: {{ \Carbon\Carbon::parse($sewa->end_date)->format('d M Y') }}</span>
                </p>
            </div>
            <span class="bg-green-100 text-green-700 text-[10px] px-2 py-1 rounded-full font-bold">AKTIF</span>
        </div>
        @empty
        <div class="text-center py-10">
            <p class="text-gray-400 italic text-sm">Tidak ada penyewa yang masa sewanya masih aktif.</p>
        </div>
        @endforelse
    </div>
</div>

        <button @click="openModal = null" class="w-full mt-8 bg-[#7C3AED] text-white py-3 rounded-xl font-bold hover:bg-[#6D28D9] transition-colors">
            Tutup
        </button>
    </div>
</div>
    </div>
</x-app-layout>
