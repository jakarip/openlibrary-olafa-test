<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\ItemLocationHour;
use App\Models\Holiday;

class ItemLocation extends Model
{
    protected $table = 'item_location';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'phone',
        'fax',
        'email',
        'address',
        'show_as_footer',
        'orderby',
        'open_sun',
        'open_mon',
        'open_tue',
        'open_wed',
        'open_thu',
        'open_fri',
        'open_sat',
        'created_by',
        'updated_by'
    ];

    public function specialHours()
    {
        return $this->hasMany(ItemLocationHour::class, 'ilh_item_location');
    }

    public function holidays()
    {
        return $this->hasMany(Holiday::class, 'location_id');
    }

    // Scope untuk data yang aktif (show_as_footer = 1)
    public function scopeActive($query)
    {
        return $query->where('show_as_footer', 1);
    }

    // Scope untuk order by orderby column
    public function scopeOrdered($query)
    {
        return $query->orderBy('orderby', 'asc');
    }
}