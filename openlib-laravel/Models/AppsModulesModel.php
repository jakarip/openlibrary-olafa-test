<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppsModulesModel extends Model
{
    use SoftDeletes;

    protected $table = 'apps_modules';
    protected $primaryKey = 'am_id';

    public function group()
    {
        return $this->belongsTo(AppsModulesGroupModel::class, 'am_group', 'amg_id');
    }

    public function permission()
    {
        return $this->hasMany(AppsPermissionModel::class, 'ap_id_modules', 'am_id');
    }
}
