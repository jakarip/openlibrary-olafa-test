<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentPenaltyModel extends Model
{
    protected $table = 'rent_penalty';

    protected $fillable = [
        'member_id',
        'rent_id',
        'penalty_date',
        'amount',
    ];

    public function rent()
    {
        return $this->belongsTo(RentModel::class, 'rent_id');
    }

    public function member()
    {
        return $this->belongsTo(MemberModel::class, 'member_id');
    }
}
