<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KnowledgeItemModel;
use App\Models\KnowledgeTypeModel;
use App\Models\ClassificationCodeModel;
use App\Models\KnowledgeSubjectModel;
use App\Models\ItemLocation;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\FakultasModel;
use App\Models\ProdiModel;
use Illuminate\Support\Facades\Log;
use App\Models\KnowledgeStockModel;
use App\Models\KnowledgeItemFileModel;
use Illuminate\Validation\ValidationException;


class CatalogData extends Controller
{
    public function index()
    {
        return view('catalog.data.index');
    }
    /**
     * Show add catalog form page
     */
    public function add()
    {
        return view('catalog.data.add');
    }

    /**
     * Show edit catalog form page
     */
    public function editPage($id)
    {
        try {
            $catalog = KnowledgeItemModel::with([
                'knowledgeType',
                'classification',
                'knowledgeSubject',
                'itemLocation',
                'fakultas',
                'prodi'
            ])->findOrFail($id);

            // PERBAIKAN: Pastikan catalog punya slug
            if (!$catalog->slug) {
                $catalog->slug = $this->generateUniqueSlug($catalog->title, $catalog->id);
                $catalog->save();
            }

            // Hitung stock total dari knowledge_stock aktual
            $actualStockTotal = DB::table('knowledge_stock')
                ->where('knowledge_item_id', $catalog->id)
                ->count();

            $catalog->stock_total = $actualStockTotal;

            return view('catalog.data.edit', compact('catalog'));

        } catch (\Exception $e) {
            return redirect()->route('catalog')->withErrors(['error' => 'Katalog tidak ditemukan']);
        }
    }
    private function ensureCatalogHasSlug($catalog)
    {
        if (!$catalog->slug) {
            $catalog->slug = $this->generateUniqueSlug($catalog->title, $catalog->id);
            $catalog->save();
        }
        return $catalog;
    }
    /**
     * Show detail catalog page atau return AJAX data
     */
    public function detailPage(Request $request, $id, $slug = null)
    {
        try {
            // Check jika ini AJAX request - RETURN JSON DATA
            if ($request->ajax() || $request->wantsJson()) {
                return $this->getDetailAjaxData($id);
            }

            // WEB REQUEST - Return skeleton view dengan minimal data
            $catalog = KnowledgeItemModel::select('id', 'code', 'slug', 'title')->findOrFail($id);

            // Pastikan slug ada
            if (!$catalog->slug) {
                $catalog->slug = $this->generateUniqueSlug($catalog->title, $catalog->id);
                $catalog->save();
            }

            // Redirect jika slug tidak match
            if ($slug && $catalog->slug !== $slug) {
                return redirect()->route('catalog.detail', [
                    'id' => $catalog->id,
                    'slug' => $catalog->slug
                ], 301);
            }

            // Log detail page access jika user terautentikasi
            if (auth()->check()) {
                $this->logDetailPageAccess($catalog->id);
            }

            // Return skeleton view dengan minimal data untuk AJAX loading
            return view('catalog.data.detail', [
                'catalogId' => $catalog->id,
                'catalogSlug' => $catalog->slug,
                'catalogCode' => $catalog->code,
                'catalogTitle' => $catalog->title
            ]);

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Katalog tidak ditemukan: ' . $e->getMessage()
                ], 404);
            }

            return redirect()->route('catalog')->withErrors(['error' => 'Katalog tidak ditemukan']);
        }
    }
    /**
     * Log access to catalog detail page with file permissions
     * @param int $catalogId Catalog ID
     */
    private function logDetailPageAccess($catalogId)
    {
        try {
            // Skip if not authenticated
            if (!auth()->check()) {
                return;
            }

            // Get current user's member type ID
            $user = auth()->user();
            $memberTypeId = $user->member_type_id ?? null;

            if (!$memberTypeId) {
                return;
            }

            // Get files for this catalog
            $files = KnowledgeItemFileModel::where('kif_knowledge_item_id', $catalogId)
                ->with('uploadType')
                ->get();

            // Check permissions for each file
            $filePermissions = [];
            foreach ($files as $file) {
                $uploadTypeId = $file->kif_upload_type_id;

                // Check download permission
                $canDownload = DB::table('member_type_upload_type')
                    ->where('member_type_id', $memberTypeId)
                    ->where('upload_type_id', $uploadTypeId)
                    ->exists();

                // Check readonly permission
                $canRead = $canDownload || DB::table('member_type_upload_type_readonly')
                    ->where('member_type_id', $memberTypeId)
                    ->where('upload_type_id', $uploadTypeId)
                    ->exists();

                $filePermissions[] = [
                    'file_id' => $file->kif_id,
                    'file_name' => $file->kif_file,
                    'upload_type' => $file->uploadType->name ?? 'unknown',
                    'can_read' => $canRead,
                    'can_download' => $canDownload
                ];
            }

            // Log access with file permissions
            Log::info('Catalog detail page access', [
                'user_id' => auth()->id(),
                'user_name' => $user->master_data_user ?? 'unknown',
                'catalog_id' => $catalogId,
                'timestamp' => now()->toDateTimeString(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'file_permissions' => $filePermissions
            ]);

        } catch (\Exception $e) {
            Log::warning('Failed to log detail page access: ' . $e->getMessage(), [
                'catalog_id' => $catalogId,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    /**
     * Get detail data lengkap untuk AJAX response
     */
    private function getDetailAjaxData($id)
    {
        try {
            $catalog = KnowledgeItemModel::with([
                'knowledgeType',
                'classification',
                'knowledgeSubject',
                'itemLocation',
                'fakultas',
                'prodi'
            ])->findOrFail($id);

            $catalogData = $catalog->toArray();
            $catalogData['cover_url'] = $this->getCoverUrl($catalog->cover_path, $catalog->code);

            // Format dates
            if ($catalog->entrance_date) {
                $catalogData['entrance_date_formatted'] = $catalog->entrance_date->format('d/m/Y');
            }

            // Add relationships data
            $catalogData['knowledge_type_name'] = $catalog->knowledgeType->name ?? 'N/A';
            $catalogData['classification_name'] = $catalog->classification->name ?? 'N/A';
            $catalogData['subject_name'] = $catalog->knowledgeSubject->name ?? 'N/A';

            // LOKASI BUKU - ambil dari relasi itemLocation
            $catalogData['location_name'] = $catalog->itemLocation->name ?? 'N/A';
            $catalogData['location_phone'] = $catalog->itemLocation->phone ?? '081280000110';
            $catalogData['location_email'] = $catalog->itemLocation->email ?? 'library@telkomuniversity.ac.id';

            $catalogData['fakultas_name'] = $catalog->fakultas->NAMA_FAKULTAS ?? null;
            $catalogData['prodi_name'] = $catalog->prodi->NAMA_PRODI ?? null;

            // Add formatted data
            $catalogData['author_type_text'] = $this->getAuthorTypeText($catalog->author_type);
            $catalogData['origination_text'] = $catalog->origination == 1 ? 'Pembelian' : 'Sumbangan';
            $catalogData['price_formatted'] = $catalog->price ? 'Rp ' . number_format($catalog->price) : null;
            $catalogData['rent_cost_formatted'] = 'Rp ' . number_format($catalog->rent_cost);
            $catalogData['penalty_cost_formatted'] = 'Rp ' . number_format($catalog->penalty_cost);

            // Get user permissions for files if user is authenticated
            $memberTypeId = auth()->user()->member_type_id ?? null;

            // Load softcopy files with permission info
            $softcopyFiles = $this->getSoftcopyFilesWithPermissions($catalog->id, $catalog->code, $memberTypeId);
            $catalogData['softcopy_files'] = $softcopyFiles;
            $catalogData['has_softcopy'] = count($softcopyFiles) > 0;

            // Stock summary dan items
            $stockSummary = $this->calculateStockSummary($catalog->id);
            $catalogData['stock_summary'] = $stockSummary;

            $stockItems = $this->getStockItemsData($catalog->id);
            $catalogData['stock_items'] = $stockItems;

            // Calculate total value
            if ($catalog->price && $stockSummary['total'] > 0) {
                $catalogData['total_value'] = $catalog->price * $stockSummary['total'];
                $catalogData['total_value_formatted'] = 'Rp ' . number_format($catalogData['total_value']);
            }

            // Log AJAX detail access if authenticated
            if (auth()->check()) {
                $this->logDetailPageAccess($catalog->id);
            }

            return response()->json([
                'success' => true,
                'data' => $catalogData
            ]);

        } catch (\Exception $e) {
            \Log::error('Catalog AJAX detail error: ' . $e->getMessage(), [
                'catalog_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data katalog: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get softcopy files with permission information
     * @param int $catalogId Catalog ID
     * @param string $catalogCode Catalog code
     * @param int|null $memberTypeId Member type ID
     * @return array Files with permission info
     */
    private function getSoftcopyFilesWithPermissions($catalogId, $catalogCode, $memberTypeId = null)
    {
        $files = KnowledgeItemFileModel::with('uploadType')
            ->where('kif_knowledge_item_id', $catalogId) // Pastikan $catalogId didefinisikan
            ->get();

        return $files->map(function ($file) use ($catalogId, $catalogCode, $memberTypeId) { // Tambahkan $catalogId ke use()
            $filePath = "book/{$catalogCode}/{$file->kif_file}";
            $fullPath = storage_path('app/public/' . $filePath);

            // Check permissions
            $canDownload = false;
            $canRead = false;

            if ($memberTypeId && $file->kif_upload_type_id) {
                // Check download permission
                $canDownload = DB::table('member_type_upload_type')
                    ->where('member_type_id', $memberTypeId)
                    ->where('upload_type_id', $file->kif_upload_type_id)
                    ->exists();

                // Check readonly permission
                $canRead = $canDownload || DB::table('member_type_upload_type_readonly')
                    ->where('member_type_id', $memberTypeId)
                    ->where('upload_type_id', $file->kif_upload_type_id)
                    ->exists();
            }

            // Get download count
            $downloadCount = DB::table('knowledge_item_file_download')
                ->where('knowledge_item_id', $catalogId)
                ->where('name', $file->kif_file)
                ->count();

            // Get view count
            $viewCount = DB::table('knowledge_item_file_readonly')
                ->where('knowledge_item_id', $catalogId)
                ->where('name', $file->kif_file)
                ->count();

            return [
                'id' => $file->kif_id,
                'filename' => $file->kif_file,
                'upload_type' => $file->uploadType ? [
                    'id' => $file->uploadType->id,
                    'name' => $file->uploadType->name,
                    'title' => $file->uploadType->title,
                    'extension' => $file->uploadType->extension
                ] : null,
                'upload_date' => $file->kif_datetime,
                'upload_date_formatted' => \Carbon\Carbon::parse($file->kif_datetime)->format('d/m/Y'),
                'exists' => file_exists($fullPath),
                'can_read' => $canRead,
                'can_download' => $canDownload,
                'download_count' => $downloadCount,
                'view_count' => $viewCount,
                'url' => $canRead ? asset('storage/' . $filePath) : null,
                'download_url' => $canDownload ? route('catalog.download', [$catalogCode, $file->kif_file]) : null,
                'flipbook_url' => $canRead && $this->isFlipbookCompatible($file->kif_file) ?
                    route('catalog.flipbook', [$catalogId, \Str::slug(KnowledgeItemModel::find($catalogId)->title ?? ''), $file->kif_id]) : null
            ];
        })->toArray();
    }

    /**
     * Check if file is compatible with flipbook
     * @param string $filename Filename
     * @return boolean True if compatible
     */
    private function isFlipbookCompatible($filename)
    {
        $allowedExtensions = ['pdf', 'epub', 'flipbook'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $allowedExtensions);
    }
    /**
     * Helper method untuk get author type text
     */
    private function getAuthorTypeText($authorType)
    {
        switch ($authorType) {
            case 1:
                return 'Perorangan';
            case 2:
                return 'Organisasi';
            case 3:
                return 'Conference';
            default:
                return 'Tidak diketahui';
        }
    }

    /**
     * Helper method untuk get softcopy files data lengkap
     */
    private function getSoftcopyFilesData($catalogId, $catalogCode)
    {
        return $this->getSoftcopyFilesWithStats($catalogId, $catalogCode);
    }

    public function dt(Request $request)
    {
        try {
            $query = KnowledgeItemModel::query()
                ->select([
                    'id',
                    'code',
                    'slug',
                    'title',
                    'author',
                    'isbn',
                    'published_year',
                    'publisher_name',
                    'knowledge_type_id',
                    'knowledge_subject_id',
                    'item_location_id',
                    'faculty_code',
                    'course_code',
                    'price',
                    'cover_path',
                    'created_at',
                    'updated_at'
                ])
                ->with([
                    'knowledgeType:id,name',
                    'classification:id,code,name',
                    'knowledgeSubject:id,name',
                    'itemLocation:id,name',
                ])
                ->withCount([
                    'stocks as stock_total',
                    'stocks as stock_available' => function ($q) {
                        $q->whereIn('status', [1, 6]);
                    }
                ]);

            // Filter
            if ($request->filled('knowledge_type_id')) {
                $query->where('knowledge_type_id', $request->knowledge_type_id);
            }
            if ($request->filled('item_location_id')) {
                $query->where('item_location_id', $request->item_location_id);
            }
            if ($request->filled('faculty_code')) {
                $query->where('faculty_code', $request->faculty_code);
            }
            if ($request->filled('course_code')) {
                $query->where('course_code', $request->course_code);
            }
            if ($request->filled('published_year')) {
                $query->where('published_year', $request->published_year);
            }

            $selectedColumns = (array) $request->input('selected_columns', []);

            $dataTable = DataTables::eloquent($query)
                ->addColumn('action', function ($catalog) {
                    $btn = '<div class="btn-group my-btn-group">';
                    $btn .= '<button class="btn rounded-pill btn-icon btn-label-primary waves-effect my-dropdown-toggle" type="button" data-id="' . $catalog->id . '" data-slug="' . $catalog->slug . '">';
                    $btn .= '<i class="ti ti-dots-vertical"></i>';
                    $btn .= '</button>';
                    $btn .= '<ul class="dropdown-menu" style="display:none;">';
                    $btn .= '<li><a class="dropdown-item d-flex align-items-center view-btn" href="javascript:void(0);" data-id="' . $catalog->id . '" data-slug="' . $catalog->slug . '">';
                    $btn .= '<i class="ti ti-eye ti-sm me-2"></i> View Detail</a></li>';
                    $btn .= '<li><a class="dropdown-item d-flex align-items-center edit-btn" href="javascript:void(0);" data-id="' . $catalog->id . '">';
                    $btn .= '<i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>';
                    $btn .= '<li><a class="dropdown-item d-flex align-items-center text-danger delete-btn" href="javascript:void(0);" data-id="' . $catalog->id . '">';
                    $btn .= '<i class="ti ti-trash me-2"></i> Delete Data</a></li>';
                    $btn .= '</ul></div>';
                    return $btn;
                })
                ->addColumn('title_with_cover', function ($catalog) {
                    $coverUrl = $this->getCoverUrl($catalog->cover_path, $catalog->code);
                    $title = Str::limit($catalog->title, 60);
                    $defaultCover = asset('assets/img/default-book-cover.jpg');
                    $html = '<div class="d-flex align-items-center">';
                    $html .= '<div class="me-3">';
                    if ($coverUrl) {
                        $html .= '<img src="' . $coverUrl . '" alt="Cover" class="rounded catalog-cover" style="width: 40px; height: 40px; object-fit: cover;" 
          onerror="this.onerror=null; this.src=\'' . $defaultCover . '\'">';
                    } else {
                        $html .= '<img src="' . $defaultCover . '" alt="No Cover" class="rounded catalog-cover" style="width: 40px; height: 40px; object-fit: cover;">';
                    }
                    $html .= '</div>';
                    $html .= '<div>';
                    $html .= '<div class="fw-medium" title="' . htmlspecialchars($catalog->title) . '">' . $title . '</div>';
                    $html .= '<small class="text-muted">ISBN: ' . ($catalog->isbn ?: 'N/A') . '</small>';
                    $html .= '</div>';
                    $html .= '</div>';
                    return $html;
                })
                ->editColumn('author', function ($catalog) {
                    return Str::limit($catalog->author, 30);
                })
                ->addColumn('knowledge_type', function ($catalog) {
                    return $catalog->knowledgeType->name ?? '-';
                })
                ->addColumn('location', function ($catalog) {
                    return $catalog->itemLocation->name ?? '-';
                })
                ->addColumn('subject', function ($catalog) {
                    return $catalog->knowledgeSubject->name ?? '-';
                })
                ->addColumn('stock_info', function ($catalog) {
                    $available = $catalog->stock_available;
                    $total = $catalog->stock_total;
                    if ($available > 0) {
                        $badge = '<span class="badge bg-success">' . $available . '/' . $total . '</span>';
                    } else {
                        $badge = '<span class="badge bg-danger">' . $available . '/' . $total . '</span>';
                    }
                    return $badge;
                })
                ->editColumn('cover_path', function ($catalog) {
                    return $this->getCoverUrl($catalog->cover_path);
                })
                ->addColumn('id', function ($catalog) {
                    return $catalog->id;
                })
                ->addColumn('slug', function ($catalog) {
                    if (!$catalog->slug) {
                        $catalog->slug = $this->generateUniqueSlug($catalog->title, $catalog->id);
                        $catalog->save();
                    }
                    return $catalog->slug;
                })
                ->rawColumns(['action', 'title_with_cover', 'stock_info']);

            // PERBAIKAN: Selalu tambahkan kolom tambahan yang dipilih
            // ISBN Column
            if (in_array('isbn', $selectedColumns)) {
                $dataTable->addColumn('isbn', function ($catalog) {
                    return $catalog->isbn ?? '-';
                });
            }

            // Published Year Column
            if (in_array('published_year', $selectedColumns)) {
                $dataTable->addColumn('published_year', function ($catalog) {
                    return $catalog->published_year ?? '-';
                });
            }

            // Publisher Name Column
            if (in_array('publisher_name', $selectedColumns)) {
                $dataTable->addColumn('publisher_name', function ($catalog) {
                    return Str::limit($catalog->publisher_name, 30);
                });
            }

            // Price Column
            if (in_array('price', $selectedColumns)) {
                $dataTable->addColumn('price', function ($catalog) {
                    return $catalog->price ? 'Rp ' . number_format($catalog->price) : '-';
                });
            }

            // PERBAIKAN UTAMA: Created At Column - SELALU tersedia, format ketika dipilih
            if (in_array('created_at', $selectedColumns)) {
                $dataTable->editColumn('created_at', function ($catalog) {
                    return $catalog->created_at ? \Carbon\Carbon::parse($catalog->created_at)->format('d/m/Y H:i:s') : '-';
                });
            }

            // PERBAIKAN UTAMA: Updated At Column - SELALU tersedia, format ketika dipilih
            if (in_array('updated_at', $selectedColumns)) {
                $dataTable->editColumn('updated_at', function ($catalog) {
                    return $catalog->updated_at ? \Carbon\Carbon::parse($catalog->updated_at)->format('d/m/Y H:i:s') : '-';
                });
            }

            return $dataTable->toJson();
        } catch (\Exception $e) {
            Log::error('DataTable error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store new catalog (rename dari insert)
     */
    public function store(Request $request)
    {
        $request->validate([
            // Basic validations
            'knowledge_type_id' => 'required|exists:knowledge_type,id',
            'classification_code_id' => 'required|exists:classification_code,id',
            'item_location_id' => 'required|exists:item_location,id',
            'knowledge_subject_id' => 'required|exists:knowledge_subject,id',
            'title' => 'required|string|max:255',
            'collation' => 'required|string|max:255',
            'author' => 'required|string',
            'publisher_name' => 'required|string|max:255',
            'published_year' => 'required|integer|min:1000|max:' . (date('Y') + 5),
            'author_type' => 'required|integer|in:1,2,3',
            'origination' => 'required|integer|in:1,2',
            'entrance_date' => 'required|date',
            'rent_cost' => 'required|integer|min:0',
            'penalty_cost' => 'required|integer|min:0',
            'stock_total' => 'required|integer|min:1',

            // Optional fields
            'isbn' => 'nullable|string|max:50',
            'faculty_code' => 'nullable|string|exists:t_mst_fakultas,C_KODE_FAKULTAS',
            'course_code' => 'nullable|string|exists:t_mst_prodi,C_KODE_PRODI',
            'alternate_subject' => 'nullable|string|max:255',
            'translator' => 'nullable|string|max:255',
            'editor' => 'nullable|string|max:255',
            'publisher_city' => 'nullable|string|max:100',
            'language' => 'nullable|string|max:50',
            'supplier' => 'nullable|string|max:255',
            'price' => 'nullable|integer|min:0',
            'abstract_content' => 'nullable|string',

            // File uploads - PERBAIKAN VALIDATION
            'cover_image' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:2048', // 2MB
            'softcopy_data' => 'nullable|string', // JSON string

            // Dynamic softcopy files validation akan dilakukan manual
        ], [
            // Custom error messages
            'cover_image.mimes' => 'Cover harus berformat JPG, JPEG, PNG, atau GIF',
            'cover_image.max' => 'Ukuran cover maksimal 2MB',
            'cover_image.file' => 'Cover harus berupa file gambar',

            // Add other custom messages if needed
            'knowledge_type_id.required' => 'Jenis katalog wajib dipilih',
            'classification_code_id.required' => 'Klasifikasi wajib dipilih',
            'item_location_id.required' => 'Lokasi wajib dipilih',
            'knowledge_subject_id.required' => 'Subjek wajib dipilih',
            'title.required' => 'Judul wajib diisi',
            'collation.required' => 'Kolasi wajib diisi',
            'author.required' => 'Pengarang wajib diisi',
            'publisher_name.required' => 'Nama penerbit wajib diisi',
            'published_year.required' => 'Tahun terbit wajib diisi',
            'author_type.required' => 'Jenis pengarang wajib dipilih',
            'origination.required' => 'Status penerimaan wajib dipilih',
            'entrance_date.required' => 'Tanggal masuk wajib diisi',
            'rent_cost.required' => 'Harga pinjam wajib diisi',
            'penalty_cost.required' => 'Biaya denda wajib diisi',
            'stock_total.required' => 'Jumlah koleksi wajib diisi',
        ]);

        // Manual validation untuk softcopy files
        $this->validateSoftcopyFiles($request);

        DB::beginTransaction();

        try {
            $user = auth()->user()?->master_data_user ?? 'system';
            $catalogCodes = $this->generateCatalogCode($request->knowledge_type_id, $request->stock_total);
            $data = $request->except(['cover_image', 'softcopy_data', 'softcopy_type_id']);

            // Add additional fields
            $data['code'] = $catalogCodes['catalog_code'];
            $data['created_by'] = $user;
            $data['updated_by'] = $user;
            $data['stock_available'] = $request->stock_total;

            $catalog = KnowledgeItemModel::create($data);

            // Handle file uploads
            $this->handleFileUploads($catalog, $request);

            // Generate slug
            $catalog->slug = $this->generateUniqueSlug($catalog->title, $catalog->id);
            $catalog->save();

            // Create stock items
            $this->createStockItems($catalog, $catalogCodes['stock_codes'], $request);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Katalog {$catalog->code} berhasil ditambahkan dengan {$request->stock_total} eksemplar!",
                'redirect_url' => route('catalog.detail', ['id' => $catalog->id, 'slug' => $catalog->slug])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->cleanupFiles($catalog->code ?? null);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan katalog: ' . $e->getMessage(),
                'errors' => []
            ], 422);
        }
    }


    public function edit(Request $request)
    {
        try {
            $id = $request->input('id');
            $catalog = KnowledgeItemModel::with([
                'knowledgeType',
                'classification',
                'knowledgeSubject',
                'itemLocation',
                'fakultas',
                'prodi',
                'stocks' // TAMBAHKAN relasi stocks
            ])->findOrFail($id);

            // Format data untuk frontend
            $catalogData = $catalog->toArray();

            // PERBAIKAN: Hitung stock_total dari knowledge_stock yang sebenarnya
            $actualStockTotal = DB::table('knowledge_stock')
                ->where('knowledge_item_id', $catalog->id)
                ->count();

            // Override stock_total dengan data aktual
            $catalogData['stock_total'] = $actualStockTotal;

            // TAMBAHAN: Hitung stock available juga
            $availableStock = DB::table('knowledge_stock')
                ->where('knowledge_item_id', $catalog->id)
                ->whereIn('status', [1, 6]) // Tersedia + Hilang diganti
                ->count();

            $catalogData['stock_available'] = $availableStock;

            // Handle date formatting
            if ($catalog->entrance_date) {
                $catalogData['entrance_date'] = $catalog->entrance_date->format('Y-m-d');
            }

            // Handle cover URL
            $catalogData['cover_url'] = $this->getCoverUrl($catalog->cover_path);

            // Handle softcopy info
            if ($catalog->softcopy_path) {
                $catalogData['has_softcopy'] = true;
                $catalogData['softcopy_name'] = basename($catalog->softcopy_path);
            }


            return response()->json([
                'success' => true,
                'data' => $catalogData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Katalog tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update catalog
     */
    public function updatePage(Request $request, $id)
    {
        $request->validate([
            'knowledge_type_id' => 'required|exists:knowledge_type,id',
            'classification_code_id' => 'required|exists:classification_code,id',
            'item_location_id' => 'required|exists:item_location,id',
            'knowledge_subject_id' => 'required|exists:knowledge_subject,id',
            'title' => 'required|string|max:255',
            'collation' => 'required|string|max:255',
            'author' => 'required|string',
            'publisher_name' => 'required|string|max:255',
            'published_year' => 'required|integer|min:1000|max:' . (date('Y') + 5),
            'author_type' => 'required|integer|in:1,2,3',
            'origination' => 'required|integer|in:1,2',
            'entrance_date' => 'required|date',
            'rent_cost' => 'required|integer|min:0',
            'penalty_cost' => 'required|integer|min:0',
            'stock_total' => 'required|integer|min:1',

            // Optional fields
            'isbn' => 'nullable|string|max:50',
            'faculty_code' => 'nullable|string|exists:t_mst_fakultas,C_KODE_FAKULTAS',
            'course_code' => 'nullable|string|exists:t_mst_prodi,C_KODE_PRODI',
            'alternate_subject' => 'nullable|string|max:255',
            'translator' => 'nullable|string|max:255',
            'editor' => 'nullable|string|max:255',
            'publisher_city' => 'nullable|string|max:100',
            'language' => 'nullable|string|max:50',
            'supplier' => 'nullable|string|max:255',
            'price' => 'nullable|integer|min:0',
            'abstract_content' => 'nullable|string',
            'softcopy_type_id' => 'nullable|exists:upload_type,id',

            // File uploads
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'softcopy_file' => 'nullable|file|max:10240',
        ]);

        DB::beginTransaction();

        try {
            $catalog = KnowledgeItemModel::findOrFail($id);
            $user = auth()->user()?->master_data_user ?? 'system';

            $data = $request->except(['cover_image', 'softcopy_file', 'softcopy_type_id']);
            $data['updated_by'] = $user;

            // Handle perubahan stock_total
            if ($data['stock_total'] != $catalog->stock_total) {
                $this->updateStockItems($catalog, $data['stock_total']);
            }

            // Handle file uploads
            $this->handleFileUploads($catalog, $request, true);

            // Update slug jika title berubah
            if ($catalog->title !== $data['title']) {
                $data['slug'] = $this->generateUniqueSlug($data['title'], $catalog->id);
            }

            // Update catalog
            $catalog->update($data);

            // Update stock data
            $this->updateExistingStockData($catalog, $data);

            DB::commit();

            return redirect()->route('catalog.detail', [
                'id' => $catalog->id,
                'slug' => $catalog->slug
            ])->with('success', 'Katalog berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui katalog: ' . $e->getMessage()]);
        }
    }


    // CatalogData.php - Method delete()
    public function delete($id) // Terima langsung dari route parameter
    {
        // Hapus validasi request, gunakan langsung route parameter
        // $request->validate([
        //     'id' => 'required|exists:knowledge_item,id',
        // ]);

        DB::beginTransaction();

        try {
            $catalog = KnowledgeItemModel::findOrFail($id); // Gunakan $id langsung

            // Check if item has stocks
            if ($catalog->stocks()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus catalog item yang memiliki stock/koleksi!'
                ], 422);
            }

            // Delete cover file
            if ($catalog->cover_path) {
                $coverPath = storage_path('app/public/' . $catalog->cover_path);
                if (file_exists($coverPath)) {
                    unlink($coverPath);
                }
            }

            // Delete softcopy file
            if ($catalog->softcopy_path) {
                $softcopyPath = storage_path('app/public/' . $catalog->softcopy_path);
                if (file_exists($softcopyPath)) {
                    unlink($softcopyPath);

                    // Hapus folder juga jika kosong
                    $folderPath = dirname($softcopyPath);
                    if (is_dir($folderPath) && count(glob($folderPath . '/*')) === 0) {
                        rmdir($folderPath);
                    }
                }
            }

            $catalog->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Catalog item berhasil dihapus!'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Catalog item tidak ditemukan!'
            ], 404);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus catalog item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function detail(Request $request)
    {
        try {
            $id = $request->input('id');

            if (!$id) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID katalog diperlukan'
                ], 400);
            }

            // Load catalog dengan relationships
            $catalog = KnowledgeItemModel::with([
                'knowledgeType',
                'classification',
                'knowledgeSubject',
                'itemLocation',
                'fakultas',
                'prodi'
            ])->findOrFail($id);

            // Format catalog data
            $catalogArray = $catalog->toArray();
            $catalogArray['cover_url'] = $this->getCoverUrl($catalog->cover_path, $catalog->code);

            // PERBAIKAN: Load softcopy files secara manual karena relationship mungkin bermasalah
            $softcopyFiles = DB::table('knowledge_item_file as kif')
                ->join('upload_type as ut', 'kif.kif_upload_type_id', '=', 'ut.id')
                ->where('kif.kif_knowledge_item_id', $catalog->id)
                ->select(
                    'kif.kif_id as id',
                    'kif.kif_file as filename',
                    'kif.kif_datetime as upload_date',
                    'ut.id as upload_type_id',
                    'ut.title as upload_type_title',
                    'ut.extension as upload_type_extension'
                )
                ->orderBy('kif.kif_datetime', 'desc')
                ->get();

            // Format softcopy files
            $catalogArray['softcopy_files'] = $softcopyFiles->map(function ($file) use ($catalog) {
                $filePath = "book/{$catalog->code}/{$file->filename}";
                $fullPath = storage_path('app/public/' . $filePath);

                return [
                    'id' => $file->id,
                    'filename' => $file->filename,
                    'upload_type' => [
                        'id' => $file->upload_type_id,
                        'title' => $file->upload_type_title,
                        'extension' => $file->upload_type_extension
                    ],
                    'upload_date' => $file->upload_date,
                    'exists' => file_exists($fullPath),
                    'url' => $file->getUrl(),
                    'download_url' => route('catalog.download', [$catalog->code, $file->filename])
                ];
            });

            $catalogArray['uploaded_types'] = $softcopyFiles->pluck('upload_type_id')->toArray();

            // Stock summary
            $stockSummary = $this->calculateStockSummary($catalog->id);
            $catalogArray['stock_summary'] = $stockSummary;

            // Stock items
            $stockItems = $this->getStockItemsData($catalog->id);
            $catalogArray['stock_items'] = $stockItems;

            return response()->json([
                'success' => true,
                'data' => $catalogArray
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Katalog tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Catalog detail error: ' . $e->getMessage(), [
                'id' => $request->input('id'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data katalog'
            ], 500);
        }
    }
    // Helper method untuk calculate stock summary
    private function calculateStockSummary($catalogId)
    {
        $stockCounts = DB::table('knowledge_stock')
            ->where('knowledge_item_id', $catalogId)
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status IN (1, 6) THEN 1 ELSE 0 END) as available'),
                DB::raw('SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as borrowed'),
                DB::raw('SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) as damaged'),
                DB::raw('SUM(CASE WHEN status = 4 THEN 1 ELSE 0 END) as lost')
            )
            ->first();

        return [
            'total' => (int) ($stockCounts->total ?? 0),
            'available' => (int) ($stockCounts->available ?? 0),
            'borrowed' => (int) ($stockCounts->borrowed ?? 0),
            'damaged' => (int) ($stockCounts->damaged ?? 0),
            'lost' => (int) ($stockCounts->lost ?? 0)
        ];
    }
    // Helper method untuk get stock items data
    private function getStockItemsData($catalogId)
    {
        $statusLabels = [
            1 => 'Tersedia',
            2 => 'Dipinjam',
            3 => 'Rusak',
            4 => 'Hilang',
            5 => 'Expired',
            6 => 'Hilang diganti',
            7 => 'Diolah',
            8 => 'Cadangan',
            9 => 'Weeding'
        ];

        return DB::table('knowledge_stock')
            ->where('knowledge_item_id', $catalogId)
            ->orderBy('code')
            ->get(['id', 'code', 'status', 'rfid'])
            ->map(function ($stock) use ($statusLabels) {
                return [
                    'id' => $stock->id,
                    'code' => $stock->code,
                    'status' => $stock->status,
                    'status_label' => $statusLabels[$stock->status] ?? 'Unknown',
                    'rfid' => $stock->rfid
                ];
            })
            ->toArray();
    }

    public function previewSoftcopyFile($fileId)
    {
        try {
            $file = KnowledgeItemFileModel::with(['knowledgeItem', 'uploadType'])->findOrFail($fileId);

            if (!$file->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan di server'
                ], 404);
            }

            // Get file info
            $filePath = $file->getStoragePath();
            $fileInfo = [
                'exists' => true,
                'name' => $file->kif_file,
                'size' => filesize($filePath),
                'size_formatted' => $this->formatFileSize(filesize($filePath)),
                'extension' => pathinfo($file->kif_file, PATHINFO_EXTENSION),
                'mime_type' => mime_content_type($filePath),
                'url' => $file->getUrl(),
                // PERBAIKAN: Ganti route yang tidak ada
                'download_url' => route('catalog.download', [$file->knowledgeItem->code, $file->kif_file]),
                'upload_type' => $file->uploadType->title ?? 'Unknown',
                'upload_date' => $file->kif_datetime
            ];

            return response()->json([
                'success' => true,
                'data' => $fileInfo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get catalog softcopy files info (for AJAX calls)
     */
    public function getCatalogSoftcopyFiles($catalogId)
    {
        try {
            $catalog = KnowledgeItemModel::findOrFail($catalogId);
            $files = $this->getSoftcopyFiles($catalogId);

            return response()->json([
                'success' => true,
                'data' => [
                    'catalog_code' => $catalog->code,
                    'softcopy_files' => $files,
                    'uploaded_types' => $this->getUploadedTypes($catalogId)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get download statistics for softcopy files
     */
    private function getSoftcopyFilesWithStats($catalogId, $catalogCode)
    {
        // Get softcopy files dengan join ke download statistics
        $softcopyFiles = DB::table('knowledge_item_file as kif')
            ->join('upload_type as ut', 'kif.kif_upload_type_id', '=', 'ut.id')
            ->leftJoin('knowledge_item_file_download as kifd', function ($join) {
                $join->on('kif.kif_knowledge_item_id', '=', 'kifd.knowledge_item_id')
                    ->on('kif.kif_file', '=', 'kifd.name');
            })
            ->where('kif.kif_knowledge_item_id', $catalogId)
            ->select(
                'kif.kif_id as id',
                'kif.kif_file as filename',
                'kif.kif_datetime as upload_date',
                'ut.id as upload_type_id',
                'ut.title as upload_type_title',
                'ut.extension as upload_type_extension',
                DB::raw('COUNT(kifd.id) as download_count')
            )
            ->groupBy('kif.kif_id', 'kif.kif_file', 'kif.kif_datetime', 'ut.id', 'ut.title', 'ut.extension')
            ->orderBy('kif.kif_datetime', 'desc')
            ->get();

        return $softcopyFiles->map(function ($file) use ($catalogCode) {
            $filePath = "book/{$catalogCode}/{$file->filename}";
            $fullPath = storage_path('app/public/' . $filePath);

            return [
                'id' => $file->id,
                'filename' => $file->filename,
                'upload_type' => [
                    'id' => $file->upload_type_id,
                    'title' => $file->upload_type_title,
                    'extension' => $file->upload_type_extension
                ],
                'upload_date' => $file->upload_date,
                'upload_date_formatted' => \Carbon\Carbon::parse($file->upload_date)->format('d/m/Y'),
                'download_count' => (int) $file->download_count,
                'exists' => file_exists($fullPath),
                'url' => asset('storage/' . $filePath),
                // PERBAIKAN: Ganti route yang tidak ada
                'download_url' => route('catalog.download', [$catalogCode, $file->filename])
            ];
        })->toArray();
    }

    public function downloadSoftcopyFile($code, $filename)
    {
        try {
            // Find catalog by code
            $catalog = KnowledgeItemModel::where('code', $code)->firstOrFail();

            // Find file record
            $file = KnowledgeItemFileModel::where('kif_knowledge_item_id', $catalog->id)
                ->where('kif_file', $filename)
                ->with('uploadType')
                ->firstOrFail();

            // CRITICAL - Check download permission
            if (!$this->checkFilePermission($file, 'download')) {
                // Log unauthorized download attempt
                Log::warning('Unauthorized download attempt', [
                    'user_id' => auth()->id() ?? 'guest',
                    'catalog_id' => $catalog->id,
                    'file_name' => $filename,
                    'ip_address' => request()->ip()
                ]);

                abort(403, 'Anda tidak memiliki akses untuk mengunduh file ini');
            }

            // Build correct file path
            $filePath = storage_path('app/public/book/' . $code . '/' . $filename);

            if (!file_exists($filePath)) {
                abort(404, 'File tidak ditemukan di server');
            }

            // Log download to knowledge_item_file_download
            $this->logFileAccess($catalog->id, $file->kif_id, 'download', $filename);

            // Get file info and MIME type
            $mimeType = $this->getMimeType(pathinfo($filename, PATHINFO_EXTENSION));

            // Return download response with 'attachment' Content-Disposition to force download
            return response()->download($filePath, $filename, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);

        } catch (\Exception $e) {
            Log::error('Error downloading file: ' . $e->getMessage(), [
                'code' => $code,
                'filename' => $filename,
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Gagal mengunduh file');
        }
    }


    /**
     * CHECK FILE PERMISSION - Based on member type and upload type
     * @param object $file File object with upload type info
     * @param string $action 'read' or 'download'
     * @return boolean True if permitted, false otherwise
     */
    private function checkFilePermission($file, $action = 'read')
    {
        try {
            // Check if user is authenticated
            if (!auth()->check()) {
                return false;
            }

            $user = auth()->user();
            $memberTypeId = $user->member_type_id ?? null;

            if (!$memberTypeId || !$file->kif_upload_type_id) {
                return false;
            }

            if ($action === 'download') {
                // Check download permission
                return DB::table('member_type_upload_type')
                    ->where('member_type_id', $memberTypeId)
                    ->where('upload_type_id', $file->kif_upload_type_id)
                    ->exists();
            } else {
                // Check read permission (download OR readonly)
                $hasDownload = DB::table('member_type_upload_type')
                    ->where('member_type_id', $memberTypeId)
                    ->where('upload_type_id', $file->kif_upload_type_id)
                    ->exists();

                $hasReadonly = DB::table('member_type_upload_type_readonly')
                    ->where('member_type_id', $memberTypeId)
                    ->where('upload_type_id', $file->kif_upload_type_id)
                    ->exists();

                return $hasDownload || $hasReadonly;
            }

        } catch (\Exception $e) {
            Log::error('Error checking file permission: ' . $e->getMessage(), [
                'file_id' => $file->kif_id ?? 'unknown',
                'action' => $action,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * LOG FILE ACCESS - Track file access for analytics
     * @param int $catalogId Catalog ID
     * @param int $fileId File ID
     * @param string $action 'read' or 'download'
     * @param string $filename Filename
     */
    private function logFileAccess($catalogId, $fileId, $action, $filename = null)
    {
        try {
            if (!auth()->check()) {
                return; // No logging for unauthenticated users
            }

            $tableName = ($action === 'download') ? 'knowledge_item_file_download' : 'knowledge_item_file_readonly';

            DB::table($tableName)->insert([
                'knowledge_item_id' => $catalogId,
                'member_id' => auth()->id(),
                'name' => $filename ?? $fileId,
                'created_at' => now()
            ]);

            Log::info("File {$action} logged successfully", [
                'catalog_id' => $catalogId,
                'file_id' => $fileId,
                'user_id' => auth()->id(),
                'action' => $action
            ]);

        } catch (\Exception $e) {
            Log::warning('Failed to log file access: ' . $e->getMessage(), [
                'catalog_id' => $catalogId,
                'file_id' => $fileId,
                'action' => $action,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * GET MIME TYPE - Enhanced version
     */
    private function getMimeType($extension)
    {
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt' => 'text/plain',
            'epub' => 'application/epub+zip',
            'flipbook' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png'
        ];

        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }
    public function logAccess(Request $request)
    {
        try {
            $request->validate([
                'catalog_id' => 'required|integer',
                'action' => 'required|string'
            ]);

            if (!auth()->check()) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }

            // Log the action
            Log::info("User frontend access log", [
                'user_id' => auth()->id(),
                'catalog_id' => $request->catalog_id,
                'action' => $request->action,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error logging access: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function deleteSoftcopyFile($fileId)
    {
        try {
            $file = KnowledgeItemFileModel::with('knowledgeItem')->findOrFail($fileId);

            $filePath = $file->getStoragePath();

            // Delete physical file
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete database record
            $file->delete();

            // Check if no more softcopy files exist for this catalog
            $remainingFiles = KnowledgeItemFileModel::where('kif_knowledge_item_id', $file->kif_knowledge_item_id)->count();
            if ($remainingFiles === 0) {
                // Clear softcopy_path in knowledge_item
                $file->knowledgeItem->update(['softcopy_path' => null]);
            }

            return response()->json([
                'success' => true,
                'message' => 'File berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique slug
     */
    private function generateUniqueSlug($title, $excludeId = null)
    {
        $slug = Str::slug($title);
        $query = KnowledgeItemModel::where('slug', 'like', $slug . '%');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $count = $query->count();

        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }

        return $slug;
    }
    // Helper method to get dropdown options
    public function getFormOptions()
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 60);

        try {
            // Optimize query - jangan ambil semua data sekaligus
            $knowledgeTypes = KnowledgeTypeModel::where('active', 1)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            $classifications = ClassificationCodeModel::select('id', 'code', 'name', 'description')
                ->orderBy('code')
                ->get();

            $subjects = KnowledgeSubjectModel::where('active', 1)->orderBy('name')->get();
            $locations = ItemLocation::orderBy('name')->get();

            // TAMBAH: Ambil data fakultas dari t_mst_fakultas
            $fakultas = FakultasModel::active()
                ->orderBy('NAMA_FAKULTAS')
                ->get(['C_KODE_FAKULTAS', 'NAMA_FAKULTAS', 'SINGKATAN']);

            $prodi = ProdiModel::active()
                ->with('fakultas')
                ->orderBy('NAMA_PRODI')
                ->get(['C_KODE_PRODI', 'C_KODE_FAKULTAS', 'NAMA_PRODI']);

            $uploadTypes = DB::table('upload_type')
                ->select('id', 'name', 'extension', 'title')
                ->orderBy('title')
                ->get();

            // DEBUG: Log untuk melihat data classifications

            return response()->json([
                'success' => true,
                'data' => [
                    'knowledge_types' => $knowledgeTypes,
                    'classifications' => $classifications,
                    'subjects' => $subjects,
                    'locations' => $locations,
                    'fakultas' => $fakultas,
                    'prodi' => $prodi,
                    'upload_types' => $uploadTypes
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stock summary
     */
    private function getStockSummary($catalogId)
    {
        $total = DB::table('knowledge_stock')->where('knowledge_item_id', $catalogId)->count();
        $available = DB::table('knowledge_stock')->where('knowledge_item_id', $catalogId)->whereIn('status', [1, 6])->count();
        $borrowed = DB::table('knowledge_stock')->where('knowledge_item_id', $catalogId)->where('status', 2)->count();
        $damaged = DB::table('knowledge_stock')->where('knowledge_item_id', $catalogId)->where('status', 3)->count();
        $lost = DB::table('knowledge_stock')->where('knowledge_item_id', $catalogId)->where('status', 4)->count();

        return compact('total', 'available', 'borrowed', 'damaged', 'lost');
    }
    /**
     * Get stock items
     */
    private function getStockItems($catalogId)
    {
        $statusLabels = [
            1 => 'Tersedia',
            2 => 'Dipinjam',
            3 => 'Rusak',
            4 => 'Hilang',
            5 => 'Expired',
            6 => 'Hilang diganti',
            7 => 'Diolah',
            8 => 'Cadangan',
            9 => 'Weeding'
        ];

        return DB::table('knowledge_stock')
            ->where('knowledge_item_id', $catalogId)
            ->orderBy('code')
            ->get(['id', 'code', 'status', 'rfid'])
            ->map(function ($stock) use ($statusLabels) {
                return [
                    'id' => $stock->id,
                    'code' => $stock->code,
                    'status' => $stock->status,
                    'status_label' => $statusLabels[$stock->status] ?? 'Unknown',
                    'rfid' => $stock->rfid
                ];
            });
    }

    /**
     * Delete file helper
     */
    private function deleteFile($path)
    {
        $fullPath = storage_path("app/public/{$path}");
        if (file_exists($fullPath)) {
            unlink($fullPath);
            return true;
        }
        return false;
    }

    /**
     * Clean up files on error
     */
    private function cleanupFiles($catalogCode)
    {
        if ($catalogCode) {
            $dirPath = storage_path("app/public/book/{$catalogCode}");
            if (is_dir($dirPath)) {
                array_map('unlink', glob("{$dirPath}/*"));
                rmdir($dirPath);
            }
        }
    }

    /**
     * Generate kode catalog otomatis dengan format: YY.TT.III (base code untuk catalog)
     * Dan YY.TT.III-C untuk setiap stock item
     */
    private function generateCatalogCode($knowledgeTypeId, $stockTotal)
    {
        $year = date('y'); // 25 untuk tahun 2025
        $typeId = str_pad($knowledgeTypeId, 2, '0', STR_PAD_LEFT); // 01, 02, dst

        // Hitung jumlah CATALOG ITEM yang sudah ada di tahun ini
        $currentYear = date('Y');
        $catalogCount = KnowledgeItemModel::whereYear('created_at', $currentYear)->count();

        // Increment dimulai dari jumlah yang sudah ada + 1
        $increment = str_pad($catalogCount + 1, 3, '0', STR_PAD_LEFT); // 001, 002, dst

        $baseCode = "{$year}.{$typeId}.{$increment}"; // 25.01.001

        // Generate codes untuk setiap eksemplar stock
        $stockCodes = [];
        for ($i = 1; $i <= $stockTotal; $i++) {
            $stockCodes[] = "{$baseCode}-{$i}"; // 25.01.001-1, 25.01.001-2, dst
        }

        return [
            'catalog_code' => $baseCode,    // Kode untuk knowledge_item: 25.01.001
            'stock_codes' => $stockCodes    // Array kode untuk knowledge_stock: 25.01.001-1, 25.01.001-2, dst
        ];
    }

    // Method untuk create stock items
    private function createStockItems($catalog, $stockCodes, $request)
    {
        foreach ($stockCodes as $stockCode) {
            DB::table('knowledge_stock')->insert([
                'knowledge_item_id' => $catalog->id,
                'knowledge_type_id' => $catalog->knowledge_type_id,
                'item_location_id' => $catalog->item_location_id,
                'code' => $stockCode,
                'faculty_code' => $catalog->faculty_code,
                'course_code' => $catalog->course_code,
                'origination' => $catalog->origination,
                'supplier' => $catalog->supplier,
                'price' => $catalog->price,
                'entrance_date' => $catalog->entrance_date,
                'status' => 1, // Default: Tersedia (langsung angka 1)
                'created_by' => $catalog->created_by,
                'created_at' => now(),
                'updated_by' => $catalog->updated_by,
                'updated_at' => now()
            ]);
        }

        // Update cache di knowledge_item - LANGSUNG SIMPEL
        $total = count($stockCodes);
        $available = $total; // Semua baru = tersedia (status 1)

        $catalog->update([
            'stock_total' => $total,
            'stock_available' => $available
        ]);
    }


    /**
     * Update getCoverUrl method untuk path baru
     */
    private function getCoverUrl($coverPath, $catalogCode = null)
    {
        if (empty($coverPath)) {
            return asset('assets/img/default-book-cover.jpg');
        }

        // Clean the path
        $coverPath = trim($coverPath);

        // Check if it's already a full URL
        if (filter_var($coverPath, FILTER_VALIDATE_URL)) {
            return $coverPath;
        }

        // Extract filename only
        $filename = basename($coverPath);

        // Check in local uploads/book/cover/ folder
        $localPath = "uploads/book/cover/{$filename}";
        $localFullPath = storage_path("app/public/{$localPath}");

        if (file_exists($localFullPath)) {
            return asset("storage/{$localPath}");
        }

        // Fallback to remote Telkom URL
        $remoteUrl = "https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/{$filename}";
        return $remoteUrl;
    }
    private function getSoftcopyFiles($catalogId)
    {
        return KnowledgeItemFileModel::with('uploadType')
            ->where('kif_knowledge_item_id', $catalogId)
            ->orderBy('kif_datetime', 'desc')
            ->get()
            ->map(function ($file) {
                $catalog = $file->knowledgeItem;

                return [
                    'id' => $file->kif_id,
                    'filename' => $file->kif_file,
                    'upload_type' => $file->uploadType,
                    'upload_date' => $file->kif_datetime,
                    'exists' => $file->exists(),
                    'url' => $file->getUrl(),
                    // PERBAIKAN: Ganti route yang tidak ada
                    'download_url' => route('catalog.download', [$catalog->code, $file->kif_file])
                ];
            });
    }
    public function getUploadedTypes($catalogId)
    {
        return KnowledgeItemFileModel::where('kif_knowledge_item_id', $catalogId)
            ->pluck('kif_upload_type_id')
            ->toArray();
    }

    /**
     * Update getSoftcopyUrl method untuk path baru  
     */
    private function getSoftcopyUrl($softcopyPath)
    {
        if (empty($softcopyPath)) {
            return null;
        }

        $localPath = storage_path('app/public/' . $softcopyPath);

        if (file_exists($localPath)) {
            return asset('storage/' . $softcopyPath);
        }

        return null;
    }

    public function addClassification(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:classification_code,code',
            'name' => 'required|string|max:255',
        ]);

        try {
            $user = auth()->user()?->master_data_user ?? 'system';

            $classification = ClassificationCodeModel::create([
                'code' => $request->code,
                'name' => $request->name,
                'tree_left' => 0,
                'tree_right' => 0,
                'tree_level' => 0,
                'description' => null,
                'created_by' => $user,
                'updated_by' => $user,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Klasifikasi berhasil ditambahkan',
                'data' => $classification
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan klasifikasi: ' . $e->getMessage()
            ], 500);
        }
    }

    // CatalogData.php
    // public function addKnowledgeTypeForm(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|min:3|max:255|unique:knowledge_type,name',
    //     ]);

    //     try {
    //         $user = auth()->user()?->master_data_user ?? 'system';

    //         $type = KnowledgeTypeModel::create([
    //             'name' => trim($request->name),
    //             'active' => 1,
    //             'type' => 1,
    //             'rentable' => 1,
    //             'created_by' => $user,
    //             'updated_by' => $user,
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);

    //         return redirect()->back()->with([
    //             'success' => true,
    //             'message' => 'Jenis katalog berhasil ditambahkan',
    //             'new_type' => $type
    //         ]);

    //     } catch (\Exception $e) {
    //         return redirect()->back()->with([
    //             'success' => false,
    //             'message' => 'Gagal menambahkan jenis katalog: ' . $e->getMessage()
    //         ])->withInput();
    //     }
    // }
    // HAPUS method addKnowledgeType yang lama, ganti dengan ini:
    public function addKnowledgeType(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|min:3|max:255|unique:knowledge_type,name',
                'rentable' => 'nullable|boolean'
            ], [
                'name.required' => 'Nama jenis koleksi wajib diisi',
                'name.min' => 'Nama jenis koleksi minimal 3 karakter',
                'name.max' => 'Nama jenis koleksi maksimal 255 karakter',
                'name.unique' => 'Nama jenis koleksi sudah ada',
                'rentable.boolean' => 'Status rentable harus berupa boolean'
            ]);

            DB::beginTransaction();

            $user = auth()->user()?->master_data_user ?? 'system';

            $type = KnowledgeTypeModel::create([
                'name' => trim($request->name),
                'active' => 1,
                'type' => 1,
                'rentable' => $request->rentable ? 1 : 0, // Convert boolean to integer
                'created_by' => $user,
                'updated_by' => $user,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Jenis katalog berhasil ditambahkan',
                'data' => [
                    'id' => $type->id,
                    'name' => $type->name,
                    'rentable' => $type->rentable
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Add Knowledge Type Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan jenis katalog: ' . $e->getMessage()
            ], 500);
        }
    }


    public function addSubject(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $user = auth()->user()?->master_data_user ?? 'system';

            $subject = KnowledgeSubjectModel::create([
                'name' => $request->name,
                'active' => 1,
                'created_by' => $user,
                'updated_by' => $user,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subjek berhasil ditambahkan',
                'data' => $subject
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan subjek: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Update stock items ketika stock_total berubah di edit
     */
    private function updateStockItems($catalog, $newStockTotal)
    {
        $currentStockCount = $catalog->stocks()->count();
        $baseCode = $catalog->code;

        if ($newStockTotal > $currentStockCount) {
            // Tambah stock items baru
            for ($i = $currentStockCount + 1; $i <= $newStockTotal; $i++) {
                $stockCode = "{$baseCode}-{$i}";

                DB::table('knowledge_stock')->insert([
                    'knowledge_item_id' => $catalog->id,
                    'knowledge_type_id' => $catalog->knowledge_type_id,
                    'item_location_id' => $catalog->item_location_id,
                    'code' => $stockCode,
                    'faculty_code' => $catalog->faculty_code,
                    'course_code' => $catalog->course_code,
                    'origination' => $catalog->origination,
                    'supplier' => $catalog->supplier,
                    'price' => $catalog->price,
                    'entrance_date' => $catalog->entrance_date,
                    'status' => KnowledgeStockModel::STATUS_AVAILABLE, // Default: Tersedia
                    'created_by' => $catalog->updated_by,
                    'created_at' => now(),
                    'updated_by' => $catalog->updated_by,
                    'updated_at' => now()
                ]);
            }

        } elseif ($newStockTotal < $currentStockCount) {
            // Hapus stock items yang berlebih (prioritas: available dan nomor terbesar)
            $stocksToDelete = $currentStockCount - $newStockTotal;

            $stocksToRemove = $catalog->stocks()
                ->orderByRaw('CASE WHEN status = ? THEN 0 ELSE 1 END', [KnowledgeStockModel::STATUS_AVAILABLE])
                ->orderBy('code', 'desc')
                ->limit($stocksToDelete)
                ->get();

            foreach ($stocksToRemove as $stock) {
                // Cek apakah stock sedang dipinjam
                if ($stock->status == KnowledgeStockModel::STATUS_BORROWED) {
                    throw new \Exception("Tidak dapat mengurangi stock karena eksemplar {$stock->code} sedang dipinjam");
                }
            }

            // Hapus stock yang dipilih
            $stockIds = $stocksToRemove->pluck('id')->toArray();
            DB::table('knowledge_stock')->whereIn('id', $stockIds)->delete();
        }

        // Update stock counts di knowledge_item setelah update
        $catalog->updateStockCounts();
    }

    /**
     * Update data di knowledge_stock yang sudah ada ketika data catalog berubah
     */
    private function updateExistingStockData($catalog, $updatedData)
    {
        // Field yang perlu disinkronkan ke knowledge_stock
        $fieldsToSync = [
            'knowledge_type_id',
            'item_location_id',
            'faculty_code',
            'course_code',
            'origination',
            'supplier',
            'price',
            'entrance_date'
        ];

        $stockUpdateData = [];
        foreach ($fieldsToSync as $field) {
            if (isset($updatedData[$field])) {
                $stockUpdateData[$field] = $updatedData[$field];
            }
        }

        // Tambahkan updated_by dan updated_at
        $stockUpdateData['updated_by'] = $catalog->updated_by;
        $stockUpdateData['updated_at'] = now();

        if (!empty($stockUpdateData)) {
            DB::table('knowledge_stock')
                ->where('knowledge_item_id', $catalog->id)
                ->update($stockUpdateData);

            Log::info("Updated existing stock data for catalog {$catalog->code}");
        }
    }




    /**
     * Format file size helper
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    // Modified handleFileUploads method
    private function handleFileUploads($catalog, $request, $isUpdate = false)
    {
        $catalogCode = $catalog->code;

        // Handle cover upload with better validation
        if ($request->hasFile('cover_image')) {
            try {
                $file = $request->file('cover_image');

                // Additional validation
                if (!$file->isValid()) {
                    throw new \Exception('File cover tidak valid');
                }

                // Validate if it's actually an image
                $imageInfo = @getimagesize($file->getPathname());
                if ($imageInfo === false) {
                    throw new \Exception('File bukan gambar yang valid');
                }

                // Check file size
                $fileSizeKB = $file->getSize() / 1024;
                if ($fileSizeKB > 2048) { // 2MB
                    throw new \Exception('Ukuran cover terlalu besar. Maksimal 2MB');
                }

                // Validate MIME type
                $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    throw new \Exception('Format cover tidak diizinkan. Hanya JPG, PNG, GIF');
                }

                // Delete old cover if updating
                if ($isUpdate && $catalog->cover_path) {
                    $this->deleteFile($catalog->cover_path);
                }

                // Create directory
                $dirPath = "uploads/book/cover";
                $fullPath = storage_path("app/public/{$dirPath}");

                if (!file_exists($fullPath)) {
                    if (!mkdir($fullPath, 0755, true)) {
                        throw new \Exception('Gagal membuat direktori untuk cover');
                    }
                }

                // Generate filename
                $extension = $file->getClientOriginalExtension();
                $fileName = "{$catalogCode}.{$extension}";

                // Move file
                if (!$file->move($fullPath, $fileName)) {
                    throw new \Exception('Gagal menyimpan file cover');
                }

                // Save path to database
                $catalog->cover_path = "{$dirPath}/{$fileName}";

            } catch (\Exception $e) {
                Log::error('Cover upload failed: ' . $e->getMessage());
                throw new \Exception('Gagal upload cover: ' . $e->getMessage());
            }
        }

        // Handle multiple softcopy uploads - keep as is
        if ($request->has('softcopy_data')) {
            $softcopyData = json_decode($request->input('softcopy_data'), true);

            if (is_array($softcopyData)) {
                foreach ($softcopyData as $data) {
                    $fileInputName = "softcopy_file_{$data['index']}";

                    if ($request->hasFile($fileInputName)) {
                        try {
                            $this->handleSoftcopyUpload(
                                $catalog,
                                $request->file($fileInputName),
                                $data['upload_type_id'],
                                $isUpdate
                            );
                        } catch (\Exception $e) {
                            Log::error("Softcopy upload failed for {$fileInputName}: " . $e->getMessage());
                            throw new \Exception("Gagal upload {$fileInputName}: " . $e->getMessage());
                        }
                    }
                }
            }
        }

        // Save changes
        if (!$isUpdate) {
            $catalog->save();
        }
    }

    // Method baru untuk handle single softcopy upload
    private function handleSoftcopyUpload($catalog, $file, $uploadTypeId, $isUpdate = false)
    {
        $uploadType = DB::table('upload_type')
            ->select('id', 'name', 'extension', 'title')
            ->where('id', $uploadTypeId)
            ->first();

        if (!$uploadType) {
            return false;
        }

        $catalogCode = $catalog->code;
        $fileExtension = strtolower($file->getClientOriginalExtension());
        $expectedExtension = strtolower($uploadType->extension);

        if ($fileExtension === $expectedExtension) {
            try {
                // Check if this upload type already exists for this catalog
                $existingFile = KnowledgeItemFileModel::where('kif_knowledge_item_id', $catalog->id)
                    ->where('kif_upload_type_id', $uploadTypeId)
                    ->first();

                if ($existingFile && !$isUpdate) {
                    throw new \Exception("File dengan tipe {$uploadType->title} sudah ada");
                }

                // Delete existing file if updating
                if ($existingFile && $isUpdate) {
                    $oldFilePath = storage_path("app/public/book/{$catalogCode}/{$existingFile->kif_file}");
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                    $existingFile->delete();
                }

                // New path: book/kodekatalog/kodekatalog_namafile.extension
                $fileName = "{$catalogCode}_{$uploadType->name}.{$fileExtension}";
                $dirPath = "book/{$catalogCode}";
                $fullPath = storage_path("app/public/{$dirPath}");

                // Create directory if not exists
                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0755, true);
                }

                // Move file
                $file->move($fullPath, $fileName);

                // Save to knowledge_item_file table
                KnowledgeItemFileModel::create([
                    'kif_knowledge_item_id' => $catalog->id,
                    'kif_file' => $fileName,
                    'kif_upload_type_id' => $uploadTypeId,
                    'kif_datetime' => now()
                ]);

                // Update softcopy_path in knowledge_item (hanya kode katalog)
                $catalog->softcopy_path = $catalogCode;

                return true;

            } catch (\Exception $e) {
                Log::warning('Failed to upload softcopy: ' . $e->getMessage());
                throw $e;
            }
        } else {
            throw new \Exception("File harus berformat .{$expectedExtension}");
        }
    }

    /**
     * Helper method untuk validate file upload
     */
    private function validateFileUpload($file, $allowedExtensions, $maxSize = 10240) // 10MB default
    {
        if (!$file || !$file->isValid()) {
            throw new \Exception('File upload tidak valid');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception('Format file tidak diizinkan. Hanya: ' . implode(', ', $allowedExtensions));
        }

        $sizeKB = $file->getSize() / 1024;
        if ($sizeKB > $maxSize) {
            throw new \Exception('Ukuran file terlalu besar. Maksimal: ' . number_format($maxSize / 1024, 1) . 'MB');
        }

        return true;
    }

    /**
     * Helper method untuk create catalog directory
     */
    private function createCatalogDirectory($catalogCode)
    {
        $dirPath = storage_path("app/public/book/{$catalogCode}");

        if (!file_exists($dirPath)) {
            if (!mkdir($dirPath, 0755, true)) {
                throw new \Exception('Gagal membuat direktori untuk katalog');
            }
        }

        return $dirPath;
    }

    /**
     * Helper method untuk cleanup catalog directory
     */
    private function cleanupCatalogDirectory($catalogCode)
    {
        $dirPath = storage_path("app/public/book/{$catalogCode}");

        if (is_dir($dirPath)) {
            // Delete all files in directory
            $files = glob($dirPath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            // Delete directory if empty
            if (count(glob($dirPath . '/*')) === 0) {
                rmdir($dirPath);
            }
        }
    }

    /**
     * Helper method untuk get file MIME type safely
     */
    private function getFileMimeType($filePath)
    {
        if (!file_exists($filePath)) {
            return 'application/octet-stream';
        }

        $mimeType = mime_content_type($filePath);
        return $mimeType ?: 'application/octet-stream';
    }

    private function validateSoftcopyFiles(Request $request)
    {
        $softcopyData = $request->input('softcopy_data');

        if (!$softcopyData) {
            return; // No softcopy files to validate
        }

        try {
            $softcopyItems = json_decode($softcopyData, true);

            if (!is_array($softcopyItems)) {
                throw new \Exception('Data softcopy tidak valid');
            }

            foreach ($softcopyItems as $item) {
                $index = $item['index'] ?? null;
                $uploadTypeId = $item['upload_type_id'] ?? null;

                if (!$uploadTypeId) {
                    throw new \Exception('Jenis softcopy wajib dipilih');
                }

                // Check if file exists
                $fileInputName = "softcopy_file_{$index}";
                if (!$request->hasFile($fileInputName)) {
                    throw new \Exception("File softcopy untuk jenis {$uploadTypeId} tidak ditemukan");
                }

                $file = $request->file($fileInputName);
                if (!$file->isValid()) {
                    throw new \Exception("File softcopy tidak valid");
                }

                // Get upload type info
                $uploadType = DB::table('upload_type')
                    ->where('id', $uploadTypeId)
                    ->first();

                if (!$uploadType) {
                    throw new \Exception("Jenis upload tidak valid");
                }

                // Validate file extension
                $fileExtension = strtolower($file->getClientOriginalExtension());
                $expectedExtension = strtolower($uploadType->extension);

                if ($fileExtension !== $expectedExtension) {
                    throw new \Exception("File harus berformat .{$expectedExtension} untuk jenis {$uploadType->title}");
                }

                // Validate file size (max 10MB)
                $fileSizeKB = $file->getSize() / 1024;
                if ($fileSizeKB > 10240) { // 10MB
                    throw new \Exception("Ukuran file {$uploadType->title} terlalu besar. Maksimal 10MB");
                }
            }

        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'softcopy_files' => [$e->getMessage()]
            ]);
        }
    }
    /**
     * FLIPBOOK VIEWER - Secure access to flipbook files
     */
    public function flipbookViewer($id, $slug = null, $fileId)
    {
        try {
            // Validate catalog exists
            $catalog = KnowledgeItemModel::findOrFail($id);

            // Validate slug if provided
            if ($slug && $slug !== \Illuminate\Support\Str::slug($catalog->title)) {
                return redirect()->route('catalog.detail', [
                    'id' => $id,
                    'slug' => \Illuminate\Support\Str::slug($catalog->title)
                ]);
            }

            // Get file record
            $file = KnowledgeItemFileModel::where('kif_id', $fileId)
                ->where('kif_knowledge_item_id', $id)
                ->with('uploadType')
                ->firstOrFail();

            // Check member permissions
            if (!$this->checkFilePermission($file, 'read')) {
                // Log unauthorized access attempt
                Log::warning('Unauthorized flipbook access attempt', [
                    'user_id' => auth()->id() ?? 'guest',
                    'catalog_id' => $id,
                    'file_id' => $fileId
                ]);

                // Return error page with redirect
                return view('catalog.flipbook.unauthorized', [
                    'redirectUrl' => route('catalog.detail', [
                        'id' => $id,
                        'slug' => \Illuminate\Support\Str::slug($catalog->title)
                    ])
                ]);
            }

            // Validate file is flipbook compatible
            $allowedExtensions = ['pdf', 'epub', 'flipbook'];
            $fileExtension = strtolower(pathinfo($file->kif_file, PATHINFO_EXTENSION));

            if (!in_array($fileExtension, $allowedExtensions)) {
                return redirect()->route('catalog.detail', [
                    'id' => $id,
                    'slug' => \Illuminate\Support\Str::slug($catalog->title)
                ])->with('error', 'File ini tidak mendukung tampilan flipbook');
            }

            // Check if file exists on disk
            $filePath = storage_path('app/public/book/' . $catalog->code . '/' . $file->kif_file);
            if (!file_exists($filePath)) {
                return redirect()->route('catalog.detail', [
                    'id' => $id,
                    'slug' => \Illuminate\Support\Str::slug($catalog->title)
                ])->with('error', 'File tidak ditemukan di server');
            }

            // Check download permission
            $canDownload = $this->checkFilePermission($file, 'download');

            // Log access to readonly table
            $this->logFileAccess($catalog->id, $fileId, 'read', $file->kif_file);

            // ENHANCED: Generate time-limited encrypted token for file access
            $encryptedToken = $this->generateSecureFileToken($id, $fileId);

            return view('catalog.flipbook.index', [
                'catalog' => $catalog,
                'file' => $file,
                'canDownload' => $canDownload,
                'fileUrl' => route('catalog.flipbook-file-secure', ['token' => $encryptedToken]),
                'securityToken' => md5(uniqid() . time() . $id . $fileId)
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading flipbook viewer: ' . $e->getMessage(), [
                'id' => $id,
                'fileId' => $fileId,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('catalog.detail', ['id' => $id])
                ->with('error', 'Gagal memuat flipbook: ' . $e->getMessage());
        }
    }

    /**
     * NEW: Generate secure, time-limited encrypted token for file access
     */
    private function generateSecureFileToken($catalogId, $fileId)
    {
        // Create data array with catalog ID, file ID and expiration timestamp
        $data = [
            'catalog_id' => $catalogId,
            'file_id' => $fileId,
            'expires' => time() + 3600, // Token expires in 1 hour
            'user_id' => auth()->id() ?? 'guest' // Bind token to specific user
        ];

        // Encrypt the data using Laravel's encryption
        return encrypt($data);
    }
    /**
     * NEW: Secure file streaming with encrypted token
     */
    public function getFlipbookFileSecure(Request $request)
    {
        try {
            // Get and validate token
            $token = $request->input('token');
            if (!$token) {
                abort(403, 'Akses ditolak - Token tidak valid');
            }

            try {
                // Decrypt token
                $data = decrypt($token);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                Log::warning('Invalid flipbook token', [
                    'token' => $token,
                    'ip' => $request->ip()
                ]);
                abort(403, 'Akses ditolak - Token tidak valid');
            }

            // Check token expiration
            if (!isset($data['expires']) || $data['expires'] < time()) {
                Log::warning('Expired flipbook token', [
                    'token_data' => $data,
                    'ip' => $request->ip()
                ]);
                abort(403, 'Akses ditolak - Token kedaluwarsa');
            }

            // Check user binding
            $currentUser = auth()->id() ?? 'guest';
            if ($data['user_id'] !== $currentUser) {
                Log::warning('Token user mismatch', [
                    'token_user' => $data['user_id'],
                    'current_user' => $currentUser,
                    'ip' => $request->ip()
                ]);
                abort(403, 'Akses ditolak - Token tidak valid untuk pengguna ini');
            }

            // Extract catalog and file IDs
            $catalogId = $data['catalog_id'];
            $fileId = $data['file_id'];

            // Validate catalog and file
            $catalog = KnowledgeItemModel::findOrFail($catalogId);
            $file = KnowledgeItemFileModel::where('kif_id', $fileId)
                ->where('kif_knowledge_item_id', $catalogId)
                ->with('uploadType')
                ->firstOrFail();

            // Check permissions - MUST HAVE READ ACCESS
            if (!$this->checkFilePermission($file, 'read')) {
                Log::warning('Unauthorized flipbook file access attempt', [
                    'user_id' => $currentUser,
                    'catalog_id' => $catalogId,
                    'file_id' => $fileId,
                    'ip' => $request->ip()
                ]);
                abort(403, 'Akses ditolak');
            }

            // Get file path
            $filePath = storage_path('app/public/book/' . $catalog->code . '/' . $file->kif_file);

            if (!file_exists($filePath)) {
                abort(404, 'File tidak ditemukan');
            }

            // Log file view (only log once per session to avoid excessive logging)
            $sessionKey = "flipbook_view_{$catalogId}_{$fileId}";
            if (!session()->has($sessionKey)) {
                $this->logFileAccess($catalog->id, $fileId, 'read', $file->kif_file);
                session()->put($sessionKey, true);
            }

            // Determine file type and appropriate handling
            $fileExtension = strtolower(pathinfo($file->kif_file, PATHINFO_EXTENSION));
            $mimeType = $this->getMimeType($fileExtension);

            // Set headers for secure display
            $headers = [
                'Content-Type' => $mimeType,
                'X-Frame-Options' => 'SAMEORIGIN',
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ];

            // IMPORTANT: If we're dealing with PDF and user has no download permission,
            // we need to modify the PDF to disable download/save functionality
            if ($fileExtension === 'pdf' && !$this->checkFilePermission($file, 'download')) {
                // Option 1: Stream PDF with Content-Disposition to discourage saving
                $headers['Content-Disposition'] = 'inline; filename="view_only_' . $file->kif_file . '"';

                // Option 2 (better): Apply watermark or secure viewing technique
                // This requires PDF manipulation libraries like FPDI/FPDF or setasign/fpdi
                // For simplicity, we'll stick with option 1 in this example

                return response()->file($filePath, $headers);
            }

            // For other file types or when download is permitted
            $headers['Content-Disposition'] = 'inline; filename="' . $file->kif_file . '"';
            return response()->file($filePath, $headers);

        } catch (\Exception $e) {
            Log::error('Error streaming flipbook file: ' . $e->getMessage(), [
                'token' => $request->input('token'),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Gagal memuat file');
        }
    }
    /**
     * Report security violations for flipbook
     */
    public function reportSecurityViolation(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string',
                'catalog_id' => 'required|numeric',
                'file_id' => 'required|numeric',
                'reason' => 'required|string'
            ]);

            // Log violation
            Log::warning('Flipbook security violation', [
                'token' => $request->token,
                'catalog_id' => $request->catalog_id,
                'file_id' => $request->file_id,
                'reason' => $request->reason,
                'user_id' => auth()->id() ?? 'guest',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error logging security violation: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * GET FLIPBOOK FILE - Secure file streaming for flipbook
     */
    // public function getFlipbookFile($id, $fileId)
    // {
    //     try {
    //         // Validate catalog and file
    //         $catalog = KnowledgeItemModel::findOrFail($id);
    //         $file = KnowledgeItemFileModel::where('kif_id', $fileId)
    //             ->where('kif_knowledge_item_id', $id)
    //             ->with('uploadType')
    //             ->firstOrFail();

    //         // Check permissions - MUST HAVE READ ACCESS
    //         if (!$this->checkFilePermission($file, 'read')) {
    //             Log::warning('Unauthorized flipbook file access attempt', [
    //                 'user_id' => auth()->id() ?? 'guest',
    //                 'catalog_id' => $id,
    //                 'file_id' => $fileId
    //             ]);
    //             abort(403, 'Akses ditolak');
    //         }

    //         // Get file path
    //         $filePath = storage_path('app/public/book/' . $catalog->code . '/' . $file->kif_file);

    //         if (!file_exists($filePath)) {
    //             abort(404, 'File tidak ditemukan');
    //         }

    //         // Log file view (only log once per session to avoid excessive logging)
    //         $sessionKey = "flipbook_view_{$id}_{$fileId}";
    //         if (!session()->has($sessionKey)) {
    //             $this->logFileAccess($catalog->id, $fileId, 'read', $file->kif_file);
    //             session()->put($sessionKey, true);
    //         }

    //         // IMPORTANT: Determine if we should stream or force download based on file type
    //         $fileExtension = pathinfo($file->kif_file, PATHINFO_EXTENSION);
    //         $mimeType = $this->getMimeType($fileExtension);

    //         // Set secure headers - ENHANCED SECURITY
    //         $headers = [
    //             'Content-Type' => $mimeType,
    //             'X-Frame-Options' => 'SAMEORIGIN',
    //             'X-Content-Type-Options' => 'nosniff',
    //             'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
    //             'Pragma' => 'no-cache',
    //             'Expires' => '0'
    //         ];

    //         // CRITICAL: Add Content-Disposition header to prevent download
    //         // Use 'inline' disposition with filename to improve security
    //         $headers['Content-Disposition'] = 'inline; filename="view_' . $file->kif_file . '"';

    //         // IMPORTANT: For non-PDF/non-viewable files, ensure we reject access
    //         $viewableExtensions = ['pdf', 'epub', 'html', 'htm', 'txt'];
    //         if (!in_array(strtolower($fileExtension), $viewableExtensions)) {
    //             // Non-viewable files should be protected
    //             if (!$this->checkFilePermission($file, 'download')) {
    //                 abort(403, 'File type requires download permission');
    //             }
    //         }

    //         return response()->file($filePath, $headers);

    //     } catch (\Exception $e) {
    //         Log::error('Error streaming flipbook file: ' . $e->getMessage(), [
    //             'id' => $id,
    //             'fileId' => $fileId,
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //         abort(500, 'Gagal memuat file');
    //     }
    // }


}