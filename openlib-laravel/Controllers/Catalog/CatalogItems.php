<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KnowledgeStockModel;
use App\Models\KnowledgeItemModel;
use App\Models\KnowledgeTypeModel;
use App\Models\ItemLocation;
use App\Models\FakultasModel;
use App\Models\ProdiModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\KnowledgeItemFileModel;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;


class CatalogItems extends Controller
{
    /**
     * Display stock items management page
     */
    public function index()
    {
        try {
            return view('catalog.items.index');
        } catch (\Exception $e) {
            Log::error('Error loading catalog items page: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat halaman manajemen eksemplar');
        }
    }

    /**
     * Show add stock item page
     */
    public function add()
    {
        try {
            return view('catalog.items.add');
        } catch (\Exception $e) {
            Log::error('Error loading add stock page: ' . $e->getMessage());
            return redirect()->route('catalog.items')->with('error', 'Gagal memuat halaman tambah eksemplar');
        }
    }

    /**
     * Show edit stock item page
     */
    public function editPage($id)
    {
        try {
            // Validate ID exists
            if (!KnowledgeStockModel::where('id', $id)->exists()) {
                return redirect()->route('catalog.items')->with('error', 'Eksemplar tidak ditemukan');
            }

            return view('catalog.items.edit', compact('id'));
        } catch (\Exception $e) {
            Log::error('Error loading edit stock page: ' . $e->getMessage());
            return redirect()->route('catalog.items')->with('error', 'Gagal memuat halaman edit eksemplar');
        }
    }

    /**
     * Show detail stock item page
     */
    public function detailPage($id, $code = null)
    {
        try {
            // Validate eksistensi stock
            $stock = KnowledgeStockModel::where('id', $id)->first();
            if (!$stock) {
                return redirect()->route('catalog.items')->with('error', 'Eksemplar tidak ditemukan');
            }

            // Validate kode jika disediakan
            if ($code && $stock->code !== $code) {
                return redirect()->route('catalog.items')->with('error', 'Kode eksemplar tidak sesuai');
            }

            // Pass variables to view
            $stockCode = $stock->code;

            return view('catalog.items.detail', compact('id', 'code', 'stockCode'));
        } catch (\Exception $e) {
            Log::error('Error loading detail stock page: ' . $e->getMessage());
            return redirect()->route('catalog.items')->with('error', 'Gagal memuat halaman detail eksemplar');
        }
    }

    /**
     * OPTIMIZED: Get DataTables data for stock items - Handles hundreds of thousands of records
     */
    public function dt(Request $request)
    {
        try {
            // Log untuk debugging pencarian
            if ($request->has('search') && !empty($request->search['value'])) {
                Log::debug('DataTables Search Value: ' . $request->search['value']);
            }

            if ($request->filled('columns')) {
                Log::debug('DataTables Columns Search: ' . json_encode($request->columns));
            }

            // OPTIMIZATION 1: Use JOIN instead of with() untuk avoid N+1 queries
            $query = KnowledgeStockModel::query()
                ->select([
                    'knowledge_stock.*',
                    // Pre-load relationship data via JOIN untuk avoid N+1
                    'knowledge_item.code as catalog_code',
                    'knowledge_item.title as catalog_title',
                    'knowledge_item.author as catalog_author',
                    'knowledge_item.publisher_name as catalog_publisher',
                    'knowledge_item.isbn as catalog_isbn',
                    'knowledge_item.cover_path as catalog_cover_path',
                    'knowledge_type.name as type_name',
                    'item_location.name as location_name'
                ])
                ->leftJoin('knowledge_item', 'knowledge_stock.knowledge_item_id', '=', 'knowledge_item.id')
                ->leftJoin('knowledge_type', 'knowledge_stock.knowledge_type_id', '=', 'knowledge_type.id')
                ->leftJoin('item_location', 'knowledge_stock.item_location_id', '=', 'item_location.id');

            // OPTIMIZATION 2: Apply filters early untuk reduce dataset
            if ($request->filled('knowledge_item_id')) {
                $query->where('knowledge_stock.knowledge_item_id', $request->knowledge_item_id);
            }

            if ($request->filled('knowledge_type_id')) {
                $query->where('knowledge_stock.knowledge_type_id', $request->knowledge_type_id);
            }

            if ($request->filled('item_location_id')) {
                $query->where('knowledge_stock.item_location_id', $request->item_location_id);
            }

            if ($request->filled('status')) {
                $query->where('knowledge_stock.status', $request->status);
            }

            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('knowledge_stock.entrance_date', [
                    $request->date_from . ' 00:00:00',
                    $request->date_to . ' 23:59:59'
                ]);
            }

            // OPTIMIZATION 3: Handle global search dengan JOIN data (no N+1)
            // FOKUS: Pencarian berdasarkan TITLE CATALOG dan data lainnya
            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function ($q) use ($searchValue) {
                    // PRIORITAS UTAMA: Search di TITLE CATALOG (knowledge_item.title)
                    // PERBAIKAN: Pastikan ini selalu menggunakan ILIKE atau case-insensitive search
                    $q->where('knowledge_item.title', 'like', "%{$searchValue}%")
                        // Search di data catalog lainnya
                        ->orWhere('knowledge_item.code', 'like', "%{$searchValue}%")
                        ->orWhere('knowledge_item.author', 'like', "%{$searchValue}%")
                        ->orWhere('knowledge_item.publisher_name', 'like', "%{$searchValue}%")
                        ->orWhere('knowledge_item.isbn', 'like', "%{$searchValue}%")
                        // Search di stock data
                        ->orWhere('knowledge_stock.code', 'like', "%{$searchValue}%")
                        ->orWhere('knowledge_stock.rfid', 'like', "%{$searchValue}%")
                        ->orWhere('knowledge_stock.supplier', 'like', "%{$searchValue}%")
                        // Search di master data
                        ->orWhere('knowledge_type.name', 'like', "%{$searchValue}%")
                        ->orWhere('item_location.name', 'like', "%{$searchValue}%");
                });

