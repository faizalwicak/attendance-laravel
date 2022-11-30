<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index()
    {
        $quote = Quote::where('school_id', auth()->user()->school_id)->where('active', 1)->first();
        if (!$quote) {
            return response()->json(['message' => 'Quote tidak ditemukan.'], 404);
        }
        return response()->json($quote);
    }
}
