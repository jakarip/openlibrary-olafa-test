<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TelupressModel extends Model
{

    protected $table = 'book_telupress'; 
    protected $primaryKey = 'book_id';

    public $timestamps = false; // Disable automatic timestamps

    public function getprodi()
    {
        return DB::select("SELECT * from t_mst_fakultas tmf left join t_mst_prodi tmp on tmp.c_kode_fakultas=tmf.c_kode_fakultas where (nama_prodi not like '%Pindahan%' and nama_prodi not like '%International%' and nama_prodi not like '%Internasional%') order by nama_fakultas,nama_prodi");
    }

    public function dtquery($param)
    {  
        // dd($param);
        return DB::select("SELECT SQL_CALC_FOUND_ROWS bt.*, 
            IFNULL(DATEDIFF(book_enddate_target_step_1, book_enddate_realization_step_1),0) proses_step1, 
            IFNULL(DATEDIFF(book_enddate_target_step_2, book_enddate_realization_step_2),0) proses_step2, 
            IFNULL(DATEDIFF(book_enddate_target_step_3, book_enddate_realization_step_3),0) proses_step3, 
            IFNULL(DATEDIFF(book_enddate_target_step_4, book_enddate_realization_step_3),0) proses_step4, 
            IFNULL(DATEDIFF(book_enddate_target_step_5, book_enddate_realization_step_5),0) proses_step5, 
            IFNULL(DATEDIFF(book_enddate_target_step_6, book_enddate_realization_step_6),0) proses_step6, 
            IFNULL(DATEDIFF(book_enddate_realization_step_6, book_startdate_realization_step_1),0) total_proses_naskah_cetak, 
            master_data_fullname,NAMA_FAKULTAS,NAMA_PRODI 
            FROM ".$this->table." bt
            LEFT JOIN member m ON m.id=book_id_user
            LEFT JOIN t_mst_prodi tmp ON c_kode_prodi=book_id_prodi
            LEFT JOIN t_mst_fakultas tmf ON tmp.c_kode_fakultas=tmf.c_kode_fakultas ".$param['where']."  
            ");
    }

    public function getmemberbyname($name)
    {
        return DB::select("SELECT * FROM member WHERE member_type_id IN (4,7) AND master_data_fullname LIKE ? AND status='1' ORDER BY master_data_fullname LIMIT 20", ['%'.$name.'%']);
    }

}
