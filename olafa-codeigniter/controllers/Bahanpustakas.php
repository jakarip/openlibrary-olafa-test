<?php

class BahanPustakas extends CI_Controller {
 
    function __construct() {
        parent::__construct(); 
		$this->load->model('BahanPustakaModel');
		$this->load->library('PHPExcel');
		//$this->load->library('PHPExcel/IOFactory');
		if(!$this->session->userdata('login')) redirect('');

    }

    function index() {  
	
		$data['curriculum']	= $this->BahanPustakaModel->getCurriculum()->result(); 
		$data['faculty']	= $this->BahanPustakaModel->getallfakultas()->result(); 
		$data['menu'] 		= 'bahanpustaka/bahanpustaka'; 
		$this->load->view('theme',$data);
    }
	 
	public function ajax_index()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$year 		= $this->input->post('year');
		$grow_year 		= $this->input->post('grow_year');
		$faculty 		= $this->input->post('faculty');

		$table 		= "select c_kode_prodi,nama_fakultas,nama_prodi,
					(select count(kt.id) from master_subject msu
					left join knowledge_item_subject kis on kis.master_subject_id=msu.id
					left join knowledge_item kt on knowledge_item_id=kt.id 
					left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
					left join knowledge_type kp on kt.knowledge_type_id=kp.id
					where ks.active='1' and kp.active='1'
					and knowledge_type_id=21 and curriculum_code='$year' and msu.course_code=tmp.c_kode_prodi
					and entrance_date between '1900-01-01' and '$grow_year-12-31 23:59:59'
					) judul,
					(select count(kk.id) from master_subject msu
					left join knowledge_item_subject kis on kis.master_subject_id=msu.id 
					left join knowledge_item kt on kt.id = kis.knowledge_item_id 
					left join knowledge_stock kk on kk.knowledge_item_id = kt.id
					left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
					left join knowledge_type kp on kt.knowledge_type_id=kp.id 
					where ks.active='1' and kp.active='1' and kk.status not in(4,5)
					and kk.knowledge_type_id=21 and curriculum_code='$year' and msu.course_code=tmp.c_kode_prodi
					and kk.entrance_date between '1900-01-01' and '$grow_year-12-31 23:59:59'
					) eks,
					(select count(kt.id) from master_subject msu
					left join knowledge_item_subject kis on kis.master_subject_id=msu.id
					left join knowledge_item kt on knowledge_item_id=kt.id 
					left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
					left join knowledge_type kp on kt.knowledge_type_id=kp.id
					where ks.active='1' and kp.active='1'
					and knowledge_type_id  in (1, 2, 3, 33, 40, 41, 59, 65) and curriculum_code='$year' and msu.course_code=tmp.c_kode_prodi
					and entrance_date between '1900-01-01' and '$grow_year-12-31 23:59:59'
					) judul_fisik,
					(select count(kk.id) from master_subject msu
					left join knowledge_item_subject kis on kis.master_subject_id=msu.id 
					left join knowledge_item kt on kt.id = kis.knowledge_item_id 
					left join knowledge_stock kk on kk.knowledge_item_id = kt.id
					left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
					left join knowledge_type kp on kt.knowledge_type_id=kp.id 
					where ks.active='1' and kp.active='1' and kk.status not in(4,5)
					and kk.knowledge_type_id  in (1, 2, 3, 33, 40, 41, 59, 65) and curriculum_code='$year' and msu.course_code=tmp.c_kode_prodi
					and kk.entrance_date between '1900-01-01' and '$grow_year-12-31 23:59:59'
					) eks_fisik,
					(select count(*) from master_subject where curriculum_code='$year' and course_code=tmp.c_kode_prodi) mk,
					(select count(*) from master_subject where curriculum_code='$year' and course_code=tmp.c_kode_prodi and master_subject.id in(select master_subject_id from knowledge_item_subject)) mkadabuku
					from t_mst_fakultas tmf left join t_mst_prodi tmp on tmp.c_kode_fakultas=tmf.c_kode_fakultas where (nama_prodi not like '%Pindahan%' and nama_prodi not like '%International%' and nama_prodi not like '%Internasional%') and nama_fakultas!='' and c_kode_fakultas='$faculty'
					";

					// echo $table;
		$colOrder 	= array(null,'nama_fakultas','nama_prodi','judul_fisik','eks_fisik','judul','eks','judul','eks','mk','mkadabuku',null); //set column field database for datatable orderable
		$colSearch 	= array('nama_fakultas','nama_prodi','judul_fisik','eks_fisik','judul','eks','judul','eks','mk','mkadabuku'); //set column field database for datatable
		$order 		= "order by nama_fakultas,nama_prodi asc"; // default order 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);
		
