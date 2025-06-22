<?php

namespace App\Models\Room;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BookingModel extends Model
{
    protected $connection = 'mysql';
    protected $table = 'room.booking';
    protected $primaryKey = 'bk_id';
    public $timestamps = false;

    protected $fillable = [
        'bk_memberid',
        'bk_username',
        'bk_mobile_phone',
        'bk_room_id',
        'bk_status',
        'bk_purpose',
        'bk_startdate',
        'bk_enddate',
        'bk_createdby',
        'bk_createdate',
        'bk_name',
        'bk_total',
        'bk_payment',
    ];

    public function getRoom($locationId = null)
    {
        $query = DB::connection('mysql')->table('room.booking')
            ->leftJoin('room.room', 'room.booking.bk_room_id', '=', 'room.room.room_id')
            ->leftJoin('batik.member', 'room.booking.bk_memberid', '=', 'batik.member.id')
            ->select(
                'room.booking.*',
                'room.room.room_id',
                'room.room.room_name',
                'batik.member.master_data_fullname'
            );

        if ($locationId) {
            $query->where('room.room.room_id_location', $locationId);
        }

        return $query->get();
    }

    public function getSyaratKetentuan()
    {
        $query = DB::connection('mysql')->table('room.term_conditions')
            ->select('id', 'information')
            ->orderBy('term_sequence');

        return $query->get();
    }

    // public function searchMembers($search)
    // {
    //     return DB::connection('mysql')
    //         ->table('member')
    //         ->select('master_data_user', 'master_data_fullname', 'master_data_number')
    //         ->where(function($query) use ($search) {
    //             $query->where('master_data_user', 'LIKE', "%{$search}%")
    //                 ->orWhere('master_data_fullname', 'LIKE', "%{$search}%")
    //                 ->orWhere('master_data_number', 'LIKE', "%{$search}%");
    //         })
    //         ->orderBy('master_data_fullname')
    //         ->limit(20)
    //         ->get();
    // }

    public function searchMembers($search)
    {
        return DB::connection('mysql')
            ->table('member')
            ->select('master_data_user', 'master_data_fullname', 'master_data_number')
            ->where(function($query) use ($search) {
                $query->where('master_data_user', 'LIKE', "{$search}%")
                    ->orWhere('master_data_fullname', 'LIKE', "{$search}%")
                    ->orWhere('master_data_number', 'LIKE', "{$search}%");
            })
            ->orderBy('master_data_fullname')
            ->limit(20)
            ->get();
    }
}
