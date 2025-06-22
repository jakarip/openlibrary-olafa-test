<?php

namespace App\Models\Room;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HistoryBookingModel extends Model
{
    protected $connection = 'mysql';
    protected $table = 'room.booking';
    protected $primaryKey = 'bk_id';
    public $timestamps = false;

    public function getHistoryBookings($locationId = null, $memberClassId = null, $memberId = null, $viewAllStatus = false)
    {
        $query = DB::connection('mysql')->table('room.booking')
            ->leftJoin('batik.member', 'room.booking.bk_memberid', '=', 'batik.member.id')
            ->leftJoin('room.room', 'room.booking.bk_room_id', '=', 'room.room.room_id')
            ->select(
                'room.booking.bk_id',
                'batik.member.master_data_fullname as bk_member_name',
                'room.booking.bk_mobile_phone',
                'room.room.room_name as bk_room_name',
                DB::raw("DATE_FORMAT(room.booking.bk_startdate, '%d-%m-%Y') as bk_startdate"),
                DB::raw("DATE_FORMAT(room.booking.bk_startdate, '%H:%i') as jam_mulai"),
                DB::raw("DATE_FORMAT(room.booking.bk_enddate, '%H:%i') as jam_selesai"),
                'room.booking.bk_purpose',
                'room.booking.bk_total',
                'room.booking.bk_status',
                'room.booking.bk_name',
                'room.booking.bk_reason',
                'room.room.room_id_location'
        );
        // ->whereIn('room.booking.bk_status', ['request', 'approved']);

        if ($viewAllStatus) {
            $query->whereIn('room.booking.bk_status', ['request', 'approved']);
        }

        if ($locationId) {
            $query->where('room.room.room_id_location', $locationId);
        }

        if (in_array($memberClassId, [2, 9]) && $memberId) {
            $query->where('room.booking.bk_memberid', $memberId);
        }

        $query->orderBy('room.booking.bk_createdate', 'desc');

        return $query->get();
    }
}
