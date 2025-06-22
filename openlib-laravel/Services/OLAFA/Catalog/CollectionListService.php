<?php 

namespace App\Services\OLAFA\Catalog;

use App\Repositories\OLAFA\Catalog\CatalogRepository;

class CollectionListService
{
    protected $catalogRepository;

    public function __construct(CatalogRepository $catalogRepository)
    {
        $this->catalogRepository = $catalogRepository;
    }

    public function getKnowledgeType()
    {
        return $this->catalogRepository->getKnowledgeType();
    }

    public function getLocations()
    {
        return $this->catalogRepository->getLocations();
    }

    public function getCollectionList($filters)
    {
        $where = '';
        $where2 = '';

        if (!empty($filters['startDate']) && !empty($filters['endDate'])) {
            $where .= "AND ks.entrance_date BETWEEN '{$filters['startDate']} 00:00:00' AND '{$filters['endDate']} 23:59:59' ";
        }

        if (!empty($filters['location']) && is_array($filters['location'])) {
            $where .= "AND ks.item_location_id IN (" . implode(',', $filters['location']) . ") ";
        }

        if (!empty($filters['status']) && is_array($filters['status'])) {
            $where .= "AND ks.status IN (" . implode(',', $filters['status']) . ") ";
        }

        if (!empty($filters['type']) && is_array($filters['type'])) {
            $where .= "AND kt.knowledge_type_id IN (" . implode(',', $filters['type']) . ") ";
        }

        if (isset($filters['klasifikasi'])) {
            if ($filters['klasifikasi'] == '0') {
                $where2 = "WHERE (codes2='0' OR (codes2>0 AND codes2<100))";
            } else {
                $where2 = "WHERE codes2>=" . $filters['klasifikasi'] . "00 AND codes2<" . $filters['klasifikasi'] . "99";
            }
        }

        if (!empty($filters['origination'])) {
            $where .= "AND ks.origination='" . $filters['origination'] . "' ";
        }
        return $this->catalogRepository->getCollectionList($where, $where2);
    }
}

?>