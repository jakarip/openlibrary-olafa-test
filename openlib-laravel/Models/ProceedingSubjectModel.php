<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProceedingSubjectModel extends Model
{
    use HasFactory;

    protected $table = 'proceeding_subject';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'subject_name',
    ];
    
    /**
     * Define a one-to-many relationship with ProceedingTitleModel.
     */
    public function titles()
    {
        return $this->hasMany(ProceedingTitleModel::class, 'subject_id', 'id');
    }
}

