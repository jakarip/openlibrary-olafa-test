<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Participant_registration extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Student_Model', 'dm', TRUE);
		$this->load->model('Participant_Registration_Model', 'sreg', TRUE);
		$this->load->model('Ms_Course_Model', '', TRUE); 
		$this->load->model('Ms_Prodi_Model', '', TRUE);
		$this->load->library('PHPExcel');
		$this->load->model('Common_Model', 'cm', TRUE);
		$this->load->model('Periode_Model', 'pm', TRUE); 
		$this->load->model('Settings_Model', '', TRUE);
		$this->load->model('Ms_Component_Model', '', TRUE); 
		ini_set('memory_limit', '-1');
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/participant_registration/index';
		$data['title']	= 'Data Participant Registrasi';		
		$data['icon']	= 'icon-user-lock';
		$data['periode'] = $this->pm->getAllOrderByDate()->result();
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;

		$columns = array(
			array( 'db' => 'sreg_id', 'dt' => 0 ),
			array( 'db' => 'pin_transaction_number', 'dt' => 2 ),
			array( 'db' => 'periode_code', 'dt' => 3 ),
			array( 'db' => 'par_fullname', 'dt' => 4 ),
			array( 'db' => 'sreg_status_pass', 'dt' =>5 ),
			array( 'db' => 'sreg_last_update', 'dt' => 6 ),
			array( 'db' => 'sreg_letter_number', 'dt' => 7),
			array( 'db' => 'pin_token', 'dt' => 8 ),
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();

		
		$periode 	= $this->input->post('periode');  
		$status 	= $this->input->post('status'); 

		if ($periode!=0){ 
			if(empty($param['where'])) 		$param['where'] = "WHERE (pin_id_periode='".$periode."')";
			else $param['where'] .= "AND (pin_id_periode='".$periode."')"; 
		}

		if(empty($param['where'])) {
			if(!empty($status))	{
				if ($status=='Y') 	$param['where'] = " WHERE sreg_status_pass='Y'";  
				else $param['where'] = " WHERE sreg_status_pass is null or sreg_status_pass='N'";  
			}
		} else {
			if(!empty($status))	{
				if ($status=='Y') 	$param['where'] .= " AND sreg_status_pass='Y'";  
				else $param['where'] .= "  AND (sreg_status_pass is null or sreg_status_pass='N')";  
			}
		}
		  
		$result = $this->sreg->dtquery($param)->result();
		$filter = $this->sreg->dtfiltered();
		$total	= $this->sreg->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		$i=0;
		foreach($result as $row)
		{
			if($row->sreg_status_pass == 'Y'){  
				$email = '<a href="javascript:send_email('.$row->sreg_id_pin.')" title="Send Email '.$row->pin_transaction_number.'" class="btn btn-xs btn-icon btn-primary"><i class="icon-envelop3"></i></a>'; 
				$label = '<span class="label label-success">Diterima</span>';
			}
			else {
				$email = "";
				$label = '<span class="label label-warning">Step '.$row->sreg_step.': '.$this->cm->get_form_step('', $row->periode_track_type,$row->sreg_step).'</span>';
			}
			$resetnotransaksi = ''; 
			if($row->sreg_status_pass == null){
				$resetnotransaksi = '<a href="javascript:reset_notransaksi('.$row->sreg_id.')" title="Reset '.$row->pin_transaction_number.'" class="btn btn-xs btn-icon btn-danger"><i class="icon-reset"></i></a>'; 
			} 

			 
			$resetstatusbutton = '';
			if($row->sreg_status_pass==null){
				$resetstatusbutton = '<button title="Reset '.$row->pin_transaction_number.'" class="btn btn-xs btn-icon btn-warning" onClick="reset_status('.$row->sreg_id_pin.')"><i class="icon-reset"></i></button>';
			} 


			if(empty($row->sreg_email_sent_status)){  
				$email_status = '';
			}
			else { 
				$email_status = '<span class="label label-success">Sent</span>';
			}
			
			

			$rows = array (
				'<input type="checkbox" class="chk-location" value="'.$row->sreg_id_pin.'">', 
				$email,
				$row->pin_transaction_number,
				$row->periode_name.' / '.$row->periode_track_type.' ('.y_convert_date($row->periode_start_date,'d/m/Y').' - '.y_convert_date($row->periode_end_date,'d/m/Y').')',
				$row->par_fullname,
				date('d/m/Y H:i', strtotime($row->sreg_last_update)),
				$label, 
				$row->sreg_letter_number, 
				$resetstatusbutton,
				$email_status,
				$resetnotransaksi
			);
			
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	}

	public function excels()
	{
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setTitle("Data Calon Mahasiswa PMB")
						 ->setDescription("description");			
						 
		$id  = $this->input->post('id');  
		// print_r($id);  
		$result = $this->sreg->getall_view($id)->result();
		
		$prodi_db = $this->Ms_Prodi_Model->getall()->result();
		$prodi = array();
		foreach($prodi_db as $pd)
			$prodi[$pd->prodi_id] = $pd->prodi_name;
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Data Calon Mahasiswa');
			 
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'No Participant');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'PIN');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'Status');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'Jalur');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'Nama Lengkap');
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'Nama Lokasi Ujian'); 
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'Alamat Lokasi Ujian'); 
		
		$i=5; 
		foreach($result as $row)
		{
			$i++;
			
			$col=6; 
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row->par_participantnumber); 
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row->pin_transaction_number);  
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, (empty($row->pin_transaction_number)?'':'Step '.$row->sreg_step.': '.$this->cm->get_form_step('', $row->periode_track_type,$row->sreg_step))); 
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row->periode_name); 
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row->par_fullname); 
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row->location_name);  
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row->location_address);  
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
		$filePath = 'cdn/Data Lokasi Ujian Calon Mahasiswa.xlsx';
		$objWriter->save($filePath);
		echo $filePath;
	}

	public function send_email_kelulusan()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;

		$this->load->library('parser');  
		
		$id  = $this->input->post('id');  
		// print_r($id); 
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
				$student = $this->sreg->getParticipantReg($db); 
				if($student)
				{
					$prd 						= json_decode($student->sreg_prodi,true);  
					$temp 					= $prd[$student->sreg_choice];		
				 
					$letter 				= $this->sreg->getLetterNumber(date('Y'))->row();
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
					$this->sreg->edit($student->sreg_id, $dtsreg);
		
					// $setting = y_load_setting(); 
					$settings = $this->Settings_Model->getall()->result(); 
							
					$setting = array();
					
					foreach($settings as $set)
						$setting[$set->setting_option] = $set->setting_value;
					  
					$data['string'] = $this->parser->parse_string($setting['template_passed'], $datas,true);
		
		
					$email = $student->par_email.'/yudhiadi1000@gmail.com'; 
					$body  = $this->load->view('frontend/pendaftaran/cetak_detail_kelulusan', $data, true);  
					echo y_send_email($email, 'BUKTI KELULUSAN', $body,$setting['file_payment_method']);   
					echo json_encode(array('status' => 'ok;', 'text' => ''));
				}
			} 
		} 
		return true; 
	} 

	

	
	
	public function send_email_kelulusans()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$this->load->library('parser');  

		$id   = $this->input->post('id');
		// print_r($id);
		$student = $this->sreg->getParticipantReg($id);
		if($student)
		{
			$prd 				= json_decode($student->sreg_prodi,true);  
			$temp 				= $prd[$student->sreg_choice];		
			
			
			$table = '<table class="component" style="width:100%" cellpadding="0" cellspacing="0"> 
			<tr>
				<th width="50%" style="text-align:center;border-top:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;border-bottom:1px solid #000;padding:5px;">Biaya Pendidikan</th>
				<th width="20%" style="text-align:center;border-top:1px solid #000;border-right:1px solid #000;border-bottom:1px solid #000;padding:5px;">Jumlah</th>
				<th width="30%" style="text-align:center;border-top:1px solid #000;border-right:1px solid #000;border-bottom:1px solid #000;padding:5px;">Batas Pembayaran</th>
			</tr>';
			$i   	= 0;
			$total	= 0;
			$n		= 0;
			foreach ($temp as $key=>$val){
				if($key!='prodi'){
					
					$i++;
					$n 			= $n+7;  
					$total 	= $total+$val['fee']+($val['component_custom']*$val['n']*1000000);
					$vals 	= $val['fee']+($val['component_custom']*$val['n']*1000000); 
					$table.= '<tr>
							<td style="text-align:center;border-left:1px solid #000;border-right:1px solid #000;border-bottom:1px solid #000;padding:5px;">Tahap '.$i.'<br>'.$val['component'].'</td>
							<td style="text-align:center;border-right:1px solid #000;border-bottom:1px solid #000;padding:5px;">'.y_num_idr($vals).'</td>
							<td style="text-align:center;border-right:1px solid #000;border-bottom:1px solid #000;padding:5px;">Tanggal (N+'.$n.') hari setelah pengumuman kelulusan</td>
						</tr>';
				}
			}
			
			$table.= '<tr>
					<td style="text-align:center;border-left:1px solid #000;border-right:1px solid #000;border-bottom:1px solid #000;padding:5px;font-weight:bold;">TOTAL</td>
					<td style="text-align:center;border-right:1px solid #000;border-bottom:1px solid #000;padding:5px;font-weight:bold;"> '.y_num_idr($total).'</td>
					<td style="text-align:center;border-right:1px solid #000;border-bottom:1px solid #000;padding:5px;font-weight:bold;"></td>
				</tr></table>'; 

			
			
			
			$datas = array(
					'pin' => $student->pin_transaction_number,
					'tanggal' => y_convert_date($student->sreg_choice_date),
					'nama' => $student->par_fullname,
					'sekolah' => $student->school_name,
					'prodi' => $student->prodi_name,
					'tabel' => $table,
					'pagebreak' => '<pagebreak><div style="padding-top:75px;"></div>',
			); 


			$setting = y_load_setting(); 
			$data['string'] = $this->parser->parse_string($setting['template_passed'], $datas,true);


			$body = $this->load->view('frontend/pendaftaran/cetak_detail_kelulusan', $data, true); 
			echo y_send_email($student->par_email, 'BUKTI KELULUSAN', $body); 
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		}
		else
		echo json_encode(array('status' => 'error;', 'text' => 'Data tidak ditemukan'));
	}

	

	// public function send_email($pin)
	// {
	// 		$data['setting'] = y_load_setting();
	// 		$data['pin']     = $pin;
	// 		$data['string'] = $this->parser->parse_string($dt->setting_value, $datas,true);

	// 		$body = $this->load->view('frontend/pin/email_request_template', $data, true);

	// 		echo y_send_email($this->session->userdata('participant_login_info')->par_email, '[NOMOR TRANSAKSI] Request Nomor Transaksi Online '.$data['setting']['website_name'], $body);
	// }
	
	public function excel2()
	{
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setTitle("Data Calon Mahasiswa SMBB")
						 ->setDescription("description");			
		
		$result = $this->dm->getall_view()->result();
			
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Data Calon Mahasiswa');
			
		$objPHPExcel->getActiveSheet()->setCellValue('A4', '');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'Nama Lengkap');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'Jenis Kelamin');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'Tempat, Tanggal Lahir');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'Asal Sekolah');
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'Email'); 
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'No. Telp'); 
		$objPHPExcel->getActiveSheet()->setCellValue('H4', 'No. HP'); 
		$objPHPExcel->getActiveSheet()->setCellValue('I4', 'Alamat');
		
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
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row->sreg_gender); 
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row->sreg_birthplace.', '.date('d/m/Y', strtotime($row->sreg_birthdate)) ); 
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row->school_name); 
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row->sreg_email);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row->sreg_phone);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row->sreg_mobile);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, 'Kec. '.$row->kec_name.', '.$row->kec_kab.', Prov. '.$row->kec_prov );
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
	
	public function reset_status()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id   = $this->input->post('id');
		
		if( $this->sreg->reset_status_byid($id) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function reset_notransaksi()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id   = $this->input->post('id');  
		$dt = $this->sreg->getbyid($id)->row();

		if($dt->sreg_active=='Y'){ 
			$data['sreg_status_print'] = 'Y';
		} 

		$data['sreg_status_pass'] = 'N';
		
		if( $this->sreg->edit($id,$data))
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
}

?>