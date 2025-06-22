<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AnggotaModel extends Model
{

    public function getallProdi()
    { 
        return DB::select("select c_kode_prodi, nama_prodi, nama_fakultas from t_mst_prodi left join t_mst_fakultas using(c_kode_fakultas) order by nama_fakultas, nama_prodi");
    } 

    public function getJumlahPenunjungByTanggal($kode, $tanggal, $time)
    {
        // dd($tanggal);
        if ($time == 'day') {
            return DB::table('member_attendance as ma')
                ->join('member as m', 'm.id', '=', 'ma.member_id')
                ->where('m.master_data_course', $kode)
                ->whereIn('m.member_type_id', [5, 6, 9, 10])
                ->whereBetween('ma.attended_at', [$tanggal[0], $tanggal[1]])
                ->whereTime('ma.attended_at', '>=', '06:00:00')
                ->whereTime('ma.attended_at', '<=', '16:30:00')
                ->count();
        } else {
            return DB::table('member_attendance as ma')
                ->join('member as m', 'm.id', '=', 'ma.member_id')
                ->where('m.master_data_course', $kode)
                ->whereIn('m.member_type_id', [5, 6, 9, 10])
                ->whereBetween('ma.attended_at', [$tanggal[0], $tanggal[1]])
                ->whereTime('ma.attended_at', '>=', '16:30:01')
                ->whereTime('ma.attended_at', '<=', '22:00:00')
                ->count();
        }
    }

    public function getJumlahPengunjungDosenPegawaiByTanggal($tanggal, $time)
{
    if ($time == 'day') {
        return DB::table('member_attendance as ma')
            ->join('member as m', 'm.id', '=', 'ma.member_id')
            ->whereIn('m.member_type_id', [1, 4, 7])
            ->whereBetween('ma.attended_at', [$tanggal[0], $tanggal[1]])
            ->whereTime('ma.attended_at', '>=', '06:00:00')
            ->whereTime('ma.attended_at', '<=', '16:30:00')
            ->count();
    } else {
        return DB::table('member_attendance as ma')
            ->join('member as m', 'm.id', '=', 'ma.member_id')
            ->whereIn('m.member_type_id', [1, 4, 7])
            ->whereBetween('ma.attended_at', [$tanggal[0], $tanggal[1]])
            ->whereTime('ma.attended_at', '>=', '16:30:01')
            ->whereTime('ma.attended_at', '<=', '22:00:00')
            ->count();
    }
}

public function getJumlahPengunjungPublicByTanggal($tanggal, $time)
{
    if ($time == 'day') {
        return DB::table('member_attendance as ma')
            ->join('member as m', 'm.id', '=', 'ma.member_id')
            ->where('m.member_type_id', 19)
            ->whereBetween('ma.attended_at', [$tanggal[0], $tanggal[1]])
            ->whereTime('ma.attended_at', '>=', '06:00:00')
            ->whereTime('ma.attended_at', '<=', '16:30:00')
            ->count();
    } else {
        return DB::table('member_attendance as ma')
            ->join('member as m', 'm.id', '=', 'ma.member_id')
            ->where('m.member_type_id', 19)
            ->whereBetween('ma.attended_at', [$tanggal[0], $tanggal[1]])
            ->whereTime('ma.attended_at', '>=', '16:30:01')
            ->whereTime('ma.attended_at', '<=', '22:00:00')
            ->count();
    }
}
}
