<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member\Member;

class UserDataController extends Controller
{
    public function index()
    {
        $member = Auth::user();

        return view('user.profile', compact('member'));
    }

    public function dt(Request $request)
    {
    }
}

