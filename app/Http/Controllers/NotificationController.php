<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use App\Notifications\PaymentStatusNotification;
use App\Notifications\AdminResponseNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $pendingPayments = Payment::with(['bill.booking.user', 'bill.booking.room'])
            ->where('status', 'pending')
            ->latest()
            ->get();
            
        return view('admin.notifikasi', compact('pendingPayments'));
    }

   public function approve($id)
{
    DB::beginTransaction();
    try {
        $payment = Payment::with('bill.booking.user', 'bill.booking.room')->findOrFail($id);
        
        // 1. Update Status Pembayaran & Kamar
        $payment->update(['status' => 'success']);
        if ($payment->bill) {
            $payment->bill->update(['status' => 'paid']);
            if ($payment->bill->booking && $payment->bill->booking->room) {
                $payment->bill->booking->room->update(['status' => 'occupied']);
                
                // Update status booking menjadi success agar logic di Notification Class sinkron
                $payment->bill->booking->update(['status' => 'success']);
            }
        }

        // 2. KIRIM NOTIFIKASI (Trigger Utama)
        if ($payment->bill && $payment->bill->booking) {
            $user = $payment->bill->booking->user;
            // Kirim notifikasi menggunakan class yang sudah kita buat
            $user->notify(new AdminResponseNotification($payment->bill->booking));
        }

        DB::commit();
        return redirect()->back()->with('success', 'Pembayaran disetujui & Notifikasi terkirim!');
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
    }
}

public function reject($id)
{
    try {
        $payment = Payment::with('bill.booking.user')->findOrFail($id);
        $payment->update(['status' => 'rejected']);

        if ($payment->bill && $payment->bill->booking) {
            // Update status booking menjadi rejected
            $payment->bill->booking->update(['status' => 'rejected']);
            
            // Kirim notifikasi penolakan
            $payment->bill->booking->user->notify(new AdminResponseNotification($payment->bill->booking));
        }

        return redirect()->back()->with('success', 'Pembayaran ditolak & User telah dikabari.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal menolak pembayaran.');
    }
}
}