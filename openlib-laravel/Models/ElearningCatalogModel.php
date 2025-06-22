<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ElearningCatalogModel extends Model
{
    use HasFactory;

    protected $table = 'elearning_catalog';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'material_id',
        'calendar_id',
        'topic_id',
        'catalog_id',
        'description',
        'created_by',
        'updated_by'
    ];

    // Relationships


    public function catalog()
    {
        return $this->belongsTo(KnowledgeItemModel::class, 'catalog_id', 'id');
    }

    // Scopes
    public function scopeByMaterial($query, $materialId)
    {
        return $query->where('material_id', $materialId);
    }

    public function scopeByCalendar($query, $calendarId)
    {
        return $query->where('calendar_id', $calendarId);
    }

    public function scopeByTopic($query, $topicId)
    {
        return $query->where('topic_id', $topicId);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->user()->master_data_user ?? auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->user()->master_data_user ?? auth()->id();
            }
        });
    }
}