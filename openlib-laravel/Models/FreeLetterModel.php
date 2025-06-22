<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FreeLetterModel extends Model
{
    protected $table = 'free_letter';
    protected $primaryKey = 'id';

    protected $attributes = [
        'updated_by' => '',
        'updated_at' => '0000-00-00 00:00:00',
        'address' => '',
    ];

    public function getProdiFak($username)
    {
        return DB::table('member as m')
            ->select(
                'm.*',
                'SINGKATAN',
                DB::raw('(select sum(amount) from rent_penalty rp where m.id = rp.member_id and m.master_data_user = ?) as penalty'),
                DB::raw('(select sum(amount) from rent_penalty_payment rp where m.id = rp.member_id and m.master_data_user = ?) as penalty_payment'),
                DB::raw('(select count(*) from amnesty_denda where username_id = m.id) as amnesty'),
                DB::raw('(select sum(amount) from rent_penalty rp where m.id = rp.member_id and m.master_data_user = ?) - (select sum(amount) from rent_penalty_payment rp where m.id = rp.member_id and m.master_data_user = ?) as sisa')
            )
            ->leftJoin('t_mst_prodi as tmp', 'tmp.c_kode_prodi', '=', 'm.master_data_course')
            ->leftJoin('t_mst_fakultas as tmf', 'tmf.c_kode_fakultas', '=', 'tmp.c_kode_fakultas')
            ->where('m.master_data_user', $username)
            ->setBindings([$username, $username, $username, $username, $username])
            ->get();
    }
    
}
