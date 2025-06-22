<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bds extends CI_Controller {

    public function __construct(){ 
        parent::__construct();
		
		if(!$this->session->userdata('login_room')) redirect(url_admin(), 'refresh'); 
        $this->load->model('BdsModel','bm'); 
    }

    public function index(){ 
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');  
        $data['menu']		= 'bds';
        $this->load->view('theme',$data);
    }

    public function ajax_data(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

		$status = $this->input->post("status");
		$where = "";
		if($status!="") $where = "where bds_status='$status'"; 
		
		// GROUP_CONCAT(concat(substr(title,1,50),' ...') SEPARATOR '<br><br> ') item_title,
		$person = ""; 
			$table 		= "select bds.*,master_data_user,master_data_fullname,
			GROUP_CONCAT(bdsb_item_code SEPARATOR '<br> ') item_code,
			GROUP_CONCAT(bdsb_item_code) item_code2,
			GROUP_CONCAT(bdsb_stock_code SEPARATOR '<br> ') stock_code,
			DATE_FORMAT(bds_createdate,'%Y-%m-%d') tanggal from telu8381_openlibrarys.book_delivery_service bds
			left join telu8381_openlibrarys.member on member.id=bds_idmember
			left join telu8381_openlibrarys.book_delivery_service_book book on bdsb_idbds=bds_id
			left join telu8381_openlibrarys.knowledge_item kit on kit.id=bdsb_item_id $where
			group by bds_id"; // default order    
		$order 		= "order by bds_createdate desc"; // default order   
		//			left join telu8381_openlibrarys.member_type on member_type_id = member_type.id 
		$colOrder 	= array(null,'tanggal','bds_number','master_data_number','master_data_fullname','bds_receiver','bds_address','bds_phone','judul','bds_photo_courier','bds_status','bds_reason',null); //set column field database for datatable orderable
		$colSearch 	= array('tanggal','bds_number','master_data_number','master_data_fullname','bds_receiver','bds_address','bds_phone','judul','bds_photo_courier','bds_status','bds_reason'); //set column field database for datatable
			 
		
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
            $row[] = convert_format_date($dt->tanggal);
            $row[] = $dt->bds_number; 
            $row[] = strtolower($dt->master_data_user);
            $row[] = ucwords(strtolower($dt->master_data_fullname));
            $row[] = $dt->bds_receiver; 
            $row[] = $dt->bds_address;
            $row[] = $dt->bds_phone;  
            $row[] = $dt->item_code;  
            $row[] = $dt->stock_code;   
            $row[] = ($dt->bds_photo_courier!=""?'<div style="cursor:pointer" onclick="pic('."'".$dt->bds_photo_courier."'".')"><img src="'.$dt->bds_photo_courier.'" width="150px";></div>':'');  
			$row[] = '<div class="btn-group"> <button type="button" class="btn btn-sm btn-default" title="'.getLang("History").'"onclick="history('."'".$dt->bds_id."'".')">History</button></div>';

			$items = explode(",",$dt->item_code2);
			
			if ($dt->bds_status=='Not Approved'){
				$row[] = '<div class="btn-group"><button type="button" class="btn btn-sm btn-info" title="'.$dt->bds_status.'">'.$dt->bds_status.'</button></div>';  
				$row[] = $dt->bds_reason;
				$row[] = "";
			}
			else if ($dt->bds_status=='Process'){
				$row[] = '<div class="btn-group"><button type="button" class="btn btn-sm btn-primary" title="'.$dt->bds_status.'">'.$dt->bds_status.'</button></div>';  
				$row[] = "-";
				$row[] = '<div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="'.getLang("Send").'"onclick="edit('."'".$dt->bds_id."','Send'".')">Send</button></div>';
			} 
			else if ($dt->bds_status=='Send'){
				$row[] = '<div class="btn-group"><button type="button" class="btn btn-sm btn-primary" title="'.$dt->bds_status.'">'.$dt->bds_status.'</button></div>';  
				$row[] = "-";
				$row[] = '<div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="'.getLang("Completed").'"onclick="edit_image('."'".$dt->bds_id."','Received'".')">Received</button></div>';
			} 
			else if ($dt->bds_status=='Received'){
				$row[] = '<div class="btn-group"><button type="button" class="btn btn-sm btn-primary" title="'.$dt->bds_status.'">'.$dt->bds_status.'</button></div>';  
				$row[] = "-";
				$row[] = '<div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="'.getLang("Completed").'"onclick="edit('."'".$dt->bds_id."','Completed'".')">Completed</button></div>';
			} 
			else if ( $dt->bds_status=='Completed'){
				$row[] = '<div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="'.$dt->bds_status.'">'.$dt->bds_status.'</button></div>';  
				$row[] = "-";
				$row[] = "";
			}
			else if ($dt->bds_status=='Approved'){
				$row[] = '<div class="btn-group"><button type="button" class="btn btn-sm btn-primary" title="'.$dt->bds_status.'">'.$dt->bds_status.'</button></div>'; 
				$row[] = "-"; 
				$row[] = '<div class="btn-group"> <button type="button" class="btn btn-sm btn-primary" title="'.getLang("Process").'"onclick="processed('."'".$dt->bds_id."','Process'".",'".$dt->item_code2."'".')">Process</button></div>'; 
			}
			else if ($dt->bds_status=='Request'){
				$row[] = '<div class="btn-group"><button type="button" class="btn btn-sm btn-warning" title="'.$dt->bds_status.'">'.$dt->bds_status.'</button></div>';
				$row[] = "-"; 
				$row[] = '<div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="'.getLang("Approved").'" onclick="edit('."'".$dt->bds_id."','Approved'".')">Approved</button></div><br><br><div class="btn-group"> <button type="button" class="btn btn-sm btn-info" title="'.getLang("Not Approved").'"onclick="not_approved('."'".$dt->bds_id."','Not Approved'".')">Not Approved</button></div>'; 
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
		if($item['bds_status']=='Approved') {
			$messages = "Permintaan peminjaman buku dengan no pesanan ".$data->bds_number." pada tanggal ".convert_format_date(substr($data->bds_createdate,0,10))." telah disetujui.";  
				
			$itemnotif['notif_id_member'] 	= $data->master_data_user;
			$itemnotif['notif_type'] 	= 'bds';
			$itemnotif['notif_content'] 	= $messages;
			$itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
			$itemnotif['notif_status'] 	= 'unread';
			$itemnotif['notif_id_detail'] 	= $id; 

			$notif_id = $this->bm->addNotificationMobile($itemnotif);

			$token = $this->bm->getTokenNotificationMobile($data->memberid)->row();

			$title = "Book Delivery Service - Approved";  
			$notif_content = $messages;
			$notif_id_detail = $id;
			$notif_type = 'bds';
			$token = $token->master_data_token;

			NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);    
		}   
		else if($item['bds_status']=='Send') {
			$messages = "Permintaan peminjaman buku dengan no pesanan ".$data->bds_number." pada tanggal ".convert_format_date(substr($data->bds_createdate,0,10))."  sedang dikirim ke alamat yang tertera.";
			// $messages = "\n\n*[OPENLIBRARY]*\nPermintaan peminjaman *".$data->room_name."* pada tanggal *".convert_format_date(substr($data->bk_startdate,0,10))." ".substr($data->bk_startdate,11,5)." - ".substr($data->bk_enddate,11,5)."* telah disetujui.\n\n";
			// SendWA($data->bk_mobile_phone,$messages);
			
				
			$itemnotif['notif_id_member'] 	= $data->master_data_user;
			$itemnotif['notif_type'] 	= 'bds';
			$itemnotif['notif_content'] 	= $messages;
			$itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
			$itemnotif['notif_status'] 	= 'unread';
			$itemnotif['notif_id_detail'] 	= $id; 
			$notif_id = $this->bm->addNotificationMobile($itemnotif);

			$token = $this->bm->getTokenNotificationMobile($data->memberid)->row();

			$title = "Book Delivery Service - Send";  
			$notif_content = $messages;
			$notif_id_detail = $id;
			$notif_type = 'bds';
			$token = $token->master_data_token;

			NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);   
		}  
		else if($item['bds_status']=='Completed') {
			$messages = "Proses pengiriman buku dengan no pesanan ".$data->bds_number." pada tanggal ".convert_format_date(substr($data->bds_createdate,0,10))." sudah selesai.";
			// $messages = "\n\n*[OPENLIBRARY]*\nPermintaan peminjaman *".$data->room_name."* pada tanggal *".convert_format_date(substr($data->bk_startdate,0,10))." ".substr($data->bk_startdate,11,5)." - ".substr($data->bk_enddate,11,5)."* telah disetujui.\n\n";
			// SendWA($data->bk_mobile_phone,$messages);
			
				
			$itemnotif['notif_id_member'] 	= $data->master_data_user;
			$itemnotif['notif_type'] 	= 'bds';
			$itemnotif['notif_content'] 	= $messages;
			$itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
			$itemnotif['notif_status'] 	= 'unread';
			$itemnotif['notif_id_detail'] 	= $id; 
			$notif_id = $this->bm->addNotificationMobile($itemnotif);

			$token = $this->bm->getTokenNotificationMobile($data->memberid)->row();

			$title = "Book Delivery Service - Completed";  
			$notif_content = $messages;
			$notif_id_detail = $id;
			$notif_type = 'bds';
			$token = $token->master_data_token;

			NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);   
		}  

		$item2['bdss_idbds'] = $id;
		$item2['bdss_date'] = $itemnotif['notif_date'];
		$item2['bdss_status'] = $item['bds_status'];
		$this->bm->addBdsStatus($item2);

		if ($this->bm->edit($id, $item)) echo json_encode(array("status" => TRUE));
    }  
	
	function not_approved(){ 
	   if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
	   $id     = $this->input->post('id');
	   $data 	= $this->bm->getbyid($id)->row();
	   $item   = $_POST['inp'];     
		   // $messages = "[OPENLIBRARY]\nMohon maaf, permintaan peminjaman ruangan pada tanggal ".convert_format_date(substr($data->bk_startdate,0,10))." ".substr($data->bk_startdate,11,5)." - ".substr($data->bk_enddate,11,5)." tidak disetujui, karena ".$item['bk_reason'];

		   // $messages = "Permintaan usulan bahan pustaka dengan judul ".$data->bp_title." pada tanggal ".convert_format_date(substr($data->bp_createdate,0,10))." telah tersedia di Tel-U Open Library."; 
		   $messages = "Mohon maaf, permintaan peminjaman buku dengan no pesanan ".$data->bds_number." pada tanggal ".convert_format_date(substr($data->bds_createdate,0,10))." tidak disetujui karena ".$item['bds_reason'];
		   
		   $itemnotif['notif_id_member'] 	= $data->master_data_user;
		   $itemnotif['notif_type'] 	= 'bds';
		   $itemnotif['notif_content'] 	= $messages;
		   $itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
		   $itemnotif['notif_status'] 	= 'unread';
		   $itemnotif['notif_id_detail'] 	= $id;
		   $notif_id = $this->bm->addNotificationMobile($itemnotif);

		   $token = $this->bm->getTokenNotificationMobile($data->memberid)->row();

		   $title = "Book Delivery Service - Not Approved";  
		   $notif_content = $messages;
		   $notif_id_detail = $id;
		   $notif_type = 'bds';
		   $token = $token->master_data_token;

		   NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);   

		   $item2['bdss_idbds'] = $id;
		   $item2['bdss_date'] = $itemnotif['notif_date'];
		   $item2['bdss_status'] = $item['bds_status'];
		   $this->bm->addBdsStatus($item2);
				   
		   // $msg = $this->splitByWords($messages);
		   // foreach($msg as $row){
		   // 	SendSms($data->bk_mobile_phone,$row);
		   // }
	   if ($this->bm->edit($id, $item)) echo json_encode(array("status" => TRUE));
   }  
  
   function processed(){ 
	if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
	$id     = $this->input->post('id');
	$data 	= $this->bm->getbyid($id)->row();
	$item   = $_POST['item'];    
	$inp   = $_POST['inp'];     
	$barcode   = $_POST['barcode'];  
	$barcode_id = array();  

	$error = "";
	foreach($barcode as $key=>$row){ 
		$code = $this->bm->checkEksemplar($item[$key],$row,$data->memberid)->row();
		if(!$code){
			$error = "Barcode yang anda masukkan tidak sesuai atau belum dilakukan proses peminjaman";
		}
		else $barcode_id[$key] = $code->id;
	}


	if($error==""){
		foreach($barcode as $key=>$row){  
			$temp['bdsb_stock_id'] = $barcode_id[$key];
			$temp['bdsb_stock_code'] = $row; 
			$this->bm->editBook($id,$item[$key],$temp);
		} 

		$messages = "Permintaan peminjaman buku dengan no pesanan ".$data->bds_number." pada tanggal ".convert_format_date(substr($data->bds_createdate,0,10))." sedang diproses.";
			
		$itemnotif['notif_id_member'] 	= $data->master_data_user;
		$itemnotif['notif_type'] 	= 'bds';
		$itemnotif['notif_content'] 	= $messages;
		$itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
		$itemnotif['notif_status'] 	= 'unread';
		$itemnotif['notif_id_detail'] 	= $id; 
		$notif_id = $this->bm->addNotificationMobile($itemnotif);

		$token = $this->bm->getTokenNotificationMobile($data->memberid)->row();

		$title = "Book Delivery Service - Process";  
		$notif_content = $messages;
		$notif_id_detail = $id;
		$notif_type = 'bds';
		$token = $token->master_data_token;

		NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);  

		$item2['bdss_idbds'] = $id;
		$item2['bdss_date'] = $itemnotif['notif_date'];
		$item2['bdss_status'] = $inp['bds_status'];
		$this->bm->addBdsStatus($item2);

		if ($this->bm->edit($id, $inp)) echo json_encode(array("status" => TRUE));

	}
	else {
		echo json_encode(array("status" => FALSE,"message"=>$error));
	}
	
}  
	
	public function received()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		if(!$this->input->post('inp')) return false;
		
		$id     = $this->input->post('id');
		$data 	= $this->bm->getbyid($id)->row();

		$item 			= $this->input->post('inp');  
		
		$uploaddir 		= 'tools/photo_courier/';
		$file 			= explode(".", $_FILES['image']['name']); 
		$ext 			= end($file);
		$newFile 		= round(microtime(true)).'.'.$ext;
		$uploadfile 	= $uploaddir . basename($newFile);
		move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile); 
		
		$item['bds_photo_courier'] 	= 'https://openlibrary.telkomuniversity.ac.id/room/tools/photo_courier/'.$newFile; 
		 
		$messages = "Permintaan peminjaman buku dengan no pesanan ".$data->bds_number." pada tanggal ".convert_format_date(substr($data->bds_createdate,0,10))." sudah diterima"; 
		$itemnotif['notif_id_member'] 	= $data->master_data_user;
		$itemnotif['notif_type'] 	= 'bds';
		$itemnotif['notif_content'] 	= $messages;
		$itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
		$itemnotif['notif_status'] 	= 'unread';
		$itemnotif['notif_id_detail'] 	= $id;
		$notif_id = $this->bm->addNotificationMobile($itemnotif);

		$token = $this->bm->getTokenNotificationMobile($data->memberid)->row();

		$title = "Book Delivery Service - Received";  
		$notif_content = $messages; 
		$notif_id_detail = $id;
		$notif_type = 'bds';
		$token = $token->master_data_token;
 
		NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);  

		$item2['bdss_idbds'] = $id;
		$item2['bdss_date'] = $itemnotif['notif_date'];
		$item2['bdss_status'] = $item['bds_status'];
		$this->bm->addBdsStatus($item2);
		

		if ($this->bm->edit($id, $item)) echo json_encode(array("status" => TRUE));
	} 

	function history(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
         
		$data = $this->bm->getBdsStatus($id)->result();
		$html = '<table width="100%"><tr><th width="50%">Tanggal</th><th width="50%">Status</th></tr>';
		foreach($data as $row){
			$html .= '<tr><td>'.$row->bdss_date.'</td><td>'.$row->bdss_status.'</td></tr>';
		}
		$html .= '</table>';


		echo $html;
    }

	public function export_excel(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

		$status = $this->input->post("status");
		$where = "";
		if($status!="") $where = "where bds_status='$status'";
		 
		$data = $this->bm->getDataExport($where)->result();
 
		
		$no = 0;
		$row[$no] = array('Tanggal','No Pesanan','Username','NIK/NIM','Nama','Penerima','Alamat','Telp','Total Buku','No. Katalog','Barcode','Status','Alasan Ditolak');
        // $no = $_POST['start'];
        foreach ($data as $key=>$dt){
			$no++;
			$row[$no] = array(convert_format_date($dt->tanggal),$dt->bds_number,$dt->master_data_user, ucwords(strtolower($dt->master_data_number)),ucwords(strtolower($dt->master_data_fullname)),$dt->bds_receiver,$dt->bds_address,$dt->bds_phone,$dt->total_buku,$dt->item_code,$dt->stock_code,$dt->bds_status,$dt->bds_reason); 
			
		}
		echo json_encode($row);  
    } 
}
