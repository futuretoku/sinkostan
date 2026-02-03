<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use App\Models\Bill;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminBillController extends Controller
{
    public function index() {
        $branches = Kost::all();
        return view('admin.manajemen-tagihan', compact('branches'));
    }

    public function getBillsByBranch($kost_id) {
        $bills = Bill::whereHas('booking.room', function($query) use ($kost_id) {
                $query->where('kost_id', $kost_id);
            })
            ->with(['booking.user', 'booking.room'])
            ->get()
            ->map(function($bill) {
                return [
                    'id' => $bill->id,
                    'tenant_name' => $bill->booking->user->name,
                    'room_number' => $bill->booking->room->room_number,
                    'month' => Carbon::parse($bill->due_date)->format('Y-m'),
                    'total' => number_format($bill->amount, 0, ',', '.'),
                    'status' => $bill->status, // unpaid, paid, overdue
                ];
            });

        return response()->json($bills);
    }
}