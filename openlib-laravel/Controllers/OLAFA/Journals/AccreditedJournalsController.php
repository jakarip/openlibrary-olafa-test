<?php

namespace App\Http\Controllers\OLAFA\Journals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\OLAFA\Journals\AccreditedJournalsRepository;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class AccreditedJournalsController extends Controller
{
    protected $repository;

    public function __construct(AccreditedJournalsRepository $repository)
    {
        $this->repository = $repository;
    }
    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        $subjects = $this->repository->getAllSubjects();
        return view('olafa.jurnal.sumberJurnal', compact('subjects'));
    }

    public function dt()
    {
        $data = $this->repository->getSubjectsWithTitles();

        // Flatten the data
        $flattenedData = [];
        foreach ($data as $subject) {
            foreach ($subject->titles as $title) {
                $flattenedData[] = (object) [
                    'id' => $title->id,
                    'subject_name' => $subject->subject_name,
                    'sinta_level' => $title->sinta_level,
                    'title_name' => $title->title_name,
                    'link' => $title->link
                ];
            }
        }

        return datatables()->of(collect($flattenedData))
            ->addColumn('action', function ($db) {
                return '<div class="btn-group">
                    <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>
                        <li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>
                    </ul>
                </div>';
            })
            ->rawColumns(['action'])->toJson();
    }

    public function dt_subjects()
    {
        $data = $this->repository->getAllSubjects();

        return datatables($data)
            ->addColumn('action', function ($db) {
                return '<div class="btn-group">
                    <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>
                        <li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>
                    </ul>
                </div>';
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function save(Request $request)
    {
        try {
            $data = $request->input('inp', []); // Ambil data dari key 'inp'
            $id = $request->id;

            if (isset($data['subject_name'])) {
                $this->repository->saveSubject($data, $id);
            } else {
                $this->repository->saveTitle($data, $id);
            }

            return response()->json(['status' => 'success', 'message' => 'Data saved successfully']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }

    
    public function getById($id)
    {
        $data = $this->repository->getTitleById($id);
        return response()->json($data);
    }

    public function getSubjects()
    {
        $subjects = $this->repository->getAllSubjects();
        return response()->json($subjects);
    }

    public function getSubjectById($id)
    {
        $subject = $this->repository->getSubjectById($id);
        return response()->json($subject);
    }

    public function delete(Request $request)
    {
        try {
            $id = $request->id;
            $type = $request->type;
    
            if ($type === 'subject') {
                // Validasi dan hapus subject
                $result = $this->repository->deleteSubject($id);
    
                if (!$result['success']) {
                    return response()->json(['status' => 'error', 'message' => $result['message']]);
                }
            } else {
                // Hapus title
                $success = $this->repository->deleteTitle($id);
    
                if (!$success) {
                    return response()->json(['status' => 'error', 'message' => 'Title not found']);
                }
            }
    
            return response()->json(['status' => 'success', 'message' => 'Data deleted successfully']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }

    public function updateSubject(Request $request, $id)
    {
        try {
            $data = ['subject_name' => $request->subject_name];
            $this->repository->saveSubject($data, $id);
    
            return response()->json(['status' => 'success', 'message' => 'Subject updated successfully']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }
}
