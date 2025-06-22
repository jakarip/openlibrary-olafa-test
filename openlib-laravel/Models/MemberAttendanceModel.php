<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MemberAttendanceModel extends Model
{
    protected $table = 'member_attendance';
    protected $primaryKey = 'id';

    // Define the inverse relationship with MemberModel
    public function member()
    {
        return $this->belongsTo(MemberModel::class, 'member_id', 'id');
    }

    public function getallProdi()
    { 
        return DB::select("select c_kode_prodi, nama_prodi, nama_fakultas from t_mst_prodi left join t_mst_fakultas using(c_kode_fakultas) order by nama_fakultas, nama_prodi");
    } 

    public function getJumlahPengunjungByTanggal($kode, $tanggal, $time)
    {
        // $timeCondition = $time == 'day' 
        // ? "TIME(ma.attended_at) BETWEEN '06:00:00' AND '16:30:00'" 
        // : "TIME(ma.attended_at) BETWEEN '16:30:01' AND '22:00:00'";

        // return DB::select("SELECT COUNT(*) AS total FROM member_attendance ma 
        //     LEFT JOIN member m ON m.id = ma.member_id 
        //     WHERE m.master_data_course = ?
        //     AND m.member_type_id IN (5, 6, 9, 10) 
        //     AND ma.attended_at BETWEEN ? AND ? 
        //     AND $timeCondition", [$kode, $tanggal[0], $tanggal[1]]);

        $query = $this->whereHas('member', function ($query) use ($kode) {
            $query->where('master_data_course', $kode)
                ->whereIn('member_type_id', [5, 6, 9, 10]);
                // ->whereNull('deleted_at'); // Ensure soft deletes are considered
        })->whereBetween('attended_at', [$tanggal[0], $tanggal[1]]);
    
        if ($time == 'day') {
            $query->whereTime('attended_at', '>=', '06:00:00')
                ->whereTime('attended_at', '<=', '16:30:00');
        } else {
            $query->whereTime('attended_at', '>=', '16:30:01')
                ->whereTime('attended_at', '<=', '22:00:00');
        }
    
        return $query->count();
    }

    public function getJumlahPengunjungDosenPegawaiByTanggal($tanggal, $time)
    {
        $query = $this->whereHas('member', function ($query) {
            $query->whereIn('member_type_id', [1, 4, 7]);
        })->whereBetween('attended_at', [$tanggal[0], $tanggal[1]]);

        if ($time == 'day') {
            $query->whereTime('attended_at', '>=', '06:00:00')
                ->whereTime('attended_at', '<=', '16:30:00');
        } else {
            $query->whereTime('attended_at', '>=', '16:30:01')
                ->whereTime('attended_at', '<=', '22:00:00');
        }

        return $query->count();
    }

    public function getJumlahPengunjungPublicByTanggal($tanggal, $time)
    {
        $query = $this->whereHas('member', function ($query) {
            $query->where('member_type_id', 19);
        })->whereBetween('attended_at', [$tanggal[0], $tanggal[1]]);

        if ($time == 'day') {
            $query->whereTime('attended_at', '>=', '06:00:00')
                ->whereTime('attended_at', '<=', '16:30:00');
        } else {
            $query->whereTime('attended_at', '>=', '16:30:01')
                ->whereTime('attended_at', '<=', '22:00:00');
        }

        return $query->count();
    }

    public function getJumlahDownloadByTanggal($kode, $tanggal)
    {
            // $data = DB::table('knowledge_item_file_download as kf')
            // ->leftJoin('knowledge_item as kit', 'kit.id', '=', 'kf.knowledge_item_id')
            // ->leftJoin('member as m', 'm.id', '=', 'kf.member_id')
            // ->selectRaw('COUNT(kf.id) as total_downloads, COUNT(m.id) as anggota')
            // ->whereIn('m.member_type_id', [5, 6, 9, 10])
            // ->where('m.master_data_course', $kode)
            // ->where('kit.knowledge_type_id', 21)
            // ->where('kf.created_at', 'like', $tanggal . '%')
            // ->groupBy('kf.member_id')
            // ->get();

            // return $data ?: collect(); 

            // return DB::table('knowledge_item_file_download as kf')
            // ->leftJoin('knowledge_item as kit', 'kit.id', '=', 'kf.knowledge_item_id')
            // ->leftJoin('member as m', 'm.id', '=', 'kf.member_id')
            // ->selectRaw('COUNT(kf.id) as total_downloads, COUNT(m.id) as anggota')
            // ->whereIn('m.member_type_id', [5, 6, 9, 10])
            // ->where('m.master_data_course', $kode)
            // ->where('kit.knowledge_type_id', 21)
            // ->where('kf.created_at', 'like', $tanggal . '%')
            // ->groupBy('kf.member_id')
            // ->get();
        
            return DB::select("
                SELECT SUM(total) AS total, COUNT(id) AS anggota 
                FROM (
                    SELECT COUNT(*) AS total, m.id AS id 
                    FROM knowledge_item_file_download kf  
                    LEFT JOIN knowledge_item kit ON kit.id = kf.knowledge_item_id 	
                    LEFT JOIN member m ON m.id = kf.member_id 
                    WHERE m.member_type_id IN (5, 6, 9, 10) 
                    AND m.master_data_course = '$kode' 
                    AND kit.knowledge_type_id = '21'
                    AND kf.created_at LIKE '$tanggal%' 
                    GROUP BY m.id
                ) AS subquery
            ");
    }

    public function getJumlahPeminjamanByTanggal($kode, $tanggal)
    {
        // $result = DB::select("select sum(total)total,anggota from (	
		// 		select sum(total) total, count(id) anggota from (Select count(*) total,m.id id from rent ma 
		// 		left join member m on m.id=member_id where m.member_type_id in (5,6,9,10) and m.master_data_course='$kode' and ma.rent_date like '$tanggal%' group by member_id)a 
		// 		union 
		// 		select sum(total) total, count(id) anggota from (Select count(*) total,m.id id from rent ma 
		// 		left join member m on m.id=member_id where m.member_type_id in (5,6,9,10) and m.master_data_course='$kode' and ma.rent_date like '$tanggal%' and extended_count='1' group by member_id)a 
		// 		union
		// 		select sum(total*2) total, count(id) anggota from (Select count(*) total,m.id id from rent ma 
		// 		left join member m on m.id=member_id where m.member_type_id in (5,6,9,10) and m.master_data_course='$kode' and ma.rent_date like '$tanggal%' and extended_count='2' group by member_id)a
		// 	)b
        //     ");
        // $kode = '42';
        // $tanggal = '2020-01';
        $result = DB::select("
            SELECT 
                SUM(total) AS total,
                COUNT(DISTINCT id) AS anggota
            FROM (
                SELECT 
                    COUNT(*) AS total,
                    m.id AS id
                FROM 
                    rent ma 
                LEFT JOIN 
                    member m 
                ON 
                    m.id = ma.member_id 
                WHERE 
                    m.member_type_id IN (5, 6, 9, 10) 
                    AND m.master_data_course = ? 
                    AND ma.rent_date LIKE ? 
                GROUP BY 
                    m.id

                UNION ALL

                SELECT 
                    COUNT(*) AS total,
                    m.id AS id
                FROM 
                    rent ma 
                LEFT JOIN 
                    member m 
                ON 
                    m.id = ma.member_id 
                WHERE 
                    m.member_type_id IN (5, 6, 9, 10) 
                    AND m.master_data_course = ? 
                    AND ma.rent_date LIKE ? 
                    AND ma.extended_count = '1' 
                GROUP BY 
                    m.id

                UNION ALL

                SELECT 
                    COUNT(*) * 2 AS total,
                    m.id AS id
                FROM 
                    rent ma 
                LEFT JOIN 
                    member m 
                ON 
                    m.id = ma.member_id 
                WHERE 
                    m.member_type_id IN (5, 6, 9, 10) 
                    AND m.master_data_course = ? 
                    AND ma.rent_date LIKE ? 
                    AND ma.extended_count = '2' 
                GROUP BY 
                    m.id
            ) AS subquery;

        ", [$kode, $tanggal . '%', $kode, $tanggal . '%', $kode, $tanggal . '%']);

        return $result;

        // return DB::table('rent as ma')
        //     ->leftJoin('member as m', 'm.id', '=', 'ma.member_id')
        //     ->selectRaw('COUNT(ma.id) as total, COUNT(m.id) as anggota')
        //     ->whereIn('m.member_type_id', [5, 6, 9, 10])
        //     ->where('m.master_data_course', $kode)
        //     ->where('ma.rent_date', 'like', $tanggal . '%')
        //     ->groupBy('ma.member_id')
        //     ->get();
    }

    public function getJumlahPengembalianByTanggal($kode, $tanggal)
    {
        $result = DB::select("
            SELECT 
                SUM(total) AS total, 
                COUNT(DISTINCT id) AS anggota
            FROM (
                SELECT 
                    COUNT(*) AS total,
                    m.id AS id
                FROM 
                    rent ma 
                LEFT JOIN 
                    member m 
                ON 
                    m.id = ma.member_id 
                WHERE 
                    m.member_type_id IN (5, 6, 9, 10) 
                    AND m.master_data_course = ? 
                    AND ma.return_date LIKE ? 
                GROUP BY 
                    m.id

                UNION ALL

                SELECT 
                    COUNT(*) AS total,
                    m.id AS id
                FROM 
                    rent ma 
                LEFT JOIN 
                    member m 
                ON 
                    m.id = ma.member_id 
                WHERE 
                    m.member_type_id IN (5, 6, 9, 10) 
                    AND m.master_data_course = ? 
                    AND ma.return_date LIKE ? 
                    AND ma.extended_count = '1' 
                GROUP BY 
                    m.id

                UNION ALL

                SELECT 
                    COUNT(*) * 2 AS total,
                    m.id AS id
                FROM 
                    rent ma 
                LEFT JOIN 
                    member m 
                ON 
                    m.id = ma.member_id 
                WHERE 
                    m.member_type_id IN (5, 6, 9, 10) 
                    AND m.master_data_course = ? 
                    AND ma.return_date LIKE ? 
                    AND ma.extended_count = '2' 
                GROUP BY 
                    m.id
            ) AS subquery;

        ", [$kode, $tanggal . '%', $kode, $tanggal . '%', $kode, $tanggal . '%']);

        return $result;
        // return DB::table('rent as ma')
        //     ->leftJoin('member as m', 'm.id', '=', 'ma.member_id')
        //     ->selectRaw('COUNT(ma.id) as total, COUNT(m.id) as anggota')
        //     ->whereIn('m.member_type_id', [5, 6, 9, 10])
        //     ->where('m.master_data_course', $kode)
        //     ->where('ma.return_date', 'like', $tanggal . '%')
        //     ->groupBy('ma.member_id')
        //     ->get();
    }


    public function getJumlahDownloadNonMahasiswaByTanggal($tanggal)
    {

        $result = DB::select("
        SELECT 
            SUM(total) AS total, 
            COUNT(DISTINCT id) AS anggota
        FROM (
            SELECT 
                COUNT(*) AS total, 
                m.id AS id
            FROM 
                knowledge_item_file_download kf
            LEFT JOIN 
                knowledge_item kit 
            ON 
                kit.id = kf.knowledge_item_id
            LEFT JOIN 
                member m 
            ON 
                m.id = kf.member_id
            WHERE 
                m.member_type_id IN (1, 4, 7) 
                AND kit.knowledge_type_id = '21'
                AND kf.created_at LIKE ?
            GROUP BY 
                m.id
        ) AS a;", [$tanggal . '%']);
        
        return $result;
    }

    public function getJumlahPeminjamanNonMahasiswaByTanggal($tanggal)
    {
        $result = DB::select(
            "SELECT 
                SUM(total) AS total,
                COUNT(DISTINCT member_id) AS anggota
            FROM (
                SELECT 
                    COUNT(*) AS total,
                    m.id AS member_id
                FROM 
                    rent ma
                LEFT JOIN 
                    member m 
                ON 
                    m.id = ma.member_id
                WHERE 
                    m.member_type_id IN (1, 4, 7) 
                    AND ma.rent_date LIKE ?
                GROUP BY 
                    m.id
    
                UNION ALL
    
                SELECT 
                    COUNT(*) AS total,
                    m.id AS member_id
                FROM 
                    rent ma
                LEFT JOIN 
                    member m 
                ON 
                    m.id = ma.member_id
                WHERE 
                    m.member_type_id IN (1, 4, 7) 
                    AND ma.rent_date LIKE ? 
                    AND ma.extended_count = '1'
                GROUP BY 
                    m.id
    
                UNION ALL
    
                SELECT 
                    COUNT(*) * 2 AS total,
                    m.id AS member_id
                FROM 
                    rent ma
                LEFT JOIN 
                    member m 
                ON 
                    m.id = ma.member_id
                WHERE 
                    m.member_type_id IN (1, 4, 7) 
                    AND ma.rent_date LIKE ? 
                    AND ma.extended_count = '2'
                GROUP BY 
                    m.id
            ) AS subquery
        ", [$tanggal . '%', $tanggal . '%', $tanggal . '%']);
    
        return $result;
    }

    public function getJumlahPengembalianNonMahasiswaByTanggal($tanggal)
    {
        $result = DB::select(
            "SELECT 
                SUM(total) AS total,
                COUNT(DISTINCT member_id) AS anggota
            FROM (
                SELECT 
                    COUNT(*) AS total,
                    m.id AS member_id
                FROM 
                    rent ma
                LEFT JOIN 
                    member m 
                ON 
                    m.id = ma.member_id
                WHERE 
                    m.member_type_id IN (1, 4, 7) 
                    AND ma.return_date LIKE ?
                GROUP BY 
                    m.id
    
                UNION ALL
    
                SELECT 
                    COUNT(*) AS total,
                    m.id AS member_id
                FROM 
                    rent ma
                LEFT JOIN 
                    member m 
                ON 
                    m.id = ma.member_id
                WHERE 
                    m.member_type_id IN (1, 4, 7) 
                    AND ma.return_date LIKE ? 
                    AND ma.extended_count = '1'
                GROUP BY 
                    m.id
    
                UNION ALL
    
                SELECT 
                    COUNT(*) * 2 AS total,
                    m.id AS member_id
                FROM 
                    rent ma
                LEFT JOIN 
                    member m 
                ON 
                    m.id = ma.member_id
                WHERE 
                    m.member_type_id IN (1, 4, 7) 
                    AND ma.return_date LIKE ? 
                    AND ma.extended_count = '2'
                GROUP BY 
                    m.id
            ) AS subquery
        ", [$tanggal . '%', $tanggal . '%', $tanggal . '%']);
    
        return $result;
    }

}
