<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table = 'holiday';

    protected $fillable = [
        'holiday_date', 'location_id'
    ];

    public $timestamps = false;

    public function location()
    {
        return $this->belongsTo(ItemLocation::class, 'location_id');
    }
}
