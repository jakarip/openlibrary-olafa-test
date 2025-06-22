<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Subscribe extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Member_Model', 'dm', TRUE);
		$this->load->model('Subscribe_Admin_Model', 'sm', TRUE);
		$this->load->model('Ms_Course_Model', '', TRUE);
		$this->load->model('Ms_Prodi_Model', '', TRUE);
		$this->load->library('PHPExcel');
		$this->load->model('Common_Model', 'cm', TRUE);
		$this->load->helper('form');
		ini_set('memory_limit', '-1');
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/subscribe/index';
		$data['title']	= 'Data Berlangganan';		
		$data['icon']	= 'icon-people';
		
		$data['jenis_anggota'] 	= $this->cm->form_jenis_anggota(); 
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;

		
		$subs 	 			 	= $this->input->post('subs');   
		$type 	  			= $this->input->post('type');   
		$dates_option 	= $this->input->post('dates_option'); 
		$dates 					= $this->input->post('dates'); 

		$option = "";
		 if($dates_option!='all'){
			 $temp = explode(' - ',$dates);

			 $date1 = y_convert_date($temp[0], 'Y-m-d');
			 $date2 = y_convert_date($temp[1], 'Y-m-d');
			 $option = "subscribe_payment_date between '$date1' and '$date2'";
		 }
	
		$columns = array( 
			array( 'db' => 'subscribe_id', 'dt' => 0 ),
			array( 'db' => 'subscribe_status', 'dt' =>1 ),
			array( 'db' => 'subscribe_payment_date', 'dt' => 2 ),
			array( 'db' => 'subscribe_payment_code', 'dt' => 3 ),
			array( 'db' => 'master_data_type', 'dt' => 4 ),
			array( 'db' => 'master_data_number', 'dt' => 5 ),
			array( 'db' => 'master_data_fullname', 'dt' =>6 ),
			array( 'db' => 'master_data_email', 'dt' => 7 ),
			array( 'db' => 'subscribe_transaction', 'dt' => 8 ),
			array( 'db' => 'subscribe_month', 'dt' => 9 ),
			array( 'db' => 'subscribe_start_date', 'dt' => 10 )
	);
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query(); 

		if ($subs!=""){ 
			if(empty($param['where'])) 	$param['where'] = "WHERE (subscribe_status='".$subs."')";
			else $param['where'] .= "AND (subscribe_status='".$subs."')"; 
		}   

		if ($type!=""){ 
			if(empty($param['where'])) 	$param['where'] = "WHERE (master_data_type='".$type."')";
			else $param['where'] .= "AND (master_data_type='".$type."')"; 
		}
		else {
			if(empty($param['where'])) 	$param['where'] = "WHERE (master_data_type in ('umum','alumni','ptasuh','lemdikti'))";
			else $param['where'] .= "AND (master_data_type in ('umum','alumni','ptasuh','lemdikti'))"; 
		}

		
		if ($option!=""){ 
			if(empty($param['where'])) 	$param['where'] = "WHERE (".$option.")";
			else $param['where'] .= "AND (".$option.")";
		}

		$result = $this->sm->dtquery($param)->result();
		$filter = $this->sm->dtfiltered();
		$total	= $this->sm->dtcount();
		$output = $this->datatables->output($total, $filter);

		$jenis_anggota 	= $this->cm->form_jenis_anggota();

		foreach($result as $row)
		{
			$btn = "";
			if($row->subscribe_status == '1')
			{
					$label = '<label class="label label-success"><strong>Aktif</strong></label>';
			}
			else if($row->subscribe_status == '0')
			{
					$label = '<label class="label label-danger"><strong>Belum Validasi Pembayaran</strong></label>';
			 
			}
			else if($row->subscribe_status == '2')
			{
					$label = '<label class="label label-primary"><strong>Menunggu Verifikasi Admin</strong></label>';
					$btn = 
					'<a href="javascript:reject_form('.$row->subscribe_id.','.$row->subscribe_id_member.')" title="Reject Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-cross3"></i></a>
					<a href="javascript:approval('.$row->subscribe_id.',\''.$row->subscribe_transaction.'\',\''.$row->subscribe_id_member.'\',\''.$row->master_data_type.'\')" title="Approve Data" class="btn btn-xs btn-icon btn-success"><i class="icon-checkmark2"></i></a>';
			}
			else if($row->subscribe_status == '3')
			{
					$label = '<label class="label label-default"><strong>Non Aktif</strong></label>';
			}  

			if($row->subscribe_status == '0'){
					$payment ='<label class="label label-success" style="font-size:16px"><strong>'. y_num_idr($row->subscribe_payment_code).'</strong></label>';
			}
			else 
			$payment ='<label class="label label-default" style="font-size:16px"><strong>'. y_num_idr($row->subscribe_payment_code).'</strong></label>';

			$rows = array (
				$btn,
				$label,
				($row->subscribe_payment_date!=""?y_date_text($row->subscribe_payment_date):''),
				$payment,
				$jenis_anggota[$row->master_data_type].'<br>'.$row->master_data_institution, 
					$row->master_data_number,
					$row->master_data_fullname,
					$row->master_data_email,
					$row->subscribe_transaction,
					$row->subscribe_month.' Bulan <br><span class="text-muted">('.y_num_idr($row->subscribe_payment).')</span>',
					($row->subscribe_start_date!=""?convert_format_dates($row->subscribe_start_date).' - '.convert_format_dates($row->subscribe_end_date):'') 
			);

			$output['data'][] = $rows;
		}

		echo json_encode( $output );
	}

	
	
	public function reject_form()
	{
		if(!$this->input->post('id')) return false; 
		echo json_encode($this->dm->getbyid($this->input->post('id'))->row());
	}

	
	public function reject()
	{
			if(!$this->input->is_ajax_request()) return false;
			if(!$this->input->post('id')) return false;
  
			$id 					= $this->input->post('id');
			$sub_id 			= $this->input->post('sub_id');
			$alasan = $this->input->post('alasan'); 

			$member = $this->dm->getMember($id)->row(); 
			$sub = $this->sm->getbyid($sub_id)->row();


			$data['alasan'] = $alasan;
			$content 	= $this->load->view('email_template_validasi_reject', $data, true);
			$subject 	= "Validasi Pembayaran Berlangganan Telkom University Open Library"; 
			$state = SendEmail($member->master_data_email,$subject,$content,'Telkom University Open Library',ucwords(strtolower($member->master_data_fullname)));

			$this->sm->edit($sub_id,array('subscribe_status'=>'0','subscribe_payment_date'=> null)); 
			echo json_encode(array('status' => 'ok;', 'text' => '')); 
	}

	

	public function approve()
	{
			if(!$this->input->is_ajax_request()) return false;
			if(!$this->input->post('id')) return false;
 
			$id 					= $this->input->post('id');
			$sub_id 			= $this->input->post('sub_id');
			$usergroup 		= $this->input->post('usergroup');

			$mmbrtype = $this->cm->form_member_type();
 
			$data2['member_type_id'] = $mmbrtype[$usergroup];    
			
			$sub = $this->sm->getbyid($sub_id)->row();  
			$date = new DateTime('now');
			$date->modify('+'.$sub->subscribe_month.' month'); 

			$data['subscribe_status']     = '1';    
			$data['subscribe_start_date'] = date('Y-m-d'); 
			$data['subscribe_end_date']   = $date->format('Y-m-d');

			$member = $this->dm->getMember($id)->row(); 

			if($this->sm->edit($sub_id,$data) and $this->dm->edit($id,$data2)){
	 
					$data['subscribe_transaction']     = $sub->subscribe_transaction;    
					$data['subscribe_payment_date']     = $sub->subscribe_payment_date;   
					$data['subscribe_month']     = $sub->subscribe_month;   
					$data['subscribe_payment_code']     = $sub->subscribe_payment_code;   
					$data['password']   = ucwords(strtolower($member->PASSWORD_X));
					$data['email']   = $member->master_data_email;
					$content 	= $this->load->view('email_template_validasi', $data, true);
					$subject 	= "Validasi Pembayaran Berlangganan Telkom University Open Library"; 
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