<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    /**
     * Menampilkan katalog kamar untuk User
     * Mengurutkan berdasarkan ketersediaan (Tersedia paling atas)
     */
    public function index() 
    {
        // FIELD status digunakan agar urutan tampilan di user logis (Available dulu)
        $rooms = Room::orderByRaw("FIELD(status, 'available', 'booked', 'occupied', 'maintenance')")->get(); 
        return view('user.rooms.index', compact('rooms'));
    }

    /**
     * Menampilkan halaman "Kamar Saya" untuk penyewa yang login
     */
    public function myRoom()
    {
        $userId = Auth::id();

        // Mengambil data booking yang sedang berjalan
        $myRoom = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->join('kosts', 'rooms.kost_id', '=', 'kosts.id')
            ->select(
                'bookings.*', 
                'rooms.room_number', 
                'rooms.floor', 
                'rooms.type', 
                'rooms.price', 
                'rooms.facilities', 
                'rooms.image as room_image',
                'kosts.name as kost_name'
            )
            ->where('bookings.user_id', $userId)
            ->whereIn('bookings.status', ['paid', 'booked', 'success'])
            ->first();

        if (!$myRoom) {
            return view('user.my-room', ['myRoom' => null]);
        }

        // Ambil data tagihan terdekat yang belum dibayar
        $nextBill = DB::table('bills')
            ->where('booking_id', $myRoom->id)
            ->where('status', 'unpaid')
            ->orderBy('due_date', 'asc')
            ->first();

        // Pengolahan Gambar (Ambil gambar pertama dari string CSV)
        $imgString = $myRoom->room_image ?? '';
        $rawImages = preg_split('/,\s*/', $imgString, -1, PREG_SPLIT_NO_EMPTY);
        $displayImage = 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85'; // Placeholder

        if (!empty($rawImages)) {
            $firstImg = trim($rawImages[0]);
            if (file_exists(public_path('uploads/rooms/' . $firstImg))) {
                $displayImage = asset('uploads/rooms/' . $firstImg);
            }
        }
        $myRoom->display_image = $displayImage;

        // Format Tanggal Tagihan
        if ($nextBill) {
            $nextBill->due_date_formatted = Carbon::parse($nextBill->due_date)->translatedFormat('d F Y');
            $nextBill->periode_start = Carbon::parse($nextBill->due_date)->startOfMonth()->translatedFormat('d F Y');
            $nextBill->periode_end = Carbon::parse($nextBill->due_date)->endOfMonth()->translatedFormat('d F Y');
        }

        // Format Tanggal Sewa
        $myRoom->start_date_formatted = Carbon::parse($myRoom->start_date)->translatedFormat('d F Y');
        $myRoom->end_date_formatted = Carbon::parse($myRoom->end_date)->translatedFormat('d F Y');
        
        // Hitung Sisa Waktu Kost
        $diff = Carbon::now()->diffInMonths(Carbon::parse($myRoom->end_date), false);
        $myRoom->remaining_months = $diff > 0 ? $diff . ' Bulan' : 'Hampir Habis';

        return view('user.my-room', compact('myRoom', 'nextBill'));
    }

    /**
     * Menampilkan halaman rincian pembayaran sebelum booking
     */
    public function showPayment($id)
    {
        $room = DB::table('rooms')
            ->join('kosts', 'rooms.kost_id', '=', 'kosts.id')
            ->select('rooms.*', 'kosts.name as branch_name')
            ->where('rooms.id', $id)
            ->first();

        if (!$room) abort(404);

        // Proteksi: Hanya kamar status 'available' yang boleh dibooking
        if ($room->status !== 'available') {
            return redirect()->back()->with('error', 'Maaf, kamar ini sudah tidak tersedia.');
        }

        // Olah Gambar untuk Galeri Kecil
        $imgString = $room->image ?? '';
        $rawImages = preg_split('/,\s*/', $imgString, -1, PREG_SPLIT_NO_EMPTY);
        $validImages = [];

        foreach ($rawImages as $img) {
            $imgName = trim($img);
            if (file_exists(public_path('uploads/rooms/' . $imgName))) {
                $validImages[] = asset('uploads/rooms/' . $imgName);
            }
        }

        $room->all_images = !empty($validImages) ? $validImages : ['https://images.unsplash.com/photo-1505693416388-ac5ce068fe85'];
        
        return view('user.payment', compact('room'));
    }

    /**
     * Proses Simpan Booking & Sinkronisasi Otomatis Status Kamar
     */
    public function storeBooking(Request $request)
    {
        $request->validate([
            'room_id'         => 'required|exists:rooms,id',
            'payment_method'  => 'required',
            'duration_months' => 'required|integer|min:1',
            'start_date'      => 'required|date|after_or_equal:today'
        ]);

        $room = DB::table('rooms')->where('id', $request->room_id)->first();
        
        // Cek ulang ketersediaan (menghindari tabrakan transaksi)
        if ($room->status !== 'available') {
            return redirect()->route('user.rooms.index')->with('error', 'Kamar baru saja dipesan orang lain.');
        }

        DB::transaction(function () use ($request, $room) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = $startDate->copy()->addMonths($request->duration_months);

            // 1. Simpan data booking
            $bookingId = DB::table('bookings')->insertGetId([
                'user_id'         => Auth::id(),
                'room_id'         => $request->room_id,
                'start_date'      => $request->start_date,
                'duration_months' => $request->duration_months,
                'end_date'        => $endDate->format('Y-m-d'),
                'payment_method'  => $request->payment_method,
                'status'          => 'booked', // Status awal setelah booking
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // 2. Buat tagihan pertama secara otomatis
            DB::table('bills')->insert([
                'booking_id' => $bookingId,
                'amount'     => $room->price,
                'due_date'   => $request->start_date,
                'status'     => 'unpaid',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. UPDATE STATUS KAMAR DI DATABASE
            DB::table('rooms')
                ->where('id', $request->room_id)
                ->update(['status' => 'booked']);
        });

        return redirect()->route('user.my_room')->with('success', 'Booking Berhasil! Selesaikan pembayaran Anda.');
    }

    /**
     * PROSES PERPANJANGAN KAMAR (Fungsi Baru)
     */
    public function extendRoom(Request $request, $id)
    {
        $request->validate([
            'months' => 'required|integer|min:1'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                // 1. Ambil data booking lama
                $booking = DB::table('bookings')->where('id', $id)->first();
                $room = DB::table('rooms')->where('id', $booking->room_id)->first();

                // 2. Hitung tanggal berakhir baru
                $currentEndDate = Carbon::parse($booking->end_date);
                $newEndDate = $currentEndDate->addMonths($request->months);

                // 3. Update data booking
                DB::table('bookings')
                    ->where('id', $id)
                    ->update([
                        'end_date' => $newEndDate->format('Y-m-d'),
                        'duration_months' => $booking->duration_months + $request->months,
                        'updated_at' => now()
                    ]);

                // 4. Buat tagihan baru untuk masa perpanjangan
                // Kita buat tagihan jatuh tempo pada saat masa sewa lama habis
                DB::table('bills')->insert([
                    'booking_id' => $id,
                    'amount'     => $room->price * $request->months,
                    'due_date'   => $booking->end_date, // Jatuh tempo di akhir periode lama
                    'status'     => 'unpaid',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            return redirect()->back()->with('success', 'Kamar berhasil diperpanjang!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperpanjang: ' . $e->getMessage());
        }
    }

    /**
     * Update data kamar dari sisi Admin
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'room_number' => 'required',
            'price'       => 'required|numeric',
            'status'      => 'required|in:available,booked,occupied,maintenance',
            'type'        => 'required'
        ]);

        try {
            DB::table('rooms')->where('id', $id)->update([
                'room_number' => $request->room_number,
                'price'       => $request->price,
                'status'      => $request->status, 
                'type'        => $request->type,
                'updated_at'  => now(),
            ]);

            return redirect()->back()->with('success', 'Data kamar berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }
}