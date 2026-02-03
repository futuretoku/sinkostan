<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    /**
     * Menampilkan daftar semua pembayaran
     */
    public function index()
    {
        $payments = Payment::with([
            'bill.booking.user',
            'bill.booking.room'
        ])
        ->latest()
        ->get();

        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Konfirmasi Pembayaran: 
     * Status Booking jadi 'paid', Status Kamar jadi 'occupied'
     */
    public function approve($id)
    {
        // Cari data payment
        $payment = Payment::with('bill.booking.room')->findOrFail($id);
        
        // 1. Update status Payment jadi success
        $payment->update(['status' => 'success']);

        // 2. Update status Bill terkait jadi paid
        if ($payment->bill) {
            $payment->bill->update(['status' => 'paid']);
            
            // 3. Update status Booking jadi paid
            $booking = $payment->bill->booking;
            $booking->update(['status' => 'paid']);

            // 4. LOGIKA KAMAR: Berubah dari 'booked' menjadi 'occupied' (Terisi)
            if ($booking->room) {
                $booking->room->update(['status' => 'occupied']);
            }
        }

        return redirect()->back()->with('success', 'Pembayaran dikonfirmasi! Kamar sekarang berstatus TERISI (Occupied).');
    }

    /**
     * Tolak Pembayaran:
     * Status Booking jadi 'rejected', Status Kamar balik jadi 'available'
     */
    public function reject($id)
    {
        $payment = Payment::with('bill.booking.room')->findOrFail($id);

        // 1. Update status Payment jadi failed
        $payment->update(['status' => 'failed']);

        if ($payment->bill) {
            $booking = $payment->bill->booking;
            
            // 2. Update status Booking
            $booking->update(['status' => 'rejected']);

            // 3. LOGIKA KAMAR: Kembalikan ke 'available' agar bisa dipesan lagi
            if ($booking->room) {
                $booking->room->update(['status' => 'available']);
            }
        }

        return redirect()->back()->with('error', 'Pembayaran ditolak! Kamar kembali berstatus TERSEDIA (Available).');
    }
}