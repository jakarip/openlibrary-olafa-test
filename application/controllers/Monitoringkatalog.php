<?php

class Monitoringeproceeding extends CI_Controller {

	/**
	 * Constructor
	 */
	function __construct() {
			parent::__construct(); 
		$this->load->model('MonitoringEproceedingModel');
		if(!$this->session->userdata('login')) redirect('');
	}

	function index($edisi="") { 
		$data['choose']  = '0'; 
		$data['edition'] = $this->MonitoringEproceedingModel->getEprocEdition()->result();
		$submit			 = $this->input->post('submit');
		$choose 		 = $this->input->post('choose');
		$data['choose']  = $this->input->post('choose');
		
		if ($submit!="" and $choose!="0"){ 
			$data['jurusan'] = $this->MonitoringEproceedingModel->getallProdi()->result();
			$edition		 = $this->MonitoringEproceedingModel->getEprocEditionById($choose)->row(); 
			$no=1;
			foreach($data['jurusan'] as $row) {
				$data['tamasuk'][$no]		= $this->MonitoringEproceedingModel->totaltamasukbykodejur($row->c_kode_prodi,$edition->datestart,$edition->datefinish)->row()->total; 
				$data['jurnal'][$no]		= $this->MonitoringEproceedingModel->totaljurnalmasukbykodejur($row->c_kode_prodi,$edition->datestart,$edition->datefinish)->row()->total;
				
				$data['draft'][$no]			= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'22',$edition->datestart,$edition->datefinish)->row()->total;   
				$data['revision'][$no]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'2',$edition->datestart,$edition->datefinish)->row()->total; 
				$data['review'][$no]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'1',$edition->datestart,$edition->datefinish)->row()->total; 
				$data['archieved'][$no]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'5',$edition->datestart,$edition->datefinish)->row()->total; 
				$data['feasible'][$no]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'3',$edition->datestart,$edition->datefinish)->row()->total; 
				$data['eksternal'][$no]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'52',$edition->datestart,$edition->datefinish)->row()->total; 
				
				if($choose<=4)	
					$data['jurnalpublish'][$no]		= $this->MonitoringEproceedingModel->totaljurnalpublishbykodejur($row->c_kode_prodi,$edition->datestart,$edition->datefinish)->row()->total; 			
				else 
					$data['jurnalpublish'][$no]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($row->c_kode_prodi,'53',$edition->datestart,$edition->datefinish)->row()->total; 
				$no++;
			}  
		
		}
		
		$data['site'] 	= 'monitoring e-proceeding'; 
		$data['view'] 	= 'monitoringeproceeding/monitoringeproceeding'; 
		$this->load->view('main', $data);
	}

	function ta($jurusan,$edisi) { 
		$edition			= $this->MonitoringEproceedingModel->getEprocEditionById($edisi)->row(); 
		$data['edition']	= $edition;
		$data['data'] 		= $this->MonitoringEproceedingModel->gettamasukbykodejur($jurusan,$edition->datestart,$edition->datefinish)->result();
		
		$data['total']		= $this->MonitoringEproceedingModel->totaltamasukbykodejur($jurusan,$edition->datestart,$edition->datefinish)->row()->total; 
		$data['jurusan']	= $this->MonitoringEproceedingModel->getjurbykodejur($jurusan)->row();
		$data['view'] 		= 'monitoringeproceeding/monitoringeproceeding_detail'; 
		$data['site'] 		= 'monitoring e-proceeding / TA/PA/Thesis masuk'; 
		$data['detail'] 	= 'ta'; 
		$this->load->view('main', $data);
	}

	function doc($id,$jurusan,$edisi) { 
		$edition			= $this->MonitoringEproceedingModel->getEprocEditionById($edisi)->row(); 
		$data['edition']	= $edition;
		$data['data'] 		= $this->MonitoringEproceedingModel->getdocbykodejurandstate($jurusan,$id,$edition->datestart,$edition->datefinish)->result();
		
		$data['total']		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate($jurusan,$id,$edition->datestart,$edition->datefinish)->row()->total; 
		$data['jurusan']	= $this->MonitoringEproceedingModel->getjurbykodejur($jurusan)->row();
		if($id=='22') $data['site'] 		= 'monitoring e-proceeding / TA/PA/Thesis On Draft'; 
		else if($id=='2') $data['site'] 		= 'monitoring e-proceeding / TA/PA/Thesis Need Revision'; 
		else if($id=='1') $data['site'] 		= 'monitoring e-proceeding / TA/PA/Thesis Ready for Review'; 
		else if($id=='5') $data['site'] 		= 'monitoring e-proceeding / TA/PA/Thesis Archieved'; 
		else if($id=='3') $data['site'] 		= 'monitoring e-proceeding / TA/PA/Thesis Not Feasibe';  
		else if($id=='52') $data['site'] 		= 'monitoring e-proceeding / TA/PA/Thesis Publish Eksternal'; 
		
		$data['view'] 		= 'monitoringeproceeding/monitoringeproceeding_detail'; 
		
		$data['detail'] 	= ''; 
		$this->load->view('main', $data);
	}

	function jurnal($jurusan,$edisi) { 
		$edition			= $this->MonitoringEproceedingModel->getEprocEditionById($edisi)->row(); 
		$data['edition']	= $edition;
		$data['data']		= $this->MonitoringEproceedingModel->getjurnalmasukbykodejur($jurusan,$edition->datestart,$edition->datefinish)->result(); 
		$data['total']		= $this->MonitoringEproceedingModel->totaljurnalmasukbykodejur($jurusan,$edition->datestart,$edition->datefinish)->row()->total;
		$data['jurusan']	= $this->MonitoringEproceedingModel->getjurbykodejur($jurusan)->row();
		$data['view'] 		= 'monitoringeproceeding/monitoringeproceeding_detail'; 
		$data['site'] 		= 'monitoring e-proceeding / jurnal masuk'; 
		$data['detail'] 	= '';
		$this->load->view('main', $data);
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
		$data['view'] 		= 'monitoringeproceeding/monitoringeproceeding_detail'; 
		$data['site'] 		= 'monitoring e-proceeding / jurnal publish'; 
		$data['detail'] 	= '';
		$this->load->view('main', $data);
	} 
	
}

?>