<?php

use Illuminate\Support\Facades\Schedule;
use App\Models\Booking;
use App\Notifications\RentReminderNotification;

Schedule::call(function () {
    // Cari yang masa sewanya tinggal 7 hari lagi
    $nearEndBookings = Booking::where('status', 'success')
        ->whereDate('end_date', now()->addDays(7)->format('Y-m-d'))
        ->get();
    
foreach ($nearEndBookings as $booking) {
        $booking->user->notify(new RentReminderNotification($booking));
    }
})->dailyAt('08:00');