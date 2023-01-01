<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\ImportantLink;
use App\Models\Notification;
use App\Models\Quote;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile()
    {
        $res = auth()->user();
        $res['school'] = School::find(auth()->user()->school_id);
        $res['grade'] = Grade::find(auth()->user()->grade_id);
        $res['quote'] = Quote::where('school_id', auth()->user()->school_id)->where('active', 1)->first();
        $res['link'] = ImportantLink::where('school_id', auth()->user()->school_id)->get();

        if ($res->last_seen_notification != null) {
            $notifications = Notification::where('school_id', auth()->user()->school_id)
                ->where('updated_at', '>', $res->last_seen_notification)
                ->count();
        } else {
            $notifications = Notification::where('school_id', auth()->user()->school_id)
                ->count();
        }
        $res['notifications'] = $notifications;

        return response()->json($res);
    }

    public function school()
    {
        $res = auth()->user();
        $school = School::find($res->school_id)->first();
        return response()->json($school);
    }

    public function grade()
    {
        $res = auth()->user();
        $grade = Grade::find($res->grade_id)->first();
        return response()->json($grade);
    }

    public function updatePicture(Request $request)
    {
        $user = User::where('id', auth()->user()->id)->first();

        $validateData = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'image.required' => 'Foto tidak boleh kosong.',
            'image.image' => 'Foto tidak valid.',
            'image.mimes' => 'Foto tidak valid.',
            'image.max' => 'Foto maksimal 2 MB.'
        ]);

        if ($request->image != null) {
            $imageName = uniqid() . time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $validateData['image'] = $imageName;
        }

        $user->update($validateData);

        return response()->json(['message' => 'Foto berhasil diupdate.']);
    }
}
