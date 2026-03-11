<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'start_date',
        'duration_months',
        'end_date',
        'payment_method',
        'payment_proof',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

     // Nilai default untuk status saat data baru dibuat
     protected $attributes = [
        'status' => 'pending',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}

