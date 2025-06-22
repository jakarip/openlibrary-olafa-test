<?php

require APPPATH . '/libraries/REST_Controller.php';
 
class Rfidscanner2 extends REST_Controller {
 
    function __construct($config = 'rest') {
        parent::__construct($config);
		$this->load->model("RfidModel","rm");
		$this->load->model('Usermodel', 'um', TRUE);
		$this->load->model('ApiMobileModel', 'api', TRUE); 
		$this->load->library('Cipher');

    } 

	
   
    function private_key_post() {
		
		$public_key 		= $this->input->post('public_key');
		$status 			= "success";

		
		if($public_key=='86qp22pegQE9qKzhUurPyNTIfQolRvBA'){ 

			$data['rfid_datetime'] = strtotime(date("Y-m-d 00:00:00")); 
			$data['public_key'] = $public_key;
			$data['rfid_ip'] = $this->getIPAddress();

			$string = json_encode($data,true); 

			$cipher = new Cipher(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB); 
			$kunci = "openlibrary";  
			$private_key = $cipher->encrypt($string, $kunci);

			$this->response(array('status' => 'success', 'private_key'=>$private_key), 200);
		}
		else $this->response(array('status' => 'failed'), 502); 
    }

	function getIPAddress() {   
		//whether ip is from the share internet  
		 if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
					$ip = $_SERVER['HTTP_CLIENT_IP'];  
			}  
		//whether ip is from the proxy  
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
		 }  
	//whether ip is from the remote address  
		else{  
				 $ip = $_SERVER['REMOTE_ADDR'];  
		 }  
		 return $ip;  
	}  
   
    function index_get() {
		
		$rfid 				= $this->input->get('rfid');
		$type 				= $this->input->get('type');
		$status 			= "success";
		
		if($rfid!=""){ 
			$case 	= $this->rm->GetRfidNotSameWithIgracias()->result(); 

			foreach($case as $ca){
				$case_member[$ca->rfid] = $ca->username;
			}  

 
			$dt = $this->rm->GetRfidScanner($rfid)->row(); 
			if ($dt){ 
				if($dt->member_type_id!='19'){    

					if($type=='selfloan'){ 
						$rent = $this->api->getRentNotYetReturnIdMember($dt->memberid)->result();
						
						if($rent){    
							foreach($rent as $key => $row){  
								if($row->penalty_per_day!=0) $this->calculatepenalty($row->id,$row->return_date_expected,$row->penalty_per_day,$row->member_id);  
							} 
						} 
						
						$penalty = $this->api->getRemainingPenalty($dt->memberid)->row();
						$remaining_penalty = $penalty->penalty - $penalty->payment;  

						$this->response(array('status' => 'success', 'memberid' => $dt->memberid, 'username'=>$dt->master_data_user, 'nim_nip'=>$dt->master_data_number, 'name'=>$dt->master_data_fullname,'penalty'=>$remaining_penalty), 200); 
					} 
					else { 
						$this->response(array('status' => 'success', 'memberid' => $dt->memberid, 'username'=>$dt->master_data_user, 'nim_nip'=>$dt->master_data_number, 'name'=>$dt->master_data_fullname), 200);  
					}
				} 
				else $this->response(array('status' => 'failed'), 502);  
			} 
			else {
				if (array_key_exists($rfid, $case_member)){
					$member = $this->rm->GetMemberScanner($case_member[$rfid])->row(); 
					if ($member){  
						if($member->member_type_id !='19'){    

							if($type=='selfloan'){ 
								$rent = $this->api->getRentNotYetReturnIdMember($dt->memberid)->result(); 
								
								if($rent){   
									foreach($rent as $key => $row){  
										if($row->penalty_per_day!=0) $this->calculatepenalty($row->id,$row->return_date_expected,$row->penalty_per_day,$row->member_id);  
									} 
								} 
								
								$penalty = $this->api->getRemainingPenalty($dt->memberid)->row();
								$remaining_penalty = $penalty->penalty - $penalty->payment; 
								
								$this->response(array('status' => 'success', 'memberid' => $dt->memberid,'username'=>$member->master_data_user, 'nim_nip'=>$dt->master_data_number, 'name'=>$dt->master_data_fullname,'penalty'=>$remaining_penalty), 200);
							}
							else {

								$this->response(array('status' => 'success', 'memberid' => $dt->memberid, 'username'=>$member->master_data_user, 'nim_nip'=>$dt->master_data_number, 'name'=>$dt->master_data_fullname), 200);
							}

						} 
						else $this->response(array('status' => 'failed'), 502);  
						
					} else $this->response(array('status' => 'failed'), 502); 
				}
				else $this->response(array('status' => 'failed'), 502); 
			} 
		}
		else $this->response(array('status' => 'failed'), 502);
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
   
    function book_post() {
		
		$private_key 		= $this->input->post('private_key');
		$barcode 			= $this->input->post('barcode');
		$status 			= "success";
		
 
		$cipher = new Cipher(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB); 

		$data['rfid_datet                 ime'] = strtotime(date("Y-m-d 00:00:00")); 
		$data['public_key'] = '86qp22pegQE9qKzhUurPyNTIfQolRvBA';
		$data['rfid_ip'] = $this->getIPAddress();

	 

		$string = json_encode($data,true); 
		$encrypt = $cipher->encrypt($string, 'openlibrary');  
 
		// if($private_key==$encrypt){
	 
			$string = $cipher->decrypt($private_key, 'openlibrary'); 
			$dt = json_decode($string,true);
			$dt['rfid_action'] = 'book';
			$dt['rfid_datetime'] = date('Y-m-d H:i:s');
			unset($dt['public_key']);
			$this->rm->add_shelfloan_new_log($dt);

			$dts 	= $this->rm->GetBook($barcode)->row_array();
		 
			$this->response(array('status' => 'success','data'=>$dts), 200);
		// }
		// else $this->response(array('status' => 'failed'), 502);
    } 

	function rent_book_post() {
		
		$private_key 		= $this->input->post('private_key');
		$barcode 			= $this->input->post('barcode');
		$memberid 			= $this->input->post('memberid');
		$status 			= "success";
		
 
		$cipher = new Cipher(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB); 

		$data['rfid_datetime'] = strtotime(date("Y-m-d 00:00:00")); 
		$data['public_key'] = '86qp22pegQE9qKzhUurPyNTIfQolRvBA';
		$data['rfid_ip'] = $this->getIPAddress();

		$string = json_encode($data,true); 
		$encrypt = $cipher->encrypt($string, 'openlibrary');  

		// if($private_key==$encrypt){ 


			$dts 	= $this->rm->GetBook($barcode)->row_array();

			if($dts['rentable']=='false') $this->response(array('status' => 'failed','message'=>'Jenis buku tidak dapat dipinjam, hanya dapat dibaca di Openlibrary. Silahkan menghubungi petugas sirkulasi'), 502);

			if($dts['status']=='dipinjam') $this->response(array('status' => 'failed','message'=>'Status buku sedang dipinjam. Silahkan menghubungi petugas sirkulasi'), 502); 

			if($dts['status']=='lainnya') $this->response(array('status' => 'failed','message'=>'Buku tidak dapat dipinjam karena statusnya belum tersedia. Silahkan menghubungi petugas sirkulasi'), 502);

			$member = $this->rm->GetMemberAndRent($memberid,$dts['itemid'])->row();

			if($member->rent_quantity==$member->total_pinjam) $this->response(array('status' => 'failed','message'=>'Anda sudah mencapai batas jumlah maksimal peminjaman buku ('.$member->total_pinjam.'). Silahkan melakukan pengembalian buku terlebih dahulu'), 502);

			if($member->total_judul_sama>0) $this->response(array('status' => 'failed','message'=>'Tidak dapat meminjam buku dengan judul yang sama'), 502);

			$rent_cart['member_id'] = $memberid;
			$rent_cart['rental_code'] = 'new_selfloan';
			$rent_cart['created_by'] = 'new_selfloan';
			$rent_cart['created_at'] = date('Y-m-d H:i:s');
			// $rent_cart['updated_by'] = 'new_selfloan';
			// $rent_cart['updated_at'] = date('Y-m-d H:i:s');
			$rent['rent_cart_id'] = $this->rm->add_rent_cart($rent_cart); 
			$rent['member_id'] = $memberid;
			$rent['knowledge_stock_id'] = $dts['id'];  
  
			$extend_to_date = date('Y-m-d', strtotime("+{$member->rent_period} day", time())); 
			  
			$return_date = $this->calculateReturnDateExpected($extend_to_date);  

			$rent['rent_date'] = date('Y-m-d');
			$rent['return_date_expected'] = $return_date; 
			$rent['status'] = '1';
			$rent['rent_period'] = $member->rent_period;
			$rent['rent_period_unit'] = $member->rent_period_unit;
			$rent['rent_period_day'] = $member->rent_period;
			$rent['rent_cost_per_day'] = $dts['rent_cost'];  
			$rent['rent_cost_total'] = $member->rent_period * $dts['rent_cost'];
			$rent['penalty_per_day'] = $dts['penalty_cost'];   
			$rent['extended_count'] = 0;
			$rent['created_by'] = 'new_selfloan';
			$rent['created_at'] = date('Y-m-d H:i:s');
			// $rent['updated_by'] = 'new_selfloan'; 
			// $rent['updated_at'] = date('Y-m-d H:i:s');

			// print_r($rent_cart); 
			// print_r($rent);
			$rent_id = $this->rm->add_rent($rent);  

			$stock['status'] = '2';
			$this->rm->edit_knowledge_stock($dts['id'],$stock); 
	 
			$string = $cipher->decrypt($private_key, 'openlibrary'); 
			$dt = json_decode($string,true);
			$dt['rfid_action'] = 'rent_book';
			$dt['rfid_datetime'] = date('Y-m-d H:i:s');
			$dt['rfid_stockid'] = $dts['id'];  
			$dt['rfid_memberid'] = $memberid;
			unset($dt['public_key']);
			$this->rm->add_shelfloan_new_log($dt);  

			$messages 		= "Anda telah meminjam buku dengan kode ".$barcode.".";

			$itemnotif['notif_id_member'] 	= $member->master_data_user;
			$itemnotif['notif_type'] 		= 'peminjaman';
			$itemnotif['notif_content'] 	= $messages;
			$itemnotif['notif_date'] 		= date('Y-m-d H:i:s');
			$itemnotif['notif_status'] 		= 'unread';
			$itemnotif['notif_id_detail'] 	= $rent_id;
	
			$notif_id = $this->api->add_custom($itemnotif,'notification_mobile');  
	
			$token = $this->api->getTokenNotificationMobile($memberid)->row();
	
			$title = "Peminjaman Buku";  
			$notif_content = $messages;
			$notif_id_detail = $rent_id;
			$notif_type = 'sirkulasi'; 
			$token = $token->master_data_token;
	
			// NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token); 

		 
			$this->response(array('status' => 'success'), 200);
		// }
		// else $this->response(array('status' => 'failed'), 502);
    }   
	

	

	function return_book_post() {
		
		$private_key 		= $this->input->post('private_key');
		$barcode 			= $this->input->post('barcode');
		$status 			= "success";
 
		$cipher = new Cipher(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB); 

		$data['rfid_datetime'] = strtotime(date("Y-m-d 00:00:00")); 
		$data['public_key'] = '86qp22pegQE9qKzhUurPyNTIfQolRvBA';
		$data['rfid_ip'] = $this->getIPAddress();

		$string = json_encode($data,true); 
		$encrypt = $cipher->encrypt($string, 'openlibrary');  

		// if($private_key==$encrypt){  

			$dts 	= $this->rm->GetBook($barcode)->row_array();

			if($dts['rentable']=='false') $this->response(array('status' => 'failed','message'=>'Jenis buku tidak dapat dipinjam, hanya dapat dibaca di Openlibrary. Silahkan menghubungi petugas sirkulasi'), 502);

			if($dts['status']=='tersedia') $this->response(array('status' => 'failed','message'=>'Status buku tersedia. Silahkan menghubungi petugas sirkulasi'), 502);

			$row = $this->rm->GetRent($dts['id'])->row();
 
			
			if($row){    
				if($row->penalty_per_day!=0) $this->calculatepenalty($row->id,$row->return_date_expected,$row->penalty_per_day,$row->member_id);   
			} 

			$date_now = date("Y-m-d"); 
			 
			if($date_now > $row->return_date_expected) $this->response(array('status' => 'failed','message'=>'Anda sudah melewati batas pengembalian buku. Silahkan menghubungi petugas sirkulasi untuk melakukan pengembalian buku & membayar denda'), 502);

			 
			$rent['status'] = '2';
			$rent['return_date'] = $date_now;   
			$rent['updated_by'] = 'new_selfloan'; 
			$rent['updated_at'] = date('Y-m-d H:i:s'); 

			$this->rm->edit_rent($row->id, $rent);   

			$stock['status'] = '1';
			$this->rm->edit_knowledge_stock($dts['id'],$stock); 
	 
			$string = $cipher->decrypt($private_key, 'openlibrary'); 
			$dt = json_decode($string,true);
			$dt['rfid_action'] = 'return_book';
			$dt['rfid_datetime'] = date('Y-m-d H:i:s');
			$dt['rfid_stockid'] = $dts['id'];
			$dt['rfid_memberid'] = $row->member_id;
			unset($dt['public_key']);
			$this->rm->add_shelfloan_new_log($dt);   

			$messages 		= "Anda telah mengembalikan buku dengan kode ".$barcode.".";

			$itemnotif['notif_id_member'] 	= $row->master_data_user;
			$itemnotif['notif_type'] 		= 'pengembalian';
			$itemnotif['notif_content'] 	= $messages;
			$itemnotif['notif_date'] 		= date('Y-m-d H:i:s');
			$itemnotif['notif_status'] 		= 'unread';
			$itemnotif['notif_id_detail'] 	= $row->id;
	
			$notif_id = $this->api->add_custom($itemnotif,'notification_mobile');  
	
			$token = $this->api->getTokenNotificationMobile($row->member_id)->row();
	
			$title = "Pengembalian Buku";  
			$notif_content = $messages;
			$notif_id_detail = $row->id;
			$notif_type = 'sirkulasi'; 
			$token = $token->master_data_token;
	
			// NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token); 
		 
			$this->response(array('status' => 'success'), 200);
		// }
		// else $this->response(array('status' => 'failed'), 502);
    } 

	function calculateReturnDateExpected($return_date, $current_date = null){ 
		if ($current_date == null) $current_date = date('Y-m-d');
		$holidays_on_rent = $this->api->doCountHolidayBetween($current_date, $return_date);
		$next_return_date = $return_date;
 
		// echo $holidays_on_rent." ".$current_date." ".$return_date."\n";
		while ($holidays_on_rent > 0) {
		  $next_return_date = date('Y-m-d', strtotime("+{$holidays_on_rent} day", strtotime($return_date)));
		  $holidays_on_rent = $this->api->doCountHolidayBetween($return_date, $next_return_date); 
		  $return_date = $next_return_date;
		//   echo $holidays_on_rent." ".$current_date." ".$return_date."\n";
		}
		return $return_date;
	}
} 

?> 