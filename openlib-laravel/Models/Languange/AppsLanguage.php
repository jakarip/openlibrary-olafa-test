<?php

namespace App\Models\Languange;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppsLanguage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'apps_languages'; 

    protected $primaryKey = 'al_id'; 

    protected $fillable = [
        'al_group',
        'al_key',
        'al_lang_id',
        'al_lang_en'
    ];
}
