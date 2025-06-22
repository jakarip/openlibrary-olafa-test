<?php

namespace App\Models\Member;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberPresence extends Model
{
    use HasFactory;

    protected $table = 'member_attendance';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'member_id',
        'master_data_course',
        'item_location_id',
        'attended_at',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
