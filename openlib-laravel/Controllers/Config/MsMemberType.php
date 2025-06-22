<?php

namespace App\Http\Controllers\Config;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Member\MemberTypeModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class MsMemberType extends Controller
{
    public function index()
    {
        if(!auth()->can('config-user-type.view')){
            return redirect('/home');
        }

        return view('config.member.memberType');
    }

    public function dt(Request $request) {}

    public function getbyid(Request $request) {}

    public function save(Request $request) {}

    public function delete(Request $request) {}
}
