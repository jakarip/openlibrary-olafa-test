<?php

namespace App\Models\Member;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberClass extends Model
{
    use HasFactory;

    protected $table = 'member_class';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'homepage',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function members()
    {
        return $this->hasMany(Member::class, 'member_class_id', 'id');
    }
}
