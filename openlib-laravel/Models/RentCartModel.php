<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentCartModel extends Model
{
    protected $table = 'rent_cart';

    protected $fillable = [
        'member_id',
        'rental_code',
        'created_by',
        'updated_by',
    ];

    public function member()
    {
        return $this->belongsTo(MemberModel::class, 'member_id');
    }

    public function rents()
    {
        return $this->hasMany(RentModel::class, 'rent_cart_id');
    }
}

