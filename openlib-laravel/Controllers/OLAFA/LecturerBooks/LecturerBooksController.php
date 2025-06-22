<?php

namespace App\Http\Controllers\OLAFA\LecturerBooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LecturerBookModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;;

class LecturerBooksController extends Controller
{
    public function index(Request $request)
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        return view('olafa.lecturerbook.index');
    }

    public function dt(){
        $data = LecturerBookModel::orderBy('press_faculty_unit')
                                ->orderBy('press_author')
                                ->get();

        return datatables($data)
        ->addColumn('action', function ($db) {
            return '<div class="btn-group">
                <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->press_id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>
                    <li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->press_id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>
                </ul>
            </div>';
        })
        ->rawColumns(['action'])->toJson();
    }

    public function getById($id){
        $data = LecturerBookModel::where('press_id', $id)->first();
        return response()->json($data);
    }

    public function save(Request $request){
        try {
            $request->validate([
                'inp.press_barcode' => 'required|string',
                'inp.press_type' => 'required|string',
                'inp.press_title' => 'required|string',
                'inp.press_author' => 'required|string',
                'inp.press_publisher' => 'required|string',
                'inp.press_published_year' => 'required|integer',
                'inp.press_faculty_unit' => 'required|string',
                'inp.press_isbn' => 'required|string',
            ]);

            $inp = $request->inp;
            $dbs = LecturerBookModel::find($request->id) ?? new LecturerBookModel();

            foreach ($inp as $key => $value) {
                $dbs->$key = $value;
            }
            
            $dbs->save(); 

            return response()->json(['status' => 'success', 'message' => 'Success to save data']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }

    public function delete(Request $request){
        $id = $request->id;
        $dbs = LecturerBookModel::where('press_id', $id);
        $dbs->delete();
        
        return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
    }
}
