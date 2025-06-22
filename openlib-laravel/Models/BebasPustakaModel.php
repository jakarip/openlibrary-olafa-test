<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class BebasPustakaModel extends Model
{
    use HasFactory;

    public function member($username) {
        $username = addslashes($username);
        return DB::table('member as mm')
            ->join('t_mst_prodi as tmp', 'tmp.c_kode_prodi', '=', 'mm.master_data_course')
            ->select('mm.*', 'mm.id as memberid', 'tmp.*')
            ->where(function($query) use ($username) {
                $query->where('mm.master_data_user', 'like', "%$username%")
                        ->orWhere('mm.master_data_fullname', 'like', "%$username%")
                        ->orWhere('mm.master_data_number', 'like', "%$username%");
            })
            ->whereIn('mm.member_type_id', [5, 6, 9, 10, 12, 25])
            ->orderBy('mm.master_data_user')
            ->get();
    }

    public function dokumen($username) {
        return DB::table('workflow_document as wd')
            ->join('member as wdf', 'wdf.id', '=', 'wd.member_id')
            ->join('workflow_state as ws', 'ws.id', '=', 'wd.latest_state_id')
            ->select('wd.id', 'wd.title', 'ws.name', 'wdf.master_data_user', 'wdf.master_data_fullname', 
                    DB::raw('(select count(*) from workflow_document_file where document_id=wd.id) as jml'), 
                    'wd.latest_state_id')
            ->whereIn('wd.member_id', explode(',', $username))
            ->where('wd.workflow_id', '=', '1')
            ->orderBy('wdf.master_data_user')
            ->orderBy('wd.latest_state_id')
            ->get();
    }

    public function getDocument($id) {
        return DB::table('workflow_document as wd')
            ->join('workflow_document_file as wdf', 'wd.id', '=', 'wdf.document_id')
            ->join('member as mm', 'mm.id', '=', 'wd.member_id')
            ->join('upload_type as ut', 'ut.id', '=', 'wdf.upload_type_id')
            ->select('ut.title', 'wdf.id', 'ut.extension', 'wdf.location', 'mm.master_data_user')
            ->where('wd.id', $id)
            ->orderBy('ut.title')
            ->get();
    }

    public function getFile($id) {
        return DB::table('workflow_document_file as wdf')
            ->join('workflow_document as wd', 'wd.id', '=', 'wdf.document_id')
            ->join('member as mm', 'mm.id', '=', 'wd.member_id')
            ->select('wdf.location', 'mm.master_data_user')
            ->where('wdf.id', $id)
            ->get();
    }

    public function deleteFile($id) {
        return DB::table('workflow_document_file')
            ->where('id', $id)
            ->delete();
    }
    
    public function deleteDocument($id) {
        DB::table('workflow_document')
            ->where('id', $id)
            ->delete();
            
        DB::table('workflow_document_state')
            ->where('document_id', $id)
            ->delete();
            
        DB::table('workflow_document_subject')
            ->where('workflow_document_id', $id)
            ->delete();
            
        return DB::table('workflow_document_file')
            ->where('document_id', $id)
            ->delete();
    }

}
