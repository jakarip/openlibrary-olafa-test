<?php

namespace App\Http\Controllers\OLAFA\Journals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InternationalModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;

class InternationalJournalsController extends Controller
{
    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        return view('olafa.jurnal.jurnalinternasional');
    }

    public function dt()
    {
        $data = DB::table('internationalonline')->get();

        return datatables($data)
            ->addColumn('action', function ($db) {
                return '<div class="btn-group">
                    <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->io_id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>
                        <li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->io_id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>
                    </ul>
                </div>';
            })
            ->rawColumns(['action'])->toJson();
    }

    public function getbyid(Request $request)
    {
        return InternationalModel::find($request->id)->toJson();
    }

    public function save(Request $request)
    {
        try {
            $inp = $request->inp;
            $dbs = InternationalModel::find($request->id) ?? new InternationalModel();

            foreach ($inp as $key => $value) {
                $dbs->$key = $value;
            }

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

        $data = InternationalModel::find($id);
        $data->delete();

        return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
    }
}
