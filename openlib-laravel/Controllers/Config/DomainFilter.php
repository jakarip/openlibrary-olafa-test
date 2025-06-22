<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DomainFilter\DomainFilterModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class DomainFilter extends Controller
{
    public function index()
    {
        return view('config.domainFilter.domainFilter');
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
