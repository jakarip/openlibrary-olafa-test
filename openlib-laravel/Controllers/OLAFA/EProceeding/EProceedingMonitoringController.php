<?php

namespace App\Http\Controllers\OLAFA\EProceeding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EProceedingModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;

class EProceedingMonitoringController extends Controller
{
    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        $eProceedingModel = new EProceedingModel();
        $edition = $eProceedingModel->getEprocEdition();
        $list = $eProceedingModel->getEprocList();
        
        return view('olafa.eProceeding.monitorMaster',['edition' => $edition, 'list' => $list]);
    }

    public function dt(Request $request, EProceedingModel $eProceedingModel)
    {
        $choose = $request->input('edisi', );
        $type = $request->input('list', );
        $faculty = $request->input('faculty', );
        $choose =  '38';
        $type =  '1';
        $faculty = '7' ;

        // dd($choose, $type, $faculty);
        $data = [];
        
        $choose = !empty($choose) ? $choose : 0;
        $type = !empty($type) ? $type : 0;

        if (!empty($choose) && !empty($type)) {
            // $faculty is already assigned from the request input
            $list = $eProceedingModel->getEprocListById($type)->first();
            $jurusan = $eProceedingModel->getProdiByEprocList($faculty);
            $edition = $eProceedingModel->getEprocEditionById($choose)->first();
            // dd($jurusan);
            $no = 1;
            foreach ($jurusan as $row) {
                $data[] = [
                    'jurusan' => $row->c_kode_prodi,
                    'edisi' => $choose,
                    'no' => $no,
                    'nama_fakultas' => $row->nama_fakultas,
                    'nama_prodi' => $row->nama_prodi,
                    'tamasuk' => $eProceedingModel->totaltamasukbykodejur($row->c_kode_prodi, $edition->datestart, $edition->datefinish) ?? 0,
                    'jurnal' => $eProceedingModel->totaljurnalmasukbykodejur($row->c_kode_prodi, $edition->datestart, $edition->datefinish) ?? 0,
                    
                    'draft' => $eProceedingModel->totaldocbykodejurandstate($row->c_kode_prodi, '22', $edition->datestart, $edition->datefinish) ?? 0,
                    'revision' => $eProceedingModel->totaldocbykodejurandstate($row->c_kode_prodi, '2', $edition->datestart, $edition->datefinish) ?? 0,
                    'review' => $eProceedingModel->totaldocbykodejurandstate($row->c_kode_prodi, '1', $edition->datestart, $edition->datefinish) ?? 0,
                    'archieved' => $eProceedingModel->totaldocbykodejurandstate($row->c_kode_prodi, '5', $edition->datestart, $edition->datefinish) ?? 0,
                    'feasiblejurnal' => $eProceedingModel->totaldocbykodejurandstate($row->c_kode_prodi, '3', $edition->datestart, $edition->datefinish) ?? 0,
                    'eksternal' => $eProceedingModel->totaldocbykodejurandstate($row->c_kode_prodi, '52', $edition->datestart, $edition->datefinish) ?? 0,
                    'feasibleall' => $eProceedingModel->totaldocbykodejurandstate($row->c_kode_prodi, '4', $edition->datestart, $edition->datefinish) ?? 0,
                    'loapending' => $eProceedingModel->totaldocbykodejurandstate($row->c_kode_prodi, '64', $edition->datestart, $edition->datefinish) ?? 0,
                    'metadata' => $eProceedingModel->totaldocbykodejurandstate($row->c_kode_prodi, '91', $edition->datestart, $edition->datefinish) ?? 0,
                    
                    'archievedeksternal' => $eProceedingModel->totalarchivejurnalstatusbykodejur($row->c_kode_prodi, '52', $edition->datestart, $edition->datefinish) ?? 0,
                    'archievedloapending' => $eProceedingModel->totalarchivejurnalstatusbykodejur($row->c_kode_prodi, '64', $edition->datestart, $edition->datefinish) ?? 0,
                    'archievedfeasible' => $eProceedingModel->totalarchivejurnalstatusbykodejur($row->c_kode_prodi, '3', $edition->datestart, $edition->datefinish) ?? 0,
                    'archievedfeasibleall' => $eProceedingModel->totalarchivejurnalstatusbykodejur($row->c_kode_prodi, '4', $edition->datestart, $edition->datefinish) ?? 0,
                    'archievedjurnalpublish' => $eProceedingModel->totalarchivejurnalstatusbykodejur($row->c_kode_prodi, '53', $edition->datestart, $edition->datefinish) ?? 0,
                    'archievedmetadata' => $eProceedingModel->totalarchivejurnalstatusbykodejur($row->c_kode_prodi, '91', $edition->datestart, $edition->datefinish) ?? 0,
                    
                    'jurnalpublish' => $eProceedingModel->totaldocbykodejurandstate($row->c_kode_prodi, '53', $edition->datestart, $edition->datefinish)?? 0,
                ];
                $no++;
            }
        }
        // dd($data);

        return datatables($data)->toJson();
    }

    public function dt_detail_ta(Request $request){
        $eProceedingModel = new EProceedingModel();

        $jurusan = $request->input('jurusan');
        $edisi = $request->input('edisi');

        $edisi = $eProceedingModel->getEprocEditionById($edisi)->first();
        
        $data = $eProceedingModel->gettamasukbykodejur($jurusan, $edisi->datestart, $edisi->datefinish);
        // dd($data);
        return datatables($data)->toJson();
        
    }
    public function dt_detail_jurnal(Request $request){
        $eProceedingModel = new EProceedingModel();

        $jurusan = $request->input('jurusan');
        $edisi = $request->input('edisi');

        $edisi = $eProceedingModel->getEprocEditionById($edisi)->first();
        
        $data = $eProceedingModel->getjurnalmasukbykodejur($jurusan, $edisi->datestart, $edisi->datefinish);
        // dd($data);
        return datatables($data)->toJson();
    }
    public function dt_detail_doc(Request $request){
        $eProceedingModel = new EProceedingModel();

        $jurusan = $request->input('jurusan');
        $edisi = $request->input('edisi');
        $id = $request->input('id');

        $edisi = $eProceedingModel->getEprocEditionById($edisi)->first();

        if ($id=="5"){
			$data = $eProceedingModel->getarchivejurnalstatusbykodejur($jurusan,$id,$edisi->datestart,$edisi->datefinish); 
		}
		else {
			$data = $eProceedingModel->getdocbykodejurandstate($jurusan,$id,$edisi->datestart,$edisi->datefinish);
		} 
        // dd($data);
        return datatables($data)->toJson();
    }
    public function dt_detail_publish(Request $request){
        $eProceedingModel = new EProceedingModel();

        $jurusan = $request->input('jurusan');
        $edisi = $request->input('edisi');

        $edition = $eProceedingModel->getEprocEditionById($edisi)->first();

        if ($edisi<=4) {
			$data		= $eProceedingModel->getjurnalpublishbykodejur($jurusan,$edition->datestart,$edition->datefinish); 
		}
		else {
			$data		= $eProceedingModel->getdocbykodejurandstate($jurusan,'53',$edition->datestart,$edition->datefinish); 
		} 

        return datatables($data)->toJson();
    }
    public function dt_detail_archived(Request $request){
        $id = $request->input('id');
        if ($id=="3" or $id=="52" or $id=="91" or $id=="64" or $id=="4"){
            $eProceedingModel = new EProceedingModel();
            $jurusan = $request->input('jurusan');
            $edisi = $request->input('edisi');
            $edition = $eProceedingModel->getEprocEditionById($edisi)->first();

            $data = $eProceedingModel->getarchivejurnalstatusbykodejur($jurusan,$id,$edition->datestart,$edition->datefinish);
            return datatables($data)->toJson();
        }
    }

    public function dt_detail_archievedjournalpublish(Request $request){
        $eProceedingModel = new EProceedingModel();

        $jurusan = $request->input('jurusan');
        $edisi = $request->input('edisi');

        $edition = $eProceedingModel->getEprocEditionById($edisi)->first();

        $data = $eProceedingModel->getarchivejurnalstatusbykodejur($jurusan,'53',$edition->datestart,$edition->datefinish);
        return datatables($data)->toJson();
        
    }
}
