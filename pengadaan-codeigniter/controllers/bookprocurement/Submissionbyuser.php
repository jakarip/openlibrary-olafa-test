<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Submissionbyuser extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Member_Model', 'dm', TRUE); 
		$this->load->model('bookprocurement/Submissionbyuser_Model', 'sm', TRUE); 
		// $this->load->library('PHPExcel');
		$this->load->model('Common_Model', 'cm', TRUE);
		$this->load->helper('form');
		// ini_set('memory_limit', '-1'); 
		// error_reporting(E_ALL);
		// ini_set('display_errors', 1);

		
		 
		if (!$this->session->userdata('user_login_apps')) redirect('login');
		
		// y_is_login('bookprocurement');
	}
	
	public function index()
	{ 
		$data['view'] 	= 'bookprocurement/submissionbyuser/index';
		$data['title']	= 'Data Pengajuan Buku by User';		
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

		$array[''] 					= 'Semua Status';
		$array['Request'] 			= 'Request';
		$array['Approved'] 			= 'Approved';
		$array['Not Approved'] 		= 'Not Approved';  
		
		$data['status'] 	= $array; 
		
		$iuser = $this->session->userdata(); 
		//  print_r($iuser);
		 $this->load->view('frontend/tpl', $data);
	}
 
	
	

	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;

		$status = $this->input->post('status');
		$created_date = $this->input->post('created_date');
		$created_date_option = $this->input->post('created_date_option');

		$option = "";
		if($created_date_option!='all'){
			$temp = explode(' - ',$created_date);

			$date1 = y_convert_date($temp[0], 'Y-m-d');
			$date2 = y_convert_date($temp[1], 'Y-m-d');
			$option = "(bp_createdate between '$date1' and '$date2')";
		}
		 
		$columns = array( 
			array( 'db' => 'bp_status', 'dt' => 0 ),
			array( 'db' => 'bp_reason', 'dt' => 1 ),
			array( 'db' => 'bp_createdate', 'dt' => 2 ),
			array( 'db' => 'bp_approval_kaprodi_file', 'dt' => 3 ),
			array( 'db' => 'bp_rps_file', 'dt' => 4 ),
			array( 'db' => 'bp_createdate', 'dt' => 5 ),
			array( 'db' => 'master_data_number', 'dt' =>6 ),
			array( 'db' => 'master_data_fullname', 'dt' => 7 ),
			array( 'db' => 'bp_title', 'dt' => 8 ),
			array( 'db' => 'bp_author', 'dt' => 9 ),
			array( 'db' => 'bp_publisher', 'dt' => 10 ),
			array( 'db' => 'bp_publishedyear', 'dt' =>11 ),
			array( 'db' => 'bp_matakuliah', 'dt' => 12),
			array( 'db' => 'bp_reference', 'dt' => 13),
			 
	);
		$this->datatables->set_cols($columns);

		$param	= $this->datatables->query();  

		if ($status!=""){ 
			if(empty($param['where'])) 	$param['where'] = "AND (bp_status='".$status."')";
			else $param['where'] .= "AND (bp_status='".$status."')"; 
		} 

		if ($option!=""){ 
			if(empty($param['where'])) 	$param['where'] = "AND (".$option.")";
			else $param['where'] .= "AND (".$option.")";
		}

		$result = $this->sm->dtquery($param)->result();
		$filter = $this->sm->dtfiltered();
		$total	= $this->sm->dtcount();
		$output = $this->datatables->output($total, $filter);

		 
		$iuser = $this->session->userdata(); 
		foreach($result as $row)
		{
			$btn = "";
			$status = "";
			$reason = ""; 


 
			if ($row->bp_status=='Not Approved'){
				$btn = "";
				$status = '<div class="btn-group"><button type="button" class="btn btn-sm btn-info" title="'.$row->bp_status.'">'.$row->bp_status.'</button></div>';  
				$reason = $row->bp_reason;
			}
			else if ($row->bp_status=='Approved'){
				$btn = "";
				$status = '<div class="btn-group"><button type="button" class="btn btn-sm btn-primary" title="'.$row->bp_status.'">'.$row->bp_status.'</button></div>'; 
				$reason = "-";
			}
			else if ($row->bp_status=='Request'){
				$btn = '<div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="Approved" onclick="edit('."'".$row->bp_id."','Approved'".')">Approved</button></div><br><br><div class="btn-group"> <button type="button" class="btn btn-sm btn-danger" title="Not Approved"onclick="not_approved('."'".$row->bp_id."','Not Approved'".')">Not Approved</button></div>'; 
				$status = '<div class="btn-group"><button type="button" class="btn btn-sm btn-warning" title="'.$row->bp_status.'">'.$row->bp_status.'</button></div>';
				$reason = "-"; 
			} 
			
			if ($iuser['usergroup']!='superadmin'){  
				$btn = "";
			}
             
			$rows = array (
					$btn,
					$status,
					 $reason, 
					($row->bp_approval_kaprodi_file!=""?'<a href="'.base_url().'cdn/approval/'.$row->bp_approval_kaprodi_file.'" target="_blank" >file</a>':'') , 
					($row->bp_rps_file!=""?'<a href="'.base_url().'cdn/approval/'.$row->bp_rps_file.'" target="_blank" >file</a>':'') , 
					$row->tanggal, 
					$row->nama_fakultas, 
					$row->nama_prodi, 
					$row->master_data_number, 
					$row->master_data_fullname, 
					$row->bp_title, 
					$row->bp_author, 
					$row->bp_publisher, 
					$row->bp_publishedyear, 
					$row->bp_matakuliah, 
					$row->bp_reference
			); 

			$output['data'][] = $rows;
		}

		echo json_encode( $output );
	}  
	 
	public function save_upload()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;

		$iuser = $this->session->userdata();  

		$item 			= $this->input->post('inp');   
		
		
		$inp['bp_idmember'] = $iuser['user_id'];
		
		$inp['bp_prodi_id'] = $item['bp_prodi_id'];
		$inp['bp_upload_type'] = 'apps';
		$inp['bp_createdate'] = date('Y-m-d');
		$inp['bp_status'] = 'Request'; 

		if (isset($_FILES['approval']['name']) && $_FILES['approval']['error'] == UPLOAD_ERR_OK) {
			$uploaddir 		= 'cdn/approval/';
			$file 			= explode(".", $_FILES['approval']['name']); 
			$ext 			= end($file);
			$newFile 		= $iuser['user_id'].'_'.round(microtime(true)).'.'.$ext;
			$uploadfile 	= $uploaddir . basename($newFile);
			move_uploaded_file($_FILES['approval']['tmp_name'], $uploadfile); 
			$inp['bp_approval_kaprodi_file'] = $newFile;
		}

		if (isset($_FILES['rps']['name']) && $_FILES['rps']['error'] == UPLOAD_ERR_OK) {
			$uploaddir 		= 'cdn/rps/';
			$file 			= explode(".", $_FILES['rps']['name']); 
			$ext 			= end($file);
			$newFile 		= $iuser['user_id'].'_'.round(microtime(true)).'.'.$ext;
			$uploadfile 	= $uploaddir . basename($newFile);
			move_uploaded_file($_FILES['rps']['tmp_name'], $uploadfile);
			$inp['bp_rps_file'] = $newFile;
		} 
		
		
		foreach($item['bp_matakuliah'] as $key => $row){
			$inp['bp_matakuliah'] = $row;
			$inp['bp_title'] = $item['bp_title'][$key];
			$inp['bp_author'] = $item['bp_author'][$key];
			$inp['bp_publisher'] = $item['bp_publisher'][$key];
			$inp['bp_publishedyear'] = $item['bp_publishedyear'][$key];
			$inp['bp_reference'] = $item['bp_reference'][$key];
			$this->sm->add($inp);
		}

		echo json_encode(array("status" => 'ok;'));
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
	 
	public function update()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$id   = $this->input->post('id');  
		
        $data 	= $this->sm->getbyid($id)->row();
 
		$item2['book_id_prodi'] = $data->bp_prodi_id;
		$item2['book_member'] = $data->master_data_fullname;
		$item2['book_subject'] = $data->bp_matakuliah;
		$item2['book_title'] = $data->bp_title;
		$item2['book_author'] = $data->bp_author;
		$item2['book_publisher'] = $data->bp_publisher;
		$item2['book_published_year'] = $data->bp_publishedyear;
		$item2['book_date_prodi_submission'] = date('Y-m-d', strtotime($data->bp_createdate));
		$item2['book_status'] = 'pengajuan';
		$this->sm->addBookProcurement($item2);
		if( $this->sm->edit($id, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	} 

	
	
	function not_approved(){ 
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id'); 
        $item   = $_POST['inp'];     

		$option_reason = $this->input->post('option_reason');
		$bp_reason = $this->input->post('bp_reason');
		if($option_reason=='lain') $item['bp_reason'] = $bp_reason;
		else $item['bp_reason'] = $option_reason; 
		 
		if ($this->sm->edit($id, $item)) echo json_encode(array('status' => 'ok;', 'text' => ''));
    }  
 
} 

?>