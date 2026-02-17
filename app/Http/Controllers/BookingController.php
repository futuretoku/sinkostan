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
     */
    public function showPaymentDetail($id)
    {
        $booking = Booking::with('room')->findOrFail($id);
        
        // Jika pelanggan pilih cash, tidak perlu halaman detail pembayaran (barcode/rek)
        if ($booking->payment_method == 'cash') {
            return redirect()->route('booking.history')->with('info', 'Silakan lakukan pembayaran tunai di lokasi.');
        }

        $booking->total_price = $booking->room->price * $booking->duration_months;

        return view('user.payment-detail', compact('booking'));
    }

    /**
     * Proses Simpan Booking & Generate Tagihan
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'duration_months' => 'required|integer|min:1',
            'payment_method' => 'required' 
        ]);

        $method = $request->payment_method == 'cod' ? 'cash' : $request->payment_method;
        $startDate = $request->start_date ?? now();
        $duration = (int) $request->duration_months;

        // 1. Simpan booking
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'room_id' => $request->room_id,
            'start_date' => $startDate,
            'duration_months' => $duration,
            'end_date' => Carbon::parse($startDate)->addMonths($duration),
            'payment_method' => $method,
            'status' => 'pending', 
        ]);

        Room::where('id', $request->room_id)->update(['status' => 'booked']);

        // 2. Generate Bills & Ambil ID Bill pertama
        $room = Room::find((int)$request->room_id);
        $firstBillId = null;

        for ($i = 0; $i < $duration; $i++) {
            $bill = \App\Models\Bill::create([
                'booking_id' => $booking->id,
                'amount' => $room->price,
                'due_date' => Carbon::parse($startDate)->addMonths($i),
                'status' => 'unpaid', 
            ]);     
            if($i == 0) $firstBillId = $bill->id; // Ambil ID bill pertama
        }

        // 3. KUNCI UTAMA: Jika cash, buat record di tabel payments agar muncul di admin
        if ($method == 'cash') {
            \App\Models\Payment::create([
                'bill_id' => $firstBillId,
                'amount' => $room->price * $duration, // Total harga
                'proof' => 'CASH_PAYMENT', // Penanda bahwa ini cash
                'status' => 'pending',
            ]);

            return redirect()->route('booking.history')
                ->with('success', 'Booking berhasil! Silakan bayar tunai di lokasi.');
        }

        return redirect()->route('booking.payment_detail', ['id' => $booking->id]);
    }

    /**
     * Menampilkan riwayat booking user
     */
    public function history()
    {
        $bookings = Booking::with('room.branch')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.history', compact('bookings'));
    }
}