<?php

namespace App\Http\Controllers\Bebaspustaka;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AmnestyModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
;

class Amnesty extends Controller
{
    public function index(Request $request)
    {
        $amnestyModel = new AmnestyModel();
        $members = $amnestyModel->member($request);
        return view('bebasPustaka.amnesty.index', [
            'members' => $members
        ]);
    }

    public function dt(Request $request)
    {
        $amnestyModel = new AmnestyModel();
        $data = $amnestyModel->getAmnestyData();

        return datatables($data)
            ->addColumn('action', function ($db) {
                return '<div class="btn-group">
                    <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>
                    </ul>
                </div>';
            })
            ->rawColumns(['action'])->toJson();
    }

    public function auto_data(Request $request)
    {
        $query = strtolower($request->input('q'));
        $amnestyModel = new AmnestyModel();
        $dt = $amnestyModel->member($query);

        $arr = array();
        foreach ($dt as $row) {
            $tab['id'] = $row->id;
            $tab['name'] = $row->master_data_user . " - " . $row->master_data_fullname . " (" . $row->NAMA_PRODI . ")";
            $arr[] = $tab;
        }
        return response()->json($arr);
    }

    public function save(Request $request)
    {
        // Retrieve the data from the request
        $username_id = $request->input('username_id');

        // Check if the username_id already exists
        $existingData = AmnestyModel::where('username_id', $username_id)->first();
        if ($existingData) {
            return response()->json(['status' => 'error', 'message' => 'Username ID already exists']);
        }

        // Create a new instance of the model
        $data = new AmnestyModel();
        $data->username_id = $username_id;

        // Save the model to the database
        $data->save();

        // Return a success response
        return response()->json(['status' => 'success', 'message' => 'Success to save data']);
    }

    public function delete(Request $request)
    {

        $id = $request->id;

        $data = AmnestyModel::find($id);
        $data->delete();

        return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
    }
}
