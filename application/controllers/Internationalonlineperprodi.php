<?php

class InternationalOnlinePerProdi extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
      $this->load->model('Internationalonlineperprodimodel'); 
      if(!$this->session->userdata('login')) redirect('');
    }

	
	
    function index() {   
		$data['menu'] 	= 'internationalonline/internationalonlineperprodi'; 
		$this->load->view('theme', $data);
    }

    

    public function ajax_dt(){
		
      if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
      $table 		= "select * from (select c_kode_prodi, nama_prodi,nama_fakultas,GROUP_CONCAT(concat(io_name,concat(' - ',io_url)) ORDER BY io_name ASC SEPARATOR ', <br><br>' ) journal
      from  t_mst_prodi tp 
			left join internationalonlineperprodi iop on tp.c_kode_prodi=iop.prodi_code
      left join internationalonline io on io.io_id=iop.io_id 
      left join t_mst_fakultas tf using(c_kode_fakultas) where (nama_prodi not like '%Pindahan%' and nama_prodi not like '%International%' and nama_prodi not like '%Internasional%') and nama_fakultas!=''
			group by c_kode_prodi
      order by nama_fakultas, nama_prodi)a";
      $colOrder 	= array(null,'nama_fakultas','nama_prodi','journal',null); //set column field database for datatable orderable
      $colSearch 	= array('nama_fakultas','nama_prodi','journal'); //set column field database for datatable
      $order 		= "order by nama_fakultas,nama_prodi"; // default order  
      
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
              $row[] = $dt->nama_fakultas;
              $row[] = $dt->nama_prodi;
              $row[] = $dt->journal;
              $row[] = '<div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="'.getLang("edit").'" onclick="edit('."'".$dt->c_kode_prodi."'".')"><i class="fa fa-pencil-square-o"></i></button></div>'; 
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

    public function ajax_list()
    {
      if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
      $id 				= $this->input->post("id"); 
      $study_program 		= $this->input->post("study_program");
      
      if ($study_program!="all") $where = "and prodi_code ='$study_program'";
      else $where = "";
      $table 		= "select io.* from internationalonlineperprodi iop 
      left join internationalonline io on io.io_id=iop.io_id $where";  
      $colOrder 	= array(null,'io_name','io_url'); //set column field database for datatable orderable
      $colSearch 	= array('io_name','io_url'); //set column field database for datatable
      $order 		= "order by io_name asc"; // default order 
      
      $this->datatables->set_table($table);
      $this->datatables->set_col_order($colOrder);
      $this->datatables->set_col_search($colSearch);
      $this->datatables->set_order($order);
      
      $list = $this->datatables->get_datatables();
      $data = array();
      $no = $_POST['start'];
      foreach ($list as $dt) {
        $no++;
        $row = array();
        $row[] = '<div class="text-center"><input type="checkbox" value="'.$dt->io_id.'" name="inp[id][]"></div>';
        $row[] = $dt->io_name;
        $row[] = $dt->io_url;
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
      
    public function ajax_not_list()
    {
      if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
      $id 				= $this->input->post("id");
      $curriculum_year 	= $this->input->post("curriculum_year");
      $study_program 		= $this->input->post("study_program");
      
      // if ($study_program!="all") $where = "and course_code ='$study_program'";
      // else $where = "";
      
      $table 		= "select * from internationalonline where io_id not in (select io_id from internationalonlineperprodi where prodi_code='$study_program') "; 
      $colOrder 	= array(null,'io_name','io_url'); //set column field database for datatable orderable
      $colSearch 	= array('io_name','io_url'); //set column field database for datatable
      $order 		= "order by io_name asc"; // default order 
      
      $this->datatables->set_table($table);
      $this->datatables->set_col_order($colOrder);
      $this->datatables->set_col_search($colSearch);
      $this->datatables->set_order($order);
      
      $list = $this->datatables->get_datatables();
      $data = array();
      $no = $_POST['start'];
      foreach ($list as $dt) {
        $no++;
        $row = array();
        $row[] = '<div class="text-center"><input type="checkbox" value="'.$dt->io_id.'" name="inp[id][]"></div>';
        $row[] = $dt->io_name;
        $row[] = $dt->io_url;
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
    
    public function insert_course()
    {
      if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
      $id = $this->input->post('id'); 
      $inp = $this->input->post('inp'); 
      foreach($inp['id'] as $row){
        $item['io_id']		= $row;
        $item['prodi_code']		= $id; 
        if(!$this->Internationalonlineperprodimodel->checkExisting($row,$id)->row()) $this->Internationalonlineperprodimodel->add($item);
      }
      echo json_encode(array("status" => TRUE));
    }
      
    public function delete_course()
    {
      if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
      $id = $this->input->post('id');
      $inp = $this->input->post('inp');
      
      foreach($inp['id'] as $row){
        $this->Internationalonlineperprodimodel->deletes($row,$id);
      }
      echo json_encode(array("status" => TRUE));
    }
}

?>