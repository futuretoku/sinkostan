<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdminBranchRoomController extends Controller
{
    public function index(Request $request)
    {
        $kosts = Kost::with('rooms')->get();
        $selectedKostId = $request->get('kost_id');
        
        // Menggunakan Left Join agar jika ada data yang kurang tetap tidak error, 
        // tapi Join biasa lebih aman untuk integritas data.
        $query = DB::table('rooms')
                    ->join('kosts', 'rooms.kost_id', '=', 'kosts.id')
                    ->select('rooms.*', 'kosts.name as kost_name');

        if ($selectedKostId) {
            $query->where('rooms.kost_id', $selectedKostId);
        }

        $rooms = $query->get();

        return view('admin.manajemen-kamar', compact('kosts', 'rooms', 'selectedKostId'));
    }

    public function storeKost(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
        ]);

        $imageNames = [];
        if ($request->hasFile('kost_images')) {
            $path = public_path('uploads/kosts');
            if (!File::isDirectory($path)) { File::makeDirectory($path, 0777, true, true); }

            foreach ($request->file('kost_images') as $image) {
                $newName = 'kost_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move($path, $newName);
                $imageNames[] = $newName;
            }
        }

        DB::table('kosts')->insert([
            'name' => $request->name,
            'address' => $request->address,
            'location_link' => $request->location_link,
            'image' => !empty($imageNames) ? implode(', ', $imageNames) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Cabang berhasil ditambah!');
    }

    public function storeRoom(Request $request)
{
    $request->validate([
        'kost_id' => 'required',
        'room_number' => 'required',
        'price' => 'required|numeric',
        'floor' => 'required|numeric',
        'status' => 'required', // Pastikan status ikut divalidasi
    ]);

    // 1. Ambil status langsung dari input form
    // Karena di Blade (tampilan) nilainya sudah kita buat sama dengan database
    // (available, booked, occupied, maintenance), kita tidak perlu konversi manual lagi.
    $finalStatus = $request->status; 

    // 2. Handle Upload Gambar
    $imageNames = [];
    if ($request->hasFile('images')) {
        $path = public_path('uploads/rooms');
        if (!File::isDirectory($path)) { 
            File::makeDirectory($path, 0777, true, true); 
        }

        foreach ($request->file('images') as $image) {
            $newName = 'room_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($path, $newName);
            $imageNames[] = $newName;
        }
    }

    // 3. Simpan ke Database
    DB::table('rooms')->insert([
        'kost_id' => $request->kost_id,
        'room_number' => $request->room_number,
        'price' => $request->price,
        'floor' => $request->floor, // Tambahkan input floor
        'type' => $request->type, 
        'status' => $finalStatus, // SEKARANG SUDAH DINAMIS SESUAI PILIHAN
        'facilities' => $request->has('facilities') ? implode(', ', $request->facilities) : null,
        'image' => !empty($imageNames) ? implode(', ', $imageNames) : null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Unit kamar berhasil disimpan!');
}

public function updateRoom(Request $request, $id)
{
    $request->validate([
        'room_number' => 'required',
        'price'       => 'required|numeric',
        'floor'       => 'required|numeric',
        'status'      => 'required|in:available,booked,occupied,maintenance',
        'type'        => 'required'
    ]);

    try {
        // 1. Ambil data lama untuk cek gambar
        $room = DB::table('rooms')->where('id', $id)->first();
        if (!$room) return redirect()->back()->with('error', 'Kamar tidak ditemukan');

        $data = [
            'room_number' => $request->room_number,
            'price'       => $request->price,
            'floor'       => $request->floor,
            'status'      => $request->status,
            'type'        => $request->type,
            'facilities'  => $request->has('facilities') ? implode(', ', $request->facilities) : null,
            'updated_at'  => now(),
        ];

        // 2. Handle Upload Gambar Baru (Opsional)
        if ($request->hasFile('images')) {
            $imageNames = [];
            $path = public_path('uploads/rooms');
            
            // Buat folder jika belum ada
            if (!File::isDirectory($path)) { 
                File::makeDirectory($path, 0777, true, true); 
            }

            foreach ($request->file('images') as $image) {
                $newName = 'room_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move($path, $newName);
                $imageNames[] = $newName;
            }
            
            // Simpan gambar baru (menggantikan yang lama atau menambah, 
            // di sini kita asumsikan mengganti total jika ada upload baru)
            $data['image'] = implode(', ', $imageNames);
        }

        DB::table('rooms')->where('id', $id)->update($data);

        return redirect()->back()->with('success', 'Data kamar berhasil diperbarui!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
    }
}
}