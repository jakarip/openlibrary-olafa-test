<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppsRolesModel extends Model
{
    use SoftDeletes;

    protected $table = 'apps_roles';
    protected $primaryKey = 'ar_id';

    public function permissions()
    {
        return $this->hasMany(AppsPermissionRoleModel::class, 'apr_id_role', 'ar_id');
    }
}
