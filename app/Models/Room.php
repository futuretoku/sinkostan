<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'kost_id', 'room_number', 'floor', 'price', 'status', 'type', 'facilities', 'image'
    ];

    // Relasi Dasar
    // Pastikan relasi kost ada agar getKostNameAttribute tidak error
public function kost() { 
    return $this->belongsTo(Kost::class); 
}

// Accessor: Nama Kost (Mengambil dari tabel kosts)
public function getKostNameAttribute()
{
    return $this->kost ? $this->kost->name : 'N/A';
}

    public function bookings() { return $this->hasMany(Booking::class); }
    public function branch() { return $this->belongsTo(Branch::class); }

    /** * FITUR: Mendapatkan data booking aktif milik user login 
     * Ini digunakan untuk menarik data tanggal sewa ke halaman Kamar Saya
     */
    public function getActiveBookingAttribute()
{
    // Jika ingin melihat masa ngekost milik user yang sedang login di kamar itu:
    return $this->bookings()
        ->where('user_id', auth()->id()) 
        ->whereIn('status', ['paid', 'success'])
        ->latest()
        ->first();
}

    // Accessor: Nama Kost ($myRoom->kost_name)
   

    // Accessor: Gambar ($myRoom->display_image)
    public function getDisplayImageAttribute()
    {
        if (!$this->image) return asset('images/default-room.jpg');
        $images = explode(',', $this->image);
        $firstImage = trim($images[0]);
        // Pastikan sudah menjalankan php artisan storage:link
        return asset('storage/' . $firstImage);
    }

    // Accessor: Tanggal Mulai ($myRoom->start_date_formatted)
    public function getStartDateFormattedAttribute()
    {
        $booking = $this->active_booking;
        return $booking ? Carbon::parse($booking->start_date)->translatedFormat('d F Y') : '-';
    }

    // Accessor: Tanggal Berakhir ($myRoom->end_date_formatted)
    public function getEndDateFormattedAttribute()
    {
        $booking = $this->active_booking;
        return $booking ? Carbon::parse($booking->end_date)->translatedFormat('d F Y') : '-';
    }

    // Accessor: Durasi ($myRoom->duration_months)
    public function getDurationMonthsAttribute()
    {
        $booking = $this->active_booking;
        return $booking ? $booking->duration_months : 0;
    }

    
    
}