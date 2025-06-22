<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner\BannerModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class Banner extends Controller
{
    public function index()
    {
        return view('config.banner.banner');
    }

    public function dt(Request $request)
    {
        
    }

    public function getbyid(Request $request)
    {
        
    }

    public function save(Request $request)
    {
        
    }

    public function delete(Request $request)
    {

    }
}
