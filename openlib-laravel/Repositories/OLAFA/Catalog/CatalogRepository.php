<?php

namespace App\Repositories\OLAFA\Catalog;

use Illuminate\Support\Facades\DB;

class CatalogRepository
{

    public function getKnowledgeType()
    {
        return DB::table('knowledge_type')
            ->where('active', '1')
            ->orderBy('name')
            ->get();
    }

    public function getLocations()
    {
        return DB::table('item_location')
            ->select('id', 'name')
            ->where('show_as_footer', '1')
            ->orderBy('name')
            ->get();
    }

    public function getAllKnowledgeType($where)
    {
        $query = "
            SELECT ky.*,
                (
                    SELECT count(*) total
                    FROM (
                        SELECT kt.knowledge_type_id id
                        FROM knowledge_item kt
                        LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
                        LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                        LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                        WHERE ks.active = '1' AND kp.active = '1' $where
                        GROUP BY kt.id, kt.knowledge_type_id
                    ) a
                    WHERE id = ky.id
                ) AS judul,
                (
                    SELECT count(*) total
                    FROM knowledge_item kt
                    JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
                    LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                    LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                    WHERE ks.active = '1' AND kp.active = '1' $where AND kp.id = ky.id
                ) AS eksemplar
            FROM (
                SELECT
                    kp.name nama, kp.id,
                    SUM(CASE WHEN kk.status = '1' THEN 1 ELSE 0 END) AS tersedia,
                    SUM(CASE WHEN kk.status = '2' THEN 1 ELSE 0 END) AS dipinjam,
                    SUM(CASE WHEN kk.status = '3' THEN 1 ELSE 0 END) AS rusak,
                    SUM(CASE WHEN kk.status = '4' THEN 1 ELSE 0 END) AS hilang,
                    SUM(CASE WHEN kk.status = '5' THEN 1 ELSE 0 END) AS expired,
                    SUM(CASE WHEN kk.status = '6' THEN 1 ELSE 0 END) AS hilang_diganti,
                    SUM(CASE WHEN kk.status = '7' THEN 1 ELSE 0 END) AS diolah,
                    SUM(CASE WHEN kk.status = '8' THEN 1 ELSE 0 END) AS cadangan,
                    SUM(CASE WHEN kk.status = '9' THEN 1 ELSE 0 END) AS weeding
                FROM knowledge_item kt
                LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
                LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                WHERE ks.active = '1' AND kp.active = '1' $where
                GROUP BY kp.id, kp.name
                ORDER BY kp.name
            ) ky

        ";

        return DB::select($query);
    }

    public function getDetailStatistics($id, $type)
    {
        $query = match ($type) {
            'judul' => "
                SELECT 
                kt.id,
                GROUP_CONCAT(DISTINCT cc.code) AS klasifikasi,
                GROUP_CONCAT(DISTINCT kk.code) AS barcode,
                GROUP_CONCAT(DISTINCT ks.name) AS subjek,
                GROUP_CONCAT(DISTINCT kk.status) AS status,
                kt.code,
                kt.title,
                kt.author,
                kt.publisher_name,
                kt.origination,
                kt.created_at,
                kt.updated_at
                FROM knowledge_item kt
                LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
                LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                LEFT JOIN classification_code cc ON kt.classification_code_id = cc.id
                LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                WHERE ks.active = '1' AND kp.active = '1' AND kt.knowledge_type_id = $id
                GROUP BY kt.code, kt.id, kt.title, kt.author, kt.publisher_name, kt.origination, kt.created_at, kt.updated_at
                ORDER BY title, author ASC
            ",
            'eksemplar' => "
                SELECT cc.code klasifikasi, kk.code barcode, ks.name subjek, kk.status, kt.*
                FROM knowledge_item kt
                LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
                LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                LEFT JOIN classification_code cc ON kt.classification_code_id = cc.id
                LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                WHERE ks.active = '1' AND kp.active = '1' AND kt.knowledge_type_id = $id
            ",
            default => "
                SELECT cc.code klasifikasi, kk.code barcode, ks.name subjek, kk.status, kt.*
                FROM knowledge_item kt
                LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
                LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                LEFT JOIN classification_code cc ON kt.classification_code_id = cc.id
                LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                WHERE ks.active = '1' AND kp.active = '1' AND kt.knowledge_type_id = $id AND kk.status = '$type'
            ",
        };

        return DB::select($query);
    }

