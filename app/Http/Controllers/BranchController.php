<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function index()
{
    // 1. Ambil Data Cabang (Query kamu yang super lengkap)
    $dataCabang = DB::table('kosts')
        ->leftJoin('rooms', 'kosts.id', '=', 'rooms.kost_id')
        ->select('kosts.*')
        ->selectRaw("COUNT(rooms.id) as total_kamar")
        ->selectRaw("SUM(CASE WHEN rooms.status = 'available' THEN 1 ELSE 0 END) as tersedia")
        ->selectRaw("SUM(CASE WHEN rooms.status = 'occupied' THEN 1 ELSE 0 END) as terisi_okupansi")
        ->selectRaw("GROUP_CONCAT(DISTINCT rooms.type SEPARATOR ', ') as daftar_tipe")
        ->selectRaw("MIN(rooms.price) as harga_terendah")
        ->selectRaw("MAX(rooms.price) as harga_tertinggi")
        ->groupBy('kosts.id')
        ->get();

    // 2. Statistik Global (Buat kotak atas)
    $totalKamarTerisi = DB::table('rooms')->whereIn('status', ['occupied', 'booked'])->count();
    $totalKamarTersedia = DB::table('rooms')->where('status', 'available')->count();

    // Ganti get() menjadi paginate(5)
$notifications = auth()->user()->notifications()->latest()->paginate(5);

// Kirim ke view seperti biasa
return view('dashboard', compact(
    'dataCabang', 
    'totalKamarTerisi', 
    'totalKamarTersedia', 
    'notifications'
));
}



    public function show($id)
    {
        $branch = DB::table('kosts')->where('id', $id)->first();
        if (!$branch) abort(404);

        $rooms = DB::table('rooms')
            ->where('kost_id', $id)
            ->orderBy('floor', 'asc')
            ->orderBy('room_number', 'asc')
            ->get();

        return view('user.select-room', compact('branch', 'rooms'));
    }
}