<?php
ini_set('MAX_EXECUTION_TIME', -1);
ini_set('memory_limit','-1');

class Sbkpprint extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
		$this->load->model('SbkpprintModel');
		// $this->load->library('PHPExcel/PHPExcel');  
		$this->load->model('ApiMobileModel', 'api', TRUE); 
		if(!$this->session->userdata('login')) redirect('');	
    }  
	
	public function index(){ 
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');   
        $data['menu']		= 'bebaspustaka/sbkpprint'; 
        $this->load->view('theme',$data);
    }
	  

    public function ajax_json(){
		

		$where = "where 1=1 ";
		
		if ($_POST['dates']!="" ){
			$data['dates'] 	= $_POST['dates'];  
			$tgl 	= explode(' to ',$_POST['dates']);
			$start 	= explode('-',$tgl[0]);
			$end 	= explode('-',$tgl[1]);
			$where .="and 
			f.created_at BETWEEN '".$start[2]."-".$start[1]."-".$start[0]." 00:00:00' AND '".$end[2]."-".$end[1]."-".$end[0]."  23:59:59'";   
		}  

		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$table 		= "select * from free_letter f $where";
		$colOrder 	= array(null,'letter_number','member_number','name','donated_item_title','donated_item_author', 'created_at',null); //set column field database for datatable orderable
		$colSearch 	= array('letter_number','member_number','name','donated_item_title','donated_item_author', 'created_at'); //set column field database for datatable
		$order 		= "order by created_at desc"; // default order  
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);  
		
		$list = $this->datatables->get_datatables();
		
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $dt){
            $no++;
            $row = array();
            $row[] = '<div class="text-center">'.$no.'</div>';
            $row[] = $dt->letter_number;
            $row[] = $dt->member_number;
            $row[] = $dt->name;
            $row[] = $dt->donated_item_title;
            $row[] = $dt->donated_item_author; 
            $row[] = $dt->created_at; 
            $row[] = '<div class="btn-group"> <a type="button" class="btn btn-sm btn-success" title="'.getLang("print").'" target="_blank" href="/olafa/index.php/sbkpprint/prints/'.$dt->id.'"><i class="fa fa-print"></i></a></div>'; 
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

	public function lists_excel()
	{  
		if(!$this->input->is_ajax_request()) return false; 
		 
	 
		$where = "where 1=1 ";
		
		if ($_POST['dates']!="" ){
			$data['dates'] 	= $_POST['dates'];  
			$tgl 	= explode(' to ',$_POST['dates']);
			$start 	= explode('-',$tgl[0]);
			$end 	= explode('-',$tgl[1]);
			$where .="and 
			f.created_at BETWEEN '".$start[2]."-".$start[1]."-".$start[0]." 00:00:00' AND '".$end[2]."-".$end[1]."-".$end[0]."  23:59:59'";   
		}  
		else{
			return false;
		}
 
		
		$book = $this->SbkpprintModel->getSBKP($where)->result();
		$objPHPExcel = new PHPExcel();
	
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("SBKP")
									 ->setTitle("SBKP")
									 ->setSubject("SBKP")
									 ->setDescription("SBKP")
									 ->setKeywords("SBKP")
									 ->setCategory("SBKP");


		$sheet = $objPHPExcel->getActiveSheet();
		
		
		
		$sheet->getStyle("A1:H3")->getFont()->setBold(true)
				->setSize(12);									
		
		$sheet->getStyle('A3:H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	  
		
		$styleArray = array(
		  'borders' => array(
			'allborders' => array(
			  'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		  )
		);

		// $objPHPExcel->getActiveSheet()->getStyle('A4:Y500')->applyFromArray($styleArray);
		// unset($styleArray);
		
		$objPHPExcel->setActiveSheetIndex(0)
					 
					->setCellValue('A1', "SBKP ".$_POST['dates']) 
					->setCellValue('A3', "No Surat") 
					->setCellValue('B3', "Anggota")
					->setCellValue('C3', "Nama")
					->setCellValue('D3', "Judul")
					->setCellValue('E3', "Pengarang")
					->setCellValue('F3', "Tanggal Pembuatan");
		
		$no = 1;
		$i  = 4;
		foreach ($book as $row){
			
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValueExplicit('A'.$i, $row->letter_number, PHPExcel_Cell_DataType::TYPE_STRING) 
					->setCellValueExplicit('B'.$i, $row->member_number, PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('C'.$i, $row->name, PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('D'.$i, $row->donated_item_title, PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('E'.$i, $row->donated_item_author, PHPExcel_Cell_DataType::TYPE_STRING) 
					->setCellValueExplicit('F'.$i, $row->created_at, PHPExcel_Cell_DataType::TYPE_STRING) ;
			$no++;
			$i++;
		}
					
					 
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('SBKP');


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);


		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="SBKP".xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		//echo $objWriter->save('php://output');
		
		$filePath = FCPATH.'downloads/SBKP.xlsx';
		//echo $filePath;
		$objWriter->save($filePath);
		echo json_encode(urlencode('SBKP.xlsx'));
		exit();
	}
	 
	
	
	public function download($file)
	{
		$this->load->helper('download');
		$file = urldecode($file);
		$data = file_get_contents(FCPATH."downloads/".$file); // Read the file's contents
		$name = $file;

		force_download($name, $data); 
	}
	
	function prints($id){
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');   
		$data['dt'] = $this->SbkpprintModel->sbkpprint($id)->row(); 
	
        $this->load->view('bebaspustaka/sbkpprint_print',$data); 
    }  
	
	function auto_data(){
		$dt = $this->SbkpprintModel->member(strtolower($_GET['q']))->result_array();
		$arr = array();
		foreach ($dt as $row){
			$tab['id'] 	= $row['master_data_user'];
			$tab['name'] 	= $row['master_data_user']." - ".$row['master_data_fullname']." (".$row['NAMA_PRODI'].")";
			$arr[] = $tab;
			
		}
		echo json_encode($arr);
    }  
	 
	 function insert(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $item 					= $_POST['inp']; 

		$rent = $this->api->getRentNotYetReturnUsername($item['member_number'])->result();
		//  print_r($rent);
		
		if($rent){   
		 
			foreach($rent as $key => $row){  
				if($row->penalty_per_day!=0) $this->calculatepenalty($row->id,$row->return_date_expected,$row->penalty_per_day,$row->member_id);  
			} 
		} 
		
		$prodi 					= $this->SbkpprintModel->getProdiFak($item['member_number'])->row(); 
		$auto_inc 				= $this->SbkpprintModel->auto_inc()->row(); 
		if(($prodi->amnesty==0 and !$rent and $prodi->sisa<=0) or $prodi->amnesty!=0){ 
			$item['letter_number'] 		= $auto_inc->AUTO_INCREMENT.'/AKD26/PUS/'.date('Y');
			$item['created_at'] 		= date('Y-m-d H:i:s'); 
			$item['created_by'] 		= $this->session->userdata('username');  
			$item['course_code'] 		= ($prodi->master_data_course==""?0:$prodi->master_data_course);     
			$item['is_member'] 			= 1;   
			$item['registration_number'] = $prodi->id;  
			$item['name'] 				= $prodi->master_data_fullname;   
			$item2['status'] = '2';
			$this->SbkpprintModel->edit_member($prodi->id,$item2);
			if ($this->SbkpprintModel->add($item)) echo json_encode(array("status" => 'true'));
			else echo json_encode(array("status" => 'false'));
		}
		else echo json_encode(array("status" => 'denda'));
    }

	private function calculatePenalty($rent_id, $return_date_expected, $penalty_per_day, $member_id, $current_date = null) {  
		if ($current_date == null) $current_date = date('Y-m-d');
		
		$do_calculate_penalty = true; 

		$start_calculate_penalty_date = $return_date_expected;
		
		$dt = $this->api->getLastRentPenalty($rent_id)->row();
		 
		if ($dt) {
		  // dont calculate if latest penalty is the same with given return date
		  if ($dt->penalty_date == $current_date) {
			$do_calculate_penalty = false;
			//return;
		  }
		  $start_calculate_penalty_date = $dt->penalty_date;
		}

		if ($do_calculate_penalty) {
		  // get holidays between expected return date and today
		  
			$holidays_on_penalty = $this->api->getHoliday($start_calculate_penalty_date, $current_date)->result();
			// $holidays_on_penalty = HolidayPeer::getHolidaysBetween($start_calculate_penalty_date, $current_date);
			$holidays_date = array();
			foreach ($holidays_on_penalty as $holiday) $holidays_date[$holiday->holiday_date] = 1;

			$day_round = 60*60*24; // store 1 day in second
			$today_time = floor(strtotime($current_date)/$day_round);
			$expected_return_time = floor(strtotime($start_calculate_penalty_date)/$day_round);
			// echo "today : ".$today." expected : $start_calculate_penalty_date ".strtotime($start_calculate_penalty_date);
			if ($today_time - $expected_return_time > 0) $day_on_penalty = $today_time - $expected_return_time;
			else $day_on_penalty = 0;
			
			
			// echo "day_on_penalty : ".$day_on_penalty;
				  
			for ($i = 0; $i < $day_on_penalty; $i++) {
				$penalty_date = date('Y-m-d', strtotime("-{$i} day", strtotime($current_date)));
				if (!array_key_exists($penalty_date, $holidays_date)) {
					$penalty['member_id'] = $member_id; 
					$penalty['rent_id'] = $rent_id;  
					$penalty['penalty_date'] = $penalty_date;  
					$penalty['amount'] = $penalty_per_day;  
					
					// print_r($penalty);
					$this->api->addRentPenalty($penalty);
				}
			}
		}
		
		
		$dt = $this->api->countsumRentPenalty($rent_id)->row();
		$holiday = $this->api->countHoliday($return_date_expected, $current_date)->row();
		
		$data['penalty_day'] = $dt->total;
		$data['penalty_total'] = $dt->total_amount;
		$data['penalty_holiday'] = $holiday->total;
		
	 
		$where = "id= '".$rent_id."'"; 
		$this->api->updateRent($data,$where);
		
		
		return $dt->total_amount;
	}  


    function update(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
        $item   = $_POST['inp'];  
		
		$item['datestart'] 		= convert_format_date($_POST['start']);
		$item['datefinish'] 		= convert_format_date($_POST['end']);
		$item['year'] 			= substr($_POST['end'],6,4);
		if ($this->SbkpprintModel->edit($id, $item)) echo json_encode(array("status" => FALSE));
		else echo json_encode(array("status" => TRUE));
    }

    function edit(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
        $data 	= $this->SbkpprintModel->getbyid($id)->row();
		$data->datestart		= convert_format_date($data->datestart);
		$data->datefinish 			= convert_format_date($data->datefinish);
        echo json_encode($data);
    }
	
	public function changestatus(){ 
		
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');  
        $data['menu']		= 'monitoringeproceeding/changestatus';
        $this->load->view('theme',$data);
    }
 
}

?>