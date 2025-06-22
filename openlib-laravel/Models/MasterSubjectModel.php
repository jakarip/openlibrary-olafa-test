<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterSubjectModel extends Model
{
    use HasFactory;

    protected $table = 'master_subject';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'curriculum_code',
        'course_code',
        'code',
        'name',
        'semester',
        'course_code_cadangan'
    ];

    protected $casts = [
        'semester' => 'string'
    ];

    // Relationships
    public function knowledgeItems()
    {
        return $this->belongsToMany(
            KnowledgeItemModel::class,
            'knowledge_item_subject',
            'master_subject_id',
            'knowledge_item_id'
        )->withPivot('course_codes');
    }

    // Scopes
    public function scopeByCourse($query, $courseCode)
    {
        return $query->where('course_code', $courseCode);
    }

    public function scopeByCurriculum($query, $curriculumCode)
    {
        return $query->where('curriculum_code', $curriculumCode);
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('course_code', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->code . ' - ' . $this->name;
    }

    public function getDisplayNameAttribute()
    {
        return $this->name . ' (Semester ' . $this->semester . ')';
    }

    // Static methods
    public static function getByCourseAndSemester($courseCode, $semester)
    {
        return self::where('course_code', $courseCode)
            ->where('semester', $semester)
            ->orderBy('name')
            ->get();
    }

    public static function getSubjectOptions($courseCode = null)
    {
        $query = self::select('id', 'code', 'name', 'semester', 'course_code')
            ->orderBy('course_code')
            ->orderBy('semester')
            ->orderBy('name');

        if ($courseCode) {
            $query->where('course_code', $courseCode);
        }

        return $query->get();
    }
}