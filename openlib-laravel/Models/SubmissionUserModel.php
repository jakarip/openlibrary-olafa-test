<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SubmissionUserModel extends Model
{
    protected $table = 'usulan_bahanpustaka'; 
    protected $primaryKey = 'bp_id';

    public $timestamps = false; // Disable automatic timestamps

    public function dtquery($param)
    {   
        // dd($param);
        return DB::select("
            SELECT SQL_CALC_FOUND_ROWS *, ub.*, nama_prodi, nama_fakultas, DATE_FORMAT(bp_createdate, '%Y-%m-%d') AS tanggal,
            master_data_number, master_data_fullname
            FROM usulan_bahanpustaka ub
            LEFT JOIN t_mst_prodi tmp ON c_kode_prodi = bp_prodi_id
            LEFT JOIN t_mst_fakultas tmf ON tmf.c_kode_fakultas = tmp.c_kode_fakultas
            LEFT JOIN member ON member.id = bp_idmember
            WHERE bp_upload_type = 'apps' $param[where] $param[order] $param[limit]
            ORDER BY bp_createdate DESC
            
        ");

        // $user = Auth::user();
        // if ($user->usergroup == 'superadmin') {  
        //     return DB::select("
        //         SELECT SQL_CALC_FOUND_ROWS *, ub.*, nama_prodi, nama_fakultas, DATE_FORMAT(bp_createdate, '%Y-%m-%d') AS tanggal,
        //         master_data_number, master_data_fullname
        //         FROM usulan_bahanpustaka ub
        //         LEFT JOIN t_mst_prodi tmp ON c_kode_prodi = bp_prodi_id
        //         LEFT JOIN t_mst_fakultas tmf ON tmf.c_kode_fakultas = tmp.c_kode_fakultas
        //         LEFT JOIN member ON member.id = bp_idmember
        //         WHERE bp_upload_type = 'apps'
        //     ");
        // } else {
        //     return DB::select("
        //         SELECT SQL_CALC_FOUND_ROWS *, ub.*, nama_prodi, nama_fakultas, DATE_FORMAT(bp_createdate, '%Y-%m-%d') AS tanggal,
        //         master_data_number, master_data_fullname
        //         FROM usulan_bahanpustaka ub
        //         LEFT JOIN t_mst_prodi tmp ON c_kode_prodi = bp_prodi_id
        //         LEFT JOIN t_mst_fakultas tmf ON tmf.c_kode_fakultas = tmp.c_kode_fakultas
        //         LEFT JOIN member ON member.id = bp_idmember
        //         WHERE bp_idmember = ? AND bp_upload_type = 'apps' 
        //     ", [$user->id]);
        // }
        
    }

    public function getProdi()
    {   
        return DB::select("SELECT *	from t_mst_fakultas tmf left join t_mst_prodi tmp on tmp.c_kode_fakultas=tmf.c_kode_fakultas where (nama_prodi not like '%Pindahan%' and nama_prodi not like '%International%' and nama_prodi not like '%Internasional%') order by nama_fakultas,nama_prodi"); 
    }

    public function edit($id, $item)
    {
        return DB::table($this->table)->where($this->primaryKey, $id)->update($item);
    }

    public function addBookProcurement($item)
    {
        return DB::table('book_procurement')->insertGetId($item);
    }
}
