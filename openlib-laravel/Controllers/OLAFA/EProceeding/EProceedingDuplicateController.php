<?php

namespace App\Http\Controllers\OLAFA\EProceeding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EProceedingModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;

class EProceedingDuplicateController extends Controller
{
    public function index()
    {   
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        $eProceedingModel = new EProceedingModel();
        $editions = $eProceedingModel->getEprocEdition();
        return view('olafa.eProceeding.duplicate',['editions' => $editions]);
    }

    public function dt($id)
    {
        $eProceedingModel = new EProceedingModel();
        $edition = $eProceedingModel->getEprocEditionById($id);

        $data = $eProceedingModel->getArchiveJurnalStatusByKodeJur('','53',$edition[0]->datestart,$edition[0]->datefinish); 
        // dd($data);

        return datatables($data)
            ->addColumn('action', function ($db) {
                return '<div class="btn-group">
                    <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                    </ul>
                </div>';
            })->addColumn('duplicate', function ($db) {
                return '<button type="button" class="btn btn-primary"  style="background-color:#C22E32 ;color:#fff; border:1px solid #C22E32">Not Duplicate</button>';
            })->rawColumns(['action','duplicate'])
            ->toJson();
    }
}
