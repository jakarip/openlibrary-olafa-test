<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassificationCodeModel extends Model
{
    protected $table = 'classification_code';
    protected $primaryKey = 'id';
    public $timestamps = true;

    // Add all necessary fields to fillable property
    protected $fillable = [
        'code',
        'name',
        'description',
        'tree_left',
        'tree_right',
        'tree_level',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tree_left' => 'integer',
        'tree_right' => 'integer',
        'tree_level' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Scope for active classifications if needed
    public function scopeActive($query)
    {
        return $query->whereNotNull('id');
    }

    // Boot method to handle automatic fields
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