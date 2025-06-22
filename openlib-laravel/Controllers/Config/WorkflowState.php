<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkflowStateModel;
use Illuminate\Support\Facades\DB;
use App\Models\WorkflowModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class WorkflowState extends Controller
{
    public function index(Request $request)
    {
        if(!auth()->can('config-state-designer.view')){
            return redirect('/home');
        }

        $workflows = DB::table('workflow')->select('id', 'name')->get();
        $workflow_id = $request->query('workflow_id');
        $state_id = $request->query('state_id');

        return view('config.workflow.workflowState', [
            'workflows' => $workflows,
            'workflow_id' => $workflow_id,
            'state_id' => $state_id
        ]);
    }

    public function dt(Request $request)
    {
        $data = WorkflowStateModel::getWorkflowStatesWithWorkflow();

        // Menggunakan DataTables
        return datatables($data)->addColumn('action', function ($db) {
            if(auth()->canAtLeast(array('config-state-designer.edit','config-state-designer.delete'))){
                    $btn = '<div class="btn-group">
                        <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                    ';
                    if(auth()->can('config-state-designer.update')){
                        $btn .='<li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>';
                    }
                    if(auth()->can('config-state-designer.delete')){
                        $btn .='<li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>';
                    }
                    $btn.=' </ul>
                    </div>';
                }
                return $btn;
        })
            ->rawColumns(['action'])->toJson();
    }

    public function getById(Request $request, $id)
    {
        $workflowState = DB::table('workflow_state')
            ->join('workflow', 'workflow_state.workflow_id', '=', 'workflow.id')
            ->select('workflow_state.*', 'workflow.name as workflow_name')
            ->where('workflow_state.id', $id)
            ->first();

        return response()->json($workflowState);
    }

    public function save(Request $request)
    {
        try {
            
            $inp = $request->inp;
            $dbs = $request->id ? WorkflowStateModel::find($request->id) : new WorkflowStateModel();

            foreach ($inp as $key => $value) {
                $dbs->$key = $value;
            }

            if (is_null($dbs->id)) {
                $dbs->created_by = session()->get('userData')->id;
            }
            
            if (session()->has('userData')) {
                $dbs->updated_by = session()->get('userData')->id;
            }

            $dbs->updated_at = now(); 
            if (is_null($dbs->created_at)) {
                $dbs->created_at = now(); 
            }

            // dd($dbs);
            $dbs->save();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan']);
        } catch (\Throwable $th) {
            
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $th->getMessage()]);
        }
    }

    public function delete(Request $request) {

        $id = $request->id;

        $data = WorkflowStateModel::find($id);
        $data->delete();

        return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
    }
}
