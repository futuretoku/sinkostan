<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function index()
    {
        // 1. Mengambil data cabang (kosts) dengan hitungan yang lebih detail
        $dataCabang = DB::table('kosts')
            ->leftJoin('rooms', 'kosts.id', '=', 'rooms.kost_id')
            ->select(
                'kosts.*',
                DB::raw('count(rooms.id) as total_kamar'),
                // Kamar Tersedia: Benar-benar hanya yang statusnya 'available'
                DB::raw('SUM(CASE WHEN rooms.status = "available" THEN 1 ELSE 0 END) as tersedia'),
                // Kamar Terisi (Okupansi): Gabungan antara yang sudah dihuni (occupied) DAN yang sudah dibooking (booked)
                DB::raw('SUM(CASE WHEN rooms.status IN ("occupied", "booked") THEN 1 ELSE 0 END) as terisi_okupansi'),
                DB::raw('MIN(rooms.price) as harga_terendah'),
                DB::raw('MAX(rooms.price) as harga_tertinggi'),

                DB::raw('GROUP_CONCAT(rooms.type SEPARATOR ", ") as room_types')
            )
            ->groupBy('kosts.id')
            ->get();

        // 2. Statistik Global untuk 3 kotak di atas (Biru, Hijau, Kuning)
        // Kita hitung status 'occupied' DAN 'booked' sebagai Kamar Terisi
        $totalKamarTerisi = DB::table('rooms')
            ->whereIn('status', ['occupied', 'booked'])
            ->count();
            
        $totalKamarTersedia = DB::table('rooms')
            ->where('status', 'available')
            ->count();

        // 3. Mengirimkan data ke view
        return view('dashboard', compact('dataCabang', 'totalKamarTerisi', 'totalKamarTersedia'));
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