<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use App\Models\Room\TermconditionModel;
use Illuminate\Http\Request;

class Termcondition extends Controller
{
    public function index()
    {
        if(!auth()->can('room-termcondition.view')){
            return redirect('/home');
        }

        return view('room.termCondition');
    }

    public function dt(Request $request)
    {
        $data = (new TermconditionModel())->getTermCondition();

        return datatables($data)
            ->addColumn('action', function ($db) {
                if(auth()->canAtLeast(array('room-termcondition.edit','room-termcondition.delete'))){
                    $btn = '<div class="btn-group">
                        <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                    ';
                    if(auth()->can('room-termcondition.update')){
                        $btn .='<li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>';
                    }
                    if(auth()->can('room-termcondition.delete')){
                        $btn .='<li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>';
                    }
                    $btn.=' </ul>
                    </div>';
                }
                return $btn;
            })
            ->addColumn('information', function($db) {
                return $db->information;
            })
            ->addColumn('information_en', function($db) {
                return $db->information_en;
            })
           ->rawColumns(['action', 'information', 'information_en'])->toJson();
    }

    public function getbyid(Request $request, $id = null)
    {
        $id = $id ?? $request->id;
        return TermconditionModel::find($id);
    }

    public function get(Request $request, $id = null)
    {
        $id = $id ?? $request->id;
        return response()->json(TermconditionModel::find($id));
    }

    public function save(Request $request)
    {
        try {
            $inp = $request->inp;
            $dbs = TermconditionModel::find($request->id) ?? new TermconditionModel();

            // Simpan data editor (HTML)
            $dbs->information = $inp['information'] ?? '';
            $dbs->information_en = $inp['information_en'] ?? '';
            $dbs->term_sequence = $inp['term_sequence'] ?? null;

            $dbs->save();

            return response()->json(['status' => 'success', 'message' => 'Success to save data']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Failed to save data', 'error' => $th->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $data = TermconditionModel::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
        }
        return response()->json(['status' => 'error', 'message' => 'Data not found']);
    }
}
