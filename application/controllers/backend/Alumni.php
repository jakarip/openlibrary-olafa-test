<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Alumni extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Alumni_Model', 'dm', TRUE);
		$this->load->model('Ms_Course_Model', '', TRUE);
		$this->load->model('Ms_Prodi_Model', '', TRUE);
		$this->load->library('PHPExcel');
		$this->load->model('Common_Model', 'cm', TRUE);
		ini_set('memory_limit', '-1');
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/alumni/index';
		$data['title']	= 'Data Alumni';		
		$data['icon']	= 'icon-people';
		
		// $data['course'] = $this->Ms_Course_Model->getall()->result();
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;

		
		$subs 	  = $this->input->post('subs');  
		$status 	= $this->input->post('status'); 
		
		$columns = array(
			array( 'db' => 'master_data_number', 'dt' => 0 ),
			array( 'db' => 'master_data_fullname', 'dt' => 1 ),
			array( 'db' => 'master_data_email', 'dt' => 2 ),
			array( 'db' => 'master_data_mobile_phone', 'dt' => 3 ), 
			array( 'db' => 'status', 'dt' => 4), 
			array( 'db' => 'id', 'dt' => 5 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query(); 

		if ($status!=""){ 
			if(empty($param['where'])) 	$param['where'] = "WHERE (status='".$status."')";
			else $param['where'] .= "AND (status='".$status."')"; 
		}
		if ($subs!=""){ 
			if(empty($param['where'])) {
				if($subs=='Y') 
					$param['where'] = "WHERE (subscribe_status='1')";
				else 
					$param['where'] = "WHERE (subscribe_status is null)";
			} 
			else { 
				if($subs=='Y') 
					$param['where'] .= "AND (subscribe_status='1')";
				else 
					$param['where'] .= "AND (subscribe_status is null)";
			
			} 
		}
 

	 
			if(empty($param['where'])) 		$param['where'] = "WHERE (master_data_type='alumni')";
			else $param['where'] .= "AND (master_data_type='alumni')";  

		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{ 
			$btn = "";
			$par_active = "";
			if($row->status == '1'){
				$par_active = '<span class="label label-success">Sudah Aktivasi</span>'; 
			}	
			else if($row->status == '2'){
				$par_active = '<span class="label label-default">Belum Aktivasi</span>';  
				$btn = 
				'<a href="javascript:reject('.$row->id.')" title="Reject Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-cross3"></i></a>
				<a href="javascript:approval('.$row->id.',\''.$row->master_data_fullname.'\')" title="Approve Data" class="btn btn-xs btn-icon btn-success"><i class="icon-checkmark2"></i></a>';
			}
			else if($row->status == '3'){
				$par_active = '<span class="label label-danger">BlackList</span>';  
			}

			if($row->subscribe_status == '1'){
				$subs = '<span class="label label-success">Berlangganan Durasi '.$row->subscribe_month.' Bulan</span>'; 
				}	
			else {
				$subs = '<span class="label label-default">Belum Berlangganan</span>';  
			}
 
		

			$rows = array ( 
				// '<input type="checkbox" class="chk-location" value="'.$row->par_id.'">', 
				$row->master_data_number, 
				$row->master_data_fullname, 
				$row->master_data_email,
				$row->master_data_mobile_phone, 
				'<a href="'.$row->master_data_ktp.'" target="_blank">link</a>',
				'<a href="'.$row->master_data_ijasah.'" target="_blank">link</a>', 
				$par_active, 
				$subs,
				$btn
				// $login_as
			);
			
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	}

	

	public function approve()
	{
			if(!$this->input->is_ajax_request()) return false;
			if(!$this->input->post('id')) return false;
 
			$id = $this->input->post('id');
			$data['status'] = '1';    
			$data2['STATUS_USER'] = '1';    
			$member = $this->dm->getMember($id)->row(); 

			if($this->dm->edit($id,$data) and $this->dm->edit_t_mst_user_login($member->master_data_user,$data2)){
					// $this->send_email($pin_free);
					$data['password']   = ucwords(strtolower($member->PASSWORD_X));
					$data['email']   = $member->master_data_email;
					$content 	= $this->load->view('email_template_approve', $data, true);
					$subject 	= "Verifikasi Akun Telkom University Open Library"; 
					$state = SendEmail($member->master_data_email,$subject,$content,'Telkom University Open Library',ucwords(strtolower($member->master_data_fullname)),'https://openlibrary.telkomuniversity.ac.id/sites/cdn/syarat_keanggotaan_alumni.pdf');
					echo json_encode(array('status' => 'ok;', 'text' => ''));
			} 
			else
					echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data')); 
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

		foreach($item as $key => $row){
			$item[$key] = strtoupper($row);
		}
		
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

	public function aktivasi()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		 
		$id   							= $this->input->post('id');
		$pass   						= $this->input->post('pass');
		$item['par_active'] = '1';
		$where 							= "par_participantnumber='$id' and par_password_plain='$pass'";
		if( $this->dm->aktivasi($where, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
}

?>