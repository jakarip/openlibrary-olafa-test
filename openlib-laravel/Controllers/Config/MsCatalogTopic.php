<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Catalog\TopicModel;
use App\Models\KnowledgeSubjectModel;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class MsCatalogTopic extends Controller
{
    public function index()
    {   
        if(!auth()->can('config-catalog-topics.view')){
            return redirect('/home');
        }
        return view('config.catalog.topic');
    }

    public function dt(Request $request)
    {
        $data = KnowledgeSubjectModel::getCatalogTopicsWithItemCount();

        // Menggunakan DataTables
        return datatables($data)->addColumn('action', function ($db) {
            if(auth()->canAtLeast(array('config-catalog-topics.edit','config-catalog-topics.delete'))){
                $btn = '<div class="btn-group">
                    <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                ';
                if(auth()->can('config-catalog-topics.update')){
                    $btn .='<li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>';
                }
                if(auth()->can('config-catalog-topics.delete')){
                    $btn .='<li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>';
                }
                $btn.=' </ul>
                </div>';
            }
            return $btn;
        })->rawColumns(['action'])->toJson();
    }

    public function getbyid(Request $request)
    {
        return KnowledgeSubjectModel::find($request->id)->toJson();
    }


    public function save(Request $request)
    {
        try {
            $inp = $request->inp;

            // Jika checkbox tidak diset, default ke 0 (nonaktif)
            if (!isset($inp['active'])) {
                $inp['active'] = 0;
            }

            // Cek apakah nama sudah ada di database kecuali data yang sedang diupdate
            $existingRecord = KnowledgeSubjectModel::where('name', $inp['name'])
                ->where(function ($query) use ($request) {
                    if ($request->id) {
                        // Abaikan pengecekan pada ID yang sedang diupdate
                        $query->where('id', '!=', $request->id);
                    }
                })
                ->first();

            // Jika ada nama yang duplikat, kembalikan respons error
            if ($existingRecord) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('config.topic.message_name_duplicate')
                ]);
            }

            // Ambil data dengan jumlah katalog
            $subjectWithItemCount = KnowledgeSubjectModel::getCatalogTopicsWithItemCount()
                ->where('id', $request->id)
                ->first();

            // Jika status ingin dinonaktifkan (active = 0), cek jumlah_katalog
            if ($inp['active'] == 0 && $subjectWithItemCount && $subjectWithItemCount->jumlah_katalog > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('config.topic.message_delete_inactive')
                ]);
            }

            // Temukan data yang akan diupdate atau buat baru jika tidak ada
            $dbs = KnowledgeSubjectModel::find($request->id) ?? new KnowledgeSubjectModel();

            // Assign input data ke model
            foreach ($inp as $key => $value) {
                $dbs->$key = $value;
            }

            // Set created_by dan updated_by saat pembuatan data baru
            if (!$dbs->id) {
                $dbs->created_by = session()->get('userData')->id;
                $dbs->updated_by = session()->get('userData')->id;
            } else {
                // Set updated_by saat update data
                $dbs->updated_by = session()->get('userData')->id;
            }

            // Simpan data
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
        $subjectWithItemCount = KnowledgeSubjectModel::getCatalogTopicsWithItemCount()
        ->where('id', $id)
        ->first();
        

        // Jika jumlah_katalog lebih dari 0, kembalikan respons error
        if ($subjectWithItemCount && $subjectWithItemCount->jumlah_katalog > 0) {
            return response()->json([
                'status' => 'error',
                'message' => __('config.topic.message_delete_has_catalog')
            ]);
        }
        // Jika active sama dengan 1, kembalikan respons error
        if ($subjectWithItemCount->active == 1) {
            return response()->json([
                'status' => 'error',
                'message' => __('config.topic.message_delete_active')
            ]);
        }

        // Jika tidak ada masalah, lakukan penghapusan
        $data = KnowledgeSubjectModel::find($id);

        // Pastikan data ada sebelum mencoba menghapus
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => __('common.message_success_delete')]);

        } else {
            return response()->json(['status' => 'error', 'message' => __('common.message_not_found')]);
        }
    }
}