		$list = $this->datatables->get_datatables();
		//print_r($list);
		$data = array(); 
		$no = $_POST['start'];
		foreach ($list as $dt) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $dt->nama_fakultas;
			$row[] = $dt->nama_prodi;
			$row[] = '<div class="td_right">'.$dt->judul_fisik.' '.getLang('title').'</div>';
			$row[] = '<div class="td_right">'.$dt->eks_fisik.' '.getLang('copy').'</div>';
			$row[] = '<div class="td_right">'.$dt->judul.' '.getLang('title').'</div>';
			$row[] = '<div class="td_right">'.$dt->eks.' '.getLang('copy').'</div>';
			$row[] = '<div class="td_right">'.($dt->judul_fisik+$dt->judul).' '.getLang('title').'</div>';
			$row[] = '<div class="td_right">'.($dt->eks_fisik+$dt->eks).' '.getLang('copy').'</div>';
			$row[] = '<div class="td_right">'.$dt->mk.' '.getLang('subject').'</div>';
			$row[] = '<div class="td_right">'.$dt->mkadabuku.' '.getLang('subject').'</div>';
			$row[] = '<div class="td_right">'.number_format((float)$dt->mkadabuku/$dt->mk*100, 2, '.', '').'%</div>';
			$row[] = '<div><a class="btn btn-sm btn-danger btn-embossed" target="_blank" href="index.php/bahanpustaka/mk/'.strtolower($dt->c_kode_prodi).'/'.$year.'" title="'.getLang('subject_detail').'"><i class="fa fa-file"></i></a></div><br><div><button class="btn btn-sm btn-success btn-embossed" onclick="excel('."'".$dt->c_kode_prodi."','".$year."','".$grow_year."'".')" title="'.getLang('download').'"><i class="fa fa-cloud-download"></i></button></div>';
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->datatables->count_all(),
						"recordsFiltered" => $this->datatables->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
	
