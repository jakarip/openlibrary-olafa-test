<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemOrderModel extends Model
{
    use HasFactory;

    protected $table = 'item_order';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'member_id',
        'knowledge_item_id',
        'order_at',
        'available_at',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'order_at' => 'datetime',
        'available_at' => 'datetime',
        'status' => 'boolean'
    ];

    // Status constants
    const STATUS_PENDING = 0;
    const STATUS_AVAILABLE = 1;

    // Relationships
    public function member()
    {
        return $this->belongsTo(MemberModel::class, 'member_id', 'id');
    }

    public function knowledgeItem()
    {
        return $this->belongsTo(KnowledgeItemModel::class, 'knowledge_item_id', 'id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function scopeByItem($query, $itemId)
    {
        return $query->where('knowledge_item_id', $itemId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('order_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return $this->status ? 'Available' : 'Pending';
    }

    public function getIsPendingAttribute()
    {
        return $this->status == self::STATUS_PENDING;
    }

    public function getIsAvailableAttribute()
    {
        return $this->status == self::STATUS_AVAILABLE;
    }

    // Methods
    public function markAsAvailable()
    {
        $this->update([
            'status' => self::STATUS_AVAILABLE,
            'available_at' => now()
        ]);
    }

    public function markAsPending()
    {
        $this->update([
            'status' => self::STATUS_PENDING,
            'available_at' => null
        ]);
    }

    // Static methods
    public static function createOrder($memberId, $knowledgeItemId)
    {
        return self::create([
            'member_id' => $memberId,
            'knowledge_item_id' => $knowledgeItemId,
            'order_at' => now(),
            'status' => self::STATUS_PENDING
        ]);
    }

    public static function getPendingOrders()
    {
        return self::pending()
            ->with(['member', 'knowledgeItem'])
            ->orderBy('order_at', 'asc')
            ->get();
    }

    public static function getMemberOrders($memberId)
    {
        return self::byMember($memberId)
            ->with('knowledgeItem')
            ->orderBy('order_at', 'desc')
            ->get();
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->user()->master_data_user ?? auth()->id();
            }

            if (empty($model->order_at)) {
                $model->order_at = now();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->user()->master_data_user ?? auth()->id();
            }
        });
    }
}