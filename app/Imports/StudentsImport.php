<?php

namespace App\Imports;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Row;

class StudentsImport implements OnEachRow, WithHeadingRow, WithUpserts
{
    public function onRow(Row $row)
    {
        $grade = Grade::where('name', $row['grade'])->first();
        if ($grade) {
            $user = User::firstOrNew(['username' => $row['username']]);

            if (($user->school_id != null && $user->school_id != auth()->user()->school_id) || ($user->role != null && $user->role != 'USER')) {
            } else {
                $user->name = $row['name'];
                $user->school_id = auth()->user()->school_id;
                $user->grade_id = $grade->id;
                $user->password = Hash::make($row['password']);
                $user->gender = $row['gender'] == 'L' ? "MALE" : ($row['gender'] == 'P' ? 'FEMALE' : null);
                $user->role = 'USER';
                $user->save();
            }
        }
    }

    /**
     * @return string|array
     */
    public function uniqueBy()
    {
        return 'username';
    }
}
