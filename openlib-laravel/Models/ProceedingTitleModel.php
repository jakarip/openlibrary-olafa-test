<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProceedingTitleModel extends Model
{
    use HasFactory;

    protected $table = 'proceeding_title';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'subject_id',
        'title',
        'link',
    ];

    /**
     * Define an inverse one-to-many relationship with ProceedingSubjectModel.
     */
    public function subject()
    {
        return $this->belongsTo(ProceedingSubjectModel::class, 'subject_id', 'id');
    }
}
