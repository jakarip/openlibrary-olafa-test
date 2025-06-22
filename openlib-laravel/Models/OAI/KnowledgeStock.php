<?php

namespace App\Models\OAI;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeStock extends Model
{
    use HasFactory;

    protected $table = 'knowledge_stock';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['id', 'code', 'supplier', 'status', 'knowledge_item_id'];
}
