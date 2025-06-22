<?php

namespace App\Models\OAI;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Record extends Model
{
    use HasFactory;

    protected $table = 'knowledge_item';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'title',
        'published_year',
        'publisher_city',
        'publisher_name',
        'knowledge_type_id',
        'language',
        'author'
    ];

    public function stock()
    {
        return $this->hasOne(KnowledgeStock::class, 'knowledge_item_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo(KnowledgeSubject::class, 'knowledge_subject_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo(KnowledgeType::class, 'knowledge_type_id', 'id');
    }

    public function getSetSpecAttribute()
    {
        if ($this->type) {
            return $this->type->id . ':' . $this->type->name;
        }
        return null;
    }

    /**
     * Generate URL-friendly slug from title
     */
    public function generateSlug()
    {
        return Str::slug($this->title, '-');
    }

    /**
     * Get Resource Identifier (URL)
     */
    public function getResourceIdentifierAttribute()
    {
        $slug = $this->generateSlug();
        return "https://openlibrary.telkomuniversity.ac.id/pustaka/{$this->id}/{$slug}.html";
    }

    public function classification()
    {
        return $this->belongsTo(ClassificationCode::class, 'classification_code_id', 'id');
    }

    public function getKodeKatalogAttribute()
    {
        return $this->classification ? $this->classification->code : 'unknown';
    }

}
