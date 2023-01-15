<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\School;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;

class ClockController extends Controller
{

    public function clockInView($user_id)
    {
        $user = User::where('id', $user_id)->first();

        if ($user == null) {
            return back()->with('error', 'Siswa tidak ditemukan.');
        }
        return view('record-clock', [
            'title' => 'Presensi Masuk',
            'user' => $user, 'selectedDay' => date('Y-m-d'),
            'selectedTime' => date('H:i'),
        ]);
    }

    public function clockOutView($user_id)
    {
        $user = User::where('id', $user_id)->first();

        if ($user == null) {
            return back()->with('error', 'Siswa tidak ditemukan.');
        }
        return view('record-clock', [
            'title' => 'Presensi Pulang',
            'user' => $user, 'selectedDay' => date('Y-m-d'),
            'selectedTime' => date('H:i'),
        ]);
    }

    public function clockIn(Request $request, $user_id)
    {
        $user = User::where('id', $user_id)->first();

        if ($user == null) {
            return back()->with('error', 'Siswa tidak ditemukan.');
        }

        $validateData = $request->validate([
            'time' => 'required|date_format:H:i',
        ], [
            'time.required' => 'Waktu tidak boleh kosong.',
            'time.date_format' => 'Format waktu tidak valid.',
        ]);

        if (auth()->user()->role == 'ADMIN') {
        } else {
            $admin = User::find(auth()->user()->id);
            $allowedGrades = $admin->grades()->get();
            $allowedGradesArr = [];
            foreach ($allowedGrades as $g) {
                array_push($allowedGradesArr, $g->id);
            }

            if (!in_array($user->grade_id, $allowedGradesArr)) {
                return back()->with('error', 'Siswa tidak ditemukan.');
            }
        }

        $nowDate = new DateTime();
        $timeStrArr = explode(":", $validateData['time']);
        $nowDate->setTime((int)$timeStrArr[0], (int)$timeStrArr[0]);

        $record = Record::where('date', $nowDate->format('Y-m-d'))
            ->with('attend')
            ->where('user_id', $user_id)
            ->first();

        if ($record && $record->attend && $record->attend->clock_in_time != null) {
            return back()->with('error', 'Siswa sudah absen masuk.');
        }

        $school = School::find(auth()->user()->school_id)->first();

        $clockInTime = strtotime($school->clock_in);
        $nowTime = strtotime($nowDate->format('H:i:s'));

        $status = null;
        $message = "";

        if ($nowTime <= $clockInTime) {
            $status = 'ON_TIME';
            $message = "Presensi berhasil. Siswa masuk tepat waktu.";
        } else {
            $status = 'LATE';
            $message = "Presensi berhasil. Siswa terlambat masuk.";
        }

        $updateData = [
            'clock_in_time' => $nowDate->format('H:i:s'),
            'clock_in_status' => $status,
        ];

        if ($record) {
            if ($record->attend == null) {
                $record = $record->attend()->create($updateData);
            } else {
                $record = $record->attend()->update($updateData);
            }
        } else {
            $createdData = [
                'user_id' => $user_id,
                'date' => $nowDate->format('Y-m-d'),
                'is_leave' => 0,
            ];
            $record = Record::create($createdData);
            $record->attend()->create($updateData);
        }

        return redirect('/record/day?grade=' . $user->grade_id . '&day=' . $nowDate->format('Y-m-d'))->with('success', $message);
    }

    public function clockOut(Request $request, $user_id)
    {
        $user = User::where('id', $user_id)->first();

        if ($user == null) {
            return back()->with('error', 'Siswa tidak ditemukan.');
        }

        $validateData = $request->validate([
            'time' => 'required|date_format:H:i',
        ], [
            'time.required' => 'Waktu tidak boleh kosong.',
            'time.date_format' => 'Format waktu tidak valid.',
        ]);

        if (auth()->user()->role == 'ADMIN') {
        } else {
            $admin = User::find(auth()->user()->id);
            $allowedGrades = $admin->grades()->get();
            $allowedGradesArr = [];
            foreach ($allowedGrades as $g) {
                array_push($allowedGradesArr, $g->id);
            }

            if (!in_array($user->grade_id, $allowedGradesArr)) {
                return back()->with('error', 'Siswa tidak ditemukan.');
            }
        }

        $nowDate = new DateTime();
        $timeStrArr = explode(":", $validateData['time']);
        $nowDate->setTime((int)$timeStrArr[0], (int)$timeStrArr[0]);

        $record = Record::where('date', $nowDate->format('Y-m-d'))
            ->where('user_id', $user_id)
            ->with('attend')
            ->first();

        $message = "Presensi berhasil.";

        if ($record) {
            $record = $record->attend()->update([
                'clock_out_time' => $nowDate->format('H:i:s'),
            ]);
        } else {
            $createdData = [
                'user_id' => $user_id,
                'date' => $nowDate->format('Y-m-d'),
                'is_leave' => 0,
            ];
            $record = Record::create($createdData);
            $record->attend()->create([
                'clock_out_time' => $nowDate->format('H:i:s'),
            ]);
        }

        return redirect('/record/day?grade=' . $user->grade_id . '&day=' . $nowDate->format('Y-m-d'))->with('success', $message);
    }
}
