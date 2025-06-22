<?php

namespace App\Models\Room;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RoomGalleryModel extends Model {
    protected $connection = 'mysql';
    protected $table = 'room.room_gallery';
    protected $primaryKey = 'rg_id';
    public $timestamps = false;

    protected $fillable = [
        'rg_id', 'rg_room_id', 'rg_image'
    ];

    public function getGalleryData()
    {
        return DB::connection('mysql')->table('room.room_gallery')
            ->leftJoin('room.room', 'room.room_gallery.rg_room_id', '=', 'room.room.room_id')
            ->select(
                'room_gallery.rg_id',
                'room.room_name',
                'room_gallery.rg_image'
            )
            ->get();
    }

    public function getGalleryByRoomId($roomId)
    {
        return DB::connection('mysql')->table('room.room_gallery')
            ->where('rg_room_id', $roomId)
            ->select('rg_image')
            ->first();
    }
}
