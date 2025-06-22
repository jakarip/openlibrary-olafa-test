<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KatalogModel extends Model
{

    public function getLocation()
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

    public function getKnowledgeType()
    {
        return DB::table('knowledge_type')
            ->where('active', '1')
            ->orderBy('name')
            ->get();
    }

    public function getType()
    {
        return DB::select("SELECT * FROM knowledge_type WHERE active='1' AND id IN (1, 2, 3, 10, 21, 33, 40, 41, 59, 65, 12, 77, 78, 45, 46, 44, 42) ORDER BY name");
    }

    public function getbookonprocess($where = "", $where2 = "")
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

    public function ubahStatus($id, $item)
    {
        return DB::table('knowledge_stock')
            ->where('id', $id)
            ->update($item);
    }
}
