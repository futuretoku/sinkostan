<?php

namespace App\Http\Controllers;

use App\Models\UserRoom;
use App\Models\Bill;
use App\Models\RoomExtension;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class MyRoomController extends Controller
{
    /**
     * Tampilkan kamar aktif user saat ini
     */
    public function index()
    {
        $user = auth()->user();
        
        $myRoom = UserRoom::where('user_id', $user->id)
            ->with('room.kost')
            ->where('status', 'Aktif')
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($myRoom) {
            $myRoom = $this->formatRoomData($myRoom);
        }
        
        $nextBill = $myRoom ? Bill::where('user_id', $user->id)
            ->where('user_room_id', $myRoom->id)
            ->where('status', '!=', 'Lunas')
            ->orderBy('due_date', 'asc')
            ->first() : null;
        
        if ($nextBill) {
            $nextBill = $this->formatBillData($nextBill);
        }
        
        return view('user.my-room', compact('myRoom', 'nextBill'));
    }

    /**
     * Tampilkan detail kamar berdasarkan ID dari history
     */
    public function show($userRoomId)
    {
        $user = auth()->user();
        
        $myRoom = UserRoom::where('user_id', $user->id)
            ->where('id', $userRoomId)
            ->with('room.kost')
            ->firstOrFail();
        
        $myRoom = $this->formatRoomData($myRoom);
        
        // Ambil tagihan berikutnya untuk kamar ini
        $nextBill = Bill::where('user_id', $user->id)
            ->where('user_room_id', $userRoomId)
            ->where('status', '!=', 'Lunas')
            ->orderBy('due_date', 'asc')
            ->first();
        
        if ($nextBill) {
            $nextBill = $this->formatBillData($nextBill);
        }
        
        return view('user.my-room', compact('myRoom', 'nextBill'));
    }

    /**
     * Proses perpanjangan kamar
     */
    public function extendRoom(Request $request, $userRoomId)
    {
        $request->validate([
            'months' => 'required|integer|in:1,3,6,12',
        ]);

        $user = auth()->user();
        $myRoom = UserRoom::where('user_id', $user->id)
            ->where('id', $userRoomId)
            ->with('room')
            ->firstOrFail();

        $months = (int)$request->months;
        $currentEndDate = Carbon::parse($myRoom->end_date);
        $newEndDate = $currentEndDate->addMonths($months);
        $price = $myRoom->room->price ?? 0;
        $totalAmount = $price * $months;

        // Simpan data perpanjangan
        $extension = RoomExtension::create([
            'user_id' => $user->id,
            'room_id' => $myRoom->room_id,
            'user_room_id' => $myRoom->id,
            'months' => $months,
            'old_end_date' => $currentEndDate,
            'new_end_date' => $newEndDate,
            'price_per_month' => $price,
            'total_amount' => $totalAmount,
            'status' => 'Pending',
        ]);

        // Buat bill untuk perpanjangan
        Bill::create([
            'user_id' => $user->id,
            'room_id' => $myRoom->room_id,
            'user_room_id' => $myRoom->id,
            'extension_id' => $extension->id,
            'amount' => $totalAmount,
            'periode_start' => $currentEndDate->addDay(),
            'periode_end' => $newEndDate,
            'due_date' => now()->addDays(3),
            'status' => 'Belum Lunas',
            'type' => 'extension',
        ]);

        return redirect()->back()->with('success', 'Perpanjangan berhasil dibuat! Silakan lakukan pembayaran');
    }

    /**
     * Format data kamar
     */
    private function formatRoomData($myRoom)
    {
        $myRoom->start_date_formatted = Carbon::parse($myRoom->start_date)
            ->locale('id_ID')
            ->translatedFormat('d F Y');
        $myRoom->end_date_formatted = Carbon::parse($myRoom->end_date)
            ->locale('id_ID')
            ->translatedFormat('d F Y');
        $myRoom->duration_months = Carbon::parse($myRoom->start_date)
            ->diffInMonths(Carbon::parse($myRoom->end_date));
        
        $myRoom->room_number = $myRoom->room->number ?? '-';
        $myRoom->floor = $myRoom->room->floor ?? '-';
        $myRoom->type = $myRoom->room->type ?? '-';
        $myRoom->price = $myRoom->room->price ?? 0;
        $myRoom->kost_name = $myRoom->room->kost->name ?? '-';
        $myRoom->facilities = $myRoom->room->facilities ?? '';
        $myRoom->display_image = $myRoom->room->image ?? asset('images/default-room.jpg');
        $myRoom->status = $myRoom->status ?? 'Aktif';
        
        return $myRoom;
    }

    /**
     * Format data tagihan
     */
    private function formatBillData($bill)
    {
        $bill->due_date_formatted = Carbon::parse($bill->due_date)
            ->locale('id_ID')
            ->translatedFormat('d F Y');
        $bill->periode_start = Carbon::parse($bill->periode_start)
            ->locale('id_ID')
            ->translatedFormat('d F Y');
        $bill->periode_end = Carbon::parse($bill->periode_end)
            ->locale('id_ID')
            ->translatedFormat('d F Y');
        
        return $bill;
    }
}