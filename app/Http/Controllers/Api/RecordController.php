<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function friend()
    {
        $users = User::where('school_id', auth()->user()->school_id)
            ->where('grade_id', auth()->user()->grade_id)
            ->with(['records' => function ($query) {
                $query->where('date', date('Y-m-d'));
            }, 'records.attend', 'records.leave'])
            ->get();

        return response()->json($users);
    }
}
