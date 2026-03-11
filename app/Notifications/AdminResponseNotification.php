<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminResponseNotification extends Notification
{
    use Queueable;

    protected $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['database']; // Menyimpan notif ke tabel 'notifications'
    }

    public function toArray($notifiable)
{
    // Cek apakah status bookingnya success atau bukan
    $isSuccess = $this->booking->status === 'success';
    
    return [
        'title' => $isSuccess ? 'Pesanan Disetujui! ✅' : 'Pesanan Ditolak ❌',
        'message' => $isSuccess 
            ? 'Pembayaran kamar ' . ($this->booking->room->room_number ?? '') . ' sudah dikonfirmasi. Selamat datang di Sin Kost An!'
            : 'Mohon maaf, bukti pembayaran Anda tidak valid atau ditolak oleh Admin. Silakan hubungi admin.',
        'type' => $isSuccess ? 'success' : 'warning',
        'booking_id' => $this->booking->id
    ];
}

    
}