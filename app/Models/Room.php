<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    // Menentukan kolom mana saja yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'kost_id',
        'room_number',
        'floor',
        'price',
        'status',
        'type',
        'fasilities',
        'image'
    ];

    /**
     * Relasi ke Kost (Cabang)
     * Satu kamar dimiliki oleh satu Kost
     */
    public function kost()
    {
        return $this->belongsTo(Kost::class);
    }

    /**
     * Relasi ke Booking
     * Satu kamar bisa memiliki banyak riwayat booking
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}