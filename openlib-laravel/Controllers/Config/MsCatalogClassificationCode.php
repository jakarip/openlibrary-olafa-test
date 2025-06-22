<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassificationCodeModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class MsCatalogClassificationCode extends Controller
{
    public function index()
    {
        if(!auth()->can('config-catalog-classification.view')){
            return redirect('/home');
        }


        return view('config.catalog.classification');
    }


    public function dt(Request $request)
{
    $data = ClassificationCodeModel::getClassificationWithItemCount();


    return datatables($data)
        ->addColumn('action', function ($db) {
           if(auth()->canAtLeast(array('config-catalog-classification.edit','config-catalog-classification.delete'))){
                    $btn = '<div class="btn-group">
                        <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                    ';
                    if(auth()->can('config-catalog-classification.update')){
                        $btn .='<li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>';
                    }
                    if(auth()->can('config-catalog-classification.delete')){
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


    public function getbyid(Request $request)
    {
        return ClassificationCodeModel::find($request->id)->toJson();
    }

    public function save(Request $request)
    {
        try {
            $inp = $request->inp;
            $dbs = ClassificationCodeModel::find($request->id) ?? new ClassificationCodeModel();

            // Cek apakah code sudah ada di database kecuali data yang sedang diupdate
            $existingRecord = ClassificationCodeModel::where('code', $inp['code'])
                ->where(function ($query) use ($request) {
                    if ($request->id) {
                        // Abaikan pengecekan pada ID yang sedang diupdate
                        $query->where('id', '!=', $request->id);
                    }
                })
                ->first();

            // Jika ada code yang duplikat, kembalikan respons error
            if ($existingRecord) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('config.classification.message_code_duplicate')
                ]);
            }

            foreach ($inp as $key => $value) {
                $dbs->$key = $value;
            }
 
            if(!$dbs->created_by)
            $dbs->created_by = session()->get('userData')->id;
            $dbs->updated_by = session()->get('userData')->id;
            $dbs->tree_left = 0;
            $dbs->tree_right = 0;
            $dbs->tree_level = 0;
            $dbs->save(); 

            return response()->json(['status' => 'success', 'message' => __('common.message_success_save')]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => __('common.message_failed_save'), 'error' => $th->getMessage()]);
        }

    }

    public function delete(Request $request)
    {

        $id = $request->id;

        // Ambil data dengan jumlah katalog
        $subjectWithItemCount = ClassificationCodeModel::getClassificationWithItemCount()
            ->where('id', $id)
            ->first();

        // Jika jumlah_katalog lebih dari 0, kembalikan respons error
        if ($subjectWithItemCount && $subjectWithItemCount->total_items > 0) {
            return response()->json([
                'status' => 'error',
                'message' => __('config.classification.message_delete_has_catalog')
            ]);
        }

        // Jika tidak ada masalah, lakukan penghapusan
        $data = ClassificationCodeModel::find($id);

        // Pastikan data ada sebelum mencoba menghapus
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => __('common.message_success_delete')]);
        } else {
            return response()->json(['status' => 'error', 'message' => __('common.message_not_found')]);
        }
    }
}
