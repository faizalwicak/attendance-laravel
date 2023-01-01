<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\School;
use App\Models\User;
use DateTime;

class ClockController extends Controller
{
    public function clockIn($user_id)
    {
        $user = User::where('id', $user_id)->first();

        if ($user == null) {
            return back()->with('error', 'Siswa tidak ditemukan.');
        }

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

        return back()->with('success', $message);
    }

    public function clockOut($user_id)
    {
        $user = User::where('id', $user_id)->first();

        if ($user == null) {
            return back()->with('error', 'Siswa tidak ditemukan.');
        }

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

        $record = Record::where('date', $nowDate->format('Y-m-d'))
            ->where('user_id', $user_id)
            ->with('attend')
            ->first();

        $message = "Presensi berhasil.";

        // $limitTime = '24:00:00';
        // $nowTime = strtotime($nowDate->format('H:i:s'));

        // if ($nowTime > strtotime($limitTime)) {
        //     return back()->with('error', 'Anda bisa absensi sebelum jam ' . $limitTime . '.');
        // }

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

        return back()->with('success', $message);
    }
}
