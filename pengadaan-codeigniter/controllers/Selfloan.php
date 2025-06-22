<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Selfloan extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Selfloan_Model', 'dm', TRUE);
		
		if (!$this->session->userdata('login')) {
			header("Location: /");
			die();
		}
	}
	
	public function index()
	{  
		$iuser = $this->session->userdata('user'); 
		if($iuser['membertype']== 3){
			header("Location: /");
			die();
		}
		$data['view'] 	= 'frontend/selfloan/index';
		$data['title']	= 'Self Loan';		
		$data['icon']	= 'icon-book3';
		
		$this->load->helper('form');
		$iuser = $this->session->userdata('user');
		$data['book'] = $this->dm->GetRentBook($iuser['id'])->result();  
		$this->load->view('frontend/tpl', $data);
	}
	
	public function process()
	{
		if(!$this->input->is_ajax_request()) return false;
		$code = trim($this->input->post('barcode'));
		$iuser = $this->session->userdata('user');
		  
		$book = $this->dm->checkBook($code)->row();
		
		if($book){ 
			$member = $this->dm->CheckMemberBook($book->knowledge_type_id,$iuser['username'])->row();	
			if ($book->status!='1'){
				echo json_encode(array('status' => 'error;', 'text' => 'Status pustaka sedang tidak tersedia. Status pustaka saat ini: '.statuspustaka($book->status)));  
			}
			else if ($book->rentable!='1'){
				echo json_encode(array('status' => 'error;', 'text' => 'Jenis Pustaka tidak di ijinkan untuk dipinjamkan'));  
			}
			else if ($member->status_pinjam!='1'){
				echo json_encode(array('status' => 'error;', 'text' => 'Jenis Keanggotaan Anda tidak diperbolehkan meminjam pustaka ini'));  
			}
			else if ($member->rent_book>=$member->rent_quantity){
				echo json_encode(array('status' => 'error;', 'text' => 'Anda sudah melewati batas jumlah pustaka yang dapat dipinjam. Maksimal pustaka yang dapat dipinjam adalah '.$member->rent_quantity));
			}
			else {
				$data['barcode'] 	= $code;
				$data['id'] 		= $book->ksid;
				$data['title'] 		= $book->title;
				$data['duration'] 	= $member->rent_period; 
				$data['quantity'] 	= $member->rent_quantity; 
				
				echo json_encode(array('status' => 'ok;', 'text' => $data));  
			}
		}
		else echo json_encode(array('status' => 'error;', 'text' => 'barcode yang anda masukkan, tidak ada di data kami'));   
	}
	
	public function save()
	{
		if(!$this->input->is_ajax_request()) return false;
		$iuser = $this->session->userdata('user');
		 
		$code = $this->input->post('code');
		$stockid = $this->input->post('stockid');
		
		$rent_cart['member_id'] = $iuser['id'];
		$rent_cart['rental_code'] = $this->buildRentalCode($iuser['id']);
		$rent_cart['created_by'] = $iuser['username'];
		$rent_cart['created_at'] = date('Y-m-d H:i:s');
		
		$rentcartid = $this->dm->addTable('rent_cart',$rent_cart);
		
		$member = $this->dm->CheckMember($iuser['username'])->row();
		
		$rent = array();
		foreach($stockid as $row){ 
			$rent['rent_cart_id'] 			= $rentcartid;
			$rent['member_id'] 				= $iuser['id'];
			$rent['knowledge_stock_id'] 	= $row;
			$rent['rent_date'] 				= date('Y-m-d');
			$rent['return_date_expected'] 	= date('Y-m-d', strtotime("+".$member->rent_period." days"));
			$rent['status'] 				= '1';
			$rent['rent_period'] 			= $member->rent_period;
			$rent['rent_period_unit'] 		= $member->rent_period_unit;
			$rent['rent_period_day'] 		= $member->rent_period * getRentPeriodUnitRentDays($member->rent_period_unit);
			$rent['rent_cost_per_day'] 		= 0;
			$rent['rent_cost_total'] 		= 0;
			$rent['penalty_per_day'] 		= 0;
			$rent['penalty_day'] 			= 0;
			$rent['penalty_holiday'] 		= 0;
			$rent['penalty_total'] 			= 0;
			$rent['extended_count']			= 0;
			$rent['created_by'] = $iuser['username'];
			$rent['created_at'] = date('Y-m-d H:i:s');
			$this->dm->addTable('rent',$rent);
			
			$this->dm->editTable('knowledge_stock','id',$row, array('status'=> '2'));
			
		}
			 
		echo json_encode(array('status' => 'ok;', 'text' => 'Berhasil disimpan'));  
	}
	
	function buildRentalCode($member_id, $to_num = false, $passKey = null) {
    $in = time() . $member_id;
    $pad_up = 10;
    $index = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    if ($passKey !== null) {
      // Although this function's purpose is to just make the
      // ID short - and not so much secure,
      // with this patch by Simon Franz (http://blog.snaky.org/)
      // you can optionally supply a password to make it harder
      // to calculate the corresponding numeric ID
      for ($n = 0; $n<strlen($index); $n++) {
        $i[] = substr( $index,$n ,1);
      }
      $passhash = hash('sha256',$passKey);
      $passhash = (strlen($passhash) < strlen($index))
        ? hash('sha512',$passKey)
        : $passhash;
      for ($n=0; $n < strlen($index); $n++) {
        $p[] =  substr($passhash, $n ,1);
      }
      array_multisort($p,  SORT_DESC, $i);
      $index = implode($i);
    }
    $base  = strlen($index);
    if ($to_num) {
      // Digital number  <<--  alphabet letter code
      $in  = strrev($in);
      $out = 0;
      $len = strlen($in) - 1;
      for ($t = 0; $t <= $len; $t++) {
        $bcpow = bcpow($base, $len - $t);
        $out   = $out + strpos($index, substr($in, $t, 1)) * $bcpow;
      }
      if (is_numeric($pad_up)) {
        $pad_up--;
        if ($pad_up > 0) {
          $out -= pow($base, $pad_up);
        }
      }
      $out = sprintf('%F', $out);
      $out = substr($out, 0, strpos($out, '.'));
    } else {
      // Digital number  -->>  alphabet letter code
      if (is_numeric($pad_up)) {
        $pad_up--;
        if ($pad_up > 0) {
          $in += pow($base, $pad_up);
        }
      }
      $out = "";
      for ($t = floor(log($in, $base)); $t >= 0; $t--) {
        $bcp = pow($base, $t);
        $a   = floor($in / $bcp) % $base;
        $out = $out . substr($index, $a, 1);
        $in  = $in - ($a * $bcp);
      }
      $out = strrev($out); // reverse
    }
    return $out;
  }
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'guestbook_fullname', 'dt' => 0 ),
			array( 'db' => 'guestbook_email', 'dt' => 1 ),
			array( 'db' => 'guestbook_mobile_phone', 'dt' => 2 ), 
			array( 'db' => 'guestbook_institution', 'dt' => 3 ),
			array( 'db' => 'guestbook_date', 'dt' => 4 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{
			 
			$rows = array ( 
				$row->guestbook_fullname,
				$row->guestbook_email, 
				$row->guestbook_mobile_phone, 
				$row->guestbook_institution, 
				$row->guestbook_date, 
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
		$item['component_status'] = '1';		
		
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
		
		if(!isset($item['component_custom']))
			$item['component_custom'] = '0';
		
		if( $this->dm->edit($id, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function active()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id = $this->input->post('id');
		$mode = $this->input->post('mode');
		
		if( $this->dm->edit($id, array('component_status' => $mode)) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
}

?>