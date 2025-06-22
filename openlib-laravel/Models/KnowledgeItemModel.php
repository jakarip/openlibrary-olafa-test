<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class KnowledgeItemModel extends Model
{
    use HasFactory;

    protected $table = 'knowledge_item';
    protected $primaryKey = 'id';
    public $timestamps = true;

    // Mass assignable fields
    protected $fillable = [
        'knowledge_type_id',
        'classification_code_id',
        'item_location_id',
        'code',
        'collation',
        'faculty_code',
        'course_code',
        'title',
        'author',
        'knowledge_subject_id',
        'alternate_subject',
        'isbn',
        'author_type',
        'translator',
        'editor',
        'publisher_name',
        'publisher_city',
        'published_year',
        'language',
        'origination',
        'supplier',
        'price',
        'entrance_date',
        'abstract_content',
        'cover_path',
        'softcopy_path',
        'penalty_cost',
        'rent_cost',
        'created_by',
        'updated_by',
        'wd_id',
        'rent_freq',
        'stock_total',
        'stock_available',
        'stock_entrance_date',
        'slug',
        'stock_per_item'
    ];

    protected $casts = [
        'published_year' => 'integer',
        'author_type' => 'integer',
        'origination' => 'integer',
        'price' => 'integer',
        'penalty_cost' => 'integer',
        'rent_cost' => 'integer',
        'rent_freq' => 'integer',
        'stock_total' => 'integer',
        'stock_available' => 'integer',
        'stock_per_item' => 'integer',
        'entrance_date' => 'datetime',
        'stock_entrance_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function knowledgeSubject()
    {
        return $this->belongsTo(KnowledgeSubjectModel::class, 'knowledge_subject_id');
    }

    public function classification()
    {
        return $this->belongsTo(ClassificationCodeModel::class, 'classification_code_id', 'id');
    }

    public function knowledgeType()
    {
        return $this->belongsTo(KnowledgeTypeModel::class, 'knowledge_type_id', 'id');
    }

    public function itemLocation()
    {
        return $this->belongsTo(ItemLocation::class, 'item_location_id', 'id');
    }

    public function fakultas()
    {
        return $this->belongsTo(FakultasModel::class, 'faculty_code', 'C_KODE_FAKULTAS');
    }
    public function prodi()
    {
        return $this->belongsTo(ProdiModel::class, 'course_code', 'C_KODE_PRODI');
    }


    // One catalog item can have many stock items
    public function stocks()
    {
        return $this->hasMany(KnowledgeStockModel::class, 'knowledge_item_id', 'id');
    }

    public function refreshStockCache()
    {
        $total = DB::table('knowledge_stock')
            ->where('knowledge_item_id', $this->id)
            ->count();

        $available = DB::table('knowledge_stock')
            ->where('knowledge_item_id', $this->id)
            ->where('status', 1) // STATUS_AVAILABLE
            ->count();

        $this->update([
            'stock_total' => $total,
            'stock_available' => $available
        ]);

        return $this;
    }

    // Many-to-many relationship with subjects via knowledge_item_subject table
    public function subjects()
    {
        return $this->belongsToMany(
            MasterSubjectModel::class,
            'knowledge_item_subject',
            'knowledge_item_id',
            'master_subject_id'
        );
    }


    // E-learning catalog relationship
    public function elearningCatalogs()
    {
        return $this->hasMany(ElearningCatalogModel::class, 'catalog_id', 'id');
    }

    // File download logs
    public function fileDownloads()
    {
        return $this->hasMany(KnowledgeItemFileDownloadModel::class, 'knowledge_item_id', 'id');
    }

    // File readonly logs
    public function fileReadonly()
    {
        return $this->hasMany(KnowledgeItemFileReadonlyModel::class, 'knowledge_item_id', 'id');
    }

    // Item orders
    public function itemOrders()
    {
        return $this->hasMany(ItemOrderModel::class, 'knowledge_item_id', 'id');
    }

    // Item requests
    public function itemRequests()
    {
        return $this->hasMany(ItemRequestModel::class, 'knowledge_item_id', 'id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNotNull('id');
    }

    public function scopeByType($query, $typeId)
    {
        return $query->where('knowledge_type_id', $typeId);
    }

    public function scopeByLocation($query, $locationId)
    {
        return $query->where('item_location_id', $locationId);
    }

    public function scopeByFaculty($query, $facultyCode)
    {
        return $query->where('faculty_code', $facultyCode);
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
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('isbn', 'like', "%{$search}%")
                ->orWhere('publisher_name', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getAvailableStockAttribute()
    {
        return DB::table('knowledge_stock')
            ->where('knowledge_item_id', $this->id)
            ->whereIn('status', [1, 6])
            ->count();
    }

    public function getTotalStockAttribute()
    {
        return DB::table('knowledge_stock')
            ->where('knowledge_item_id', $this->id)
            ->count();
    }

    public function getIsAvailableAttribute()
    {
        return $this->stock_available > 0;
    }


    // Static methods
    public static function getRecentKaryaIlmiah()
    {
        return self::select('code', 'title', 'author', 'editor')
            ->whereIn('knowledge_type_id', [4, 5, 6, 79])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getByCode($code)
    {
        return self::where('code', $code)->first();
    }

    public static function getPopularItems($limit = 10)
    {
        return self::withCount(['itemOrders', 'fileDownloads'])
            ->orderBy('item_orders_count', 'desc')
            ->orderBy('file_downloads_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getNewAcquisitions($limit = 10)
    {
        return self::orderBy('entrance_date', 'desc')
            ->limit($limit)
            ->get();
    }

    // Helper methods
    public function generateSlug()
    {
        $slug = Str::slug($this->title);
        $count = self::where('slug', 'like', $slug . '%')->count();

        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }

        return $slug;
    }

    public function updateStockCounts()
    {
        $this->stock_total = $this->stocks()->count();
        $this->stock_available = $this->stocks()->where('status', 1)->count();
        $this->save();
    }

    public function softcopyFiles()
    {
        return $this->hasMany(KnowledgeItemFileModel::class, 'kif_knowledge_item_id', 'id')
            ->orderBy('kif_datetime', 'desc');
    }
    /**
     * Get all softcopy files with upload type info
     */
    public function getSoftcopyFilesAttribute()
    {
        return $this->softcopyFiles()->with('uploadType')->get();
    }

    /**
     * Check if catalog has any softcopy files
     */
    public function getHasSoftcopyAttribute()
    {
        return $this->softcopyFiles()->count() > 0;
    }

    /**
     * Get uploaded upload type IDs for this catalog
     */
    public function getUploadedTypeIds()
    {
        return $this->softcopyFiles()->pluck('kif_upload_type_id')->toArray();
    }
    // Helper method untuk get available upload types yang belum digunakan
    public function getAvailableUploadTypes()
    {
        $usedTypeIds = $this->softcopyFiles()->pluck('kif_upload_type_id')->toArray();

        return UploadTypeModel::where('is_secure', 0)
            ->whereNotIn('id', $usedTypeIds)
            ->orderBy('title')
            ->get();
    }

    // Helper method untuk get cover URL dengan path baru
    public function getCoverUrl()
    {
        if (empty($this->cover_path)) {
            return asset('assets/img/default-book-cover.jpg');
        }

        // Clean the path
        $coverPath = trim($this->cover_path);

        // Check if it's already a full URL
        if (filter_var($coverPath, FILTER_VALIDATE_URL)) {
            return $coverPath;
        }

        // Jika hanya nama file, tambahkan path prefix
        if (!str_contains($coverPath, '/')) {
            $fullPath = "book/{$this->code}/{$coverPath}";
        } else {
            $fullPath = $coverPath;
        }

        // Cek file dengan path baru
        $localPath = storage_path('app/public/' . $fullPath);

        if (file_exists($localPath)) {
            return asset('storage/' . $fullPath);
        } else {
            // Fallback ke path lama jika ada
            $oldPath = storage_path('app/public/uploads/book/cover/' . basename($coverPath));
            if (file_exists($oldPath)) {
                return asset('storage/uploads/book/cover/' . basename($coverPath));
            } else {
                // Fallback ke remote URL
                return 'https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/' . basename($coverPath);
            }
        }
    }


    // Helper method untuk check apakah punya softcopy
    public function hasSoftcopy()
    {
        return $this->softcopyFiles()->count() > 0;
    }


    // Boot method to handle automatic fields
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->user()->master_data_user ?? auth()->id();
            }

            if (empty($model->entrance_date)) {
                $model->entrance_date = now();
            }

            if (empty($model->slug)) {
                $model->slug = $model->generateSlug();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->user()->master_data_user ?? auth()->id();
            }
        });
    }
    public function type()
    {
        return $this->belongsTo(KnowledgeTypeModel::class, 'knowledge_type_id', 'id');
    }
    public function stock()
    {
        return $this->belongsTo(KnowledgeStockItemModel::class, 'id', 'knowledge_item_id');
    }
}
