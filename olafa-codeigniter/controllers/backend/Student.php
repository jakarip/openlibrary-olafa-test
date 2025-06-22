<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Student extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Student_Registration_Model', 'dm', TRUE);
		$this->load->model('Ms_Course_Model', '', TRUE);
		$this->load->model('Ms_Prodi_Model', '', TRUE);
		$this->load->library('PHPExcel');
		$this->load->model('Common_Model', 'cm', TRUE);
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/student/index';
		$data['title']	= 'Data Calon Mahasiswa';		
		$data['icon']	= 'icon-people';
		
		$data['course'] = $this->Ms_Course_Model->getall()->result();
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'sreg_status', 'dt' => 0 ),
			array( 'db' => 'sreg_name', 'dt' => 1 ),
			array( 'db' => 'sreg_gender', 'dt' => 2 ),
			array( 'db' => 'sreg_birthdate', 'dt' => 3 ),
			array( 'db' => 'school_name', 'dt' => 4 ),
			array( 'db' => 'sreg_email', 'dt' => 5 ),
			array( 'db' => 'sreg_phone', 'dt' => 6 ),
			array( 'db' => 'sreg_mobile', 'dt' => 7 ),
			array( 'db' => 'kec_name', 'dt' => 8 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();
				
		if(empty($param['where']))
			$param['where'] = " WHERE (sreg_print_status = 'Y') ";
		else
			$param['where'] .= " AND (sreg_print_status = 'Y') ";
				
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{
			if($row->sreg_status == 'Y')
				$label = '<span class="label label-success">Lulus</span>';
			else
				$label = '<span class="label label-danger">Pending</span>';
			
			$rows = array (
				$label,
				$row->sreg_name,
				$row->sreg_gender,
				$row->sreg_birthplace.', '.date('d/m/Y', strtotime($row->sreg_birthdate)),				
				$row->school_name,
				$row->sreg_email,
				$row->sreg_phone,
				$row->sreg_mobile,
				'Kec. '.$row->kec_name.', '.$row->kec_kab.', Prov. '.$row->kec_prov,
			);
			
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	}
	
	public function excel2()
	{
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setTitle("Data Calon Mahasiswa SMBB")
						 ->setDescription("description");			
		
		$result = $this->dm->getall_view()->result();
		
		$prodi_db = $this->Ms_Prodi_Model->getall()->result();
		$prodi = array();
		foreach($prodi_db as $pd)
			$prodi[$pd->prodi_id] = $pd->prodi_name;
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Data Calon Mahasiswa');
			
		$objPHPExcel->getActiveSheet()->setCellValue('A4', '');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'PIN');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'Nama Lengkap');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'Jenis Kelamin');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'Tempat, Tanggal Lahir');
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'Asal Sekolah');
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'Email'); 
		$objPHPExcel->getActiveSheet()->setCellValue('H4', 'No. Telp'); 
		$objPHPExcel->getActiveSheet()->setCellValue('I4', 'No. HP'); 
		$objPHPExcel->getActiveSheet()->setCellValue('J4', 'Alamat');
		$objPHPExcel->getActiveSheet()->setCellValue('K4', 'Pilihan');
		$objPHPExcel->getActiveSheet()->setCellValue('L4', 'Biaya');
		
		$i=5; 
		foreach($result as $row)
		{
			$i++;
			
			$col=6;
			if($row->sreg_status == 'Y') 
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'LULUS'); 
			else 
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'GAGAL'); 
			
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row->student_username); 
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row->sreg_name); 
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row->sreg_gender); 
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row->sreg_birthplace.', '.date('d/m/Y', strtotime($row->sreg_birthdate)) ); 
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row->school_name); 
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row->sreg_email);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row->sreg_phone);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $row->sreg_mobile);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, 'Kec. '.$row->kec_name.', '.$row->kec_kab.', Prov. '.$row->kec_prov );
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $this->xls_parse_prodi($row->sreg_prodi, $prodi) );
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $this->xls_parse_fee($row->sreg_prodi) );
		}
		
		$style = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
		 
		$objPHPExcel->getDefaultStyle()->applyFromArray($style); 
		
		$objPHPExcel->setActiveSheetIndex(0);

		// Save it as an excel 2003 file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		//$objWriter->save('php://output');str_replace("world","Peter","Hello world!");
		$filePath = 'cdn/Data Calon Mahasiswa.xlsx';
		$objWriter->save($filePath);
		echo $filePath;
	}
	
	private function xls_parse_prodi($text, $prodi)
	{
		//{"1":{"prodi":"12","87":{"n":"1","fee":"5000000","component":"UP3","component_custom":"0"},"86":{"n":"1","fee":"6500000","component":"BPP","component_custom":"0"},"142":{"n":"1","fee":"10000000","component":"SDP2","component_custom":"1"}},"2":{"prodi":"10","74":{"n":"1","fee":"5000000","component":"UP3","component_custom":"0"},"73":{"n":"1","fee":"6500000","component":"BPP","component_custom":"0"},"150":{"n":"1","fee":"10000000","component":"SDP2","component_custom":"1"}},"3":{"prodi":"7","55":{"n":"1","fee":"5000000","component":"UP3","component_custom":"0"},"57":{"n":"1","fee":"6500000","component":"BPP","component_custom":"0"},"162":{"n":"1","fee":"10000000","component":"SDP2","component_custom":"1"}}}
		$json = json_decode($text, true);
		
		$return = '';
		if(!empty($json))
		{
			foreach($json as $key => $p)
			{
				if(isset($prodi[$p['prodi']]))
					$return .= $key.'. '.$prodi[$p['prodi']]."; \n";	
				else
					$return .= $key.'. -'."; \n";
			}
		}
		
		return $return;
	}
	
	private function xls_parse_fee($text)
	{
		$json = json_decode($text, true);
		
		$return = '';
		if(!empty($json))
		{
			foreach($json as $key => $p)
			{
				$return .= $key.'. ';
				
				$component = $p;
				unset($component['prodi']);
				
				foreach($component as $key2 => $fee)
				{
					$f = $fee['fee'] + ($fee['n'] * 1000000 * $fee['component_custom']);
					$return .= $fee['component'].' = '.($f)."; ";
				}
				
				$return .= "; \n";
			}
		}
		
		return $return;
	}
	
	public function excel()
	{
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setTitle("Data Calon Mahasiswa")
						 ->setDescription("description");			
		
		$course = $this->Ms_Course_Model->getall()->result();
		$result = $this->dm->get_all()->result();
		
		
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Data Calon Mahasiswa');
		
		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', '');
		$objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA');
		$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'ASAL SEKOLAH');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:C5');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'BOBOT SEKOLAH');
		$objPHPExcel->getActiveSheet()->mergeCells('D4:D5');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'RATA-RATA NILAI');
		$objPHPExcel->getActiveSheet()->mergeCells('E4:E5');
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'NILAI AKHIR'); 
		$objPHPExcel->getActiveSheet()->mergeCells('F4:F5');
		$col=6;
		foreach($course as $c) {    
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, '4', $c->course_name); 
			$objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($col,'4',$col+4,'4');
			$col=$col+5;
		}  
		
		$col=6;
		foreach($course as $c) {    
			for($s=1; $s<=5; $s++) { 
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, '5', $s);  
				$col++;
			}
		}  
		
		$i=5; 
		foreach($result as $row)
		{
			$i++;
			
			$col=6;
			if($row->sreg_status == 'Y') 
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'LULUS'); 
			else 
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'GAGAL'); 
			
			
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row->sreg_name); 
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row->school_name); 
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row->sreg_score_school); 
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row->sreg_score_avg); 
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, round($row->sreg_score_final, 2));  
			
			if(!empty($row->sreg_score)) 
				$nilai = json_decode($row->sreg_score, true); 
			else
				$nilai = array();
			
			foreach($course as $c) { 
				for($s=1; $s<=5; $s++) {
					if(isset($nilai[$c->course_id][$s])) {
						
						$result = $nilai[$c->course_id][$s];
					}
					else $result = 0; 
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $result);  
					$col++;
				}
			} 
		}
		
		
		
		
		$style = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
		 
		$objPHPExcel->getDefaultStyle()->applyFromArray($style); 
		
		$objPHPExcel->setActiveSheetIndex(0);

		// Save it as an excel 2003 file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		//$objWriter->save('php://output');str_replace("world","Peter","Hello world!");
		$filePath = 'cdn/Data Calon Mahasiswa.xlsx';
		$objWriter->save($filePath);
		echo $filePath;
	}
	
	public function insert()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		
		if( $this->dm->add($item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function edit()
	{
		if(!$this->input->post('id')) return false;
		
		echo json_encode($this->dm->getbyid($this->input->post('id'))->row());
	}
	
	public function update()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$id   = $this->input->post('id');
		
		if( $this->dm->edit($id, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function delete()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id = $this->input->post('id');
		
		if( $this->dm->delete($id) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menghapus Data'));
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
}

?>