<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppsModulesGroupModel extends Model
{
    // use SoftDeletes;

    protected $table = 'apps_modules_group';
    protected $primaryKey = 'amg_id';
}
