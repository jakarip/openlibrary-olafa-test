<?php

namespace App\Http\Controllers\OLAFA\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EcatalogGenerationController extends Controller
{
    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        return view('olafa.katalog.e-catalog');
    }
}
