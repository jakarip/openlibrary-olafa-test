<?php

namespace App\Http\Controllers\OLAFA\EProceeding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EProceedingModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;

class EProceedingGenerationController extends Controller
{
    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        $eproceedingModel = new EProceedingModel();

        $eprcocEditions = $eproceedingModel->getEprocEdition();
        $eprocLists = $eproceedingModel->getEprocList();

        return view('olafa.eProceeding.generate',[
            'eprcocEditions' => $eprcocEditions,
            'eprocLists' => $eprocLists
        ]);
    }

    public function generate(Request $request)
    {
        $eproceedingModel = new EProceedingModel();

        $eproceedingModel->generateEproc($request->id);

        return response()->json(['status' => 'success', 'message' => 'Success to generate data']);
    }
}
