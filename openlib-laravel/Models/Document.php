<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Document extends Model
{
    protected $table = 'workflow_document';
    protected $primaryKey = 'id';

    protected $fillable = [
        'workflow_id', 
        'course_code',
        'title',
        'member_id',
        'knowledge_subject_id',
        'knowledge_type_id',
        'lecturer_id',
        'lecturer2_id',
        'approved_id',
        'file_upload_terms',
        'status',
        'latest_state_id',
        'abstract_content',
        'created_by',
        'updated_by'
    ];

    public function addCustom($item, $table)
{
    return DB::table($table)->insertGetId($item);
}
    // Query untuk datatables
    public function dtquery($param)
{
    $session = session('user_doc');
    $query = DB::table('workflow_document as wd')
        ->leftJoin('workflow as w', 'w.id', '=', 'wd.workflow_id')
        ->leftJoin('knowledge_subject as ks', 'ks.id', '=', 'wd.knowledge_subject_id')
        ->leftJoin('knowledge_type as kt', 'kt.id', '=', 'wd.knowledge_type_id')
        ->leftJoin('member as m', 'm.id', '=', 'wd.member_id')
        ->leftJoin('workflow_document_state as wds', function ($join) {
            $join->on('wds.document_id', '=', 'wd.id')
                ->whereNull('wds.close_date');
        })
        ->leftJoin('workflow_state as ws', 'ws.id', '=', 'wd.latest_state_id')
        ->leftJoin('member as m2', 'm2.id', '=', 'wds.allowed_member_id')
        ->leftJoin('workflow_state_permission as wsp', function ($join) use ($session) {
            $join->on('wsp.state_id', '=', 'wd.latest_state_id')
                ->where('wsp.member_type_id', '=', $session['membertype']);
        })
        ->select(
            'wd.id as wd_id',
            'w.id as w_id',
            'kt.id as kt_id',
            'm.master_data_user',
            'm.master_data_fullname',
            'wsp.id as wsp_id',
            'wd.created_at as wd_date',
            'ws.name as state_name',
            'wd.status as wd_status',
            'w.name as jenis_workflow',
            'wd.title',
            'w.final_state_id',
            'wd.latest_state_id',
            'wd.member_id as wd_member_id',
            'wd.file_upload_terms',
            'ks.name as subjek',
            'kt.name as jenis_katalog',
            'm2.id as allow_only_id',
            'm2.master_data_user as allow_only_username',
            'm2.master_data_fullname as allow_only_name',
            'wsp.*',
            DB::raw("(select name from member_type where id='".$session['membertype']."') as jenis_member"),
            DB::raw("CASE 
                WHEN wd.member_id = '{$session['id']}' 
                     OR '{$session['membertype']}' = '1' 
                     OR wsp.can_edit_state = 1 
                     OR wsp.can_edit_attribute = 1 THEN 1 
                ELSE 0 
            END as can_view_details"),
            DB::raw("CASE 
                WHEN wsp.can_edit_state = 1 
                     OR wsp.can_edit_attribute = 1 
                     OR wd.member_id = '{$session['id']}' THEN 1 
                ELSE 0 
            END as can_edit_document")
        );

    // Allow both admin and students to see all documents
    if ($session['membertype'] != "1") {
        $query->where(function ($q) use ($session) {
            $q->where('wsp.member_type_id', $session['membertype'])
                ->orWhere('wd.member_id', $session['id'])
                ->orWhere('wds.allowed_member_id', $session['id'])
                ->orWhereNull('wds.allowed_member_id') // Dokumen umum
                ->orWhere('wsp.member_type_id', '1'); // Tambahkan akses untuk mahasiswa
        });
    }
    // Filter berdasarkan status
    if (isset($param['status']) && $param['status'] !== '') {
        $query->where('wd.status', '=', $param['status']);
    }

    // Filter berdasarkan workflow
    if (isset($param['workflow']) && $param['workflow'] !== '') {
        $query->where('w.id', '=', $param['workflow']);
    }

    // Filter berdasarkan jenis (knowledge type)
    if (isset($param['type']) && $param['type'] !== '') {
        $query->where('kt.id', '=', $param['type']);
    }

    // Filter berdasarkan range tanggal
    if (isset($param['dates_acceptance_option']) && $param['dates_acceptance_option'] === 'date' && isset($param['dates_acceptance'])) {
        $dates = explode(' - ', $param['dates_acceptance']);
        $startDate = date('Y-m-d', strtotime($dates[0]));
        $endDate = date('Y-m-d', strtotime($dates[1]));
        $query->whereBetween('wd.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
    }

    // Filter berdasarkan attribute (dapat mengubah dokumen)
    if (isset($param['attribute']) && $param['attribute'] == '1') {
        $query->where(function ($q) use ($session) {
            // Jika admin, tampilkan semua dokumen
            if ($session['membertype'] == '1') { // Asumsikan '1' adalah ID untuk admin
                $q->whereRaw('1 = 1'); // Tidak ada pembatasan untuk admin
            } else {
                // Logika untuk non-admin
                $q->where(function ($subQuery) use ($session) {
                    $subQuery->where('wsp.can_edit_state', '=', 1)
                             ->orWhere('wsp.can_edit_attribute', '=', 1)
                             ->orWhere(function ($nestedQuery) use ($session) {
                                 $nestedQuery->where('wd.member_id', '=', $session['id'])
                                             ->whereNotNull('wd.member_id'); // Pastikan member_id tidak NULL
                             })
                             ->orWhere('wd.lecturer_id', '=', Auth::user()->id);
                })->where('wd.member_id', '=', Auth::user()->id)
                ->orWhere('wd.lecturer_id', '=', Auth::user()->id);
            }
        });
    }

    // Filter berdasarkan onlyforme (dokumen hanya untuk saya)
    if (isset($param['onlyforme']) && $param['onlyforme'] == '1') {
        $query->where(function ($q) {
            $q->where('wds.allowed_member_id', '=', Auth::user()->id)
              ->whereNotNull('wds.allowed_member_id')
              ->orWhere('wd.lecturer_id', '=', Auth::user()->id);
        })->where('wd.member_id', '=', Auth::user()->id)
        ->orWhere('wd.lecturer_id', '=', Auth::user()->id); 
    }

    // Filter berdasarkan attribute (dapat mengubah dokumen) dan onlyforme (dokumen hanya untuk saya)
    if (isset($param['attribute']) && $param['attribute'] == '1' && isset($param['onlyforme']) && $param['onlyforme'] == '1') {
        $query->where(function ($q) use ($session) {
            // Jika admin, tampilkan semua dokumen
            if ($session['membertype'] == '1') { // Asumsikan '1' adalah ID untuk admin
                $q->whereRaw('1 = 1'); // Tidak ada pembatasan untuk admin
            } else {
                // Logika untuk non-admin
                $q->where(function ($subQuery) use ($session) {
                    $subQuery->where('wsp.can_edit_state', '=', 1)
                            ->orWhere('wsp.can_edit_attribute', '=', 1)
                            ->orWhere(function ($nestedQuery) use ($session) {
                                $nestedQuery->where('wd.member_id', '=', $session['id'])
                                            ->whereNotNull('wd.member_id'); // Pastikan member_id tidak NULL
                            });
                })
                ->where(function ($subQuery) {
                    $subQuery->where('wds.allowed_member_id', '=', Auth::user()->id)
                             ->whereNotNull('wds.allowed_member_id')
                             ->orWhere('wd.lecturer_id', '=', Auth::user()->id);
                });
            }
        });
    }
    
    // Order by
    if (!empty($param['order'])) {
        $query->orderByRaw($param['order']);
    } else {
        $query->orderByDesc('wd.created_at'); // Default urutan dari terbaru
    }

    // Pagination
    if (!empty($param['limit']) && isset($param['offset'])) {
        $query->limit($param['limit'])->offset($param['offset']);
    }

    return $query->get();
}


    // Fungsi untuk menghitung jumlah data yang difilter
    public function dtfiltered()
    {
        return DB::select('SELECT FOUND_ROWS() as jumlah')[0]->jumlah;
    }

    // Fungsi untuk menghitung total data
    public function dtcount()
{
    $session = session('user_doc');
    $query = DB::table('workflow_document as wd')
        ->leftJoin('workflow as w', 'w.id', '=', 'wd.workflow_id')
        ->leftJoin('knowledge_subject as ks', 'ks.id', '=', 'wd.knowledge_subject_id')
        ->leftJoin('knowledge_type as kt', 'kt.id', '=', 'wd.knowledge_type_id')
        ->leftJoin('member as m', 'm.id', '=', 'wd.member_id')
        ->leftJoin('workflow_document_state as wds', function ($join) {
            $join->on('wds.document_id', '=', 'wd.id')
                ->whereNull('wds.close_date');
        })
        ->leftJoin('workflow_state as ws', 'ws.id', '=', 'wd.latest_state_id')
        ->leftJoin('member as m2', 'm2.id', '=', 'wds.allowed_member_id')
        ->leftJoin('workflow_state_permission as wsp', function ($join) use ($session) {
            $join->on('wsp.state_id', '=', 'wd.latest_state_id')
                ->where('wsp.member_type_id', '=', $session['membertype']);
        })
        ->where('ks.active', '1')
        ->where('kt.active', '1');

    if ($session['membertype'] != "1") {
        $query->where(function ($q) use ($session) {
            $q->where('wsp.member_type_id', $session['membertype'])
              ->orWhere('wd.member_id', $session['id'])
              ->orWhere('wds.allowed_member_id', $session['id'])
              ->orWhereNull('wds.allowed_member_id')
              ->orWhere('wsp.member_type_id', '1');
        });
    }

    return $query->count();
}

    // Fungsi untuk mengambil semua data
    public function getall()
    {
        return DB::table($this->table)->get();
    }

    // Fungsi untuk mengambil data berdasarkan query
    public function getbyquery($param)
    {
        return DB::select("SELECT * FROM {$this->table} {$param['where']} {$param['order']} {$param['limit']}");
    }

    // Fungsi untuk menghitung data berdasarkan query
    public function countByQuery($param)
{
    $session = session('user_doc');
    $query = DB::table('workflow_document as wd')
        ->leftJoin('workflow as w', 'w.id', '=', 'wd.workflow_id')
        ->leftJoin('knowledge_subject as ks', 'ks.id', '=', 'wd.knowledge_subject_id')
        ->leftJoin('knowledge_type as kt', 'kt.id', '=', 'wd.knowledge_type_id')
        ->leftJoin('member as m', 'm.id', '=', 'wd.member_id')
        ->leftJoin('workflow_document_state as wds', function ($join) {
            $join->on('wds.document_id', '=', 'wd.id')
                ->whereNull('wds.close_date');
        })
        ->leftJoin('workflow_state as ws', 'ws.id', '=', 'wd.latest_state_id')
        ->leftJoin('member as m2', 'm2.id', '=', 'wds.allowed_member_id')
        ->leftJoin('workflow_state_permission as wsp', function ($join) use ($session) {
            $join->on('wsp.state_id', '=', 'wd.latest_state_id')
                ->where('wsp.member_type_id', '=', $session['membertype']);
        });

    if ($session['membertype'] != "1") {
        $query->where(function ($q) use ($session) {
            $q->where('wsp.member_type_id', $session['membertype'])
              ->orWhere('wd.member_id', $session['id'])
              ->orWhere('wds.allowed_member_id', $session['id'])
              ->orWhereNull('wds.allowed_member_id')
              ->orWhere('wsp.member_type_id', '1');
        });
    }

    if (!empty($param['where'])) {
        $query->whereRaw(ltrim($param['where'], 'WHERE '));
    }

    return $query->count();
}

    // Fungsi untuk menghitung semua data
    public function countall()
    {
        return DB::table($this->table)->count();
    }

    // Fungsi untuk mengambil data berdasarkan kondisi
    public function getby($item)
    {
        return DB::table($this->table)->where($item)->get();
    }

    // Fungsi untuk mengambil data berdasarkan ID
    public function getbyid($id)
    {
        return DB::table($this->table)->where($this->primaryKey, $id)->first();
    }

    // Fungsi untuk menambahkan data
    public function add($item)
    {
        return DB::table($this->table)->insertGetId($item);
    }

    // Fungsi untuk mengedit data
    public function edit($id, $item)
    {
        return DB::table($this->table)->where($this->primaryKey, $id)->update($item);
    }

    // Fungsi untuk menghapus data

    // Fungsi untuk aktivasi
    public function aktivasi($where, $item)
    {
        return DB::table($this->table)->where($where)->update($item);
    }

    // Fungsi untuk mengambil data member
    public function getMember($id)
    {
        return DB::table('member as m')
            ->leftJoin('masterdata.t_mst_user_login', 'master_data_user', '=', 'c_username')
            ->where('m.id', $id)
            ->first();
    }

    // Fungsi untuk mengambil data workflow
    public function getWorkflow()
    {
        $session = session('user_doc');
        return DB::table('workflow as w')
            ->leftJoin('workflow_member_type as wmt', 'w.id', '=', 'wmt.workflow_id')
            ->where('member_type_id', $session['membertype'])
            ->orderBy('name')
            ->get();
    }

    // Fungsi untuk mengambil data workflow document
    public function getWorkflowDoc($id)
    {
        return DB::table('workflow_document')->where('id', $id)->first();
    }

    // Fungsi untuk mengambil data workflow by ID
    public function getWorkflowbyId($id)
    {
        return DB::table('workflow')->where('id', $id)->orderBy('name')->get();
    }

    // Fungsi untuk mengambil data workflow state by ID
    public function getWorkflowStatebyId($id, $workflow_id)
    {
        return DB::table('workflow_state')
            ->where('id', $id)
            ->where('workflow_id', $workflow_id)
            ->orderBy('name')
            ->get();
    }

    // Fungsi untuk mengambil data workflow by member type
    public function getWorkflowbyMemberType($id)
    {
        return DB::table('workflow as w')
            ->leftJoin('workflow_member_type as wmt', 'w.id', '=', 'wmt.workflow_id')
            ->where('member_type_id', $id)
            ->orderBy('name')
            ->get();
    }

    // Fungsi untuk mengambil data unit
    public function getUnit()
    {
        return DB::table('t_mst_prodi')->orderBy('nama_prodi')->get();
    }

    // Fungsi untuk mengambil data subject
    public function getSubject($name)
    {
        return DB::table('knowledge_subject')
            ->where('name', 'like', "%$name%")
            ->orderBy('name')
            ->limit(20)
            ->get();
    }

    // Fungsi untuk mengambil data knowledge type by workflow ID
    public function getKnowledgeTypeByWorkflowId($id)
    {
        return DB::table('workflow_knowledge_type as wkt')
            ->leftJoin('knowledge_type as kt', 'kt.id', '=', 'wkt.knowledge_type_id')
            ->where('workflow_id', $id)
            ->orderBy('name')
            ->get();
    }

    // Fungsi untuk mengambil data upload type by workflow ID
    public function getUploadTypeByWorkflowId($id)
    {
        return DB::table('workflow_upload_type as wkt')
            ->leftJoin('upload_type as kt', 'kt.id', '=', 'wkt.upload_type_id')
            ->where('workflow_id', $id)
            ->orderBy('title')
            ->get();
    }
public function getUploadTypeById($id)
{
    return DB::table('workflow_upload_type as wut')
        ->join('upload_type as ut', 'ut.id', '=', 'wut.upload_type_id')
        ->where('wut.upload_type_id', $id) // Ubah ini dari 'wut.id' ke 'wut.upload_type_id'
        ->select([
            'ut.id',
            'ut.title',
            'ut.name', 
            'ut.extension',
            'wut.upload_type_id'
        ])
        ->first();
}
public function getUploadTypeByWorkflowId2($id)
{
    return DB::table('workflow_upload_type as wkt')
        ->leftJoin('upload_type as kt', 'kt.id', '=', 'wkt.upload_type_id')
        ->where('workflow_id', $id)
        ->where(function ($query) {
            $query->where('extension', 'docx')
                  ->orWhereIn('kt.id', [95, 97]);
        })
        ->orderBy('title')
        ->get([
            'kt.id',
            'kt.title',
            'kt.name',
            'kt.extension',
            'wkt.upload_type_id' // Pastikan kolom ini termasuk
        ]);
}

    // Fungsi untuk mengambil data master subject by unit ID
    public function getMasterSubjectByUnitId($id)
    {
        return DB::table('master_subject')
            ->where('course_code', $id)
            ->orderBy('name')
            ->get(['id', 'code', 'name']);
    }

    // Fungsi untuk mengambil data knowledge type
    public function getKnowledgeType()
    {
        return DB::table('knowledge_type')
            ->where('active', '1')
            ->orderBy('name')
            ->get();
    }

    // Fungsi untuk menambahkan data custom
    public function add_custom($item, $table)
    {
        return DB::table($table)->insertGetId($item);
    }

    // Fungsi untuk mengambil data workflow document by ID
    public function getWorkflowDocumentbyId($id, $type)
{
    return DB::table('workflow_document as wd')
        ->select([
            'start_state_id',
            'wd.*',
            'wd.id as wd_id',
            'w.id as w_id',
            'w.name as w_name', // Add this line
            'kt.name as jenis_katalog',
            'm.master_data_user',
            'm.master_data_fullname',
            'lecturer.master_data_number as lecturer_number',
            'lecturer.master_data_fullname as lecturer_name',
            'lecturer2.master_data_number as lecturer2_number',
            'lecturer2.master_data_fullname as lecturer2_name',
            'approved.master_data_number as approved_number',
            'approved.master_data_fullname as approved_name',
            'ks.name as ks_name',
            'ws.name as state_name',
            'wsp.can_edit_state',
            'wsp.can_edit_attribute'
        ])
        ->leftJoin('workflow as w', 'w.id', '=', 'wd.workflow_id')
        ->leftJoin('knowledge_type as kt', 'kt.id', '=', 'wd.knowledge_type_id')
        ->leftJoin('member as m', 'm.id', '=', 'wd.member_id')
        ->leftJoin('member as lecturer', 'lecturer.id', '=', 'wd.lecturer_id')
        ->leftJoin('member as lecturer2', 'lecturer2.id', '=', 'wd.lecturer2_id')
        ->leftJoin('member as approved', 'approved.id', '=', 'wd.approved_id')
        ->leftJoin('knowledge_subject as ks', 'ks.id', '=', 'wd.knowledge_subject_id')
        ->leftJoin('t_mst_prodi as tmp', 'tmp.C_KODE_PRODI', '=', 'wd.course_code')
        ->leftJoin('t_mst_fakultas as tmf', 'tmf.C_KODE_FAKULTAS', '=', 'tmp.C_KODE_FAKULTAS')
        ->leftJoin('workflow_state_permission as wsp', function ($join) use ($type) {
            $join->on('wsp.state_id', '=', 'wd.latest_state_id')
                ->where('wsp.member_type_id', '=', $type);
        })
        ->leftJoin('workflow_state as ws', 'ws.id', '=', 'wd.latest_state_id')
        ->where('wd.id', $id)
        ->first();
}

    // Fungsi untuk mengambil data next state
    public function getNextState($stateId)
{
    return DB::table('workflow_transition as wt')
        ->select('ws.id', 'ws.name', 'ws.description')
        ->join('workflow_task as t', 't.id', '=', 'wt.task_id') // Join to tasks table
        ->join('workflow_state as ws', 'ws.id', '=', 't.next_state_id') // Then to state table
        ->where('wt.state_id', $stateId)
        ->orderBy('ws.name')
        ->get();
}

    // Fungsi untuk mengambil data workflow document subject by document ID
    public function getWorkflowDocumentSubjectByDocumentId($id)
    {
        return DB::table('workflow_document_subject')
            ->leftJoin('master_subject as ms', 'ms.id', '=', 'workflow_document_subject.master_subject_id')
            ->where('workflow_document_id', $id)
            ->orderBy('ms.name')
            ->get();
    }

    // Fungsi untuk mengambil data document master subject by unit ID
    public function getDocumentMasterSubjectByUnitId($id, $wd_id)
    {
        return DB::table('master_subject as ms')
            ->select('ms.*', DB::raw("(select count(*)total from workflow_document_subject where workflow_document_id='$wd_id' and master_subject_id=ms.id)total"))
            ->where('course_code', $id)
            ->orderBy('ms.name')
            ->get();
    }

    // Fungsi untuk mengambil data document file
    public function getDocumentFile($documentId)
{
    return DB::table('workflow_document_file')
        ->select([
            'workflow_document_file.id', // ID dari tabel workflow_document_file
            'workflow_document_file.document_id',
            'workflow_document_file.upload_type_id',
            'workflow_document_file.location as name',
            'workflow_document_file.location as location',
            'workflow_document_file.created_by',
            'workflow_document_file.created_at',
            'workflow_document_file.updated_by',
            'workflow_document_file.updated_at',
            'upload_type.title as title', // Pastikan kolom ini ada di tabel upload_type
            'upload_type.extension as extension' // Pastikan kolom ini ada di tabel upload_type
        ])
        ->leftJoin('upload_type', 'upload_type.id', '=', 'workflow_document_file.upload_type_id') // Periksa nama tabel dan kolom
        ->where('workflow_document_file.document_id', $documentId)
        ->get();
}

    // Fungsi untuk mengambil data document file dengan exclude
    public function getDocumentFileWithExclude($id, $state_id)
    {
        $query = DB::table('workflow_document_file as wdf')
            ->leftJoin('upload_type as ut', 'ut.id', '=', 'wdf.upload_type_id')
            ->where('document_id', $id);

        if ($state_id != "") {
            $query->whereNotIn('upload_type_id', function ($q) use ($state_id) {
                $q->select('upload_type_id')
                    ->from('workflow_exclude_file_transfer')
                    ->where('workflow_state_id', $state_id);
            });
        }

        return $query->orderBy('wdf.name')->get();
    }

    // Fungsi untuk mengambil data document state
    public function getDocumentState($id)
    {
        return DB::table('workflow_document_state as wds')
            ->select(
                'ws.name as state_name',
                'm.master_data_user',
                'm.master_data_fullname',
                'mt.name as member_type_name',
                'wds.open_date',
                'wds.close_date'
            )
            ->leftJoin('workflow_state as ws', 'ws.id', '=', 'wds.state_id')
            ->leftJoin('member as m', 'm.id', '=', 'wds.open_by')
            ->leftJoin('member_type as mt', 'mt.id', '=', 'm.member_type_id')
            ->where('wds.document_id', $id)
            ->orderBy('wds.id', 'asc')
            ->get();
    }

    // Fungsi untuk mengambil data document state exclude
    public function getDocumentStateExclude($id)
    {
        return DB::table('workflow_document_state as wds')
            ->leftJoin('workflow_state as ws', 'ws.id', '=', 'wds.state_id')
            ->leftJoin('member as m', 'm.id', '=', 'wds.open_by')
            ->leftJoin('member_type as mt', 'mt.id', '=', 'm.member_type_id')
            ->where('document_id', $id)
            ->whereIn('state_id', [91, 52])
            ->orderBy('wds.id', 'desc')
            ->limit(1)
            ->get();
    }

    // Fungsi untuk mengambil data document state ID
    public function getDocumentStateId($wd_id, $state_id)
    {
        return DB::table('workflow_document_state')
            ->where('document_id', $wd_id)
            ->where('state_id', $state_id)
            ->whereNull('close_date')
            ->first();
    }

    // Fungsi untuk mengambil data document comment
    public function getDocumentComment($where)
    {
        return DB::table('workflow_comment as wc')
            ->leftJoin('member as m', 'm.id', '=', 'wc.member_id')
            ->whereRaw($where)
            ->orderBy('wc.created_at')
            ->get();
    }

    // Fungsi untuk menghapus document comment
    public function delete_document_comment($id)
    {
        return DB::table('workflow_comment')
            ->where('id', $id)
            ->orWhere('parent_id', $id)
            ->delete();
    }

    // Fungsi untuk mengambil data state by ID
    public function getStateById($id)
    {
        return DB::table('workflow_state')
            ->where('id', $id)
            ->first();
    }

    // Fungsi untuk mengedit workflow document state
    public function edit_workflow_document_state($wd_id, $state_id, $user_id)
    {
        return DB::table('workflow_document_state')
            ->where('document_id', $wd_id)
            ->where('state_id', $state_id)
            ->update([
                'close_date' => now(),
                'open_by' => $user_id
            ]);
    }

    public function getDocumentCommentsWithReplies($documentId)
    {
        // Get all comments for this document in a single query
        $allComments = DB::table('workflow_comment as wc')
            ->select([
                'wc.id',
                'wc.parent_id',
                'wc.comment',
                'wc.created_at',
                'm.master_data_user',
                'm.master_data_fullname'
            ])
            ->leftJoin('member as m', 'm.id', '=', 'wc.member_id')
            ->where('wc.document_id', $documentId)
            ->orderBy('wc.created_at', 'asc')
            ->get();
    
        // Organize comments hierarchically
        $comments = [];
        $replies = [];
    
        foreach ($allComments as $comment) {
            if ($comment->parent_id === null) {
                $comments[$comment->id] = [
                    'id' => $comment->id,
                    'user' => $comment->master_data_user,
                    'name' => $comment->master_data_fullname,
                    'comment' => $comment->comment,
                    'created_at' => $comment->created_at,
                    'reply' => []
                ];
            } else {
                $replies[$comment->parent_id][] = [
                    'id' => $comment->id,
                    'user' => $comment->master_data_user,
                    'name' => $comment->master_data_fullname,
                    'comment' => $comment->comment,
                    'created_at' => $comment->created_at
                ];
            }
        }
    
        // Attach replies to their parent comments
        foreach ($replies as $parentId => $replyList) {
            if (isset($comments[$parentId])) {
                $comments[$parentId]['reply'] = $replyList;
            }
        }
    
        return array_values($comments);
    }

    // Fungsi untuk mengedit approved document state
    public function edit_approved_document_state($wd_id, $state_id, $user_id)
    {
        return DB::table('workflow_document_state')
            ->where('document_id', $wd_id)
            ->where('state_id', $state_id)
            ->whereNull('close_date')
            ->update(['allowed_member_id' => $user_id]);
    }

    // Fungsi untuk menghapus workflow document subject
    public function delete_workflow_document_subject($wd_id)
    {
        return DB::table('workflow_document_subject')
            ->where('workflow_document_id', $wd_id)
            ->delete();
    }

    // Fungsi untuk mengambil data skripsi mahasiswa
    public function smk_t_tra_mhs_skripsi($username)
    {
        return DB::table('masterdata.smk_t_tra_mhs_skripsi')
            ->where('c_npm', $username)
            ->first();
    }

    // Fungsi untuk mengambil data members
    public function getMembers($username)
    {
        return DB::table('member')
            ->where('master_data_user', $username)
            ->get();
    }

    // Fungsi untuk mengecek file yang sudah ada
    public function check_existing_file($wd_id, $location)
    {
        return DB::table('workflow_document_file')
            ->where('document_id', $wd_id)
            ->where('location', $location)
            ->first();
    }

    // Fungsi untuk mengambil data document sdgs
    public function getDocumentSdgs($wd_id)
    {
        return DB::table('workflow_document_sdgs')
            ->where('document_id', $wd_id)
            ->get();
    }

    // Fungsi untuk menghapus workflow document sdgs
    public function delete_workflow_document_sdgs($wd_id)
    {
        return DB::table('workflow_document_sdgs')
            ->where('document_id', $wd_id)
            ->delete();
    }

    // Fungsi untuk mengambil data workflow document member
    public function getWorkflowDocumentMember($id)
    {
        return DB::table('workflow_document as wd')
            ->leftJoin('member as m', 'm.id', '=', 'wd.member_id')
            ->where('wd.id', $id)
            ->first();
    }

    // Fungsi untuk mengambil token notification mobile
    public function getTokenNotificationMobile($id)
    {
        return DB::table('member')
            ->where('id', $id)
            ->value('master_data_token');
    }

    // Fungsi untuk mengambil data lecturer
    public function getlecturer($term)
    {
        return DB::table('member')
            ->where(function ($query) use ($term) {
                $query->where('master_data_user', 'like', "%$term%")
                    ->orWhere('master_data_fullname', 'like', "%$term%");
            })
            ->whereIn('member_type_id', [1, 3, 4, 7])
            ->where('status', '1')
            ->orderBy('master_data_fullname')
            ->limit(25)
            ->get();
    }

    public function sdgs()
{
    return [
        '1'  => 'Pilar pembangunan sosial - Menghapus kemiskinan',
        '2'  => 'Pilar pembangunan sosial - Mengakhiri kelaparan',
        '3'  => 'Pilar pembangunan sosial - Kesehatan yang baik dan kesejahteraan',
        '4'  => 'Pilar pembangunan sosial - Pendidikan Bermutu',
        '5'  => 'Pilar pembangunan sosial - Kesetaraan gender',
        '7'  => 'Pilar pembangunan ekonomi - Energi bersih dan terjangkau',
        '8'  => 'Pilar pembangunan ekonomi - Pekerjaan layak dan pertumbuhan ekonomi',
        '9'  => 'Pilar pembangunan ekonomi - Infrastruktur, industri, dan inovasi',
        '10' => 'Pilar pembangunan ekonomi - Mengurangi ketimpangan',
        '17' => 'Pilar pembangunan ekonomi - Kemitraan untuk mencapai tujuan',
        '6'  => 'Pilar pembangunan lingkungan - Akses air bersih dan sanitasi',
        '11' => 'Pilar pembangunan lingkungan - Kota dan komunitas yang berkelanjutan',
        '12' => 'Pilar pembangunan lingkungan - Konsumsi dan produksi yang bertanggungjawab',
        '13' => 'Pilar pembangunan lingkungan - Penanganan perubahan iklim',
        '14' => 'Pilar pembangunan lingkungan - Menjaga ekosistem laut',
        '15' => 'Pilar pembangunan lingkungan - Menjaga ekosistem darat',
        '16' => 'Pilar pembangunan hukum dan tata kelola - Perdamaian, keadilan, dan kelembagaan yang kuat',
    ];
}

    // Fungsi untuk mengambil data last book
    public function getLastBook($type)
    {
        return DB::table('knowledge_item')
            ->where('code', 'like', "$type%")
            ->orderBy('id', 'desc')
            ->limit(1)
            ->value('code');
    }

    // Fungsi untuk mengambil data file download
    public function getFileDownload($id, $fid)
    {
        return DB::table('workflow_document_file as wdf')
            ->leftJoin('workflow_document as wd', 'wd.id', '=', 'wdf.document_id')
            ->leftJoin('member as m', 'm.id', '=', 'wd.member_id')
            ->where('wdf.document_id', $id)
            ->where('wdf.id', $fid)
            ->first();
    }

    // Fungsi untuk mengambil data member prodi
    public function getMemberProdi($id)
    {
        return DB::table('member')
            ->leftJoin('t_mst_prodi as tmp', 'tmp.c_kode_prodi', '=', 'member.master_data_course')
            ->where('member.id', $id)
            ->value('nama_prodi');
    }

    // Fungsi untuk mengecek duplikat workflow document
    public function checkDuplicateWorkflowDocument($id)
    {
        return DB::table('workflow_document')
            ->where('workflow_id', 1)
            ->where('member_id', $id)
            ->exists();
    }

    // Fungsi untuk mengambil list member by document
    public function getListMemberbyDocument($start, $end)
    {
        return DB::table('workflow_document as wd')
            ->join('member as m', 'm.id', '=', 'wd.member_id')
            ->where('wd.workflow_id', 1)
            ->groupBy('m.id')
            ->orderBy('wd.id', 'desc')
            ->limit($end - $start)
            ->offset($start)
            ->pluck('master_data_user');
    }

    // Fungsi untuk mengambil list duplicate member by document
    public function getListDupicateMemberbyDocument($start, $end)
    {
        return DB::table(DB::raw("(select m.id,master_data_user, count(*) total from workflow_document wd join member m on m.id=member_id where wd.workflow_id='1' group by m.id order by wd.id desc) as a"))
            ->where('total', '>', 1)
            ->orderBy('total', 'desc')
            ->limit($end - $start)
            ->offset($start)
            ->get();
    }

    // Fungsi untuk mengambil status document
    public function getStatusDocument($id)
    {
        return DB::table(DB::raw("(select wd.id,latest_state_id, case when  latest_state_id in (3,52,64,53,5,91) then '1' else '0' end as total from workflow_document wd where member_id='$id' and workflow_id='1') as aa"))
            ->where('total', '1')
            ->orderBy('total', 'desc')
            ->get();
    }

    // Fungsi untuk mengambil list document
    public function getListDocument($id)
    {
        return DB::table(DB::raw("(select wd.id,latest_state_id, case when  latest_state_id in (3,52,64,53,5,91) then '1' else '0' end as total from workflow_document wd where member_id='$id' and workflow_id='1') as aa"))
            ->where('total', '0')
            ->get();
    }

    // Fungsi untuk mengambil list document file
    public function getListDocumentFile($id)
    {
        return DB::table('workflow_document_file')
            ->where('document_id', $id)
            ->pluck('location');
    }

    // Fungsi untuk mengambil list duplicate member by document 2
    public function getListDupicateMemberbyDocument2($start, $end)
    {
        return DB::table('workflow_document_file as wdf')
            ->leftJoin('workflow_document as wd', 'wd.id', '=', 'wdf.document_id')
            ->whereNull('wd.id')
            ->groupBy('wdf.document_id')
            ->orderBy('wdf.location')
            ->limit($end - $start)
            ->offset($start)
            ->get();
    }
}
class Workflow extends Model
{
    protected $table = 'workflow';
    protected $fillable = ['name', 'start_state_id'];
}

class KnowledgeType extends Model
{
    protected $table = 'knowledge_type';
    protected $fillable = ['name', 'workflow_id'];
}

class Status extends Model
{
    protected $table = 'workflow_document';
    protected $fillable = ['name'];
}

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Member extends Model
{
    protected $table = 'member';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // Add your fillable fields here
    ];

    /**
     * Query for new datatables purpose
     */
    public function dtQuery($param)
    {
        return DB::select("SELECT SQL_CALC_FOUND_ROWS * FROM {$this->table} 
            LEFT JOIN member_subscribe ON subscribe_id_member = id AND subscribe_status = '1'
            {$param['where']} {$param['order']} {$param['limit']}");
    }

    public function dtFiltered()
    {
        $result = DB::select('SELECT FOUND_ROWS() as jumlah');
        return $result[0]->jumlah;
    }

    public function dtCount()
    {
        return DB::table($this->table)->count();
    }

    public function getAll()
    {
        return DB::table($this->table)->get();
    }

    public function getByQuery($param)
    {
        return DB::select("SELECT * FROM {$this->table} {$param['where']}
            {$param['order']} {$param['limit']}");
    }

    public function countByQuery($param)
    {
        $result = DB::select("SELECT COUNT({$this->primaryKey}) as jumlah FROM {$this->table} {$param['where']}");
        return $result[0]->jumlah ?? 0;
    }

    public function countAll()
    {
        return DB::table($this->table)->count();
    }

    public function getBy($item)
    {
        return DB::table($this->table)->where($item)->get();
    }

    public function getById($id)
    {
        return DB::table($this->table)->where($this->primaryKey, $id)->first();
    }

    public function add($item)
    {
        return DB::table($this->table)->insertGetId($item);
    }

    public function edit($id, $item)
    {
        return DB::table($this->table)->where($this->primaryKey, $id)->update($item);
    }

    public function editTUserLogin($id, $item)
    {
        return DB::table('masterdata.t_mst_user_login')->where('C_USERNAME', $id)->update($item);
    }

    public function deleteById($id)
    {
        return DB::table($this->table)->where($this->primaryKey, $id)->delete();
    }

    public function deleteTUserLogin($id)
    {
        return DB::table('masterdata.t_mst_user_login')->where('c_username', $id)->delete();
    }

    public function deleteTPegawai($id)
    {
        return DB::table('masterdata.t_mst_pegawai')->where('c_nip', $id)->delete();
    }

    public function deleteVfsUsers($id)
    {
        return DB::table('masterdata.vfs_users')->where('usr', $id)->delete();
    }

    public function aktivasi($where, $item)
    {
        return DB::table($this->table)->where($where)->update($item);
    }

    public function getMember($id)
    {
        return DB::select("SELECT * FROM {$this->table} m 
            LEFT JOIN masterdata.t_mst_user_login ON master_data_user = c_username 
            WHERE m.id = ?", [$id]);
    }
    public function getMemberProdi($memberId)
    {
        return DB::table('member as m')
            ->leftJoin('t_mst_prodi as tmp', 'tmp.c_kode_prodi', '=', 'm.master_data_course')
            ->where('m.id', $memberId)
            ->value('tmp.nama_prodi');
    }

}