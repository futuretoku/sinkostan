<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class AdminBillController extends Controller
{
    /**
     * Menampilkan halaman utama manajemen tagihan.
     */
    public function index()
    {
        $branches = Kost::all();
        return view('admin.manajemen-tagihan', compact('branches'));
    }

    /**
     * Mengambil data tagihan berdasarkan cabang (AJAX).
     */
    public function getBillsByBranch($kost_id)
    {
        $bills = Bill::whereHas('booking.room', function ($query) use ($kost_id) {
                $query->where('kost_id', $kost_id);
            })
            ->with(['booking.user', 'booking.room', 'payments'])
            ->get()
            ->groupBy('booking.user_id')
            ->map(function ($userBills) {
                $firstBill = $userBills->first();
                return [
                    'tenant_name'  => $firstBill->booking->user->name ?? 'N/A',
                    'room_number'  => $firstBill->booking->room->room_number ?? '-',
                    // Tambahkan phone di sini untuk mempermudah debugging jika perlu
                    'phone'        => $firstBill->booking->user->phone ?? null,
                    'total_unpaid' => $userBills->where('status', '!=', 'paid')->count(),
                    'all_bills'    => $userBills->map(function ($bill) {
                        $latestPayment = $bill->payments->first();
                        return [
                            'id'         => $bill->id,
                            'month'      => Carbon::parse($bill->due_date)->translatedFormat('F Y'),
                            'amount'     => $bill->amount,
                            'status'     => $bill->status,
                            'proof_path' => $latestPayment ? $latestPayment->proof : null,
                        ];
                    })
                ];
            })->values();

        return response()->json($bills);
    }

    /**
     * Mengirim pengingat pembayaran via WhatsApp Bot.
     */
    public function sendReminder(Request $request, $id) 
    {
        $bill = Bill::with(['booking.user'])->findOrFail($id);
        $user = $bill->booking->user;
        
        $rawPhone = $user->phone;

        if (!$rawPhone) {
            return response()->json(['success' => false, 'message' => 'Nomor HP tidak ditemukan di database!'], 404);
        }

        // --- PROSES NORMALISASI NOMOR HP ---
        // 1. Hapus semua karakter non-angka (spasi, plus, minus, dll)
        $phone = preg_replace('/[^0-9]/', '', $rawPhone);

        // 2. Ubah awalan 08 atau 8 jadi 628
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }
        
        // 3. Tambahkan @c.us jika bot Node.js kamu memerlukannya (opsional, tergantung script bot)
        // $phone = $phone . "@c.us"; 

        $nominal = number_format($bill->amount, 0, ',', '.');
        $bulan = Carbon::parse($bill->due_date)->translatedFormat('F Y');
        
        $message = "Halo *{$user->name}*,\n\nIni adalah pengingat dari *Sin Kost*.\nTagihan Anda untuk bulan *{$bulan}* sebesar *Rp {$nominal}* belum terbayar.\n\nMohon segera melakukan pembayaran dan upload bukti transfer melalui website.\n\nTerima kasih! 🙏";

        try {
            $response = Http::timeout(10)->post('http://localhost:3000/send-message', [
                'phone'   => $phone, 
                'message' => $message
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Notifikasi WA terkirim ke: ' . $phone
                ]);
            }

            return response()->json([
                'success' => false, 
                'message' => 'Bot WA merespon dengan error: ' . $response->body()
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal terhubung ke Bot WA (localhost:3000). Pastikan Bot sudah dijalankan.'
            ], 500);
        }
    }

    /**
     * Konfirmasi pembayaran secara manual oleh admin.
     */
    public function confirm($id)
    {
        $bill = Bill::findOrFail($id);
        $bill->update(['status' => 'paid']);

        return response()->json(['success' => true, 'message' => 'Pembayaran berhasil dikonfirmasi!']);
    }

    /**
     * Menghapus data tagihan.
     */
    public function destroy($id)
    {
        $bill = Bill::findOrFail($id);
        $bill->delete();

        return response()->json(['success' => true, 'message' => 'Tagihan berhasil dihapus!']);
    }
}