<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking extends CI_Controller {

    public function __construct(){
        parent::__construct();
		
		if(!$this->session->userdata('login_room')) redirect(url_admin(), 'refresh'); 
        $this->load->model('RoomModel','rm');
        $this->load->model('BookingModel','bm');
        $this->load->model('QuitionerModel','rms');
    } 
	 
	 public function valid_date($date, $format = 'd-m-Y'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
	}

    public function bo(){  	 
 
		//  echo "<pre>"; print_r($this->session->all_userdata()); echo "</pre>";
		// print_r($this->session->all_userdata());
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');  
		
		$this->bm->editRequestToCancel();
		
		$data['room'] 		= $this->rm->getbyactiveid()->result();
		$holiday 			= $this->bm->holiday()->result_array();
		$dts = array();
		foreach ($holiday as $row){
			$dts[] = '"'.$row['holiday_date'].'"';
		}
		$html="";
		$date = $this->input->post('date_choose');
		if (ISSET($date)=="") {
			$date = date('Y-m-d');
			$data['date_choose'] = date('d-m-Y');
		}
		else {
			if(!$this->valid_date($date)) {
				$date = date('Y-m-d');
				$data['date_choose'] = date('d-m-Y');
			}else {
				$data['date_choose'] = $date;
				$date = convert_format_date($date);
			} 
			
		}
		 
		$no=0;
		foreach($data['room'] as $row){
			$no++;
			$html.="'".$no."':{title:'".$row->room_name."<br>(Maks : &plusmn; ".$row->room_capacity." ".getLang('people').")<br><a style=\"font-weight:bold;cursor:pointer;color:#000\" href=\"javascript:void(0)\" onclick=\"gallery(\'".$row->room_id."\')\">".getLang('view_gallery')."</a>',schedule:[";
			
			$scd = $this->bm->getSchedule($row->room_id,$date)->result();
			foreach ($scd as $dt){
				$starthour 	= substr($dt->bk_startdate,11,5);
				$endhour 	= substr($dt->bk_enddate,11,5);  
				$html.="{start:'".$starthour."',end:'".$endhour."',text:'".$dt->bk_status."',data:{ id : ".$dt->bk_id."}},";
			}
			$html = rtrim($html, ",");
			$html.="]},";
		}
		$html = rtrim($html, ",");
		
		$data['schedule']	= $html; 
		$data['holiday'] 	= implode(",",$dts); 
		if ($this->session->userdata('user_id')=='1') $data['menu']		= 'booking_admin'; 
        else $data['menu']		= 'booking';  
      $this->load->view('theme',$data);
    }
	
	public function gallery($id){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		//$id = $_POST['id'];
		$data = $this->rm->getimagebyroomid($id)->result();
		$html="";
		foreach($data as $row){
			$html.='<a class="fancybox_gallery" rel="gallery1" href="tools/images/'.$row->rg_image.'" ></a>';
		}
		echo $html;
	}
	 	 
	public function detail() {
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id = $_POST['id'];
		
		$row = $this->bm->getBookingDetail($id)->row();
		 
			$date 		= substr($row->bk_startdate,0,10);
			$starthour 	= substr($row->bk_startdate,11,5);
			$endhour 	= substr($row->bk_enddate,11,5);
			$dt 		= date("d M Y", strtotime($date));
			$data['header'] 	= $dt." ".$starthour.' - '.$endhour;
			
			$member = $this->bm->getMember($row->bk_id)->result();
			$list = "";
			$no = 1;
			foreach ($member as $mem){
				$list.= $no.". &nbsp;&nbsp;".$mem->master_data_user." - ".$mem->master_data_fullname."<br>";
				$no++;
			}
			
			if ($row->bk_status=='Approved') {
				$status = '<button type="button" class="btn btn-sm btn-primary">'.$row->bk_status.'</button>';
			}else if($row->bk_status=='Request') { 
				$status = '<button type="button" class="btn btn-sm btn-warning">'.$row->bk_status.'</button>';
			}else if($row->bk_status=='Attend') { 
				$status = '<button type="button" class="btn btn-sm btn-success">'.$row->bk_status.'</button>';
			}
			$desc = '<table width="100%"><tr><td width="100px">'.getLang('name').'</td><td width="20px;">:</td><td><b>'.(($row->member_type_id=='1'  && $this->session->userdata('user_id')!='1')?'<span style="color:red">Admin Openlibrary</span>':$row->master_data_fullname).'</b></td></tr><tr><td>&nbsp;</td></tr><tr><td>Status</td><td>:</td><td>'.$status.'</td></tr><tr><td>&nbsp;</td></tr><tr><td style="vertical-align:top">'.getLang('member_name').'</td><td style="vertical-align:top">:</td><td>'.$list.'</td></tr><tr><td>&nbsp;</td></tr><tr><td>'.getLang('purpose').'</td><td>:</td><td>'.$row->bk_purpose.'</td></tr></table>'; 
			
			$data['desc']=$desc;
		
		
		echo json_encode($data); 
	} 
	
	function member(){
		$dt = $this->bm->member(strtolower($_GET['q']))->result_array();
		$arr = array();
		foreach ($dt as $row){
			$tab['id'] 	= $row['master_data_user'];
			$tab['name'] 	= $row['master_data_user']." - ".$row['master_data_fullname'];
			$arr[] = $tab;
			
		}
		echo json_encode($arr);
    } 
	 

    public function ajax_data(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$table 		= "select * from telu8381_room.room";
		$colOrder 	= array(null,'room_name','room_capacity','room_description','room_activate',null); //set column field database for datatable orderable
		$colSearch 	= array('room_name','room_capacity','room_description','room_activate'); //set column field database for datatable
			$order 		= "order by room_active,room_name asc"; // default order  
		
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
            $row[] = $dt->room_name;
            $row[] = $dt->room_capacity;
            $row[] = $dt->room_description; 
			
            $row[] = '<div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="'.getLang("edit").'" onclick="edit('."'".$dt->room_id."'".')"><i class="fa fa-pencil-square-o"></i></button> '.(($dt->room_active==0)?'<button type="button" class="btn btn-sm btn-danger" title="'.getLang("deactivate_data").'" onclick="del('."'1'".','."'".$dt->room_id."'".','."'".$dt->room_name."'".')"><i class="fa fa-unlink"></i></button>':'<button type="button" class="btn btn-sm btn-default" title="'.getLang("activate_data").'" onclick="del('."'0'".','."'".$dt->room_id."'".','."'".$dt->room_name."'".')"><i class="fa fa-link"></i></button>').'</div>';
            $row[] = $dt->room_active;
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

    function insert(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $item 							= $_POST['inp']; 
		//print_r($_POST);
		
		$date 		= $this->input->post('date');
		$duration 	= $this->input->post('duration');
		$member 	= $this->input->post('member'); 
		$starthour 	= $this->input->post('starthour'); 
		$dt 		= convert_format_date($date);
		
		$endhour 	= date('H:i', strtotime($starthour.' + '.$duration.' minutes') );
		
		if ($starthour=='all'){
			$starthour 	= '08:00';
			$endhour 	= $duration;
		}
		
		$startdate 	= $dt." ".$starthour;
		$enddate	= $dt." ".$endhour;
		
		$count = $this->bm->checkBannedPerMonthStatus()->row();
		$blacklist = $this->bm->getBlacklist()->row();
		if ($blacklist){
			echo json_encode(array("error" => getLang('Anda sedang di blacklist')));
		}
		else {
			 
			if (($count->total<'2' && $this->session->userdata('user_id')==2) or ($this->session->userdata('user_id')==1)) {
				$count = $this->bm->checkCountBookingRoomStatus()->row();  
				if (($count->total<'2' && $this->session->userdata('user_id')==2) or ($this->session->userdata('user_id')==1)) {
					if(!$this->bm->checkExistBooking($startdate,$enddate,$item['bk_room_id'])->row()){
						$data2 						= ($member!=""?explode(",",$member):array());
					 
						$room 						= $this->rm->getbyid($item['bk_room_id'])->row();
						
						 
						// if (($room->room_min <= (count($data2)+1) && $this->session->userdata('user_id')==2) or ($this->session->userdata('user_id')==1)) {
							$item['bk_username']		= $this->session->userdata('username');
							$item['bk_memberid']		= $this->session->userdata('memberid');
							$item['bk_mobile_phone']	= $this->session->userdata('phone');
							$item['bk_status']  		= 'Request';
							$item['bk_createdby']  		= $this->session->userdata('username');
							$item['bk_createdate'] 		= date("Y-m-d H:i:s"); 
							$item['bk_startdate']		= $startdate;
							$item['bk_enddate']			= $enddate;
							$data2 						= explode(",",$member);
							$item['bk_total']			= count($data2);
							$item2['bm_bk_id']			= $this->bm->add($item);
							$temp						= array();
							if($data2){
								foreach($data2 as $row){
									$mm = $this->bm->getMemberByUsername($row)->row();
									
									$temp[]					= $mm->id;
									$item2['bm_username'] 	= $row;
									$item2['bm_userid'] 	= $mm->id;
									$this->bm->addBookingMember($item2);
								} 
								$temp 						= implode(",",$temp);
								$temp 						= $this->bm->getListNameMember($temp)->row();
								$item3['bk_name']			= $temp->nama;
							}  
							
							$this->bm->edit($item2['bm_bk_id'],$item3);
							// $messages 		= "\n\n*[OPENLIBRARY]*\nAnda telah melakukan permintaan peminjaman ruangan.\nRuangan : *".$room->room_name."*\nTanggal : *".$date."*\nJam : *".$starthour." - ".$endhour."*\nAkan dikonfirmasi jika telah diproses\n\n";
							$messages 		= "Anda telah melakukan permintaan peminjaman ruangan ".$room->room_name." tanggal : ".$date." dan jam : ".$starthour." - ".$endhour.". Akan dikonfirmasi jika telah diproses.";
 
							$itemnotif['notif_id_member'] 	= $this->session->userdata('username');
							$itemnotif['notif_type'] 	= 'ruangan';
							$itemnotif['notif_content'] 	= $messages;
							$itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
							$itemnotif['notif_status'] 	= 'unread';
							$itemnotif['notif_id_detail'] 	= $item2['bm_bk_id'];

							$notif_id = $this->bm->addNotificationMobile($itemnotif);

							$token = $this->bm->getTokenNotificationMobile($this->session->userdata('memberid'))->row();

							$title = "Ruangan - Request";  
							$notif_content = $messages;
							$notif_id_detail = $item2['bm_bk_id'];
							$notif_type = 'ruangan';
							$token = $token->master_data_token;

							NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);   
							
							//notif admin ulan, gilang, zaky
							$token = $this->bm->getTokenNotificationMobile('12186')->row(); //ulan

							$title = "Ruangan - Request";  
							$notif_content = $this->session->userdata('username').' Request Peminjaman Ruangan';
							$notif_id_detail = $item2['bm_bk_id'];
							$notif_type = 'ruangan';
							$token = $token->master_data_token; 
							// NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token); 

							$token = $this->bm->getTokenNotificationMobile('123126')->row(); //gilang

							$title = "Ruangan - Request";  
							$notif_content = $this->session->userdata('username').' Request Peminjaman Ruangan';
							$notif_id_detail = $item2['bm_bk_id'];
							$notif_type = 'ruangan';
							$token = $token->master_data_token; 
							// NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token); 
							
							//notif admin
							$token = $this->bm->getTokenNotificationMobile('109765')->row(); //zaky

							$title = "Ruangan - Request";  
							$notif_content = $this->session->userdata('username').' Request Peminjaman Ruangan';
							$notif_id_detail = $item2['bm_bk_id'];
							$notif_type = 'ruangan';
							$token = $token->master_data_token; 
							// NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token); 

							
							// SendSms($item['bk_mobile_phone'],$messages);
							// SendSms('081280000110',"Ada Request Peminjaman Ruangan");
							// SendWA($item['bk_mobile_phone'],$messages);
							// SendWA('081280000110',"Ada Request Peminjaman Ruangan");
							// SendWA('085294741000',"Ada Request Peminjaman Ruangan");
							echo json_encode(array("status" => TRUE));
						// }
						// else echo json_encode(array("error" => 'Jumlah orang yang diinput ('.count($data2).' orang) tidak memenuhi jumlah minimum orang di ruangan '.$room->room_name.', yaitu '.$room->room_min.' orang'));
					}
					else echo json_encode(array("error" => 'Ruangan yang dipilih untuk tanggal '.$date.' '.$starthour.' - '.$endhour.' sudah ada yang menggunakan. Silahkan memilih ruangan / jadwal yang lain'));
				}
				else echo json_encode(array("error" => 'Anda sudah melebihi jumlah permintaan peminjaman yang diperbolehkan. Maksimal hanya diperbolehkan 2x permintaan peminjaman ruangan. Silahkan menunggu admin untuk melakukan proses pada request jadwal peminjaman anda yang sebelumnya'));
			}
			else echo json_encode(array("error" => getLang('Anda tidak dapat melakukan peminjaman pada bulan ini, dikarenakan anda telah 2x melakukan peminjaman tetapi tidak hadir pada hari peminjaman.')));
		}
    }  

    function update(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
        $item   = $_POST['inp'];    
		if ($this->bm->edit($id, $item)) echo json_encode(array("status" => TRUE));
    }

    function edit(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
        $data 	= $this->bm->getbyid($id)->row();
        echo json_encode($data);
    }

    function deletes(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
        $item	= $_POST['inp'];   
        $this->bm->edit($id, $item);
        echo json_encode(array("status" => TRUE));
    } 
}
?>