	public function excel()
	{
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setTitle("Bahan Pustaka")
						 ->setDescription("description");			
		
		$year 		= $this->input->post('year');
		$grow_year 		= $this->input->post('grow_year');
		$id 		= $this->input->post('id');
		
		$prodi 		= $this->BahanPustakaModel->getStudyProgram($id)->row();
		$mk 		= $this->BahanPustakaModel->getMK($id,$year)->result();
		
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', $prodi->nama_fakultas.' - '.$prodi->nama_prodi);
		
		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'KODE MK / SMT / MK / SKS');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NO INDUK');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NO KELAS');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'JENIS BUKU');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'JUDUL BUKU');
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'PENGARANG');
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'JUMLAH TOTAL (Eksemplar)');
		$objPHPExcel->getActiveSheet()->setCellValue('H4', 'JUMLAH TERSEDIA (Eksemplar)');
		$i 		= 5;
		$keys 	= "";
		foreach ($mk as $mks){
			 $buku = $this->BahanPustakaModel->getbukuref($mks->id, '', ''); 
			 echo $buku->num_rows;
			 if ($buku->num_rows()!=0){
				 foreach ($buku->result() as $key=>$bk){
					if ($key==0){
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$mks->code.' / '.$mks->semester.' / '.$mks->name.' / '.$mks->sks);
					}
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $bk->kode_buku);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $bk->klasifikasi);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, ($bk->knowledge_type_id=='21'?'E-BOOK':'BUKU TERCETAK'));
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $bk->title);
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $bk->author);
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $bk->eks);
					$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $bk->tersedia);
					$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $bk->kiid);
					$i++;
				 }
			}
			else {
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$mks->code.' / '.$mks->semester.' / '.$mks->name.' / '.$mks->sks);
				$i++;
			}
			// $keys==0;
		
		}
		$objPHPExcel->setActiveSheetIndex(0);

		// Save it as an excel 2003 file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		//$objWriter->save('php://output');str_replace("world","Peter","Hello world!");
		$filePath = 'downloads/'.str_replace(" ","_",$prodi->nama_fakultas).'-'.str_replace(" ","_",$prodi->nama_prodi).'.xlsx';
		$objWriter->save($filePath);
		echo $filePath;
	}
	
	public function excel_header()
	{
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setTitle("Bahan Pustaka")
						 ->setDescription("description");			
		
		$year 		= $this->input->post('year'); 
		$grow_year 		= $this->input->post('grow_year'); 
		
		$prodi 		= $this->BahanPustakaModel->getBahanPustaka($year,$grow_year)->result();
		
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', $year);
		
		
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NO');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'FAKULTAS');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'PROGRAM STUDI');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'TOTAL JUDUL BUKU');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'TOTAL EKSEMPLAR');
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'MATAKULIAH');
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'MATAKULIAH YANG ADA BUKUNYA');  
		$i 		= 5;
		$keys 	= "";
		$judul 	= 0;
		$eks	= 0;
		$mk		= 0;
		$mkadabuku	= 0;
		foreach ($prodi as $mks){ 
			$judul 		= $judul+$mks->judul;
			$eks		= $eks+$mks->eks;
			$mk			= $mk+$mks->mk;
			$mkadabuku	= $mkadabuku+$mks->mkadabuku;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $i-4);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $mks->nama_fakultas);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $mks->nama_prodi);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $mks->judul.' '.getLang('title'));
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $mks->eks.' '.getLang('copy'));
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $mks->mk.' '.getLang('subject'));
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $mks->mkadabuku.' '.getLang('subject'));
			$i++;
		
		}
		
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'TOTAL');
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $judul.' '.getLang('title'));
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $eks.' '.getLang('copy'));
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $mk.' '.getLang('subject'));
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $mkadabuku.' '.getLang('subject'));
		
		$objPHPExcel->setActiveSheetIndex(0);

		// Save it as an excel 2003 file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		//$objWriter->save('php://output');
		$filePath = 'downloads/bahanpustaka_'.$year.'.xlsx';
		$objWriter->save($filePath);
		echo $filePath;
	}
	
	public function totalcollection()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed'); 
		echo json_encode($this->BahanPustakaModel->totalcollection($this->input->post('year'),$this->input->post('grow_year'))->row());
	}
	
	function mk($jurusan='',$tahun="") { 
	 	$jurusan			= strtoupper($jurusan);
		$data['jurusan']	= $this->BahanPustakaModel->getjurbykodejur($jurusan)->row();
		$data['mk'] 		= $this->BahanPustakaModel->totalsubject($jurusan,$tahun)->row(); 
		if(empty($data['jurusan'])) return false;
		 
		$data['tahun'] 			= $tahun; 
		$data['menu'] 			= 'bahanpustaka/bahanpustaka_mk'; 
		$this->load->view('theme', $data);
	}
	
	
	public function ajax_mk()
	{
		$jurusan 	= $this->input->post('jurusan'); 
		$tahun 		= $this->input->post('tahun');
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$year 		= $this->input->post('year');
		$table 		= "select *,SUBSTR(msu.code,-1) sks,(select count(*) from knowledge_item_subject kis 
					left join knowledge_item kt on knowledge_item_id=kt.id 
					left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
					left join knowledge_type kp on kt.knowledge_type_id=kp.id
					where ks.active='1' and kp.active='1' and knowledge_type_id=21 and kis.master_subject_id=msu.id) jmljudul,
					(select count(*) from knowledge_item_subject kis 
					left join knowledge_item kt on knowledge_item_id=kt.id 
					left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
					left join knowledge_type kp on kt.knowledge_type_id=kp.id
					where ks.active='1' and kp.active='1' and knowledge_type_id  in (1, 2, 3, 33, 40, 41, 59, 65) and kis.master_subject_id=msu.id) jmljudul_fisik
					from master_subject msu where course_code ='$jurusan' AND msu.curriculum_code = '$tahun'";
		$colOrder 	= array(null,'code','semester','name','sks','jmljudul_fisik','jmljudul',null); //set column field database for datatable orderable
		$colSearch 	= array('code','semester','name','sks','jmljudul_fisik','jmljudul'); //set column field database for datatable
		$order 		= "order by semester"; // default order 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);
		
		$list = $this->datatables->get_datatables();
		//print_r($list);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $dt) {
			$id 				= array('id' => $dt->id, 'kodemk' => $dt->code, 'namamk' => $dt->name);
			$ids				= urlencode(base64_encode(serialize($id)));
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $dt->code;
			$row[] = $dt->semester;
			$row[] = $dt->name;
			$row[] = '<div class="td_right">'.$dt->sks.' '.getLang('sks').'</div>';
			$row[] = '<div class="td_right">'.$dt->jmljudul_fisik.' '.getLang('title').'</div>';
			$row[] = '<div class="td_right">'.$dt->jmljudul.' '.getLang('title').'</div>';
			$row[] = '<div><a class="btn btn-sm btn-danger btn-embossed" href="javascript:;"  onclick="viewBuku('."'".$ids."'".','."'book'".')"  title="'.getLang('Detail Buku Tercetak').'"><i class="fa fa-file"></i></a><a class="btn btn-sm btn-danger btn-embossed" href="javascript:;"  onclick="viewBuku('."'".$ids."'".','."'ebook'".')"  title="'.getLang('Detail E-Book').'"><i class="fa fa-file"></i></a></div>';
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->datatables->count_all(),
						"recordsFiltered" => $this->datatables->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
	
	function viewbuku() {
		$data['mk'] 	 		= unserialize(base64_decode(urldecode($this->input->post('id')))); 
		$type 	 		= $this->input->post('type');
		
		$data['type'] 	 		= ($type=='book'?'Buku Tercetak':'E-Book');
		
		if(!is_array($data['mk'])) return false;
		 
		$data['bukuref']		= $this->BahanPustakaModel->getbukuref($data['mk']['id'], '', '',$type)->result(); 
		
        $this->load->view('bahanpustaka/bahanpustaka_view_buku', $data); 
		 
	}
	
}

?>