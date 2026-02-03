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

        <template x-if="openModal">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
                <div @click.away="openModal = null" class="bg-white rounded-[2.5rem] w-full max-w-md p-10 shadow-2xl animate-in fade-in zoom-in duration-300">
                    
                    <div x-show="openModal === 'keuangan'">
                        <h2 class="text-2xl font-bold text-[#101828] mb-6">Detail Keuangan</h2>
                        <div class="space-y-4">
                            <div class="p-4 bg-green-50 rounded-2xl">
                                <p class="text-sm text-green-600 font-medium">Total Pemasukan (Lunas)</p>
                                <p class="text-2xl font-bold text-green-700">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div x-show="openModal === 'kamar'">
                        <h2 class="text-2xl font-bold text-[#101828] mb-6">Status Kamar</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-blue-50 rounded-2xl">
                                <p class="text-sm text-blue-600">Total Kamar</p>
                                <p class="text-2xl font-bold text-blue-700">{{ $totalKamar }}</p>
                            </div>
                            <div class="p-4 bg-orange-50 rounded-2xl">
                                <p class="text-sm text-orange-600">Terisi</p>
                                <p class="text-2xl font-bold text-orange-700">{{ $kamarTerisi }}</p>
                            </div>
                        </div>
                    </div>

                    <div x-show="openModal === 'penyewa'">
                        <h2 class="text-2xl font-bold text-[#101828] mb-6">Data Penyewa</h2>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-4 border-b border-gray-100">
                                <span class="text-[#667085]">Penyewa Aktif</span>
                                <span class="font-bold text-[#101828]">{{ $penyewaAktif }} Orang</span>
                            </div>
                            <div class="flex justify-between items-center p-4 border-b border-gray-100">
                                <span class="text-[#667085]">Riwayat Selesai</span>
                                <span class="font-bold text-[#101828]">{{ $totalRiwayat }} Data</span>
                            </div>
                        </div>
                    </div>

                    <button @click="openModal = null" class="w-full mt-8 bg-[#7C3AED] text-white py-3 rounded-xl font-bold hover:bg-[#6D28D9] transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
