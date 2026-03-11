@extends('layouts.app')

@section('content')
<div class="bg-[#F2F4F7] min-h-screen p-8">
    <div class="max-w-6xl mx-auto bg-white rounded-3xl shadow-sm border border-gray-100 p-10">
        
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-bold text-[#101828]">Laporan Hunian & Inventaris</h1>
                <p class="text-[#667085]">Data Real-time Status Kamar dan Penghuni</p>
            </div>
            <button onclick="window.print()" class="bg-gray-800 text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-black transition-all">
                Cetak Laporan
            </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                <p class="text-xs text-gray-500 font-bold uppercase">Total Unit</p>
                <p class="text-xl font-black text-gray-800">{{ $daftarKamar->count() }}</p>
            </div>
            <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100">
                <p class="text-xs text-blue-500 font-bold uppercase">Tersedia</p>
                <p class="text-xl font-black text-blue-800">{{ $kamarTersedia }}</p>
            </div>
            <div class="bg-orange-50 p-4 rounded-2xl border border-orange-100">
                <p class="text-xs text-orange-500 font-bold uppercase">Terisi</p>
                <p class="text-xl font-black text-orange-800">{{ $kamarTerisi }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-2xl border border-green-100">
                <p class="text-xs text-green-500 font-bold uppercase">Okupansi</p>
                <p class="text-xl font-black text-green-800">{{ number_format(($kamarTerisi / $daftarKamar->count()) * 100, 1) }}%</p>
            </div>
        </div>

        <div class="mb-12">
            <h2 class="text-xl font-bold text-[#344054] mb-6 flex items-center">
                <span class="w-2 h-8 bg-blue-500 rounded-full mr-3"></span>
                Manifes Penyewa Aktif
            </h2>
            <div class="overflow-hidden border border-gray-100 rounded-2xl">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase">
                        <tr>
                            <th class="p-4">Identitas Penyewa</th>
                            <th class="p-4">No. Kamar</th>
                            <th class="p-4">Tanggal Masuk</th>
                            <th class="p-4">Masa Sewa</th>
                            <th class="p-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        @foreach($daftarPenyewaAktif as $sewa)
                        <tr class="hover:bg-blue-50/30 transition-all">
                            <td class="p-4 font-bold text-gray-900">{{ $sewa->user->name }}</td>
                            <td class="p-4 font-mono text-blue-600 font-bold">Room {{ $sewa->room->room_number }}</td>
                            <td class="p-4">{{ $sewa->start_date->format('d M Y') }}</td>
                            <td class="p-4 text-xs">
                                <span class="bg-gray-100 px-2 py-1 rounded-md italic">
                                    {{ $sewa->start_date->diffForHumans($sewa->end_date, true) }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="text-[10px] bg-green-100 text-green-700 px-2 py-1 rounded-lg font-black uppercase">
                                    Kontrak Aktif
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <h2 class="text-xl font-bold text-[#344054] mb-6 flex items-center">
                <span class="w-2 h-8 bg-orange-500 rounded-full mr-3"></span>
                Status Inventaris Kamar
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($daftarKamar as $room)
                <div class="p-5 border border-gray-100 rounded-2xl flex justify-between items-center {{ $room->status == 'available' ? 'bg-white' : 'bg-gray-50' }}">
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase">Nomor Unit</p>
                        <p class="text-lg font-black text-gray-800">Kamar {{ $room->room_number }}</p>
                    </div>
                    <div class="text-right text-xs font-bold uppercase">
                        @if($room->status == 'available')
                            <span class="text-green-600 border border-green-200 bg-green-50 px-3 py-1 rounded-full">Tersedia</span>
                        @else
                            <span class="text-orange-600 border border-orange-200 bg-orange-50 px-3 py-1 rounded-full">Terisi</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection