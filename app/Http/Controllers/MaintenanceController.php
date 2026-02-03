<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    // Menampilkan semua laporan di halaman maintenance
    public function index()
    {
        $laporans = Maintenance::latest()->get();
        // Sesuai struktur folder: resources/views/admin/maintenance.blade.php
        return view('admin.maintenance', compact('laporans'));
    }

    // Fungsi untuk Update Status dari Pop-up Admin
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $maintenance = Maintenance::findOrFail($id);
        $maintenance->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Status berhasil diperbarui!');
    }

    // Fungsi simpan laporan dari User
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'kategori' => 'required',
            'deskripsi' => 'required',
            'nomor_kamar' => 'required',
        ]);

        Maintenance::create([
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'nomor_kamar' => $request->nomor_kamar,
            'status' => 'Dalam Proses',
        ]);

        return redirect()->back()->with('success', 'Laporan berhasil dikirim!');
    }
}