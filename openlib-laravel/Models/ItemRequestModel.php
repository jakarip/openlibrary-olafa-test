<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemRequestModel extends Model
{
    use HasFactory;

    protected $table = 'item_request';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'member_id',
        'knowledge_item_id',
        'course_code',
        'title',
        'author',
        'publisher',
        'description',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    // Status constants
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;

    // Relationships
    public function member()
    {
        return $this->belongsTo(MemberModel::class, 'member_id', 'id');
    }

    public function knowledgeItem()
    {
        return $this->belongsTo(KnowledgeItemModel::class, 'knowledge_item_id', 'id');
    }

    public function subjects()
    {
        return $this->belongsToMany(
            MasterSubjectModel::class,
            'item_request_subject',
            'item_request_id',
            'master_subject_id'
        );
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function scopeByCourse($query, $courseCode)
    {
        return $query->where('course_code', $courseCode);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('author', 'like', "%{$search}%")
                ->orWhere('publisher', 'like', "%{$search}%")
                ->orWhere('course_code', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return $this->status ? 'Approved' : 'Pending';
    }

    public function getIsPendingAttribute()
    {
        return $this->status == self::STATUS_PENDING;
    }

    public function getIsApprovedAttribute()
    {
        return $this->status == self::STATUS_APPROVED;
    }

    // Methods
    public function approve($knowledgeItemId = null)
    {
        $updateData = [
            'status' => self::STATUS_APPROVED
        ];

        if ($knowledgeItemId) {
            $updateData['knowledge_item_id'] = $knowledgeItemId;
        }

        $this->update($updateData);
    }

    public function reject()
    {
        $this->update([
            'status' => self::STATUS_PENDING,
            'knowledge_item_id' => null
        ]);
    }

    // Static methods
    public static function createRequest($data)
    {
        return self::create([
            'member_id' => $data['member_id'],
            'course_code' => $data['course_code'],
            'title' => $data['title'],
            'author' => $data['author'],
            'publisher' => $data['publisher'],
            'description' => $data['description'] ?? null,
            'status' => self::STATUS_PENDING
        ]);
    }

    public static function getPendingRequests()
    {
        return self::pending()
            ->with(['member', 'subjects'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getMemberRequests($memberId)
    {
        return self::byMember($memberId)
            ->with('knowledgeItem')
            ->orderBy('created_at', 'desc')
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
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->user()->master_data_user ?? auth()->id();
            }
        });
    }
}