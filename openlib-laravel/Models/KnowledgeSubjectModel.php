<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeSubjectModel extends Model
{
    protected $table = 'knowledge_subject';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function knowledgeItems()
    {
        return $this->hasMany(KnowledgeItemModel::class, 'knowledge_subject_id', 'id');
    }

    public static function getCatalogTopicsWithItemCount(){
        return self::select('id', 'name', 'active','updated_by', 'updated_at')
            ->withCount('knowledgeItems as jumlah_katalog')
            ->orderBy('name', 'ASC')
            ->get();
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->user()->master_data_user ?? auth()->id();
                $model->updated_by = auth()->user()->master_data_user ?? auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->user()->master_data_user ?? auth()->id();
            }
        });
    }
}