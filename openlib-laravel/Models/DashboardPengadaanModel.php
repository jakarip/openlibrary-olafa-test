<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DashboardPengadaanModel extends Model
{
    use HasFactory;

    function total_pengajuan($start, $end)
    {
        $query = "
            SELECT COUNT(*) AS total, 
                (SELECT COUNT(*) FROM book_procurement
                WHERE book_status = 'pengajuan' AND book_date_prodi_submission BETWEEN '$start' AND '$end') AS pengajuan, 
                (SELECT COUNT(*) FROM book_procurement 
                WHERE book_status = 'logistik' AND book_date_prodi_submission BETWEEN '$start' AND '$end') AS logistik, 
                (SELECT COUNT(*) FROM book_procurement 
                WHERE book_status = 'penerimaan' AND book_date_prodi_submission BETWEEN '$start' AND '$end') AS penerimaan,
                (SELECT COUNT(*) FROM book_procurement
                WHERE book_status = 'r_ketersediaan' AND book_date_prodi_submission BETWEEN '$start' AND '$end') AS available,
                (SELECT COUNT(*) FROM book_procurement
                WHERE book_status = 's_email' AND book_date_prodi_submission BETWEEN '$start' AND '$end') AS email_confirmed
            FROM book_procurement 
            WHERE book_date_prodi_submission BETWEEN '$start' AND '$end'
        ";
        return DB::select($query);
    }

    function total_pengajuan_telupress($start, $end)
    {
        $query = "
            SELECT COUNT(*) AS total, 
                (SELECT COUNT(*) FROM book_telupress
                WHERE book_status = '1' AND book_startdate_realization_step_1 BETWEEN '$start' AND '$end') AS step1, 
                (SELECT COUNT(*) FROM book_telupress
                WHERE book_status = '2' AND book_startdate_realization_step_2 BETWEEN '$start' AND '$end') AS step2, 
                (SELECT COUNT(*) FROM book_telupress
                WHERE book_status = '3' AND book_startdate_realization_step_3 BETWEEN '$start' AND '$end') AS step3, 
                (SELECT COUNT(*) FROM book_telupress
                WHERE book_status = '4' AND book_startdate_realization_step_4 BETWEEN '$start' AND '$end') AS step4, 
                (SELECT COUNT(*) FROM book_telupress
                WHERE book_status = '5' AND book_startdate_realization_step_5 BETWEEN '$start' AND '$end') AS step5, 
                (SELECT COUNT(*) FROM book_telupress
                WHERE book_status = '6' AND book_startdate_realization_step_6 BETWEEN '$start' AND '$end') AS step6, 
                (SELECT COUNT(*) FROM book_telupress
                WHERE book_status = '7' AND book_received_date BETWEEN '$start' AND '$end') AS step7
            FROM book_telupress 
            WHERE book_startdate_realization_step_1 BETWEEN '$start' AND '$end'
        ";
        return DB::select($query);
    }

    function rerata_hari_status_penerimaan($start,$end)
	{   
        $query ="
                select ROUND(IFNULL((proses_pengadaan/total), 0), 0) rerata_penerimaan from (SELECT count(*) total, sum(DATEDIFF(book_date_acceptance, book_date_logistic_process)) proses_pengadaan FROM book_procurement
                left join t_mst_prodi tmp on c_kode_prodi=book_id_prodi
                left join t_mst_fakultas tmf on tmp.c_kode_fakultas=tmf.c_kode_fakultas 
                where book_date_prodi_submission between '$start' and '$end' and book_status='penerimaan'
            ) a";
            
        return DB::select($query);

	}

    public function rerata_hari_status_penerimaan_prodi($start, $end, $prodiid)
    {
        $query = "
                select ROUND(IFNULL((proses_pengadaan/total), 0), 0) rerata_penerimaan from (
                SELECT count(*) as total, 
                sum(DATEDIFF(book_date_acceptance, book_date_logistic_process)) as proses_pengadaan 
                FROM book_procurement
                left join t_mst_prodi tmp on c_kode_prodi=book_id_prodi
                left join t_mst_fakultas tmf on tmp.c_kode_fakultas=tmf.c_kode_fakultas 
                where book_date_prodi_submission between '$start' and '$end'
                and book_status='penerimaan'  
                and book_id_prodi='$prodiid'
            ) a";
        return DB::select($query);
    }

    public function rerata_hari_status_penerimaan_faculty($start, $end, $facid)
    {
        $query ="
                select ROUND(IFNULL((proses_pengadaan/total), 0), 0) rerata_penerimaan from (
                SELECT count(*) total, 
                sum(DATEDIFF(book_date_acceptance, book_date_logistic_process)) proses_pengadaan 
                FROM book_procurement
                left join t_mst_prodi tmp on c_kode_prodi=book_id_prodi
                left join t_mst_fakultas tmf on tmp.c_kode_fakultas=tmf.c_kode_fakultas 
                where book_date_prodi_submission between '$start' and  '$end'
                and book_status='penerimaan'  
                and tmf.c_kode_fakultas='$facid'
            ) a";
            
        return DB::select($query);
    }

    public function total_pengajuan_prodi($start, $end, $prodiid)
    {
        return DB::select("
            select count(*) total, 
            (select count(*) from book_procurement  
            where book_status='pengajuan' and book_date_prodi_submission between ? and ? and book_id_prodi=?) pengajuan,
            (select count(*) from book_procurement  
            where book_status='logistik' and book_date_prodi_submission between ? and ? and book_id_prodi=?) logistik, 
            (select count(*) from book_procurement  
            where book_status='penerimaan' and book_date_prodi_submission between ? and ? and book_id_prodi=?) penerimaan,
            (select count(*) from book_procurement 
            where book_status='r_ketersediaan' and book_date_prodi_submission between ? and ? and book_id_prodi=?) available,
            (select count(*) from book_procurement 
            where book_status='s_email' and book_date_prodi_submission between ? and ? and book_id_prodi=?) email_confirmed
            from book_procurement  
            where book_date_prodi_submission between ? and ? and book_id_prodi=?", 
            [$start, $end, $prodiid, $start, $end, $prodiid, $start, $end, $prodiid, $start, $end, $prodiid, $start, $end, $prodiid, $start, $end, $prodiid]
        );
    }

    public function total_pengajuan_faculty($start, $end, $facid)
    {
        return DB::select("
            select count(*) total, 
            (select count(*) from book_procurement 
            join t_mst_prodi on c_kode_prodi=book_id_prodi
            where book_status='pengajuan' and book_date_prodi_submission between ? and ? and c_kode_fakultas=?) pengajuan,
            (select count(*) from book_procurement 
            join t_mst_prodi on c_kode_prodi=book_id_prodi
            where book_status='logistik' and book_date_prodi_submission between ? and ? and c_kode_fakultas=?) logistik, 
            (select count(*) from book_procurement 
            join t_mst_prodi on c_kode_prodi=book_id_prodi
            where book_status='penerimaan' and book_date_prodi_submission between ? and ? and c_kode_fakultas=?) penerimaan,
            (select count(*) from book_procurement
            join t_mst_prodi on c_kode_prodi=book_id_prodi
            where book_status='r_ketersediaan' and book_date_prodi_submission between ? and ? and c_kode_fakultas=?) available,
            (select count(*) from book_procurement
            join t_mst_prodi on c_kode_prodi=book_id_prodi
            where book_status='s_email' and book_date_prodi_submission between ? and ? and c_kode_fakultas=?) email_confirmed
            from book_procurement 
            join t_mst_prodi on c_kode_prodi=book_id_prodi
            where book_date_prodi_submission between ? and ? and c_kode_fakultas=?", 
            [$start, $end, $facid, $start, $end, $facid, $start, $end, $facid, $start, $end, $facid, $start, $end, $facid, $start, $end, $facid]
        );
    }
}