    public function getCatalogList($where, $where2)
    {
        $query = "
            SELECT * FROM (
                SELECT 
                    kt.code catalog,
                    COUNT(ks.code) eksemplar,
                    cc.code klasifikasi,
                    kt.title,
                    tmp.nama_prodi,
                    kt.author,
                    kt.publisher_name,
                    kl.name tipe,
                    kt.published_year,
                    SUM(ks.price) harga,
                    il.name lokasi,
                    REPLACE(LTRIM(REPLACE(cc.code, '0', ' ')), ' ', '0') codes2
                FROM knowledge_stock ks
                LEFT JOIN knowledge_item kt ON kt.id = ks.knowledge_item_id
                LEFT JOIN classification_code cc ON cc.id = kt.classification_code_id
                LEFT JOIN knowledge_type kl ON kl.id = kt.knowledge_type_id
                LEFT JOIN item_location il ON il.id = kt.item_location_id
                LEFT JOIN t_mst_prodi tmp ON tmp.c_kode_prodi = kt.course_code
                WHERE 1=1 $where
                GROUP BY 
                    kt.id, 
                    ks.course_code,
                    kt.code,
                    cc.code,
                    kt.title,
                    tmp.nama_prodi,
                    kt.author,
                    kt.publisher_name,
                    kl.name,
                    kt.published_year,
                    il.name,
                    codes2
            ) b $where2
        ";
        return DB::select($query);
    }

    public function getCollectionList($where, $where2)
    {
        $query = "
            SELECT 
                il.name AS location_name, 
                ks.id AS id, 
                kt.code AS catalog, 
                ks.status, 
                ks.code AS barcode, 
                cc.code AS klasifikasi, 
                kt.title, 
                ks.origination,
                kt.author, 
                kt.publisher_name, 
                kl.name AS tipe, 
                kt.published_year, 
                REPLACE(LTRIM(REPLACE(cc.code, '0', ' ')), ' ', '0') AS codes2 
            FROM 
                knowledge_item kt
            LEFT JOIN 
                knowledge_stock ks ON kt.id = ks.knowledge_item_id
            LEFT JOIN 
                classification_code cc ON cc.id = kt.classification_code_id
            LEFT JOIN 
                item_location il ON il.id = ks.item_location_id
            LEFT JOIN 
                knowledge_type kl ON kl.id = kt.knowledge_type_id 
            WHERE 
                1=1 
                $where 
            ORDER BY 
                kt.published_year DESC, 
                ks.status, 
                kl.id, 
                kt.id ASC
        ";

        $query = "SELECT * FROM ($query) a $where2";

        return DB::select($query);
    }

    public function getType()
    {
        return DB::select("SELECT * FROM knowledge_type WHERE active='1' AND id IN (1, 2, 3, 10, 21, 33, 40, 41, 59, 65, 12, 77, 78, 45, 46, 44, 42) ORDER BY name");
    }

    
    public function getBooksOnProcess($where = "", $where2 = "")
    {
        $query = "select * from (SELECT il.name location_name, kl.name tipe, ks.id id, kt.CODE catalog, ks.CODE barcode, cc.CODE klasifikasi, title, ks.origination,
                    author, publisher_name, replace(ltrim(replace(cc.code,'0',' ')),' ','0') codes2 FROM knowledge_item kt
                    LEFT JOIN knowledge_stock ks ON kt.id = knowledge_item_id
                    LEFT JOIN knowledge_subject kss ON kss.id = kt.knowledge_subject_id
                    LEFT JOIN classification_code cc ON cc.id = kt.classification_code_id
                    LEFT JOIN item_location il ON il.id = ks.item_location_id
                    LEFT JOIN knowledge_type kl ON kl.id = ks.knowledge_type_id 
                    where 1=1 AND kss.active = '1' $where 
                    ORDER BY status, kl.id, kt.id) a $where2
                    ";
        
        return DB::select($query);
    }

    public function updateStatus($id, $item)
    {
        return DB::table('knowledge_stock')
            ->where('id', $id)
            ->update($item);
    }
}

?>