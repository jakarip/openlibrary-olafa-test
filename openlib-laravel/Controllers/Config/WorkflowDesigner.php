<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workflow\WorkflowDesignerModel;
use App\Models\WorkflowModel;
use App\Models\Workflow\WorkflowTaskModel;
use App\Models\WorkflowStateModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;

class WorkflowDesigner extends Controller
{
    public function index()
    {
        if(!auth()->can('config-workflow-designer.view')){
            return redirect('/home');
        }

        // Fetching the members from the database (name and id)
        $members = DB::table('member_type')->select('id', 'name')->get();
        $documents = DB::table('knowledge_type')->select('id', 'name')->get();
        $files = DB::table('upload_type')->select('id', 'name')->get();

        return view('config.workflow.workflowDesigner',[
            'members' => $members,
            'documents' => $documents,
            'files' => $files
        ]);
    }

    public function dt(Request $request)
    {
        $data = WorkflowModel::with(['startState', 'finalState'])->get();

        // Menggunakan DataTables
        return datatables($data)->addColumn('action', function ($db) {
           if(auth()->canAtLeast(array('config-workflow-designer.edit','config-workflow-designer.delete'))){
                    $btn = '<div class="btn-group">
                        <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                    ';
                    if(auth()->can('config-workflow-designer.update')){
                        $btn .='<li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="' . url('config/workflow-designer/form/' . $db->id) . '"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>';
                    }
                    if(auth()->can('config-workflow-designer.delete')){
                        $btn .='<li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>';
                    }
                    $btn.=' </ul>
                    </div>';
                }
                return $btn;
        })
        ->editColumn('start_state_id', function ($db) {
            return $db->startState ? $db->startState->name : '-'; // Tampilkan nama state atau '-'
        })
        ->editColumn('final_state_id', function ($db) {
            return $db->finalState ? $db->finalState->name : '-'; // Tampilkan nama state atau '-'
        })
        ->rawColumns(['action'])->toJson();
    }

    public function formDetail($id)
    {   
        // Ambil ID dari request
        $workflowId = $id;

        // Ambil data workflow beserta relasi startState dan finalState
        $workflow = WorkflowModel::with(['startState', 'finalState'])
                    ->where('id', $workflowId)
                    ->first();

        // Ambil semua data dari tabel member_type, knowledge_type, dan upload_type
        $members = DB::table('member_type')->select('id', 'name')->get();
        $documents = DB::table('knowledge_type')->select('id', 'name')->get();
        $files = DB::table('upload_type')->select('id', 'name')->get();

        // Ambil data berdasarkan workflow_id dari masing-masing tabel relasi
        $selectedMembers = DB::table('workflow_member_type')
                            ->where('workflow_id', $workflowId)
                            ->pluck('member_type_id')
                            ->toArray();; 

        $selectedDocuments = DB::table('workflow_knowledge_type')
                            ->where('workflow_id', $workflowId)
                            ->pluck('knowledge_type_id')
                            ->toArray();; 

        $selectedFiles = DB::table('workflow_upload_type')
                            ->where('workflow_id', $workflowId)
                            ->pluck('upload_type_id')
                            ->toArray();; 

        $workflowStates = DB::table('workflow_state')
                            ->leftJoin('workflow_state_permission', 'workflow_state.id', '=', 'workflow_state_permission.state_id')
                            ->leftJoin('member_type', 'workflow_state_permission.member_type_id', '=', 'member_type.id')
                            ->where('workflow_state.workflow_id', $workflowId)
                            ->groupBy(
                                'workflow_state.id', 
                                'workflow_state.name', 
                                'workflow_state.description', 
                                'workflow_state.rule_type'
                            )
                            ->select(
                                'workflow_state.id',
                                'workflow_state.name',
                                'workflow_state.description',
                                'workflow_state.rule_type',
                                DB::raw('GROUP_CONCAT(member_type.name) AS member_type_names'),
                                DB::raw('GROUP_CONCAT(workflow_state_permission.can_comment) AS can_comments'),
                                DB::raw('GROUP_CONCAT(workflow_state_permission.can_edit_state) AS can_edit_states'),
                                DB::raw('GROUP_CONCAT(workflow_state_permission.can_edit_attribute) AS can_edit_attributes'),
                                DB::raw('GROUP_CONCAT(workflow_state_permission.can_upload) AS can_uploads'),
                                DB::raw('GROUP_CONCAT(workflow_state_permission.can_download) AS can_downloads'),
                                DB::raw('GROUP_CONCAT(workflow_state_permission.id) AS permission_ids')
                            )
                            ->get();


        // Update the workflowTasks query to group states by task
        $workflowTasks = DB::table('workflow_task')
                        ->leftJoin('workflow_transition', 'workflow_task.id', '=', 'workflow_transition.task_id')
                        ->leftJoin('workflow_state AS from_state', 'workflow_transition.state_id', '=', 'from_state.id')
                        ->leftJoin('workflow_state AS to_state', 'workflow_task.next_state_id', '=', 'to_state.id')
                        ->where('workflow_task.workflow_id', $workflowId)
                        ->groupBy(
                            'workflow_task.id', 
                            'workflow_task.name', 
                            'workflow_task.description', 
                            'workflow_task.duration', 
                            'workflow_task.display_order'
                        )
                        ->select(
                            'workflow_task.id',
                            'workflow_task.name',
                            'workflow_task.description',
                            'workflow_task.duration',
                            'workflow_task.display_order',
                            DB::raw('GROUP_CONCAT(DISTINCT from_state.name SEPARATOR "\n") AS from_state_name'), 
                            DB::raw('GROUP_CONCAT(DISTINCT to_state.name SEPARATOR "\n") AS to_state_name')
                        )
                        ->orderBy('workflow_task.display_order')
                        ->get();


        // dd([
        //     'workflow' => $workflow,
        //     'members' => $members,
        //     'documents' => $documents,
        //     'files' => $files,
        //     'selectedMembers' => $selectedMembers,
        //     'selectedDocuments' => $selectedDocuments,
        //     'selectedFiles' => $selectedFiles,
        //     'workflowStates' => $workflowStates,
        //     'workflowTasks' => $workflowTasks,
        // ]);
        
        // Kirim data ke view
        return view('config.workflow.workflowDetail', [
            'workflow' => $workflow,
            'members' => $members,
            'documents' => $documents,
            'files' => $files,
            'selectedMembers' => $selectedMembers,
            'selectedDocuments' => $selectedDocuments,
            'selectedFiles' => $selectedFiles,
            'workflowStates' => $workflowStates,
            'workflowTasks' => $workflowTasks,
        ]);

    }

