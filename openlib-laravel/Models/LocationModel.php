<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\KnowledgeItemModel;

class LocationModel extends Authenticatable
{
    protected $table = 'item_location';
    protected $primaryKey = 'id';

    public function collection(){
        return $this->hasMany(KnowledgeItemModel::class, 'item_location_id', 'location_id');
    }

}
