<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Member\MemberType;
class MemberModel extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'member';
    protected $primaryKey = 'id';

    // Define the relationship with MemberAttendanceModel
    public function attendances()
    {
        return $this->hasMany(MemberAttendanceModel::class, 'member_id', 'id');
    }
    public function memberType()
    {
        return $this->belongsTo(MemberType::class, 'member_type_id', 'id');
    }
}
