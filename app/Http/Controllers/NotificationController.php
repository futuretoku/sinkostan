<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        // Mengambil pembayaran yang statusnya masih pending beserta relasi data usernya
        $pendingPayments = Payment::with(['bill.booking.user', 'bill.booking.room'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.notifikasi', compact('pendingPayments'));
    }
}