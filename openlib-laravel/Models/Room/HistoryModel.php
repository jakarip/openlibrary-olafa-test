<?php

namespace App\Models\Room;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HistoryModel extends Model
{
    protected $connection = 'mysql';
    protected $table = 'room.booking';
    protected $primaryKey = 'bk_id';
    public $timestamps = false;

    public function getHistory($roomId = null, $status = null)
    {
        $query =  DB::connection('mysql')->table('room.booking')
            ->leftJoin('batik.member', 'room.booking.bk_memberid', '=', 'batik.member.id')
            ->leftJoin('room.room', 'room.booking.bk_room_id', '=', 'room.room.room_id')
            ->select(
                'booking.bk_id',
                'member.master_data_fullname as bk_member_name',
                'booking.bk_mobile_phone',
                'room.room_name as bk_room_name',
                DB::raw("DATE_FORMAT(booking.bk_startdate, '%d-%m-%Y') as bk_startdate"),
                DB::raw("DATE_FORMAT(booking.bk_startdate, '%H:%i') as jam_mulai"),
                DB::raw("DATE_FORMAT(booking.bk_enddate, '%H:%i') as jam_selesai"),
                'booking.bk_purpose',
                'booking.bk_total',
                'booking.bk_status',
                'booking.bk_payment',
                'booking.bk_reason'
            )
            ->whereIn('booking.bk_status', ['attend']);

            if ($roomId) {
                $query->where('booking.bk_room_id', $roomId);
            }

            if ($status) {
                $query->where('booking.bk_status', $status);
            }

            $query->orderBy('room.booking.bk_startdate', 'desc');

            return $query->get();
    }
}
