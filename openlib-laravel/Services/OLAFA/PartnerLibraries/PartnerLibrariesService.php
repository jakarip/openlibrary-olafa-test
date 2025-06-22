<?php 
namespace App\Services\OLAFA\PartnerLibraries;

use App\Repositories\OLAFA\PartnerLibraries\PartnerLibrariesRepository;

class PartnerLibrariesService
{
    protected $repository;

    public function __construct(PartnerLibrariesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllLibraries()
    {
        return $this->repository->getAll();
    }

    public function getLibraryById($id)
    {
        return $this->repository->getById($id);
    }

    public function saveLibrary(array $data, $file = null, $id = null)
    {
        // Simpan data ke database
        $library = $this->repository->save($data, $id);

        // Simpan logo jika ada
        if ($file) {
            $this->repository->saveLogo($file, $library);
        }

        return $library;
    }

    public function deleteLibrary($id)
    {
        return $this->repository->delete($id);
    }
}

?>