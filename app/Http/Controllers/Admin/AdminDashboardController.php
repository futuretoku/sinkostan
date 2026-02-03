<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AdminDashboardController extends Controller
{
    public function index(Request $request) {
        $kosts = DB::table('kosts')->get();
        $selectedKostId = $request->get('kost_id');
        $range = $request->get('range', 'bulan'); 

        // --- 1. Logika untuk Data Kamar ---
        $query = DB::table('rooms');
        if ($selectedKostId) {
            $query->where('kost_id', $selectedKostId);
        }

        $totalKamar = (clone $query)->count();
        $kamarTerisi = (clone $query)->where('status', 'occupied')->count();
        $kamarKosong = (clone $query)->where('status', 'available')->count();
        
        $okupansi = $totalKamar > 0 ? round(($kamarTerisi / $totalKamar) * 100) : 0;

        // --- 2. Logika untuk Data Pemasukan ---
        $pemasukanQuery = DB::table('payments')
            ->join('bills', 'payments.bill_id', '=', 'bills.id')
            ->join('bookings', 'bills.booking_id', '=', 'bookings.id')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->where('payments.status', 'confirmed');

        if ($selectedKostId) {
            $pemasukanQuery->where('rooms.kost_id', $selectedKostId);
        }

        // FILTER RANGE WAKTU
        if ($range == 'minggu') {
            $pemasukanQuery->whereBetween('payments.updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($range == 'bulan') {
            $pemasukanQuery->whereMonth('payments.updated_at', Carbon::now()->month)
                           ->whereYear('payments.updated_at', Carbon::now()->year);
        } elseif ($range == 'tahun') {
            $pemasukanQuery->whereYear('payments.updated_at', Carbon::now()->year);
        }

        $pemasukan = $pemasukanQuery->sum('payments.amount');

        return view('admin.dashboard', compact(
            'kosts', 'totalKamar', 'kamarTerisi', 'kamarKosong', 'okupansi', 'selectedKostId', 'pemasukan', 'range'
        ));
    }

    /**
     * FUNGSI BARU: Simpan Kamar Baru (Manajemen Kamar)
     * Menangani upload gambar dan fasilitas
     */
    public function storeRoom(Request $request) {
        // 1. Validasi Input
        $request->validate([
            'kost_id' => 'required',
            'room_number' => 'required',
            'type' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        // 2. Olah Fasilitas (Array Checkbox ke String)
        $facilities = "";
        if ($request->has('facilities')) {
            $facilities = implode(', ', $request->facilities);
        }

        // 3. Olah Gambar (Simpan File ke storage/app/public/rooms)
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('rooms', 'public');
        }

        // 4. Harga Otomatis (Elite vs Standard)
        $price = ($request->type == 'Elite') ? 1500000 : 800000;

        // 5. Insert ke Database
        DB::table('rooms')->insert([
            'kost_id' => $request->kost_id,
            'room_number' => $request->room_number,
            'type' => $request->type,
            'facilities' => $facilities,
            'image' => $imagePath,
            'price' => $price,
            'status' => 'available',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Kamar baru berhasil ditambahkan!');
    }

    public function updateStatus(Request $request) {
        $request->validate([
            'room_id' => 'required',
            'status' => 'required'
        ]);

        DB::table('rooms')->where('id', $request->room_id)->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Status kamar berhasil diperbarui!');
    }
}