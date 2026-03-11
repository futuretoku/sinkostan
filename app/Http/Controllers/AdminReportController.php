<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Maintenance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    /**
     * Halaman Dashboard Utama Laporan
     */
    public function index()
    {
        $now = Carbon::now();

        // 1. Ringkasan Keuangan
        $totalPemasukan = Bill::where('status', 'paid')->sum('amount');
        
        $pemasukanTerbaru = Bill::with(['booking.user', 'booking.room'])
                                ->where('status', 'paid')
                                ->latest()
                                ->take(5)
                                ->get();

        // 2. Ringkasan Kamar
        $rooms = Room::select('status')->get();
        $totalKamar = $rooms->count();
        $kamarTerisi = $rooms->where('status', 'occupied')->count();
        $kamarTersedia = $rooms->where('status', 'available')->count();
        $daftarKamar = Room::orderBy('room_number')->get();

        // 3. Ringkasan Penyewa Aktif
        $daftarPenyewaAktif = Booking::with(['user', 'room'])
                                     ->where('end_date', '>=', $now)
                                     ->get();
        
        $penyewaAktif = $daftarPenyewaAktif->count();
        $totalRiwayat = Booking::where('end_date', '<', $now)->count();

        return view('admin.laporan', compact(
            'totalPemasukan', 'pemasukanTerbaru',
            'totalKamar', 'kamarTerisi', 'kamarTersedia', 'daftarKamar',
            'penyewaAktif', 'totalRiwayat', 'daftarPenyewaAktif'
        ));
    }

    /**
     * FUNGSI GABUNGAN: Laporan Keuangan (Buku Kas & Grafik)
     * Saya satukan isinya agar route manapun yang dipanggil tidak akan error variable
     */
    public function keuanganDetail(Request $request)
    {
        // 1. Tangkap filter
        $bulan = (int)$request->get('bulan', date('m'));
        $tahun = (int)$request->get('tahun', date('Y'));

        // 2. Ambil Pemasukan
        $pemasukan = Bill::with(['booking.user', 'booking.room'])
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', 'paid')
            ->latest()
            ->get();

        // 3. Ambil Pengeluaran
        $pengeluaran = Maintenance::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        // 4. Proses Buku Kas ($semuaTransaksi) - WAJIB ADA UNTUK VIEW
        $semuaTransaksi = collect();

        foreach ($pemasukan as $p) {
            $semuaTransaksi->push([
                'tanggal'   => $p->created_at,
                'nama'      => $p->booking->user->name ?? 'Penyewa Umum',
                'deskripsi' => 'Pembayaran Sewa Kamar No. ' . ($p->booking->room->room_number ?? '-'),
                'tipe'      => 'pemasukan',
                'jumlah'    => $p->amount
            ]);
        }

        foreach ($pengeluaran as $ex) {
            $semuaTransaksi->push([
                'tanggal'   => $ex->created_at,
                'nama'      => 'Maintenance: ' . ($ex->judul ?? 'Perbaikan'),
                'deskripsi' => $ex->deskripsi ?? 'Biaya operasional/perbaikan',
                'tipe'      => 'pengeluaran',
                'jumlah'    => $ex->biaya ?? 0
            ]);
        }

        $semuaTransaksi = $semuaTransaksi->sortByDesc('tanggal');

        // 5. Summary
        $totalPemasukan = $pemasukan->sum('amount');
        $totalPengeluaran = $pengeluaran->sum('biaya');

        // 6. Grafik
        $grafikLabel = [];
        $grafikData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $grafikLabel[] = $date->translatedFormat('d M'); 
            $grafikData[] = Bill::where('status', 'paid')
                                ->whereDate('created_at', $date)
                                ->sum('amount');
        }

        // Variabel untuk kompatibilitas view (pemasukanTerbaru)
        $pemasukanTerbaru = $pemasukan;

        return view('admin.laporankeuangan', compact(
            'semuaTransaksi', 
            'pemasukanTerbaru',
            'totalPemasukan', 
            'totalPengeluaran', 
            'bulan', 
            'tahun', 
            'grafikLabel', 
            'grafikData'
        ));
    }

    /**
     * Alias fungsi financeDetail (mengarahkan ke keuanganDetail)
     * Biar route kamu yang mengarah ke sini tidak perlu diubah
     */
    public function financeDetail(Request $request)
    {
        return $this->keuanganDetail($request);
    }

    /**
     * Halaman Detail Laporan Kamar & Penyewa
     */
    public function kamarPenyewaDetail()
    {
        $now = Carbon::now();
        $daftarKamar = Room::orderBy('room_number', 'asc')->get();
        $kamarTersedia = $daftarKamar->where('status', 'available')->count();
        $kamarTerisi = $daftarKamar->where('status', 'occupied')->count();

        $daftarPenyewaAktif = Booking::with(['user', 'room'])
                                    ->where('end_date', '>=', $now)
                                    ->orderBy('end_date', 'asc')
                                    ->get();

        return view('admin.laporankamardanpenyewa', compact(
            'daftarKamar', 'kamarTerisi', 'kamarTersedia', 'daftarPenyewaAktif'
        ));
    }
}