<?php

namespace App\Models\Member;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MemberType extends Model
{
    use HasFactory;

    protected $table = 'member_type';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'membership_period',
        'membership_period_unit',
        'rent_period',
        'rent_period_unit',
        'rent_quantity'
    ];

    public function members()
    {
        return $this->hasMany(Member::class, 'member_type_id', 'id');
    }
}
