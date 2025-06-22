<?php
namespace App\Models\OAI;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeType extends Model
{
    use HasFactory;

    protected $table = 'knowledge_type';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['id', 'name'];
}
