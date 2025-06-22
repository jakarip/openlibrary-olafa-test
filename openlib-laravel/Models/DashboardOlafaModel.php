<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DashboardOlafaModel extends Model
{
    function getFileTotal()
    {
        $query = "SELECT * FROM file_total";
        return DB::select($query);
    }

    function getTotalJudulFisikAll($year, $digital)
    {
        $query = "
            SELECT COUNT(id) AS total
            FROM (
                SELECT kt.id
                FROM knowledge_item kt
                LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
                LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                WHERE ks.active = '1'
                    AND kp.active = '1'
                    AND kk.knowledge_type_id NOT IN ($digital)
                GROUP BY kt.id
            ) a
        ";
        return DB::select($query);
    }

    function getTotalKoleksiFisikAll($year, $digital)
    {
        $query = "
            SELECT COUNT(kk.id) AS total
            FROM knowledge_item kt
            LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
            LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
            LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
            WHERE ks.active = '1'
                AND kp.active = '1'
                AND kk.status NOT IN (4, 5)
                AND kk.knowledge_type_id NOT IN ($digital)
        ";
        return DB::select($query);
    }

    function getTotalJudulDigitalAll($year, $digital)
    {
        $query = "
            SELECT COUNT(id) AS total
            FROM (
                SELECT kt.id
                FROM knowledge_item kt
                LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
                LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                WHERE ks.active = '1'
                    AND kp.active = '1'
                    AND kk.knowledge_type_id IN ($digital)
                GROUP BY kt.id
            ) a
        ";
        return DB::select($query);
    }

    function getTotalKoleksiDigitalAll($year, $digital)
    {
        $query = "
            SELECT COUNT(kk.id) AS total
            FROM knowledge_item kt
            LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
            LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
            LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
            WHERE ks.active = '1'
                AND kp.active = '1'
                AND kk.status NOT IN (4, 5)
                AND kk.knowledge_type_id IN ($digital)
        ";
        return DB::select($query);
    }

    function getTotalJudulFisik($year, $digital)
    {
        $query = "
            SELECT COUNT(id) AS total
            FROM (
                SELECT kt.id
                FROM knowledge_item kt
                LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
                LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                WHERE ks.active = '1'
                    AND kp.active = '1'
                    AND kt.entrance_date BETWEEN '$year-01-01' AND '$year-12-31'
                    AND kk.knowledge_type_id NOT IN ($digital)
                GROUP BY kt.id
            ) a
        ";
        return DB::select($query);
    }

    function getTotalKoleksiFisik($year, $digital)
    {
        $query = "
            SELECT COUNT(kk.id) AS total
            FROM knowledge_item kt
            LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
            LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
            LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
            WHERE ks.active = '1'
                AND kp.active = '1'
                AND kk.status NOT IN (4, 5)
                AND kk.entrance_date BETWEEN '$year-01-01' AND '$year-12-31'
                AND kk.knowledge_type_id NOT IN ($digital)
        ";
        return DB::select($query);
    }

    function getTotalJudulDigital($year, $digital)
    {
        $query = "
            SELECT COUNT(id) AS total
            FROM (
                SELECT kt.id
                FROM knowledge_item kt
                LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
                LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                WHERE ks.active = '1'
                    AND kp.active = '1'
                    AND kt.entrance_date BETWEEN '$year-01-01' AND '$year-12-31'
                    AND kk.knowledge_type_id IN ($digital)
                GROUP BY kt.id
            ) a
        ";
        return DB::select($query);
    }

    function getTotalKoleksiDigital($year, $digital)
    {
        $query = "
            SELECT COUNT(kk.id) AS total
            FROM knowledge_item kt
            LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
            LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
            LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
            WHERE ks.active = '1'
                AND kp.active = '1'
                AND kk.status NOT IN (4, 5)
                AND kk.entrance_date BETWEEN '$year-01-01' AND '$year-12-31'
                AND kk.knowledge_type_id IN ($digital)
        ";
        return DB::select($query);
    }

    function getTotalJudulFisikPerBulan($date, $digital)
    {
        $query = "
            SELECT COUNT(id) AS total, DATE_FORMAT(entrance_date, '%Y-%m') AS tgl
            FROM (
                SELECT kt.id, kt.entrance_date
                FROM knowledge_item kt
                LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
                LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                WHERE ks.active = '1'
                    AND kp.active = '1'
                    AND YEAR(kt.entrance_date) = $date
                    AND kk.knowledge_type_id NOT IN ($digital)
                GROUP BY kt.id, kt.entrance_date
            ) a
            GROUP BY DATE_FORMAT(entrance_date, '%Y-%m')
        ";
        return DB::select($query);
    }

    function getTotalKoleksiFisikPerBulan($date, $digital)
    {
        $query = "
            SELECT COUNT(kk.id) AS total, DATE_FORMAT(kk.entrance_date, '%Y-%m') AS tgl
            FROM knowledge_item kt
            LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
            LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
            LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
            WHERE ks.active = '1'
                AND kp.active = '1'
                AND kk.status NOT IN (4, 5)
                AND YEAR(kk.entrance_date) = $date
                AND kk.knowledge_type_id NOT IN ($digital)
            GROUP BY DATE_FORMAT(kk.entrance_date, '%Y-%m')
        ";
        return DB::select($query);
    }

    function getTotalJudulDigitalPerBulan($date, $digital)
    {
        $query = "
            SELECT COUNT(id) AS total, DATE_FORMAT(entrance_date, '%Y-%m') AS tgl
            FROM (
                SELECT kt.id, kt.entrance_date
                FROM knowledge_item kt
                LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
                LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                WHERE ks.active = '1'
                    AND kp.active = '1'
                    AND YEAR(kt.entrance_date) = $date
                    AND kk.knowledge_type_id IN ($digital)
                GROUP BY kt.id, kt.entrance_date
            ) a
            GROUP BY DATE_FORMAT(entrance_date, '%Y-%m')
        ";
        return DB::select($query);
    }

    function getTotalKoleksiDigitalPerBulan($date, $digital)
    {
        $query = "
            SELECT COUNT(kk.id) AS total, DATE_FORMAT(kk.entrance_date, '%Y-%m') AS tgl
            FROM knowledge_item kt
            LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
            LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
            LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
            WHERE ks.active = '1'
                AND kp.active = '1'
                AND kk.status NOT IN (4, 5)
                AND YEAR(kk.entrance_date) = $date
                AND kk.knowledge_type_id IN ($digital)
            GROUP BY DATE_FORMAT(kk.entrance_date, '%Y-%m')
        ";
        return DB::select($query);
    }

    function getTotalCivitasAll()
    {
        $query = "
            SELECT COUNT(id) AS total
            FROM member
            WHERE member_type_id IN (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 25)
                AND status = '1'
        ";
        return DB::select($query);
    }

    function getTotalCivitasAllWebMobile()
    {
        $query = "
            SELECT COUNT(id) AS total
            FROM member
            WHERE member_type_id IN (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 25)
                AND status = '1'
                AND master_data_uuid IS NOT NULL
        ";
        return DB::select($query);
    }

    function getTotalCivitasAllWeb()
    {
        $query = "
            SELECT COUNT(id) AS total
            FROM member
            WHERE member_type_id IN (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 25)
                AND status = '1'
                AND master_data_uuid IS NULL
        ";
        return DB::select($query);
    }

    function getTotalUmumAllWebMobile()
    {
        $query = "
            SELECT COUNT(id) AS total
            FROM member
            WHERE member_type_id IN (19, 20, 21, 22, 23, 24, 26, 27, 28, 29, 30, 31)
                AND status = '1'
                AND master_data_uuid IS NOT NULL
        ";
        return DB::select($query);
    }

    function getTotalUmumAllWeb()
    {
        $query = "
            SELECT COUNT(id) AS total
            FROM member
            WHERE member_type_id IN (19, 20, 21, 22, 23, 24, 26, 27, 28, 29, 30, 31)
                AND status = '1'
                AND master_data_uuid IS NULL
        ";
        return DB::select($query);
    }

    function getTotalCivitasWebMobile($year)
    {
        $query = "
            SELECT COUNT(id) AS total
            FROM member
            WHERE created_at BETWEEN '$year-01-01' AND '$year-12-31'
                AND member_type_id IN (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 25)
                AND status = '1'
                AND master_data_uuid IS NOT NULL
        ";
        return DB::select($query);
    }

    function getTotalCivitasWeb($year)
    {
        $query = "
            SELECT COUNT(id) AS total
            FROM member
            WHERE created_at BETWEEN '$year-01-01' AND '$year-12-31'
                AND member_type_id IN (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 25)
                AND status = '1'
                AND master_data_uuid IS NULL
        ";
        return DB::select($query);
    }

    function getTotalUmumWebMobile($year)
    {
        $query = "
            SELECT COUNT(id) AS total
            FROM member
            WHERE created_at BETWEEN '$year-01-01' AND '$year-12-31'
                AND member_type_id IN (19, 20, 21, 22, 23, 24, 26, 27, 28, 29, 30, 31)
                AND status = '1'
                AND master_data_uuid IS NOT NULL
        ";
        return DB::select($query);
    }

    function getTotalUmumWeb($year)
    {
        $query = "
            SELECT COUNT(id) AS total
            FROM member
            WHERE created_at BETWEEN '$year-01-01' AND '$year-12-31'
                AND member_type_id IN (19, 20, 21, 22, 23, 24, 26, 27, 28, 29, 30, 31)
                AND status = '1'
                AND master_data_uuid IS NULL
        ";
        return DB::select($query);
    }

    function getTotalCivitasWebMobilePerBulan($date)
    {
        $query = "
            SELECT months.month, COALESCE(counts.total, 0) AS total
            FROM (
                SELECT '$date-01' AS month UNION ALL
                SELECT '$date-02' UNION ALL
                SELECT '$date-03' UNION ALL
                SELECT '$date-04' UNION ALL
                SELECT '$date-05' UNION ALL
                SELECT '$date-06' UNION ALL
                SELECT '$date-07' UNION ALL
                SELECT '$date-08' UNION ALL
                SELECT '$date-09' UNION ALL
                SELECT '$date-10' UNION ALL
                SELECT '$date-11' UNION ALL
                SELECT '$date-12'
            ) AS months
            LEFT JOIN (
                SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total
                FROM member
                WHERE YEAR(created_at) = $date
                    AND member_type_id IN (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 25)
                    AND status = '1'
                    AND master_data_uuid IS NOT NULL
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ) AS counts
            ON months.month = counts.month
            ORDER BY months.month
        ";
        return DB::select($query);
    }

    function getTotalCivitasWebPerBulan($date)
    {
        $query = "
            SELECT months.month, COALESCE(counts.total, 0) AS total
            FROM (
                SELECT '$date-01' AS month UNION ALL
                SELECT '$date-02' UNION ALL
                SELECT '$date-03' UNION ALL
                SELECT '$date-04' UNION ALL
                SELECT '$date-05' UNION ALL
                SELECT '$date-06' UNION ALL
                SELECT '$date-07' UNION ALL
                SELECT '$date-08' UNION ALL
                SELECT '$date-09' UNION ALL
                SELECT '$date-10' UNION ALL
                SELECT '$date-11' UNION ALL
                SELECT '$date-12'
            ) AS months
            LEFT JOIN (
                SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total
                FROM member
                WHERE YEAR(created_at) = $date
                    AND member_type_id IN (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 25)
                    AND status = '1'
                    AND master_data_uuid IS NULL
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ) AS counts
            ON months.month = counts.month
            ORDER BY months.month
        ";
        return DB::select($query);
    }

    function getTotalUmumWebMobilePerBulan($date)
    {
        $query = "
            SELECT months.month, COALESCE(counts.total, 0) AS total
            FROM (
                SELECT '$date-01' AS month UNION ALL
                SELECT '$date-02' UNION ALL
                SELECT '$date-03' UNION ALL
                SELECT '$date-04' UNION ALL
                SELECT '$date-05' UNION ALL
                SELECT '$date-06' UNION ALL
                SELECT '$date-07' UNION ALL
                SELECT '$date-08' UNION ALL
                SELECT '$date-09' UNION ALL
                SELECT '$date-10' UNION ALL
                SELECT '$date-11' UNION ALL
                SELECT '$date-12'
            ) AS months
            LEFT JOIN (
                SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total
                FROM member
                WHERE YEAR(created_at) = $date
                    AND member_type_id IN (19, 20, 21, 22, 23, 24, 26, 27, 28, 29, 30, 31)
                    AND status = '1'
                    AND master_data_uuid IS NOT NULL
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ) AS counts
            ON months.month = counts.month
            ORDER BY months.month
        ";
        return DB::select($query);
    }

    function getTotalUmumWebPerBulan($date)
    {
        $query = "
            SELECT months.month, COALESCE(counts.total, 0) AS total
            FROM (
                SELECT '$date-01' AS month UNION ALL
                SELECT '$date-02' UNION ALL
                SELECT '$date-03' UNION ALL
                SELECT '$date-04' UNION ALL
                SELECT '$date-05' UNION ALL
                SELECT '$date-06' UNION ALL
                SELECT '$date-07' UNION ALL
                SELECT '$date-08' UNION ALL
                SELECT '$date-09' UNION ALL
                SELECT '$date-10' UNION ALL
                SELECT '$date-11' UNION ALL
                SELECT '$date-12'
            ) AS months
            LEFT JOIN (
                SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total
                FROM member
                WHERE YEAR(created_at) = $date
                    AND member_type_id IN (19, 20, 21, 22, 23, 24, 26, 27, 28, 29, 30, 31)
                    AND status = '1'
                    AND master_data_uuid IS NULL
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ) AS counts
            ON months.month = counts.month
            ORDER BY months.month
        ";
        return DB::select($query);
    }

    function getTotalPengunjungFisik($year)
    {
        $tahun = $year;
        $query = "select count(*)total from member_attendance where date_format(attended_at,'%Y') like '$tahun%' ";
        return DB::select($query);
    }

    function getTotalPengunjungOnline($year)
    {
        $tahun = $year;
        $query = "select (januari+februari+maret+april+mei+juni+juli+agustus+september+oktober+november+desember) AS total from online_visitor where year='$tahun' and type='users'";
        return DB::select($query);
    }

    function getTotalPageView($year)
    {
        $tahun = $year;
        $query = "select (januari+februari+maret+april+mei+juni+juli+agustus+september+oktober+november+desember) AS total from online_visitor where year='$tahun' and type='pageviews'";
        return DB::select($query);
    }

    function getTotalPengunjungOnlineEproc($year)
    {
        $tahun = $year;
        $query = "select (januari+februari+maret+april+mei+juni+juli+agustus+september+oktober+november+desember) AS total from online_visitor_eproc where year='$tahun' and type='users'";
        return DB::select($query);
    }

    function getTotalPageViewEproc($year)
    {
        $tahun = $year;
        $query = "select (januari+februari+maret+april+mei+juni+juli+agustus+september+oktober+november+desember) AS total from online_visitor_eproc where year='$tahun' and type='pageviews'";
        return DB::select($query);
    }

    function getTotalOnlineVisitorPerBulan($date)
    {
        $query = "select * from online_visitor where year='$date' and type='users'";
        return DB::select($query);
    }
    function getTotalPageViewsPerBulan($date)
    {
        $query = "select * from online_visitor where year='$date' and type='pageviews'";
        return DB::select($query);
    }

    function getTotalOnlineVisitorPerBulanEproc($date)
    {
        $query = "select * from online_visitor_eproc where year='$date' and type='users'";
        return DB::select($query);
    }
    function getTotalPageViewsPerBulanEproc($date)
    {
        $query = "select * from online_visitor_eproc where year='$date' and type='pageviews'";
        return DB::select($query);
    }

    function getTotalPengunjungFisikPerBulan($date)
    {
        $query = "
            SELECT months.month, COALESCE(counts.total, 0) AS total FROM
        (
            SELECT '$date-01' AS month UNION ALL SELECT '$date-02' UNION ALL SELECT '$date-03' UNION ALL SELECT '$date-04' UNION ALL SELECT '$date-05' UNION ALL SELECT '$date-06' UNION ALL SELECT '$date-07' UNION ALL SELECT '$date-08' UNION ALL SELECT '$date-09' UNION ALL SELECT '$date-10' UNION ALL SELECT '$date-11' UNION ALL SELECT '$date-12'
        ) AS months
        LEFT JOIN (
            SELECT DATE_FORMAT(attended_at, '%Y-%m') AS month, COUNT(*) AS total
            FROM member_attendance
            WHERE YEAR(attended_at) = $date
            GROUP BY DATE_FORMAT(attended_at, '%Y-%m')
        ) AS counts
        ON months.month = counts.month ORDER BY months.month";
        return DB::select($query);

        // return $this->db->query("select count(*)total,DATE_FORMAT(attended_at,'%Y-%m')tgl  from member_attendance
        //      where YEAR(attended_at)=$date
        //      group by DATE_FORMAT(attended_at,'%Y-%m')");
    }

    function getTotalPeminjaman($year)
    {
        $tahun = $year;
        $query = "select count(*)total from rent where date_format(rent_date,'%Y') like '$tahun%'";
        return DB::select($query);
    }

    function getTotalPengembalian($year)
    {
        $tahun = $year;
        $query = "select count(*)total from rent where return_date is not null and date_format(return_date,'%Y') like '$tahun%' ";
        return DB::select($query);
    }

    function getTotalRoom($year)
    {
        $tahun = $year;
        $query = "select count(*)total from room.booking where date_format(bk_startdate,'%Y') like '$tahun%' ";
        return DB::select($query);
    }

    function getTotalBds($year)
    {
        $tahun = $year;
        $query = "select count(*)total from book_delivery_service where date_format(bds_createdate,'%Y') like '$tahun%' ";
        return DB::select($query);
    }

    function getTotalUsulan($year)
    {
        $tahun = $year;
        $query = "select count(*)total from usulan_bahanpustaka where date_format(bp_createdate,'%Y') like '$tahun%' ";
        return DB::select($query);
    }

    function getTotalSBKP($year)
    {
        $tahun = $year;
        $query = "select count(*)total from free_letter where date_format(created_at,'%Y') like '$tahun%' ";
        return DB::select($query);
    }

    function getTotalAccessEbook($year)
    {
        $tahun = $year;
        $query = "select (januari+februari+maret+april+mei+juni+juli+agustus+september+oktober+november+desember) AS total from online_access where year='$tahun' and type='ebook'";
        return DB::select($query);
    }

    function getTotalAccessKaryaIlmiah($year)
    {
        $tahun = $year;
        $query = "select (januari+februari+maret+april+mei+juni+juli+agustus+september+oktober+november+desember) AS total from online_access where year='$tahun' and type='karyailmiah'";
        return DB::select($query);
    }

    // function getTotalPeminjamanPerBulan($date)
    // {
    //  return $this->db->query("select count(*)total,DATE_FORMAT(rent_date,'%Y-%m')tgl from rent
    //      where YEAR(rent_date)=$date
    //      group by DATE_FORMAT(rent_date,'%Y-%m')");
    // }



    function getTotalPeminjamanPerBulan($date)
    {
        $query = "
            SELECT months.month, COALESCE(counts.total, 0) AS total FROM
        (
            SELECT '$date-01' AS month UNION ALL SELECT '$date-02' UNION ALL SELECT '$date-03' UNION ALL SELECT '$date-04' UNION ALL SELECT '$date-05' UNION ALL SELECT '$date-06' UNION ALL SELECT '$date-07' UNION ALL SELECT '$date-08' UNION ALL SELECT '$date-09' UNION ALL SELECT '$date-10' UNION ALL SELECT '$date-11' UNION ALL SELECT '$date-12'
        ) AS months
        LEFT JOIN (
            SELECT DATE_FORMAT(rent_date, '%Y-%m') AS month, COUNT(*) AS total
            FROM rent
            WHERE YEAR(rent_date) = $date
            GROUP BY DATE_FORMAT(rent_date, '%Y-%m')
        ) AS counts
        ON months.month = counts.month ORDER BY months.month";
        return DB::select($query);

        // return $this->db->query("
        // select * ,IFNULL(totals,0) total from (
        //  select *,month_name tgl from months left join (
        //      select count(*)totals,DATE_FORMAT(rent_date,'%Y-%m')tgls from rent
        //              where YEAR(rent_date)='$date'
        //              group by DATE_FORMAT(rent_date,'%Y-%m')) a on month_name=a.tgls
        //              where month_name like '$date%'
        //              )b order by month_name");
    }

    function getTotalPengembalianPerBulan($date)
    {
        $query = "
            SELECT months.month, COALESCE(counts.total, 0) AS total FROM
        (
            SELECT '$date-01' AS month UNION ALL SELECT '$date-02' UNION ALL SELECT '$date-03' UNION ALL SELECT '$date-04' UNION ALL SELECT '$date-05' UNION ALL SELECT '$date-06' UNION ALL SELECT '$date-07' UNION ALL SELECT '$date-08' UNION ALL SELECT '$date-09' UNION ALL SELECT '$date-10' UNION ALL SELECT '$date-11' UNION ALL SELECT '$date-12'
        ) AS months
        LEFT JOIN (
            SELECT DATE_FORMAT(return_date, '%Y-%m') AS month, COUNT(*) AS total
            FROM rent
            WHERE YEAR(return_date) = $date
            GROUP BY DATE_FORMAT(return_date, '%Y-%m')
        ) AS counts
        ON months.month = counts.month ORDER BY months.month";
        return DB::select($query);

        // return $this->db->query("select count(*)total,DATE_FORMAT(return_date,'%Y-%m')tgl from rent
        //  where YEAR(return_date)=$date
        //  group by DATE_FORMAT(return_date,'%Y-%m')");
    }

    function getTotalRoomPerBulan($date)
    {
        $query = "
            SELECT months.month, COALESCE(counts.total, 0) AS total FROM
        (
            SELECT '$date-01' AS month UNION ALL SELECT '$date-02' UNION ALL SELECT '$date-03' UNION ALL SELECT '$date-04' UNION ALL SELECT '$date-05' UNION ALL SELECT '$date-06' UNION ALL SELECT '$date-07' UNION ALL SELECT '$date-08' UNION ALL SELECT '$date-09' UNION ALL SELECT '$date-10' UNION ALL SELECT '$date-11' UNION ALL SELECT '$date-12'
        ) AS months
        LEFT JOIN (
            SELECT DATE_FORMAT(bk_startdate, '%Y-%m') AS month, COUNT(*) AS total
            FROM room.booking
            WHERE YEAR(bk_startdate) = $date
            GROUP BY DATE_FORMAT(bk_startdate, '%Y-%m')
        ) AS counts
        ON months.month = counts.month ORDER BY months.month";
        return DB::select($query);

        // return $this->db->query("select count(*)total,DATE_FORMAT(bk_startdate,'%Y-%m')tgl from room.booking
        //  where YEAR(bk_startdate)=$date
        //  group by DATE_FORMAT(bk_startdate,'%Y-%m')");
    }

    function getTotalBdsPerBulan($date)
    {
        $query = "
            SELECT months.month, COALESCE(counts.total, 0) AS total FROM
        (
            SELECT '$date-01' AS month UNION ALL SELECT '$date-02' UNION ALL SELECT '$date-03' UNION ALL SELECT '$date-04' UNION ALL SELECT '$date-05' UNION ALL SELECT '$date-06' UNION ALL SELECT '$date-07' UNION ALL SELECT '$date-08' UNION ALL SELECT '$date-09' UNION ALL SELECT '$date-10' UNION ALL SELECT '$date-11' UNION ALL SELECT '$date-12'
        ) AS months
        LEFT JOIN (
            SELECT DATE_FORMAT(bds_createdate, '%Y-%m') AS month, COUNT(*) AS total
            FROM book_delivery_service
            WHERE YEAR(bds_createdate) = $date
            GROUP BY DATE_FORMAT(bds_createdate, '%Y-%m')
        ) AS counts
        ON months.month = counts.month ORDER BY months.month";
        return DB::select($query);

        // return $this->db->query("
        // select count(*)total,DATE_FORMAT(bds_createdate,'%Y-%m')tgl from book_delivery_service
        //      where YEAR(bds_createdate)=$date
        //      group by DATE_FORMAT(bds_createdate,'%Y-%m')");
    }

    function getTotalUsulanPerBulan($date)
    {
        $query = "
            SELECT months.month, COALESCE(counts.total, 0) AS total FROM
        (
            SELECT '$date-01' AS month UNION ALL SELECT '$date-02' UNION ALL SELECT '$date-03' UNION ALL SELECT '$date-04' UNION ALL SELECT '$date-05' UNION ALL SELECT '$date-06' UNION ALL SELECT '$date-07' UNION ALL SELECT '$date-08' UNION ALL SELECT '$date-09' UNION ALL SELECT '$date-10' UNION ALL SELECT '$date-11' UNION ALL SELECT '$date-12'
        ) AS months
        LEFT JOIN (
            SELECT DATE_FORMAT(bp_createdate, '%Y-%m') AS month, COUNT(*) AS total
            FROM usulan_bahanpustaka
            WHERE YEAR(bp_createdate) = $date
            GROUP BY DATE_FORMAT(bp_createdate, '%Y-%m')
        ) AS counts
        ON months.month = counts.month ORDER BY months.month";
        return DB::select($query);

        // return $this->db->query("select count(*)total,DATE_FORMAT(bp_createdate,'%Y-%m')tgl from usulan_bahanpustaka
        //  where YEAR(bp_createdate)=$date
        //  group by DATE_FORMAT(bp_createdate,'%Y-%m')");
    }

    function getTotalSBKPPerBulan($date)
    {
        $query = "
            SELECT months.month, COALESCE(counts.total, 0) AS total FROM
        (
            SELECT '$date-01' AS month UNION ALL SELECT '$date-02' UNION ALL SELECT '$date-03' UNION ALL SELECT '$date-04' UNION ALL SELECT '$date-05' UNION ALL SELECT '$date-06' UNION ALL SELECT '$date-07' UNION ALL SELECT '$date-08' UNION ALL SELECT '$date-09' UNION ALL SELECT '$date-10' UNION ALL SELECT '$date-11' UNION ALL SELECT '$date-12'
        ) AS months
        LEFT JOIN (
            SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total
            FROM free_letter
            WHERE YEAR(created_at) = $date
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ) AS counts
        ON months.month = counts.month ORDER BY months.month";
        return DB::select($query);
    }


    function getTotalAccessEbookPerBulan($date)
    {
        $query = "select * from online_access where year='$date' and type='ebook'";
        return DB::select($query);
    }


    function getTotalAccessKaryaIlmiahPerBulan($date)
    {
        $query = "select * from online_access where year='$date' and type='karyailmiah'";
        return DB::select($query);
    }

    function getRasioMKperProdi()
    {
        $query = "select nama_prodi,mk,buku,round(buku/mk *100,2)rasio from (
        select nama_fakultas,nama_prodi,(select count(*)total from master_subject where course_code=c_kode_prodi
        and curriculum_code='2016')mk,
        (
        select count(master_subject_id) from (
        select * from master_subject ms left join knowledge_item_subject kis
        on ms.id=kis.master_subject_id
        and curriculum_code='2016'  group by master_subject_id )a where  course_code=c_kode_prodi and master_subject_id is not null
        )buku
        from t_mst_fakultas tmf
        left join t_mst_prodi tmp using (c_kode_fakultas)
        group by nama_fakultas,nama_prodi)b";
        return DB::select($query);
    }

    function getRasioMKperProdi2()
    {
        $query = "select c_kode_prodi,nama_fakultas,nama_prodi,
                    (select count(kt.id) from master_subject msu
                    left join knowledge_item_subject kis on kis.master_subject_id=msu.id
                    left join knowledge_item kt on knowledge_item_id=kt.id
                    left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
                    left join knowledge_type kp on kt.knowledge_type_id=kp.id
                    where ks.active='1' and kp.active='1'
                    and knowledge_type_id in (1, 2, 3, 33, 40, 41, 59, 65, 21) and curriculum_code='2020' and msu.course_code=tmp.c_kode_prodi) judul,
                    (select count(kk.id) from master_subject msu
                    left join knowledge_item_subject kis on kis.master_subject_id=msu.id
                    left join knowledge_item kt on kt.id = kis.knowledge_item_id
                    left join knowledge_stock kk on kk.knowledge_item_id = kt.id
                    left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
                    left join knowledge_type kp on kt.knowledge_type_id=kp.id
                    where ks.active='1' and kp.active='1' and kk.status not in(4,5)
                    and kk.knowledge_type_id in (1, 2, 3, 33, 40, 41, 59, 65, 21) and curriculum_code='2020' and msu.course_code=tmp.c_kode_prodi) eks,
                    (select count(*) from master_subject where curriculum_code='2020' and course_code=tmp.c_kode_prodi) mk,
                    (select count(*) from master_subject where curriculum_code='2020' and course_code=tmp.c_kode_prodi and master_subject.id in(select master_subject_id from knowledge_item_subject)) mkadabuku
                    from t_mst_fakultas tmf left join t_mst_prodi tmp on tmp.c_kode_fakultas=tmf.c_kode_fakultas where (nama_prodi not like '%Pindahan%' and nama_prodi not like '%International%' and nama_prodi not like '%Internasional%')";
        return DB::select($query);
    }

    function getTotalJournalVisitorPerBulan($date)
    {
        $query = "select count(*)total,DATE_FORMAT(jv_date,'%Y-%m')tgl from journal_visitor
            where YEAR(jv_date)=$date
            group by DATE_FORMAT(jv_date,'%Y-%m')";
        return DB::select($query);
    }

    function getTotalPengunjungJournal()
    {
        $tahun = date('Y');
        $query = "select count(*)total from journal_visitor where date_format(jv_date,'%Y') like '$tahun%' ";
        return DB::select($query);
    }

    function getLoaPending()
    {
        $tahun = date('Y');
        $query = "select * from workflow_document left join member m on m.id=member_id where latest_state_id='64'";
        return DB::select($query);
    }

    function getdoc_noloa($date)
    {

        $query = "select * from (select wdd.id,wdd.latest_state_id,  wdd.created_at, master_data_user, master_data_fullname, title,(SELECT count( * )
FROM free_letter
WHERE member_number = m.master_data_user
) free_letter, (select count(*) from workflow_document_file where document_id=wdd.id and upload_type_id='83') file
            from workflow_document wdd  left join workflow_state_sort_id wss on wss.id_state=wdd.latest_state_id
            left join member m on wdd.member_id=m.id

            where    workflow_id='1' and latest_state_id='52'
            and wss.id_ws =
            (select max(id_ws) from workflow_document wd left join workflow_state_sort_id wid on wid.id_state=wd.latest_state_id
            where wd.member_id=wdd.member_id and workflow_id='1') and wdd.created_at<= '$date'   group by wdd.member_id order by free_letter desc)a where file='0'";
        return DB::select($query);
    }
}