                // Tambahkan log SQL untuk debugging
                Log::debug('SQL Query for Global Search: ' . $this->getSqlWithBindings($query));
            }

            // OPTIMIZATION 4: Handle column-specific search dengan JOIN
            if ($request->filled('columns')) {
                foreach ($request->columns as $column) {
                    if (!empty($column['search']['value'])) {
                        $columnSearch = $column['search']['value'];
                        $columnName = $column['data'] ?? $column['name'];

                        Log::debug('Column search for: ' . $columnName . ' with value: ' . $columnSearch);

                        switch ($columnName) {
                            case 'catalog_title':
                            case 'catalog_info': // Tambahan untuk handle jika frontend menggunakan nama kolom ini
                                $query->where('knowledge_item.title', 'like', "%{$columnSearch}%");
                                Log::debug('Searching in knowledge_item.title for: ' . $columnSearch);
                                break;
                            case 'catalog_code_search':
                            case 'catalog_code': // Tambahan untuk handle jika frontend menggunakan nama kolom ini
                                $query->where('knowledge_item.code', 'like', "%{$columnSearch}%");
                                break;
                            case 'catalog_author':
                            case 'author': // Tambahan untuk handle jika frontend menggunakan nama kolom ini
                                $query->where('knowledge_item.author', 'like', "%{$columnSearch}%");
                                break;
                            case 'type_name':
                            case 'type_info': // Tambahan untuk handle jika frontend menggunakan nama kolom ini
                                $query->where('knowledge_type.name', 'like', "%{$columnSearch}%");
                                break;
                            case 'location_name':
                            case 'location_info': // Tambahan untuk handle jika frontend menggunakan nama kolom ini
                                $query->where('item_location.name', 'like', "%{$columnSearch}%");
                                break;
                            default:
                                if (in_array($columnName, ['code', 'rfid', 'supplier'])) {
                                    $query->where("knowledge_stock.{$columnName}", 'like', "%{$columnSearch}%");
                                }
                                break;
                        }
                    }
                }

                // Tambahkan log SQL untuk debugging
                Log::debug('SQL Query after column searches: ' . $this->getSqlWithBindings($query));
            }

            $selectedColumns = (array) $request->input('selected_columns', []);

            // OPTIMIZATION 5: Pre-calculate status labels untuk avoid repetitive calls
            $statusLabels = KnowledgeStockModel::getStatusLabels();

            $dataTable = DataTables::eloquent($query)
                // OPTIMIZATION 6: Use raw data instead of accessing relationships
                ->addColumn('action', function ($stock) {
                    return $this->generateActionButtons($stock);
                })
                ->addColumn('catalog_info', function ($stock) {
                    // Use JOIN data instead of relationship
                    $coverUrl = $this->getCoverUrl($stock->catalog_cover_path, $stock->catalog_code);
                    return $this->generateCatalogInfoOptimized(
                        $stock->catalog_code,
                        $stock->catalog_title,
                        $stock->catalog_author,
                        $coverUrl
                    );
                })
                ->addColumn('stock_code', function ($stock) {
                    return $this->generateStockCodeDisplayOptimized($stock->code, $stock->rfid);
                })
                ->addColumn('status_badge', function ($stock) use ($statusLabels) {
                    // Use pre-calculated labels
                    $label = $statusLabels[$stock->status] ?? 'Unknown';
                    return $this->generateStatusBadgeOptimized($stock->status, $label);
                })
                ->addColumn('location_info', function ($stock) {
                    return $stock->location_name ?? '-';
                })
                ->addColumn('type_info', function ($stock) {
                    return $stock->type_name ?? '-';
                })
                // Virtual columns untuk search (using JOIN data)
                ->addColumn('catalog_title', function ($stock) {
                    return $stock->catalog_title ?? '';
                })
                ->addColumn('catalog_code_search', function ($stock) {
                    return $stock->catalog_code ?? '';
                })
                ->addColumn('catalog_author', function ($stock) {
                    return $stock->catalog_author ?? '';
                })
                ->addColumn('type_name', function ($stock) {
                    return $stock->type_name ?? '';
                })
                ->addColumn('location_name', function ($stock) {
                    return $stock->location_name ?? '';
                })
                ->editColumn('entrance_date', function ($stock) {
                    return $stock->entrance_date ?
                        \Carbon\Carbon::parse($stock->entrance_date)->format('d/m/Y') : '-';
                })
                ->rawColumns(['action', 'catalog_info', 'stock_code', 'status_badge']);

            // Tambahkan mapping searchable columns untuk memastikan pencarian bekerja
            $dataTable->filterColumn('catalog_title', function ($query, $keyword) {
                $query->where('knowledge_item.title', 'like', "%{$keyword}%");
            });

            $dataTable->filterColumn('catalog_info', function ($query, $keyword) {
                $query->where('knowledge_item.title', 'like', "%{$keyword}%")
                    ->orWhere('knowledge_item.author', 'like', "%{$keyword}%")
                    ->orWhere('knowledge_item.code', 'like', "%{$keyword}%");
            });

            // OPTIMIZATION 7: Conditional columns dengan minimal processing
            if (in_array('rfid', $selectedColumns)) {
                $dataTable->addColumn('rfid', function ($stock) {
                    return $stock->rfid ?: '<span class="text-muted">Belum ada</span>';
                });
            }

            if (in_array('supplier', $selectedColumns)) {
                $dataTable->addColumn('supplier', function ($stock) {
                    return $stock->supplier ?: '-';
                });
            }

            if (in_array('price', $selectedColumns)) {
                $dataTable->addColumn('price_formatted', function ($stock) {
                    return $stock->price ? 'Rp ' . number_format($stock->price, 0, ',', '.') : '-';
                });
            }

            if (in_array('created_at', $selectedColumns)) {
                $dataTable->editColumn('created_at', function ($stock) {
                    return $stock->created_at ?
                        \Carbon\Carbon::parse($stock->created_at)->format('d/m/Y H:i') : '-';
                });
            }

            return $dataTable->toJson();

        } catch (\Exception $e) {
            Log::error('Error in stock items DataTable: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal memuat data eksemplar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper untuk debug SQL query dengan bindings
     */
    private function getSqlWithBindings($query)
    {
        $sql = $query->toSql();
        $bindings = $query->getBindings();

        $result = $sql;
        if (count($bindings) > 0) {
            foreach ($bindings as $binding) {
                $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
                $result = preg_replace('/\?/', $value, $result, 1);
            }
        }

        return $result;
    }

    /**
     * OPTIMIZATION: Generate catalog info without relationship access
     */
    private function generateCatalogInfoOptimized($code, $title, $author, $coverUrl)
    {
        $displayTitle = \Illuminate\Support\Str::limit($title ?? '', 40);
        $displayAuthor = \Illuminate\Support\Str::limit($author ?? '', 30);

        $html = '<div class="d-flex justify-content-start align-items-center">';

        // Cover Image
        $html .= '<div class="avatar-wrapper me-3">';
        $html .= '<div class="avatar avatar-md rounded-2 bg-label-secondary position-relative">';
        $html .= '<img src="' . $coverUrl . '" alt="Cover" class="catalog-cover cursor-pointer" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src=\'' . asset('assets/img/default-book-cover.jpg') . '\'" title="' . htmlspecialchars($title ?? '') . '">';
        $html .= '</div>';
        $html .= '</div>';

        // Catalog Info
        $html .= '<div class="d-flex flex-column">';
        $html .= '<h6 class="text-nowrap mb-0" title="' . htmlspecialchars($title ?? '') . '">' . $displayTitle . '</h6>';
        $html .= '<small class="text-muted d-block">Kode: <strong>' . ($code ?? '-') . '</strong></small>';
        $html .= '<small class="text-muted d-block">Penulis: ' . $displayAuthor . '</small>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * OPTIMIZATION: Generate stock code display without relationship
     */
    private function generateStockCodeDisplayOptimized($code, $rfid)
    {
        $html = '<div>';
        $html .= '<code class="fw-bold">' . ($code ?? '-') . '</code>';
        if ($rfid) {
            $html .= '<br><small class="text-muted">RFID: ' . $rfid . '</small>';
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * OPTIMIZATION: Generate status badge without model calls
     */
    private function generateStatusBadgeOptimized($status, $label)
    {
        $badgeClass = 'bg-secondary';

        switch ((int) $status) {
            case 1: // Available
            case 6: // Lost Replaced
                $badgeClass = 'bg-success';
                break;
            case 2: // Borrowed
                $badgeClass = 'bg-warning text-dark';
                break;
            case 3: // Damaged
                $badgeClass = 'bg-danger';
                break;
            case 4: // Lost
                $badgeClass = 'bg-dark';
                break;
            case 5: // Expired
                $badgeClass = 'bg-secondary';
                break;
            case 7: // Processing
                $badgeClass = 'bg-info';
                break;
            case 8: // Reserve
                $badgeClass = 'bg-primary';
                break;
            case 9: // Weeding
                $badgeClass = 'bg-secondary';
                break;
        }

        return "<span class='badge {$badgeClass}'>{$label}</span>";
    }
    /**
     * Enhanced insert method dengan pre-validation
     */
    public function insert(Request $request)
    {
        try {
            // Pre-validation check
            $this->validateStockRequest($request);

            // Additional business validation
            $this->validateBusinessRules($request);

            DB::beginTransaction();

            $user = auth()->user()?->master_data_user ?? 'system';

            // Generate stock code
            $stockCode = $this->generateNewStockCode($request->knowledge_item_id);

            $data = $request->only([
                'knowledge_item_id',
                'knowledge_type_id',
                'item_location_id',
                'rfid',
                'faculty_code',
                'course_code',
                'origination',
                'supplier',
                'price',
                'entrance_date',
                'status'
            ]);

            $data['code'] = $stockCode;
            $data['created_by'] = $user;
            $data['updated_by'] = $user;
            $data['status'] = $data['status'] ?? KnowledgeStockModel::STATUS_AVAILABLE;

            $stock = KnowledgeStockModel::create($data);

            // Update parent catalog stock counts
            $this->updateCatalogStockCounts($stock->knowledge_item_id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Eksemplar {$stockCode} berhasil ditambahkan!",
                'data' => $stock->load(['knowledgeItem', 'knowledgeType', 'itemLocation'])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating stock item: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan eksemplar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stock item for editing
     */
    public function edit(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:knowledge_stock,id'
            ]);

            $stock = KnowledgeStockModel::with([
                'knowledgeItem:id,code,title',
                'knowledgeType:id,name',
                'itemLocation:id,name'
            ])->findOrFail($request->id);

            // Format data for frontend
            $stockData = $stock->toArray();

            if ($stock->entrance_date) {
                $stockData['entrance_date'] = $stock->entrance_date->format('Y-m-d');
            }

            return response()->json([
                'success' => true,
                'data' => $stockData
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting stock item for edit: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Eksemplar tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update stock item
     */
    public function update(Request $request)
    {
        try {
            $this->validateStockRequest($request, true);

            DB::beginTransaction();

            $stock = KnowledgeStockModel::findOrFail($request->id);
            $oldKnowledgeItemId = $stock->knowledge_item_id;

            $user = auth()->user()?->master_data_user ?? 'system';

            $data = $request->only([
                'knowledge_item_id',
                'knowledge_type_id',
                'item_location_id',
                'rfid',
                'faculty_code',
                'course_code',
                'origination',
                'supplier',
                'price',
                'entrance_date',
                'status'
            ]);

            $data['updated_by'] = $user;

            // If knowledge_item_id changed, regenerate code
            if ($stock->knowledge_item_id != $request->knowledge_item_id) {
                $data['code'] = $this->generateNewStockCode($request->knowledge_item_id); // FIXED: Use renamed method
            }

            $stock->update($data);

            // Update stock counts for both old and new catalog if changed
            if ($oldKnowledgeItemId != $stock->knowledge_item_id) {
                $this->updateCatalogStockCounts($oldKnowledgeItemId);
            }
            $this->updateCatalogStockCounts($stock->knowledge_item_id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Eksemplar berhasil diperbarui!',
                'data' => $stock->fresh(['knowledgeItem', 'knowledgeType', 'itemLocation'])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating stock item: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui eksemplar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete stock item
     */
    public function delete(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:knowledge_stock,id'
            ]);

            DB::beginTransaction();

            $stock = KnowledgeStockModel::findOrFail($request->id);

            // Check if stock is currently borrowed
            if ($stock->status == KnowledgeStockModel::STATUS_BORROWED) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus eksemplar yang sedang dipinjam!'
                ], 422);
            }

            $knowledgeItemId = $stock->knowledge_item_id;
            $stockCode = $stock->code;

            $stock->delete();

            // Update parent catalog stock counts
            $this->updateCatalogStockCounts($knowledgeItemId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Eksemplar {$stockCode} berhasil dihapus!"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting stock item: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus eksemplar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function detail(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:knowledge_stock,id'
            ]);

            $stock = KnowledgeStockModel::with([
                'knowledgeItem' => function ($query) {
                    $query->select([
                        'id',
                        'code',
                        'title',
                        'author',
                        'publisher_name',
                        'publisher_city',
                        'published_year',
                        'isbn',
                        'cover_path',
                        'abstract_content',
                        'language',
                        'classification_code_id',
                        'knowledge_subject_id',
                        'alternate_subject',
                        'collation',
                        'editor',
                        'translator',
                        'author_type',
                        'rent_cost',
                        'penalty_cost'
                    ]);
                },
                'knowledgeItem.classification:id,code,name',
                'knowledgeItem.knowledgeSubject:id,name',
                'knowledgeItem.softcopyFiles' => function ($query) {
                    $query->with('uploadType:id,name,title,extension');
                },
                'knowledgeType:id,name',
                'itemLocation:id,name,address,phone,email'
            ])->findOrFail($request->id);

            // Convert to array
            $stockArray = $stock->toArray();

            // Add status label
            $stockArray['status_label'] = $stock->status_label;
            $stockArray['status_name'] = $stock->status_label;

            // Format dates
            if ($stock->entrance_date) {
                $stockArray['entrance_date_formatted'] = $stock->entrance_date->format('d F Y');
                $stockArray['entrance_date'] = $stock->entrance_date->format('Y-m-d');
            }

            if (isset($stock->created_at)) {
                $stockArray['created_at_formatted'] = $stock->created_at->format('d F Y H:i');
                $stockArray['created_at'] = $stock->created_at->format('Y-m-d H:i:s');
            }

            if (isset($stock->updated_at)) {
                $stockArray['updated_at_formatted'] = $stock->updated_at->format('d F Y H:i');
                $stockArray['updated_at'] = $stock->updated_at->format('Y-m-d H:i:s');
            }

            // PERBAIKAN: Generate proper cover URL dan data catalog
            if (isset($stockArray['knowledge_item'])) {
                $catalog = $stockArray['knowledge_item'];

                // Generate cover URL
                $stockArray['knowledge_item']['cover_url'] = $this->getCoverUrl(
                    $catalog['cover_path'] ?? null,
                    $catalog['code'] ?? null
                );

                // Add additional catalog data
                if (isset($catalog['classification'])) {
                    $stockArray['knowledge_item']['classification'] = $catalog['classification'];
                }

                if (isset($catalog['knowledge_subject'])) {
                    $stockArray['knowledge_item']['subject_name'] = $catalog['knowledge_subject']['name'];
                }

                // Format currency fields
                $stockArray['knowledge_item']['rent_cost_formatted'] = $catalog['rent_cost'] ?
                    'IDR ' . number_format($catalog['rent_cost'], 0, ',', '.') : 'IDR 0';
                $stockArray['knowledge_item']['penalty_cost_formatted'] = $catalog['penalty_cost'] ?
                    'IDR ' . number_format($catalog['penalty_cost'], 0, ',', '.') : 'IDR 0';

                // Author type text
                $authorTypes = [
                    1 => 'Pengarang Utama',
                    2 => 'Pengarang Kedua',
                    3 => 'Pengarang Ketiga',
                    4 => 'Editor',
                    5 => 'Penerjemah'
                ];
                $stockArray['knowledge_item']['author_type_text'] = $authorTypes[$catalog['author_type'] ?? 1] ?? 'Pengarang Utama';

                // Process softcopy files
                if (isset($stockArray['knowledge_item']['softcopy_files'])) {
                    $processedFiles = [];

                    foreach ($stockArray['knowledge_item']['softcopy_files'] as $file) {
                        $processedFile = [
                            'filename' => $file['kif_file'],
                            'upload_type' => $file['upload_type'],
                            'upload_date' => $file['kif_datetime'],
                            'download_url' => $this->generateDownloadUrl($catalog['code'], $file['kif_file'])
                        ];
                        $processedFiles[] = $processedFile;
                    }

                    $stockArray['knowledge_item']['softcopy_files'] = $processedFiles;
                }
            }

            // Add stock summary for the catalog
            if (isset($stockArray['knowledge_item']['id'])) {
                $catalogId = $stockArray['knowledge_item']['id'];
                $stockSummary = [
                    'total' => KnowledgeStockModel::where('knowledge_item_id', $catalogId)->count(),
                    'available' => KnowledgeStockModel::where('knowledge_item_id', $catalogId)
                        ->where('status', KnowledgeStockModel::STATUS_AVAILABLE)->count(),
                    'borrowed' => KnowledgeStockModel::where('knowledge_item_id', $catalogId)
                        ->where('status', KnowledgeStockModel::STATUS_BORROWED)->count()
                ];
                $stockArray['stock_summary'] = $stockSummary;
            }

            return response()->json([
                'success' => true,
                'data' => $stockArray
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting stock item detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Eksemplar tidak ditemukan'
            ], 404);
        }
    }
    /**
     * Download softcopy file
     */
    public function downloadFile($code, $filename, Request $request)
    {
        try {
            // Find catalog by code
            $catalog = KnowledgeItemModel::where('code', $code)->first();
            if (!$catalog) {
                abort(404, 'Katalog tidak ditemukan');
            }

            // Verify file exists in database
            $fileRecord = KnowledgeItemFileModel::where('kif_knowledge_item_id', $catalog->id)
                ->where('kif_file', $filename)
                ->with('uploadType')
                ->first();

            if (!$fileRecord) {
                abort(404, 'File tidak ditemukan dalam database');
            }

            // Build file path: book/kodekatalog/kodekatalog_namafile
            $filePath = storage_path('app/public/book/' . $code . '/' . $filename);

            // Check if file exists on disk
            if (!file_exists($filePath)) {
                // Try alternative path: book/kodekatalog/namafile (without prefix)
                $altFilePath = storage_path('app/public/book/' . $code . '/' . $filename);
                if (!file_exists($altFilePath)) {
                    abort(404, 'File tidak ditemukan di server');
                }
                $filePath = $altFilePath;
            }

            // Get file info
            $fileInfo = pathinfo($filePath);
            $mimeType = $this->getMimeType($fileInfo['extension'] ?? '');

            // Log download (dengan filename)
            $this->logFileDownload($catalog->id, $fileRecord->kif_id, $request, $filename);

            // Return file download response
            return response()->download($filePath, $filename, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);

        } catch (\Exception $e) {
            Log::error('Error downloading file: ' . $e->getMessage(), [
                'code' => $code,
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);

            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }

            abort(500, 'Gagal mengunduh file');
        }
    }

    /**
     * Get MIME type based on file extension
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
            'rtf' => 'application/rtf',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            '7z' => 'application/x-7z-compressed',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'tiff' => 'image/tiff',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
            'mov' => 'video/quicktime',
            'epub' => 'application/epub+zip',
            'mobi' => 'application/x-mobipocket-ebook'
        ];

        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }

    /**
     * Log file download dengan nama file yang tepat
     */
    private function logFileDownload($catalogId, $fileId, $request, $filename)
    {
        try {
            // Check if user is authenticated
            $memberId = null;
            if (auth()->check()) {
                $memberId = auth()->id();
            } else {
                // Untuk user yang tidak login, bisa pakai ID khusus atau skip logging
                return;
            }

            // Log to knowledge_item_file_download table
            DB::table('knowledge_item_file_download')->insert([
                'knowledge_item_id' => $catalogId,
                'member_id' => $memberId,
                'name' => $filename, // Store actual filename untuk counting
                'created_at' => now()
            ]);

            Log::info('File download logged', [
                'catalog_id' => $catalogId,
                'file_id' => $fileId,
                'filename' => $filename,
                'member_id' => $memberId,
                'ip' => $request->ip()
            ]);

        } catch (\Exception $e) {
            // Don't fail download if logging fails
            Log::warning('Failed to log file download: ' . $e->getMessage());
        }
    }
    /**
     * Get actual download count for a specific file
     */
    private function getFileDownloadCount($knowledgeItemId, $filename)
    {
        try {
            // Count downloads based on knowledge_item_id and filename
            // Since the 'name' field in download log contains filename info
            $downloadCount = DB::table('knowledge_item_file_download')
                ->where('knowledge_item_id', $knowledgeItemId)
                ->where('name', 'like', "%{$filename}%")
                ->count();

            return $downloadCount;
        } catch (\Exception $e) {
            Log::warning('Failed to get download count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Generate download URL for softcopy files
     */
    private function generateDownloadUrl($catalogCode, $filename)
    {
        // Sesuaikan dengan route download yang ada di sistem Anda
        // Contoh: /catalog/download/{code}/{file}
        return route('catalog.download', [
            'code' => $catalogCode,
            'file' => $filename
        ]);
    }

    /**
     * Search catalogs for filter with optimized query - UPDATED dengan delay support
     */
    public function searchCatalogsForFilter(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $limit = $request->get('limit', 15);

            $query = KnowledgeItemModel::select('id', 'code', 'title', 'author', 'cover_path');

            if (!empty($search) && strlen($search) >= 2) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%");
                });
            } else if (empty($search)) {
                // PERBAIKAN: Jika tidak ada search, tampilkan data terbaru
                $query->orderBy('created_at', 'desc');
            } else {
                // PERBAIKAN: Jika search kurang dari 2 karakter, return kosong
                return response()->json(['success' => true, 'data' => []]);
            }

            $catalogs = $query->orderBy('code')
                ->limit($limit)
                ->get()
                ->map(function ($catalog) {
                    return [
                        'id' => $catalog->id,
                        'code' => $catalog->code,
                        'title' => $catalog->title,
                        'author' => $catalog->author,
                        'cover_url' => $this->getCoverUrl($catalog->cover_path),
                        'text' => $catalog->code . ' - ' . $catalog->title // PERBAIKAN: Format untuk select2
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $catalogs
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching catalogs for filter: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari katalog: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Get form options for dropdowns - UPDATED dengan cover URL
     */
    public function getFormOptions()
    {
        try {
            // Catalogs dengan cover URL yang sudah diperbaiki
            $catalogs = KnowledgeItemModel::select('id', 'code', 'title', 'author', 'cover_path')
                ->orderBy('code')
                ->get()
                ->map(function ($catalog) {
                    return [
                        'id' => $catalog->id,
                        'code' => $catalog->code,
                        'title' => $catalog->title,
                        'author' => $catalog->author,
                        'cover_url' => $this->getCoverUrl($catalog->cover_path, $catalog->code)
                    ];
                });

            $knowledgeTypes = KnowledgeTypeModel::where('active', 1)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            $locations = ItemLocation::select('id', 'name')
                ->orderBy('name')
                ->get();

            $fakultas = FakultasModel::active()
                ->orderBy('NAMA_FAKULTAS')
                ->get(['C_KODE_FAKULTAS', 'NAMA_FAKULTAS', 'SINGKATAN']);

            $prodi = ProdiModel::active()
                ->orderBy('NAMA_PRODI')
                ->get(['C_KODE_PRODI', 'C_KODE_FAKULTAS', 'NAMA_PRODI']);

            $statusOptions = KnowledgeStockModel::getStatusLabels();

            return response()->json([
                'success' => true,
                'data' => [
                    'catalogs' => $catalogs,
                    'knowledge_types' => $knowledgeTypes,
                    'locations' => $locations,
                    'fakultas' => $fakultas,
                    'prodi' => $prodi,
                    'status_options' => $statusOptions
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading form options: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data dropdown: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Preview new stock code
     */
    public function previewStockCode(Request $request)
    {
        try {
            $request->validate([
                'knowledge_item_id' => 'required|exists:knowledge_item,id'
            ]);

            $catalog = KnowledgeItemModel::findOrFail($request->knowledge_item_id);
            $existingCount = KnowledgeStockModel::where('knowledge_item_id', $request->knowledge_item_id)->count();
            $newCode = $this->generateNewStockCode($request->knowledge_item_id);

            return response()->json([
                'success' => true,
                'data' => [
                    'new_code' => $newCode,
                    'catalog_title' => $catalog->title,
                    'catalog_code' => $catalog->code,
                    'existing_count' => $existingCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Search catalogs with delay support - NEW METHOD
     */
    public function searchCatalogs(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $limit = $request->get('limit', 20);

            $query = KnowledgeItemModel::select('id', 'code', 'title', 'author', 'cover_path');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%");
                });
            }

            $catalogs = $query->orderBy('code')
                ->limit($limit)
                ->get()
                ->map(function ($catalog) {
                    return [
                        'id' => $catalog->id,
                        'code' => $catalog->code,
                        'title' => $catalog->title,
                        'author' => $catalog->author,
                        'cover_url' => $this->getCoverUrl($catalog->cover_path, $catalog->code)
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $catalogs
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching catalogs: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari katalog: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics for dashboard cards
     */
    public function getStatistics()
    {
        try {
            $statistics = [
                'total_stock' => KnowledgeStockModel::count(),
                'available_stock' => KnowledgeStockModel::where('status', KnowledgeStockModel::STATUS_AVAILABLE)->count(),
                'borrowed_stock' => KnowledgeStockModel::where('status', KnowledgeStockModel::STATUS_BORROWED)->count(),
                'damaged_stock' => KnowledgeStockModel::whereIn('status', [
                    KnowledgeStockModel::STATUS_DAMAGED,
                    KnowledgeStockModel::STATUS_LOST
                ])->count(),
                'processing_stock' => KnowledgeStockModel::where('status', KnowledgeStockModel::STATUS_PROCESSING)->count(),
                'reserve_stock' => KnowledgeStockModel::where('status', KnowledgeStockModel::STATUS_RESERVE)->count(),
            ];

            // Additional statistics
            $statistics['percentage_available'] = $statistics['total_stock'] > 0
                ? round(($statistics['available_stock'] / $statistics['total_stock']) * 100, 1)
                : 0;

            $statistics['percentage_borrowed'] = $statistics['total_stock'] > 0
                ? round(($statistics['borrowed_stock'] / $statistics['total_stock']) * 100, 1)
                : 0;

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading statistics: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Bulk update status for multiple stock items
     */
    public function bulkUpdateStatus(Request $request)
    {
        try {
            $request->validate([
                'stock_ids' => 'required|array|min:1',
                'stock_ids.*' => 'exists:knowledge_stock,id',
                'status' => 'required|integer|in:1,2,3,4,5,6,7,8,9'
            ]);

            DB::beginTransaction();

            $user = auth()->user()?->master_data_user ?? 'system';

            KnowledgeStockModel::whereIn('id', $request->stock_ids)
                ->update([
                    'status' => $request->status,
                    'updated_by' => $user,
                    'updated_at' => now()
                ]);

            // Get affected catalog items for stock count update
            $affectedItemIds = KnowledgeStockModel::whereIn('id', $request->stock_ids)
                ->pluck('knowledge_item_id')
                ->unique();

            // Update stock counts for affected catalogs
            foreach ($affectedItemIds as $itemId) {
                $this->updateCatalogStockCounts($itemId);
            }

            DB::commit();

            $statusLabel = KnowledgeStockModel::getStatusLabels()[$request->status];
            $count = count($request->stock_ids);

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengubah status {$count} eksemplar menjadi '{$statusLabel}'"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk updating status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transfer stock items to different location
     */
    public function transferLocation(Request $request)
    {
        try {
            $request->validate([
                'stock_ids' => 'required|array|min:1',
                'stock_ids.*' => 'exists:knowledge_stock,id',
                'new_location_id' => 'required|exists:item_location,id'
            ]);

            DB::beginTransaction();

            $user = auth()->user()?->master_data_user ?? 'system';

            KnowledgeStockModel::whereIn('id', $request->stock_ids)
                ->update([
                    'item_location_id' => $request->new_location_id,
                    'updated_by' => $user,
                    'updated_at' => now()
                ]);

            DB::commit();

            $location = ItemLocation::find($request->new_location_id);
            $count = count($request->stock_ids);

            return response()->json([
                'success' => true,
                'message' => "Berhasil memindahkan {$count} eksemplar ke '{$location->name}'"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error transferring location: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memindahkan lokasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check RFID availability
     */
    public function checkRfid(Request $request)
    {
        try {
            $request->validate([
                'rfid' => 'required|string',
                'exclude_id' => 'nullable|exists:knowledge_stock,id'
            ]);

            $query = KnowledgeStockModel::where('rfid', $request->rfid);

            if ($request->exclude_id) {
                $query->where('id', '!=', $request->exclude_id);
            }

            $exists = $query->exists();

            return response()->json([
                'success' => true,
                'available' => !$exists,
                'message' => $exists ? 'RFID sudah digunakan' : 'RFID `tersedia`'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking RFID: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========== PRIVATE HELPER METHODS ==========

    /**
     * Enhanced validation with business rules
     */
    private function validateStockRequest(Request $request, $isUpdate = false)
    {
        $rules = [
            // REQUIRED FIELDS (NOT NULL in database)
            'knowledge_item_id' => 'required|exists:knowledge_item,id',
            'knowledge_type_id' => 'required|exists:knowledge_type,id',
            'item_location_id' => 'required|exists:item_location,id',
            'origination' => 'required|integer|in:1,2', // 1=Pembelian, 2=Sumbangan
            'entrance_date' => 'required|date|before_or_equal:today',
            'status' => 'required|integer|in:1,2,3,4,5,6,7,8,9',

            // NULLABLE FIELDS with conditional rules
            'rfid' => 'nullable|string|max:50|regex:/^[A-Za-z0-9]+$/', // Only alphanumeric
            'faculty_code' => 'nullable|exists:t_mst_fakultas,C_KODE_FAKULTAS',
            'course_code' => 'nullable|exists:t_mst_prodi,C_KODE_PRODI',
            'supplier' => 'nullable|string|max:255|min:2',
            'price' => 'nullable|integer|min:0|max:999999999',
        ];

        // CONDITIONAL VALIDATION RULES

        // 1. RFID uniqueness validation
        if ($isUpdate) {
            $rules['id'] = 'required|exists:knowledge_stock,id';

            if ($request->filled('rfid')) {
                $rules['rfid'] = [
                    'nullable',
                    'string',
                    'max:50',
                    'regex:/^[A-Za-z0-9]+$/',
                    Rule::unique('knowledge_stock', 'rfid')->ignore($request->id)
                ];
            }
        } else {
            if ($request->filled('rfid')) {
                $rules['rfid'] = 'nullable|string|max:50|regex:/^[A-Za-z0-9]+$/|unique:knowledge_stock,rfid';
            }
        }

        // 2. Business Rule: Jika origination = 1 (Pembelian), supplier dan price WAJIB
        if ($request->origination == 1) {
            $rules['supplier'] = 'required|string|max:255|min:2';
            $rules['price'] = 'required|integer|min:1'; // Tidak boleh 0 untuk pembelian
        }

        // 3. Business Rule: Jika ada course_code, faculty_code juga wajib
        if ($request->filled('course_code')) {
            $rules['faculty_code'] = 'required|exists:t_mst_fakultas,C_KODE_FAKULTAS';

            // Validasi course_code sesuai dengan faculty_code
            $rules['course_code'] = [
                'required',
                'exists:t_mst_prodi,C_KODE_PRODI',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->filled('faculty_code')) {
                        $prodi = \App\Models\ProdiModel::where('C_KODE_PRODI', $value)
                            ->where('C_KODE_FAKULTAS', $request->faculty_code)
                            ->first();

                        if (!$prodi) {
                            $fail('Program studi tidak sesuai dengan fakultas yang dipilih.');
                        }
                    }
                }
            ];
        }

        // 4. Status transition validation for updates
        if ($isUpdate && $request->filled('status')) {
            $currentStock = KnowledgeStockModel::find($request->id);
            if ($currentStock) {
                $this->validateStatusTransition($currentStock->status, $request->status, $rules);
            }
        }

        $request->validate($rules, [
            // Custom error messages
            'knowledge_item_id.required' => 'Katalog wajib dipilih',
            'knowledge_item_id.exists' => 'Katalog tidak ditemukan',
            'knowledge_type_id.required' => 'Jenis katalog wajib dipilih',
            'item_location_id.required' => 'Lokasi wajib dipilih',
            'origination.required' => 'Asal penerimaan wajib dipilih',
            'origination.in' => 'Asal penerimaan tidak valid (1=Pembelian, 2=Sumbangan)',
            'entrance_date.required' => 'Tanggal masuk wajib diisi',
            'entrance_date.before_or_equal' => 'Tanggal masuk tidak boleh di masa depan',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status tidak valid',
            'rfid.regex' => 'RFID hanya boleh berisi huruf dan angka',
            'rfid.unique' => 'RFID sudah digunakan oleh eksemplar lain',
            'supplier.required' => 'Supplier wajib diisi untuk pembelian',
            'supplier.min' => 'Nama supplier minimal 2 karakter',
            'price.required' => 'Harga wajib diisi untuk pembelian',
            'price.min' => 'Harga tidak boleh 0 untuk pembelian',
            'price.max' => 'Harga terlalu besar',
            'faculty_code.required' => 'Fakultas wajib dipilih jika ada program studi',
            'course_code.required' => 'Program studi wajib jika fakultas dipilih',
        ]);
    }


    /**
     * Get cover URL with fallback logic
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

        // First, try the new path structure
        $newPath = "uploads/book/cover/{$catalogCode}.jpg";
        $newFullPath = storage_path("app/public/{$newPath}");

        if (file_exists($newFullPath)) {
            return asset("storage/{$newPath}");
        }

        // If catalog code is provided but file not found in new structure,
        // try with a different extension
        if ($catalogCode) {
            $extensions = ['jpeg', 'png', 'gif'];
            foreach ($extensions as $ext) {
                $altPath = "uploads/book/cover/{$catalogCode}.{$ext}";
                $altFullPath = storage_path("app/public/{$altPath}");
                if (file_exists($altFullPath)) {
                    return asset("storage/{$altPath}");
                }
            }
        }

        // If still not found, check in old path structure
        // First case: full path is provided
        if (str_contains($coverPath, '/')) {
            $oldFullPath = storage_path("app/public/{$coverPath}");
            if (file_exists($oldFullPath)) {
                return asset("storage/{$coverPath}");
            }
        }
        // Second case: only filename is provided but with catalog code
        else if ($catalogCode) {
            $oldPath = "book/{$catalogCode}/{$coverPath}";
            $oldFullPath = storage_path("app/public/{$oldPath}");
            if (file_exists($oldFullPath)) {
                return asset("storage/{$oldPath}");
            }
        }

        // Last fallback: Try direct access to old cover path structure
        $fallbackPath = "uploads/book/cover/" . basename($coverPath);
        $fallbackFullPath = storage_path("app/public/{$fallbackPath}");
        if (file_exists($fallbackFullPath)) {
            return asset("storage/{$fallbackPath}");
        }

        // Ultimate fallback: remote URL
        return 'https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/' .
            ($catalogCode ? $catalogCode : basename($coverPath));
    }


    /**
     * Additional business rules validation
     */
    private function validateBusinessRules(Request $request)
    {
        // 1. Check if catalog allows more stock items
        $catalog = KnowledgeItemModel::find($request->knowledge_item_id);
        if (!$catalog) {
            throw new \Exception('Katalog tidak ditemukan');
        }

        // 2. Check duplicate RFID in real-time
        if ($request->filled('rfid')) {
            $existingRfid = KnowledgeStockModel::where('rfid', $request->rfid)
                ->when($request->filled('id'), function ($q) use ($request) {
                    return $q->where('id', '!=', $request->id);
                })
                ->first();

            if ($existingRfid) {
                throw new \Exception('RFID sudah digunakan oleh eksemplar: ' . $existingRfid->code);
            }
        }

        // 3. Validate entrance_date tidak lebih dari tanggal katalog dibuat
        if ($catalog->entrance_date && $request->entrance_date < $catalog->entrance_date->format('Y-m-d')) {
            throw new \Exception('Tanggal masuk eksemplar tidak boleh lebih awal dari tanggal masuk katalog');
        }
    }

    /**
     * Validate status transition rules
     */
    private function validateStatusTransition($currentStatus, $newStatus, &$rules)
    {
        // Define allowed status transitions
        $allowedTransitions = [
            1 => [2, 3, 4, 7, 8, 9], // Available → Borrowed, Damaged, Lost, Processing, Reserve, Weeding
            2 => [1, 3, 4, 6],       // Borrowed → Available, Damaged, Lost, Lost Replaced
            3 => [1, 9],             // Damaged → Available, Weeding
            4 => [6, 9],             // Lost → Lost Replaced, Weeding
            5 => [9],                // Expired → Weeding
            6 => [1],                // Lost Replaced → Available
            7 => [1, 3, 9],          // Processing → Available, Damaged, Weeding
            8 => [1, 2],             // Reserve → Available, Borrowed
            9 => []                  // Weeding → (no transitions allowed)
        ];

        if (!in_array($newStatus, $allowedTransitions[$currentStatus] ?? [])) {
            $statusLabels = \App\Models\KnowledgeStockModel::getStatusLabels();
            $currentLabel = $statusLabels[$currentStatus] ?? 'Unknown';
            $newLabel = $statusLabels[$newStatus] ?? 'Unknown';

            $rules['status'] = [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($currentLabel, $newLabel) {
                    $fail("Tidak dapat mengubah status dari '{$currentLabel}' ke '{$newLabel}'");
                }
            ];
        }
    }
    /**
     * Generate new stock code based on catalog - FIXED: Renamed method
     */
    private function generateNewStockCode($knowledgeItemId)
    {
        $catalog = KnowledgeItemModel::findOrFail($knowledgeItemId);
        $baseCode = $catalog->code;

        // PERBAIKAN: Cari sequence terakhir untuk catalog ini
        $lastSequence = KnowledgeStockModel::where('knowledge_item_id', $knowledgeItemId)
            ->where('code', 'LIKE', $baseCode . '-%')
            ->orderByRaw('CAST(SUBSTRING_INDEX(code, "-", -1) AS UNSIGNED) DESC')
            ->first();

        $nextSequence = 1;
        if ($lastSequence) {
            $lastNumber = (int) substr($lastSequence->code, strrpos($lastSequence->code, '-') + 1);
            $nextSequence = $lastNumber + 1;
        }

        return $baseCode . '-' . $nextSequence;
    }


    /**
     * Update catalog stock counts
     */
    private function updateCatalogStockCounts($knowledgeItemId)
    {
        try {
            $catalog = KnowledgeItemModel::find($knowledgeItemId);
            if ($catalog) {
                $catalog->updateStockCounts();
            }
        } catch (\Exception $e) {
            Log::warning("Failed to update stock counts for catalog {$knowledgeItemId}: " . $e->getMessage());
        }
    }

    /**
     * Generate action buttons for DataTable
     */
    private function generateActionButtons($stock)
    {
        $btn = '<div class="btn-group my-btn-group">';
        $btn .= '<button class="btn rounded-pill btn-icon btn-label-primary waves-effect my-dropdown-toggle" type="button" data-id="' . $stock->id . '">';
        $btn .= '<i class="ti ti-dots-vertical"></i>';
        $btn .= '</button>';
        $btn .= '<ul class="dropdown-menu" style="display:none;">';

        $btn .= '<li><a class="dropdown-item d-flex align-items-center view-btn" href="' . route('catalog.items.detail', ['id' => $stock->id, 'code' => $stock->code]) . '">';
        $btn .= '<i class="ti ti-eye ti-sm me-2"></i> View Detail</a></li>';

        $btn .= '<li><a class="dropdown-item d-flex align-items-center edit-btn" href="' . route('catalog.items.edit', $stock->id) . '">';
        $btn .= '<i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>';

        if ($stock->status != KnowledgeStockModel::STATUS_BORROWED) {
            $btn .= '<li><a class="dropdown-item d-flex align-items-center text-danger delete-btn" href="javascript:void(0);" data-id="' . $stock->id . '">';
            $btn .= '<i class="ti ti-trash me-2"></i> Delete Data</a></li>';
        }

        $btn .= '<li><hr class="dropdown-divider"></li>';

        // Print Barcode
        $btn .= '<li><a class="dropdown-item d-flex align-items-center print-barcode-btn" href="' . route('catalog.items.print-barcode', $stock->id) . '" target="_blank">';
        $btn .= '<i class="ti ti-printer me-2"></i> Print Barcode</a></li>';

        $btn .= '</ul></div>';

        return $btn;
    }


    /**
     * Generate catalog info display with cover image
     */
    private function generateCatalogInfo($stock)
    {
        if (!$stock->knowledgeItem) {
            return '<span class="text-muted">Data katalog tidak ditemukan</span>';
        }

        $catalog = $stock->knowledgeItem;
        $title = \Illuminate\Support\Str::limit($catalog->title, 40);
        $author = \Illuminate\Support\Str::limit($catalog->author, 30);

        // Get cover URL with catalog code
        $coverUrl = $this->getCoverUrl($catalog->cover_path, $catalog->code);

        $html = '<div class="d-flex justify-content-start align-items-center">';

        // Cover Image with hover effect
        $html .= '<div class="avatar-wrapper me-3">';
        $html .= '<div class="avatar avatar-md rounded-2 bg-label-secondary position-relative">';
        $html .= '<img src="' . $coverUrl . '" alt="Cover" class="catalog-cover cursor-pointer" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src=\'' . asset('assets/img/default-book-cover.jpg') . '\'" title="' . htmlspecialchars($catalog->title) . '">';
        $html .= '</div>';
        $html .= '</div>';

        // Catalog Info
        $html .= '<div class="d-flex flex-column">';
        $html .= '<h6 class="text-nowrap mb-0" title="' . htmlspecialchars($catalog->title) . '">' . $title . '</h6>';
        $html .= '<small class="text-muted d-block">Kode: <strong>' . $catalog->code . '</strong></small>';
        $html .= '<small class="text-muted d-block">Penulis: ' . $author . '</small>';
        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }


    /**
     * Generate stock code display for DataTable - FIXED: Renamed method
     */
    private function generateStockCodeDisplay($stock)
    {
        $html = '<div>';
        $html .= '<code class="fw-bold">' . $stock->code . '</code>';
        if ($stock->rfid) {
            $html .= '<br><small class="text-muted">RFID: ' . $stock->rfid . '</small>';
        }
        $html .= '</div>';

        return $html;
    }


    /**
     * Generate status badge
     */
    private function generateStatusBadge($stock)
    {
        // PERBAIKAN: Gunakan status_label dari model
        $label = $stock->status_label;

        $badgeClass = 'bg-secondary';
        switch ($stock->status) {
            case KnowledgeStockModel::STATUS_AVAILABLE:
            case KnowledgeStockModel::STATUS_LOST_REPLACED:
                $badgeClass = 'bg-success';
                break;
            case KnowledgeStockModel::STATUS_BORROWED:
                $badgeClass = 'bg-warning text-dark';
                break;
            case KnowledgeStockModel::STATUS_DAMAGED:
                $badgeClass = 'bg-danger';
                break;
            case KnowledgeStockModel::STATUS_LOST:
                $badgeClass = 'bg-dark';
                break;
            case KnowledgeStockModel::STATUS_EXPIRED:
                $badgeClass = 'bg-secondary';
                break;
            case KnowledgeStockModel::STATUS_PROCESSING:
                $badgeClass = 'bg-info';
                break;
            case KnowledgeStockModel::STATUS_RESERVE:
                $badgeClass = 'bg-primary';
                break;
            case KnowledgeStockModel::STATUS_WEEDING:
                $badgeClass = 'bg-secondary';
                break;
        }

        return "<span class='badge {$badgeClass}'>{$label}</span>";
    }

}