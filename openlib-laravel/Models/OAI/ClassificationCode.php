<?php

namespace App\Models\OAI;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassificationCode extends Model
{
    use HasFactory;

    protected $table = 'classification_code';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['id', 'code'];
}
