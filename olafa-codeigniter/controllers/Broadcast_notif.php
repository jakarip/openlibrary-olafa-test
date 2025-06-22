<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Broadcast_notif extends CI_Controller {

    public function __construct(){
        parent::__construct();
		
		if(!$this->session->userdata('login_room')) redirect(url_admin(), 'refresh'); 
        $this->load->model('Broadcast_notifModel','bm');
		$this->load->model('ApiMobileModel', 'api', TRUE); 
    }

    public function index(){ 

		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh'); 
        $data['menu']		= 'broadcast_notif'; 
        $this->load->view('theme',$data);
    }

    public function ajax_data(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$table 		= "select * from notification_broadcast";
		$colOrder 	= array(null,'nb_title','nb_content','nb_username','nb_datetime',null); //set column field database for datatable orderable
		$colSearch 	= array('nb_title','nb_content','nb_username','nb_datetime'); //set column field database for datatable
			$order 		= "order by nb_datetime desc"; // default order  
		
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
            $row[] = $dt->nb_title;
            $row[] = $dt->nb_content;
            $row[] = $dt->nb_username;
            $row[] = date("d M Y H:i:s", strtotime($dt->nb_datetime)); 
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
        $title = $this->input->post('title'); 
        $content = $this->input->post('content'); 
         
        $content = str_replace("<br />","",$content);
        // echo $content;

        $item['nb_title'] 		= $title;
        $item['nb_content'] 		= $content;
        $item['nb_datetime'] 		= date('Y-m-d H:i:s');
        $item['nb_username'] 		= $this->session->userdata('username');
 
        $this->bm->add($item);

           

        $member = $this->api->getMemberByUsername('yudhinugrohoadi')->row(); 
       

        $token = $this->api->getTokenNotificationMobile($member->id)->row();
       

        $notif_content = $content;
        $notif_id = '1';
        $notif_id_detail = '1';
        $notif_type = 'broadcast';
        $token = $token->master_data_token;
        // NotificationMobile($title,'1',$notif_content,$notif_id_detail,$notif_type,$token);  

        NotificationMobileBroadcast($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$title); 


       
		echo json_encode(array("status" => TRUE));
    } 

    function deletes(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
		//echo $id;
        $this->bm->delete($id);
        echo json_encode(array("status" => TRUE));
    }  
	 
}
