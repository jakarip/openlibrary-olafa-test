<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Referral extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		 
		$this->load->model('Referral_Payment_Model', 'dm', TRUE);
		$this->load->model('Referral_Model', 'rm', TRUE);
		$this->load->model('Participant_Model', 'pm', TRUE);
		$this->load->model('Common_Model', 'cm', TRUE);
		
		if (!$this->session->userdata('login') and !$this->session->userdata('referral_login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
	    if($this->session->userdata('usergroup') == 'referral')
	        redirect('itpanel/referral/dashboard');

		$data['view'] 	= 'backend/referral/index';
		$data['title']	= 'Referral';		
		$data['icon']	= 'icon-collaboration';
		
		$this->load->view('backend/tpl', $data);
	} 

	public function detail($id="")
	{  
		if($this->session->userdata('usergroup') == 'referral')
		redirect('itpanel/referral/dashboard'); 

		if(!$id)
		redirect('itpanel/referral'); 

			
		$data['id']			= $id; 
		$data['view'] 	= 'backend/referral/detail';
		$data['title']	= 'List Data Pendaftar';		
		$data['icon']		= 'icon-users';
		
		$this->load->view('backend/tpl', $data);
	}

	public function json_detail()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array( 
			array( 'db' => 'pin_transaction_number', 'dt' => 0 ),
			array( 'db' => 'par_participantnumber', 'dt' => 1 ),
			array( 'db' => 'par_fullname', 'dt' => 2 ),
			array( 'db' => 'school_name', 'dt' => 3 ),
			array( 'db' => 'par_phone', 'dt' => 4 ),
			array( 'db' => 'status', 'dt' => 5 ), 
			array( 'db' => 'total', 'dt' => 6 ), 
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		

		$id = $this->input->post('id');
	
		if($param['where']==""){
			$param['where'] = " where sreg_referral='$id'";
		}
		else { 
			$param['where'].= " and sreg_referral='$id'";
		} 
	
		$result = $this->rm->dtquery_detail($param)->result();
		$filter = $this->rm->dtfiltered();
		$total	= $this->rm->dtcount_detail();
		$output = $this->datatables->output($total, $filter);
		
		$promo		= $this->cm->form_promotion_setting();
		foreach($result as $row)
		{ 

			$rows = array ( 
				$row->pin_transaction_number,
				$row->par_participantnumber,
				$row->par_fullname,
				$row->school_name,
				$row->par_phone, 
				$row->status, 
				'Rp. '.number_format($row->total, 0, ',', '.')
			);
			
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	}
	
	public function dashboard()
	{  
		if($this->session->userdata('usergroup') == 'admin')
		redirect('itpanel');

		$data['view'] 	= 'backend/referral/dashboard';
		$data['title']	= 'Dashboard';		
		$data['icon']		= 'icon-meter-fast';
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function payment()
	{  
		if($this->session->userdata('usergroup') == 'referral')
		redirect('itpanel/referral/dashboard'); 

		$data['view'] 	= 'backend/referral/payment_referral';
		$data['title']	= 'Bayar Komisi';		
		$data['icon']		= 'icon-cash4';
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function payment_register($id="")
	{  
		if($this->session->userdata('usergroup') == 'referral')
		redirect('itpanel/referral/dashboard'); 

		if(!$id)
		redirect('itpanel/referral/payment'); 

			
		$data['id']			= $id; 
		$data['view'] 	= 'backend/referral/payment_register';
		$data['title']	= 'List Data Pendaftar';		
		$data['icon']		= 'icon-users';
		
		$this->load->view('backend/tpl', $data);
	}

	public function form()
	{
			if($this->session->userdata('usergroup') != 'referral')
					redirect('itpanel/referral');

			$data['view'] 	= 'backend/referral/form';
			$data['title']	= 'Data Akun Referral';
			$data['icon']	= 'icon-user';
			$data['data'] = $this->rm->getbyid($this->session->userdata('info_login')->ref_id)->row();

			$this->load->view('backend/tpl', $data);
	}

	public function json_payment_referral()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'ref_fullname', 'dt' => 0 ),
			array( 'db' => 'ref_username', 'dt' => 1 ),
			array( 'db' => 'ref_email', 'dt' => 2 ),
			array( 'db' => 'ref_phone', 'dt' => 3 ),
			array( 'db' => 'register', 'dt' => 4 ),
			array( 'db' => 'total', 'dt' => 5 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		 
		
		$result = $this->rm->dtquery($param)->result();
		$filter = $this->rm->dtfiltered();
		$total	= $this->rm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{
			if($row->ref_active == '1'){
				$par_active = '<span class="label label-success">Aktivasi</span>'; 
			}	
			else {
				$par_active = '<span class="label label-warning">Belum Aktivasi</span>';  
			} 

			$rows = array (
				$row->ref_fullname,
				$row->ref_username,
				$row->ref_email,
				$row->ref_phone,
				$row->register, 
				'Rp. '.number_format($row->total, 0, ',', '.'), 
				'<a href="itpanel/referral/payment_register/'.$row->ref_id.'" target="_blank" title="Detail Pendaftar" class="btn btn-xs btn-icon btn-success">Bayar</a>'
			);
			
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	}
	
	public function json_payment_register()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'pin_transaction_number', 'dt' => 0 ),
			array( 'db' => 'par_participantnumber', 'dt' => 1 ),
			array( 'db' => 'par_fullname', 'dt' => 2 ),
			array( 'db' => 'school_name', 'dt' => 3 ),
			array( 'db' => 'par_phone', 'dt' => 4 ),
			array( 'db' => 'rp_set_status', 'dt' => 5 ),
			array( 'db' => 'rp_set_cost', 'dt' => 6 ),
			array( 'db' => 'rp_status_payment', 'dt' => 7 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		

		$id = $this->input->post('id');
	
		if($param['where']==""){
			$param['where'] = " where rp_id_ref='$id'";
		}
		else { 
			$param['where'].= " and rp_id_ref='$id'";
		} 
	
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		$promo		= $this->cm->form_promotion_setting();
		foreach($result as $row)
		{
			if($row->rp_status_payment == 'sudah'){
				$status = '<span class="label label-success">Sudah Ditransfer</span>'; 
				$btn = '<a href="javascript:active(\'belum\', '.$row->rp_id.', \''.$row->par_fullname.' - SUDAH DITRANSFER\')" title="Belum Ditransfer" class="btn btn-xs btn-icon btn-danger"><i class="icon-folder-remove"></i></a>';
			}	
			else {
				$status = '<span class="label label-warning">Belum Ditransfer</span>';  
				$btn = '<a href="javascript:active(\'sudah\', '.$row->rp_id.', \''.$row->par_fullname.' - BELUM DITRANSFER\')" title="Sudah Ditransfer" class="btn btn-xs btn-icon btn-success"><i class="icon-folder-check"></i></a>';
			}   

			$rows = array (
				$row->pin_transaction_number,
				$row->par_participantnumber,
				$row->par_fullname,
				$row->school_name,
				$row->par_phone, 
				$promo[$row->rp_set_status], 
				'Rp. '.number_format($row->rp_set_cost, 0, ',', '.'), 
				$status,  
				$btn
			);
			
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	}
	
	public function json_ref()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'pin_transaction_number', 'dt' => 0 ),
			array( 'db' => 'par_participantnumber', 'dt' => 1 ),
			array( 'db' => 'par_fullname', 'dt' => 2 ),
			array( 'db' => 'school_name', 'dt' => 3 ),
			array( 'db' => 'par_phone', 'dt' => 4 ),
			array( 'db' => 'rp_set_status', 'dt' => 5 ),
			array( 'db' => 'rp_set_cost', 'dt' => 6 ),
			array( 'db' => 'rp_status_payment', 'dt' => 7 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
	
		if($param['where']==""){
			$param['where'] = " where ref_username='".$this->session->userdata('info_login')->ref_username."'";
		}
		else { 
			$param['where'].= " and ref_username='".$this->session->userdata('info_login')->ref_username."'";
		} 
	
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		$promo		= $this->cm->form_promotion_setting();
		foreach($result as $row)
		{
			if($row->rp_status_payment == 'sudah'){
				$status = '<span class="label label-success">Sudah Ditransfer</span>'; 
			}	
			else {
				$status = '<span class="label label-warning">Belum Ditransfer</span>';  
			}  

			$rows = array (
				$row->pin_transaction_number,
				$row->par_participantnumber,
				$row->par_fullname,
				$row->school_name,
				$row->par_phone, 
				$promo[$row->rp_set_status], 
				'Rp. '.number_format($row->rp_set_cost, 0, ',', '.'), 
				$status
			);
			
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	}
	
	public function json_admin()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'ref_fullname', 'dt' => 0 ),
			array( 'db' => 'ref_username', 'dt' => 1 ),
			array( 'db' => 'ref_email', 'dt' => 2 ),
			array( 'db' => 'ref_phone', 'dt' => 3 ),
			array( 'db' => 'register', 'dt' => 4 ),
			array( 'db' => 'total', 'dt' => 5 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
 

		if($this->session->userdata('usergroup')=='referral'){
			if($param['where']==""){
				$param['where'] = " where ref_username='".$this->session->userdata('info_login')->ref_username."'";
			}
			else { 
				$param['where'].= " and ref_username='".$this->session->userdata('info_login')->ref_username."'";
			}
		}
 
		$result = $this->rm->dtquery($param)->result();
		$filter = $this->rm->dtfiltered();
		$total	= $this->rm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{
		// 	if($row->ref_active == '1'){
		// 		$par_active = '<span class="label label-success">Aktivasi</span>'; 
		// 	}	
		// 	else {
		// 		$par_active = '<span class="label label-warning">Belum Aktivasi</span>';  
		// 	}

			// if($this->session->userdata('usergroup')=='admin'){
			// 	$delete = '<a href="javascript:del('.$row->ref_id.', \''.$row->ref_fullname.'\')" title="Delete Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-trash"></i></a>';
			// }
			// else {
			// 	$delete = '';
			// }

			$rows = array (
				$row->ref_fullname,
				$row->ref_username,
				$row->ref_email,
				$row->ref_phone,
				$row->register, 
				'Rp. '.number_format($row->total, 0, ',', '.'), 
				'<a href="itpanel/referral/detail/'.$row->ref_id.'" target="_blank" title="Detail Pendaftar" class="btn btn-xs btn-icon btn-success"><i class="icon-users4"></i></a>'
				// $par_active,
				// '<a href="javascript:edit('.$row->ref_id.')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a>'.$delete.'
				// <a href="itpanel/referral/participant/'.$row->ref_id.'" target="_blank" title="Participant Data" class="btn btn-xs btn-icon btn-success"><i class="icon-profile"></i></a>'
			);
			
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	}
	
	public function insert()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$password = $this->input->post('password');
		$item['ref_password'] = '$PMBREF$'.substr(sha1(md5(md5($password))), 0, 50);
		
		if( $this->rm->add($item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function edit()
	{
		if(!$this->input->post('id')) return false;
		
		echo json_encode($this->rm->getbyid($this->input->post('id'))->row());
	}
	
	public function update()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$id   = $this->input->post('id');
		
		$password = $this->input->post('password');
		if(!empty($password))
			$item['ref_password'] = '$PMBREF$'.substr(sha1(md5(md5($password))), 0, 50);
		
		if( $this->rm->edit($id, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function delete()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id = $this->input->post('id');
		
		if( $this->rm->delete($id) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menghapus Data'));
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/

	

	public function participant($id)
	{
		$data['view'] 	= 'backend/referral/participant';
		$data['title']	= 'Data Participant';		
		$data['icon']		= 'icon-people'; 
		$data['id']			= $id;  
		$this->load->view('backend/tpl', $data);
	}
	public function json_participant()
	{
		if(!$this->input->is_ajax_request()) return false;
		$id = $this->input->post('id');
		
		$columns = array( 
			array( 'db' => 'par_fullname', 'dt' => 0 ),
			array( 'db' => 'par_participantnumber', 'dt' =>1 ),
			array( 'db' => 'par_active', 'dt' => 2 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();

		if($param['where']==""){
			$param['where'] = " where par_id_ref='$id'";
		}
		else { 
			$param['where'].= " and par_id_ref='$id'";
		}  

		$result = $this->pm->dtquery($param)->result();
		$filter = $this->pm->dtfiltered();
		$total	= $this->pm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{  

			if($row->par_active == '1'){
					$par_active = '<span class="label label-success">Aktivasi</span>'; 
				}	
			else {
				$par_active = '<span class="label label-warning">Belum Aktivasi</span>';  
			}

			$rows = array (  
				$row->par_participantnumber, 
				$row->par_fullname, 
				$par_active
			 
			); 
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	}

	public function active()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id = $this->input->post('id');
		$sts = $this->input->post('sts'); 

		if($sts=='belum') $temp = array('rp_status_payment' => $sts,'rp_date_payment' => null);
		else $temp = array('rp_status_payment' => $sts,'rp_date_payment' => date('Y-m-d'));

		if( $this->dm->edit($id, $temp) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data')); 
	}
}

?>