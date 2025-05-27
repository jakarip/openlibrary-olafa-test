<?php
ini_set('MAX_EXECUTION_TIME', -1);
ini_set('memory_limit','-1');

class Laporan extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
		$this->load->model('Laporanmodel');
		if(!$this->session->userdata('login')) redirect('');	
    }   
	
	
	function perminggu() {   
	// ini_set('display_errors', '1');
	// ini_set('display_startup_errors', '1');
	// error_reporting(E_ALL);
		
		$tglawal = "";
		$tglakhir = "";
		if (ISSET($_POST['report']) and ($_POST['reservation']!="") ){
				$data['reservation'] 	= $_POST['reservation'];  
				$data['prodi_value'] 	= $_POST['prodi'];  
				$tgl 	= explode(' to ',$_POST['reservation']);
				$start 	= explode('-',$tgl[0]);
				$end 	= explode('-',$tgl[1]);
				$tglawal 	= $start[2]."-".$start[1]."-".$start[0]." 00:00:00";
				$tglakhir 	= $end[2]."-".$end[1]."-".$end[0]." 23:59:59"; 	
		
			$data['report']['pengunjung'] = $this->Laporanmodel->pengunjung($tglawal,$tglakhir,$_POST['prodi'])->row()->total;
			$data['report']['peminjaman'] = $this->Laporanmodel->peminjaman($tglawal,$tglakhir,$_POST['prodi'])->row()->total;
			$data['report']['pengembalian'] = $this->Laporanmodel->pengembalian($tglawal,$tglakhir,$_POST['prodi'])->row()->total;
			$data['report']['bebaspustaka'] = $this->Laporanmodel->bebaspustaka($tglawal,$tglakhir,$_POST['prodi'])->row()->total;
			// $data['report']['ruangan'] = $this->Laporanmodel->ruangan($tglawal,$tglakhir,$_POST['prodi'])->row()->total;
		 
			$data['report']['tapa_readonly'] = $this->Laporanmodel->tapa_transaksi_readonly($tglakhir)->row();
			$data['report']['ebook_readonly'] = $this->Laporanmodel->ebook_transaksi_readonly($tglakhir)->row();  
			$data['report']['visitor_openlib'] = $this->Laporanmodel->visitor_openlib($tglakhir)->result(); 
			$data['report']['visitor_eproc'] = $this->Laporanmodel->visitor_eproc($tglakhir)->result(); 
		

			$data['report']['4'] = $this->Laporanmodel->tapa_based_on_bebaspustaka_date($tglawal,$tglakhir,'4',$_POST['prodi'])->row()->total;
			$data['report']['3'] = $this->Laporanmodel->tapa_based_on_bebaspustaka_date($tglawal,$tglakhir,'3',$_POST['prodi'])->row()->total;
			$data['report']['52'] = $this->Laporanmodel->tapa_based_on_bebaspustaka_date($tglawal,$tglakhir,'52',$_POST['prodi'])->row()->total;
			$data['report']['64'] = $this->Laporanmodel->tapa_based_on_bebaspustaka_date($tglawal,$tglakhir,'64',$_POST['prodi'])->row()->total;
			$data['report']['53'] = $this->Laporanmodel->tapa_based_on_bebaspustaka_date($tglawal,$tglakhir,'53',$_POST['prodi'])->row()->total;
			$data['report']['91'] = $this->Laporanmodel->tapa_based_on_bebaspustaka_date($tglawal,$tglakhir,'91',$_POST['prodi'])->row()->total;
		
			$data['sumbangan_buku'] = $this->Laporanmodel->sumbangan_buku($tglawal,$tglakhir,$_POST['prodi'])->result();
			$data['sumbangan_ebook'] = $this->Laporanmodel->sumbangan_ebook($tglawal,$tglakhir,$_POST['prodi'])->result();
			
				 
		}  
    	$data['prodi']		= $this->Laporanmodel->prodi()->result();
		$data['menu']		= 'laporan/perminggu';	
		
		$this->load->view('theme', $data);
    } 
	  

    public function ajax_json(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$table 		= "select * from free_letter f ";
		$colOrder 	= array(null,'letter_number','member_number','name','donated_item_title','donated_item_author', 'created_at',null); //set column field database for datatable orderable
		$colSearch 	= array('letter_number','member_number','name','donated_item_title','donated_item_author', 'created_at'); //set column field database for datatable
		$order 		= "order by created_at desc"; // default order  
		
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
            $row[] = $dt->letter_number;
            $row[] = $dt->member_number;
            $row[] = $dt->name;
            $row[] = $dt->donated_item_title;
            $row[] = $dt->donated_item_author; 
            $row[] = $dt->created_at; 
            $row[] = '<div class="btn-group"> <a type="button" class="btn btn-sm btn-success" title="'.getLang("print").'" target="_blank" href="/olafa/index.php/sbkpprint/prints/'.$dt->id.'"><i class="fa fa-print"></i></a></div>'; 
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
	
	function prints($id){
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');   
		$data['dt'] = $this->SbkpprintModel->sbkpprint($id)->row(); 
	
        $this->load->view('bebaspustaka/sbkpprint_print',$data); 
    }  
	
	function auto_data(){
		$dt = $this->SbkpprintModel->member(strtolower($_GET['q']))->result_array();
		$arr = array();
		foreach ($dt as $row){
			$tab['id'] 	= $row['master_data_user'];
			$tab['name'] 	= $row['master_data_user']." - ".$row['master_data_fullname']." (".$row['NAMA_PRODI'].")";
			$arr[] = $tab;
			
		}
		echo json_encode($arr);
    }  
	
	 function insert(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $item 					= $_POST['inp']; 
		
		$prodi 					= $this->SbkpprintModel->getProdiFak($item['member_number'])->row(); 
		$auto_inc 				= $this->SbkpprintModel->auto_inc()->row(); 
		if($prodi->sisa <= 0){ 
			$item['letter_number'] 		= $auto_inc->AUTO_INCREMENT.'/AKD26/PUS/'.date('Y');
			$item['created_at'] 		= date('Y-m-d H:i:s'); 
			$item['created_by'] 		= $this->session->userdata('username');  
			$item['course_code'] 		= ($prodi->master_data_course==""?0:$prodi->master_data_course);     
			$item['is_member'] 			= 1;   
			$item['registration_number'] = $prodi->id;  
			$item['name'] 				= $prodi->master_data_fullname;   
			$item2['status'] = '2';
			$this->SbkpprintModel->edit_member($prodi->id,$item2);
			if ($this->SbkpprintModel->add($item)) echo json_encode(array("status" => 'true'));
			else echo json_encode(array("status" => 'false'));
		}
		else echo json_encode(array("status" => 'denda'));
    }


    function update(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
        $item   = $_POST['inp'];  
		
		$item['datestart'] 		= convert_format_date($_POST['start']);
		$item['datefinish'] 		= convert_format_date($_POST['end']);
		$item['year'] 			= substr($_POST['end'],6,4);
		if ($this->SbkpprintModel->edit($id, $item)) echo json_encode(array("status" => FALSE));
		else echo json_encode(array("status" => TRUE));
    }

    function edit(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
        $data 	= $this->SbkpprintModel->getbyid($id)->row();
		$data->datestart		= convert_format_date($data->datestart);
		$data->datefinish 			= convert_format_date($data->datefinish);
        echo json_encode($data);
    }
	
	public function changestatus(){ 
		
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');  
        $data['menu']		= 'monitoringeproceeding/changestatus';
        $this->load->view('theme',$data);
    }
 
}

?>