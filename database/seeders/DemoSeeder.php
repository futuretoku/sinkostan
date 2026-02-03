<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kost;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // USER
        $user = User::create([
            'name' => 'User Demo',
            'email' => 'user@demo.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // KOST
        $kost = Kost::create([
            'name' => 'Kost Mawar',
            'address' => 'Jl. Mawar No. 1',
        ]);

        // ROOM
        $room = Room::create([
            'kost_id' => $kost->id,
            'room_number' => 'A-01',
            'floor' => 1,
            'price' => 1200000,
            'status' => 'occupied',
        ]);

        // BOOKING
        $booking = Booking::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'start_date' => Carbon::now(),
            'duration_months' => 1,
            'end_date' => Carbon::now()->addMonth(),
            'status' => 'active',
        ]);

        // BILL
        Bill::create([
            'booking_id' => $booking->id,
            'amount' => 1200000,
            'due_date' => Carbon::now()->addDays(7),
            'status' => 'unpaid',
        ]);
    }
}
