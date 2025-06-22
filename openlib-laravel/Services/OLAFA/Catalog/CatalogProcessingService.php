<?php

namespace App\Services\OLAFA\Catalog;

use App\Repositories\OLAFA\Catalog\CatalogRepository;

class CatalogProcessingService
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

    public function getType()
    {
        return $this->catalogRepository->getType();
    }

    public function getBooksOnProcess($filters)
    {
        $where = '';
        $where2 = '';

        if (!empty($filters['startDate']) && !empty($filters['endDate'])) {
            $where .= "AND ks.created_at BETWEEN '{$filters['startDate']} 00:00:00' AND '{$filters['endDate']} 23:59:59' ";
        }

        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $where .= "AND ks.knowledge_type_id = '{$filters['type']}' ";
        }

        if (!empty($filters['barcode'])) {
            $where .= "AND ks.code = '{$filters['barcode']}' ";
        }

        if (!empty($filters['origination']) && $filters['origination'] !== 'all') {
            $where .= "AND ks.origination = '{$filters['origination']}' ";
        }

        if (!empty($filters['status'])) {
            $where .= "AND ks.status = '{$filters['status']}' ";
        }

        if (isset($filters['klasifikasi'])) {
            if ($filters['klasifikasi'] == '0') {
                $where2 = "WHERE (codes2 = '0' OR (codes2 > 0 AND codes2 < 100))";
            } else {
                $where2 = "WHERE codes2 >= {$filters['klasifikasi']}00 AND codes2 < {$filters['klasifikasi']}99";
            }
        }

        if (!empty($filters['location']) && count($filters['location']) > 0) {
            $where .= "AND ks.item_location_id IN (" . implode(',', $filters['location']) . ") ";
        }
        return $this->catalogRepository->getBooksOnProcess($where, $where2);
    }

    public function updateStatus($ids, $updatedBy)
    {
        $item = [
            'status' => '1',
            'updated_by' => $updatedBy,
            'updated_at' => now(),
        ];

        foreach ($ids as $id) {
            $this->catalogRepository->updateStatus($id, $item);
        }

        return true;
    }
}
