<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\Booking; 

class ProfileController extends Controller
{
    /**
     * Menampilkan Halaman Profil Utama User
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Mengambil SEMUA data sewa berdasarkan user_id (bukan cuma first())
        $bookings = Booking::where('user_id', $user->id)
            ->whereIn('status', ['paid', 'booked', 'success']) 
            ->with(['room.kost']) 
            ->latest()
            ->get(); // Menggunakan get() agar bisa di-looping di view

        return view('user.profil', [
            'user' => $user,
            'bookings' => $bookings, // Mengirimkan variabel jamak ke view
        ]);
    }

    /**
     * Update informasi profil user (Nama, Email, WA, Bio, Foto).
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Validasi input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20'], 
            'description' => ['nullable', 'string', 'max:500'], 
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // Mapping input ke kolom database
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone; 
        $user->description = $request->description; 

        // Reset verifikasi email jika email diganti
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Logika Upload Foto Profil
        if ($request->hasFile('avatar')) {
            // Hapus foto lama dari storage jika bukan foto default
            if ($user->avatar && $user->avatar !== 'avatar.png') {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
            }

            $file = $request->file('avatar');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('avatars', $filename, 'public');
            $user->avatar = $filename;
        }

        $user->save();

        // Kembali ke halaman profil dengan status sukses
        return Redirect::route('profile.index')->with('status', 'profile-updated');
    }

    /**
     * Menghapus akun user.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}