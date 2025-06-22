<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class History extends CI_Controller {

    public function __construct(){ 
        parent::__construct();
		
		if(!$this->session->userdata('login_room')) redirect(url_admin(), 'refresh'); 
        $this->load->model('BookingModel','bm');
        $this->load->model('RoomModel','room');
        $this->load->model('QuitionerModel','rm');
    }

    public function index(){ 
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');  
		$data['room'] 		= $this->room->getbyactiveid()->result();
        $data['menu']		= 'history';
        $this->load->view('theme',$data);
    }

    public function ajax_data(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$person = ""; 

		$bk_room_id = $this->input->post("bk_room_id");
		$where = "";
		if($bk_room_id!="") $where = "and bk_room_id='$bk_room_id'";

		$startdate = date("Y-m-01", strtotime("-3 months"));
		$person 	= getLang('people');
		$table 		= "select *,DATE_FORMAT(bk_startdate,'%Y-%m-%d') tanggal,DATE_FORMAT(bk_startdate,'%H:%i') starthour,DATE_FORMAT(bk_enddate,'%H:%i') endhour, bk_total member_name 
				from telu8381_room.booking  
				left join telu8381_openlibrarys.member on member.id=bk_memberid 
				left join telu8381_room.room on bk_room_id=room_id where bk_status='Attend' and bk_startdate between '".$startdate."' and '".date('Y-m-d')." 23:59:59' $where";
		$order 		= "order by bk_status desc,bk_startdate desc"; // default order 
		
		//			left join telu8381_openlibrarys.member_type on member_type_id = member_type.id 
		$colOrder 	= array(null,'master_data_fullname','bk_mobile_phone','room_name','tanggal','bk_startdate','bk_enddate','bk_purpose','member_name','bk_status','bk_payment',null); //set column field database for datatable orderable
		$colSearch 	= array('master_data_fullname','bk_mobile_phone','room_name','tanggal','starthour','endhour','bk_purpose','member_name','bk_status','bk_payment'); //set column field database for datatable
			
		
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
			$row[] = '<div class="btn-group"><button type="button" class="btn btn-sm btn-info" title="'.$dt->bk_status.'">'.$dt->bk_status.'</button></div>';  
			$row[] = ($dt->bk_payment!=""?"Rp ".number_format($dt->bk_payment,0,',','.'):"");
			$row[] = '<div class="btn-group"><div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="'.getLang("Not Attend").'" onclick="payment('."'".$dt->bk_id."','Payment'".')">Payment</button></div>';
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

	public function export_excel(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

		$bk_room_id = $this->input->post("bk_room_id");
		$where = "";
		if($bk_room_id!="") $where = "and bk_room_id='$bk_room_id'";
		 
		$data = $this->bm->ExportRoom($where)->result();
 
		
		$no = 0;
		$row[$no] = array('Nama Pemesan','Nama Ruangan','Tanggal','Jam Mulai','Jam Selesai','Tujuan','Jumlah Anggota','Pembayaran');
        // $no = $_POST['start'];
        foreach ($data as $key=>$dt){
			$no++;
			$row[$no] = array(ucwords(strtolower($dt->master_data_fullname)),$dt->room_name,convert_format_date($dt->tanggal),$dt->starthour,$dt->endhour,$dt->bk_purpose,ucwords(strtolower($dt->member_name)),$dt->bk_payment); 
			 

		}
		echo json_encode($row); 
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
	
	 function payment(){ 
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
        $data 	= $this->bm->getbyid($id)->row();
        $item   = $_POST['inp'];      
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
