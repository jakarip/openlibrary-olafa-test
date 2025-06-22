<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SubmissionModel extends Model
{
    protected $table = 'book_procurement'; 
    protected $primaryKey = 'book_id';

    public $timestamps = false; // Disable automatic timestamps

    public function dtquery($param)
    {   
        // dd($param);
        $query = "SELECT SQL_CALC_FOUND_ROWS *, 
        DATEDIFF(book_date_logistic_submission, book_date_prodi_submission) as proses_pengajuan, 
        DATEDIFF(book_date_acceptance, book_date_logistic_process) as proses_pengadaan, 
        DATEDIFF(book_date_available, book_date_acceptance) as proses_ketersediaan, 
        DATEDIFF(book_date_email_confirmed, book_date_available) as proses_email 
        FROM ".$this->table." 
        left join t_mst_prodi tmp on c_kode_prodi=book_id_prodi
        left join t_mst_fakultas tmf on tmp.c_kode_fakultas=tmf.c_kode_fakultas
        ".$param['where']." ".$param['order']." ".$param['limit']." 
        ORDER BY book_status DESC";
        
        // dd($query); // Display the query

        return DB::select($query);
    }
    // ORDER BY book_id DESC LIMIT 5"; 

    public function getFaculty()
    {   
        return DB::select("SELECT c_kode_fakultas, nama_fakultas FROM t_mst_fakultas ORDER BY nama_fakultas"); 
    }

    public function getProdi()
    {   
        return DB::select("SELECT *	from t_mst_fakultas tmf left join t_mst_prodi tmp on tmp.c_kode_fakultas=tmf.c_kode_fakultas where (nama_prodi not like '%Pindahan%' and nama_prodi not like '%International%' and nama_prodi not like '%Internasional%') order by nama_fakultas,nama_prodi"); 
    }

    public function getProdiByFacId($id)
    {   
        return DB::select("SELECT * FROM t_mst_prodi WHERE c_kode_fakultas = ? AND nama_prodi NOT LIKE '%Pindahan%' AND nama_prodi NOT LIKE '%International%' ORDER BY nama_prodi", [$id]); 
    }

    function getProdiByProdId($id)
	{   
		return DB::select("SELECT * FROM t_mst_prodi WHERE c_kode_prodi = '$id'"); 
	}
}
