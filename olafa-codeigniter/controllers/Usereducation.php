<?php

class Usereducation extends CI_Controller {
 
    function __construct() {
        parent::__construct(); 
		$this->load->model('UserEducationModel');
		$this->load->library('PHPExcel');
		//$this->load->library('PHPExcel/IOFactory');

    }

    function index() {  
	
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');  
		$data['curriculum']	= $this->UserEducationModel->getYear()->result(); 
		$data['menu'] 		= 'usereducation/usereducation'; 
		$this->load->view('theme',$data);
    }
	 
	public function ajax_index()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$year 		= $this->input->post('year');
		$table 		= "select *,(select count(*) from usereducation us where useredu_year='$year' and us.useredu_prodi=ud.useredu_prodi) total,(select count(*) from usereducation us where useredu_year='$year' and us.useredu_prodi=ud.useredu_prodi and useredu_date is not null) hadir from usereducation ud where useredu_year='$year' 
						group by useredu_fak,useredu_prodi";
		$colOrder 	= array(null,'useredu_fak','useredu_prodi',null); //set column field database for datatable orderable
		$colSearch 	= array('useredu_fak','useredu_prodi'); //set column field database for datatable
		$order 		= "order by useredu_fak,useredu_prodi asc"; // default order 
		
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
			$row[] = $dt->useredu_fak;
			$row[] = $dt->useredu_prodi; 
			$row[] = $dt->total; 
			$row[] = $dt->hadir; 
			$row[] = '<div><a class="btn btn-sm btn-danger btn-embossed" target="_blank" href="index.php/usereducation/students/'.strtolower($dt->useredu_id).'/'.$year.'" title="'.getLang('student').'"><i class="fa fa-users"></i></a></div>';
		
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
		$id 		= $this->input->post('id');
		
		$prodi 		= $this->UserEducationModel->getStudyProgram($id)->row();
		$mk 		= $this->UserEducationModel->getMK($id,$year)->result();
		
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', $prodi->nama_fakultas.' - '.$prodi->nama_prodi);
		
		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'KODE MK / SMT / MK / SKS');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NO INDUK');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NO KELAS');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'JUDUL BUKU');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'PENGARANG');
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'JUMLAH TOTAL (Eksemplar)');
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'JUMLAH TERSEDIA (Eksemplar)');
		$i 		= 5;
		$keys 	= "";
		foreach ($mk as $mks){
			 $buku = $this->UserEducationModel->getbukuref($mks->id, '', ''); 
			 echo $buku->num_rows;
			 if ($buku->num_rows()!=0){
				 foreach ($buku->result() as $key=>$bk){
					if ($key==0){
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$mks->code.' / '.$mks->semester.' / '.$mks->name.' / '.$mks->sks);
					}
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $bk->kode_buku);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $bk->klasifikasi);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $bk->title);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $bk->author);
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $bk->eks);
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $bk->tersedia);
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
		//$objWriter->save('php://output');
		$filePath = 'downloads/'.$prodi->nama_fakultas.' - '.$prodi->nama_prodi.'.xlsx';
		$objWriter->save($filePath);
		echo $filePath;
	}
	
	public function excel_header()
	{
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setTitle("Bahan Pustaka")
						 ->setDescription("description");			
		
		$year 		= $this->input->post('year'); 
		
		$prodi 		= $this->UserEducationModel->getBahanPustaka($year)->result();
		
		
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
		echo json_encode($this->UserEducationModel->totalcollection($this->input->post('year'))->row());
	}
	
	function student($jurusan='',$tahun="") { 
		
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');  
	 	$jurusan			= strtoupper($jurusan);
		$data['jurusan']	= $this->UserEducationModel->getjurbykodejur($jurusan)->row();
		$data['present'] 	= $this->UserEducationModel->totalpresent($data['jurusan']->useredu_prodi,$tahun)->row();  
		$data['total'] 		= $this->UserEducationModel->totalstudent($data['jurusan']->useredu_prodi,$tahun)->row();  
		if(empty($data['jurusan'])) return false;
		 
		$data['tahun'] 			= $tahun; 
		$data['menu'] 			= 'usereducation/usereducation_student'; 
		$this->load->view('theme', $data);
	} 
	
	function students($jurusan='',$tahun="",$id="") {  
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');  
		if ($id!=""){ 
			if ($this->UserEducationModel->present($id)) $data['status'] = "success"; 
			else  $data['status'] = "failed"; 
		}
		
	 	$jurusan			= strtoupper($jurusan);
		$data['jurusan']	= $this->UserEducationModel->getjurbykodejur($jurusan)->row();
		$data['present'] 	= $this->UserEducationModel->totalpresent($data['jurusan']->useredu_prodi,$tahun)->row();  
		$data['total'] 		= $this->UserEducationModel->totalstudent($data['jurusan']->useredu_prodi,$tahun)->row();  
		$data['data'] 		=  $this->UserEducationModel->datastudent($data['jurusan']->useredu_prodi,$tahun)->result();  
		$data['tahun']		= $tahun;
		
		 
		$data['tahun'] 			= $tahun; 
		$data['menu'] 			= 'usereducation/usereducation_students'; 
		$this->load->view('theme', $data);
	} 
	
	public function ajax_student()
	{
		$jurusan 	= $this->input->post('jurusan'); 
		$tahun 		= $this->input->post('tahun');
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$year 		= $this->input->post('year');
		$table 		= " select * from usereducation where useredu_prodi='$jurusan'
					 AND useredu_year = '$tahun'";
		$colOrder 	= array(null,'useredu_name','useredu_date','useredu_date',null); //set column field database for datatable orderable
		$colSearch 	= array('useredu_name','useredu_date','useredu_date'); //set column field database for datatable
		$order 		= "order by useredu_name"; // default order 
		
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
			$row[] = $dt->useredu_name; 
			$row[] = $dt->useredu_date;  
			if ($dt->useredu_date!=null){
				$row[] = '<div><a class="btn btn-sm btn-success btn-embossed" href="javascript:;"  title="'.getLang('present').'"><i class="fa fa-check-circle"></i></a></div>';
				$row[] = '';
			}
			else { 
				$row[] = '';
				$row[] = '<div><a class="btn btn-sm btn-danger btn-embossed" href="javascript:;"  onclick="present('."'".$dt->useredu_id."'".')"  title="'.getLang('present').'"><i class="fa fa-check"></i>&nbsp;&nbsp;'.getLang('present').'</a></div>';
			}
		
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
	
	function present() {
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id	 =  $this->input->post('id');
		
		$jurusan		= $this->UserEducationModel->getjurbykodejur($id)->row();
		if ($this->UserEducationModel->present($id)) {
			if (!$this->input->is_ajax_request()) exit('No direct script access allowed'); 
			$present 	= $this->UserEducationModel->totalpresent($jurusan->useredu_prodi,$jurusan->useredu_year)->row();  
			$total 		= $this->UserEducationModel->totalstudent($jurusan->useredu_prodi,$jurusan->useredu_year)->row();  
			 
			echo 'success,<b>'.$present->total.' '.getLang("from").' '.$total->total.' '.getLang("present").'</b>';
			
		} else echo "failed,";
	}
	
}

?>