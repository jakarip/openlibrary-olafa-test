<?php

class InternationalOnline extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
		$this->load->model('Internationalonlinemodel'); 
		if(!$this->session->userdata('login')) redirect('');
    }

	
	
    function index() {   
		$data['menu'] 	= 'internationalonline/internationalonline'; 
		$this->load->view('theme', $data);
    }

    

    public function ajax_dt(){
		
      if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
      $table 		= "select * from internationalonline";
      $colOrder 	= array(null,'io_name','io_url',null); //set column field database for datatable orderable
      $colSearch 	= array('io_name','io_url'); //set column field database for datatable
      $order 		= "order by io_name asc"; // default order  
      
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
              $row[] = $dt->io_name;
              $row[] = '<a href="'.$dt->io_url.'" target="_blank">'.$dt->io_url.'</a>'; 
              $row[] = '<div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="'.getLang("edit").'" onclick="edit('."'".$dt->io_id."'".')"><i class="fa fa-pencil-square-o"></i></button></div>'; 
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
          $item 					= $_POST['inp'];  
      if ($this->Internationalonlinemodel->add($item)) echo json_encode(array("status" => FALSE));
      else echo json_encode(array("status" => TRUE));
    }
  
  
    function update(){
      if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
          $id     = $this->input->post('id');
          $item   = $_POST['inp'];  
       
      if ($this->Internationalonlinemodel->edit($id, $item)) echo json_encode(array("status" => FALSE));
      else echo json_encode(array("status" => TRUE));
    }
  
    function edit(){
      if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
          $id     = $this->input->post('id');
          $data 	= $this->Internationalonlinemodel->getbyid($id)->row(); 
          echo json_encode($data);
    }
}

?>