<?php

namespace App\Models\Room;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RoomModel extends Model {
    protected $connection = 'mysql';
    protected $table = 'room.room';
    protected $primaryKey = 'room_id';

    public $timestamps = false;

    protected $fillable = ['room_name', 'room_min', 'room_max', 'room_capacity', 'room_description', 'room_id_location', 'room_price', 'room_hour'];

    public function getRoomsWithGallery($locationId = null)
    {
        $query = DB::connection('mysql')->table('room.room')
            ->leftJoin('room.room_gallery', 'room.room.room_id', '=', 'room.room_gallery.rg_room_id')
            ->select(
                'room.room_id',
                'room.room_name',
                'room.room_min',
                'room.room_max',
                'room.room_capacity',
                'room.room_description',
                'room.room_active',
                'room.room_id_location',
                'room.room_price',
                'room.room_hour'
            );

        if ($locationId) {
            $query->where('room.room_id_location', $locationId);
        }

        $query->orderBy('room.room_active', 'asc');

        return $query->get();
    }

}
