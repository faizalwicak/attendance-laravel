<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\ImportantLink;
use App\Models\Notification;
use App\Models\Quote;
use App\Models\Record;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    public function home()
    {
        $quote = Quote::where('school_id', auth()->user()->school_id)->where('active', 1)->first();
        $links = ImportantLink::where('school_id', auth()->user()->school_id)->get();

        $now = new DateTime('now');
        $record = Record::where('user_id', auth()->user()->id)
            ->whereDate('date', '=', $now->format('Y-m-d'))
            ->with('attend')
            ->with('leave')
            ->first();

        return view('mobile/home', ['title' => 'Beranda', 'quote' => $quote, 'links' => $links, 'record' => $record]);
    }

    public function friend()
    {
        $users = User::where('school_id', auth()->user()->school_id)
            ->where('grade_id', auth()->user()->grade_id)
            ->with(['records' => function ($query) {
                $query->where('date', date('Y-m-d'));
            }, 'records.attend', 'records.leave'])
            ->get();

        return view('mobile/friend', ['title' => 'Teman Kelas', 'users' => $users]);
    }

    public function notification()
    {
        $notifications = Notification::where('school_id', auth()->user()->school_id)
            ->orderBy('updated_at', 'DESC')
            ->get();

        $user = User::find(auth()->user()->id);
        $user->update([
            'last_seen_notification' => date('Y-m-d H:i:s')
        ]);
        return view('mobile/notification', ['title' => 'Pengumuman', 'notifications' => $notifications]);
    }

    public function profile()
    {
        return view('mobile/profile', ['title' => 'Profil']);
    }

    public function updateImage(Request $request)
    {
        $user = User::find(auth()->user()->id);

        $validateData = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'image.required' => 'Foto tidak boleh kosong.',
            'image.image' => 'Foto tidak valid.',
            'image.mimes' => 'Foto tidak valid.',
            'image.max' => 'Foto maksimal 2 MB.'
        ]);

        $imageName = uniqid() . time() . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);
        $user->image = $imageName;
        $user->save();

        return redirect('/mobile/profile')->with('success', 'Foto berhasil disimpan.');
    }

    public function password()
    {
        return view('mobile/password', ['title' => 'Ganti Password']);
    }

    public function updatePassword(Request $request)
    {
        $user = User::findOrFail(auth()->user()->id);

        $validateData = $request->validate([
            'old-password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    $fail('Password lama salah.');
                }
            },],
            'password' => 'required|min:5',
            're-password' => 'required|same:password',
        ], [
            'old-password.required' => 'Password lama tidak boleh kosong.',
            'password.required' => 'Password baru tidak boleh kosong.',
            'password.min' => 'Password baru minimal 6 karakter.',
            're-password.required' => 'Konfirmasi password tidak boleh kosong.',
            're-password.same' => 'Konfirmasi password tidak sama.',
        ]);

        $validateData['password'] = Hash::make($validateData['password']);

        $user->update($validateData);

        return redirect('/mobile/profile/password')
            ->with('success', 'Password berhasil disimpan.');
    }
}
