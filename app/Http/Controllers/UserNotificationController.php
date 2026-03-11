<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserNotificationController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Ambil data langsung dari tabel notifications milik user yang login
        $notifications = DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->where('notifiable_type', 'App\Models\User')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Tandai sudah dibaca secara manual via query agar tidak error logic
        DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->where('read_at', null)
            ->update(['read_at' => now()]);

        return view('user.notifuser', compact('notifications'));
    }
}