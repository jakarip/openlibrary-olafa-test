<?php 
namespace App\Services\OLAFA\Catalog;

use App\Repositories\OLAFA\Catalog\CatalogRepository;

class CatalogListService
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

    public function getCatalogList($filters)
    {
        $where = '';
        $where2 = '';

        if (!empty($filters['startDate']) && !empty($filters['endDate'])) {
            $where .= "and kt.entrance_date BETWEEN '{$filters['startDate']} 00:00:00' AND '{$filters['endDate']} 23:59:59' ";
        }

        if (!empty($filters['type']) && is_array($filters['type'])) {
            $where .= "and kt.knowledge_type_id in (" . implode(',', $filters['type']) . ")";
        }

        if (!empty($filters['klasifikasi'])) {
            if ($filters['klasifikasi'] == '0') {
                $where2 = "where (codes2='0' or (codes2>0 and codes2<100))";
            } else {
                $where2 = "where codes2>=" . $filters['klasifikasi'] . "00 and codes2<" . $filters['klasifikasi'] . "99";
            }
        }
        // dd($where, $where2);
        return $this->catalogRepository->getCatalogList($where, $where2);
    }
}


?>