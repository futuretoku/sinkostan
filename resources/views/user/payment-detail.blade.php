@extends('layouts.user')

@section('content')
<style>
    /* Animasi loading untuk tombol submit */
    .btn-loading {
        position: relative;
        color: transparent !important;
        pointer-events: none;
    }
    .btn-loading::after {
        content: "";
        position: absolute;
        width: 20px;
        height: 20px;
        top: 50%;
        left: 50%;
        margin: -10px 0 0 -10px;
        border: 3px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 0.8s ease-in-out infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<div class="bg-[#f1f5f9] min-h-screen p-4 md:p-8 flex justify-center items-start">
    <div class="w-full max-w-2xl space-y-6">
        
        {{-- Tombol Kembali --}}
        <a href="{{ route('dashboard') }}" class="inline-flex items-center bg-[#6366f1] text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-[#4f46e5] transition-all mb-2 shadow-lg shadow-indigo-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Dashboard
        </a>

        {{-- 1. Card Detail Pembayaran --}}
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm text-center border border-slate-100">
            <h2 class="text-xl font-bold text-slate-800 mb-1">Detail Pembayaran</h2>
            <p class="text-sm text-slate-400 mb-6">Selesaikan pembayaran sebelum waktu habis</p>

            <div class="bg-[#f0f4ff] rounded-2xl py-6 px-4 border border-indigo-50">
                <p class="text-xs text-slate-500 uppercase tracking-widest font-bold mb-2">Total Pembayaran</p>
                <h1 class="text-3xl font-black text-[#6366f1]">
                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                </h1>
            </div>

            <div class="mt-4">
                <p class="text-xs font-bold text-slate-400">Selesaikan pembayaran dalam</p>
                <div id="countdown" class="text-red-500 font-black text-lg tracking-tighter">23:59:59</div>
            </div>
        </div>

        {{-- 2. Card Instruksi (Dinamis: QRIS atau Transfer) --}}
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center font-black text-indigo-600 shadow-inner">
                    {{ $booking->payment_method == 'ewallet' ? 'QRIS' : 'BANK' }}
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-lg uppercase tracking-tight">
                        {{ $booking->payment_method == 'ewallet' ? 'E-Wallet (QRIS)' : 'Transfer Bank' }}
                    </h3>
                    <p class="text-sm text-slate-400 font-medium">Atas Nama: Sin Kost An</p>
                </div>
            </div>

            <div class="flex flex-col items-center justify-center py-8 border-2 border-dashed border-slate-100 rounded-[2rem] bg-slate-50/30">
                @if($booking->payment_method == 'ewallet')
                    {{-- Tampilan QRIS --}}
                    <p class="text-xs font-bold text-slate-400 mb-4 italic uppercase tracking-widest">Scan QR Code Dibawah Ini :</p>
                    <div class="bg-white p-4 rounded-2xl shadow-xl border border-slate-100">
                        <img src="{{ asset('images/image.jpeg') }}" alt="QRIS" class="w-80 h-80 object-contain" id="qrCode">
                    </div>
                    <button onclick="downloadQR()" class="mt-6 text-indigo-600 text-xs font-black hover:text-indigo-800 flex items-center gap-2">
                        <span>📥</span> Simpan QR ke Galeri
                    </button>
                @else
                    {{-- Tampilan Transfer Bank --}}
                    <p class="text-xs font-bold text-slate-400 mb-3 uppercase tracking-widest">Nomor Rekening :</p>
                    <div class="flex items-center gap-4 bg-white px-6 py-5 rounded-2xl w-11/12 justify-between shadow-sm border border-slate-100">
                        <span id="rekNumber" class="text-2xl font-black text-slate-700 tracking-widest">1234567890</span>
                        <button onclick="copyRek()" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-[10px] font-black shadow-md hover:bg-indigo-700 active:scale-95 transition-all">
                            SALIN
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-5 text-center font-medium">Bisa melalui ATM, Mobile Banking, atau Internet Banking</p>
                @endif
            </div>
        </div>

        {{-- 3. Cara Pembayaran & Form Upload --}}
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 bg-slate-100 rounded-full flex items-center justify-center text-xs">📖</span>
                Cara Pembayaran
            </h3>
            <ol class="text-sm text-slate-500 space-y-3 list-decimal ml-5 font-medium mb-8">
                <li>Masuk ke aplikasi mobile banking / e-wallet kamu</li>
                <li>Pilih menu <b>Transfer</b> atau <b>Scan QRIS</b></li>
                <li>Masukkan nominal <b>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</b></li>
                <li>Selesaikan transaksi dan screenshot bukti bayar</li>
            </ol>

            <div class="border-t border-dashed border-slate-200 pt-8 mt-4">
                <form action="{{ route('bill.pay.store', $booking->id) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <div class="space-y-4">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-[0.2em] text-center mb-4">Upload Bukti Transfer</label>
                        
                        <div class="relative group">
                            <input type="file" name="proof" id="proofInput" class="hidden" accept="image/*" required onchange="previewImage(this)">
                            <label for="proofInput" class="flex flex-col items-center justify-center border-2 border-dashed border-slate-200 rounded-[2rem] p-8 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/50 transition-all group overflow-hidden bg-slate-50/30">
                                
                                {{-- Preview Image --}}
                                <div id="previewContainer" class="hidden w-full flex justify-center">
                                    <img id="imagePreview" src="#" alt="Preview" class="max-h-64 rounded-xl shadow-lg border-2 border-white">
                                </div>

                                {{-- Placeholder --}}
                                <div id="uploadPlaceholder" class="flex flex-col items-center py-4">
                                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-3xl shadow-sm mb-4 group-hover:scale-110 transition-transform">📸</div>
                                    <span id="fileName" class="text-xs font-bold text-slate-400 group-hover:text-indigo-600 transition-colors">Ketuk untuk pilih foto struk / screenshot</span>
                                </div>
                            </label>
                        </div>

                        <button type="submit" id="btnSubmitUpload" class="w-full bg-[#10b981] hover:bg-[#059669] text-white font-black py-5 rounded-[1.5rem] transition-all shadow-xl shadow-emerald-100 active:scale-[0.98] flex items-center justify-center gap-2 mt-6">
                            <span>Konfirmasi & Kirim Bukti</span>
                        </button>
                        
                        <p class="text-[10px] text-slate-400 text-center mt-6 uppercase tracking-[0.15em] font-bold leading-relaxed">
                            Pemesanan Anda akan diverifikasi Admin dalam 1x24 jam
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Countdown Timer (24 Jam)
    function startTimer(duration, display) {
        var timer = duration, hours, minutes, seconds;
        setInterval(function () {
            hours = parseInt(timer / 3600, 10);
            minutes = parseInt((timer % 3600) / 60, 10);
            seconds = parseInt(timer % 60, 10);

            hours = hours < 10 ? "0" + hours : hours;
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = hours + ":" + minutes + ":" + seconds;

            if (--timer < 0) {
                display.textContent = "EXPIRED";
            }
        }, 1000);
    }

    window.onload = function () {
        var display = document.querySelector('#countdown');
        startTimer(86399, display); 
    };

    // 2. Fungsi Salin Rekening
    function copyRek() {
        const rek = document.getElementById('rekNumber').innerText;
        navigator.clipboard.writeText(rek);
        alert('📋 Nomor rekening berhasil disalin!');
    }

    // 3. Fungsi Download QR
    function downloadQR() {
        const img = document.getElementById('qrCode');
        const link = document.createElement('a');
        link.href = img.src;
        link.download = 'QRIS-SinKostAn.png';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // 4. Fungsi Preview Gambar & Update Tampilan Form
    function previewImage(input) {
        const file = input.files[0];
        const fileNameLabel = document.getElementById('fileName');
        const previewContainer = document.getElementById('previewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');

        if (file) {
            const reader = new FileReader();
            fileNameLabel.innerText = "File terpilih: " + file.name;
            fileNameLabel.classList.add('text-indigo-600');
            
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                previewContainer.classList.remove('hidden');
                // Sembunyikan icon kamera bawaan agar fokus ke gambar
                uploadPlaceholder.querySelector('div').classList.add('hidden'); 
            }
            
            reader.readAsDataURL(file);
        }
    }

    // 5. Efek Loading saat Kirim
    document.getElementById('uploadForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmitUpload');
        btn.classList.add('btn-loading');
        btn.querySelector('span').style.opacity = '0';
    });
</script>
@endsection