<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EJournalTitleModel extends Model
{
    use HasFactory;

    protected $table = 'e_journal_title';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'subject_id',
        'title_name',
        'sinta_level',
        'link',
    ];

    /**
     * Define an inverse one-to-many relationship with EJournalSubjectModel.
     */
    public function subject()
    {
        return $this->belongsTo(EJournalSubjectModel::class, 'subject_id');
    }
}
