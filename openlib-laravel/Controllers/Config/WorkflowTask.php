<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\Workflow\WorkflowTaskModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkflowTask extends Controller
{
    public function index(Request $request)
    {
        if(!auth()->can('config-workflow-task.view')){
            return redirect('/home');
        }

        $workflows = DB::table('workflow')->get();
        $workflows_state = DB::table('workflow_state')->get();
        $workflow_id = $request->query('workflow_id');
        $task_id = $request->query('task_id');
        return view('config.workflow.workflowTask', [
            'workflows' => $workflows,
            'workflow_id' => $workflow_id,
            'workflows_state' => $workflows_state,
            'task_id' => $task_id
        ]);
    }

    public function dt()
    {
        $data = (new WorkflowTaskModel())->getDataTable();

        // Menggunakan DataTables
        return datatables($data)->addColumn('action', function ($db) {
            if(auth()->canAtLeast(array('config-workflow-task.edit','config-workflow-task.delete'))){
                    $btn = '<div class="btn-group">
                        <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                    ';
                    if(auth()->can('config-workflow-task.update')){
                        $btn .='<li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>';
                    }
                    if(auth()->can('config-workflow-task.delete')){
                        $btn .='<li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>';
                    }
                    $btn.=' </ul>
                    </div>';
                }
                return $btn;
        })
            ->rawColumns(['action'])->toJson();
    }

    public function getById($id)
    {
        $task = DB::table('workflow_task')
            ->join('workflow_transition', 'workflow_task.id', '=', 'workflow_transition.task_id')
            ->select('workflow_task.*', 'workflow_transition.state_id')
            ->where('workflow_task.id', $id)
            ->first();
        
        // Ambil semua state_id terkait dengan task_id sebagai array
        $stateIds = DB::table('workflow_transition')
            ->where('task_id', $id)
            ->pluck('state_id')
            ->toArray();

        // Menggabungkan data `task` dan `stateIds` dalam satu array untuk dikembalikan
        return [
            'task' => $task,
            'stateIds' => $stateIds
        ];
        

        // return response()->json($task);
    }


    public function save(Request $request)
    {
        try {
            $inp = $request->inp;

            // Retrieve the WorkflowTaskModel based on the request ID
            $dbs = $request->id ? WorkflowTaskModel::find($request->id) : new WorkflowTaskModel();

            // Assign input values to the model dynamically
            $dbs->workflow_id = $inp['workflow_id'];
            $dbs->name = $inp['name'];
            $dbs->description = $inp['description'];
            $dbs->duration = $inp['duration'];
            $dbs->display_order = $inp['display_order'];
            $dbs->next_state_id = $inp['next_state_id']; // Saving next_state_id

            // Check if created_by should be set (on insert)
            if (is_null($dbs->id)) {
                $dbs->created_by = session()->get('userData')->id;
            }

            // Always update the updated_by field
            if (session()->has('userData')) {
                $dbs->updated_by = session()->get('userData')->id;
            }

            // Set timestamps
            $dbs->updated_at = now();
            if (is_null($dbs->created_at)) {
                $dbs->created_at = now();
            }

            // Save the task
            $dbs->save();

            // Hapus state lama sebelum disimpan
            DB::table('workflow_transition')->where('task_id', $dbs->id)->delete();

            // Simpan state_id sebagai array
            if (!empty($inp['state_id'])) {
                foreach ($inp['state_id'] as $stateId) {
                    DB::table('workflow_transition')->insert([
                        'task_id' => $dbs->id,
                        'state_id' => $stateId,
                    ]);
                }
            }

            return response()->json(['status' => 'success', 'message' => 'Success to save data']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Failed to save data: ' . $th->getMessage()]);
        }
    }


    public function delete(Request $request)
    {
        try {
            (new WorkflowTaskModel())->deleteTask($request->id);
            return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Failed to delete data']);
        }
    }
}
