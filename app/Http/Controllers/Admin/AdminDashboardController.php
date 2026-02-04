<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Kost; 
use App\Models\Room; 
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request) {
        // Menggunakan Eager Loading agar relasi ->rooms tersedia di Blade
        $kosts = Kost::with('rooms')->get();
        
        $selectedKostId = $request->get('kost_id');
        $range = $request->get('range', 'bulan'); 

        // --- 1. Logika untuk Data Kamar (Stats Cards) ---
        $query = Room::query();
        if ($selectedKostId) {
            $query->where('kost_id', $selectedKostId);
        }

        $totalKamar = (clone $query)->count();
        $kamarTerisi = (clone $query)->where('status', 'occupied')->count();
        $kamarKosong = (clone $query)->where('status', 'available')->count();
        
        $okupansi = $totalKamar > 0 ? round(($kamarTerisi / $totalKamar) * 100) : 0;

        // --- 2. Logika untuk Data Pemasukan ---
        // Query disesuaikan dengan struktur JOIN yang benar di database Anda
        $pemasukanQuery = Payment::join('bills', 'payments.bill_id', '=', 'bills.id')
            ->join('bookings', 'bills.booking_id', '=', 'bookings.id')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->where('payments.status', 'success');

        if ($selectedKostId) {
            $pemasukanQuery->where('rooms.kost_id', $selectedKostId);
        }

        // Filter Waktu
        if ($range == 'minggu') {
            $pemasukanQuery->whereBetween('payments.updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($range == 'bulan') {
            $pemasukanQuery->whereMonth('payments.updated_at', Carbon::now()->month)
                           ->whereYear('payments.updated_at', Carbon::now()->year);
        } elseif ($range == 'tahun') {
            $pemasukanQuery->whereYear('payments.updated_at', Carbon::now()->year);
        }

        $pemasukan = $pemasukanQuery->sum('payments.amount');

        // --- 3. Data untuk Grafik (Disesuaikan dengan Nama Kolom Database) ---
        $incomeLabels = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'];
        $incomeValues = [0, 0, 0, (int)$pemasukan]; 
        
        // PERBAIKAN DISINI: Nama kolom adalah 'method' dan value 'ewallet'
        $paymentMethods = [
            Payment::where('method', 'transfer')->count(),
            Payment::where('method', 'ewallet')->count(),
            Payment::where('method', 'cash')->count(),
        ];

        return view('admin.dashboard', compact(
            'kosts', 
            'totalKamar', 
            'kamarTerisi', 
            'kamarKosong', 
            'okupansi', 
            'selectedKostId', 
            'pemasukan', 
            'range',
            'incomeLabels',
            'incomeValues',
            'paymentMethods'
        ));
    }

    public function storeRoom(Request $request) {
        $request->validate([
            'kost_id' => 'required',
            'room_number' => 'required',
            'type' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        $facilities = $request->has('facilities') ? implode(', ', $request->facilities) : "";
        $imagePath = $request->hasFile('image') ? $request->file('image')->store('rooms', 'public') : null;
        $price = ($request->type == 'Elite') ? 1500000 : 800000;

        Room::create([
            'kost_id' => $request->kost_id,
            'room_number' => $request->room_number,
            'type' => $request->type,
            'facilities' => $facilities,
            'image' => $imagePath,
            'price' => $price,
            'status' => 'available',
        ]);

        return redirect()->back()->with('success', 'Kamar berhasil ditambahkan!');
    }

    public function updateStatus(Request $request) {
        $request->validate([
            'room_id' => 'required',
            'status' => 'required'
        ]);

        Room::where('id', $request->room_id)->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Status berhasil diperbarui!');
    }
}