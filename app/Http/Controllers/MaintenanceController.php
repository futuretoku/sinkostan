<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaintenanceController extends Controller
{
    // 1. FUNGSI UNTUK MENAMPILKAN FORM (USER)
    public function create()
    {
        // Mengambil booking milik user yang sedang login
        $userBookings = Booking::where('user_id', Auth::id())
                        ->where('status', 'paid') // Hanya yang sudah bayar/aktif
                        ->with('room')
                        ->get();

        return view('user.maintenance', compact('userBookings'));
    }

    // 2. FUNGSI UNTUK MENYIMPAN DATA (USER)
    public function store(Request $request)
    {
        $request->validate([
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
            'user_id'     => Auth::id(), // Pastikan sudah tambah kolom user_id di DB
            'kost_id'     => $request->kost_id,
            'nomor_kamar' => $request->nomor_kamar,
            'judul'       => $request->judul,
            'kategori'    => $request->kategori,
            'deskripsi'   => $request->deskripsi,
            'foto'        => $pathFoto,
            'status'      => 'Dalam Proses',
        ]);

        return redirect()->back()->with('success', 'Keluhan berhasil dikirim!');
    }

    // Fungsi untuk menampilkan daftar keluhan (Admin)
    public function adminIndex(Request $request)
{
    // Mengambil ID Kost dari dropdown filter
    $kostId = $request->input('kost_id');

    // Ambil data laporan, jika ada filter kost_id maka filter, jika tidak ambil semua
    $laporans = Maintenance::when($kostId, function ($query) use ($kostId) {
            return $query->where('kost_id', $kostId);
        })
        ->with('kost') // Eager loading supaya nggak N+1 query
        ->latest()
        ->get();

    $kosts = \App\Models\Kost::all(); // Untuk isi dropdown

    return view('admin.maintenance', compact('laporans', 'kosts'));
}

    public function index (Request $request)
    {
        $kosts = Kost::all();

        $query = Maintenance::with('kost');


        if($request->has('kost_id') &&  $request->kost_id != '' ){
            $query->where('kost_id', $request->kost_id);
        }

        $laporans = $query->latest()->get();

        return view ('admin.maintenance', compact ('laporans', 'kosts'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required']);

        $maintenance = Maintenance::findOrFail($id);
        $maintenance->update(['status'=> $request->status]);

    return redirect()->back()->with('success', 'Status Berhasil Diperbarui');
    }
}