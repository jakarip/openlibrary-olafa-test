<?php 

namespace App\Services\OLAFA\Catalog;

use App\Repositories\OLAFA\Catalog\CatalogRepository;

class CatalogStatisticsService
{
    protected $catalogRepository;

    public function __construct(CatalogRepository $catalogRepository)
    {
        $this->catalogRepository = $catalogRepository;
    }

    public function getLocations()
    {
        return $this->catalogRepository->getLocations();
    }

    public function getStatistics($filters)
    {
        $where = '';
        if (!empty($filters['startDate']) && !empty($filters['endDate'])) {
            $where = "and kk.entrance_date BETWEEN '{$filters['startDate']} 00:00:00' AND '{$filters['endDate']} 23:59:59'";
        }

        return $this->catalogRepository->getAllKnowledgeType($where);
    }

    public function getDetailStatistics($id, $type)
    {
        $data = $this->catalogRepository->getDetailStatistics($id, $type);

        $statuses = [
            1 => "Tersedia",
            2 => "Dipinjam",
            3 => "Rusak",
            4 => "Hilang",
            5 => "Expired",
            6 => "Hilang Diganti",
            7 => "Sedang Diproses",
            8 => "Cadangan",
            9 => "Weeding"
        ];

        foreach ($data as $dt) {
            $dt->status = $statuses[$dt->status] ?? 'Unknown';
            $dt->origination = $dt->origination == '1' ? 'Beli' : 'Sumbangan';
        }

        return $data;
    }
}
?>