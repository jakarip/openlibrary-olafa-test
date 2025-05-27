  <?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bookings extends CI_Controller {

    public function __construct(){
        parent::__construct();
		
		if(!$this->session->userdata('login_room')) redirect(url_admin(), 'refresh'); 
        $this->load->model('RoomModel','rm');
        $this->load->model('BookingModel','bm');
    }

    public function bo($id=""){ 
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh'); 
        $data['menu']		= 'booking';
        $data['id']			= $id; 
		
		$data['room'] 		= $this->rm->getbyactiveid()->result();
		$holiday 			= $this->bm->holiday()->result_array();
		$dt = array();
		foreach ($holiday as $row){
			$dt[] = '"'.$row['holiday_date'].'"';
		}
		$data['holiday'] = implode(",",$dt);
		
        $this->load->view('theme',$data);
    }
	
	// public function mobilephone(){
		
		// if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		// $dt 				= $this->bm->getMobilePhone()->row();
		// if ($dt) echo $dt->bk_mobile_phone;
		// else echo '';
	// }
	
	public function json_event() {
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id = $_GET['id'];
		$startdate = $_GET['start'];
		$enddate = $_GET['end'];
		
		$data = $this->bm->getbyaprrovedid($id,$startdate,$enddate)->result();
		
		$html='['; 
		foreach($data as $row){
			$date 		= substr($row->bk_startdate,0,10);
			$starthour 	= substr($row->bk_startdate,11,5);
			$endhour 	= substr($row->bk_enddate,11,5);
			$dt 		= date("d M Y", strtotime($date));
			$header		= $dt." ".$starthour.' - '.$endhour;
			
			$member = $this->bm->getMember($row->bk_id)->result();
			$list = "";
			$no = 1;
			foreach ($member as $mem){
				$list.= $no.". &nbsp;&nbsp;".$mem->master_data_user." - ".$mem->master_data_fullname."<br>";
				$no++;
			}
			
			if ($row->bk_status=='Approved' || $row->bk_status=='Attended') {
				$color = "green";
				$status = '<button type=\"button\" class=\"btn btn-sm btn-success\">'.$row->bk_status.'</button>';
			}else if($row->bk_status=='Request') {
				$color = "#b80005";
				$status = '<button type=\"button\" class=\"btn btn-sm btn-danger\">'.$row->bk_status.'</button>';
			}
			$desc = '<table><tr><td width=\"100px\">'.getLang('name').'</td><td width=\"10px;\">:</td><td>'.$row->master_data_fullname.'</td></tr><tr><td>&nbsp;</td></tr><tr><td>Status</td><td>:</td><td>'.$status.'</td></tr><tr><td>&nbsp;</td></tr><tr><td style=\"vertical-align:top\">'.getLang('member_name').'</td><td style=\"vertical-align:top\">:</td><td>'.$list.'</td></tr><tr><td>&nbsp;</td></tr><tr><td>'.getLang('purpose').'</td><td>:</td><td>'.$row->bk_purpose.'</td></tr></table>';
			$html.='  { "start": "'.$row->bk_startdate.'", "end": "'.$row->bk_enddate.'" , "title": "'.$row->master_data_fullname.'", "header": "'.$header.'", "description": "'.$desc.'","color": "'.$color.'"},';
		}
		$html = rtrim($html, ",");
		$html.=']';
		
		echo $html;
		
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
	
	// public function json($id="",$limit="",$year="",$month="",$day="") {
		 
		// if ($month==false){
			// $data = $this->bm->getbyaprrovedid($id,date('Y-m'))->result();
		// }
		// else {
			// $month++;
			// $month = sprintf('%02d',$month);
			// $data = $this->bm->getbyaprrovedid($id,$year."-".$month)->result(); 
		// } 
		// $html='['; 
		// foreach($data as $row){
			// $html.='  { "date": "'.$row->bk_startdate.'", "enddate": "'.$row->bk_enddate.'" , "type": "meeting", "title": "'.$row->master_data_fullname.'", "description": "'.$row->bk_reason.'", "url": "" },';
		// }
		// $html = rtrim($html, ",");
		// $html.=']';
		
		// echo $html;
	// }

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
		
		
		$startdate 	= $dt." ".$starthour;
		$enddate	= $dt." ".$endhour;
		
		$count = $this->bm->checkBannedPerMonthStatus()->row();
		if ($count->total<'2') {
			$count = $this->bm->checkCountBookingRoomStatus()->row(); 
			if ($count->total<'2') {
				if(!$this->bm->checkExistBooking($startdate,$enddate,$item['bk_room_id'])->row()){
					$item['bk_username']	= $this->session->userdata('username');
					$item['bk_status']  	= 'Request';
					$item['bk_createdby']  	= $this->session->userdata('username');
					$item['bk_createdate'] 	= date("Y-m-d H:i:s"); 
					$item['bk_startdate']	= $startdate;
					$item['bk_enddate']		= $enddate;
					$item2['bm_bk_id'] = $this->bm->add($item);
					$data2 = explode(",",$member);
					foreach($data2 as $row){
						$item2['bm_username'] = $row;
						$this->bm->addBookingMember($item2);
					} 
					$messages 		= "[OPENLIBRARY]\n Anda telah melakukan permintaan peminjaman ruangan pada tanggal ".$date." ".$starthour." - ".$endhour.".\n Anda akan dikonfirmasi jika telah diproses.";
					$this->SmsLog($item['bk_mobile_phone'],$messages);
					$this->SmsLog('081280000110',"Ada Request Peminjaman Ruangan");
					echo json_encode(array("status" => TRUE));
				}
				else echo json_encode(array("error" => 'Ruangan yang dipilih untuk tanggal '.$date.' '.$starthour.' - '.$endhour.' sudah ada yang menggunakan. Silahkan memilih ruangan / jadwal yang lain'));
			}
			else echo json_encode(array("error" => 'Anda sudah melebihi jumlah permintaan peminjaman yang diperbolehkan. Maksimal hanya diperbolehkan 2x permintaan peminjaman ruangan. Silahkan menunggu admin untuk melakukan proses pada request jadwal peminjaman anda yang sebelumnya'));
		}
		else echo json_encode(array("error" => getLang('Anda tidak dapat melakukan peminjaman pada bulan ini, dikarenakan anda telah 2x melakukan peminjaman tetapi tidak hadir pada hari peminjaman.')));
    } 
	
	public function SmsLog($hp,$messages) {
		$url = "http://10.13.14.171/sendsms.php?appname=OPENLIBRARY&number=".$hp."&text=".urlencode($messages);
		file_get_contents($url);
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
