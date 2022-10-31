<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\School;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile() {
        $res = auth()->user();
        $res['school'] = School::find($res->school_id)->first();
        $res['grade'] = Grade::find($res->grade_id)->first();
        return response()->json($res);
    }

    public function school() {
        $res = auth()->user();
        $school = School::find($res->school_id)->first();
        return response()->json($school);
    }

    public function grade() {
        $res = auth()->user();
        $grade = Grade::find($res->grade_id)->first();
        return response()->json($grade);
    }

}
