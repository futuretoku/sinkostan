<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminTenantController extends Controller
{
    public function index()
    {
        $branches = Kost::all();
        return view('admin.manajemen-penyewa', compact('branches'));
    }

   public function getTenantsByBranch($kost_id)
{
    $tenants = Booking::whereHas('room', function($query) use ($kost_id) {
            $query->where('kost_id', $kost_id);
        })
        ->with(['user', 'room'])
        ->get();

    $formattedData = $tenants->map(function($booking) {
        $endDate = \Carbon\Carbon::parse($booking->end_date);
        $now = \Carbon\Carbon::now();
        
        // Logika Status Berdasarkan Waktu
        if ($now->gt($endDate)) {
            $status = 'Habis';
        } elseif ($now->diffInDays($endDate) <= 7) {
            $status = 'Hampir Habis';
        } else {
            $status = 'Aktif';
        }

        return [
            'name' => $booking->user->name,
            'room_number' => $booking->room->room_number,
            'price' => number_format($booking->room->price, 0, ',', '.'),
            'due_date' => $endDate->translatedFormat('d F Y'),
            'days_left' => (int) $now->diffInDays($endDate, false),
            'status' => $status,
        ];
    });

    return response()->json($formattedData);
}
}