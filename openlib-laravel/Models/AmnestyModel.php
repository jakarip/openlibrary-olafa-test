<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AmnestyModel extends Model
{
    protected $table = 'amnesty_denda';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function getAmnestyData()
    {
        return DB::table('amnesty_denda as ad')
            ->join('member as m', 'm.id', '=', 'ad.username_id')
            ->select('ad.*', 'm.master_data_user', 'm.master_data_fullname')
            ->orderBy('m.master_data_fullname', 'asc')
            ->get();
    }

    public function member($username)
    {
        return DB::table('member')
            ->join('t_mst_prodi', 'master_data_course', '=', 'c_kode_prodi')
            ->where('master_data_user')
            ->orWhere('master_data_fullname', 'like', "%{$username}%")
            ->orderBy('master_data_fullname')
            ->select('member.*', 't_mst_prodi.NAMA_PRODI')
            ->get();
    }

    public function checkInsert($username_id)
    {
        return DB::table('amnesty_denda')
            ->where('username_id', $username_id)
            ->exists();
    }
}
