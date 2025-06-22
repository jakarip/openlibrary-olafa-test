<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Generate_Pin extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Pin_Model', 'dm', TRUE);
		$this->load->model('Periode_Model', 'pm', TRUE);
		$this->load->model('Participant_Registration_Model', 'sreg', TRUE);
        $this->load->model('Discount_Model', 'dim', TRUE);
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/generate_pin/index';
		$data['title']	= 'Generate No. Transaksi';		
		$data['icon']	= 'icon-lock';
		
		$this->load->helper('form');
		$data['jns']		= $this->cm->form_pin();	
		$data['pil_prodi']	= $this->cm->form_pil_prodi();
		$data['periode']	= $this->pm->getActive()->result();
        $data['discount']	= $this->dim->getActive()->result();
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'pin_id', 'dt' => 0 ),
			array( 'db' => 'par_fullname', 'dt' => 1 ),
			array( 'db' => 'pin_status', 'dt' => 2 ),
			array( 'db' => 'periode_name', 'dt' => 3 ),
			array( 'db' => 'pin_price', 'dt' => 4 ),
			array( 'db' => 'pin_max_prodi', 'dt' => 5 ),
			array( 'db' => 'pin_create_date', 'dt' => 6 ),
			array( 'db' => 'pin_transaction_number', 'dt' => 7),
			array( 'db' => 'pin_desc', 'dt' => 8 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{
			$delete = "";
			if($row->pin_status == 'Y')
			{
				$label = '<span class="label label-danger">Used</span>';
				
				// if($row->sreg_status == 'Y')
					// $label .= ' <span class="label label-success">Lulus</span>';
				// else
					// $label .= ' <span class="label label-danger">Step '.$row->sreg_step.'</span>';
			}
			else if($row->pin_status=='N' and $row->pin_booking_by==null){
				$label = '<span class="label label-success">Available</span>';
				$delete = '<a href="javascript:del('.$row->pin_id.', \''.$row->pin_transaction_number.'\')" title="Delete Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-trash"></i></a>';
			} 
			else {
				$label = '<span class="label label-warning">Booked By '.$row->par_fullname.'</span>';
				$delete = '<a href="javascript:del('.$row->pin_id.', \''.$row->pin_transaction_number.'\')" title="Delete Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-trash"></i></a>';
			}
			$reset  = "";
			$resetbutton = "";

			if($row->pin_status=='N' and $row->pin_booking_by!=null){
				$reset = '<a title="Reset '.$row->pin_transaction_number.'" class="btn btn-xs btn-icon btn-success" onClick="reset('.$row->pin_id.')"><i class="icon-reset"></i></a>';
				$resetbutton = '<input type="checkbox" class="reset" value="'.$row->pin_id.'">';
			}
			
			$rows = array (
				'<input type="checkbox" class="chk" value="'.$row->pin_id.'">',
				$resetbutton,
				$label,
				$this->cm->get('form_pin', $row->pin_type).'<br><span class="text-grey">'.date('m/d/Y H:i:s', strtotime($row->pin_create_date)).'</span>',
				$row->periode_name.' / '.$row->periode_track_type,
				'Rp. '.number_format($row->pin_price,0,",","."),
				$row->pin_max_prodi,
				$row->pin_transaction_number,
				$row->pin_token,
				$row->pin_desc,
				// !empty($row->student_request) ? '<span title="'.$row->student_request.'" style="text-decoration-line: underline; text-decoration-style: dotted;">Request By Email</span><br>'.date('d/m/Y H:i', strtotime($row->student_request_date)) : $row->student_desc,
				'<a href="javascript:copy('.$row->pin_id.')" title="Copy to Clipboard" class="btn btn-xs btn-icon btn-warning"><i class="icon-files-empty"></i></a>
				<a href="javascript:edit('.$row->pin_id.')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a>'.$delete.$reset
			);
			 
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	}
	
	public function insert()
	{ 
		ini_set('max_execution_time', 0);
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('total')) return false; 
		
		$user 		= y_info_login();
		$total 		= $this->input->post('total'); 
		$type_pin 	= $this->input->post('type_pin');
		$desc 		= $this->input->post('desc');
		$periode 	= $this->input->post('periode');
		$pcode 		= $this->input->post('periode_kode');
		$pil_prodi 	= $this->input->post('pil_prodi');

		$discount_code 	= $this->input->post('discount_code');

		$harga 		= str_replace(".","",$this->input->post('harga'));
		
		if($type_pin=='online') $type_pin_temp = 1;
		else  $type_pin_temp = 2;
		
		$pin =  $pcode;
		
		$tot 	= $this->dm->cekTotalPin($pin)->row();
		$dt 	= $this->dm->cekPin($pin)->result();
		// print_r($dt);
		$array 	= array();
		if($dt){ foreach($dt as $row) $array[] = $row->pin_transaction_number; }
		
		$all_total = $tot->total+$total;
		
		if ($all_total<=100000){
			$i 		= 0;
			$string = ""; 
			$max 	= (($all_total+100)<=100000?($all_total+100):99999); 
			do {
				$number 	= rand(0,$max);
				$username 	= $pin.sprintf("%05s", $number);
				$password 	= $this->generate_password(9);
				// $chk 		= $this->dm->getby(array('pin_transaction_number' => $username))->row();
				if(!in_array($username,$array))
				{ 
					$item = array('pin_create_date' => date('Y-m-d H:i:s'),
					  'pin_transaction_number'	=> $username,
					  'pin_token' 				=> $password,  
					  'pin_id_periode' 			=> $periode,
					  'pin_max_prodi' 			=> $pil_prodi,
					  'pin_price' 				=> $harga,
					  'pin_type' 				=> $type_pin,
					  'pin_desc' 				=> $desc,
					  'pin_kodediskon' 			=> $discount_code
					); 
					  
					// echo "<pre>";
					// print_r($item);
					// echo "</pre>";
					
					$id = $this->dm->add($item);
					if(!empty($id))
					{  
						array_push($array,$username);
						$i++;
						$string .= "$i. $username : $password  \n";
					}  
				 
				} 
			} while ($i<$total); 
			
			echo json_encode(array('status' => 'ok;', 'text' => $string));
			
		}
		else echo json_encode(array('status' => 'error;', 'text' => 'Data Pin yang di pilih melebihi jumlah yang bisa di generate. maksimal berjumlah 100000. Saat ini sudah ada total '.$tot->total.' pin untuk data yang dipilih'));
		
		// $chk = $this->dm->getby(array('pin_transaction_number' => $username))->row();
		// if(!empty($chk))
		// {
			// echo json_encode(array('status' => 'error;', 'text' => 'Username Duplicate'));
			// return false;	
		// }
		
		// $item = array('pin_create_date' 		=> date('Y-m-d H:i:s'),
					  // 'pin_transaction_number'	=> $username,
					  // 'pin_token' 				=> $password,  
					  // 'pin_id_periode' 			=> $periode,
					  // 'pin_max_prodi' 			=> $pil_prodi,
					  // 'pin_price' 				=> $harga,
					  // 'pin_type' 				=> $type_pin,
					  // 'pin_desc' 				=> $desc);

		
		// $id = $this->dm->add($item);
		// if( !empty($id) )
		// { 
			// echo json_encode(array('status' => 'ok;', 'text' => ''));
		// }
		// else
			// echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function generate_password($n)
	{  
		//B, I, O, Z, S
		$characters = 'ACDEFGHJKLMNPQRTUVWXY0123456789';
		$string = '';
		$max = strlen($characters) - 1;
		for ($i = 0; $i < $n; $i++) {
			  $string .= $characters[mt_rand(0, $max)];
		}
		return $string;
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
		
		// $item['student_password'] = '$USMBB$'.substr(sha1(md5(md5($item['student_password_plain']))), 0, 50);
		
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
	
	public function getmax()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$max = $this->dm->getmax()->row()->maks;
		
		echo json_encode(array('status' => 'ok;', 'text' => $max));
	}
	
	public function copied()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id  = $this->input->post('id'); 
		$dbs = $this->dm->getbyin($id)->result();
		$text = '';
		
		if(!empty($dbs))
		{
			foreach($dbs as $db)
				$text .= $db->pin_transaction_number.' : '.$db->pin_token."<br>";	
		}
		
		echo json_encode(array('status' => 'ok;', 'text' => $text));
	}

	public function reset()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id  = $this->input->post('id'); 
		$dbs = $this->dm->getbyin($id)->result();
		$text = '';
		
		if(!empty($dbs))
		{
			foreach($dbs as $db){
				$this->sreg->deletebypin($db->pin_id);
				$dt_pin['pin_status'] = 'N';
				$dt_pin['pin_booking_by'] = null; 
				$this->dm->edit($db->pin_id,$dt_pin);
			} 
		}
		
		echo json_encode(array('status' => 'ok;', 'text' => $text));
	}
}

?>