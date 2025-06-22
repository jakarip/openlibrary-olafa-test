<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    public function index()
    {
        // dd(auth()->getPermissions());
        // dd(session()->get('permissions'));
        //dd(app()->getLocale());
        return view('dashboard.index');
    }
}
