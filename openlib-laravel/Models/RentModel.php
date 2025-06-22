<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentModel extends Authenticatable
{
    protected $table = 'rent';

    protected $fillable = [
        'rent_cart_id',
        'member_id',
        'knowledge_stock_id',
        'rent_date',
        'return_date_expected',
        'return_date',
        'status',
        'rent_period',
        'rent_period_unit',
        'rent_period_day',
        'rent_cost_per_day',
        'rent_cost_total',
        'penalty_per_day',
    ];

    public function member()
    {
        return $this->belongsTo(MemberModel::class, 'member_id');
    }

    public function rentCart()
    {
        return $this->belongsTo(RentCartModel::class, 'rent_cart_id');
    }

    public function knowledgeStock()
    {
        return $this->belongsTo(KnowledgeStockModel::class, 'knowledge_stock_id');
    }

    public function penalties()
    {
        return $this->hasMany(RentPenaltyModel::class, 'rent_id');
    }
}

