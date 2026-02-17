<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminTenantController extends Controller
{
    /**
     * Menampilkan halaman utama manajemen penyewa
     */
    public function index()
    {
        $branches = Kost::all();
        return view('admin.manajemen-penyewa', compact('branches'));
    }

    /**
     * Mengambil data penyewa berdasarkan cabang (kost_id)
     * Dikelompokkan per User agar satu orang tidak muncul berulang kali di list utama
     */
    public function getTenantsByBranch($kost_id)
    {
        $tenants = Booking::whereHas('room', function($query) use ($kost_id) {
                $query->where('kost_id', $kost_id);
            })
            ->with(['user', 'room'])
            ->get()
            ->groupBy('user_id');

        $formattedData = $tenants->map(function($userBookings) {
            $firstBooking = $userBookings->first();
            $user = $firstBooking->user;
            
            // Detail rincian kamar untuk ditampilkan di Modal (Pop-up)
            $roomDetails = $userBookings->map(function($b) {
                $endDate = Carbon::parse($b->end_date);
                return [
                    'booking_id' => $b->id,
                    'room_number' => $b->room->room_number,
                    // Cek apakah masa sewa sudah lewat dari waktu sekarang
                    'status' => Carbon::now()->gt($endDate) ? 'Habis' : 'Aktif',
                    'due_date' => $endDate->translatedFormat('d F Y')
                ];
            });

            return [
                'user_id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone ?? '-',
                // Gabungkan nomor-nomor kamar (Contoh: "101, 105")
                'rooms_summary' => $userBookings->map(fn($b) => $b->room->room_number)->implode(', '),
                'room_details' => $roomDetails,
                'status' => $roomDetails->contains('status', 'Habis') ? 'Ada yang Habis' : 'Aktif',
            ];
        })->values();

        return response()->json($formattedData);
    }

    /**
     * Update status per kamar (Booking)
     * Jika Habis: Tanggal dimajukan ke kemarin & kamar jadi 'Tersedia'
     * Jika Aktif: Tanggal ditambah 30 hari & kamar jadi 'Terisi'
     */
    public function updateStatus(Request $request)
{
    try {
        $booking = Booking::findOrFail($request->id);
        $room = Room::find($booking->room_id);
        
        if ($request->status === 'Habis') {
            $booking->update(['end_date' => Carbon::now()->subDay()]);
            
            if ($room) {
                // COBA GANTI JADI HURUF KECIL: 'tersedia'
                // Atau cek di phpMyAdmin kamu, kolom status pakainya 'Tersedia' atau 'tersedia'
                $room->update(['status' => 'available']); 
            }
        } else {
            $booking->update(['end_date' => Carbon::now()->addDays(30)]);
            if ($room) {
                // COBA GANTI JADI HURUF KECIL: 'terisi'
                $room->update(['status' => 'occupied']);
            }
        }

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
}