<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index()
    {
        // Mengambil pembayaran yang statusnya masih pending
        $pendingPayments = Payment::with(['bill.booking.user', 'bill.booking.room'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.notifikasi', compact('pendingPayments'));
    }

    // FUNGSI UNTUK KONFIRMASI (APPROVE)
    public function approve($id)
    {
        DB::beginTransaction();
        try {
            $payment = Payment::findOrFail($id);
            
            // 1. Update status payment jadi 'success'
            $payment->update(['status' => 'success']);

            // 2. Update status bill (tagihan) jadi 'paid'
            if ($payment->bill) {
                $payment->bill->update(['status' => 'paid']);
                
                // 3. Update status kamar jadi 'occupied' (terisi)
                if ($payment->bill->booking && $payment->bill->booking->room) {
                    $payment->bill->booking->room->update(['status' => 'occupied']);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pembayaran berhasil dikonfirmasi!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // FUNGSI UNTUK TOLAK (REJECT) - INI YANG TADI ERROR
    public function reject($id)
    {
        $payment = Payment::findOrFail($id);

        // Pastikan status 'failed' terdaftar di ENUM database kamu
        // Jika di database nama statusnya 'cancel' atau 'rejected', ganti kata 'failed' di bawah ini
        $payment->update([
            'status' => 'rejected' 
        ]);

        return redirect()->back()->with('success', 'Pembayaran telah ditolak.');
    }
}