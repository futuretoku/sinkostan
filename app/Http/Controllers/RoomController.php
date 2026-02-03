<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    /**
     * Menampilkan halaman pembayaran untuk kamar tertentu.
     */
    public function showPayment($id)
    {
        // 1. Ambil data dengan Join menggunakan Query Builder
        // Menggabungkan tabel rooms dan kosts untuk mendapatkan nama cabang
        $room = DB::table('rooms')
            ->join('kosts', 'rooms.kost_id', '=', 'kosts.id')
            ->select('rooms.*', 'kosts.name as branch_name')
            ->where('rooms.id', $id)
            ->first();

        // Jika data tidak ditemukan, hentikan proses (404)
        if (!$room) {
            abort(404);
        }

        // 2. LOGIKA PEMROSESAN GAMBAR
        // Mengambil string gambar dari database (misal: "kamar1.jpg, kamar2.jpg")
        $imgString = $room->image ?? '';
        
        // Memecah string berdasarkan koma dan spasi sekaligus (menggunakan regex)
        $rawImages = preg_split('/,\s*/', $imgString, -1, PREG_SPLIT_NO_EMPTY);
        $validImages = [];

        if (!empty($rawImages)) {
            foreach ($rawImages as $img) {
                $imgName = trim($img); // Bersihkan spasi sisa
                
                // Cek apakah file fisik ada di folder public/uploads/rooms/
                if (file_exists(public_path('uploads/rooms/' . $imgName))) {
                    $validImages[] = asset('uploads/rooms/' . $imgName);
                }
            }
        }

        // 3. JIKA TIDAK ADA GAMBAR VALID, BERIKAN PLACEHOLDER
        // Mencegah error di tampilan Blade jika array kosong
        if (empty($validImages)) {
            // Menggunakan placeholder kamar default
            $validImages[] = 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85';
        }

        /**
         * HASIL AKHIR:
         * Kita tempelkan array gambar yang sudah valid ke properti baru bernama 'all_images'
         */
        $room->all_images = $validImages;

        // Kirim data ke view user/payment.blade.php
        return view('user.payment', compact('room'));
    }

    /**
     * Menyimpan data booking dari form pembayaran.
     */
    public function storeBooking(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'room_id'         => 'required',
            'payment_method'  => 'required',
            'duration_months' => 'required|integer|min:1',
            'start_date'      => 'required|date'
        ]);

        // Catatan: Di sini kamu bisa menambahkan logika Insert ke tabel 'bookings' atau 'transactions'
        // Contoh: DB::table('bookings')->insert([...]);

        return "Booking Berhasil disimpan! Kamar ID: " . $request->room_id . 
               " dengan metode: " . $request->payment_method . 
               " selama " . $request->duration_months . " bulan.";
    }
}