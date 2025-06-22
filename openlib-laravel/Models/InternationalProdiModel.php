<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InternationalProdiModel extends Model
{
    protected $table = 'internationalonlineperprodi'; 
    public $timestamps = false; // Disable timestamps

    protected $fillable = [
        'prodi_code',
        'io_id'
    ];

    public static function getProdiWithJournals()
    {
        return DB::table('t_mst_prodi as tp')
            ->select(
                'tp.c_kode_prodi', 
                'tp.nama_prodi', 
                'tf.nama_fakultas',
                DB::raw("GROUP_CONCAT(CONCAT(io.io_name, ' - ', io.io_url) ORDER BY io.io_name ASC SEPARATOR '<br><br>') AS journal")
            )
            ->leftJoin('internationalonlineperprodi as iop', 'tp.c_kode_prodi', '=', 'iop.prodi_code')
            ->leftJoin('internationalonline as io', 'io.io_id', '=', 'iop.io_id')
            ->leftJoin('t_mst_fakultas as tf', 'tf.c_kode_fakultas', '=', 'tp.c_kode_fakultas')
            ->where('tp.nama_prodi', 'NOT LIKE', '%Pindahan%')
            ->where('tp.nama_prodi', 'NOT LIKE', '%International%')
            ->where('tp.nama_prodi', 'NOT LIKE', '%Internasional%')
            ->where('tf.nama_fakultas', '!=', '')
            ->groupBy('tp.c_kode_prodi', 'tp.nama_prodi', 'tf.nama_fakultas')
            ->orderBy('tf.nama_fakultas')
            ->orderBy('tp.nama_prodi')
            ->get();
    }
}
