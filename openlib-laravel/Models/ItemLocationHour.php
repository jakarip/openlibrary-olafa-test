<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ItemLocationHour extends Model
{
    protected $table = 'item_location_hour';

    protected $fillable = [
        'ilh_item_location', 'ilh_date', 'ilh_hour'
    ];

    public $timestamps = false;

    public function location()
    {
        return $this->belongsTo(ItemLocation::class, 'ilh_item_location');
    }
}
