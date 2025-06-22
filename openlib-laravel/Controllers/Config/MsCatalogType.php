<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Catalog\TypeModel;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class MsCatalogType extends Controller
{
    public function index()
    {   
        if(!auth()->can('config-catalog-type.view')){
            return redirect('/home');
        }

        $members = DB::table('member_type')->select('id', 'name')->get();

        return view('config.catalog.type', [
            'members' => $members,
        ]);
    }

    public function dt(Request $request)
    {

        $data = TypeModel::getTypeWithMemberTypes();

        // Menggunakan DataTables
        return datatables($data)->addColumn('action', function ($db) {
            
            if(auth()->canAtLeast(array('config-catalog-type.edit','config-catalog-type.delete'))){
                $btn = '<div class="btn-group">
                    <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                ';
                if(auth()->can('config-catalog-type.update')){
                    $btn .='<li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>';
                }
                if(auth()->can('config-catalog-type.delete')){
                    $btn .='<li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>';
                }
                $btn.=' </ul>
                </div>';
            }
            return $btn;
        })
            ->rawColumns(['action'])->toJson();
    }

    public function getbyid(Request $request)
    {
        try {
            // Ambil data dari knowledge_type berdasarkan ID
            $type = TypeModel::find($request->id);

            if (!$type) {
                return response()->json(['status' => 'error', 'message' => __('common.message_not_found')], 404);
            }

            // Ambil member_types yang terkait berdasarkan knowledge_type_id
            $memberTypes = DB::table('member_type_permission AS mtp')
                ->join('member_type AS mt', 'mtp.member_type_id', '=', 'mt.id')
                ->where('mtp.knowledge_type_id', $request->id)
                ->select('mt.id', 'mt.name') // Pilih kolom yang diperlukan
                ->get();

            // Ambil semua member untuk menampilkan yang tidak terasosiasi
            $allMembers = DB::table('member_type')->select('id', 'name')->get();

            // Kembalikan data dalam format JSON
            return response()->json([
                'status' => 'success',
                'type' => $type,
                'member_types' => $memberTypes,
                'all_members' => $allMembers // Menambahkan semua anggota
            ]);
        } catch (\Throwable $th) {
            // Jika terjadi kesalahan, kembalikan response dengan error
            return response()->json(['status' => 'error', 'message' => __('common.message_error_title'), 'error' => $th->getMessage()], 500);
        }
    }

    public function save(Request $request)
    {
        try {
            $inp = $request->inp;

            // Jika 'active' tidak ada, set ke 0
            if (!isset($inp['active'])) {
                $inp['active'] = 0;
            }

            // Cek apakah nama sudah ada di database kecuali data yang sedang diupdate
            $existingRecord = TypeModel::where('name', $inp['name'])
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
                    'message' =>  __('config.catalog_type.message_name_duplicate')
                ]);
            }

            // Ambil data dengan jumlah katalog
            $subjectWithItemCount = TypeModel::getTypeWithMemberTypes()
                ->where('id', $request->id)
                ->first();

            // Jika status ingin dinonaktifkan (active = 0), cek jumlah_katalog
            if ($inp['active'] == 0 && $subjectWithItemCount && $subjectWithItemCount->item_count >  0) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('config.catalog_type.message_delete_inactive')
                ]);
            }

            // Cek apakah sudah ada data yang ada atau buat baru
            $dbs = TypeModel::find($request->id) ?? new TypeModel();

            // Assign data dari input ke model
            foreach ($inp as $key => $value) {
                $dbs->$key = $value;
            }

            // Set created_by dan updated_by jika data baru
            if (!$dbs->id) {
                $dbs->created_by = session()->get('userData')->id;
                $dbs->updated_by = session()->get('userData')->id;
            }

            // Simpan data ke database
            $dbs->save();

            // Proses untuk menyimpan member_type
            $associatedMembers = json_decode($request->associated_members, true); // Ambil data anggota yang diasosiasikan
            $unassociatedMembers = $request->unassociated_members; // Ambil data anggota yang tidak diasosiasikan

            // Hapus semua relasi anggota yang ada sebelumnya
            DB::table('member_type_permission')->where('knowledge_type_id', $dbs->id)->delete();

            // Tambahkan relasi anggota yang diasosiasikan
            if ($associatedMembers) {
                foreach ($associatedMembers as $memberId) {
                    // Tambahkan relasi ke member_type_permission
                    DB::table('member_type_permission')->updateOrInsert(
                        ['knowledge_type_id' => $dbs->id, 'member_type_id' => $memberId],
                        []
                    );
                }
            }

            return response()->json(['status' => 'success', 'message' => __('common.message_success_save')]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => __('common.message_failed_save'), 'error' => $th->getMessage()]);
        }
    }


    public function delete(Request $request)
    {

        // dd($request->all());
        $id = $request->id;
        // Ambil data dengan jumlah katalog
        $subjectWithItemCount = TypeModel::getTypeWithMemberTypes()
            ->where('id', $id)
            ->first();

        // Jika jumlah_katalog lebih dari 0, kembalikan respons error
        if ($subjectWithItemCount && $subjectWithItemCount->item_count > 0) {
            return response()->json([
                'status' => 'error',
                'message' => __('config.catalog_type.message_delete_has_catalog')
            ]);
        }

         // Jika active sama dengan 1, kembalikan respons error
        if ($subjectWithItemCount->active == 1) {
            return response()->json([
                'status' => 'error',
                'message' => __('config.catalog_type.message_delete_active')
            ]);
        }

        // Jika tidak ada masalah, lakukan penghapusan
        $data = TypeModel::find($id);

        // Pastikan data ada sebelum mencoba menghapus
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => __('common.message_success_delete')]);
        } else {
            return response()->json(['status' => 'error', 'message' => __('common.message_not_found')]);
        }
    }
}