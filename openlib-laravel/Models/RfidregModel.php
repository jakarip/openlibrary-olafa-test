<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class RfidregModel extends Model
{
    use HasFactory;

    public function member($username)
    {
        return DB::table('member')
            ->where('master_data_user', 'like', "%$username%")
            ->orWhere('master_data_fullname', 'like', "%$username%")
            ->get();
    }

    public function getRfid($rfid, $username = "")
    {
        if ($username != "") {
            return DB::table('member')
                ->where('master_data_user', $username)
                ->get();
        } else {
            return DB::table('member')
                ->where('rfid1', $rfid)
                ->orWhere('rfid2', $rfid)
                ->get();
        }
    }

    public function getMember($username)
    {
        return DB::table('member')
            ->select('*', 'id as memberid')
            ->where('master_data_user', $username)
            ->get();
    }

    public function checkInsert($item)
    {
        return DB::table('rfid_not_same_with_igracias')
            ->where('username', $item['username'])
            ->orWhere('rfid', $item['rfid'])
            ->get();
    }

    public function getUser($option, $username)
    {
        if ($option == 'mahasiswa') {
            return DB::table('t_mst_mahasiswa')
                ->where('c_npm', $username)
                ->select('c_kode_prodi')
                ->get();
        } else {
            return DB::table('t_mst_pegawai')
                ->where('c_nip', $username)
                ->select('c_kode_status_pegawai')
                ->get();
        }
    }

    public function getMemberTypeApi($api_base_type, $api_key_value)
    {
        return DB::table('member_type_api')
            ->where('api_base_type', $api_base_type)
            ->where('api_key_value', $api_key_value)
            ->get();
    }

    public function getById($id)
    {
        return DB::table('rfid_not_same_with_igracias')
            ->where('id', $id)
            ->first();
    }

    public function addRfidNotSameWithIgracias($item)
    {
        return DB::table('rfid_not_same_with_igracias')->insert($item);
    }

    public function addMember($item)
    {
        DB::table('member')->insert($item);
        return DB::getPdo()->lastInsertId();
    }
}
