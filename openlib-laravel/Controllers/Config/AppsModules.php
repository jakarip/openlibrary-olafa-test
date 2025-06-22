<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppsModulesModel;
use App\Models\AppsPermissionModel;

class AppsModules extends Controller
{
    public function index()
    {
        //(auth()->can('circulation-borrowing.view'));

        return view('config.apps-modules');
    }

    public function dt(Request $request)
    {
        $data = AppsModulesModel::query();

        return datatables($data)
            ->addColumn('action', function ($db) {
                return '<div class="btn-group">
                        <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->am_id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Instrumen</a></li>
                            <li><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->am_id . '\')"><i class="ti ti-trash me-2"></i> Delete Instrumen</a></li>
                        </ul>
                    </div>';
            })
           ->rawColumns(['action'])->toJson();
    }

    public function getbyid(Request $request)
    {
        return AppsModulesModel::find($request->id)->toJson();
    }

    public function save(Request $request)
    {
        $new_record = false;
        try {
            $inp = $request->inp;
            $dbs = AppsModulesModel::find($request->id) ?? new AppsModulesModel(); 

            foreach ($inp as $key => $value) {
                $dbs->$key = $value;
            }

            if(!$dbs->am_id){
                $new_record = true; 
            }

            $dbs->save();

            if($new_record){
                foreach(['create', 'view', 'update', 'delete'] as $action){
                    AppsPermissionModel::firstOrCreate([
                        'ap_action' => ucwords($action),
                        'ap_slug_action' => $action,
                        'ap_slug' => $dbs->am_slug.'.'.$action,
                        'ap_id_modules' => $dbs->am_id
                    ]);
                }
            }

            return response()->json(['status' => 'success', 'message' => 'Success to save data']);
        } catch (\Throwable $th) {
            throw $th;
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to save data']);
    }

    public function delete(Request $request)
    {

        $id = $request->id;

        $data = AppsModulesModel::find($id);
        $data->delete();

        return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
    }
}
