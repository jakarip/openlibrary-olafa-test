<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EProceedingModel extends Model
{
    protected $table = 'journal_eproc_edition'; 
    protected $primaryKey = 'eproc_edition_id';
    public $timestamps = false; // Disable timestamps

    protected $fillable = [
        'nama', 
        'datestart', 
        'datefinish', 
        'year'
    ];

    public function getLastEprocEdition()
    {
        return DB::select("select * from journal_eproc_edition order by eproc_edition_id desc limit 1");
    }
    
    public static function getWorkflowDocuments()
    {
        return DB::table('workflow_document as wdd')
            ->select(
                'wdd.id as wddid',
                'wrs.name',
                'm.master_data_user',
                'ki.code',
                'ki.id',
                'wdd.title',
                'm.master_data_fullname',
                'ki.editor',
                'wds.state_id',
                'wdd.latest_state_id',
                'wdd.updated_by'
            )
            ->leftJoin('knowledge_item as ki', 'ki.title', '=', 'wdd.title')
            ->leftJoin('workflow_state_sort_id as wss', 'wss.id_state', '=', 'wdd.latest_state_id')
            ->leftJoin('member as m', 'wdd.member_id', '=', 'm.id')
            ->leftJoin('workflow_document_state as wds', 'wds.document_id', '=', 'wdd.id')
            ->leftJoin('workflow_state as wrs', 'wrs.id', '=', 'wds.state_id')
            ->leftJoin('t_mst_prodi as tp', 'tp.C_KODE_PRODI', '=', 'm.master_data_course')
            ->leftJoin('t_mst_fakultas as tf', 'tf.C_KODE_FAKULTAS', '=', 'tp.C_KODE_FAKULTAS')
            ->where('wdd.workflow_id', 1)
            // ->whereIn('wds.state_id', [4])
            ->whereIn('wds.state_id', [4, 3, 52, 53, 64, 91])
            // ->whereIn('wdd.latest_state_id', [3])
            ->whereIn('wdd.latest_state_id', [3, 4, 5, 52, 53, 64, 91])
            ->whereIn('wds.id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('workflow_document_state')
                    ->whereColumn('document_id', 'wdd.id')
                    ->where('state_id', '!=', 5);
            })
            ->groupBy(
                'wdd.id',
                'wrs.name',
                'm.master_data_user',
                'ki.code',
                'ki.id',
                'wdd.title',
                'm.master_data_fullname',
                'ki.editor',
                'wds.state_id',
                'wdd.latest_state_id',
                'wdd.updated_by'
            )
            ->orderBy('wds.state_id')
            // ->take(20)
            ->get();
    }

    public function getEprocEdition()
    { 
        return DB::table('journal_eproc_edition')
            ->orderBy('eproc_edition_id', 'desc')
            ->get(); 
    } 

    public function getEprocEditionById($id)
    {
        return DB::table('journal_eproc_edition')->where('eproc_edition_id', $id)->get();
    }

    public function getEprocList()
    { 
        return DB::table('journal_eproc_list')
            ->get(); 
    } 

    public function getArchiveJurnalStatusByKodeJur($jurusan, $state, $datestart, $datefinish)
    {
        $query = DB::table('workflow_document as wdd')
            ->select(
                'tf.nama_fakultas',
                'tp.nama_prodi',
                'm.master_data_user',
                'ki.code',
                'ki.id',
                'ki.title',
                'm.master_data_fullname',
                'ki.editor',
                'wdd.latest_state_id',
                'wdd.updated_by'
            )
            ->leftJoin('knowledge_item as ki', 'ki.title', '=', 'wdd.title')
            ->leftJoin('workflow_state_sort_id as wss', 'wss.id_state', '=', 'wdd.latest_state_id')
            ->leftJoin('member as m', 'wdd.member_id', '=', 'm.id')
            ->leftJoin('workflow_document_state as wds', 'wds.document_id', '=', 'wdd.id')
            ->leftJoin('t_mst_prodi as tp', 'tp.C_KODE_PRODI', '=', 'm.master_data_course')
            ->leftJoin('t_mst_fakultas as tf', 'tf.C_KODE_FAKULTAS', '=', 'tp.C_KODE_FAKULTAS')
            ->where('wdd.workflow_id', 1)
            ->whereBetween('wdd.created_at', ["$datestart 00:00:00", "$datefinish 23:59:59"]);

        if ($jurusan != "") {
            $query->where('m.master_data_course', $jurusan);
        }

        if ($state == '5') {
            $query->where('wds.state_id', 5)
                ->groupBy([
                    'wdd.member_id',
                    'tf.nama_fakultas',
                    'tp.nama_prodi',
                    'm.master_data_user',
                    'ki.code',
                    'ki.id',
                    'ki.title',
                    'm.master_data_fullname',
                    'ki.editor',
                    'wdd.latest_state_id',
                    'wdd.updated_by'
                ])
                ->orderBy('tf.nama_fakultas')
                ->orderBy('tp.nama_prodi');
        } else {
            $query->where('wds.state_id', $state)
                ->where('wdd.latest_state_id', 5)
                ->whereIn('wds.id', function($subQuery) {
                    $subQuery->select(DB::raw('MAX(id)'))
                        ->from('workflow_document_state')
                        ->whereColumn('document_id', 'wdd.id')
                        ->where('state_id', '!=', 5);
                })
                ->groupBy([
                    'wdd.member_id',
                    'tf.nama_fakultas',
                    'tp.nama_prodi',
                    'm.master_data_user',
                    'ki.code',
                    'ki.id',
                    'ki.title',
                    'm.master_data_fullname',
                    'ki.editor',
                    'wdd.latest_state_id',
                    'wdd.updated_by'
                ])
                ->orderBy('tf.nama_fakultas')
                ->orderBy('tp.nama_prodi');
        }

        return $query->get();
    }

    public function totalarchivejurnalstatusbykodejur($jurusan, $state, $datestart, $datefinish)
    { 
        $query = DB::table('workflow_document as wdd')
        ->select(DB::raw('count(nama_fakultas) as total'))
        ->leftJoin('workflow_document_file as wf', 'wdd.id', '=', 'wf.document_id')
        ->leftJoin('knowledge_item as ki', 'ki.title', '=', 'wdd.title')
        ->leftJoin('workflow_state_sort_id as wss', 'wss.id_state', '=', 'wdd.latest_state_id')
        ->leftJoin('member as m', 'wdd.member_id', '=', 'm.id')
        ->leftJoin('workflow_document_state as wds', 'wds.document_id', '=', 'wdd.id')
        ->leftJoin('t_mst_prodi as tp', 'tp.C_KODE_PRODI', '=', 'm.master_data_course')
        ->leftJoin('t_mst_fakultas as tf', 'tf.C_KODE_FAKULTAS', '=', 'tp.C_KODE_FAKULTAS')
        ->where('wdd.workflow_id', 1)
        ->whereBetween('wdd.created_at', ["$datestart 00:00:00", "$datefinish 23:59:59"])
        ->where('wds.state_id', $state)
        ->where('wdd.latest_state_id', 5)
        ->whereIn('wds.id', function($query) {
            $query->select(DB::raw('max(id)'))
                ->from('workflow_document_state')
                ->whereColumn('document_id', 'wdd.id')
                ->where('state_id', '!=', 5);
        });

        if (!empty($jurusan)) {
            $query->where('m.master_data_course', $jurusan);
        }

        $query->groupBy('wdd.member_id')
            ->orderBy('nama_fakultas')
            ->orderBy('nama_prodi');


        $results = $query->get();

        $total = $results->count();

        return $total;
    }  

    public function totaldocbykodejurandstate($jurusan, $state, $datestart, $datefinish)
    {
        $query = DB::table('workflow_document as wdd')
        ->select(DB::raw('count(wdd.id) as total'))
        ->leftJoin('workflow_state_sort_id as wss', 'wss.id_state', '=', 'wdd.latest_state_id')
        ->leftJoin('member as m', 'wdd.member_id', '=', 'm.id')
        ->leftJoin('workflow_document_file as wf', 'wdd.id', '=', 'wf.document_id')
        ->where('wdd.workflow_id', 1)
        ->where('wdd.latest_state_id', $state)
        ->whereIn('wss.id_ws', function($query) {
            $query->select(DB::raw('max(id_ws)'))
                ->from('workflow_document as wd')
                ->leftJoin('workflow_state_sort_id as wid', 'wid.id_state', '=', 'wd.latest_state_id')
                ->whereColumn('wd.member_id', 'wdd.member_id')
                ->where('wd.workflow_id', 1);
        })
        ->whereBetween('wdd.created_at', ["$datestart 00:00:00", "$datefinish 23:59:59"]);

        if (!empty($jurusan)) {
            $query->where('m.master_data_course', $jurusan);
        }

        $query->groupBy('wdd.member_id')
            ->orderBy('wdd.member_id');

        $results = $query->get();

        $total = $results->count();

        return $total;
    }
    
    public function getEprocListById($id)
    {
        return DB::table('journal_eproc_list')
            ->where('list_id', $id)
            ->get();
    }

    public function getProdiByEprocList($filter)
    {
        return DB::table('t_mst_prodi')
            ->select('jenis_eproc', 'c_kode_prodi', 'nama_prodi', 'nama_fakultas')
            ->leftJoin('t_mst_fakultas', 't_mst_prodi.c_kode_fakultas', '=', 't_mst_fakultas.c_kode_fakultas')
            ->where('t_mst_prodi.c_kode_fakultas', $filter)
            ->orderBy('jenis_eproc')
            ->orderBy('nama_fakultas')
            ->orderBy('nama_prodi')
            // ->limit(5)
            ->get();
    }

    public function totaltamasukbykodejur($jurusan, $datestart, $datefinish)
    {
        $query = DB::table('workflow_document as wdd')
            ->select(DB::raw('count(master_data_user) as total'))
            ->leftJoin('workflow_state_sort_id as wss', 'wss.id_state', '=', 'wdd.latest_state_id')
            ->leftJoin('member as m', 'wdd.member_id', '=', 'm.id')
            ->leftJoin('workflow_state as ws', 'ws.id', '=', 'wdd.latest_state_id')
            ->where('wdd.workflow_id', 1)
            ->whereBetween('wdd.created_at', ["$datestart 00:00:00", "$datefinish 23:59:59"])
            ->whereIn('wss.id_ws', function($query) {
                $query->select(DB::raw('max(id_ws)'))
                    ->from('workflow_document as wd')
                    ->leftJoin('workflow_state_sort_id as wid', 'wid.id_state', '=', 'wd.latest_state_id')
                    ->whereColumn('wd.member_id', 'wdd.member_id')
                    ->where('wd.workflow_id', 1);
            });

        if (!empty($jurusan)) {
            $query->where('m.master_data_course', $jurusan);
        }

        $query->groupBy('wdd.member_id')
            ->orderBy('wdd.member_id');

        $results = $query->get();

        $total = $results->count();
        // dd($totalSum);

        return $total;
    }

    public function totaljurnalmasukbykodejur($jurusan, $datestart, $datefinish)
    {
        $query = DB::table('workflow_document as wdd')
            ->select(DB::raw('count(wdd.id) as total'))
            ->leftJoin('workflow_state_sort_id as wss', 'wss.id_state', '=', 'wdd.latest_state_id')
            ->leftJoin('workflow_document_file as wf', 'wdd.id', '=', 'wf.document_id')
            ->leftJoin('member as m', 'wdd.member_id', '=', 'm.id')
            ->where('wdd.workflow_id', 1)
            ->where('wf.upload_type_id', 16)
            ->whereBetween('wdd.created_at', ["$datestart 00:00:00", "$datefinish 23:59:59"])
            ->whereIn('wss.id_ws', function($query) {
                $query->select(DB::raw('max(id_ws)'))
                    ->from('workflow_document as wd')
                    ->leftJoin('workflow_state_sort_id as wid', 'wid.id_state', '=', 'wd.latest_state_id')
                    ->whereColumn('wd.member_id', 'wdd.member_id')
                    ->where('wd.workflow_id', 1);
            });

        if (!empty($jurusan)) {
            $query->where('m.master_data_course', $jurusan);
        }

        $query->groupBy('wdd.member_id')
            ->orderBy('wdd.member_id');

        $results = $query->get();

        // Calculate the total sum of all 'total' values
        $total = $results->count();

        return $total;
    }

    public function gettamasukbykodejur($jurusan, $datestart, $datefinish)
    {
        return DB::table('workflow_document as wdd')
            ->select('m.master_data_user', 'm.master_data_fullname', 'wdd.title', 'ws.name as state_name')
            ->leftJoin('workflow_state_sort_id as wss', 'wss.id_state', '=', 'wdd.latest_state_id')
            ->leftJoin('member as m', 'wdd.member_id', '=', 'm.id')
            ->leftJoin('workflow_state as ws', 'ws.id', '=', 'wdd.latest_state_id')
            ->where('wdd.workflow_id', 1)
            ->where('m.master_data_course', $jurusan)
            ->whereBetween('wdd.created_at', ["$datestart 00:00:00", "$datefinish 23:59:59"])
            ->whereIn('wss.id_ws', function ($query) {
                $query->select(DB::raw('max(id_ws)'))
                    ->from('workflow_document as wd')
                    ->leftJoin('workflow_state_sort_id as wid', 'wid.id_state', '=', 'wd.latest_state_id')
                    ->whereColumn('wd.member_id', 'wdd.member_id')
                    ->where('workflow_id', 1);
            })
            ->groupBy('wdd.member_id', 'm.master_data_user', 'm.master_data_fullname', 'wdd.title', 'ws.name')
            ->orderBy('wdd.member_id')
            ->get();
    }

    public function getjurnalmasukbykodejur($jurusan, $datestart, $datefinish)
    {
        return DB::table('workflow_document as wdd')
            ->select('wdd.id', 'm.master_data_user', 'm.master_data_fullname', 'wdd.title')
            ->leftJoin('workflow_state_sort_id as wss', 'wss.id_state', '=', 'wdd.latest_state_id')
            ->leftJoin('workflow_document_file as wf', 'wdd.id', '=', 'wf.document_id')
            ->leftJoin('member as m', 'wdd.member_id', '=', 'm.id')
            ->where('wdd.workflow_id', 1)
            ->where('wf.upload_type_id', 16)
            ->where('m.master_data_course', $jurusan)
            ->whereBetween('wdd.created_at', ["$datestart 00:00:00", "$datefinish 23:59:59"])
            ->whereIn('wss.id_ws', function ($query) {
                $query->select(DB::raw('max(id_ws)'))
                    ->from('workflow_document as wd')
                    ->leftJoin('workflow_state_sort_id as wid', 'wid.id_state', '=', 'wd.latest_state_id')
                    ->whereColumn('wd.member_id', 'wdd.member_id')
                    ->where('workflow_id', 1);
            })
            ->groupBy('wdd.member_id', 'wdd.id', 'm.master_data_user', 'm.master_data_fullname', 'wdd.title')
            ->orderBy('wdd.member_id')
            ->get();
    }

    public function getdocbykodejurandstate($jurusan, $state, $datestart, $datefinish)
    {
        return DB::table('workflow_document as wdd')
            ->select(
                'wdd.id',
                'm.master_data_user',
                'm.master_data_fullname',
                'wdd.title',
                DB::raw('(SELECT count(*) FROM free_letter WHERE member_number = m.master_data_user) as free_letter'),
                DB::raw('(SELECT count(*) FROM workflow_document_file WHERE document_id = wdd.id AND upload_type_id = 83) as file')
            )
            ->leftJoin('workflow_state_sort_id as wss', 'wss.id_state', '=', 'wdd.latest_state_id')
            ->leftJoin('member as m', 'wdd.member_id', '=', 'm.id')
            ->where('wdd.workflow_id', 1)
            ->where('wdd.latest_state_id', $state)
            ->where('m.master_data_course', $jurusan)
            ->whereBetween('wdd.created_at', ["$datestart 00:00:00", "$datefinish 23:59:59"])
            ->whereIn('wss.id_ws', function ($query) {
                $query->select(DB::raw('max(id_ws)'))
                    ->from('workflow_document as wd')
                    ->leftJoin('workflow_state_sort_id as wid', 'wid.id_state', '=', 'wd.latest_state_id')
                    ->whereColumn('wd.member_id', 'wdd.member_id')
                    ->where('workflow_id', 1);
            })
            ->groupBy('wdd.member_id', 'wdd.id', 'm.master_data_user', 'm.master_data_fullname', 'wdd.title')
            ->orderBy('free_letter', 'desc')
            ->get();
    }

    public function getjurnalpublishbykodejur($jurusan, $datestart, $datefinish)
    {
        return DB::table('journal_eproc')
            ->select('nim as master_data_user', 'nama as master_data_fullname', 'judul as title', 'eproc_id as id')
            ->where('kode_prodi', $jurusan)
            ->whereBetween('created_date', ["$datestart 00:00:00", "$datefinish 23:59:59"])
            ->where('file', '!=', '')
            ->groupBy('nim', 'nama', 'judul', 'eproc_id')
            ->orderBy('nama')
            ->get();
    }

}
