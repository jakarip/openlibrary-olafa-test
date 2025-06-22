<?php

namespace App\Models\Holiday;

use Illuminate\Database\Eloquent\Model;

class HolidayModel extends Model
{
    protected $table = 'holiday';

    public $timestamps = false;

    protected $fillable = [
        'holiday_rule_id',
        'holiday_date',
        'location_id',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];

    public function rule()
    {
        return $this->belongsTo(HolidayRuleModel::class, 'holiday_rule_id');
    }
}
