<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataMaster\MsCompetencyModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class MsCompetency extends Controller
{
    public function index()
    {
        return view('config/dataMaster');
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
