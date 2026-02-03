<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Bill;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookingController extends Controller
{
    /**
     * Menampilkan halaman formulir pembayaran (Pilih Metode & Durasi)
     */
    public function showPayment($room_id)
    {
        $room = Room::with('branch')->findOrFail($room_id);
        return view('user.payment', compact('room'));
    }

    /**
     * Menampilkan Halaman Detail Pembayaran (Tampilan Barcode/Rekening)
     * URL: /booking/payment-detail/{id}
     */
    public function showPaymentDetail($id)
    {
        // 1. Ambil data booking
        $booking = Booking::with('room')->findOrFail($id);
        
        // 2. Hitung total harga untuk ditampilkan di desain detail
        $booking->total_price = $booking->room->price * $booking->duration_months;

        // 3. Pastikan mengarah ke file payment-detail.blade.php
        return view('user.payment-detail', compact('booking'));
    }

    /**
     * Proses Simpan Booking & Generate Tagihan
     * URL: /booking/store
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'duration_months' => 'required|integer|min:1',
            'payment_method' => 'required'
        ]);

        $startDate = $request->start_date ?? now();

        // 2. Simpan data booking
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'room_id' => $request->room_id,
            'start_date' => $startDate,
            'duration_months' => (int) $request->duration_months,
            'end_date' => Carbon::parse($startDate)->addMonths((int) $request->duration_months),
            'payment_method' => $request->payment_method, 
        ]);

        // 3. Update status kamar
        Room::where('id', $request->room_id)->update(['status' => 'booked']);

        // 4. Generate tagihan per bulan
        $room = Room::find( (int)$request->room_id);
        for ($i = 0; $i < $request->duration_months; $i++) {
            Bill::create([
                'booking_id' => $booking->id,
                'amount' => $room->price,
                'due_date' => Carbon::parse($startDate)->addMonths($i),
                'status' => 'unpaid', 
            ]);     
        }

        // 5. REDIRECT (Kunci Utama Agar URL Berubah)
        // Ini yang akan memindahkan URL dari /booking/store ke /booking/payment-detail/ID
        if ($request->payment_method == 'cod') {
            return redirect()->route('dashboard')->with('success', 'Booking Berhasil!');
        }

        

        return redirect()->route('booking.payment_detail', ['id' => $booking->id]);
    }

    public function history()
{
    // Mengambil data booking milik user yang sedang login, diurutkan dari yang terbaru
    $bookings = Booking::with('room.branch')
        ->where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->get();

    return view('user.history', compact('bookings'));
}
}