<?php

namespace App\Http\Controllers\Config;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Member\UserRoleModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class MsUserRole extends Controller
{
    public function index()
    {
        if(!auth()->can('config-member-type.view')){
            return redirect('/home');
        }

        return view('config.member.userRole');
    }

    public function dt(Request $request) {}
}
