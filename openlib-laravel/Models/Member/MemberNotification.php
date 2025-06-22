<?php

namespace App\Models\Member;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberNotification extends Model
{
    use HasFactory;

    protected $table = 'member_notification';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'member_id',
        'title',
        'content',
        'sent',
        'error_message',
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
