<?php

namespace App\Models\Holiday;

use Illuminate\Database\Eloquent\Model;

class HolidayRuleModel extends Model
{
    protected $table = 'holiday_rule';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'weekday',
        'day',
        'month',
        'year_from',
        'year_to',
        'location_id',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];

    public function holidays()
    {
        return $this->hasMany(HolidayModel::class, 'holiday_rule_id');
    }
}
