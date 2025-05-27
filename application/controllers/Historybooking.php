<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Historybooking extends CI_Controller {

    public function __construct(){ 
        parent::__construct();
		
		if(!$this->session->userdata('login_room')) redirect(url_admin(), 'refresh'); 
        $this->load->model('BookingModel','bm');
        $this->load->model('QuitionerModel','rm');
    }

    public function index(){ 
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');  
        $data['menu']		= 'historybooking';
        $this->load->view('theme',$data);
    }

    public function ajax_data(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$person = "";
		if($this->session->userdata('usergroup')=='superadmin'){
			$person 	= getLang('people');
			$table 		= "select *,DATE_FORMAT(bk_startdate,'%Y-%m-%d') tanggal,DATE_FORMAT(bk_startdate,'%H:%i') starthour,DATE_FORMAT(bk_enddate,'%H:%i') endhour, bk_total member_name 
					from telu8381_room.booking  
					left join telu8381_openlibrarys.member on member.id=bk_memberid 
					left join telu8381_room.room on bk_room_id=room_id where bk_status in ('Request','Approved') and bk_startdate>='".date('Y-m-d')."'";
			$order 		= "order by bk_status desc,bk_startdate desc"; // default order  
		} else {
			$table 		= "select *,DATE_FORMAT(bk_startdate,'%Y-%m-%d') tanggal,DATE_FORMAT(bk_startdate,'%H:%i') starthour,DATE_FORMAT(bk_enddate,'%H:%i') endhour, bk_name member_name
					from telu8381_room.booking 
					left join telu8381_openlibrarys.member on member.id=bk_memberid  
					left join telu8381_room.room on bk_room_id=room_id where bk_username='".$this->session->userdata('username')."'";
			$order 		= "order by bk_startdate desc"; // default order  
		}
		
		//			left join telu8381_openlibrarys.member_type on member_type_id = member_type.id 
		$colOrder 	= array(null,'master_data_fullname','bk_mobile_phone','room_name','tanggal','bk_startdate','bk_enddate','bk_purpose','member_name','bk_status','bk_reason',null); //set column field database for datatable orderable
		$colSearch 	= array('master_data_fullname','bk_mobile_phone','room_name','tanggal','starthour','endhour','bk_purpose','member_name','bk_status','bk_reason'); //set column field database for datatable
			
		
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
            $row[] = ucwords(strtolower($dt->master_data_fullname));
            $row[] = $dt->bk_mobile_phone;
            $row[] = $dt->room_name.' ('.$dt->room_capacity.' '.getLang('people').')';
            $row[] = convert_format_date($dt->tanggal);
            $row[] = $dt->starthour;
            $row[] = $dt->endhour; 
            $row[] = $dt->bk_purpose;
            $row[] = ucwords(strtolower($dt->member_name)).' '.$person;
			
			if ($dt->bk_status=='Cancel'){
				$row[] = '<div class="btn-group"><button type="button" class="btn btn-sm btn-default" title="'.$dt->bk_status.'">'.$dt->bk_status.'</button></div>'; 
				$row[] = "";
				$row[] = "";
			}
			else if ($dt->bk_status=='Not Approved'){
				$row[] = '<div class="btn-group"><button type="button" class="btn btn-sm btn-info" title="'.$dt->bk_status.'">'.$dt->bk_status.'</button></div>';  
				$row[] = $dt->bk_reason;
				$row[] = "";
			}
			else if ($dt->bk_status=='Not Attend'){
				$row[] = '<div class="btn-group"><button type="button" class="btn btn-sm btn-danger" title="'.$dt->bk_status.'">'.$dt->bk_status.'</button></div>';  
				$row[] = "-";
				$row[] = "";
			}
			else if ( $dt->bk_status=='Attend'){
				$row[] = '<div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="'.$dt->bk_status.'">'.$dt->bk_status.'</button></div>';  
				$row[] = "-";
				$row[] = "";
			}
			else if ($dt->bk_status=='Approved'){
				$row[] = '<div class="btn-group"><button type="button" class="btn btn-sm btn-primary" title="'.$dt->bk_status.'">'.$dt->bk_status.'</button></div>'; 
				$row[] = "-";
				if($this->session->userdata('usergroup')=='superadmin'){
					$row[] = '<div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="'.getLang("Attend").'"onclick="edit('."'".$dt->bk_id."','Attend'".')">Attend</button></div><br><br><div class="btn-group"> <button type="button" class="btn btn-sm btn-danger" title="'.getLang("Not Attend").'" onclick="edit('."'".$dt->bk_id."','Not Attend'".')">Not Attend</button></div>';
				}
				else {
					$row[] = "";
				}
			}
			else if ($dt->bk_status=='Request'){
				$row[] = '<div class="btn-group"><button type="button" class="btn btn-sm btn-warning" title="'.$dt->bk_status.'">'.$dt->bk_status.'</button></div>';
				$row[] = "-";
				if($this->session->userdata('usergroup')=='superadmin'){
					$row[] = '<div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="'.getLang("Approved").'" onclick="edit('."'".$dt->bk_id."','Approved'".')">Approved</button></div><br><br><div class="btn-group"> <button type="button" class="btn btn-sm btn-info" title="'.getLang("Not Approved").'"onclick="not_approved('."'".$dt->bk_id."','Not Approved'".')">Not Approved</button></div><br><br><div class="btn-group"> <button type="button" class="btn btn-sm btn-default" title="'.getLang("Cancel").'"onclick="edit('."'".$dt->bk_id."','Cancel'".')">Cancel</button></div>';
				}else {
					$row[] = '<div class="btn-group"> <button type="button" class="btn btn-sm btn-default" title="'.getLang("Cancel").'"onclick="edit('."'".$dt->bk_id."','Cancel'".')">Cancel</button></div>';
				}
			} 
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
	
	
    function update(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
        $data 	= $this->bm->getbyid($id)->row();
        $item   = $_POST['inp'];    
		if($item['bk_status']=='Approved') {
			$messages = "Permintaan peminjaman ".$data->room_name." pada tanggal ".convert_format_date(substr($data->bk_startdate,0,10))." ".substr($data->bk_startdate,11,5)." - ".substr($data->bk_enddate,11,5)." telah disetujui.";
			// $messages = "\n\n*[OPENLIBRARY]*\nPermintaan peminjaman *".$data->room_name."* pada tanggal *".convert_format_date(substr($data->bk_startdate,0,10))." ".substr($data->bk_startdate,11,5)." - ".substr($data->bk_enddate,11,5)."* telah disetujui.\n\n";
			// SendWA($data->bk_mobile_phone,$messages);
			
				
			$itemnotif['notif_id_member'] 	= $data->bk_username;
			$itemnotif['notif_type'] 	= 'ruangan';
			$itemnotif['notif_content'] 	= $messages;
			$itemnotif['notif_date'] 	= date('Y-m-d H:i:s', strtotime('7 hour'));
			$itemnotif['notif_status'] 	= 'unread';
			$itemnotif['notif_id_detail'] 	= $id;
			$notif_id = $this->bm->addNotificationMobile($itemnotif);

			$token = $this->bm->getTokenNotificationMobile($data->bk_memberid)->row();

			$title = "Ruangan - Approved";  
			$notif_content = $messages;
			$notif_id_detail = $id;
			$notif_type = 'ruangan';
			$token = $token->master_data_token;

			NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token); 
			
		}
		else if($item['bk_status']=='Cancel') {
			$messages = "Permintaan peminjaman ".$data->room_name." pada tanggal ".convert_format_date(substr($data->bk_startdate,0,10))." ".substr($data->bk_startdate,11,5)." - ".substr($data->bk_enddate,11,5)." telah dibatalkan.";
			// $messages = "\n\n*[OPENLIBRARY]*\nPermintaan peminjaman *".$data->room_name."* pada tanggal *".convert_format_date(substr($data->bk_startdate,0,10))." ".substr($data->bk_startdate,11,5)." - ".substr($data->bk_enddate,11,5)."* telah dibatalkan.\n\n";
			// SendWA($data->bk_mobile_phone,$messages);
			
			
			$itemnotif['notif_id_member'] 	= $data->bk_username;
			$itemnotif['notif_type'] 	= 'ruangan';
			$itemnotif['notif_content'] 	= $messages;
			$itemnotif['notif_date'] 	= date('Y-m-d H:i:s', strtotime('7 hour'));
			$itemnotif['notif_status'] 	= 'unread';
			$itemnotif['notif_id_detail'] 	= $id;
			$notif_id = $this->bm->addNotificationMobile($itemnotif); 

			$token = $this->bm->getTokenNotificationMobile($data->bk_memberid)->row();

			$title = "Ruangan - Cancel";  
			$notif_content = $messages;
			$notif_id_detail = $id;
			$notif_type = 'ruangan';
			$token = $token->master_data_token;

			NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token); 
		}
		else if($item['bk_status']=='Not Approved') {
			$messages = "Mohon maaf, permintaan peminjaman ".$data->room_name." pada tanggal ".convert_format_date(substr($data->bk_startdate,0,10))." ".substr($data->bk_startdate,11,5)." - ".substr($data->bk_enddate,11,5)." tidak disetujui karena ".$data->bk_reason;
			// $messages = "\n\n*[OPENLIBRARY]*\nMohon maaf, permintaan peminjaman *".$data->room_name."* pada tanggal *".convert_format_date(substr($data->bk_startdate,0,10))." ".substr($data->bk_startdate,11,5)." - ".substr($data->bk_enddate,11,5)."* tidak disetujui karena *".$data->bk_reason."*\n\n";
			// SendWA($data->bk_mobile_phone,$messages);
				
			$itemnotif['notif_id_member'] 	= $data->bk_username;
			$itemnotif['notif_type'] 	= 'ruangan';
			$itemnotif['notif_content'] 	= $messages;
			$itemnotif['notif_date'] 	= date('Y-m-d H:i:s', strtotime('7 hour'));
			$itemnotif['notif_status'] 	= 'unread';
			$itemnotif['notif_id_detail'] 	= $id;
			$notif_id = $this->bm->addNotificationMobile($itemnotif); 

			$token = $this->bm->getTokenNotificationMobile($data->bk_memberid)->row();

			$title = "Ruangan - Not Approved";  
			$notif_content = $messages;
			$notif_id_detail = $id;
			$notif_type = 'ruangan';
			$token = $token->master_data_token;

			NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token); 
		} 

		if ($this->bm->edit($id, $item)) echo json_encode(array("status" => TRUE));
    }
	
	 function not_approved(){ 
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
        $data 	= $this->bm->getbyid($id)->row();
        $item   = $_POST['inp'];     
			// $messages = "[OPENLIBRARY]\nMohon maaf, permintaan peminjaman ruangan pada tanggal ".convert_format_date(substr($data->bk_startdate,0,10))." ".substr($data->bk_startdate,11,5)." - ".substr($data->bk_enddate,11,5)." tidak disetujui, karena ".$item['bk_reason'];

			$messages = "Mohon maaf, permintaan peminjaman ".$data->room_name." pada tanggal ".convert_format_date(substr($data->bk_startdate,0,10))." ".substr($data->bk_startdate,11,5)." - ".substr($data->bk_enddate,11,5)." tidak disetujui karena ".$data->bk_reason;
			
				
			$itemnotif['notif_id_member'] 	= $data->bk_username;
			$itemnotif['notif_type'] 	= 'ruangan';
			$itemnotif['notif_content'] 	= $messages;
			$itemnotif['notif_date'] 	= date('Y-m-d H:i:s', strtotime('7 hour'));
			$itemnotif['notif_status'] 	= 'unread';
			$itemnotif['notif_id_detail'] 	= $id;
			$notif_id = $this->bm->addNotificationMobile($itemnotif); 

			$token = $this->bm->getTokenNotificationMobile($data->bk_memberid)->row();

			$title = "Ruangan - Not Approved";  
			$notif_content = $messages;
			$notif_id_detail = $id;
			$notif_type = 'ruangan';
			$token = $token->master_data_token;

			NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token); 
			        
			// $msg = $this->splitByWords($messages);
			// foreach($msg as $row){
			// 	SendSms($data->bk_mobile_phone,$row);
			// }
		if ($this->bm->edit($id, $item)) echo json_encode(array("status" => TRUE));
    }
	
	public static function splitByWords($text, $splitLength = 15)
	{
			// explode the text into an array of words
			$wordArray = explode(' ', $text);

			// Too many words
			if( sizeof($wordArray) > $splitLength )
			{
				// Split words into two arrays
				$firstWordArray = array_slice($wordArray, 0, $splitLength);
				$lastWordArray = array_slice($wordArray, $splitLength+1, sizeof($wordArray));

				// Turn array back into two split strings 
				$firstString = implode(' ', $firstWordArray);
				$lastString = implode(' ', $lastWordArray);
				return array($firstString, $lastString);
			}
			// if our array is under the limit, just send it straight back
			return array($text);
	}  
}
