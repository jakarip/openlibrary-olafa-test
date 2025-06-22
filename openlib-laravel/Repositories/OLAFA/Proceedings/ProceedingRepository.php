<?php 

namespace App\Repositories\OLAFA\Proceedings;

use App\Models\ProceedingSubjectModel;
use App\Models\ProceedingTitleModel;

class ProceedingRepository
{
    // Subject Methods
    public function getAllSubjects()
    {
        return ProceedingSubjectModel::all();
    }

    public function getSubjectById($id)
    {
        return ProceedingSubjectModel::find($id);
    }

    public function getSubjectsWithTitles()
    {
        return ProceedingSubjectModel::with('titles')->get();
    }

    public function saveSubject(array $data, $id = null)
    {
        $subject = $id ? ProceedingSubjectModel::find($id) : new ProceedingSubjectModel();
        $subject->fill($data);
        $subject->save();

        return $subject;
    }

    public function deleteSubject($id)
    {
        $subject = ProceedingSubjectModel::find($id);

        if ($subject) {
            if ($subject->titles()->exists()) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete subject. There are titles associated with this subject.'
                ];
            }

            $subject->delete();
            return [
                'success' => true,
                'message' => 'Subject deleted successfully.'
            ];
        }

        return [
            'success' => false,
            'message' => 'Subject not found.'
        ];
    }

    // Title Methods
    public function getTitleById($id)
    {
        return ProceedingTitleModel::find($id);
    }

    public function saveTitle(array $data, $id = null)
    {
        
        $title = $id ? ProceedingTitleModel::find($id) : new ProceedingTitleModel();
        $title->fill($data);
        $title->save();

        return $title;
    }

    public function deleteTitle($id)
    {
        $title = ProceedingTitleModel::find($id);

        if ($title) {
            $title->delete();
            return true;
        }

        return false;
    }
}

?>