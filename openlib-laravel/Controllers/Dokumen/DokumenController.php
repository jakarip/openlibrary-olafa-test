<?php

    namespace App\Http\Controllers\Dokumen;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Document;
    use App\Models\Workflow;
    use App\Models\KnowledgeType;
    use App\Models\Status;
    use App\Models\Member;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Facades\Mail;
    use App\Helpers\Helpers;
    use Illuminate\Support\Facades\Log;


    class DokumenController extends Controller
    {
        protected $documentModel;
        protected $workflowModel;
        protected $knowledgeTypeModel;
        protected $statusModel;
        protected $memberModel;

        public function __construct(
            Document $documentModel,
            Workflow $workflowModel,
            KnowledgeType $knowledgeTypeModel,
            Status $statusModel,
            Member $memberModel
        
        ) {
            $this->documentModel = $documentModel;
            $this->workflowModel = $workflowModel;
            $this->knowledgeTypeModel = $knowledgeTypeModel;
            $this->statusModel = $statusModel;
            $this->memberModel = $memberModel;
        }
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        session(['user_doc' => [
            'membertype' => Auth::user()->member_type_id,
            'id' => Auth::id(),
        ]]);

        $workflows = $this->workflowModel->all();
        $types = $this->knowledgeTypeModel->all();
        $statuses = [
            1 => 'Ongoing',
            2 => 'Archived'
        ];

        return view('dokumen.index', compact('workflows', 'types', 'statuses'));
    }



    public function json(Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 20);
        $searchValue = $request->input('search')['value'] ?? null;
    
        $param = [
            'where' => $this->buildWhereClause($request),
            'order' => $this->buildOrderClause($request),
            'limit' => $length,
            'offset' => $start,
            'status' => $request->input('status', ''),
            'workflow' => $request->input('workflow', ''),
            'type' => $request->input('type', ''),
            'dates_acceptance_option' => $request->input('dates_acceptance_option', ''),
            'dates_acceptance' => $request->input('dates_acceptance', ''),
            'attribute' => $request->input('attribute', ''),
            'onlyforme' => $request->input('onlyforme', ''),
        ];
    
        $data = $this->documentModel->dtquery($param);
        
        $recordsTotal = $this->documentModel->dtcount();
        
        $filteredParam = $param;
        unset($filteredParam['limit']);
        unset($filteredParam['offset']);
        $recordsFiltered = $this->documentModel->countByQuery(['where' => $filteredParam['where']]);
    
        $output = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => [],
        ];

        foreach ($data as $row) {
            $action = '<div class="text-center">
                <div class="btn-group">
                    <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">';
        
            // Selalu tampilkan tombol "Lihat Detail"
            $action .= '<li class="d-flex"> 
                <a class="dropdown-item d-flex" href="' . route('dokumen.edit', ['id' => $row->wd_id]) . '">
                    <i class="ti ti-file-info ti-sm me-2"></i> Lihat Detail
                </a>
            </li>';
        
            $action .= '</ul>
                </div>
            </div>';
        
            $output['data'][] = [
                'creator' => "<strong>{$row->master_data_user}</strong><br>{$row->master_data_fullname}<br>{$row->wd_date}<br>",
                'workflow' => "<div style='text-align: center;'><b>{$row->jenis_workflow}</b><br><br>". "</div>",
                'title' => $row->title,
                'subject' => '<div class="text-center">' . $row->subjek . '</div>',
                'type' => $row->jenis_katalog,
                'action' => $action,
                'state' => "<b>$row->state_name</b>",
            ];
        }

        return response()->json($output);
    }

    public function create()
    {
        if (!Auth::check()) {
            return redirect('/');
        }

    
        session(['user_doc' => [
            'membertype' => Auth::user()->member_type_id,
            'id' => Auth::id(),
        ]]);

        $workflows = $this->workflowModel->all();
        $types = $this->knowledgeTypeModel->all();
        $statuses = [
            1 => 'Ongoing',
            2 => 'Archived'
        ];

        $units = $this->documentModel->getUnit();
        $unitOptions = [];
        foreach ($units as $unit) {
            $unitOptions[$unit->C_KODE_PRODI] = $unit->NAMA_PRODI;
        }

        $wd = $this->documentModel->find(1);

        return view('dokumen.add', compact('workflows', 'types', 'statuses', 'wd', 'unitOptions'));
    }
    public function getMasterSubject(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $unitId = $request->input('id');
        if (!$unitId) {
            return response()->json(['error' => 'Unit ID is required'], 400);
        }

        $subjects = $this->documentModel->getMasterSubjectByUnitId($unitId);

        if ($subjects->isEmpty()) {
            return response()->json(['error' => 'No subjects found'], 404);
        }

        return response()->json($subjects);
    }

        
        /**
         * Build WHERE clause based on request parameters.
         */
        private function buildWhereClause(Request $request)
    {
        $where = [];


        \Log::info('Filter Parameters:', [
            'workflow' => $request->workflow,
            'type' => $request->type,
            'status' => $request->status,
            'attribute' => $request->attribute,
            'onlyforme' => $request->onlyforme,
            'dates_acceptance_option' => $request->dates_acceptance_option,
            'dates_acceptance' => $request->dates_acceptance,
        ]);

        if ($request->has('workflow') && $request->workflow) {
            $where[] = "w.id = '{$request->workflow}'";
        }

        if ($request->has('type') && $request->type) {
            $where[] = "kt.id = '{$request->type}'";
        }

        if ($request->has('status') && $request->status) {
            $where[] = "wd.status = '{$request->status}'";
        }

        if ($request->has('attribute') && $request->attribute == '1') {
            $where[] = "(wsp.can_edit_state = 1 OR wsp.can_edit_attribute = 1 OR wd.member_id = '" . Auth::id() . "')";
        }

        if ($request->has('onlyforme') && $request->onlyforme == '1') {
            $where[] = "wds.allowed_member_id = '" . Auth::id() . "'";
        }

        if ($request->has('dates_acceptance_option') && $request->dates_acceptance_option != 'all') {
            $dates = explode(' - ', $request->dates_acceptance);
            $date1 = date('Y-m-d', strtotime($dates[0]));
            $date2 = date('Y-m-d', strtotime($dates[1]));
            $where[] = "wd.created_at BETWEEN '{$date1} 00:00:00' AND '{$date2} 23:59:59'";
        }

        return !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    }
        
        /**
         * Build ORDER BY clause based on request parameters.
         */
        private function buildOrderClause(Request $request)
        {
            $order = [];
        
            if ($request->has('order')) {
                \Log::info('Order Parameters:', $request->order);
        
                foreach ($request->order as $item) {
                    $columnIndex = $item['column'];
                    $dir = $item['dir'];
        

                    $columns = [
                        0 => 'm.master_data_user',
                        1 => 'w.name',
                        2 => 'wd.title',
                        3 => 'ks.name',
                        4 => 'kt.name',
                        5 => 'ws.name',
                    ];
        
                    if (isset($columns[$columnIndex])) {
                        $order[] = "{$columns[$columnIndex]} {$dir}";
                    }
                }
            }
        
            return !empty($order) ? implode(', ', $order) : '';
        }
        
        /**
         * Build LIMIT clause based on request parameters.
         */
        private function buildLimitClause(Request $request)
        {
            if ($request->has('start') && $request->has('length')) {
                return "LIMIT {$request->start}, {$request->length}";
            }
        
            return '';
        }

        public function getLecturerId(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $searchTerm = $request->input('searchTerm');
        if (!$searchTerm) {
            return response()->json(['error' => 'Search term is required'], 400);
        }

        $lecturers = $this->documentModel->getLecturer($searchTerm);

        $result = [];
        foreach ($lecturers as $lecturer) {
            $result[] = [
                'id' => $lecturer->id,
                'text' => '( ' . $lecturer->master_data_number . ') - ' . $lecturer->master_data_fullname
            ];
        }

        return response()->json($result);
    }

        public function getKnowledgeType(Request $request)
        {
            if (!$request->ajax()) {
                return response()->json(['error' => 'Invalid request'], 400);
            }

            $session = session('user_doc');
            $workflowId = $request->input('id');

        
            $knowledgeTypes = $this->documentModel->getKnowledgeTypeByWorkflowId($workflowId);

            $memberProdi = $this->documentModel->getMemberProdi($session['id']);

            $prodiName = $memberProdi ?? '';
            $temp = explode(" ", $prodiName);
            $kode = '6'; 
            if ($temp[0] == 'S1') {
                $kode = '4';
            } elseif ($temp[0] == 'S2') {
                $kode = '5';
            }

            $response = [
                'knowledge_types' => $knowledgeTypes,
                'kode' => $kode,
            ];

            return response()->json($response);
        }

        public function getSubjects(Request $request)
    {
        $searchTerm = $request->input('searchTerm');
        $subjects = $this->documentModel->getSubject($searchTerm);

        $results = [];
        foreach ($subjects as $subject) {
            $results[] = ['id' => $subject->id, 'text' => $subject->name];
        }

        return response()->json($results);
    }
    public function getFile(Request $request)
{
    if (!$request->ajax()) {
        return response()->json(['error' => 'Invalid request'], 400);
    }

    $id = $request->input('id');
    $action = $request->input('action');

    $files = ($id == 13 && $action == 'insert') 
        ? $this->documentModel->getUploadTypeByWorkflowId2($id)
        : $this->documentModel->getUploadTypeByWorkflowId($id);

    $results = [];
    foreach ($files as $file) {
        $results[] = [
            'id' => $file->id,
            'upload_type_id' => $file->upload_type_id ?? $file->id, // Fallback ke id jika tidak ada
            'title' => $file->title,
            'name' => $file->name,
            'extension' => $file->extension
        ];
    }

    return response()->json($results);
}
    public function store(Request $request)
{
    $inp = $request->except('_token');
    $master_subject = $request->input('master_subject');
    $upload_type = $request->file('upload_type');
    $session = session('user_doc');
    $username = $session['master_data_user'] ?? Auth::user()->master_data_user;
    
    if ($inp['workflow_id'] == 1) {
        $check = $this->documentModel->checkDuplicateWorkflowDocument(Auth::user()->id);
        if ($check) {
            return redirect()->route('dokumen.index')->with('alert', 'Data laporan sudah pernah dibuat. Silahkan melakukan edit pada laporan tersebut.');
        }
    }

    $workflow = $this->documentModel->getWorkflowById($inp['workflow_id'])->first();
    $date_now = Carbon::now('Asia/Jakarta');
    $inp['member_id'] = $session['id'];
    $inp['status'] = '1';
    $inp['created_by'] = $session['master_data_user'] ?? Auth::user()->master_data_user;
    $inp['updated_by'] = $session['master_data_user'] ?? Auth::user()->master_data_user;
    $inp['created_at'] = $date_now;
    $inp['updated_at'] = $date_now;
    $inp['latest_state_id'] = $workflow->start_state_id;

    if (isset($inp['knowledge_subject_id']) && is_array($inp['knowledge_subject_id'])) {
        $inp['knowledge_subject_id'] = implode(',', $inp['knowledge_subject_id']);
    }

    $wd_id = $this->documentModel->create($inp)->id;

    if ($master_subject && is_array($master_subject)) {
        foreach ($master_subject as $subject_id) {
            $this->documentModel->addCustom([
                'workflow_document_id' => $wd_id,
                'master_subject_id' => $subject_id
            ], 'workflow_document_subject');
        }
    }

    $state = $this->documentModel->getWorkflowStateById($workflow->start_state_id, $workflow->id)->first();
    $state_data = [
        'document_id' => $wd_id,
        'member_id' => $session['id'],
        'state_id' => $workflow->start_state_id,
        'open_date' => $date_now
    ];
    if ($state->rule_type == 1) {
        $state_data['allowed_member_id'] = $session['id'];
        $state_data['open_by'] = $session['id'];
    }
    $this->documentModel->addCustom($state_data, 'workflow_document_state');

    if ($upload_type && is_array($upload_type)) {
        $upload = [];
        $dt = $this->documentModel->getUploadTypeByWorkflowId($workflow->id);
        foreach ($dt as $row) {
            $upload[$row->id] = [
                'id' => $row->id,
                'ext' => $row->extension,
                'name' => $row->name,
                'title' => $row->title
            ];
        }

        \Log::info('Available Upload Types:', $upload);

        $upPath = storage_path('app/public/documents/' . $username . '/');
        if (!file_exists($upPath)) {
            mkdir($upPath, 0777, true);
        }

        foreach ($upload_type as $index => $file) {
            if ($file != "") {
                // Get original file extension
                $originalExtension = $file->getClientOriginalExtension();
                
                // Get file type from request or use first available type
                $uploadTypeId = $request->input('file_types')[$index] ?? array_key_first($upload);
                $typeInfo = $upload[$uploadTypeId] ?? reset($upload);
                
                // Generate standardized filename
                $newfilename = $username . '_' . $wd_id . '_' . $typeInfo['name'] . '.' . $originalExtension;
                
                if ($file->move($upPath, $newfilename)) {
                    \Log::info('File moved successfully', [
                        'original' => $file->getClientOriginalName(),
                        'saved_as' => $newfilename
                    ]);

                    $insertData = [
                        'document_id' => $wd_id,
                        'upload_type_id' => $uploadTypeId,
                        'name' => $typeInfo['title'],
                        'location' => $newfilename,
                        'created_by' => $username ?: 'system',
                        'created_at' => now(),
                        'updated_by' => $username ?: 'system',
                        'updated_at' => now()
                    ];

                    if (!empty($insertData['document_id']) && !empty($insertData['upload_type_id']) && !empty($insertData['location'])) {
                        $insertResult = $this->documentModel->addCustom($insertData, 'workflow_document_file');

                        if ($insertResult) {
                            \Log::info('File record inserted successfully', ['file' => $newfilename]);
                        } else {
                            \Log::error('Failed to insert file record', ['file' => $newfilename]);
                        }
                    } else {
                        \Log::error('Missing required parameters for file insert', ['data' => $insertData]);
                    }
                } else {
                    \Log::error('Failed to move file', ['file' => $newfilename]);
                }
            }
        }
    }
    
    return redirect()->route('dokumen.index')->with('success', 'Document uploaded successfully.');
}

    public function edit($id)
    {
        if (!Auth::check()) {
            return redirect('/');
        }
    
        $session = session('user_doc');
    
        $document = $this->documentModel->getWorkflowDocumentbyId($id, $session['membertype']);

        $comments = $this->documentModel->getDocumentCommentsWithReplies($document->id);
        if (!$document) {
            return redirect()->route('document.index')->with('error', 'Document not found');
        }
    
        if (($document->start_state_id ?? null) == ($document->latest_state_id ?? null) &&
            ($document->member_id ?? null) != ($session['id'] ?? null) && ($session['membertype'] ?? null) != '1') {
            return redirect()->route('document.index');
        }
    
        $nextStates = $this->documentModel->getNextState($document->latest_state_id ?? 0);
        $next = ['' => 'Pilih Next State'];
        foreach ($nextStates as $state) {
            $next[$state->id ?? ''] = $state->name ?? 'Unknown State';
        }
    
        $units = $this->documentModel->getUnit();
        $unitOptions = [];
        foreach ($units as $unit) {
            $unitOptions[$unit->C_KODE_PRODI ?? ''] = $unit->NAMA_PRODI ?? '';
        }
    
        $masterSubjects = $this->documentModel->getDocumentMasterSubjectByUnitId($document->course_code ?? '', $document->id);
        $masterSubjectOptions = "";
        $masterSubjectView = "";
        foreach ($masterSubjects as $subject) {
            $selected = ($subject->total ?? 0) != 0 ? 'selected' : '';
            if ($selected) {
                $masterSubjectView .= e($subject->code ?? '') . ' - ' . e($subject->name ?? '') . '<br>';
            }
            $masterSubjectOptions .= '<option value="' . e($subject->id ?? '') . '" ' . $selected . '>' 
                . e($subject->code ?? '') . ' - ' . e($subject->name ?? '') . '</option>';
        }
    
        $sdgsExisting = $this->documentModel->getDocumentSdgs($document->id);
        $sdgsExist = [];
        foreach ($sdgsExisting as $row) {
            if (isset($row->sdgs_kode)) {
                $sdgsExist[] = $row->sdgs_kode;
            }
        }
        $sdgsOptions = $this->documentModel->sdgs() ?? [];
        $sdgsView = '';
        foreach ($sdgsOptions as $key => $row) {
            if (in_array($key, $sdgsExist)) {
                $sdgsView .= e($row) . '<br>';
            }
        }
    
        $existingFiles = $this->documentModel->getDocumentFile($document->id) ?? [];
        $documentStates = $this->documentModel->getDocumentState($document->id) ?? [];
        $fileTypes = $this->documentModel->getUploadTypeByWorkflowId($document->workflow_id ?? 0) ?? [];
        $comments = $this->documentModel->getDocumentCommentsWithReplies($document->id);
        $userId = Auth::id();
        $isAdmin = Auth::user()->member_type_id == 1;
        $isLecturer = $document->lecturer_id == $userId || $document->lecturer2_id == $userId;
        $isOwner = $document->member_id == $userId;

        $canDownload = $isAdmin || $isLecturer || $isOwner;
        
        return view('dokumen.edit', [
            'document' => $document,
            'canDownload' => $canDownload,
            'next' => $next,
            'unitOptions' => $unitOptions,
            'masterSubjectOptions' => $masterSubjectOptions,
            'masterSubjectView' => $masterSubjectView,
            'sdgsExisting' => $sdgsExisting,
            'sdgs' => $sdgsOptions,
            'sdgsView' => $sdgsView,
            'sdgsExist' => $sdgsExist,
            'existingFiles' => $existingFiles,
            'documentStates' => $documentStates,
            'title' => 'Edit Document',
            'icon' => 'icon-book',
            'nextStates' => $nextStates,
            'fileTypes' => $fileTypes,
            'comments' => $comments
        ]);
    }

    public function deleteComment(Request $request)
{
    $commentId = $request->input('id');

    try {
        $comment = DB::table('workflow_comment')->where('id', $commentId)->first();

        if (!$comment) {
            return response()->json(['success' => false, 'message' => 'Komentar tidak ditemukan.']);
        }

        DB::table('workflow_comment')->where('id', $commentId)->delete();

        return response()->json(['success' => true, 'message' => 'Komentar berhasil dihapus.']);
    } catch (\Exception $e) {
        \Log::error('Error deleting comment:', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus komentar.']);
    }
}
public function download($documentId, $fileId)
{
    if (!Auth::check()) {
        return redirect('/login');
    }

    try {
        if (!is_numeric($documentId) || !is_numeric($fileId)) {
            abort(400, 'Invalid document or file ID');
        }

        $fileRecord = DB::table('workflow_document_file')
            ->where('id', $fileId)
            ->where('document_id', $documentId)
            ->first();

        if (!$fileRecord) {
            \Log::error('File not found in database', [
                'documentId' => $documentId,
                'fileId' => $fileId
            ]);
            abort(404, 'File not found');
        }

        $document = DB::table('workflow_document')
            ->where('id', $documentId)
            ->first();

        if (!$document) {
            \Log::error('Document not found in database', [
                'documentId' => $documentId
            ]);
            abort(404, 'Document not found');
        }

        $userId = Auth::id();
        $isAdmin = Auth::user()->member_type_id == 1; 
        $isLecturer = $document->lecturer_id == $userId || $document->lecturer2_id == $userId;
        $isOwner = $document->member_id == $userId;

        if (!$isAdmin && !$isLecturer && !$isOwner) {
            \Log::warning('Unauthorized file download attempt', [
                'userId' => $userId,
                'documentId' => $documentId,
                'fileId' => $fileId
            ]);
            abort(403, 'You do not have permission to download this file');
        }

        $userFolder = $document->created_by ?? 'unknown_user';

        $possiblePaths = [
            storage_path("app/public/documents/{$userFolder}/{$fileRecord->location}"),
            storage_path("app/public/documents/{$fileRecord->location}"),
            storage_path("app/documents/{$userFolder}/{$fileRecord->location}"),
        ];

        $filePath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $filePath = $path;
                break;
            }
        }

        if (!$filePath) {
            \Log::error('File not found in storage', [
                'location' => $fileRecord->location,
                'paths_checked' => $possiblePaths
            ]);
            abort(404, 'File not found in storage');
        }

        $mime = mime_content_type($filePath);
        $originalExtension = pathinfo($fileRecord->location, PATHINFO_EXTENSION);
        $safeFilename = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|', ' '], '_', $fileRecord->name);

        if (empty($originalExtension)) {
            $extensionMap = [
                'image/png' => 'png',
                'image/jpeg' => 'jpg',
                'image/webp' => 'webp',
                'application/pdf' => 'pdf',
                'application/msword' => 'doc',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            ];
            $originalExtension = $extensionMap[$mime] ?? pathinfo($filePath, PATHINFO_EXTENSION);
        }

        $downloadFilename = $safeFilename;
        if (!empty($originalExtension) && !preg_match('/\.' . preg_quote($originalExtension, '/') . '$/i', $safeFilename)) {
            $downloadFilename .= '.' . strtolower($originalExtension);
        }

        \Log::debug('Download processed', [
            'original_name' => $fileRecord->name,
            'location' => $fileRecord->location,
            'mime_type' => $mime,
            'final_filename' => $downloadFilename,
            'file_path' => $filePath
        ]);

        return response()->download($filePath, $downloadFilename, [
            'Content-Type' => $mime,
            'Content-Length' => filesize($filePath),
        ]);

    } catch (\Exception $e) {
        \Log::error('File download error:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'file_id' => $fileId,
            'document_id' => $documentId
        ]);
        abort(500, 'An error occurred while processing your request');
    }
}

