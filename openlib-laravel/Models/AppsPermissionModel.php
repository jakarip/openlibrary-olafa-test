<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppsPermissionModel extends Model
{
    use SoftDeletes;

    protected $table = 'apps_permission';
    protected $primaryKey = 'ap_id';
    protected $fillable = ['ap_action', 'ap_slug_action', 'ap_slug', 'ap_id_modules'];
}
