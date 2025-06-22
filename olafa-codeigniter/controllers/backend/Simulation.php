<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'libraries/simple_html_dom.php');

class Simulation extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Participant_Model', 'dm', TRUE);
		$this->load->model('Participant_Registration_Model', 'sr', TRUE);
		$this->load->model('Student_Simulation_Temp_Model', 'temp', TRUE);
		$this->load->model('Ms_Course_Model', '', TRUE);
		$this->load->model('Ms_Prodi_Model', '', TRUE);
		$this->load->model('Ms_School_Model', '', TRUE);
		$this->load->model('Settings_Model', '', TRUE);
		$this->load->model('Common_Model', 'cm', TRUE);
		$this->load->model('Ms_Scholarship_Model', '', TRUE);
		$this->load->model('Ms_Component_Model', '', TRUE);  
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '0');
		ini_set('max_input_time', '0'); 
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/simulation/index';
		$data['title']	= 'Simulasi Kelulusan';		
		$data['icon']	= 'icon-podium';
		
		$temp = $this->temp->count();
		if($temp > 0)
			redirect(y_url_admin().'/simulation/step2');
			
		$data['count'] = $this->sr->getsimulation_nonutbk()->num_rows();
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function step2()
	{
		$data['view'] 	= 'backend/simulation/step2';
		$data['title']	= 'Simulasi Kelulusan';		
		$data['icon']	= 'icon-podium'; 
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'sreg_id', 'dt' => 0 ),
			array( 'db' => 'par_fullname', 'dt' => 1 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();
				
		$result = $this->temp->dtquery($param)->result();
		$filter = $this->temp->dtfiltered();
		$total	= $this->temp->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		$i 			= 0; 
		$scholar 	= $this->Ms_Scholarship_Model->getprodischolarship()->result();  
		$temp 		= array(); 
		if($scholar){
			foreach($scholar as $row){
				$temp[$row->ps_id_prodi][$row->ps_id_scholarship]['amount'] 	= $row->ps_amount;
				$temp[$row->ps_id_prodi][$row->ps_id_scholarship]['name'] 		= $row->scholarship_name;
				$temp[$row->ps_id_prodi][$row->ps_id_scholarship]['id'] 		= $row->scholarship_id;
			} 
		}
		
		foreach($result as $row)
		{
			$scholar = (int) $row->sst_scholarship;
			$status = (int) $row->sst_status;
			$choice = (int) $row->sst_choice;
			
			if($status > 1) $status = 1;
			
			if($status == '1')
				$label = '<span class="label label-success">Lulus</span>';
			else
				$label = '<span class="label label-danger">Gagal</span>';
						
			$label_status = '<input type="hidden" class="status-hidden" value="1"><span class="label label-success">Sukses</span>';
			if(empty($row->sreg_id))
				$label_status = '<input type="hidden" class="status-hidden" value="0"><span class="label label-danger">Error: ID</span>';
			else if($status == 1 and $choice == 0)
				$label_status = '<input type="hidden" class="status-hidden" value="0"><span class="label label-danger">Error: Pilihan Ke-</span>';
			else if($status == 0 and $choice != 0)
				$label_status = '<input type="hidden" class="status-hidden" value="0"><span class="label label-danger">Error: Status Lulus</span>';
			else if($row->sreg_status_pass == 'Y')
				$label_status = '<input type="hidden" class="status-hidden" value="0"><span class="label label-danger">Error: Duplicate</span>';
			
			$array = ""; 
			if (!empty($row->prodi_id) and $row->sst_scholarship!=0){
				if($temp){
					
					$array=$temp[$row->prodi_id][$scholar]['name'].' - '.number_format($temp[$row->prodi_id][$scholar]['amount'],0,'','.'); 
				}
			}
			
			$rows = array (
				$row->sreg_id,
				$label_status,
				$row->par_fullname.'<br><span style="color:green;font-weight:bold">'.$row->school_name.'</span>', 
				$label,
				$row->prodi_name, 
				$row->sreg_score_avg,
				$array
				
			);
			
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
	
	public function step1_download_excel()
	{
		$course = $this->Ms_Course_Model->getby(array('course_status' => 1,'course_type'=>'rapor'))->result();
		$result = $this->sr->getsimulation_nonutbk()->result();		
		$identifier = md5(time());
		
		$nopil 			= $this->Settings_Model->getvalue('max_prodi'); 
		
		$prodi_db 		= $this->Ms_Prodi_Model->getall()->result();
		$scholarship 	= $this->Ms_Scholarship_Model->getby(array('scholarship_status' => 1))->result();
		$prodi = array();
		foreach($prodi_db as $pd)
			$prodi[$pd->prodi_id] = $pd->prodi_name;
		
		$this->load->library('PHPExcel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setTitle("Data Simulasi Calon Mahasiswa")
						 			 ->setDescription("Data Simulasi Calon Mahasiswa");
		 
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'DATA SIMULASI CALON MAHASISWA ('.date('m/d/Y').')');
		$objPHPExcel->getActiveSheet()->setCellValue('A3', 'Catatan Penting:');
		
		$objRichText = new PHPExcel_RichText();
		$objRichText->createText('1. Jangan mengubah nilai pada kolom ');
		$objRichText->createTextRun('ID')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('A4', $objRichText);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A5', '2. Anda hanya diperkenankan mengubah pada kolom berwarna BIRU');
		$objPHPExcel->getActiveSheet()->setCellValue('A6', '3. Jangan menambah atau mengurangi jumlah kolom');
		
		$objRichText = new PHPExcel_RichText();
		$objRichText->createText('4. Jangan mengubah pada bagian ');
		$objRichText->createTextRun('ID IDENTIFIER')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('A7', $objRichText);
		
		$objRichText = new PHPExcel_RichText();
		$objRichText->createText('5. Jangan mengubah ');
		$objRichText->createTextRun('Nama File (Filename)')->getFont()->setBold(true);
		$objRichText->createText(' dan ');
		$objRichText->createTextRun('Nama / Title Sheet')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('A8', $objRichText);
		
		$objRichText = new PHPExcel_RichText();
		$objRichText->createText('6. Tuliskan hasil akhir pada kolom ');
		$objRichText->createTextRun('HASIL')->getFont()->setBold(true);
		$objRichText->createText(' dengan nilai => ');		
		$objRichText->createTextRun('1 ')->getFont()->setBold(true);		
		$objRichText->createTextRun('untuk Lulus;')->getFont()->setUnderline(true);
		$objRichText->createTextRun('  0 ')->getFont()->setBold(true);
		$objRichText->createTextRun('untuk Tidak Lulus')->getFont()->setUnderline(true);
		$objPHPExcel->getActiveSheet()->setCellValue('A9', $objRichText);
		
		$objRichText = new PHPExcel_RichText();
		$objRichText->createText('7. Tuliskan lulus pada pilihan ke- pada kolom ');
		$objRichText->createTextRun('LULUS PILIHAN KE-')->getFont()->setBold(true);
		$objRichText->createText(' dengan Angka ');
		$objRichText->createTextRun('1,2,3,…dst')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('A10', $objRichText); 
		
		$objRichText = new PHPExcel_RichText();
		$objRichText->createText('8. Jika siswa yang lulus mendapatkan beasiswa, tulis hasil akhir pada kolom ');
		$objRichText->createTextRun('KODE BEASISWA')->getFont()->setBold(true);
		$objRichText->createText(' dengan => ');		
		$objRichText->createTextRun('KODE BEASISWA')->getFont()->setBold(true);		
		$objRichText->createTextRun(' yang ada pada tabel dibawah ini.')->getFont()->setUnderline(true);  	
		$objPHPExcel->getActiveSheet()->setCellValue('A11', $objRichText); 
		 
		$schcol='1';
		if($scholarship){
			$objRichText = new PHPExcel_RichText(); 
			$objRichText->createTextRun('Kode Beasiswa')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->setCellValue('A12', $objRichText);
			
			$objRichText = new PHPExcel_RichText(); 
			$objRichText->createTextRun('Nama Beasiswa')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->setCellValue('A13', $objRichText); 
			foreach($scholarship as $sch){
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($schcol,'12', $sch->scholarship_id);
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($schcol,'13', $sch->scholarship_name);
				$schcol++;
			}
		} 
		
		$objRichText = new PHPExcel_RichText();
		$objRichText->createTextRun('ID IDENTIFIER:')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('A15', $objRichText);
				
		$objRichText = new PHPExcel_RichText();
		$objRichText->createTextRun($identifier)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B15', $objRichText);
		
		$objPHPExcel->getActiveSheet()->getStyle('A15:B15')->applyFromArray(
			array(
				'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FF0000')),
				'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
			)
		);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A17', 'ID');
		$objPHPExcel->getActiveSheet()->mergeCells('A17:A18');
		$objPHPExcel->getActiveSheet()->setCellValue('B17', 'NO TRANSAKSI');
		$objPHPExcel->getActiveSheet()->mergeCells('B17:B18');
		$objPHPExcel->getActiveSheet()->setCellValue('C17', 'NO PARTICIPANT');
		$objPHPExcel->getActiveSheet()->mergeCells('C17:C18');
		$objPHPExcel->getActiveSheet()->setCellValue('D17', 'NAMA');
		$objPHPExcel->getActiveSheet()->mergeCells('D17:D18');
		$objPHPExcel->getActiveSheet()->setCellValue('E17', 'ASAL SEKOLAH');
		$objPHPExcel->getActiveSheet()->mergeCells('E17:E18');
		$objPHPExcel->getActiveSheet()->setCellValue('F17', 'JURUSAN');
		$objPHPExcel->getActiveSheet()->mergeCells('F17:F18');
		// $objPHPExcel->getActiveSheet()->setCellValue('F14', 'BOBOT SEKOLAH');
		// $objPHPExcel->getActiveSheet()->mergeCells('F14:F15');
		$objPHPExcel->getActiveSheet()->setCellValue('G17', 'RATA-RATA NILAI');
		$objPHPExcel->getActiveSheet()->mergeCells('G17:G18');
		
		/*------ Begin Data -----*/
		
		$col=7;
		foreach($course as $c) 
		{   
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, '17', 'Bobot'); 
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col+1, '17', $c->course_name); 
			$objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($col+1, '17', $col+5, '17');
			$col=$col+6;
		}  
		
		$col=7;
		foreach($course as $c) 
		{    
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, '18', $c->course_score);
			$col++;
			
			for($s=1; $s<=5; $s++) 
			{ 
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, '18', $s);  
				$col++;
			}
		}
		
		for($pil=1; $pil<=$nopil; $pil++)
		{
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, '17', 'PILIHAN '.$pil); 
			$objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($col, '17', $col, '17');
			$col++;
		}
		
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, '17', 'Passing Grade'); 
		$objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($col, '17', $col, '18');
		
		$col++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, '17', 'Nilai Total Bobot'); 
		$objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($col, '17', $col, '18');
		
		$col++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, '17', 'HASIL'); 
		$objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($col, '17', $col, '18');
		
		$col++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, '17', 'LULUS PILIHAN KE-'); 
		$objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($col,'17',$col,'18');
		
		$col++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, '17', 'KODE BEASISWA (OPSIONAL)'); 
		$objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($col,'17',$col,'18');

		$col++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, '17', 'NIP TELKOM'); 
		$objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($col,'17',$col,'18');
		
		$i=19; 
		foreach($result as $row)
		{		
			$col=7;
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row->sreg_id);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row->pin_transaction_number);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row->par_participantnumber);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row->par_fullname); 
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row->school_name); 
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row->par_department); 
			// $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row->sreg_score_school); 
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row->sreg_score_avg); 
			
			if(!empty($row->sreg_score)) 
				$nilai = json_decode($row->sreg_score, true); 
			else
				$nilai = array();
			
			$bobot_total = 0;
			foreach($course as $c) 
			{ 
				//bobot = nilai bobot masing-masing course * average nilai;
				if(isset($nilai[$c->course_id]))
					$bobot = $c->course_score * (array_sum($nilai[$c->course_id]) / count($nilai[$c->course_id]));
				else
					$bobot = 0;
					
				$bobot_total += $bobot;
				
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $bobot);  
				$col++;
					
				for($s=1; $s<=5; $s++) 
				{
					if(isset($nilai[$c->course_id][$s]))						
						$result = $nilai[$c->course_id][$s];
					else
						$result = 0;
						 
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $result);  
					$col++;
				}
			}
			
			if(!empty($row->sreg_prodi))
			{
				$json_pilihan = json_decode($row->sreg_prodi, true);
				$pilihan	  = array();
				
				foreach($json_pilihan as $idp => $vp)
					$pilihan[$idp] = $vp['prodi'];
			}
			else
				$pilihan = array(); 
			
			for($pil=1; $pil<=$nopil; $pil++)
			{
				if(isset($pilihan[$pil]))
				{
					$prodi_id = $pilihan[$pil];
					
					if(isset($prodi[$prodi_id]))
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $prodi[$prodi_id]);
					else
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, '');
				}
				else
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, '');
				
				$col++;
			}
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, '65');
			$col++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $bobot_total);
			$col++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, ($bobot_total >= 65 ? '1' : '0') );
			$col++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, ($bobot_total >= 65 ? '1' : '0') );
			$col++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, '');
			$col++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $row->par_nip_telkom);
			
			$i++;
		}
		
		$i--;
		$before_last = PHPExcel_Cell::stringFromColumnIndex($col-1);
		$last = PHPExcel_Cell::stringFromColumnIndex($col);
		$lastsch = PHPExcel_Cell::stringFromColumnIndex($schcol-1);
		
		//Header
		$th = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFFF00')),
					'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
					'font'  => array('bold'  => true),
					'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
			  ); 
				  
		$objPHPExcel->getActiveSheet()->getStyle('A17:'.$last.'18')->applyFromArray($th);
		$objPHPExcel->getActiveSheet()->getStyle('A12:'.$lastsch.'13')->applyFromArray($th);
		
		//Content		
		$td = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
					'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
			  );
		
		$objPHPExcel->getActiveSheet()->getStyle('A19:'.$last.$i)->applyFromArray($td);
		
		//Edit Cell
		$objPHPExcel->getActiveSheet()->getStyle('F19:F'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('B3E5FC');
		$objPHPExcel->getActiveSheet()->getStyle($before_last.'19:'.$last.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('B3E5FC');
		
		//Protect ID
		//$objPHPExcel->getActiveSheet()->protectCells('A16:A'.$i, 'PHP');
		
		//Width
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(32);
		
		$objPHPExcel->setActiveSheetIndex(0);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$identifier.'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		ob_end_clean();
		$objWriter->save('php://output');
		
		exit();
	}
	
	public function step2_upload_excel()
	{
		$this->load->library('PHPExcel');
		
		$inputFileName = $_FILES['xls_file']['tmp_name'];
		
		//  Read your Excel workbook
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        //  Get worksheet dimensions
        $sheet              = $objPHPExcel->getSheet(0); 
        $highestRow         = $sheet->getHighestRow(); 
        $highestColumn      = $sheet->getHighestColumn();
		
		$rowData = $sheet->rangeToArray('A19:'.$highestColumn.$highestRow, NULL, TRUE, FALSE);
		
		$item  = array();
		$rowno = 1;
		$error = array();
		foreach($rowData as $row)
		{
			$id = (int) trim($row[0]);
			
			if(empty($id) or $id == 0)
			{
				$error[] = $rowno;
			}
			else
			{
				$index 		= count($row);
				$scholar 	= (int) trim($row[$index-2]);
				$choice 	= (int) trim($row[$index-3]);
				$status 	= (int) trim($row[$index-4]);
				
				$item[] = array('sst_id_sreg' => $id,
								'sst_choice' => (empty($choice) ? 0 : $choice),
								'sst_status' => (empty($status) ? 0 : $status),
								'sst_scholarship' => (empty($scholar) ? 0 : $scholar),
								// 'sst_school_score' => $row[4],
								'sst_choice_prodi' => '0');
			}
			
			$rowno++;
		}  
		
		if(empty($error))
		{
			$this->temp->truncate();
			$this->temp->add_batch($item);
			$this->step2_update_choice();
			
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		}
		else
		{
			echo json_encode(array('status' => 'error;', 'text' => "Terdapat ID yang kosong pada baris: \n".implode(', ', $error)."\n\nNb: Jangan mengubah nilai pada kolom ID"));
		}
	}
	
	private function step2_update_choice()
	{
		$temps = $this->temp->getall_view()->result();
		
		$prodi_db = $this->Ms_Prodi_Model->getall()->result();
		$prodi = array();
		foreach($prodi_db as $pd)
			$prodi[$pd->prodi_id] = $pd->prodi_name;
		
		foreach($temps as $temp)
		{
			$pilihan = array();
			
			if($temp->sst_choice > 0)
			{			
				if(!empty($temp->sreg_prodi))
				{
					$json_pilihan = json_decode($temp->sreg_prodi, true);
					
					foreach($json_pilihan as $idp => $vp)
						$pilihan[$idp] = $vp['prodi'];
				}
			
						
				if(isset($pilihan[$temp->sst_choice]))
				{
					if(isset($prodi[$pilihan[$temp->sst_choice]]))
					{
						$this->temp->edit($temp->sst_id, array('sst_choice_prodi' => $pilihan[$temp->sst_choice]));
					}
				}
			}
		}
	}
	
	public function step2_cancel()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$this->temp->truncate();
		
		echo json_encode(array('status' => 'ok;', 'text' => ''));
	}
	
	public function step3()
	{
		if(!$this->input->post('identifier')) redirect(y_url_admin().'/simulation/step2'); 
		$dbs = $this->temp->getall_view()->result();
		
		$scholar 	= $this->Ms_Scholarship_Model->getprodischolarship()->result();  
		$temp 		= array(); 
		if($scholar){
			foreach($scholar as $row){
				$temp[$row->ps_id_prodi][$row->ps_id_scholarship]['amount'] 	= $row->ps_amount;
				$temp[$row->ps_id_prodi][$row->ps_id_scholarship]['name'] 		= $row->scholarship_name;
				$temp[$row->ps_id_prodi][$row->ps_id_scholarship]['id'] 		= $row->scholarship_id;
			} 
		} 
		
		if(!empty($dbs))
		{
			$pin = array();
			foreach($dbs as $db)
			{
				$scholarship 	= (int) $db->sst_scholarship;
				$status 		= (int) $db->sst_status;
				$choice 		= (int) $db->sst_choice;
				$prodi  		= (int) $db->sst_choice_prodi; 
				
				if($status > 1) {
					$status = 1;
				}  

				if(!empty($db->sreg_id) and $status == 1 and $choice != 0 and $prodi != 0 and ($db->sreg_status_pass == 'N' || $db->sreg_status_pass==null))  
				{ 
					$pin[] = $db->sreg_id_pin;
					$item['sreg_status_pass'] 	= ($status == 1 ? 'Y' : 'N');
					$item['sreg_choice'] 		= $choice;
					$item['sreg_choice_prodi'] 	= $prodi;
					$item['sreg_choice_date'] 	= date('Y-m-d'); 
					
					if($temp and $db->sst_scholarship!=0){
						$test['id'] 		= $scholarship;
						$test['amount'] 	= $temp[$prodi][$scholarship]['amount'];
						$test['name'] 		= $temp[$prodi][$scholarship]['name']; 
						
						$item['sreg_scholarship'] 		= json_encode($test);
					}
					$this->sr->edit($db->sst_id_sreg, $item);
				}
				else {
					$item['sreg_status_pass'] 	= ($status == 1 ? 'Y' : 'N');   
					$this->sr->edit($db->sst_id_sreg, $item);
				}
			}
			 
			$this->temp->truncate();
			
			$data['view'] 	= 'backend/simulation/step3';
			$data['title']	= 'Simulasi Kelulusan';		
			$data['icon']	= 'icon-podium';
			$this->send_email_kelulusan(implode(',',$pin));
			$this->load->view('backend/tpl', $data);
		}
		else redirect(y_url_admin().'/simulation/step2');
	}

	public function send_email_kelulusan($id)
	{ 

		$this->load->library('parser');   

		$text = '';
		
		$dbs = explode(",",$id); 

		$comp = $this->Ms_Component_Model->getby(array('component_status'=>'1'))->result();
		$comps = array();
		foreach($comp as $row){
			$comps[$row->component_name] = $row->component_bank;
		} 

		if(!empty($dbs))
		{
			foreach($dbs as $db){
				$student = $this->sr->getParticipantReg($db); 
				if($student)
				{
					$prd 						= json_decode($student->sreg_prodi,true);  
					$temp 					= $prd[$student->sreg_choice];		
				 
					$letter 				= $this->sr->getLetterNumber(date('Y'))->row();
					$month 					= month_roman_number();
				 
					if($student->sreg_letter_number==""){
						if($letter){
							$number = explode('/',$letter->sreg_letter_number);
							$number = sprintf("%04d",(int)$number[0]+1);
							$sreg_letter_number = $number.'/'.date('m/Y');
						}
						else {
							$number = '0001';
							$sreg_letter_number = $number.'/'.date('m/Y'); 
						} 

						$month = $month[date('m')];
						$year  = date('Y');

						$this->Settings_Model->update_value('letter_number', $sreg_letter_number);
					}
					else {
						$sreg_letter_number = $student->sreg_letter_number;
						$let = explode('/',$student->sreg_letter_number);
						$number = $let[0];
						$month  = $month[$let[1]];
						$year   = $let[2];
					}
					
					$table = '<table class="component" style="width:100%" cellpadding="0" cellspacing="0"> 
					<tr>
						<th width="50%" style="text-align:center;border-top:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;border-bottom:1px solid #000;padding:5px;">Rincian BIaya</th>
						<th width="30%" style="text-align:center;border-top:1px solid #000;border-right:1px solid #000;border-bottom:1px solid #000;padding:5px;">Nomor Virtual Account(VA)</th>
						<th width="20%" style="text-align:center;border-top:1px solid #000;border-right:1px solid #000;border-bottom:1px solid #000;padding:5px;">Total Biaya</th>
					</tr>';
					$i   	= 0;
					$total	= 0;
					$n		= 0;
					foreach ($temp as $key=>$val){
						if($key!='prodi'){
							$bank = $student->pin_transaction_number;
							if(array_key_exists($val['component'],$comps)){
								$bank = $comps[$val['component']].$bank;
							}
							$i++;
							// $n 			= $n+7;  
							$total 	= $total+$val['fee']+($val['component_custom']*$val['n']*1000000);
							$vals 	= $val['fee']+($val['component_custom']*$val['n']*1000000); 
							$table.= '<tr>
									<td style="text-align:center;border-left:1px solid #000;border-right:1px solid #000;border-bottom:1px solid #000;padding:5px;">'.$val['component'].'</td>
									<td style="text-align:center;border-right:1px solid #000;border-bottom:1px solid #000;padding:5px;">'.$bank.'</td>
									<td style="text-align:center;border-right:1px solid #000;border-bottom:1px solid #000;padding:5px;">'.y_num_idr($vals).'</td>
								</tr>';
						}
					}
					
					$table.= '<tr>
							<td style="text-align:center;border-left:1px solid #000;border-right:1px solid #000;border-bottom:1px solid #000;padding:5px;font-weight:bold;">TOTAL</td>
							<td style="text-align:center;border-right:1px solid #000;border-bottom:1px solid #000;padding:5px;font-weight:bold;"></td>
							<td style="text-align:center;border-right:1px solid #000;border-bottom:1px solid #000;padding:5px;font-weight:bold;"> '.y_num_idr($total).'</td>
						</tr></table>';  
					
					$datas = array(
							'pin' => $student->pin_transaction_number,
							'tanggal' => y_convert_date($student->sreg_choice_date),
							'nama' => $student->par_fullname,
							'sekolah' => $student->school_name,
							'prodi' => $student->prodi_name,
							'fakultas' => $student->prodi_faculty,
							'tabel' => $table,
							'no_surat' => $number,
							'month' => $month,
							'year' => $year,
							'total' => y_num_idr($total),
							'periode' => $student->periode_name,
							'pagebreak' => '<pagebreak><div style="padding-top:75px;"></div>',
					); 

					$dtsreg['sreg_email_sent_status'] = 'SENT';
					$dtsreg['sreg_letter_number'] 	  = $sreg_letter_number;
					$this->sr->edit($student->sreg_id, $dtsreg);
		
					// $setting = y_load_setting(); 
					$settings = $this->Settings_Model->getall()->result(); 
							
					$setting = array();
					
					foreach($settings as $set)
						$setting[$set->setting_option] = $set->setting_value;
					  
					$data['string'] = $this->parser->parse_string($setting['template_passed'], $datas,true);
		
					$email = $student->par_email.'/pmb@ittelkom-sby.ac.id'; 
					$body  = $this->load->view('frontend/pendaftaran/cetak_detail_kelulusan', $data, true);  
					echo y_send_email($email, 'BUKTI KELULUSAN', $body,$setting['file_payment_method']);   
					// echo y_send_email('pmb@ittelkom-sby.ac.id', 'BUKTI KELULUSAN '.$student->par_fullname, $body,$setting['file_payment_method']); 
					sleep(10); 
				}
			} 
		} 
		return true; 
	} 
}

?>