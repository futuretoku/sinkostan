<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RentReminderNotification extends Notification
{
    use Queueable;

    public $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Masa Sewa Hampir Habis!',
            'message' => 'Kamar ' . $this->booking->room->room_number . ' akan berakhir dalam 7 hari. Segera lakukan perpanjangan.',
            'type' => 'warning',
            'status' => 'pending'
        ];
    }
}