public function update(Request $request, $id)
    {
        Log::info('Update Request Data:', $request->all());

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired'
            ], 401);
        }

        DB::beginTransaction();

        try {
            $document = $this->documentModel->find($request->wd_id);
            if (!$document) {
                throw new \Exception('Document not found');
            }

            $userId = Auth::id();
            $user = Auth::user();
            $isAdmin = $user->member_type_id == 1;
            $isLecturer = $document->lecturer_id == $userId || $document->lecturer2_id == $userId;
            $isOwner = $document->member_id == $userId;

            // Handle comments
            if ($request->filled('parent_id')) {
                $this->handleCommentReply($request, $document, $userId, $user);
            }

            if ($request->filled('new_comment')) {
                $this->handleNewComment($request, $document, $userId);
            }

            // Handle state changes
            if ($request->filled('latest_state_id')) {
                $this->handleStateChange($request, $document, $userId, $user, $isLecturer, $isAdmin, $isOwner);
            }

            // Handle document updates
            if ($this->shouldUpdateDocument($request)) {
                $this->handleDocumentUpdate($request, $document, $user, $isLecturer);
            }

            // Handle file uploads - NEW CODE
            if ($request->hasFile('upload_type')) {
                $this->handleFileUploads($request, $document, $user);
            }

            DB::commit();
            
            Log::info('Document updated successfully', [
                'document_id' => $document->id,
                'user_id' => $userId
            ]);
            
            return redirect()->route('dokumen.edit', $document->id)
                ->with('success', 'Dokumen berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update error:', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Gagal memperbarui dokumen: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Handle file uploads for both create and update
     */
    private function handleFileUploads($request, $document, $user)
{
    $uploadFiles = $request->upload_type; // Ambil langsung dari array asosiatif
    $username = $user->master_data_user;

    foreach ($uploadFiles as $uploadTypeId => $file) {
        if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
            try {
                $typeInfo = $this->documentModel->getUploadTypeById($uploadTypeId);
                
                if (!$typeInfo) {
                    throw new \Exception("Upload type ID $uploadTypeId not found");
                }

                $originalExtension = strtolower($file->getClientOriginalExtension());
                $newFilename = sprintf('%s_%d_%s.%s',
                    $username,
                    $document->id,
                    $typeInfo->name,
                    $originalExtension
                );

                $uploadPath = storage_path('app/public/documents/' . $username . '/');
                $file->move($uploadPath, $newFilename);

                $this->documentModel->addCustom([
                    'document_id' => $document->id,
                    'upload_type_id' => $uploadTypeId,
                    'name' => $typeInfo->title,
                    'location' => $newFilename,
                    'created_by' => $username,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'updated_by' => $username
                ], 'workflow_document_file');

            } catch (\Exception $e) {
                Log::error('File upload failed', [
                    'type_id' => $uploadTypeId,
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }
    }
}
/**
 * Handle comment reply if parent_id and comment content exists
 */
private function handleCommentReply($request, $document, $userId, $user)
{
    if (empty(trim($request->comment))) {
        return; 
    }

    Log::info('Processing reply comment', [
        'parent_id' => $request->parent_id,
        'has_comment' => $request->has('comment')
    ]);

    $parentComment = DB::table('workflow_comment')
        ->where('id', $request->parent_id)
        ->where('document_id', $document->id)
        ->first();

    if (!$parentComment) {
        throw new \Exception('Parent comment not found or not belonging to this document');
    }

    $replyId = DB::table('workflow_comment')->insertGetId([
        'document_id' => $document->id,
        'parent_id' => $request->parent_id,
        'comment' => $request->comment,
        'document_state_id' => $request->latest_state_id_old ?? null,
        'member_id' => $userId,
        'created_at' => Carbon::now()
    ]);

    if (!$replyId) {
        throw new \Exception('Failed to insert reply - no ID returned');
    }

    Log::info('Reply inserted successfully', ['reply_id' => $replyId]);

    if ($parentComment->member_id != $userId) {
        $messages = $user->master_data_fullname . " membalas komentar Anda pada dokumen " . $document->title;
        
        $notificationData = [
            'notif_id_member' => $parentComment->member_id,
            'notif_type' => 'komentar',
            'notif_content' => $messages,
            'notif_date' => Carbon::now(),
            'notif_status' => 'unread',
            'notif_id_detail' => $document->id,
        ];

        DB::table('notification_mobile')->insert($notificationData);
        Log::info('Notification sent for reply', [
            'to_user' => $parentComment->member_id,
            'reply_id' => $replyId
        ]);
    }
}

/**
 * Handle new comment creation
 */
private function handleNewComment($request, $document, $userId)
{
    if (empty(trim($request->new_comment))) {
        return;
    }

    Log::info('Processing new comment', [
        'document_id' => $document->id,
        'user_id' => $userId
    ]);

    $commentId = DB::table('workflow_comment')->insertGetId([
        'document_id' => $document->id,
        'comment' => $request->new_comment,
        'document_state_id' => $request->latest_state_id_old ?? null,
        'member_id' => $userId,
    ]);

    if (!$commentId) {
        throw new \Exception('Failed to insert new comment');
    }

    Log::info('New Comment inserted:', [
        'comment_id' => $commentId,
        'document_id' => $document->id
    ]);
}

/**
 * Handle document state changes
 */
private function handleStateChange($request, $document, $userId, $user, $isLecturer, $isAdmin, $isOwner)
{
    if ($isLecturer && !$isAdmin && !$isOwner) {
        $validator = Validator::make($request->all(), [
            'latest_state_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        if ($request->latest_state_id != $document->latest_state_id) {
            $this->updateDocumentState($document, $request->latest_state_id, $userId);
            
            $messages = $user->master_data_fullname . " meminta revisi pada dokumen.";
            $itemnotif = [
                'notif_id_member' => $document->created_by,
                'notif_type' => 'revisi',
                'notif_content' => $messages,
                'notif_date' => Carbon::now(),
                'notif_status' => 'unread',
                'notif_id_detail' => $document->id,
            ];

            DB::table('notification_mobile')->insert($itemnotif);

            $this->handleStateChangeNotifications($document, $user, $request->latest_state_id, $request->new_comment);
        }
        return;
    }

    if ($request->latest_state_id != $document->latest_state_id) {
        $this->updateDocumentState($document, $request->latest_state_id, $userId);

        $messages = $user->master_data_fullname . " telah memperbarui status dokumen.";
        $itemnotif = [
            'notif_id_member' => $document->lecturer_name,
            'notif_type' => 'dokumen',
            'notif_content' => $messages,
            'notif_date' => Carbon::now(),
            'notif_status' => 'unread',
            'notif_id_detail' => $document->id,
        ];

        DB::table('notification_mobile')->insert($itemnotif);
        $this->handleStateChangeNotifications($document, $user, $request->latest_state_id);
    }
}

/**
 * Update document state
 */
private function updateDocumentState($document, $newStateId, $userId)
{
    $closed = DB::table('workflow_document_state')
        ->where('document_id', $document->id)
        ->whereNull('close_date')
        ->update(['close_date' => Carbon::now()]);

    if ($closed === false) {
        throw new \Exception('Failed to close current document state');
    }

    $stateId = DB::table('workflow_document_state')->insertGetId([
        'document_id' => $document->id,
        'state_id' => $newStateId,
        'member_id' => $userId,
        'open_date' => Carbon::now(),
        'open_by' => $userId,
    ]);

    if (!$stateId) {
        throw new \Exception('Failed to create new document state');
    }

    $document->update(['latest_state_id' => $newStateId]);
}

/**
 * Handle document metadata updates
 */

private function handleDocumentUpdate($request, $document, $user, $isLecturer)
{
    if ($isLecturer) {
        $validator = Validator::make($request->all(), [
            'latest_state_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        if ($request->latest_state_id != $document->latest_state_id) {
            $this->updateDocumentState($document, $request->latest_state_id, $user->id);
        }

        return;
    }

    $validator = Validator::make($request->all(), [
        'wd_id' => 'required|integer',
        'workflow_id' => 'required|integer',
        'title' => 'required|string|max:255',
        'knowledge_subject_id' => 'required|integer',
        'abstract_content' => 'required|string',
        'lecturer2_id' => 'nullable|integer',
        'sdgs' => 'nullable|array',
        'master_subject' => 'nullable|array',
    ]);

    if ($validator->fails()) {
        throw new \Illuminate\Validation\ValidationException($validator);
    }

    $updateData = [
        'title' => $request->title,
        'knowledge_subject_id' => $request->knowledge_subject_id,
        'abstract_content' => $request->abstract_content,
        'course_code' => $request->course_code,
        'updated_at' => Carbon::now(),
        'updated_by' => $user->master_data_user,
    ];

    $updated = $document->update($updateData);
    if (!$updated) {
        throw new \Exception('Failed to update document data');
    }

    $this->updateDocumentRelations($document->id, $request->sdgs, $request->master_subject);
}

/**
 * Check if document should be updated
 */
private function shouldUpdateDocument($request)
{
    return $request->filled('title') || 
           $request->filled('abstract_content') || 
           $request->filled('knowledge_subject_id') || 
           $request->filled('course_code') || 
           $request->filled('sdgs') || 
           $request->filled('master_subject');
}

// Keep the existing handleStateChangeNotifications and updateDocumentRelations methods

    /**
     * Handle email notifications for state changes
     */
    private function handleStateChangeNotifications($document, $user, $newStateId, $comment = null)
    {
        $stateName = DB::table('workflow_state')->find($newStateId)->name ?? 'status baru';
        $owner = DB::table('member')->where('id', $document->member_id)->first();
        $testEmail = 'dreadfulleviathan@gmail.com';
        if ($owner && $owner->master_data_email) {
            $emailContent = "Hai <b>{$owner->master_data_fullname}</b>,<br><br>";
            
            if ($newStateId == 2) {
                $emailContent .= "Dosen pembimbing Anda, <b>{$user->master_data_fullname}</b>, " .
                                "telah meminta revisi pada dokumen Anda dengan judul \"{$document->title}\".<br><br>" .
                                "Komentar: " . ($comment ?: "Tidak ada komentar spesifik") . "<br><br>";
            } else {
                $emailContent .= "Dokumen Anda dengan judul \"{$document->title}\" " .
                                "telah diperbarui statusnya menjadi <b>{$stateName}</b> oleh <b>{$user->master_data_fullname}</b>.<br><br>";
            }
            
            $emailContent .= "Untuk detail lebih lanjut, silakan kunjungi <a target='_blank' href='https://openlibrary.telkomuniversity.ac.id'>Openlibrary</a>.<br><br>" .
                        "Terima kasih,<br>" .
                        "Tim Open Library Telkom University";
            
            $subject = $newStateId == 2 ? 'Permintaan Revisi Dokumen' : 'Perubahan Status Dokumen';
            
            Helpers::SendEmail(
                $testEmail,
                $subject,
                $emailContent,
                'Open Library Telkom University',
                $owner->master_data_fullname
            );
        }
        
        $approvedStatuses = [3, 4, 52, 53, 64, 91];
        if (in_array($newStateId, $approvedStatuses)) {
            $lecturers = DB::table('member')
                ->whereIn('id', [$document->lecturer_id, $document->lecturer2_id])
                ->whereNotNull('master_data_email')
                ->get();
            
            foreach ($lecturers as $lecturer) {
                $lecturerEmailContent = "Hai <b>{$lecturer->master_data_fullname}</b>,<br><br>" .
                    "Dokumen dengan judul \"{$document->title}\" " .
                    "telah mendapatkan approval dari <b>{$user->master_data_fullname}</b>.<br><br>" .
                    "Status saat ini: <b>{$stateName}</b><br><br>" .
                    "Untuk detail lebih lanjut, silakan kunjungi <a target='_blank' href='https://openlibrary.telkomuniversity.ac.id'>Openlibrary</a>.<br><br>" .
                    "Terima kasih,<br>" .
                    "Tim Open Library Telkom University";
                
                Helpers::SendEmail(
                    $testEmail,
                    'Approval Dokumen',
                    $lecturerEmailContent,
                    'Open Library Telkom University',
                    $lecturer->master_data_fullname
                );
            }
        }
    }

    /**
     * Update document relations (SDGs and Subjects)
     */
    private function updateDocumentRelations($documentId, $sdgs, $subjects)
    {
        if ($sdgs) {
            DB::table('workflow_document_sdgs')
                ->where('document_id', $documentId)
                ->delete();

            foreach ($sdgs as $sdg) {
                DB::table('workflow_document_sdgs')->insert([
                    'document_id' => $documentId,
                    'sdgs_kode' => $sdg,
                ]);
            }
        }

        if ($subjects) {
            DB::table('workflow_document_subject')
                ->where('workflow_document_id', $documentId)
                ->delete();

            foreach ($subjects as $subjectId) {
                DB::table('workflow_document_subject')->insert([
                    'workflow_document_id' => $documentId,
                    'master_subject_id' => $subjectId,
                ]);
            }
        }
    }
}