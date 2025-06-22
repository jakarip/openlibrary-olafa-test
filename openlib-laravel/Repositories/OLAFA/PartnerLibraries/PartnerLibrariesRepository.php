<?php 
namespace App\Repositories\OLAFA\PartnerLibraries;

use App\Models\LibrarySourceModel;
use Illuminate\Support\Facades\Storage;

class PartnerLibrariesRepository
{
    public function getAll()
    {
        return LibrarySourceModel::all();
    }

    public function getById($id)
    {
        return LibrarySourceModel::find($id);
    }

    public function save(array $data, $id = null)
    {
        $library = $id ? LibrarySourceModel::find($id) : new LibrarySourceModel();

        foreach ($data as $key => $value) {
            if ($key !== 'logo') {
                $library->$key = $value;
            }
        }

        $library->save();

        return $library;
    }

    public function saveLogo($file, $library)
    {
        if ($file && $file->isValid()) {
            $extension = $file->getClientOriginalExtension();
            $newFileName = time() . '.' . $extension;

            // Simpan file ke storage
            Storage::putFileAs('public/olafa/mitra', $file, $newFileName);

            // Hapus file lama jika ada
            if ($library->logo_name && Storage::exists('public/olafa/mitra/' . $library->logo_name)) {
                Storage::delete('public/olafa/mitra/' . $library->logo_name);
            }

            // Simpan nama file baru ke database
            $library->logo_name = $newFileName;
            $library->save();

            return $newFileName;
        }

        return null;
    }

    public function delete($id)
    {
        $library = LibrarySourceModel::find($id);

        if ($library) {
            // Hapus file logo jika ada
            if ($library->logo_name && Storage::exists('public/olafa/mitra/' . $library->logo_name)) {
                Storage::delete('public/olafa/mitra/' . $library->logo_name);
            }

            // Hapus data dari database
            $library->delete();

            return true;
        }

        return false;
    }
}
?>