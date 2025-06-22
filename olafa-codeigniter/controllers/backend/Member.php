<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Member_Model', 'dm', TRUE);
		$this->load->model('Ms_Course_Model', '', TRUE);
		$this->load->model('Ms_Prodi_Model', '', TRUE);
		$this->load->library('PHPExcel');
		$this->load->model('Common_Model', 'cm', TRUE);
		ini_set('memory_limit', '-1');
		$this->load->helper('form');
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/member/index';
		$data['title']	= 'Data Member';		
		$data['icon']	= 'icon-people';
		
		$data['jenis_anggota'] 	= $this->cm->form_jenis_anggota();
		// $data['course'] = $this->Ms_Course_Model->getall()->result();
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;

		
		$subs 	  			= $this->input->post('subs');  
		$status 				= $this->input->post('status'); 
		$type 					= $this->input->post('type'); 
		$dates_option 	= $this->input->post('dates_option'); 
		$dates 					= $this->input->post('dates'); 

		$option = "";
		 if($dates_option!='all'){
			 $temp = explode(' - ',$dates);

			 $date1 = y_convert_date($temp[0], 'Y-m-d');
			 $date2 = y_convert_date($temp[1], 'Y-m-d');
			 $option = "created_at between '$date1' and '$date2'";
		 }
		
		$columns = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'status', 'dt' => 1 ),
			array( 'db' => 'subscribe_status', 'dt' => 2 ),
			array( 'db' => 'master_data_ijasah', 'dt' => 3 ),
			array( 'db' => 'master_data_ktp', 'dt' => 4 ),
			array( 'db' => 'master_data_idcard', 'dt' =>5 ),
			array( 'db' => 'created_at', 'dt' => 6),
			array( 'db' => 'master_data_type', 'dt' => 7 ),
			array( 'db' => 'master_data_number', 'dt' => 8 ),
			array( 'db' => 'master_data_fullname', 'dt' => 9 ),
			array( 'db' => 'master_data_email', 'dt' => 10 ),
			array( 'db' => 'master_data_mobile_phone', 'dt' => 11 )  
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query(); 

		if ($status!=""){ 
			if(empty($param['where'])) 	$param['where'] = "WHERE (status='".$status."')";
			else $param['where'] .= "AND (status='".$status."')"; 
		}

		if ($option!=""){ 
			if(empty($param['where'])) 	$param['where'] = "WHERE (".$option.")";
			else $param['where'] .= "AND (".$option.")";
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

		if ($type!=""){ 
			if(empty($param['where'])) 	$param['where'] = "WHERE (master_data_type='".$type."')  AND (master_data_number is not null and master_data_number!='')";
			else $param['where'] .= "AND (master_data_type='".$type."') AND (master_data_number is not null and master_data_number!='')"; 
		}
		else {
			if(empty($param['where'])) 	$param['where'] = "WHERE (master_data_type in ('umum','alumni','ptasuh','lemdikti')) AND (master_data_number is not null and master_data_number!='')";
			else $param['where'] .= "AND (master_data_type in ('umum','alumni','ptasuh','lemdikti')) AND (master_data_number is not null and master_data_number!='')"; 
		}
   

		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		$jenis_anggota 	= $this->cm->form_jenis_anggota();
		
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
				'<a href="javascript:reject_form('.$row->id.')" title="Reject Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-cross3"></i></a>
				<a href="javascript:approval('.$row->id.',\''.addslashes($row->master_data_fullname).'\')" title="Approve Data" class="btn btn-xs btn-icon btn-success"><i class="icon-checkmark2"></i></a>';
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
				$btn,
				$par_active, 
				$subs,
				($row->master_data_ijasah!=''?'<a href="'.$row->master_data_ijasah.'" target="_blank">link</a>':'-'),
				'<a href="'.$row->master_data_ktp.'" target="_blank">link</a>',
				($row->master_data_idcard!=''?'<a href="'.$row->master_data_idcard.'" target="_blank">link</a>':'-'),
				// '<input type="checkbox" class="chk-location" value="'.$row->par_id.'">',  
				y_convert_date($row->created_at), 
				$jenis_anggota[$row->master_data_type].'<br>'.$row->master_data_institution, 
				$row->master_data_number, 
				$row->master_data_fullname, 
				$row->master_data_email,
				$row->master_data_mobile_phone
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
					$data['password']   = $member->PASSWORD_X;
					$data['email']   = $member->master_data_email;
					$content 	= $this->load->view('email_template_approve', $data, true);
					$subject 	= "Account Verification Telkom University Open Library"; 
					$state = SendEmail($member->master_data_email,$subject,$content,'Telkom University Open Library',ucwords(strtolower($member->master_data_fullname)));
					echo json_encode(array('status' => 'ok;', 'text' => ''));
			} 
			else
					echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data')); 
	} 

	
	public function reject()
	{
			if(!$this->input->is_ajax_request()) return false;
			if(!$this->input->post('id')) return false;
 
			$id = $this->input->post('id');
			$alasan = $this->input->post('alasan'); 
			$send = $this->input->post('send'); 
			$member = $this->dm->getMember($id)->row();  

			if($send){ 
				$data['alasan'] = $alasan;
				$content 	= $this->load->view('email_template_reject', $data, true);
				$subject 	= "Verifikasi Akun Telkom University Open Library"; 
				$state = SendEmail($member->master_data_email,$subject,$content,'Telkom University Open Library',ucwords(strtolower($member->master_data_fullname)));
			}  
 
			$this->dm->delete($id);
			$this->dm->delete_t_mst_user_login($member->master_data_user);
			$this->dm->delete_t_mst_pegawai($member->master_data_user);
			$this->dm->delete_vfs_users($member->master_data_user);
			
			echo json_encode(array('status' => 'ok;', 'text' => ''));  
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
	
	public function reject_form()
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