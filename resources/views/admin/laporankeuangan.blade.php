@extends('layouts.app')

@section('content')
<div class="bg-[#F9FAFB] min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    
    {{-- FILTER SECTION --}}
    <div class="max-w-6xl mx-auto mb-6 no-print">
        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-gray-100">
            <form action="{{ route('admin.laporankeuangan-detail') }}" method="GET" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Periode Bulan</label>
                    <select name="bulan" class="w-full bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 outline-none">
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $bulan == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Tahun</label>
                    <select name="tahun" class="w-full bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 outline-none">
                        @for($y=date('Y'); $y>=date('Y')-3; $y--)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-bold text-sm transition-all shadow-lg shadow-indigo-100">
                    Filter Data
                </button>
                <a href="{{ route('admin.laporankeuangan-detail') }}" class="bg-gray-100 text-gray-600 px-6 py-3 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">
                    Reset
                </a>
            </form>
        </div>
    </div>

    {{-- MAIN REPORT CARD --}}
    <div class="max-w-6xl mx-auto bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden print:shadow-none print:border-none">
        
        <div class="p-10 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6 bg-white">
            <div class="text-center md:text-left">
                <h1 class="text-4xl font-black text-gray-900 tracking-tight uppercase italic">Laporan Jurnal Keuangan</h1>
                <div class="flex items-center justify-center md:justify-start mt-2 text-indigo-600 font-bold">
                    <span class="bg-indigo-50 px-4 py-1 rounded-full text-sm">
                        Periode: {{ Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
                    </span>
                </div>
            </div>
            <div class="no-print">
                <button onclick="window.print()" class="group flex items-center bg-gray-900 text-white px-6 py-3 rounded-2xl font-bold text-sm hover:scale-105 transition-all shadow-xl">
                    <svg class="w-5 h-5 mr-2 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Laporan Akuntansi
                </button>
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="px-10 py-10 bg-gray-50/30 border-b border-gray-100">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="p-6 bg-white rounded-3xl border border-green-100 shadow-sm">
                    <p class="text-[10px] font-black text-green-600 uppercase tracking-widest mb-1">Total Pemasukan (Debit)</p>
                    <p class="text-2xl font-black text-gray-900">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
                </div>
                <div class="p-6 bg-white rounded-3xl border border-red-100 shadow-sm">
                    <p class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-1">Total Pengeluaran (Kredit)</p>
                    <p class="text-2xl font-black text-gray-900">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                </div>
                <div class="p-6 {{ ($totalPemasukan - $totalPengeluaran) >= 0 ? 'bg-indigo-600' : 'bg-red-600' }} rounded-3xl shadow-xl">
                    <p class="text-[10px] font-black text-white/70 uppercase tracking-widest mb-1">Saldo Akhir (Net Profit)</p>
                    <p class="text-2xl font-black text-white">Rp {{ number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- TRANSACTIONS TABLE --}}
        <div class="p-10">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center">
                    <span class="w-12 h-12 bg-gray-900 text-white rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </span>
                    <div>
                        <h2 class="text-xl font-black text-gray-900 uppercase">Buku Besar Transaksi</h2>
                        <p class="text-sm text-gray-400 font-medium">Rincian arus kas masuk dan keluar secara kronologis</p>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-[2rem] border border-gray-100 shadow-sm">
                <table class="w-full text-left">
                    <thead class="bg-gray-900 text-white text-[10px] uppercase tracking-[0.2em] font-bold">
                        <tr>
                            <th class="px-8 py-5">Tanggal</th>
                            <th class="px-8 py-5">Kategori & Deskripsi</th>
                            <th class="px-8 py-5 text-center">Tipe</th>
                            <th class="px-8 py-5 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        {{-- Logika Gabungan Pemasukan & Pengeluaran --}}
                        @forelse($semuaTransaksi as $trans)
                        <tr class="hover:bg-gray-50 transition-all group">
                            <td class="px-8 py-5 text-xs text-gray-500 font-bold">
                                {{ $trans['tanggal']->translatedFormat('d F Y') }}
                                <br><span class="text-[9px] font-normal text-gray-400">{{ $trans['tanggal']->format('H:i') }} WIB</span>
                            </td>
                            <td class="px-8 py-5">
                                <p class="text-sm font-black text-gray-800">{{ $trans['nama'] }}</p>
                                <p class="text-xs text-gray-500 italic">{{ $trans['deskripsi'] }}</p>
                            </td>
                            <td class="px-8 py-5 text-center">
                                @if($trans['tipe'] == 'pemasukan')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-[9px] font-black uppercase rounded-full border border-green-200">MASUK</span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-700 text-[9px] font-black uppercase rounded-full border border-red-200">KELUAR</span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-right">
                                <span class="text-sm font-black {{ $trans['tipe'] == 'pemasukan' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $trans['tipe'] == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($trans['jumlah'], 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center">
                                <p class="text-gray-400 font-bold italic text-sm">Data tidak ditemukan untuk periode ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- FOOTER SIGNATURE --}}
        <div class="px-10 py-10 bg-gray-50 flex flex-col md:flex-row justify-between items-center border-t border-gray-100">
            <div class="mb-8 md:mb-0 text-center md:text-left">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Audit Trace</p>
                <div class="text-[9px] text-gray-400 space-y-1">
                    <p>Doc ID: SKA/TRX/{{ $tahun }}/{{ $bulan }}/{{ strtoupper(Str::random(5)) }}</p>
                    <p>Petugas: {{ auth()->user()->name }}</p>
                </div>
            </div>
            <div class="text-center w-64">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-16">Otoritas Pengelola Kost</p>
                <div class="border-b-2 border-gray-900 w-full mb-2"></div>
                <p class="text-xs font-black text-gray-900 uppercase tracking-widest italic">Verified Account</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('financialChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($grafikLabel),
            datasets: [{
                label: 'Arus Kas',
                data: @json($grafikData),
                borderColor: '#4f46e5',
                borderWidth: 3,
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { font: { size: 9 } } },
                x: { ticks: { font: { size: 9 } } }
            }
        }
    });
</script>
@endsection