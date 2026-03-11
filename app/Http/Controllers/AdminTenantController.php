<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\PaymentStatusNotification; 

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
     * Mengambil data penyewa berdasarkan cabang (AJAX)
     */
    public function getTenantsByBranch($kost_id)
    {
        $tenants = Booking::whereHas('room', function($query) use ($kost_id) {
                $query->where('kost_id', $kost_id);
            })
            ->with(['user', 'room'])
            ->latest()
            ->get()
            ->groupBy('user_id');

    // Kita ambil data booking, bukan user, supaya bisa dipisah per baris di tabel
    $bookings = Booking::whereHas('room', function($query) use ($kost_id) {
            $query->where('kost_id', $kost_id);
        })
        ->with(['user', 'room'])
        ->latest()
        ->get();

    $formattedData = $bookings->map(function($b) {
        if (!$b->user) return null;

        $endDate = Carbon::parse($b->end_date);
        
        // Logika penentu keaktifan:
        // Harus berstatus 'success' DAN tanggal berakhirnya belum lewat dari sekarang
        $isAktif = ($b->status === 'success' && Carbon::now()->lt($endDate));

        return [
            'user_id' => $b->user->id,
            'booking_id' => $b->id, // Penting untuk update status nanti
            'name' => $b->user->name,
            'phone' => $b->user->phone ?? '-',
            'rooms_summary' => $b->room->room_number,
            'room_details' => [
                [
                    'booking_id' => $b->id,
                    'room_id' => $b->room->id,
                    'room_number' => $b->room->room_number,
                    'status' => $isAktif ? 'Aktif' : 'Habis',
                    'due_date' => $endDate->translatedFormat('d F Y')
                ]
            ],
            'is_active' => $isAktif, // Ini yang menentukan dia masuk tab mana
            'status' => $isAktif ? 'Aktif' : 'Selesai Sewa',
        ];
    })->filter()->values();

    return response()->json($formattedData);
}

    /**
     * Update status sewa dan kirim notifikasi otomatis
     */
    public function updateStatus(Request $request)
{
    DB::beginTransaction();
    try {
        // Ambil booking beserta relasi room
        $booking = Booking::with('room')->findOrFail($request->id);
        $room = $booking->room;
        
        // Memastikan input status bersih dari spasi dan huruf kecil
        $statusRequest = strtolower(trim($request->status));

        if ($statusRequest === 'habis') {
            // 1. Update Booking agar dianggap "Selesai" oleh sistem
            $booking->update([
                'end_date' => Carbon::now()->subDay(), // Set kadaluarsa (kemarin)
                'status' => 'rejected' // 'rejected' tersedia di ENUM bookings tabel kamu
            ]);

            // 2. Update status kamar menjadi 'available'
            if ($room) {
                // PENTING: Jangan masukkan 'user_id' karena kolomnya tidak ada di tabel rooms kamu
                $room->update([
                    'status' => 'available' 
                ]);
            }
        } else if ($statusRequest === 'aktif') {
            // 1. Perpanjang masa sewa
            $booking->update([
                'end_date' => Carbon::now()->addDays(30),
                'status' => 'success' // 'success' tersedia di ENUM bookings tabel kamu
            ]);

            // 2. Update status kamar menjadi 'occupied'
            if ($room) {
                $room->update([
                    'status' => 'occupied'
                ]);
            }
        }

        // 3. Kirim notifikasi
        if ($booking->user) {
            $booking->user->notify(new PaymentStatusNotification($booking));
        }

        DB::commit();
        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        DB::rollback();
        // Mengembalikan pesan error asli agar kamu bisa lihat di Console Browser jika gagal
        return response()->json([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Menghapus riwayat penyewa (membersihkan data booking)
     */
    public function deleteTenant($id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            
            // Ambil semua ID kamar yang pernah di-booking user ini untuk di-reset
            $roomIds = Booking::where('user_id', $id)->pluck('room_id');
            Room::whereIn('id', $roomIds)->update([
                'status' => 'available', 
                'user_id' => null
            ]);

            // Hapus riwayat booking
            $user->bookings()->delete(); 

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}