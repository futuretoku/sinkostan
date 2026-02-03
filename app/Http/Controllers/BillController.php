<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\Booking; // Tambahkan ini
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function index()
    {
        $bills = Bill::whereHas('booking', function ($q) {
            $q->where('user_id', auth()->id());
        })->with('booking.room')->get();

        return view('user.bills', compact('bills'));
    }

    public function pay(Bill $bill)
    {
        // security: pastikan bill milik user
        if ($bill->booking->user_id !== auth()->id()) {
            abort(403);
        }

        return view('user.pay', compact('bill'));
    }

    /**
     * Fungsi ini sekarang bisa menangani upload dari Booking maupun Bill langsung
     */
    public function storePayment(Request $request, $id)
    {
        // 1. Validasi
        $request->validate([
            // 'payment_method' kita buat optional karena di detail booking sudah terpilih sebelumnya
            'payment_method' => 'nullable|in:transfer,ewallet,cash', 
            'proof' => 'required|image|max:2048'
        ]);

        // Cek apakah yang dikirim ID Booking atau ID Bill
        // Kita prioritaskan mencari Booking dulu karena alur 'payment-detail' kamu
        $booking = Booking::find($id);

        if ($booking) {
            // ALUR DARI HALAMAN PAYMENT DETAIL (BOOKING)
            $path = $request->file('proof')->store('payments', 'public');

            // Update status booking dan kamar
            $booking->update([
                'status' => 'pending',
                'payment_proof' => $path // Pastikan kolom ini ada di tabel bookings
            ]);

            // LOGIKA KAMAR: Berubah jadi 'booked'
            $booking->room->update(['status' => 'booked']);

            // Opsional: Buat record di tabel payments untuk tagihan pertama (bulan pertama)
            $firstBill = $booking->bills()->first();
            if ($firstBill) {
                Payment::updateOrCreate(
                    ['bill_id' => $firstBill->id],
                    [
                        'amount' => $firstBill->amount,
                        'method' => $booking->payment_method,
                        'proof' => $path,
                        'status' => 'pending'
                    ]
                );
            }

            return redirect()->route('booking.history')
                ->with('success', 'Bukti dikirim! Status kamar: DIBOOKING. Menunggu verifikasi admin.');

        } else {
            // ALUR LAMA (DARI DAFTAR TAGIHAN / BILL)
            $bill = Bill::findOrFail($id);
            $path = $request->file('proof')->store('payments', 'public');

            Payment::create([
                'bill_id' => $bill->id,
                'amount' => $bill->amount,
                'method' => $request->payment_method ?? 'transfer',
                'proof' => $path,
                'status' => 'pending'
            ]);

            return redirect('/my-bills')
                ->with('success', 'Pembayaran tagihan dikirim, menunggu verifikasi admin');
        }
    }
}