<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppsLanguagesModel extends Model
{
    use SoftDeletes;

    protected $table = 'apps_languages';
    protected $primaryKey = 'al_id';
}
