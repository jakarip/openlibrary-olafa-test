<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EJournalSubjectModel extends Model
{
    use HasFactory;

    protected $table = 'e_journal_subject';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'subject_name',
    ];

    /**
     * Define a one-to-many relationship with EJournalTitleModel.
     */
    public function titles()
    {
        return $this->hasMany(EJournalTitleModel::class, 'subject_id');
    }
}
