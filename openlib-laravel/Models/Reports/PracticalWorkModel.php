<?php

namespace App\Models\Reports;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PracticalWorkModel extends Model
{
    use HasFactory;

    protected $table = 'ereport';
    protected $primaryKey = 'ereport_id';
    public $timestamps = false;

    protected $fillable = [
        'ereport_id',
        'ereport_type',
        'master_data_number',
        'master_data_fullname',
        'prodi',
        'ereport_id_lecture',
        'ereport_mentor_company_name',
        'ereport_industry_type',
        'ereport_mentor_company_address',
        'ereport_mentor_front_name',
        'ereport_mentor_last_name',
        'ereport_mentor_academic',
        'ereport_mentor_email',
        'ereport_mentor_phone',
        'ereport_status',
        'ereport_file', 
        'ereport_file_similarity',  
        'ereport_file_approval',
        'ereport_file_finish',
        'ereport_file_Implementation',
        // Tambahkan file lain sesuai kebutuhan
    ];


    // public function getEreportDetails()
    // {
    //     return DB::table('ereport as e')
    //         ->join('member as m', 'e.ereport_id_member', '=', 'm.id')
    //         ->join('t_mst_prodi as p', 'm.master_data_course', '=', 'p.C_KODE_PRODI')
    //         ->leftJoin('member as d', 'e.ereport_id_lecturer', '=', 'd.id')
    //         ->select([
    //             'e.ereport_id',
    //             'e.ereport_type',
    //             'm.master_data_number',
    //             'm.master_data_fullname',
    //             'p.NAMA_PRODI as nama_prodi',
    //             'e.ereport_title',
    //             'd.master_data_fullname as dosen_pembimbing',
    //             'e.ereport_mentor_company_name',
    //             'e.ereport_industry_type',
    //             'e.ereport_mentor_company_address',
    //             DB::raw("CONCAT(e.ereport_mentor_front_name, ' ', e.ereport_mentor_last_name) AS mentor_name"),
    //             'e.ereport_mentor_academic',
    //             DB::raw("CONCAT(e.ereport_mentor_phone, ' / ', e.ereport_mentor_email) AS mentor_contact"),
    //             'e.ereport_status'
    //         ])
    //         ->get();
    // }

    public function getEreportDetailsBuilder($memberClassId = null, $memberId = null)
{
    $query = DB::table('ereport as e')
        ->join('member as m', 'e.ereport_id_member', '=', 'm.id')
        ->join('t_mst_prodi as p', 'm.master_data_course', '=', 'p.C_KODE_PRODI')
        ->leftJoin('member as d', 'e.ereport_id_lecturer', '=', 'd.id')
        ->select([
            'e.ereport_id',
            'e.ereport_type',
            'm.master_data_number',
            'm.master_data_fullname',
            'p.NAMA_PRODI as nama_prodi',
            'e.ereport_title',
            'd.master_data_fullname as dosen_pembimbing',
            'e.ereport_mentor_company_name',
            'e.ereport_industry_type',
            'e.ereport_mentor_company_address',
            DB::raw("CONCAT(e.ereport_mentor_front_name, ' ', e.ereport_mentor_last_name) AS mentor_name"),
            'e.ereport_mentor_academic',
            DB::raw("CONCAT(e.ereport_mentor_phone, ' / ', e.ereport_mentor_email) AS mentor_contact"),
            'e.ereport_status'
        ]);

    if ($memberClassId == 2) {
        $query->where('e.ereport_id_member', $memberId);
    }

    return $query;
}
    
    public function getById($id)
    {
        return $this->where($this->primaryKey, $id)->first();
    }

    public function add(array $data)
    {
        return $this->create($data)->{$this->primaryKey};
    }

    public function updateReport($id, array $data)
    {
        $updated = $this->where($this->primaryKey, $id)->update($data);
        return $updated; // Ini akan mengembalikan jumlah baris yang diperbarui
    }

    public function deleteReport($id)
    {
        return $this->where($this->primaryKey, $id)->delete();
    }
}