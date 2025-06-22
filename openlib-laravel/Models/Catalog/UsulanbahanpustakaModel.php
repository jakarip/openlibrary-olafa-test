<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UsulanbahanpustakaModel extends Model
{
    protected $connection = 'mysql';
    protected $table = 'batik.usulan_bahanpustaka';
    protected $primaryKey = 'bp_id';
    public $timestamps = false;

    public function getUsulanBahanPustaka($status = null)
    {
        $query = DB::connection('mysql')->table('batik.usulan_bahanpustaka')
            ->leftJoin('batik.member', 'usulan_bahanpustaka.bp_idmember', '=', 'member.id')
            ->leftJoin('batik.t_mst_fakultas', 'usulan_bahanpustaka.bp_faculty_id', '=', 't_mst_fakultas.c_kode_fakultas')
            ->leftJoin('batik.t_mst_prodi', 'usulan_bahanpustaka.bp_prodi_id', '=', 't_mst_prodi.c_kode_prodi')
            ->leftJoin('batik.usulan_bahanpustaka_status', 'usulan_bahanpustaka.bp_id', '=', 'usulan_bahanpustaka_status.bps_idbp')
            ->select(
                'usulan_bahanpustaka.bp_id',
                'usulan_bahanpustaka.bp_createdate',
                'usulan_bahanpustaka.bp_faculty_id',
                'usulan_bahanpustaka.bp_prodi_id',
                't_mst_fakultas.C_KODE_FAKULTAS',
                't_mst_prodi.C_KODE_PRODI',
                't_mst_fakultas.nama_fakultas',
                't_mst_prodi.nama_prodi',
                'member.master_data_number',
                'member.master_data_fullname',
                'usulan_bahanpustaka.bp_title',
                'usulan_bahanpustaka.bp_author',
                'usulan_bahanpustaka.bp_publisher',
                'usulan_bahanpustaka.bp_publishedyear',
                'usulan_bahanpustaka.bp_matakuliah',
                'usulan_bahanpustaka.bp_semester',
                'usulan_bahanpustaka.bp_reference',
                'usulan_bahanpustaka.bp_item_code',
                'usulan_bahanpustaka.bp_status',
                DB::raw('GROUP_CONCAT(usulan_bahanpustaka_status.bps_status) AS concatenated_bps_status'),
                DB::raw('GROUP_CONCAT(usulan_bahanpustaka_status.bps_date) AS concatenated_bps_date'),
                'usulan_bahanpustaka.bp_reason'
            )
            ->groupBy(
                'usulan_bahanpustaka.bp_id',
                'usulan_bahanpustaka.bp_createdate',
                'usulan_bahanpustaka.bp_faculty_id',
                'usulan_bahanpustaka.bp_prodi_id',
                't_mst_fakultas.C_KODE_FAKULTAS',
                't_mst_prodi.C_KODE_PRODI',
                't_mst_fakultas.nama_fakultas',
                't_mst_prodi.nama_prodi',
                'member.master_data_number',
                'member.master_data_fullname',
                'usulan_bahanpustaka.bp_title',
                'usulan_bahanpustaka.bp_author',
                'usulan_bahanpustaka.bp_publisher',
                'usulan_bahanpustaka.bp_publishedyear',
                'usulan_bahanpustaka.bp_matakuliah',
                'usulan_bahanpustaka.bp_semester',
                'usulan_bahanpustaka.bp_reference',
                'usulan_bahanpustaka.bp_item_code',
                'usulan_bahanpustaka.bp_status',
                'usulan_bahanpustaka.bp_reason'
            );

        if ($status) {
            $query->where('usulan_bahanpustaka.bp_status', $status);
        }

        return $query->get();
    }
}
