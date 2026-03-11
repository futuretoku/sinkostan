<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use App\Models\Maintenance;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaintenanceController extends Controller
{
    // FUNGSI UNTUK USER: Menampilkan Form & Monitoring Laporan Aktif
    public function create()
    {
        // 1. Ambil data kamar yang dimiliki user
        $userBookings = Booking::where('user_id', Auth::id())
                        ->where('status', 'paid')
                        ->with('room.kost')
                        ->get();

        // 2. Ambil keluhan yang SEDANG DIPROSES (untuk fitur monitoring di samping form)
        $onProgressMaintenances = Maintenance::where('user_id', Auth::id())
                                ->where('status', 'Dalam Proses')
                                ->latest()
                                ->get();

        // Pastikan view-nya mengarah ke file yang benar (tadi kamu pakai user.maintenance)
        return view('user.maintenance', compact('userBookings', 'onProgressMaintenances'));
    }

    // FUNGSI UNTUK USER: Simpan Laporan
    public function store(Request $request)
    {
        $request->validate([
            'room_id'     => 'required',
            'nomor_kamar' => 'required',
            'kost_id'     => 'required',
            'judul'       => 'required',
            'kategori'    => 'required',
            'deskripsi'   => 'required',
            'foto'        => 'nullable|image|max:2048'
        ]);

        $pathFoto = null;
        if ($request->hasFile('foto')) {
            $pathFoto = $request->file('foto')->store('maintenance', 'public');
        }

        Maintenance::create([
            'user_id'     => Auth::id(),
            'kost_id'     => $request->kost_id,
            'room_id'     => $request->room_id, 
            'nomor_kamar' => $request->nomor_kamar,
            'judul'       => $request->judul,
            'kategori'    => $request->kategori,
            'deskripsi'   => $request->deskripsi,
            'foto'        => $pathFoto,
            'status'      => 'Dalam Proses',
        ]);

        // with('success', ...) ini yang memicu alert muncul di halaman
        return redirect()->back()->with('success', 'Keluhan berhasil dikirim! Admin akan segera mengeceknya.');
    }

    // FUNGSI UNTUK USER: Melihat Riwayat Selesai
    public function history()
    {
        $laporans = Maintenance::where('user_id', Auth::id())
                    ->where('status', 'Selesai') // Biasanya history hanya yang sudah selesai
                    ->latest()
                    ->get();
        return view('user.riwayat-maintenance', compact('laporans'));
    }

    // FUNGSI UNTUK ADMIN: Menampilkan Daftar
    public function adminIndex(Request $request)
    {
        $kosts = Kost::all();
        $kostId = $request->input('kost_id');

        $laporans = Maintenance::when($kostId, function ($query) use ($kostId) {
                return $query->where('kost_id', $kostId);
            })
            ->with(['kost', 'user', 'room'])
            ->latest()
            ->get();

        return view('admin.maintenance', compact('laporans', 'kosts'));
    }

    // FUNGSI UNTUK ADMIN: Update Status & Upload Bukti
    public function updateStatus(Request $request, $id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->status = $request->status;

        if ($request->status == 'Selesai' && $request->hasFile('foto_selesai')) {
            $paths = [];
            foreach ($request->file('foto_selesai') as $file) {
                $paths[] = $file->store('bukti_perbaikan', 'public');
            }
            $maintenance->foto_selesai = json_encode($paths);
        }

        $maintenance->save();
        return redirect()->back()->with('success', 'Status laporan berhasil diperbarui!');
    }
}