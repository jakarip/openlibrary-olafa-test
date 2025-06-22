<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Respond extends CI_Controller {

    public function __construct(){ 
        parent::__construct();
		
		if(!$this->session->userdata('login_room')) redirect(url_admin(), 'refresh'); 
        $this->load->model('BookingModel','bm');
        $this->load->model('QuitionerModel','rm');
    }

    public function index(){ 
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh'); 
		$data['quitioner'] 	= $this->rm->quitioner()->result();
        $data['menu']		= 'respond';
        $this->load->view('theme',$data);
    }

    public function ajax_data(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$person = "";
		
			$table 		= "select qr.*,master_data_fullname
					from telu8381_room.quitioner_responder qr
					left join telu8381_openlibrarys.member on member.id=qr.id";
			$order 	= "order by tanggal desc";  
			
		$colOrder 	= array(null,'tanggal','master_data_fullname','no23','no1','no2','no3','no4','no5','no6','no7','no8','no9','no10','no11','no12','no13','no14','no15','no16','no17','no18','no19','no20','no21','no22'); //set column field database for datatable orderable
		$colSearch 	= array('tanggal','master_data_fullname','no23','no1','no2','no3','no4','no5','no6','no7','no8','no9','no10','no11','no12','no13','no14','no15','no16','no17','no18','no19','no20','no21','no22'); //set column field database for datatable
			
		
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
            $row[] = $dt->tanggal; 
            $row[] = ucwords(strtolower($dt->master_data_fullname));
            $row[] = $dt->no23; 
            $row[] = $dt->no1; 
            $row[] = $dt->no2; 
            $row[] = $dt->no3; 
            $row[] = $dt->no4; 
            $row[] = $dt->no5; 
            $row[] = $dt->no6; 
            $row[] = $dt->no7; 
            $row[] = $dt->no8; 
            $row[] = $dt->no9; 
            $row[] = $dt->no10; 
            $row[] = $dt->no11; 
            $row[] = $dt->no12; 
            $row[] = $dt->no13; 
            $row[] = $dt->no14; 
            $row[] = $dt->no15; 
            $row[] = $dt->no16; 
            $row[] = $dt->no17; 
            $row[] = $dt->no18; 
            $row[] = $dt->no19; 
            $row[] = $dt->no20; 
            $row[] = $dt->no21; 
            $row[] = $dt->no22; 

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
	 
}
