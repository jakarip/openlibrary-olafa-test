<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KaryaIlmiahModel extends Model
{
    use HasFactory;

    protected $table = 'knowledge_item';

    protected $fillable = [
        'knowledge_subject_id',
        'knowledge_type_id',
        'course_code',
    ];

    public static function getProdiWithKnowledgeCount()
    {
        return DB::table('t_mst_prodi as tp')
            ->select('tp.c_kode_prodi', 'tp.nama_prodi', 'tf.nama_fakultas', DB::raw('(
                SELECT COUNT(*) AS total
                FROM knowledge_item kt
                LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                WHERE ks.active = "1" 
                AND kp.active = "1" 
                AND kt.knowledge_type_id IN (4, 5, 6, 79) 
                AND kt.course_code = tp.c_kode_prodi
            ) AS jml_ta'))
            ->leftJoin('t_mst_fakultas as tf', 'tp.c_kode_fakultas', '=', 'tf.c_kode_fakultas')
            ->where('tp.nama_prodi', 'NOT LIKE', '%Pindahan%')
            ->where('tp.nama_prodi', 'NOT LIKE', '%International%')
            ->where('tp.nama_prodi', 'NOT LIKE', '%Internasional%')
            ->where('tf.nama_fakultas', '!=', '')
            ->orderBy('tf.nama_fakultas')
            ->orderBy('tp.nama_prodi')
            ->get();
    }

    public static function getKnowledgeItemsByProdi($kode_prodi)
    {
        return DB::table('knowledge_item as kt')
            ->select('kt.author', 'kt.title', 'kt.published_year')
            ->leftJoin('knowledge_subject as ks', 'kt.knowledge_subject_id', '=', 'ks.id')
            ->leftJoin('knowledge_type as kp', 'kt.knowledge_type_id', '=', 'kp.id')
            ->where('ks.active', '1')
            ->where('kp.active', '1')
            ->whereIn('kt.knowledge_type_id', [4, 5, 6, 79])
            ->where('kt.course_code', $kode_prodi)
            ->groupBy('kt.author', 'kt.title', 'kt.published_year', 'kt.id')
            ->orderBy('kt.published_year', 'desc')
            ->get();
    }

    public static function getNamaProdiByKodeProdi($kode_prodi)
    {
        return DB::table('t_mst_prodi')
            ->where('c_kode_prodi', $kode_prodi)
            ->value('nama_prodi');
    }
    
}
