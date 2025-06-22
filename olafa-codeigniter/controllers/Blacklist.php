<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blacklist extends CI_Controller {

    public function __construct(){
        parent::__construct();
		
		if(!$this->session->userdata('login_room')) redirect(url_admin(), 'refresh'); 
        $this->load->model('BlacklistModel','bm');
    }

    public function index(){ 

		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh'); 
        $data['menu']		= 'blacklist'; 
        $this->load->view('theme',$data);
    }

    public function ajax_data(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$table 		= "select bl_username,master_data_fullname,bl_date,bl_reason from telu8381_room.blacklist left join telu8381_openlibrarys.member on blacklist.bl_username=master_data_user group by bl_username,master_data_fullname";
		$colOrder 	= array(null,'bl_username','master_data_fullname','bl_date','bl_reason',null); //set column field database for datatable orderable
		$colSearch 	= array('bl_username','master_data_fullname','bl_date','bl_reason'); //set column field database for datatable
			$order 		= "order by bl_date,master_data_fullname asc"; // default order  
		
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
            $row[] = $dt->bl_username;
            $row[] = $dt->master_data_fullname;
            $row[] = date("d M Y", strtotime($dt->bl_date));
            $row[] = $dt->bl_reason;
			
            $row[] = '<div class="btn-group"><button type="button" class="btn btn-sm btn-danger" title="'.getLang("delete").'" onclick="del(\''.$dt->bl_username.'\')"><i class="fa fa-trash-o"></i></button></div>';
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
        $member 	= $this->input->post('member'); 
		$data2 = explode(",",$member);
		foreach($data2 as $row){
			$item['bl_username'] 	= $row;
			$item['bl_reason'] 		= $this->input->post('reason'); 
			$item['bl_date'] 		= convert_format_date($this->input->post('dates'));
			$this->bm->add($item);
		}  
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
