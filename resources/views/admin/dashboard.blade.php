@extends('layouts.app')

@section('content')
{{-- INFO: Tidak perlu tag <main> lagi karena sudah dibungkus di app.blade.php --}}

<div class="space-y-8">
    {{-- SECTION: HEADER & FILTER --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Dashboard Overview</h1>
            <p class="text-sm text-gray-500 font-medium">Manajemen aset dan pendapatan real-time.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            {{-- Dropdown Cabang --}}
            <form action="{{ route('admin.dashboard') }}" method="GET" id="filterDashboard" class="relative group">
                <input type="hidden" name="range" id="rangeInput" value="{{ $range ?? 'bulan' }}">
                <select name="kost_id" onchange="this.form.submit()" 
                        class="appearance-none bg-white border border-gray-200 pl-4 pr-10 py-2.5 rounded-xl font-bold text-sm outline-none shadow-sm hover:border-indigo-300 transition-all cursor-pointer">
                    <option value="">Semua Cabang</option>
                    @foreach($kosts as $k)
                        <option value="{{ $k->id }}" {{ ($selectedKostId ?? '') == $k->id ? 'selected' : '' }}>
                            {{ $k->name }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </form>

            {{-- Toggle Range --}}
            <div class="flex bg-gray-200/60 p-1 rounded-xl backdrop-blur-sm">
                @foreach(['minggu', 'bulan', 'tahun'] as $r)
                    <button type="button" onclick="updateRange('{{ $r }}')" 
                            class="px-4 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all {{ ($range ?? 'bulan') == $r ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ $r }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- SECTION: STATS CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-[1.5rem] shadow-sm border border-gray-100">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Kamar</p>
            <div class="text-2xl font-black text-gray-900">{{ $totalKamar ?? 0 }}</div>
            <div class="mt-2 text-[10px] text-gray-400 font-bold uppercase">Unit Terdaftar</div>
        </div>
        <div class="bg-white p-5 rounded-[1.5rem] shadow-sm border border-gray-100 border-l-4 border-l-indigo-500">
            <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">Kamar Terisi</p>
            <div class="text-2xl font-black text-gray-900">{{ $kamarTerisi ?? 0 }}</div>
            <div class="mt-2 text-[10px] text-indigo-500 font-bold uppercase">{{ $okupansi ?? 0 }}% Okupansi</div>
        </div>
        <div class="bg-white p-5 rounded-[1.5rem] shadow-sm border border-gray-100">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Kamar Kosong</p>
            <div class="text-2xl font-black text-gray-900">{{ $kamarKosong ?? 0 }}</div>
            <div class="mt-2 text-[10px] text-gray-400 font-bold uppercase">Ready to Rent</div>
        </div>
        <div class="bg-green-50/50 p-5 rounded-[1.5rem] shadow-sm border border-green-100">
            <p class="text-[10px] font-black text-green-600 uppercase tracking-widest mb-1">Pemasukan</p>
            <div class="text-xl font-black text-gray-900">Rp {{ number_format($pemasukan ?? 0, 0, ',', '.') }}</div>
            <div class="mt-2 text-[10px] text-green-500 font-bold uppercase">Periode Ini</div>
        </div>
    </div>

    {{-- SECTION: VISUALISASI CABANG --}}
    <div>
        <div class="flex items-center gap-3 mb-6">
            <h2 class="text-sm font-black text-gray-900 uppercase tracking-tighter italic border-l-4 border-indigo-600 pl-3">Cabang Kost Terdaftar</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($kosts as $kost)
            <div class="bg-white rounded-[2rem] overflow-hidden shadow-sm border border-gray-100 group hover:shadow-xl transition-all duration-500">
                <div class="relative h-48 overflow-hidden">
                    @if($kost->image)
                        <img src="{{ asset('uploads/kosts/' . explode(', ', $kost->image)[0]) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    @else
                        <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-400 text-[10px] font-bold uppercase">Foto Tidak Tersedia</div>
                    @endif
                    <div class="absolute top-4 left-4">
                        <span class="bg-indigo-600 text-white px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest shadow-lg">ACTIVE</span>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="font-black text-gray-800 leading-tight">{{ $kost->name }}</h3>
                        <div class="text-right shrink-0">
                            <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">Mulai</p>
                            <p class="text-indigo-600 font-black text-xs">Rp {{ number_format($kost->rooms->min('price') ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-500 italic line-clamp-1 border-t border-gray-50 pt-3">{{ $kost->address }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- SECTION: CHARTS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
            <h3 class="font-black text-gray-400 text-[9px] uppercase tracking-[0.2em] mb-6">Grafik Penghasilan</h3>
            <div class="h-64">
                <canvas id="incomeChart"></canvas>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
            <h3 class="font-black text-gray-400 text-[9px] uppercase tracking-[0.2em] mb-6 text-center">Sumber Pembayaran</h3>
            <div class="h-64 relative">
                <canvas id="paymentMethodChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function updateRange(val) {
        document.getElementById('rangeInput').value = val;
        document.getElementById('filterDashboard').submit();
    }

    document.addEventListener('DOMContentLoaded', () => {
        // INCOME CHART
        const incomeCtx = document.getElementById('incomeChart');
        if (incomeCtx) {
            new Chart(incomeCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($incomeLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun']) !!},
                    datasets: [{
                        label: 'IDR',
                        data: {!! json_encode($incomeValues ?? [0,0,0,0,0,0]) !!},
                        backgroundColor: '#4f46e5',
                        borderRadius: 8,
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });
        }

        // PAYMENT METHOD CHART
        const paymentCtx = document.getElementById('paymentMethodChart');
        if (paymentCtx) {
            new Chart(paymentCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Transfer', 'E-Wallet', 'Tunai'],
                    datasets: [{
                        data: {!! json_encode($paymentMethods ?? [0,0,0]) !!},
                        backgroundColor: ['#4f46e5', '#38bdf8', '#fbbf24'],
                        borderWidth: 4,
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false, 
                    cutout: '75%',
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } } 
                }
            });
        }
    });
</script>
@endsection