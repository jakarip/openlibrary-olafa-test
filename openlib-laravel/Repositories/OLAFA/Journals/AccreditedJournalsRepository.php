<?php 

namespace App\Repositories\OLAFA\Journals;

use App\Models\EJournalSubjectModel;
use App\Models\EJournalTitleModel;

class AccreditedJournalsRepository
{
    public function getAllSubjects()
    {
        return EJournalSubjectModel::all();
    }

    public function getSubjectById($id)
    {
        return EJournalSubjectModel::find($id);
    }

    public function getSubjectsWithTitles(){
        return EJournalSubjectModel::with('titles')->get();
    }

    public function getAllTitles()
    {
        return EJournalTitleModel::all();
    }

    public function getTitleById($id)
    {
        return EJournalTitleModel::find($id);
    }

    public function saveSubject(array $data, $id = null)
    {
        $subject = $id ? EJournalSubjectModel::find($id) : new EJournalSubjectModel();
        $subject->fill($data);
        $subject->save();

        return $subject;
    }

    public function saveTitle(array $data, $id = null)
    {
        $title = $id ? EJournalTitleModel::find($id) : new EJournalTitleModel();
        $title->fill($data);
        $title->save();

        return $title;
    }

    public function deleteSubject($id)
    {
        $subject = EJournalSubjectModel::find($id);

        if ($subject) {
            // Periksa apakah ada title yang berelasi dengan subject
            if ($subject->titles()->exists()) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete subject. There are titles associated with this subject.'
                ];
            }

            // Hapus subject jika tidak ada title yang berelasi
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

    public function deleteTitle($id)
    {
        $title = EJournalTitleModel::find($id);
        if ($title) {
            $title->delete();
            return true;
        }

        return false;
    }
}

?>