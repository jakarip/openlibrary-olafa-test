<?php

namespace App\Models\Member;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Member\MemberType;
use App\Models\Member\MemberClass;
use App\Models\Member\MemberNotification;
class Member extends Model
{
    use HasFactory;

    protected $table = 'member';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'member_type_id',
        'member_class_id',
        'master_data_user',
        'master_data_password',
        'master_data_email',
        'master_data_course',
        'master_data_fullname',
        'master_data_number',
        'master_data_mobile_phone',
        'master_data_lecturer_status',
        'master_data_nidn',
        'master_data_generation',
        'master_data_photo',
        'master_data_status',
        'rfid1',
        'rfid2',
        'status',
        'block_until',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'master_data_ijasah',
        'master_data_institution',
        'master_data_ktp',
        'master_data_address',
        'master_data_type',
        'master_data_idcard',
        'master_data_uuid',
        'master_data_token',
        'deleted_at',
        'remember_token',
        'reset_token',
        'reset_token_expire',
    ];


    public function memberType()
    {
        return $this->belongsTo(MemberType::class, 'member_type_id', 'id');
    }

    public function memberClass()
    {
        return $this->belongsTo(MemberClass::class, 'member_class_id', 'id');
    }
    public function notifications()
    {
        return $this->hasMany(MemberNotification::class, 'member_id');
    }

}
