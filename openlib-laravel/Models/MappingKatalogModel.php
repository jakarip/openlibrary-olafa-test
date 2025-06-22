<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class MappingKatalogModel extends Model
{
    use HasFactory;

    public function dtquery($param)
    {
        return DB::select("
            SELECT *, (
                SELECT COUNT(ms.id) 
                FROM knowledge_item_subject kis
                JOIN master_subject ms ON ms.id = kis.master_subject_id 
                WHERE kis.knowledge_item_id = a.id 
                AND ms.curriculum_code = '2020'
            ) AS total  
            FROM (
                SELECT 
                    kt.id, 
                    cc.name AS klasifikasi,
                    ks.name AS subjek,
                    kt.title,
                    kt.published_year,
                    kt.code AS codes,
                    author,
                    kp.name AS tipe,
                    '0' AS total
                FROM 
                    knowledge_item kt
                LEFT JOIN 
                    knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                LEFT JOIN 
                    classification_code cc ON kt.classification_code_id = cc.id
                LEFT JOIN 
                    knowledge_type kp ON kt.knowledge_type_id = kp.id
                WHERE 
                    ks.active = '1' 
                    AND kp.active = '1'
                    {$param['where']} 
                    ) a
                    ");
                }
                // {$param['limit']} 
                

    public function get_type()
    {
        return DB::select("SELECT * FROM knowledge_type WHERE active = '1' AND id IN (1, 2, 3, 10, 21, 33, 40, 41, 59, 65) ORDER BY name");
    }
    
    public function getcurriculumyear()
{
    return DB::select("SELECT curriculum_code, MAX(id) as id FROM master_subject GROUP BY curriculum_code ORDER BY curriculum_code DESC");
}

    public function getstudyprogram()
    {
        return DB::select("SELECT * FROM t_mst_prodi tmp LEFT JOIN t_mst_fakultas tmf ON tmp.C_KODE_FAKULTAS = tmf.C_KODE_FAKULTAS ORDER BY NAMA_FAKULTAS, NAMA_PRODI");
    }

    public function getbyid($id)
    {
        return DB::table('knowledge_item')->where('id', $id)->first();
    }

    public function add($item)
    {
        DB::table('knowledge_item_subject')->insert($item);
    }

    public function deleteItem($master_subject_id, $knowledge_item_id)
    {
        DB::table('knowledge_item_subject')
            ->where('master_subject_id', $master_subject_id)
            ->where('knowledge_item_id', $knowledge_item_id)
            ->delete();
    }

    public function checkExisting($master_subject_id, $knowledge_item_id)
    {
        return DB::table('knowledge_item_subject')
            ->where('master_subject_id', $master_subject_id)
            ->where('knowledge_item_id', $knowledge_item_id)
            ->get();
    }
}


