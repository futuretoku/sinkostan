<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function index()
{
    $now = \Carbon\Carbon::now();

    // 1. Laporan Keuangan
    $totalPemasukan = \App\Models\Bill::where('status', 'paid')->sum('amount');
    $pemasukanTerbaru = \App\Models\Bill::with('booking.user')->where('status', 'paid')->latest()->take(5)->get();

    // 2. Laporan Kamar
    $totalKamar = \App\Models\Room::count();
    $kamarTerisi = \App\Models\Room::where('status', 'occupied')->count();
    $kamarTersedia = \App\Models\Room::where('status', 'available')->count();
    $daftarKamar = \App\Models\Room::all();

    // 3. Laporan Penyewa (SINKRON DENGAN MANAJEMEN PENYEWA)
    // Ambil semua booking yang belum melewati tanggal end_date
    $queryAktif = \App\Models\Booking::where('end_date', '>=', $now);
    
    $penyewaAktif = $queryAktif->count();
    $totalRiwayat = \App\Models\Booking::where('end_date', '<', $now)->count();

    // Ambil data untuk list di modal
    $daftarPenyewaAktif = \App\Models\Booking::with(['user', 'room'])
                                 ->where('end_date', '>=', $now)
                                 ->get();

    return view('admin.laporan', compact(
        'totalPemasukan', 'pemasukanTerbaru',
        'totalKamar', 'kamarTerisi', 'kamarTersedia', 'daftarKamar',
        'penyewaAktif', 'totalRiwayat', 'daftarPenyewaAktif'
    ));
}
}