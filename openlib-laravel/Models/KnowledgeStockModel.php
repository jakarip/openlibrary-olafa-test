<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KnowledgeStockModel extends Model
{
    use HasFactory;

    protected $table = 'knowledge_stock';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'knowledge_item_id',
        'knowledge_type_id',
        'item_location_id',
        'code',
        'rfid',
        'faculty_code',
        'course_code',
        'origination',
        'supplier',
        'price',
        'entrance_date',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'knowledge_item_id' => 'integer',
        'knowledge_type_id' => 'integer',
        'item_location_id' => 'integer',
        'origination' => 'integer',
        'price' => 'integer',
        'status' => 'integer',
        'entrance_date' => 'datetime'
    ];

    // Status constants
    const STATUS_AVAILABLE = 1;       // Tersedia
    const STATUS_BORROWED = 2;        // Dipinjam
    const STATUS_DAMAGED = 3;         // Rusak
    const STATUS_LOST = 4;            // Hilang
    const STATUS_EXPIRED = 5;         // Expired
    const STATUS_LOST_REPLACED = 6;   // Hilang diganti
    const STATUS_PROCESSING = 7;      // Diolah
    const STATUS_RESERVE = 8;         // Cadangan
    const STATUS_WEEDING = 9;         // Weeding

    // Status labels
    public static function getStatusLabels()
    {
        return [
            self::STATUS_AVAILABLE => 'Tersedia',
            self::STATUS_BORROWED => 'Dipinjam',
            self::STATUS_DAMAGED => 'Rusak',
            self::STATUS_LOST => 'Hilang',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_LOST_REPLACED => 'Hilang diganti',
            self::STATUS_PROCESSING => 'Diolah',
            self::STATUS_RESERVE => 'Cadangan',
            self::STATUS_WEEDING => 'Weeding'
        ];
    }

    // Relationships
    public function knowledgeItem()
    {
        return $this->belongsTo(KnowledgeItemModel::class, 'knowledge_item_id');
    }

    public function knowledgeType()
    {
        return $this->belongsTo(KnowledgeTypeModel::class, 'knowledge_type_id');
    }

    public function itemLocation()
    {
        return $this->belongsTo(ItemLocation::class, 'item_location_id');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeBorrowed($query)
    {
        return $query->where('status', self::STATUS_BORROWED);
    }

    public function scopeDamaged($query)
    {
        return $query->where('status', self::STATUS_DAMAGED);
    }

    public function scopeLost($query)
    {
        return $query->whereIn('status', [self::STATUS_LOST, self::STATUS_LOST_REPLACED]);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        $labels = self::getStatusLabels();
        return $labels[$this->status] ?? 'Unknown';
    }

    public function getIsAvailableAttribute()
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function getIsBorrowedAttribute()
    {
        return $this->status === self::STATUS_BORROWED;
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

        // Auto-update stock counts di knowledge_item setelah create/update/delete
        static::saved(function ($model) {
            $model->knowledgeItem?->updateStockCounts();
        });

        static::deleted(function ($model) {
            $model->knowledgeItem?->updateStockCounts();
        });
    }
}