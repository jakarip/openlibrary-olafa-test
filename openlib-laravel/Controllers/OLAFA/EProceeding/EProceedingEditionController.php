<?php

namespace App\Http\Controllers\OLAFA\EProceeding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EProceedingModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;


class EProceedingEditionController extends Controller
{
    
    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        return view('olafa.eProceeding.master');
    }

    public function dt()
    {
        $data = EProceedingModel::orderBy('datestart', 'asc')->get();

        return datatables($data)
            ->addColumn('action', function ($db) {
                return '<div class="btn-group">
                    <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->eproc_edition_id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>
                        </ul>
                    </div>';    
                    // <li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->eproc_edition_id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>
                    })
            ->rawColumns(['action'])->toJson();
    }

    public function getById(Request $request)
    {
        return EProceedingModel::find($request->id)->toJson();
    }

    public function save(Request $request)
    {
        try {
            $inp = $request->inp;
            $dbs = EProceedingModel::find($request->id) ?? new EProceedingModel();

            foreach ($inp as $key => $value) {
                $dbs->$key = $value;
            }

            $dbs->year =date('Y', strtotime($inp['datefinish']));

            $dbs->save(); 

            return response()->json(['status' => 'success', 'message' => 'Success to save data']);
        } catch (\Throwable $th) {
            throw $th;
        }

        // return response()->json(['status' => 'error', 'message' => 'Failed to save data']);
    }

    public function delete(Request $request)
    {

        $id = $request->id;
        $dbs = EProceedingModel::find($id);
        $dbs->delete();

        return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
    }
}
