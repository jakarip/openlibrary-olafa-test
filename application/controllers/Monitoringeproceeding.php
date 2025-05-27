<?php
ini_set('MAX_EXECUTION_TIME', -1);
ini_set('memory_limit','-1');

class Monitoringeproceeding extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
		$this->load->model('MonitoringEproceedingModel');
		if(!$this->session->userdata('login')) redirect('');	
    } 

    function listeprocperprodi($edisi="") {
		$data = $this->MonitoringEproceedingModel->listeprocperprodi('','53','2015-08-16','2015-11-29')->result();
		
		foreach($data as $row){
			$editor ="";
			if (!empty($row->editor)) $editor = ucwords(strtolower(', '.$row->editor));
			echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />'.ucwords(strtolower($row->title)).'<br>'.ucwords(strtolower($row->master_data_fullname)).$editor."<br>";
		}
	}   
	
    function index($choose="",$type="",$faculty="") { 
		// if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');  
		$data['edition'] 	= $this->MonitoringEproceedingModel->getEprocEdition()->result();
		$data['list'] 		= $this->MonitoringEproceedingModel->getEprocList()->result();
		 
		$data['choose']  = 0; 
		$data['type']  	 = 0; 
		
		if (!empty($choose) && !empty($type)) {
			// $choose = $this->MonitoringEproceedingModel->getLastEprocEdition()->row();
			// $choose = $choose->eproc_edition_id;
			$data['choose'] = $choose;
			$data['type'] = $type;
			$data['faculty']  = $faculty;
			
			
			$list = $this->MonitoringEproceedingModel->getEprocListById($type)->row();
			$data['jurusan'] = $this->MonitoringEproceedingModel->getProdiByEprocList($faculty)->result();
			$edition		 = $this->MonitoringEproceedingModel->getEprocEditionById($choose)->row(); 
			$no=1; 
			foreach($data['jurusan'] as $row) {
				$data['tamasuk'][$no]					= $this->MonitoringEproceedingModel->totaltamasukbykodejur($row->c_kode_prodi,$edition->datestart,$edition->datefinish)->row()->total; 
				$data['jurnal'][$no]					= $this->MonitoringEproceedingModel->totaljurnalmasukbykodejur($row->c_kode_prodi,$edition->datestart,$edition->datefinish)->row()->total;
				
				$data['draft'][$no]						= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'22',$edition->datestart,$edition->datefinish)->row()->total;   
				$data['revision'][$no]					= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'2',$edition->datestart,$edition->datefinish)->row()->total; 
				$data['review'][$no]					= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'1',$edition->datestart,$edition->datefinish)->row()->total; 
				$data['archieved'][$no]					= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'5',$edition->datestart,$edition->datefinish)->row()->total; 
				$data['feasiblejurnal'][$no]			= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'3',$edition->datestart,$edition->datefinish)->row()->total; 
				$data['eksternal'][$no]					= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'52',$edition->datestart,$edition->datefinish)->row()->total;  
				$data['feasibleall'][$no]				= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'4',$edition->datestart,$edition->datefinish)->row()->total; 
				$data['loapending'][$no]				= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'64',$edition->datestart,$edition->datefinish)->row()->total; 
				$data['metadata'][$no]				= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'91',$edition->datestart,$edition->datefinish)->row()->total;  

				
				$data['archievedeksternal'][$no]		= $this->MonitoringEproceedingModel->totalarchivejurnalstatusbykodejur($row->c_kode_prodi,'52',$edition->datestart,$edition->datefinish)->row()->total; 
				$data['archievedloapending'][$no]				= $this->MonitoringEproceedingModel->totalarchivejurnalstatusbykodejur($row->c_kode_prodi,'64',$edition->datestart,$edition->datefinish)->row()->total;
				$data['archievedfeasible'][$no]			= $this->MonitoringEproceedingModel->totalarchivejurnalstatusbykodejur($row->c_kode_prodi,'3',$edition->datestart,$edition->datefinish)->row()->total;
				$data['archievedfeasibleall'][$no]		= $this->MonitoringEproceedingModel->totalarchivejurnalstatusbykodejur($row->c_kode_prodi,'4',$edition->datestart,$edition->datefinish)->row()->total;
				$data['archievedjurnalpublish'][$no]	= $this->MonitoringEproceedingModel->totalarchivejurnalstatusbykodejur($row->c_kode_prodi,'53',$edition->datestart,$edition->datefinish)->row()->total;
				$data['archievedmetadata'][$no]	= $this->MonitoringEproceedingModel->totalarchivejurnalstatusbykodejur($row->c_kode_prodi,'91',$edition->datestart,$edition->datefinish)->row()->total;
				
				
				$data['jurnalpublish'][$no]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'53',$edition->datestart,$edition->datefinish)->row()->total; 
				
				$no++;
			}  
			
		}
		$data['menu'] 	= 'monitoringeproceeding/monitoringeproceeding'; 
		$this->load->view('theme', $data);
    }
	
	public function edition(){ 
		
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');  
        $data['menu']		= 'monitoringeproceeding/edition';
        $this->load->view('theme',$data);
    }

    public function ajax_edition(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$table 		= "select * from journal_eproc_edition";
		$colOrder 	= array(null,'nama','datestart', 'datefinish',null); //set column field database for datatable orderable
		$colSearch 	= array('nama','datestart', 'datefinish'); //set column field database for datatable
		$order 		= "order by datestart desc"; // default order  
		
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
            $row[] = $dt->nama;
            $row[] = convert_format_date($dt->datestart);
            $row[] = convert_format_date($dt->datefinish); 
            $row[] = '<div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="'.getLang("edit").'" onclick="edit('."'".$dt->eproc_edition_id."'".')"><i class="fa fa-pencil-square-o"></i></button></div>'; 
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
		$item['datestart'] 		= convert_format_date($_POST['start']);
		$item['datefinish'] 		= convert_format_date($_POST['end']);
		$item['year'] 			= substr($_POST['end'],6,4);
		if ($this->MonitoringEproceedingModel->add($item)) echo json_encode(array("status" => FALSE));
		else echo json_encode(array("status" => TRUE));
    }


    function update(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
        $item   = $_POST['inp'];  
		
		$item['datestart'] 		= convert_format_date($_POST['start']);
		$item['datefinish'] 		= convert_format_date($_POST['end']);
		$item['year'] 			= substr($_POST['end'],6,4);
		if ($this->MonitoringEproceedingModel->edit($id, $item)) echo json_encode(array("status" => FALSE));
		else echo json_encode(array("status" => TRUE));
    }

    function edit(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
        $data 	= $this->MonitoringEproceedingModel->getbyid($id)->row();
		$data->datestart		= convert_format_date($data->datestart);
		$data->datefinish 			= convert_format_date($data->datefinish);
        echo json_encode($data);
    }
	
	public function changestatus(){ 
		
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');  
        $data['menu']		= 'monitoringeproceeding/changestatus';
        $this->load->view('theme',$data);
    }

    public function ajax_change(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$table 		= "select wdd.id wddid,wrs.name,master_data_user, ki.code,ki.id, 
							wdd.title,master_data_fullname,editor,state_id,latest_state_id,wdd.updated_by
							from workflow_document wdd  
							left join knowledge_item ki on ki.title=wdd.title
							left join workflow_state_sort_id wss on wss.id_state=wdd.latest_state_id  
							left join member m on wdd.member_id=m.id 
							left join workflow_document_state wds on wds.document_id=wdd.id
							left join workflow_state wrs on wrs.id=wds.state_id
							left join t_mst_prodi tp on C_KODE_PRODI=m.master_data_course
							left join t_mst_fakultas tf on tf.C_KODE_FAKULTAS=tp.C_KODE_FAKULTAS
							where     wdd.workflow_id='1' and state_id in (4,3,52,53,64,91) and latest_state_id in (3,4,5,52,53,64,91)
							  AND wds.id = (  
			SELECT
				max(id)
			FROM
				workflow_document_state  
			WHERE
				document_id = wdd.id and state_id!='5'
				)
							 group by wdd.member_id order by state_id";
		$colOrder 	= array(null,'master_data_user','master_data_fullname','code','title', 'editor', 'name', null); //set column field database for datatable orderable
		$colSearch 	= array('master_data_user','master_data_fullname','code','title', 'editor','name'); //set column field database for datatable
		$order 		= "order by wddid desc"; // default order  
		
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
            $row[] = $dt->master_data_user;
            $row[] = $dt->master_data_fullname;
            $row[] = $dt->code;
            $row[] = $dt->title;
            $row[] = $dt->editor; 
            $row[] = $dt->name; 
			
			if ($dt->state_id=='4'){
              $row[] = '<div class="btn-group">
			  <button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & No Publish Proceedings ( Not Feasible )" onclick="status('."'".$dt->wddid."','3','".$dt->code."'".')">Approved For Catalog & No Publish Proceedings ( Not Feasible )</button></div><br><br>
			 <div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )" onclick="status('."'".$dt->wddid."','52','".$dt->code."'".')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )</button></div><br><br>
			  <div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & Journal Publish Tel-U Proceedings" onclick="status('."'".$dt->wddid."','53','".$dt->code."'".')">Approved For Catalog & Journal Publish Tel-U Proceedings</button>
			  </div><br><br>
			  <div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & Journal Publish Tel-U Proceedings" onclick="status('."'".$dt->wddid."','64','".$dt->code."'".')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal - LoA Pending )</button>
			  </div><br><br>
			  <div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="Metadata Approve for Catalog & Journal Publish External" onclick="status('."'".$dt->wddid."','91','".$dt->code."'".')">Metadata Approve for Catalog & Journal Publish External</button>
			  </div>'; 
			}
			else 	if ($dt->state_id=='3'){
				 $row[] = '<div class="btn-group">
			 <button type="button" class="btn btn-sm btn-success" title="Document Not Feasible" onclick="status('."'".$dt->wddid."','4','".$dt->code."'".')">Document Not Feasible</button></div><br><br>
			 <div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )" onclick="status('."'".$dt->wddid."','52','".$dt->code."'".')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )</button></div><br><br>
			  <div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & Journal Publish Tel-U Proceedings" onclick="status('."'".$dt->wddid."','53','".$dt->code."'".')">Approved For Catalog & Journal Publish Tel-U Proceedings</button>
			  </div><br><br>
			  <div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & Journal Publish Tel-U Proceedings" onclick="status('."'".$dt->wddid."','64','".$dt->code."'".')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal - LoA Pending )</button>
			  </div><br><br>
			  <div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="Metadata Approve for Catalog & Journal Publish External" onclick="status('."'".$dt->wddid."','91','".$dt->code."'".')">Metadata Approve for Catalog & Journal Publish External</button>
			  </div>'; 
			}
			else 	if ($dt->state_id=='52'){
				 $row[] = '<div class="btn-group">
			  <button type="button" class="btn btn-sm btn-success" title="Document Not Feasible" onclick="status('."'".$dt->wddid."','4','".$dt->code."'".')">Document Not Feasible</button></div><br><br>
			 <div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & No Publish Proceedings ( Not Feasible )" onclick="status('."'".$dt->wddid."','3','".$dt->code."'".')">Approved For Catalog & No Publish Proceedings ( Not Feasible )</button></div><br><br>
			  <div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & Journal Publish Tel-U Proceedings" onclick="status('."'".$dt->wddid."','53','".$dt->code."'".')">Approved For Catalog & Journal Publish Tel-U Proceedings</button>
			  </div><br><br>
			  <div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & Journal Publish Tel-U Proceedings" onclick="status('."'".$dt->wddid."','64','".$dt->code."'".')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal - LoA Pending )</button>
			  </div><br><br>
			  <div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="Metadata Approve for Catalog & Journal Publish External" onclick="status('."'".$dt->wddid."','91','".$dt->code."'".')">Metadata Approve for Catalog & Journal Publish External</button>
			  </div>'; 
			}
			else 	if ($dt->state_id=='53'){
				 $row[] = '<div class="btn-group">
			  <button type="button" class="btn btn-sm btn-success" title="Document Not Feasible" onclick="status('."'".$dt->wddid."','4','".$dt->code."'".')">Document Not Feasible</button></div><br><br>
			  <div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & No Publish Proceedings ( Not Feasible )" onclick="status('."'".$dt->wddid."','3','".$dt->code."'".')">Approved For Catalog & No Publish Proceedings ( Not Feasible )</button></div><br><br>
			 <div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )" onclick="status('."'".$dt->wddid."','52','".$dt->code."'".')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )</button>
			  </div><br><br>
			  <div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & Journal Publish Tel-U Proceedings" onclick="status('."'".$dt->wddid."','64','".$dt->code."'".')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal - LoA Pending )</button>
			  </div><br><br>
			  <div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="Metadata Approve for Catalog & Journal Publish External" onclick="status('."'".$dt->wddid."','91','".$dt->code."'".')">Metadata Approve for Catalog & Journal Publish External</button>
			  </div>'; 
			}
			else 	if ($dt->state_id=='64'){
				 $row[] = '<div class="btn-group">
			  <button type="button" class="btn btn-sm btn-success" title="Document Not Feasible" onclick="status('."'".$dt->wddid."','4','".$dt->code."'".')">Document Not Feasible</button></div><br><br>
			  <div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & No Publish Proceedings ( Not Feasible )" onclick="status('."'".$dt->wddid."','3','".$dt->code."'".')">Approved For Catalog & No Publish Proceedings ( Not Feasible )</button></div><br><br>
			 <div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )" onclick="status('."'".$dt->wddid."','52','".$dt->code."'".')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )</button>
			  </div><br><br>
			  <div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & Journal Publish Tel-U Proceedings" onclick="status('."'".$dt->wddid."','53','".$dt->code."'".')">Approved For Catalog & Journal Publish Tel-U Proceedings</button>
			  </div><br><br>
			  <div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="Metadata Approve for Catalog & Journal Publish External" onclick="status('."'".$dt->wddid."','91','".$dt->code."'".')">Metadata Approve for Catalog & Journal Publish External</button>
			  </div>'; 
			}
			else if ($dt->state_id=='91'){
				 $row[] = '<div class="btn-group">
			  <button type="button" class="btn btn-sm btn-success" title="Document Not Feasible" onclick="status('."'".$dt->wddid."','4','".$dt->code."'".')">Document Not Feasible</button></div><br><br>
			  <div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & No Publish Proceedings ( Not Feasible )" onclick="status('."'".$dt->wddid."','3','".$dt->code."'".')">Approved For Catalog & No Publish Proceedings ( Not Feasible )</button></div><br><br>
			 <div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )" onclick="status('."'".$dt->wddid."','52','".$dt->code."'".')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )</button>
			  </div><br><br>
			  <div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & Journal Publish Tel-U Proceedings" onclick="status('."'".$dt->wddid."','53','".$dt->code."'".')">Approved For Catalog & Journal Publish Tel-U Proceedings</button>
			  </div><br><br>
			  <div class="btn-group"><button type="button" class="btn btn-sm btn-success" title="Approved For Catalog & Journal Publish Tel-U Proceedings" onclick="status('."'".$dt->wddid."','64','".$dt->code."'".')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal - LoA Pending )</button>
			  </div>'; 
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
	
	function status(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 		= $this->input->post('id');
		$code 		= $this->input->post('code');
		$status 	= $this->input->post('status');
		$document 	= $this->MonitoringEproceedingModel->getDocument($id)->row();
		
		$jurnal 			= $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$code.'/'.$code.'_jurnal.pdf';
		$eproc 				= $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$code.'/'.$code.'_jurnal_eproc.pdf';
		$bab1 				= $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$code.'/'.$code.'_bab1.pdf';
		$bab2 				= $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$code.'/'.$code.'_bab2.pdf';
		$bab3 				= $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$code.'/'.$code.'_bab3.pdf';
		$bab4 				= $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$code.'/'.$code.'_bab4.pdf';
		$bab5 				= $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$code.'/'.$code.'_bab5.pdf';
		$bab6 				= $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$code.'/'.$code.'_bab6.pdf';
		$jurnaldocx 		= $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$code.'/'.$code.'_jurnal.docx';
		
		if ($status=='53'){
			if ($code!=""){
				if (file_exists($jurnal)){
					copy($jurnal, $eproc);
				}
			}
		}
		else if ($status=='52' || $status=='51' || $status=='4' || $status=='3' || $status=='64' || $status=='91'){
			if ($code!=""){ 
				if (file_exists($eproc)){
					unlink($eproc);
				}

				if (file_exists($jurnal)){
					unlink($jurnal);
				}

				if (file_exists($jurnaldocx)){
					unlink($jurnaldocx);
				}

				if($status=='91'){
					
					if (file_exists($bab1)){
						unlink($bab1);
					}
					
					if (file_exists($bab2)){
						unlink($bab2);
					}
					
					if (file_exists($bab3)){
						unlink($bab3);
					}
					
					if (file_exists($bab4)){
						unlink($bab4);
					}
					
					if (file_exists($bab5)){
						unlink($bab5);
					}
					
					if (file_exists($bab6)){
						unlink($bab6);
					}
				}
			}
		}
		
		$this->MonitoringEproceedingModel->updateDocument($id,$status,$document->latest_state_id);
		
		// echo json_encode(array("status" => TRUE));
    }
	
	function duplicate_eproc($edisi="") { 
		 
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');  
		$data['editions'] = $this->MonitoringEproceedingModel->getEprocEdition()->result();
		
		$choose 		 = $this->input->post('choose');
		$data['choose']  = $choose; 
		if (empty($choose) and $edisi=="") {
			$choose = $this->MonitoringEproceedingModel->getLastEprocEdition()->row();
			$choose = $choose->eproc_edition_id;
			$data['choose'] = $choose;
			$edisi 			= $choose;
		}
		else if($edisi!="" and empty($choose)){
			$data['choose'] = $choose;
			$choose 		= $edisi;
		}
		
		$edition		 = $this->MonitoringEproceedingModel->getEprocEditionById($choose)->row();  
		$data['edition']	= $edition;
		$data['data']		= $this->MonitoringEproceedingModel->getarchivejurnalstatusbykodejur('','53',$edition->datestart,$edition->datefinish)->result(); 
		$data['total']		= $this->MonitoringEproceedingModel->totalarchivejurnalstatusbykodejur('','53',$edition->datestart,$edition->datefinish)->row()->total;
		$data['duplicate']  = 0;
		foreach ($data['data'] as $dt) {
			$eproc   	 		= '../../../../../../../data/batik/symfony_projects/book/'.$dt->code.'/'.$dt->code.'_jurnal_eproc.pdf';
			$data['action'][$dt->code] 	= "";
			
			if (file_exists($eproc)){
				$data['status'][$dt->code] = '<button type="button" class="btn btn-primary" style="background-color:#1A82C3;color:#fff;">Duplicated</button>';
				$data['duplicate']++;
			}
			else  {
				$data['status'][$dt->code] = '<button type="button" class="btn btn-primary"  style="background-color:#C22E32 ;color:#fff; border:1px solid #C22E32">Not Duplicate</button>';
				$eproc   	 = '../../../../../../../data/batik/symfony_projects/book/'.$dt->code.'/'.$dt->code.'_jurnal.pdf';
				if (file_exists($eproc)){
					$data['action'][$dt->code] = '<button type="button" onclick="window.open(\''.base_url().'index.php/monitoringeproceeding/duplicate/'.$dt->id.'/'.$choose.'\',\'_self\')" class="btn btn-primary" style="background-color:#4CAE4C;color:#fff;border:1px solid #4CAE4C">Duplicate</button>';
				}
			}
		} 
		$data['detail'] 	= '';
		$data['site'] 		= "";
		$data['menu'] 	= 'monitoringeproceeding/duplicate_eproc'; 
		$this->load->view('theme', $data);
    }
	
	// function year() { 
		// $data['edition'] = $this->MonitoringEproceedingModel->getEprocEditionYear()->result();
		
		// $choose 		 = $this->input->post('choose');
		// $data['choose']  = $choose; 
		// if (empty($choose)) {
			// $choose = $this->MonitoringEproceedingModel->getLastEprocEditionYear()->row();
			// $choose = $choose->year;
			// $data['choose'] = $choose;
		// }
		
		// $data['jurusan'] = $this->MonitoringEproceedingModel->getallProdi()->result();
		// $eproc = $this->MonitoringEproceedingModel->getLastEprocYear($choose)->result();
		
			// foreach($eproc as $dt) {
			// $edition		 = $this->MonitoringEproceedingModel->getEprocEditionById($choose)->row(); 
			// $no=1;
			// foreach($data['jurusan'] as $row) {
				// $data['tamasuk'][$no]		= $this->MonitoringEproceedingModel->totaltamasukbykodejur($row->c_kode_prodi,$edition->datestart,$edition->datefinish)->row()->total; 
				// $data['jurnal'][$no]		= $this->MonitoringEproceedingModel->totaljurnalmasukbykodejur($row->c_kode_prodi,$edition->datestart,$edition->datefinish)->row()->total;
				// $data['draft'][$no]			= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'22',$edition->datestart,$edition->datefinish)->row()->total;   
				// $data['revision'][$no]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'2',$edition->datestart,$edition->datefinish)->row()->total; 
				// $data['review'][$no]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'1',$edition->datestart,$edition->datefinish)->row()->total; 
				// $data['archieved'][$no]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'5',$edition->datestart,$edition->datefinish)->row()->total; 
				// $data['feasiblejurnal'][$no]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'3',$edition->datestart,$edition->datefinish)->row()->total; 
				// $data['eksternal'][$no]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'52',$edition->datestart,$edition->datefinish)->row()->total;  
				// $data['feasibleall'][$no]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'4',$edition->datestart,$edition->datefinish)->row()->total; 
				// $data['archievedeksternal'][$no]		= $this->MonitoringEproceedingModel->totalarchivejurnalstatusbykodejur($row->c_kode_prodi,'52',$edition->datestart,$edition->datefinish)->row()->total; 
				// $data['archievedfeasible'][$no]		= $this->MonitoringEproceedingModel->totalarchivejurnalstatusbykodejur($row->c_kode_prodi,'3',$edition->datestart,$edition->datefinish)->row()->total;
				// $data['archievedjurnalpublish'][$no]	= $this->MonitoringEproceedingModel->totalarchivejurnalstatusbykodejur($row->c_kode_prodi,'53',$edition->datestart,$edition->datefinish)->row()->total;
				
				// if($choose<=4)	
					// $data['jurnalpublish'][$no]		= $this->MonitoringEproceedingModel->totaljurnalpublishbykodejur($row->c_kode_prodi,$edition->datestart,$edition->datefinish)->row()->total; 			
				// else 
					// $data['jurnalpublish'][$no]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'53',$edition->datestart,$edition->datefinish)->row()->total; 
				// $no++;
				
				
				
			// } 
		// }
		// $data['menu'] 	= 'monitoringeproceeding/monitoringeproceeding'; 
		// $this->load->view('theme', $data);
    // }

    function ta($jurusan,$edisi) { 
		$edition			= $this->MonitoringEproceedingModel->getEprocEditionById($edisi)->row(); 
		$data['edition']	= $edition;
		$data['data'] 		= $this->MonitoringEproceedingModel->gettamasukbykodejur($jurusan,$edition->datestart,$edition->datefinish)->result();
		
		$data['total']		= $this->MonitoringEproceedingModel->totaltamasukbykodejur($jurusan,$edition->datestart,$edition->datefinish)->row()->total; 
		$data['jurusan']	= $this->MonitoringEproceedingModel->getjurbykodejur($jurusan)->row(); 
		$data['detail'] 	= 'ta'; 
		$data['site'] 		= "";
		$data['menu'] 	= 'monitoringeproceeding/monitoringeproceeding_detail_ta'; 
		$this->load->view('theme', $data);
    } 
	
	function jurnal($jurusan,$edisi) { 
		$edition			= $this->MonitoringEproceedingModel->getEprocEditionById($edisi)->row(); 
		$data['edition']	= $edition;
		$data['data']		= $this->MonitoringEproceedingModel->getjurnalmasukbykodejur($jurusan,$edition->datestart,$edition->datefinish)->result(); 
		$data['total']		= $this->MonitoringEproceedingModel->totaljurnalmasukbykodejur($jurusan,$edition->datestart,$edition->datefinish)->row()->total;
		$data['jurusan']	= $this->MonitoringEproceedingModel->getjurbykodejur($jurusan)->row(); 
		$data['detail'] 	= '';
		$data['site'] 		= '';
		$data['menu'] 	= 'monitoringeproceeding/monitoringeproceeding_detail_jurnal'; 
		$this->load->view('theme', $data);
    }
	
	function doc($id,$jurusan,$edisi) { 
		$edition			= $this->MonitoringEproceedingModel->getEprocEditionById($edisi)->row(); 
		$data['edition']	= $edition;
		
		if ($id=="5"){
			$data['data']		= $this->MonitoringEproceedingModel->getarchivejurnalstatusbykodejur($jurusan,$id,$edition->datestart,$edition->datefinish)->result(); 
		}
		else {
			$data['data'] 		= $this->MonitoringEproceedingModel->getdocbykodejurandstate($jurusan,$id,$edition->datestart,$edition->datefinish)->result();
		} 
		
		$data['detail'] 	= ''; 
		if($id=='52' || $id=='64' || $id=='3' || $id=='4' || $id=='91') $data['detail'] 	= 'publish';
		$data['id'] = $id;
		$data['site'] 		= $this->MonitoringEproceedingModel->getState($id)->row()->name;
		$data['total']		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($jurusan,$id,$edition->datestart,$edition->datefinish)->row()->total; 
		$data['jurusan']	= $this->MonitoringEproceedingModel->getjurbykodejur($jurusan)->row();
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		$data['menu'] 	= 'monitoringeproceeding/monitoringeproceeding_detail_doc'; 
		$this->load->view('theme', $data);
    }
	 
	function archieved($id,$jurusan,$edisi) {
		if ($id=="3" or $id=="52" or $id=="91" or $id=="64" or $id=="4"){
			$edition			= $this->MonitoringEproceedingModel->getEprocEditionById($edisi)->row(); 
			$data['edition']	= $edition;
			$data['data']		= $this->MonitoringEproceedingModel->getarchivejurnalstatusbykodejur($jurusan,$id,$edition->datestart,$edition->datefinish)->result(); 
			$data['total']		= $this->MonitoringEproceedingModel->totalarchivejurnalstatusbykodejur($jurusan,$id,$edition->datestart,$edition->datefinish)->row()->total;
			$data['jurusan']	= $this->MonitoringEproceedingModel->getjurbykodejur($jurusan)->row(); 
			$data['site'] 		= 'Archieved '.$this->MonitoringEproceedingModel->getState($id)->row()->name;
			$data['detail'] 	= ''; 
			$data['menu'] 	= 'monitoringeproceeding/monitoringeproceeding_detail_archieved'; 
			$this->load->view('theme', $data);
		}
		// else redirect('monitoringeproceeding');
    }
	
	function archievedjournalpublish($jurusan,$edisi) {
		$edition			= $this->MonitoringEproceedingModel->getEprocEditionById($edisi)->row(); 
		$data['edition']	= $edition;
		$data['data']		= $this->MonitoringEproceedingModel->getarchivejurnalstatusbykodejur($jurusan,'53',$edition->datestart,$edition->datefinish)->result(); 
		$data['total']		= $this->MonitoringEproceedingModel->totalarchivejurnalstatusbykodejur($jurusan,'53',$edition->datestart,$edition->datefinish)->row()->total;
		$data['jurusan']	= $this->MonitoringEproceedingModel->getjurbykodejur($jurusan)->row();
		$data['duplicate']  = 0;
		foreach ($data['data'] as $dt) {
			$eproc   	 		= $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$dt->code.'/'.$dt->code.'_jurnal_eproc.pdf';
			$data['action'][$dt->code] 	= "";
			
			if (file_exists($eproc)){
				$data['status'][$dt->code] = '<button type="button" class="btn btn-primary" style="background-color:#1A82C3;color:#fff;">Sudah Ada Jurnal Eproc</button>';
				$data['duplicate']++;
			}
			else  {
				$data['status'][$dt->code] = '<button type="button" class="btn btn-primary"  style="background-color:#C22E32 ;color:#fff; border:1px solid #C22E32">Belum Ada Jurnal Eproc</button>';
				$eproc   	 = $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$dt->code.'/'.$dt->code.'_jurnal.pdf';
			}
		}
		$data['site'] 		= 'monitoring e-proceeding / TA/PA/Thesis Archived Publish Tel-U Proceeding'; 
		$data['detail'] 	= '';
		$data['menu'] 	= 'monitoringeproceeding/monitoringeproceeding_detail_archievedjournalpublish'; 
		$this->load->view('theme', $data);
    }
	
	function generate() {  
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');  
		$data['choose']  = '0'; 
		$data['edition'] = $this->MonitoringEproceedingModel->getEprocEdition()->result();
		$submit			 = $this->input->post('submit');
		$choose 		 = $this->input->post('choose');
		$list 		 	 = $this->input->post('list');
		$data['choose']  = $this->input->post('choose');
		$data['list']  	 = $this->input->post('list');
		
		
		$edition		 = $this->MonitoringEproceedingModel->getEprocEditionById($choose)->row(); 
		$html="";
		if ($submit!="" and $choose!="0" and $list!="0"){
			$html = '<table border="5" cellspacing="1" cellpadding="1">
						<tbody>';
						//<tr width="100%" background="/uploads/left.png">
			$html .='	<tr width="100%">
						<td rowspan="150" width="3%">&nbsp;</td>
						</tr>';
			if ($list=='design'){
				$list_id = '4';
				$html .='
						<tr>
						<td><img src="/uploads/banner/banner_artdesign.jpg" alt="" width="100%" /></td>
						</tr>';
				$fakultas = "'4'";
			}
			else if ($list=='engineering'){
				$list_id = '1';
				$html .='
						<tr>
						<td><img src="/uploads/banner/banner_engineering.jpg" alt="" width="100%" /></td>
						</tr>';
				$fakultas = "'5','6','7'";
			}	
			else if ($list=='management'){
				$list_id = '3';
				$html .='
						<tr>
						<td><img src="/uploads/banner/banner_management.jpg" alt="" width="100%" /></td>
						</tr>';
				$fakultas = "'8','9'";
			}	
			else if ($list=='science'){
				$list_id = '2';
				$html .='
						<tr>
						<td><img src="/uploads/banner/banner_appliedscience.jpg" alt="" width="100%" /></td>
						</tr>';
				$fakultas = "'3'";
			}
			
			
			$article = $this->MonitoringEproceedingModel->getArticleEprocByIdEdition($choose,$list_id)->result();  
			
			$art = array();
			if(is_array($article)){
				foreach($article as $row){
					$art[] = $row->jea_code;
				}
			}
			
			$html.='<tr bgcolor="eeeeee">
					<td style="text-align: right;"><strong><span style="font-size: 12px;">Vol., No. '.$edition->nama.'</span></strong></td>
					</tr> 
					<tr>
						<td></td>
					</tr>';
			$catalog = array();
			$jurusan = $this->MonitoringEproceedingModel->getJurusanPerFakultas($fakultas)->result(); 
			$data['total'] 			= 0;
			$data['jurnal'] 		= 0;
			$data['jurnal_eproc'] 	= 0;
			foreach($jurusan as $jur) {	
				$dt = $this->MonitoringEproceedingModel->generateEproc($jur->C_KODE_PRODI,$edition->datestart,$edition->datefinish)->result(); 
				$data['total'] = $data['total'] + $this->MonitoringEproceedingModel->generateEproc($jur->C_KODE_PRODI,$edition->datestart,$edition->datefinish)->num_rows();
				// echo "<pre>";
				// print_r($dt);
				// echo "</pre>";
				$catalog[$jur->NAMA_PRODI] = array();
				$html .='
						<tr style="background-color: #dd2821;">
						<td style="color: #ffffff;"><strong>Program Studi '.$jur->NAMA_PRODI.'</strong></td>
						</tr>
						<tr>
							<td>&nbsp;&nbsp;<strong><input class="selectAllPDF" type="checkbox" name="" value="'.$jur->C_KODE_PRODI.'">&nbsp;&nbsp;Check All PDF by NIM</strong>&nbsp;&nbsp;&nbsp;&nbsp;<input data-id="'.$jur->C_KODE_PRODI.'"" type="button" value="Download PDF" class="btn btn-success btn-embossed downloadPDF">&nbsp;&nbsp;<strong><input class="selectAllWord" type="checkbox" name="" value="'.$jur->C_KODE_PRODI.'">&nbsp;&nbsp;Check All Word by NIM</strong>&nbsp;&nbsp;&nbsp;&nbsp;<input data-id="'.$jur->C_KODE_PRODI.'"" type="button" value="Download Word" class="btn btn-success btn-embossed downloadWord">

							<input class="selectAllPDFno" type="checkbox" name="" value="'.$jur->C_KODE_PRODI.'">&nbsp;&nbsp;Check All PDF by No Katalog</strong>&nbsp;&nbsp;&nbsp;&nbsp;<input data-id="'.$jur->C_KODE_PRODI.'"" type="button" value="Download PDF" class="btn btn-success btn-embossed downloadPDFno">&nbsp;&nbsp;<strong><input class="selectAllWordno" type="checkbox" name="" value="'.$jur->C_KODE_PRODI.'">&nbsp;&nbsp;Check All Word By No Katalog</strong>&nbsp;&nbsp;&nbsp;&nbsp;<input data-id="'.$jur->C_KODE_PRODI.'"" type="button" value="Download Word" class="btn btn-success btn-embossed downloadWordno">
							
							</td>
						</tr>
						<tr>
						<td><ol>'; 
				foreach ($dt as $row){
					
					$html.='';
					
					$eproc  = $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$row->code.'/'.$row->code.'_jurnal_eproc.pdf';
					$jurnal  = $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$row->code.'/'.$row->code.'_jurnal.pdf';
					$word  = $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$row->code.'/'.$row->code.'_jurnal.docx';
					if (file_exists($jurnal)) $data['jurnal']++; 
					// if(!file_exists($jurnal) and file_exists($eproc)) echo $jurnal;
					if (file_exists($eproc)){ 
						// if($jur->C_KODE_PRODI=='12') echo $row->code."<br>";

						//pdf
						$datas['download'] = 1;  
						$datas['readonly'] = 1;   
						$datas['name'] = $row->master_data_number.'.pdf';
						$datas['link'] = $eproc;  
						$test = json_encode($datas);  
						$pdf = base64_encode($test);  
						$link_download_pdf = "https://openlibrary.telkomuniversity.ac.id/open/index.php/download/flippingbook_url_download_bypass/".$pdf;
						
						//=====================

						//pdf
						$datas['download'] = 1;  
						$datas['readonly'] = 1;   
						$datas['name'] = $row->master_data_number.'.docx';
						$datas['link'] = $word;  
						$test = json_encode($datas);  
						$word = base64_encode($test);  
						$link_download_word = "https://openlibrary.telkomuniversity.ac.id/open/index.php/download/flippingbook_url_download_bypass/".$word;
						
						//=====================

						//pdf
						$datas['download'] = 1;  
						$datas['readonly'] = 1;   
						$datas['name'] = $row->code.'_jurnal_eproc.pdf';
						$datas['link'] = $eproc;   
						$datas['dwn']['knowledge_item_id'] = $row->id;
						$datas['dwn']['member_id'] = '';
						$datas['dwn']['name'] = $row->code.'_jurnal_eproc.pdf';
						
						$test = json_encode($datas);  
						$pdfno = base64_encode($test); 
						$link_download_pdfno = "https://openlibrary.telkomuniversity.ac.id/open/index.php/download/flippingbook_url_download_bypass/".$pdfno;
						//=====================

						//pdf
						$datas['download'] = 1;  
						$datas['readonly'] = 1;   
						$datas['name'] = $row->code.'_jurnal_eproc.docx';
						$datas['link'] = $word;  
						$test = json_encode($datas);  
						$wordno = base64_encode($test);  
						$link_download_wordno = "https://openlibrary.telkomuniversity.ac.id/open/index.php/download/flippingbook_url_download_bypass/".$wordno;
						
						//=====================
						
						$data['jurnal_eproc']++;
						$catalog[$jur->NAMA_PRODI][] = $row->code.'_jurnal_eproc.pdf'; 
						// $html.='<li><a '.(!in_array($row->code,$art)?'style="color:red"':'').' target="_blank" href="/pustaka/files/'.$row->id.'/jurnal_eproc/'.clean(strtolower($row->title)).'.pdf">'.ucwords(strtolower($row->title)).'</a>';

						$html.='<li>&nbsp;&nbsp;<input class="checkPDF'.$jur->C_KODE_PRODI.'" type="checkbox"  name="downloadPDF'.$jur->C_KODE_PRODI.'" value="'.$pdf.'"> <a '.(!in_array($row->code,$art)?'style="color:red"':'').' target="_blank" href="'.$link_download_pdf.'">PDF by NIM</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="checkWord'.$jur->C_KODE_PRODI.'" type="checkbox"  name="downloadWord'.$jur->C_KODE_PRODI.'" value="'.$word.'">&nbsp;&nbsp;<a '.(!in_array($row->code,$art)?'style="color:red"':'').' target="_blank" href="'.$link_download_word.'">Word by NIM</a>&nbsp;&nbsp;
						<input class="checkPDFno'.$jur->C_KODE_PRODI.'" type="checkbox"  name="downloadPDFno'.$jur->C_KODE_PRODI.'" value="'.$pdfno.'"> <a '.(!in_array($row->code,$art)?'style="color:red"':'').' target="_blank" href="'.$link_download_pdfno.'">PDF by No Katalog</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="checkWordno'.$jur->C_KODE_PRODI.'" type="checkbox"  name="downloadWordno'.$jur->C_KODE_PRODI.'" value="'.$wordno.'">&nbsp;&nbsp;<a '.(!in_array($row->code,$art)?'style="color:red"':'').' target="_blank" href="'.$link_download_wordno.'">Word by No Katalog</a>&nbsp;&nbsp;'.ucwords(strtolower($row->title));

						$html.='<br> '.$row->code.' - '.$row->master_data_number.' - '.ucwords(strtolower($row->master_data_fullname)).(!empty($row->editor)? ', '.ucwords(strtolower($row->editor)):'').'</li>';
				 

					}
					// else $html.='<li>'.ucwords(strtolower($row->title)).'<br>'.ucwords(strtolower($row->master_data_fullname)).(!empty($row->editor)? ', '.ucwords(strtolower($row->editor)):'').'</li>';
					
				}
				$html.='</ol>
						</td>
						</tr>';
			}
			$html.='
					<tr>
					<td>
					<p><img src="/uploads/footer_eproc.jpg" alt="" width="100%" /></p>
					<p>&nbsp;</p>
					</td>
					</tr>
					</tbody>
					</table><br><br><br>';

			foreach($catalog as $key => $rows){

				$html .="$key <br><br>array('".implode("', '",$rows)."');<br><br><br>";
			}
			
			// $html .= implode("<br>",$catalog);

		}
		$data['html'] = $html;
		// echo '<pre>';
		// echo htmlspecialchars($html);
		// echo '</pre>';
				
		$data['menu'] 	= 'monitoringeproceeding/monitoringeproceeding_generate'; 
		$this->load->view('theme', $data);
    }
	 
	
	function duplicate($id,$edisi) {
		$edition	= $this->MonitoringEproceedingModel->getEprocEditionById($edisi)->row(); 
		$item		= $this->MonitoringEproceedingModel->getarchivejurnalstatusbykodejurperitem('',$edition->datestart,$edition->datefinish,$id); 
	 
		if ($item->num_rows()!=0){
			$item = $item->row();
			$file = $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$item->code.'/'.$item->code.'_jurnal.pdf';
			$newfile = $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$item->code.'/'.$item->code.'_jurnal_eproc.pdf';
			
			if (file_exists($file)){
				if (!copy($file, $newfile)) {
					echo "failed to copy $file...\n";
				}	
				else redirect ('index.php/monitoringeproceeding/duplicate_eproc/'.$edisi);
				
			}
			else echo "g ada jurnal";
		}
		else redirect('index.php/monitoringeproceeding');	
		
	}
	
	function duplicateall($edisi) {
		$edition	= $this->MonitoringEproceedingModel->getEprocEditionById($edisi)->row(); 
		$item		= $this->MonitoringEproceedingModel->getarchivejurnalstatusbykodejurperitem('',$edition->datestart,$edition->datefinish,''); 
		if ($item->num_rows()!=0){
			$item = $item->result();
			
			foreach ($item as $dt){
				$file = $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$dt->code.'/'.$dt->code.'_jurnal.pdf';
				$newfile = $_SERVER['DOCUMENT_ROOT'].'/../symfony_projects/book/'.$dt->code.'/'.$dt->code.'_jurnal_eproc.pdf';
				
				if (file_exists($file)){
					if (!file_exists($newfile)){
						if (!copy($file, $newfile)) {
							echo "failed to copy $file...\n";
						}	
					}
				}
			}
			redirect ('index.php/monitoringeproceeding/duplicate_eproc/'.$edisi);
		}
		else redirect('index.php/monitoringeproceeding');	
		
	}
	
    

     function publish($jurusan,$edisi) {
		$edition			= $this->MonitoringEproceedingModel->getEprocEditionById($edisi)->row();
		$data['edition']	= $edition;	
		if ($edisi<=4) {
			$data['data'] 		= $this->MonitoringEproceedingModel->getjurnalpublishbykodejur($jurusan,$edition->datestart,$edition->datefinish)->result(); 
			$data['total']		= $this->MonitoringEproceedingModel->totaljurnalpublishbykodejur($jurusan,$edition->datestart,$edition->datefinish)->row()->total;
		}
		else {
			$data['data'] 		= $this->MonitoringEproceedingModel->getdocbykodejurandstate($jurusan,'53',$edition->datestart,$edition->datefinish)->result(); 
			$data['total']		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($jurusan,'53',$edition->datestart,$edition->datefinish)->row()->total;
		} 
		$data['jurusan']	= $this->MonitoringEproceedingModel->getjurbykodejur($jurusan)->row(); 
		$data['site'] 		= $this->MonitoringEproceedingModel->getState('53')->row()->name;
		$data['detail'] 	= 'publish';
		$data['menu'] 		= 'monitoringeproceeding/monitoringeproceeding_detail_publish'; 
		$this->load->view('theme', $data);
    } 
	
}

?>