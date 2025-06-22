<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class WorkflowPermission extends Controller
{
    public function index()
    {
    }

    public function dt(Request $request)
    {
    }

    public function update(Request $request)
    {
        try{
            // Validasi input dari form
            $validatedData = $request->validate([
                'state_id' => 'required|integer',
                'member_type_id' => 'required|integer',
                'can_comment' => 'nullable|boolean',
                'can_edit_state' => 'nullable|boolean',
                'can_edit_attribute' => 'nullable|boolean',
                'can_upload' => 'nullable|boolean',
                'can_download' => 'nullable|boolean',
            ]);

            // Pastikan permission dengan id yang diberikan ada
            $permission = DB::table('workflow_state_permission')
                ->where('id', $request->id)
                ->first();

            if (!$permission) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Permission not found.'
                ], 404);
            }

            // Dapatkan workflow_id dari workflow_state berdasarkan state_id
            $workflow = DB::table('workflow_state')
            ->where('id', $validatedData['state_id'])
            ->first();

            if (!$workflow) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Workflow not found.'
                ], 404);
            }

            // Siapkan data untuk diupdate
            $updateData = [
                'state_id' => $validatedData['state_id'],
                'member_type_id' => $validatedData['member_type_id'],
                'can_comment' => $request->has('can_comment') ? 1 : 0,
                'can_edit_state' => $request->has('can_edit_state') ? 1 : 0,
                'can_edit_attribute' => $request->has('can_edit_attribute') ? 1 : 0,
                'can_upload' => $request->has('can_upload') ? 1 : 0,
                'can_download' => $request->has('can_download') ? 1 : 0,
                'updated_by' => auth()->user()->id,
                'updated_at' => now(),
            ];

            // Update data di database
            DB::table('workflow_state_permission')
                ->where('id', $request->id)
                ->update($updateData);
            
            return response()->json([
                'status' => 'success',
                'redirect' => route('workflow-designer.form', ['id' => $workflow->workflow_id]),
                'message' => 'Data berhasil disimpan.'
            ]);
            } catch (\Throwable $th) {
                // Anda bisa menambahkan log atau menangani exception sesuai kebutuhan
                return back()->withErrors(['error' => 'Gagal menyimpan data.']);
            }
        
    }

    public function edit($id)
    {
        // Ambil data permission beserta workflow_id dari workflow_state
        $permission = DB::table('workflow_state_permission')
            ->join('member_type', 'workflow_state_permission.member_type_id', '=', 'member_type.id')
            ->join('workflow_state', 'workflow_state_permission.state_id', '=', 'workflow_state.id') // Join untuk mendapatkan workflow_id
            ->select('workflow_state_permission.*', 
                    'member_type.name as member_type_name', 
                    'workflow_state.workflow_id', // Ambil workflow_id
                    'workflow_state_permission.id AS permission_id')
            ->where('workflow_state_permission.id', $id)
            ->first();

        if (!$permission) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Menggunakan workflow_id yang diambil dari permission
        $workflow_id = $permission->workflow_id;

        // Mendapatkan daftar member types
        $memberTypes = DB::table('member_type')->select('id', 'name')->get();

        $selectedMembers = DB::table('workflow_member_type')
                            ->where('workflow_id', $workflow_id)
                            ->pluck('member_type_id')
                            ->toArray(); 

        // Mendapatkan workflow states berdasarkan workflow_id
        $workflowStates = DB::table('workflow_state')
            ->where('workflow_id', $workflow_id) // Filter berdasarkan workflow_id
            ->select('id', 'name')
            ->get();

        return view('config.workflow.workflowPermissionEdit', compact('permission', 'memberTypes', 'workflowStates', 'workflow_id','selectedMembers'));
    }


    public function showWorkflowPermission($state_id)
    {
        // Ambil data workflow_state_permission berdasarkan state_id
        $workflowPermission = DB::table('workflow_state')
            ->leftJoin('workflow_state_permission', 'workflow_state.id', '=', 'workflow_state_permission.state_id')
            ->leftJoin('member_type', 'workflow_state_permission.member_type_id', '=', 'member_type.id')
            ->where('workflow_state.id', $state_id)
            ->select(
                'workflow_state.id',
                'workflow_state.name',
                'workflow_state.description',
                'workflow_state.rule_type',
                'member_type.name AS member_type_name',
                'workflow_state_permission.can_comment',
                'workflow_state_permission.can_edit_state',
                'workflow_state_permission.can_edit_attribute',
                'workflow_state_permission.can_upload',
                'workflow_state_permission.can_download',
                'workflow_state_permission.id AS permission_id' // Menambahkan id permission
            )
            ->get();

        // Ambil workflow_id dari state yang dipilih
        $workflow_id = DB::table('workflow_state')->where('id', $state_id)->value('workflow_id');

        // Ambil data workflow_state berdasarkan workflow_id
        $workflowStates = DB::table('workflow_state')
            ->where('workflow_id', $workflow_id)
            ->select('id', 'name', 'workflow_id')
            ->get();

        $memberTypes = DB::table('member_type')->select('id', 'name')->get();
        
        $selectedMembers = DB::table('workflow_member_type')
                            ->where('workflow_id', $workflow_id)
                            ->pluck('member_type_id')
                            ->toArray(); 
                            
        return view('config.workflow.workflowPermission', [
            'state_id' => $state_id,
            'workflowPermission' => $workflowPermission,
            'workflowStates' => $workflowStates,
            'memberTypes' => $memberTypes,
            'workflow_id' => $workflow_id,
            'selectedMembers' => $selectedMembers
        ]);
    }

    public function save(Request $request)
    {
        // Validasi input form
        $request->validate([
            'inp.state_id' => 'required|exists:workflow_state,id',
            'inp.member_type_id' => 'required|exists:member_type,id',
        ]);

        try {
            // Ambil data dari request
            $data = [
                'state_id' => $request->inp['state_id'],
                'member_type_id' => $request->inp['member_type_id'],
                'can_comment' => $request->can_comment ? 1 : 0,
                'can_edit_state' => $request->can_edit_state ? 1 : 0,
                'can_edit_attribute' => $request->can_edit_attribute ? 1 : 0,
                'can_upload' => $request->can_upload ? 1 : 0,
                'can_download' => $request->can_download ? 1 : 0,
                'updated_by' => session()->get('userData')->id,
                'updated_at' => now(),
            ];

            // Dapatkan workflow_id dari workflow_state berdasarkan state_id
            $workflow = DB::table('workflow_state')
            ->where('id', $data['state_id'])
            ->first();

            if (!$workflow) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Workflow not found.'
                ], 404);
            }

            if ($request->id) {
                DB::table('workflow_state_permission')
                    ->where('id', $request->id)
                    ->update($data);
            } else {
                $data['created_by'] = session()->get('userData')->id;
                $data['created_at'] = now();
                DB::table('workflow_state_permission')->insert($data);
            }

        return response()->json([
            'status' => 'success',
            'redirect' => route('workflow-designer.form', ['id' => $workflow->workflow_id]),
            'message' => 'Data berhasil disimpan.'
        ]);
        } catch (\Throwable $th) {
            // Anda bisa menambahkan log atau menangani exception sesuai kebutuhan
            return back()->withErrors(['error' => 'Gagal menyimpan data.']);
        }
    }

    public function delete(Request $request)
    {
    }
}
