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
        // 1. Laporan Keuangan (Total Pemasukan dari tagihan yang sudah lunas)
        $totalPemasukan = Bill::where('status', 'paid')->sum('amount');

        // 2. Laporan Kamar
        $totalKamar = Room::count();
        $kamarTerisi = Room::where('status', 'occupied')->count();
        $kamarTersedia = Room::where('status', 'available')->count();

        // 3. Laporan Penyewa
        $penyewaAktif = Booking::where('status', 'active')->count();
        $totalRiwayat = Booking::where('status', 'completed')->count();

        return view('admin.laporan', compact(
            'totalPemasukan', 
            'totalKamar', 'kamarTerisi', 'kamarTersedia',
            'penyewaAktif', 'totalRiwayat'
        ));
    }
}