<?php

namespace App\Models\Room;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RoomHourModel extends Model {
    protected $connection = 'mysql';
    protected $table = 'room.room_hour';
    protected $primaryKey = 'rh_id';

    public $timestamps = false;

    protected $fillable = ['rh_id_location', 'rh_name', 'rh_starthour', 'rh_endhour'];

    public function getRoomsHour()
    {
        $query = DB::connection('mysql')->table('room.room_hour')
            ->leftJoin('item_location', 'room_hour.rh_id_location', '=', 'item_location.id')
            ->select(
                'room_hour.rh_id',
                'room_hour.rh_id_location',
                'item_location.name as location_name',
                'room_hour.rh_name',
                'room_hour.rh_starthour',
                'room_hour.rh_endhour',
                'room_hour.rh_status'
            );

        $query->orderBy('room_hour.rh_status', 'desc');

        return $query->get();
    }

}