    public function save(Request $request)
    {
        try {
            
            // Ambil data dari request, kecuali fields tertentu
            $inp = $request->except(['members', 'documents', 'filesType']);
    
            // Cari atau buat instance baru WorkflowModel
            $dbs = WorkflowModel::find($request->id) ?? new WorkflowModel();
    
            // Isi field sesuai input request
            foreach ($inp as $key => $value) {
                $dbs->$key = $value;
            }
    
            // Default start_state_id dan final_state_id
            $dbs->start_state_id = $request->input('start_state_id', 0);
            $dbs->final_state_id = $request->input('final_state_id', 0);
    
            // Tetapkan created_by dan updated_by
            if (!$dbs->created_by) {
                $dbs->created_by = session()->get('userData')->id;
            }
            $dbs->updated_by = session()->get('userData')->id;
    
            // Simpan data ke workflow
            $dbs->save();
    
            // Simpan data ke tabel terkait
            $this->saveWorkflowRelations($dbs->id, $request->documents, $request->members, $request->filesType);
    
            return response()->json(['status' => 'success', 'message' => 'Success to save data']);
        } catch (\Throwable $th) {
            throw $th;
        }
        return response()->json(['status' => 'error', 'message' => 'Failed to save data']);
    }
    
    // Fungsi untuk menyimpan relasi workflow dengan documents, members, dan files
    protected function saveWorkflowRelations($workflowId, $documents, $members, $filesType)
    {
        // Hapus data lama terlebih dahulu
        DB::table('workflow_knowledge_type')->where('workflow_id', $workflowId)->delete();
        DB::table('workflow_member_type')->where('workflow_id', $workflowId)->delete();
        DB::table('workflow_upload_type')->where('workflow_id', $workflowId)->delete();

        // Simpan data baru ke tabel workflow_knowledge_type
        if ($documents) {
            foreach (json_decode($documents) as $documentId) {
                DB::table('workflow_knowledge_type')->insert([
                    'workflow_id' => $workflowId,
                    'knowledge_type_id' => $documentId,
                ]);
            }
        }

        // Simpan data baru ke tabel workflow_member_type
        if ($members) {
            foreach (json_decode($members) as $memberId) {
                DB::table('workflow_member_type')->insert([
                    'workflow_id' => $workflowId,
                    'member_type_id' => $memberId,
                ]);
            }
        }

        // Simpan data baru ke tabel workflow_upload_type
        if ($filesType) {
            foreach (json_decode($filesType) as $fileId) {
                DB::table('workflow_upload_type')->insert([
                    'workflow_id' => $workflowId,
                    'upload_type_id' => $fileId,
                ]);
            }
        }
    }   

    public function delete(Request $request)
    {

        $workflow = WorkflowModel::find($request->id);

        if ($workflow) {
            // Get all related task IDs and state IDs
            $tasks = DB::table('workflow_task')->where('workflow_id', $request->id)->pluck('id');
            $states = DB::table('workflow_state')->where('workflow_id', $request->id)->pluck('id');

            // Hapus data terkait di tabel relasi
            DB::table('workflow_knowledge_type')->where('workflow_id', $request->id)->delete();
            DB::table('workflow_member_type')->where('workflow_id', $request->id)->delete();
            DB::table('workflow_upload_type')->where('workflow_id', $request->id)->delete();

            // Hapus data di workflow_task
            DB::table('workflow_task')->where('workflow_id', $request->id)->delete();

            // Hapus data di workflow_state
            DB::table('workflow_state')->where('workflow_id', $request->id)->delete();

            // Hapus data di workflow_transition yang berhubungan dengan task dan state yang dihapus
            if ($tasks->isNotEmpty()) {
                DB::table('workflow_transition')->whereIn('task_id', $tasks)->delete();
            }

            if ($states->isNotEmpty()) {
                DB::table('workflow_transition')->whereIn('state_id', $states)->delete();
            }

            // Hapus workflow itu sendiri
            $workflow->delete();

            return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
        }

    }
}
