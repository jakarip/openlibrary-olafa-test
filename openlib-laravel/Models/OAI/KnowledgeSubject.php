<?php

namespace App\Models\OAI;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeSubject extends Model
{
    use HasFactory;

    protected $table = 'knowledge_subject';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['id', 'name'];
}
