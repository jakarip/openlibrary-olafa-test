<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnowledgeStockItemModel extends Authenticatable
{
    protected $table = 'stock_per_items';
    protected $primaryKey = 'knowledge_item_id';
}
