<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {

        $notifications = Notification::where('school_id', auth()->user()->school_id)
            ->orderBy('updated_at', 'DESC')
            ->get();

        $user = User::find(auth()->user()->id);
        $user->update([
            'last_seen_notification' => date('Y-m-d H:i:s')
        ]);

        return response()->json($notifications);
    }
}
