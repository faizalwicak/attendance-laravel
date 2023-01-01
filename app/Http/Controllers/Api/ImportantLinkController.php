<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ImportantLink;
use Illuminate\Http\Request;

class ImportantLinkController extends Controller
{
    public function index()
    {
        $links = ImportantLink::where('school_id', auth()->user()->school_id)->get();
        return response()->json($links);
    }
}
