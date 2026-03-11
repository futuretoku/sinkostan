<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomExtension extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'user_room_id',
        'months',
        'old_end_date',
        'new_end_date',
        'price_per_month',
        'total_amount',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function userRoom()
    {
        return $this->belongsTo(UserRoom::class);
    }
}