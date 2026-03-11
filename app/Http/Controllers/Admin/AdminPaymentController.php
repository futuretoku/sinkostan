<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
// Pastikan ini terpanggil!
use App\Notifications\AdminResponseNotification;

class AdminPaymentController extends Controller
{
    public function index()
    {
        // Mengambil pembayaran yang statusnya 'pending' untuk ditampilkan di halaman notifikasi admin
        $pendingPayments = Payment::with([
            'bill.booking.user',
            'bill.booking.room'
        ])
        ->where('status', 'pending')
        ->latest()
        ->get();

        return view('admin.notifications.index', compact('pendingPayments'));
    }

    public function approve($id)
    {
        $payment = Payment::with('bill.booking.room')->findOrFail($id);
        
        $payment->update(['status' => 'success']);

        if ($payment->bill) {
            $payment->bill->update(['status' => 'paid']);
            
            $booking = $payment->bill->booking;
            $booking->update(['status' => 'success']); // Status success agar pemicu notif 'Berhasil'

            if ($booking->room) {
                $booking->room->update(['status' => 'occupied']);
            }

            // TRIGGER: Kirim notifikasi ke User
            $booking->user->notify(new AdminResponseNotification($booking));
        }

        return redirect()->back()->with('success', 'Pembayaran dikonfirmasi dan penghuni telah dinotifikasi!');
    }

    public function reject($id)
    {
        $payment = Payment::with('bill.booking.room')->findOrFail($id);

        $payment->update(['status' => 'rejected']);

        if ($payment->bill) {
            $booking = $payment->bill->booking;
            $booking->update(['status' => 'rejected']);

            if ($booking->room) {
                $booking->room->update(['status' => 'available']);
            }

            // TRIGGER: Kirim notifikasi ke User (status rejected akan memicu notif 'Ditolak')
            $booking->user->notify(new AdminResponseNotification($booking));
        }

        return redirect()->back()->with('error', 'Pembayaran ditolak dan penghuni telah dinotifikasi.');
    }
}