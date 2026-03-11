<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Booking;

class PaymentStatusNotification extends Notification
{
    use Queueable;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        // Untuk sementara pakai database saja supaya muncul di lonceng notif web
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $status = ($this->booking->status === 'success') ? 'Aktif' : 'Habis';
        
        return [
            'booking_id' => $this->booking->id,
            'message' => "Masa sewa kamar " . $this->booking->room->room_number . " telah " . $status,
            'status' => $this->booking->status,
        ];
    }
}