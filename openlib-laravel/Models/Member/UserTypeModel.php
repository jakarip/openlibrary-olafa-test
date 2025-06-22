<?php

namespace App\Models\Member;

use App\Models\MemberModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTypeModel extends Model
{
    protected $table = 'member_class'; 
    protected $primaryKey = 'id';

    public function members()
    {
        return $this->hasMany(MemberModel::class, 'member_class_id', 'id'); // Adjust the foreign key and local key as needed
    }

    public static function getUserTypeWithItemCount()
    {
        return self::select('id', 'name', 'updated_by', 'updated_at')
            ->withCount('members as total_items')
            ->get();
    }

    

}
