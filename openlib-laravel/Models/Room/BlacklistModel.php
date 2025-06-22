<?php

namespace App\Models\Room;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BlacklistModel extends Model
{
    protected $connection = 'mysql';
    protected $table = 'room.blacklist';

    public function showBlacklistDetails()
    {
        return DB::connection('mysql')->table('room.blacklist')
            ->join('batik.member', 'room.blacklist.bl_username', '=', 'batik.member.master_data_user')
            ->select(
                'blacklist.bl_username',
                'member.master_data_fullname',
                'blacklist.bl_reason',
                'blacklist.bl_date',
                'member.id'
            )
            ->get();
    }

    public function searchMembers($search)
    {
        return DB::connection('mysql')
            ->table('batik.member')
            ->select('master_data_user', 'master_data_fullname', 'master_data_number')
            ->where('master_data_user', 'LIKE', "%{$search}%")
            ->orWhere('master_data_fullname', 'LIKE', "%{$search}%")
            ->orWhere('master_data_number', 'LIKE', "%{$search}%")
            ->get();
    }
}
