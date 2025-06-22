<?php

namespace App\Http\Controllers\Config;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Member\UserTypeModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class MsUserType extends Controller
{
    public function revenue()
    {
        if(!auth()->can('config-user-type.view')){
            return redirect('/home');
        }

        return view('config.member.userType');
    }

    public function revenue_dt(Request $request) {
        $data = UserTypeModel::getUserTypeWithItemCount();

        return datatables($data)
        ->addColumn('action', function ($db) {
            if(auth()->canAtLeast(array('config-user-type.edit','config-user-type.delete'))){
                $btn = '<div class="btn-group">
                    <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                ';
                if(auth()->can('config-user-type.update')){
                    $btn .='<li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>';
                }
                if(auth()->can('config-user-type.delete')){
                    $btn .='<li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>';
                }
                $btn.=' </ul>
                </div>';
            }
            return $btn;
        })
        ->rawColumns(['action'])
        ->toJson();
    }

    public function getById(Request $request)
    {
        return UserTypeModel::find($request->id)->toJson();
    }

    public function save(Request $request)
    {
        try {
            $inp = $request->inp;

            // Cek apakah nama sudah ada di database kecuali data yang sedang diupdate
            $existingRecord = UserTypeModel::where('name', $inp['name'])
                ->where(function ($query) use ($request) {
                    if ($request->id) {
                        // Abaikan pengecekan pada ID yang sedang diupdate
                        $query->where('id', '!=', $request->id);
                    }
                })
                ->first();
                
            if ($existingRecord) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('config.user_type.message_name_duplicate')
                ]);
            }

            $userTypeWithItemCount = UserTypeModel::getUserTypeWithItemCount()
                ->where('id', $request->id)
                ->first();

            if ($userTypeWithItemCount && $userTypeWithItemCount->total_items > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('config.user_type.message_renamed')
                ]);
            }

            $dbs = UserTypeModel::find($request->id) ?? new UserTypeModel();

            foreach ($inp as $key => $value) {
                $dbs->$key = $value;
            }
            if (!isset($dbs->homepage)) { // Set homepage to 0 if not provided
                $dbs->homepage = 0;
            }
            if (!$dbs->created_by)
                $dbs->created_by = session()->get('userData')->id;

            $dbs->updated_by = session()->get('userData')->id;
            $dbs->save();

            return response()->json(['status' => 'success', 'message' => __('common.message_success_save')]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => __('common.message_error_title'), 'error' => $th->getMessage()], 500);
        }

    }

    public function delete(Request $request)
    {

        $id = $request->id;

        $userTypeWithItemCount = UserTypeModel::getUserTypeWithItemCount()
                ->where('id', $request->id)
                ->first();


        // Jika total_items lebih dari 0, kembalikan respons error
        if ($userTypeWithItemCount && $userTypeWithItemCount->total_items > 0) {
            return response()->json([
                'status' => 'error',
                'message' => __('config.user_type.message_delete_has_member')
            ]);
        }


        $data = UserTypeModel::find($id);

        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => __('common.message_success_delete')]);
        } else {
            return response()->json(['status' => 'error', 'message' => __('common.message_not_found')]);
        }
    }

}
