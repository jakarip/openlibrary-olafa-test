<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KnowledgeItemFileModel extends Model
{
    use HasFactory;

    protected $table = 'knowledge_item_file';
    protected $primaryKey = 'kif_id';
    public $timestamps = false; // Tabel ini pakai kif_datetime

    protected $fillable = [
        'kif_knowledge_item_id',
        'kif_file',
        'kif_upload_type_id',
        'kif_datetime'
    ];

    protected $casts = [
        'kif_datetime' => 'datetime',
        'kif_knowledge_item_id' => 'integer', // PASTIKAN DATABASE SUDAH INT
        'kif_upload_type_id' => 'integer',
    ];

    // Relationships
    public function knowledgeItem()
    {
        return $this->belongsTo(KnowledgeItemModel::class, 'kif_knowledge_item_id', 'id');
    }

    public function uploadType()
    {
        return $this->belongsTo(UploadTypeModel::class, 'kif_upload_type_id', 'id');
    }

    // Helper methods
    public function getFullPath()
    {
        $catalog = $this->knowledgeItem;
        if (!$catalog) {
            return null;
        }
        return "book/{$catalog->code}/{$this->kif_file}";
    }

    public function getStoragePath()
    {
        $fullPath = $this->getFullPath();
        if (!$fullPath) {
            return null;
        }
        return storage_path('app/public/' . $fullPath);
    }

    public function getUrl()
    {
        $fullPath = $this->getFullPath();
        if (!$fullPath) {
            return null;
        }
        return asset('storage/' . $fullPath);
    }

    public function exists()
    {
        $path = $this->getStoragePath();
        return $path && file_exists($path);
    }
    /**
     * Accessor untuk filename
     */
    public function getFilenameAttribute()
    {
        return $this->kif_file;
    }

    /**
     * Accessor untuk upload date
     */
    public function getUploadDateAttribute()
    {
        return $this->kif_datetime;
    }

    /**
     * Generate download URL
     */
    public function getDownloadUrlAttribute()
    {
        if ($this->knowledgeItem) {
            return route('catalog.download', [
                'code' => $this->knowledgeItem->code,
                'file' => $this->kif_file
            ]);
        }
        return null;
    }

    /**
     * Get file extension
     */
    public function getFileExtensionAttribute()
    {
        return pathinfo($this->kif_file, PATHINFO_EXTENSION);
    }

    /**
     * Get file size if available
     */
    public function getFileSizeAttribute()
    {
        if ($this->knowledgeItem) {
            $filePath = storage_path('app/public/book/' . $this->knowledgeItem->code . '/' . $this->kif_file);
            if (file_exists($filePath)) {
                return filesize($filePath);
            }
        }
        return null;
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        $size = $this->file_size;
        if (!$size)
            return 'Unknown';

        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }
    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kif_datetime)) {
                $model->kif_datetime = now();
            }
        });
    }


}