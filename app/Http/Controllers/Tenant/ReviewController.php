<?php

namespace App\Http\Controllers\Tenant;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = DB::table('reviews')->orderBy('created_at', 'desc')->get();
        return view('tenant.reviews.index', compact('reviews'));
    }
}



