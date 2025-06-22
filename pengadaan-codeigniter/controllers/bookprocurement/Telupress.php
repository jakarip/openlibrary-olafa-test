<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Telupress extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		 
		$this->load->model('Member_Model', 'dm', TRUE); 
		$this->load->model('bookprocurement/Telupress_Model', 'sm', TRUE); 
		// $this->load->library('PHPExcel');
		$this->load->model('Common_Model', 'cm', TRUE);
		$this->load->helper('form');
		ini_set('memory_limit', '-1');  
		if (!$this->session->userdata('user_login_apps')) redirect('login');
		
		// y_is_login('bookprocurement');
	}
	
	public function index()
	{ 
		$data['view'] 	= 'bookprocurement/telupress/index';
		$data['title']	= 'Data Pengajuan TelU Press';		
		$data['icon']	= 'icon-book3';

		$prodi 	= $this->sm->getprodi()->result(); 
		$temp[''] = "Semua Prodi"; 
		$temp2[''] = "Pilih Prodi"; 
		foreach($prodi as $row){
			$temp[$row->C_KODE_PRODI] = ucwords(strtolower($row->NAMA_FAKULTAS)).' - '.ucwords(strtolower($row->NAMA_PRODI));
			$temp2[$row->C_KODE_PRODI] = ucwords(strtolower($row->NAMA_FAKULTAS)).' - '.ucwords(strtolower($row->NAMA_PRODI));
		}
		$data['prodi'] = $temp;
		$data['prodi_input'] = $temp2;

		
		$data['step'] = $this->step();
		$data['status'] = $this->status(); 
		
		$this->load->view('frontend/tpl', $data);
	}

	public function step()
	{
		return array(
			'1'=>'3',
			'2'=>'40',
			'3'=>'20',
			'4'=>'20',
			'5'=>'7',
			'6'=>'10'
		); 
	}
	public function status()
	{
		return array(
			''=>'Semua Jenis Status',
			'1'=>'Pengajuan Naskah',
			'2'=>'Review Naskah',
			'3'=>'Editing & Proofread',
			'4'=>'Layout',
			'5'=>'ISBN',
			'6'=>'Cetak',
			'7'=>'Sudah Diterima'
		);
	}

	
	
	public function getlecturerid()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('searchTerm')) return false;
		
		$s = $this->input->post('searchTerm');
		$dbs = $this->sm->getlecturer($s)->result();
		
		$result = array(); 
		foreach($dbs as $db)
			$result[] = array('id' => $db->id,
							  'text' => '( '.$db->master_data_number.') - '.$db->master_data_fullname);
		
		echo json_encode($result);
	}  
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;

		
		$prodi 	 			 			= $this->input->post('prodi');   
		// $type 	  						= $this->input->post('type');   
		$status 	  					= $this->input->post('status');   
		// $dates_submission_option 		= $this->input->post('dates_submission_option'); 
		// $dates_logistic_option 			= $this->input->post('dates_logistic_option'); 
		// $dates_acceptance_option 		= $this->input->post('dates_acceptance_option'); 
		// $dates_email_confirmed_option 	= $this->input->post('dates_email_confirmed_option'); 
		// $dates_available_option 		= $this->input->post('dates_available_option'); 
		// $dates_submission 				= $this->input->post('dates_submission'); 
		// $dates_logistic 				= $this->input->post('dates_logistic'); 
		// $dates_acceptance 				= $this->input->post('dates_acceptance'); 
		// $dates_email_confirmed 				= $this->input->post('dates_email_confirmed'); 
		// $dates_available 				= $this->input->post('dates_available'); 

		// $option = "";
		// if($dates_submission_option!='all'){
		// 	$temp = explode(' - ',$dates_submission);

		// 	$date1 = y_convert_date($temp[0], 'Y-m-d');
		// 	$date2 = y_convert_date($temp[1], 'Y-m-d');
		// 	$option = "(book_date_prodi_submission between '$date1' and '$date2')";
		// }
		
		// if($dates_logistic_option!='all'){
		// 	$temp = explode(' - ',$dates_logistic);

		// 	$date1 = y_convert_date($temp[0], 'Y-m-d');
		// 	$date2 = y_convert_date($temp[1], 'Y-m-d');
		// 	$option .= ($option!="" ? " and" : "")." (book_date_logistic_submission between '$date1' and '$date2')";
		// }
		
		// if($dates_acceptance_option!='all'){
		// 	$temp = explode(' - ',$dates_acceptance);

		// 	$date1 = y_convert_date($temp[0], 'Y-m-d');
		// 	$date2 = y_convert_date($temp[1], 'Y-m-d');
		// 	$option .= ($option!="" ? " and" : "")." (book_date_acceptance between '$date1' and '$date2')";
		// } 
		
		// if($dates_email_confirmed_option!='all'){
		// 	$temp = explode(' - ',$dates_email_confirmed);

		// 	$date1 = y_convert_date($temp[0], 'Y-m-d');
		// 	$date2 = y_convert_date($temp[1], 'Y-m-d');
		// 	$option .= ($option!="" ? " and" : "")." (book_date_email_confirmed between '$date1' and '$date2')";
		// } 
		
		// if($dates_available_option!='all'){
		// 	$temp = explode(' - ',$dates_available);

		// 	$date1 = y_convert_date($temp[0], 'Y-m-d');
		// 	$date2 = y_convert_date($temp[1], 'Y-m-d');
		// 	$option .= ($option!="" ? " and" : "")." (book_date_available between '$date1' and '$date2')";
		// } 
	
		$columns = array( 
			array( 'db' => 'book_status', 'dt' => 0 ), 
			array( 'db' => 'book_status', 'dt' => 1 ),
			array( 'db' => 'master_data_fullname', 'dt' =>2 ),
			array( 'db' => 'NAMA_FAKULTAS', 'dt' =>3 ),
			array( 'db' => 'NAMA_PRODI', 'dt' =>4 ),
			array( 'db' => 'book_title', 'dt' => 5 ),
			array( 'db' => 'book_startdate_target_step_1', 'dt' => 6 ),
			array( 'db' => 'book_startdate_realization_step_1', 'dt' => 7 ), 
			array( 'db' => 'book_startdate_target_step_2', 'dt' => 8 ),
			array( 'db' => 'book_startdate_realization_step_2', 'dt' => 9 ),  
			array( 'db' => 'book_startdate_target_step_3', 'dt' => 10 ),
			array( 'db' => 'book_startdate_realization_step_3', 'dt' => 11 ), 
			array( 'db' => 'book_startdate_target_step_4', 'dt' => 12 ),
			array( 'db' => 'book_startdate_realization_step_4', 'dt' => 13 ),  
			array( 'db' => 'book_startdate_target_step_5', 'dt' => 14 ),
			array( 'db' => 'book_startdate_realization_step_5', 'dt' => 15 ),  
			array( 'db' => 'book_startdate_target_step_6', 'dt' => 16 ),
			array( 'db' => 'book_startdate_realization_step_6', 'dt' => 17 ),  
			array( 'db' => 'book_desc', 'dt' => 18 ),
			array( 'db' => 'book_received_date', 'dt' => 19 ),
			array( 'db' => 'book_cost', 'dt' => 20 ),
			array( 'db' => 'total_proses_naskah_cetak', 'dt' => 21 )
	);
		$this->datatables->set_cols($columns);

		$param	= $this->datatables->query();  

		// if ($type!=""){ 
		// 	if(empty($param['where'])) 	$param['where'] = "WHERE (book_type='".$type."')";
		// 	else $param['where'] .= "AND (book_type='".$type."')"; 
		// }   

		if ($status!=""){ 
			if(empty($param['where'])) 	$param['where'] = "WHERE (book_status='".$status."')";
			else $param['where'] .= "AND (book_status='".$status."')"; 
		} 

		if ($prodi!=""){ 
			if(empty($param['where'])) 	$param['where'] = "WHERE (book_id_prodi='".$prodi."')";
			else $param['where'] .= "AND (book_id_prodi='".$prodi."')"; 
		} 

		
		// if ($option!=""){ 
		// 	if(empty($param['where'])) 	$param['where'] = "WHERE (".$option.")";
		// 	else $param['where'] .= "AND (".$option.")";
		// }

		$result = $this->sm->dtquery($param)->result();
		$filter = $this->sm->dtfiltered();
		$total	= $this->sm->dtcount();
		$output = $this->datatables->output($total, $filter);

		$status_type 		= $this->status(); 
		$iuser = $this->session->userdata(); 
		foreach($result as $row)
		{
			$btn = "";
			$accept = "";
			$logistic = "";
			$email_confirmed = "";
			$available = "";
			$checkbox = "";
			$conclusion = "";


			if($row->book_status == '7')
			{
					$status = '<label class="label label-success"><strong>'.$status_type[$row->book_status].'</strong></label>';
			
					if($row->proses_step6<0){
						$conclusion = '<label class="label label-danger"><strong>Melewati SLA</strong></label>';
					}
					else $conclusion = '<label class="label label-success"><strong>Memenuhi SLA</strong></label>';
			} 
			else if($row->book_status == '6')
			{ 
				$status = '<label class="label label-ffa18e"><strong>'.$status_type[$row->book_status].'</strong></label>';

				if($row->proses_step6<0){
					$conclusion = '<label class="label label-danger"><strong>Melewati SLA</strong></label>';
				}
				else $conclusion = '<label class="label label-success"><strong>Memenuhi SLA</strong></label>';
			} 
			else if($row->book_status == '5')
			{
					$status = '<label class="label label-550080"><strong>'.$status_type[$row->book_status].'</strong></label>';
			} 
			else if($row->book_status == '4')
			{
					$status = '<label class="label label-ff0090"><strong>'.$status_type[$row->book_status].'</strong></label>';
			} 
			else if($row->book_status == '3')
			{
					$status = '<label class="label label-info"><strong>'.$status_type[$row->book_status].'</strong></label>';
			} 
			else if($row->book_status == '2')
			{
					$status = '<label class="label label-warning"><strong>'.$status_type[$row->book_status].'</strong></label>';
			} 
			else if($row->book_status == '1')
			{
					$status = '<label class="label label-danger"><strong>'.$status_type[$row->book_status].'</strong></label>';
			} 
			// else if($row->book_status == 'q_email')
			// {
			// 		$status = '<label class="label label-primary"><strong>'.$status_type[$row->book_status].'</strong></label>';
			// 		$available = ' <a href="javascript:edit_available('.$row->book_id.')" title="Input Tanggal Ketersediaan Buku" class="btn btn-xs btn-icon btn-success"><i class="icon-calendar"></i></a>'; 
			// 		$email_confirmed = ' <a href="javascript:edit_email_confirmed('.$row->book_id.')" title="Input Tanggal Konfirmasi Email" class="btn btn-xs btn-icon btn-primary"><i class="icon-calendar"></i></a>'; 
			// 		$accept = ' <a href="javascript:edit_accept('.$row->book_id.')" title="Input Tanggal Penerimaan" class="btn btn-xs btn-icon btn-info"><i class="icon-calendar"></i></a>'; 
			// 		$logistic = ' <a href="javascript:logistics('.$row->book_id.',\'edit\')" title="Ubah Data Pengajuan ke Logistik" class="btn btn-xs btn-icon btn-warning"><i class="icon-calendar3"></i></a>';  
			// }
			// else if($row->book_status == 'penerimaan')
			// {
			// 		$status = '<label class="label label-info"><strong>'.$status_type[$row->book_status].'</strong></label>'; 
			// 		$email_confirmed = ' <a href="javascript:edit_email_confirmed('.$row->book_id.')" title="Input Tanggal Konfirmasi Email" class="btn btn-xs btn-icon btn-primary"><i class="icon-calendar"></i></a>'; 
			// 		$accept = ' <a href="javascript:edit_accept('.$row->book_id.')" title="Input Tanggal Penerimaan" class="btn btn-xs btn-icon btn-info"><i class="icon-calendar"></i></a>'; 
			// 		$logistic = ' <a href="javascript:logistics('.$row->book_id.',\'edit\')" title="Ubah Data Pengajuan ke Logistik" class="btn btn-xs btn-icon btn-warning"><i class="icon-calendar3"></i></a>';  
			// }
			// else if($row->book_status == 'logistik')
			// { 
			// 	$status = '<label class="label label-warning"><strong>'.$status_type[$row->book_status].'</strong></label>'; 
			// 	$accept = ' <a href="javascript:edit_accept('.$row->book_id.')" title="Input Tanggal Penerimaan" class="btn btn-xs btn-icon btn-info"><i class="icon-calendar"></i></a>'; 
			// 	$logistic = ' <a href="javascript:logistics('.$row->book_id.',\'edit\')" title="Ubah Data Pengajuan ke Logistik" class="btn btn-xs btn-icon btn-warning"><i class="icon-calendar3"></i></a>';  
			// }
			// else if($row->book_status == 'pengajuan')
			// {
			// 	$status = '<label class="label label-danger"><strong>'.$status_type[$row->book_status].'</strong></label>';  
			// 	$checkbox = '<input type="checkbox" class="chk-logistic" value="'.$row->book_id.'">';
			// }   

			$btn = '<a href="javascript:edit('.$row->book_id.')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a> <a href="javascript:del('.$row->book_id.', \''.$row->book_title.'\')" title="Delete Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-trash"></i></a>';

			
			if ($iuser['usergroup']!='superadmin'){  
				$btn = "";
				$accept = "";
				$logistic = "";
				$checkbox = "";
			}
			if($row->proses_step1<0){
				$step1 = '<span style="color:red;">'.date ( 'd-m-Y', strtotime ( $row->book_startdate_realization_step_1)).' s/d '.date ( 'd-m-Y', strtotime ( $row->book_enddate_realization_step_1)).'</span>';
			}
			else { 
				if($row->book_startdate_realization_step_1!=null and $row->book_enddate_realization_step_1!=null)
					$step1 = '<span style="color:green;">'.date ( 'd-m-Y', strtotime ( $row->book_startdate_realization_step_1)).' s/d '.date ( 'd-m-Y', strtotime ( $row->book_enddate_realization_step_1)).'</span>';
				else 
					$step1 = ($row->book_startdate_realization_step_1!=""? date('Y-m-d', strtotime($row->book_startdate_realization_step_1)):null).' s/d '.($row->book_enddate_realization_step_1!=""? date('Y-m-d', strtotime($row->book_enddate_realization_step_1)):null);
			}

			if($row->proses_step2<0){
				$step2 = '<span style="color:red;">'.date ( 'd-m-Y', strtotime ( $row->book_startdate_realization_step_2)).' s/d '.date ( 'd-m-Y', strtotime ( $row->book_enddate_realization_step_2)).'</span>';
			}
			else { 
				if($row->book_startdate_realization_step_2!=null and $row->book_enddate_realization_step_2!=null)
					$step2 = '<span style="color:green;">'.date ( 'd-m-Y', strtotime ( $row->book_startdate_realization_step_2)).' s/d '.date ( 'd-m-Y', strtotime ( $row->book_enddate_realization_step_2)).'</span>';
				else 
					$step2 = ($row->book_startdate_realization_step_2!=""? date('Y-m-d', strtotime($row->book_startdate_realization_step_2)):null).' s/d '.($row->book_enddate_realization_step_2!=""? date('Y-m-d', strtotime($row->book_enddate_realization_step_2)):null);
			}

			if($row->proses_step3<0){
				$step3 = '<span style="color:red;">'.date ( 'd-m-Y', strtotime ( $row->book_startdate_realization_step_3)).' s/d '.date ( 'd-m-Y', strtotime ( $row->book_enddate_realization_step_3)).'</span>';
			}
			else { 
				if($row->book_startdate_realization_step_3!=null and $row->book_enddate_realization_step_3!=null)
					$step3 = '<span style="color:green;">'.date ( 'd-m-Y', strtotime ( $row->book_startdate_realization_step_3)).' s/d '.date ( 'd-m-Y', strtotime ( $row->book_enddate_realization_step_3)).'</span>';
				else  
					$step3 = ($row->book_startdate_realization_step_3!=""? date('Y-m-d', strtotime($row->book_startdate_realization_step_3)):null).' s/d '.($row->book_enddate_realization_step_3!=""? date('Y-m-d', strtotime($row->book_enddate_realization_step_3)):null);
			}

			if($row->proses_step4<0){
				$step4 = '<span style="color:red;">'.date ( 'd-m-Y', strtotime ( $row->book_startdate_realization_step_4)).' s/d '.date ( 'd-m-Y', strtotime ( $row->book_enddate_realization_step_4)).'</span>';
			}
			else { 
				if($row->book_startdate_realization_step_4!=null and $row->book_enddate_realization_step_4!=null)
					$step4 = '<span style="color:green;">'.date ( 'd-m-Y', strtotime ( $row->book_startdate_realization_step_4)).' s/d '.date ( 'd-m-Y', strtotime ( $row->book_enddate_realization_step_4)).'</span>';
				else  
					$step4 = ($row->book_startdate_realization_step_4!=""? date('Y-m-d', strtotime($row->book_startdate_realization_step_4)):null).' s/d '.($row->book_enddate_realization_step_4!=""? date('Y-m-d', strtotime($row->book_enddate_realization_step_4)):null);
			}

			if($row->proses_step5<0){
				$step5 = '<span style="color:red;">'.date ( 'd-m-Y', strtotime ( $row->book_startdate_realization_step_5)).' s/d '.date ( 'd-m-Y', strtotime ( $row->book_enddate_realization_step_5)).'</span>';
			}
			else { 
				if($row->book_startdate_realization_step_5!=null and $row->book_enddate_realization_step_5!=null)
					$step5 = '<span style="color:green;">'.date ( 'd-m-Y', strtotime ( $row->book_startdate_realization_step_5)).' s/d '.date ( 'd-m-Y', strtotime ( $row->book_enddate_realization_step_5)).'</span>';
				else 
					$step5 = ($row->book_startdate_realization_step_5!=""? date('Y-m-d', strtotime($row->book_startdate_realization_step_5)):null).' s/d '.($row->book_enddate_realization_step_5!=""? date('Y-m-d', strtotime($row->book_enddate_realization_step_5)):null);
			}

			if($row->proses_step6<0){
				$step6 = '<span style="color:red;">'.date ( 'd-m-Y', strtotime ( $row->book_startdate_realization_step_6)).' s/d '.date ( 'd-m-Y', strtotime ( $row->book_enddate_realization_step_6)).'</span>';
			}
			else { 
				if($row->book_startdate_realization_step_6!=null and $row->book_enddate_realization_step_6!=null)
					$step6 = '<span style="color:green;">'.date ( 'd-m-Y', strtotime ( $row->book_startdate_realization_step_6)).' s/d '.date ( 'd-m-Y', strtotime ( $row->book_enddate_realization_step_6)).'</span>';
				else 
					$step6 = ($row->book_startdate_realization_step_6!=""? date('Y-m-d', strtotime($row->book_startdate_realization_step_6)):null).' s/d '.($row->book_enddate_realization_step_6!=""? date('Y-m-d', strtotime($row->book_enddate_realization_step_6)):null);
			}

			$jml_hari_kerja = "-";
			if($row->book_startdate_realization_step_1!="" and $row->book_enddate_realization_step_6!=""){
				$jml_hari_kerja = $this->Count_Days_Without_Weekends($row->book_startdate_realization_step_1,$row->book_enddate_realization_step_6); 
			}
 
			$rows = array (
					$btn, 
					$status."<br>".$conclusion,
					$row->master_data_fullname, 
					$row->NAMA_FAKULTAS, 
					$row->NAMA_PRODI, 
					$row->book_title,  
					($row->book_startdate_target_step_1!=""? date('d-m-Y', strtotime($row->book_startdate_target_step_1)):null).' s/d '.($row->book_enddate_target_step_1!=""? date('d-m-Y', strtotime($row->book_enddate_target_step_1)):null),
					$step1,
					($row->book_startdate_target_step_2!=""? date('d-m-Y', strtotime($row->book_startdate_target_step_2)):null).' s/d '.($row->book_enddate_target_step_2!=""? date('d-m-Y', strtotime($row->book_enddate_target_step_2)):null),
					$step2,
					($row->book_startdate_target_step_3!=""? date('d-m-Y', strtotime($row->book_startdate_target_step_3)):null).' s/d '.($row->book_enddate_target_step_3!=""? date('d-m-Y', strtotime($row->book_enddate_target_step_3)):null),
					$step3,
					($row->book_startdate_target_step_4!=""? date('d-m-Y', strtotime($row->book_startdate_target_step_4)):null).' s/d '.($row->book_enddate_target_step_4!=""? date('d-m-Y', strtotime($row->book_enddate_target_step_4)):null),
					$step4,
					($row->book_startdate_target_step_5!=""? date('d-m-Y', strtotime($row->book_startdate_target_step_5)):null).' s/d '.($row->book_enddate_target_step_5!=""? date('d-m-Y', strtotime($row->book_enddate_target_step_5)):null),
					$step5,
					($row->book_startdate_target_step_6!=""? date('d-m-Y', strtotime($row->book_startdate_target_step_6)):null).' s/d '.($row->book_enddate_target_step_6!=""? date('d-m-Y', strtotime($row->book_enddate_target_step_6)):null),
					$step6,
					$row->book_desc, 
					($row->book_received_date!=""? date('d-m-Y', strtotime($row->book_received_date)):null),
					 "Rp " . number_format($row->book_cost,0,',','.'),
					$jml_hari_kerja
			); 

			$output['data'][] = $rows;
		}

		echo json_encode( $output );
	}

	function Count_Days_Without_Weekends($start, $end){
		$days_diff = floor(((abs(strtotime($end) - strtotime($start))) / (60*60*24)));
		$run_days=0;
		for($i=0; $i<=$days_diff; $i++){
			$newdays = $i-$days_diff;
			$futuredate = strtotime("$newdays days");
			$mydate = date("F d, Y", $futuredate);
			$today = date("D", strtotime($mydate));             
			if(($today != "Sat") && ($today != "Sun")){
				$run_days++;
			}
		}
	return $run_days;
	}
 
	
	
	public function getmember()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('searchTerm')) return false;
		
		$s = $this->input->post('searchTerm');
		$dbs = $this->sm->getmemberbyname($s)->result();
		
		$result = array(); 
		foreach($dbs as $db)
			$result[] = array('id' => $db->id,
							  'text' => $db->master_data_fullname);
		
		echo json_encode($result);
	} 
	
	public function insert()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		
		$item['book_startdate_realization_step_1'] = date('Y-m-d', strtotime($item['book_startdate_realization_step_1']));
		$item['book_enddate_realization_step_1'] = ($item['book_enddate_realization_step_1']!=""? date('Y-m-d', strtotime($item['book_enddate_realization_step_1'])):null);
		$item['book_startdate_realization_step_2'] = ($item['book_startdate_realization_step_2']!=""? date('Y-m-d', strtotime($item['book_startdate_realization_step_2'])):null);
		$item['book_enddate_realization_step_2'] = ($item['book_enddate_realization_step_2']!=""? date('Y-m-d', strtotime($item['book_enddate_realization_step_2'])):null); 
		$item['book_startdate_realization_step_3'] = ($item['book_startdate_realization_step_3']!=""? date('Y-m-d', strtotime($item['book_startdate_realization_step_3'])):null);
		$item['book_enddate_realization_step_3'] = ($item['book_enddate_realization_step_3']!=""? date('Y-m-d', strtotime($item['book_enddate_realization_step_3'])):null);
		$item['book_startdate_realization_step_4'] = ($item['book_startdate_realization_step_4']!=""? date('Y-m-d', strtotime($item['book_startdate_realization_step_4'])):null);
		$item['book_enddate_realization_step_4'] = ($item['book_enddate_realization_step_4']!=""? date('Y-m-d', strtotime($item['book_enddate_realization_step_4'])):null);
		$item['book_startdate_realization_step_5'] = ($item['book_startdate_realization_step_5']!=""? date('Y-m-d', strtotime($item['book_startdate_realization_step_5'])):null);
		$item['book_enddate_realization_step_5'] = ($item['book_enddate_realization_step_5']!=""? date('Y-m-d', strtotime($item['book_enddate_realization_step_5'])):null);
		$item['book_startdate_realization_step_6'] = ($item['book_startdate_realization_step_6']!=""? date('Y-m-d', strtotime($item['book_startdate_realization_step_6'])):null);
		$item['book_enddate_realization_step_6'] = ($item['book_enddate_realization_step_6']!=""? date('Y-m-d', strtotime($item['book_enddate_realization_step_6'])):null);
		$item['book_received_date'] = ($item['book_received_date']!=""? date('Y-m-d', strtotime($item['book_received_date'])):null);
		
		$step = $this->step();

		$item['book_startdate_target_step_1'] = $item['book_startdate_realization_step_1'];
		$item['book_enddate_target_step_1'] =  date ( 'Y-m-d', strtotime ( $item['book_startdate_target_step_1']. '+ '.($step[1]-1).' weekdays' ));
		$item['book_startdate_target_step_2'] =  date ( 'Y-m-d', strtotime ( $item['book_enddate_target_step_1'].  '+ 1 weekdays' )); 
		$item['book_enddate_target_step_2'] =  date ( 'Y-m-d', strtotime ( $item['book_startdate_target_step_2']. '+ '.($step[2]-1).' weekdays' ));
		$item['book_startdate_target_step_3'] =  date ( 'Y-m-d', strtotime ( $item['book_enddate_target_step_2'].  '+ 1 weekdays' )); 
		$item['book_enddate_target_step_3'] =  date ( 'Y-m-d', strtotime ( $item['book_startdate_target_step_3']. '+ '.($step[3]-1).' weekdays' ));
		$item['book_startdate_target_step_4'] =  date ( 'Y-m-d', strtotime ( $item['book_enddate_target_step_3'].  '+ 1 weekdays' )); 
		$item['book_enddate_target_step_4'] =  date ( 'Y-m-d', strtotime ( $item['book_startdate_target_step_4']. '+ '.($step[3]-1).' weekdays' ));
		$item['book_startdate_target_step_5'] =  date ( 'Y-m-d', strtotime ( $item['book_enddate_target_step_4'].  '+ 1 weekdays' )); 
		$item['book_enddate_target_step_5'] =  date ( 'Y-m-d', strtotime ( $item['book_startdate_target_step_5']. '+ '.($step[3]-1).' weekdays' ));
		$item['book_startdate_target_step_6'] =  date ( 'Y-m-d', strtotime ( $item['book_enddate_target_step_5'].  '+ 1 weekdays' )); 
		$item['book_enddate_target_step_6'] =  date ( 'Y-m-d', strtotime ( $item['book_startdate_target_step_6']. '+ '.($step[3]-1).' weekdays' ));
		 

		$item['book_status'] = '1';
		if($item['book_startdate_realization_step_2']!="") $item['book_status'] = '2';
		if($item['book_startdate_realization_step_3']!="") $item['book_status'] = '3';
		if($item['book_startdate_realization_step_4']!="") $item['book_status'] = '4';
		if($item['book_startdate_realization_step_5']!="") $item['book_status'] = '5';
		if($item['book_startdate_realization_step_6']!="") $item['book_status'] = '6';
		if($item['book_received_date']!="") $item['book_status'] = '7';
		
		if( $this->sm->add($item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function edit()
	{
		if(!$this->input->post('id')) return false; 
		$dt = $this->sm->getbyid($this->input->post('id'))->row();
		$dt->book_startdate_realization_step_1 = date('Y-m-d', strtotime($dt->book_startdate_realization_step_1));
		$dt->book_enddate_realization_step_1 = ($dt->book_enddate_realization_step_1!=""? date('Y-m-d', strtotime($dt->book_enddate_realization_step_1)):null);
		$dt->book_startdate_realization_step_2 = ($dt->book_startdate_realization_step_2!=""? date('Y-m-d', strtotime($dt->book_startdate_realization_step_2)):null);
		$dt->book_enddate_realization_step_2 = ($dt->book_enddate_realization_step_2!=""? date('Y-m-d', strtotime($dt->book_enddate_realization_step_2)):null); 
		$dt->book_startdate_realization_step_3 = ($dt->book_startdate_realization_step_3!=""? date('Y-m-d', strtotime($dt->book_startdate_realization_step_3)):null);
		$dt->book_enddate_realization_step_3 = ($dt->book_enddate_realization_step_3!=""? date('Y-m-d', strtotime($dt->book_enddate_realization_step_3)):null);
		$dt->book_startdate_realization_step_4 = ($dt->book_startdate_realization_step_4!=""? date('Y-m-d', strtotime($dt->book_startdate_realization_step_4)):null);
		$dt->book_enddate_realization_step_4 = ($dt->book_enddate_realization_step_4!=""? date('Y-m-d', strtotime($dt->book_enddate_realization_step_4)):null);
		$dt->book_startdate_realization_step_5 = ($dt->book_startdate_realization_step_5!=""? date('Y-m-d', strtotime($dt->book_startdate_realization_step_5)):null);
		$dt->book_enddate_realization_step_5 = ($dt->book_enddate_realization_step_5!=""? date('Y-m-d', strtotime($dt->book_enddate_realization_step_5)):null);
		$dt->book_startdate_realization_step_6 = ($dt->book_startdate_realization_step_6!=""? date('Y-m-d', strtotime($dt->book_startdate_realization_step_6)):null);
		$dt->book_enddate_realization_step_6 = ($dt->book_enddate_realization_step_6!=""? date('Y-m-d', strtotime($dt->book_enddate_realization_step_6)):null);

		$dt->book_startdate_target_step_1 = date('Y-m-d', strtotime($dt->book_startdate_target_step_1));
		$dt->book_enddate_target_step_1 = ($dt->book_enddate_target_step_1!=""? date('Y-m-d', strtotime($dt->book_enddate_target_step_1)):null);
		$dt->book_startdate_target_step_2 = ($dt->book_startdate_target_step_2!=""? date('Y-m-d', strtotime($dt->book_startdate_target_step_2)):null);
		$dt->book_enddate_target_step_2 = ($dt->book_enddate_target_step_2!=""? date('Y-m-d', strtotime($dt->book_enddate_target_step_2)):null); 
		$dt->book_startdate_target_step_3 = ($dt->book_startdate_target_step_3!=""? date('Y-m-d', strtotime($dt->book_startdate_target_step_3)):null);
		$dt->book_enddate_target_step_3 = ($dt->book_enddate_target_step_3!=""? date('Y-m-d', strtotime($dt->book_enddate_target_step_3)):null);
		$dt->book_startdate_target_step_4 = ($dt->book_startdate_target_step_4!=""? date('Y-m-d', strtotime($dt->book_startdate_target_step_4)):null);
		$dt->book_enddate_target_step_4 = ($dt->book_enddate_target_step_4!=""? date('Y-m-d', strtotime($dt->book_enddate_target_step_4)):null);
		$dt->book_startdate_target_step_5 = ($dt->book_startdate_target_step_5!=""? date('Y-m-d', strtotime($dt->book_startdate_target_step_5)):null);
		$dt->book_enddate_target_step_5 = ($dt->book_enddate_target_step_5!=""? date('Y-m-d', strtotime($dt->book_enddate_target_step_5)):null);
		$dt->book_startdate_target_step_6 = ($dt->book_startdate_target_step_6!=""? date('Y-m-d', strtotime($dt->book_startdate_target_step_6)):null);
		$dt->book_enddate_target_step_6 = ($dt->book_enddate_target_step_6!=""? date('Y-m-d', strtotime($dt->book_enddate_target_step_6)):null);
		
		$dt->book_received_date = ($dt->book_received_date!=""? date('Y-m-d', strtotime($dt->book_received_date)):null); 
		echo json_encode($dt);
	}
	
	 
	
	public function update()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$id   = $this->input->post('id');  


		$item = $this->input->post('inp');
		
		$item['book_startdate_realization_step_1'] = date('Y-m-d', strtotime($item['book_startdate_realization_step_1']));
		$item['book_enddate_realization_step_1'] = ($item['book_enddate_realization_step_1']!=""? date('Y-m-d', strtotime($item['book_enddate_realization_step_1'])):null);
		$item['book_startdate_realization_step_2'] = ($item['book_startdate_realization_step_2']!=""? date('Y-m-d', strtotime($item['book_startdate_realization_step_2'])):null);
		$item['book_enddate_realization_step_2'] = ($item['book_enddate_realization_step_2']!=""? date('Y-m-d', strtotime($item['book_enddate_realization_step_2'])):null); 
		$item['book_startdate_realization_step_3'] = ($item['book_startdate_realization_step_3']!=""? date('Y-m-d', strtotime($item['book_startdate_realization_step_3'])):null);
		$item['book_enddate_realization_step_3'] = ($item['book_enddate_realization_step_3']!=""? date('Y-m-d', strtotime($item['book_enddate_realization_step_3'])):null);
		$item['book_startdate_realization_step_4'] = ($item['book_startdate_realization_step_4']!=""? date('Y-m-d', strtotime($item['book_startdate_realization_step_4'])):null);
		$item['book_enddate_realization_step_4'] = ($item['book_enddate_realization_step_4']!=""? date('Y-m-d', strtotime($item['book_enddate_realization_step_4'])):null);
		$item['book_startdate_realization_step_5'] = ($item['book_startdate_realization_step_5']!=""? date('Y-m-d', strtotime($item['book_startdate_realization_step_5'])):null);
		$item['book_enddate_realization_step_5'] = ($item['book_enddate_realization_step_5']!=""? date('Y-m-d', strtotime($item['book_enddate_realization_step_5'])):null);
		$item['book_startdate_realization_step_6'] = ($item['book_startdate_realization_step_6']!=""? date('Y-m-d', strtotime($item['book_startdate_realization_step_6'])):null);
		$item['book_enddate_realization_step_6'] = ($item['book_enddate_realization_step_6']!=""? date('Y-m-d', strtotime($item['book_enddate_realization_step_6'])):null);

		$item['book_startdate_target_step_1'] = date('Y-m-d', strtotime($item['book_startdate_target_step_1']));
		$item['book_enddate_target_step_1'] = ($item['book_enddate_target_step_1']!=""? date('Y-m-d', strtotime($item['book_enddate_target_step_1'])):null);
		$item['book_startdate_target_step_2'] = ($item['book_startdate_target_step_2']!=""? date('Y-m-d', strtotime($item['book_startdate_target_step_2'])):null);
		$item['book_enddate_target_step_2'] = ($item['book_enddate_target_step_2']!=""? date('Y-m-d', strtotime($item['book_enddate_target_step_2'])):null); 
		$item['book_startdate_target_step_3'] = ($item['book_startdate_target_step_3']!=""? date('Y-m-d', strtotime($item['book_startdate_target_step_3'])):null);
		$item['book_enddate_target_step_3'] = ($item['book_enddate_target_step_3']!=""? date('Y-m-d', strtotime($item['book_enddate_target_step_3'])):null);
		$item['book_startdate_target_step_4'] = ($item['book_startdate_target_step_4']!=""? date('Y-m-d', strtotime($item['book_startdate_target_step_4'])):null);
		$item['book_enddate_target_step_4'] = ($item['book_enddate_target_step_4']!=""? date('Y-m-d', strtotime($item['book_enddate_target_step_4'])):null);
		$item['book_startdate_target_step_5'] = ($item['book_startdate_target_step_5']!=""? date('Y-m-d', strtotime($item['book_startdate_target_step_5'])):null);
		$item['book_enddate_target_step_5'] = ($item['book_enddate_target_step_5']!=""? date('Y-m-d', strtotime($item['book_enddate_target_step_5'])):null);
		$item['book_startdate_target_step_6'] = ($item['book_startdate_target_step_6']!=""? date('Y-m-d', strtotime($item['book_startdate_target_step_6'])):null);
		$item['book_enddate_target_step_6'] = ($item['book_enddate_target_step_6']!=""? date('Y-m-d', strtotime($item['book_enddate_target_step_6'])):null);

		$item['book_received_date'] = ($item['book_received_date']!=""? date('Y-m-d', strtotime($item['book_received_date'])):null);


		$step = $this->step();
		$item['book_status'] = '1';
		if($item['book_startdate_realization_step_2']!="") $item['book_status'] = '2';
		if($item['book_startdate_realization_step_3']!="") $item['book_status'] = '3';
		if($item['book_startdate_realization_step_4']!="") $item['book_status'] = '4';
		if($item['book_startdate_realization_step_5']!="") $item['book_status'] = '5';
		if($item['book_startdate_realization_step_6']!="") $item['book_status'] = '6';
		if($item['book_received_date']!="") $item['book_status'] = '7';
		
		
		if( $this->sm->edit($id, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	} 

	public function save_logistic()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item 	= $this->input->post('inp');
		$id   	= $this->input->post('id');
		$type   = $this->input->post('type');

		$temp = explode(",",$id);
		
		$arr = array();
		foreach($temp as $row){
			$arr[] = $row;
		}
		// $this->db->where_in('username', $names);

		$item['book_date_logistic_submission'] = date('Y-m-d', strtotime($item['book_date_logistic_submission']));
		$item['book_date_logistic_process'] 	 = $item['book_date_logistic_submission'];
		if($type=='input') $item['book_status'] = 'logistik';
 
		
		if( $this->sm->edit_logistic($arr, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}

	
	
	public function save_accept()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		

		$item = $this->input->post('inp');
		$id   = $this->input->post('id');  
		
		$dt = $this->sm->get_email_confirmed_and_available($id)->row(); 
		if($dt->book_status=='logistik') $item['book_status'] = 'penerimaan';

		$item['book_date_acceptance'] = date('Y-m-d', strtotime($item['book_date_acceptance'])); 
		
		$item['book_total_price'] = 	$item['book_copy']*$item['book_procurement_price'];
 
		
		if( $this->sm->edit($id, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	} 
	
	public function save_email_confirmed()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		

		$item = $this->input->post('inp');
		$id   = $this->input->post('id');  
		
		$dt = $this->sm->get_email_confirmed_and_available($id)->row(); 
		if($dt->book_status=='penerimaan') $item['book_status'] = 'q_email';

		$item['book_date_email_confirmed'] = date('Y-m-d', strtotime($item['book_date_email_confirmed']));
		
		if( $this->sm->edit($id, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function save_available()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		

		$item = $this->input->post('inp');
		$id   = $this->input->post('id');  
		
		$dt = $this->sm->get_email_confirmed_and_available($id)->row(); 
		if($dt->book_status=='q_email') $item['book_status'] = 'r_ketersediaan';

		$item['book_date_available'] = date('Y-m-d', strtotime($item['book_date_available']));
		
		if( $this->sm->edit($id, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function delete()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id = $this->input->post('id');
		
		if( $this->sm->delete($id) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menghapus Data'));
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/

	public function aktivasi()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		 
		$id   							= $this->input->post('id');
		$pass   						= $this->input->post('pass');
		$item['par_active'] = '1';
		$where 							= "par_participantnumber='$id' and par_password_plain='$pass'";
		if( $this->sm->aktivasi($where, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
} 

?>