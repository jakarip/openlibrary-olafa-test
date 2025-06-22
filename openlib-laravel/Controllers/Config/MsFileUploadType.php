<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UploadTypeModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class MsFileUploadType extends Controller
{
    public function index()
    {
        if(!auth()->can('file-upload-type.view')){
            return redirect('/home');
        }


        // Fetching the members from the database (name and id)
        $members = DB::table('member_type')->select('id', 'name')->get();

        // Fetch selected member types for download and read-only access (for all upload types)
        $memberTypeUpload = DB::table('member_type_upload_type')->pluck('member_type_id')->toArray();
        $memberTypeReadOnly = DB::table('member_type_upload_type_readonly')->pluck('member_type_id')->toArray();

        // Passing the members and the selected member types to the view
        return view('config.catalog.fileUploadtype', [
            'members' => $members,
            'memberTypeUpload' => $memberTypeUpload,
            'memberTypeReadOnly' => $memberTypeReadOnly,
        ]);
    }

    public function dt(Request $request)
    {
        $data = UploadTypeModel::getUploadTypeWithMemberAccess();

        // Menggunakan DataTables
        return datatables($data)->addColumn('action', function ($db) {
            if(auth()->canAtLeast(array('file-upload-type.edit','file-upload-type.delete'))){
                    $btn = '<div class="btn-group">
                        <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                    ';
                    if(auth()->can('file-upload-type.update')){
                        $btn .='<li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>';
                    }
                    if(auth()->can('file-upload-type.delete')){
                        $btn .='<li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>';
                    }
                    $btn.=' </ul>
                    </div>';
                }
                return $btn;
        })->rawColumns(['action'])->toJson();
    }

    public function getById(Request $request)
    {
        try {// Fetching upload type by ID
            $uploadType = UploadTypeModel::find($request->id);
    
            if ($uploadType) {
                // Fetch all members
                $memberTypes = DB::table('member_type')->select('id', 'name')->get();
    
                // Fetch member_type for upload and readonly access
                $memberTypeUpload = DB::table('member_type_upload_type')
                    ->where('upload_type_id', $uploadType->id)
                    ->pluck('member_type_id')
                    ->toArray();
    
                $memberTypeReadOnly = DB::table('member_type_upload_type_readonly')
                    ->where('upload_type_id', $uploadType->id)
                    ->pluck('member_type_id')
                    ->toArray();
    
                // Return response with relevant data
                return response()->json([
                    'uploadType' => $uploadType,
                    'members' => $memberTypes,
                    'memberTypeUpload' => $memberTypeUpload,
                    'memberTypeReadOnly' => $memberTypeReadOnly
                ]);
            }
    }
        catch (\Throwable $th) {
             // Jika terjadi kesalahan, kembalikan response dengan error
            return response()->json(['status' => 'error', 'message' => __('common.message_error_title'), 'error' => $th->getMessage()], 500);
        }
        
    }



    public function save(Request $request)
    {
        try {
                $inp = $request->except(['readOnlyMembers', 'downloadMembers', 'id', '_token']);
                $inp['is_secure'] = isset($inp['is_secure']) ? 1 : 0;

                // Ensure ID is present for updates
                $dbs = $request->id ? UploadTypeModel::find($request->id) : new UploadTypeModel();

                foreach ($inp as $key => $value) {
                    $dbs->$key = $value;
                }

                if (is_null($dbs->id)) {
                    $dbs->created_by = session()->get('userData')->id;
                }
                if (session()->has('userData')) {
                    $dbs->updated_by = session()->get('userData')->id;
                }
                $dbs->updated_at = now(); // Set updated_at to the current timestamp
                if (is_null($dbs->created_at)) {
                    $dbs->created_at = now(); // Set created_at to the current timestamp if it's a new record
                }

                // Save the upload type
                $dbs->save();

                // Handle member access
                $this->saveMemberAccess($dbs->id, $request->readOnlyMembers, $request->downloadMembers);

                return response()->json(['status' => 'success', 'message' => __('common.message_success_save')]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => __('common.message_failed_save'), 'error' => $th->getMessage()]);
        }
    }

    protected function saveMemberAccess($uploadTypeId, $readOnlyMembers, $downloadMembers)
    {
        // Sync read-only member types
        DB::table('member_type_upload_type_readonly')->where('upload_type_id', $uploadTypeId)->delete();
        if ($readOnlyMembers) {
            foreach (json_decode($readOnlyMembers) as $memberId) {
                DB::table('member_type_upload_type_readonly')->insert([
                    'upload_type_id' => $uploadTypeId,
                    'member_type_id' => $memberId,
                ]);
            }
        }

        // Sync downloadable member types
        DB::table('member_type_upload_type')->where('upload_type_id', $uploadTypeId)->delete();
        if ($downloadMembers) {
            foreach (json_decode($downloadMembers) as $memberId) {
                DB::table('member_type_upload_type')->insert([
                    'upload_type_id' => $uploadTypeId,
                    'member_type_id' => $memberId,
                ]);
            }
        }
    }

    public function delete(Request $request)
    {
        // Validate and delete the upload type
        $uploadType = UploadTypeModel::find($request->id);
        if ($uploadType) {
            // Delete related records in member_type_upload_type and member_type_upload_type_readonly
            DB::table('member_type_upload_type')->where('upload_type_id', $request->id)->delete();
            DB::table('member_type_upload_type_readonly')->where('upload_type_id', $request->id)->delete();

            // Now delete the upload type itself
            $uploadType->delete();
            return response()->json(['status' => 'success', 'message' => __('common.message_success_delete')]);
        }
        return response()->json(['status' => 'error', 'message' => __('common.message_not_found')], 404);
    }
}
