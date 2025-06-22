<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AppsPermissionModel;

class AppsPermissionRoleModel extends Model
{
    use SoftDeletes;

    protected $table = 'apps_permission_role';
    protected $primaryKey = 'apr_id';
    protected $fillable = ['apr_id_permission', 'apr_id_role'];

    public function permission()
    {
        return $this->belongsTo(AppsPermissionModel::class, 'apr_id_permission', 'ap_id');
    }
}


