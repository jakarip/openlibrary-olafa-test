<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KnowledgeItemFileReadonlyModel extends Model
{
    use HasFactory;

    protected $table = 'knowledge_item_file_readonly';
    protected $primaryKey = 'id';
    public $timestamps = false; // Table only has created_at

    protected $fillable = [
        'knowledge_item_id',
        'member_id',
        'name',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    // Relationships
    public function knowledgeItem()
    {
        return $this->belongsTo(KnowledgeItemModel::class, 'knowledge_item_id', 'id');
    }

    public function member()
    {
        return $this->belongsTo(MemberModel::class, 'member_id', 'id');
    }

    // Scopes
    public function scopeByItem($query, $itemId)
    {
        return $query->where('knowledge_item_id', $itemId);
    }

    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Static methods
    public static function logReadonly($knowledgeItemId, $memberId, $fileName)
    {
        return self::create([
            'knowledge_item_id' => $knowledgeItemId,
            'member_id' => $memberId,
            'name' => $fileName,
            'created_at' => now()
        ]);
    }



    public static function getMemberReadonlyHistory($memberId, $limit = 50)
    {
        return self::where('member_id', $memberId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->with('knowledgeItem')
            ->get();
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });
    }
}