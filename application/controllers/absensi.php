<?php

class Absensi extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
		$this->load->model('AbsensiModel');
    }

    function index($edisi="") { 
		$data['choose']  = '0'; 
		
		$submit			 = $this->input->post('submit'); 
		$data['month']	 = $this->input->post('month'); 
		if (!empty($submit)){ 
			
			$data['jurusan'] = $this->AbsensiModel->getallProdi()->result();
			$no=1;
			foreach($data['jurusan'] as $row) {
				$data['jumlah'][$no]		= $this->AbsensiModel->getJumlahByTanggal($row->c_kode_prodi,$data['month'])->row()->total; 
				$no++;
			}  
		
		}
		
		$data['site'] 	= 'pengunjung'; 
		$data['view'] 	= 'absensi/absensi'; 
		$this->load->view('main', $data);
    }

    function ta($jurusan,$edisi) { 
		$edition			= $this->AbsensiModel->getEprocEditionById($edisi)->row(); 
		$data['edition']	= $edition;
		$data['data'] 		= $this->AbsensiModel->gettamasukbykodejur($jurusan,$edition->datestart,$edition->datefinish)->result();
		
		$data['total']		= $this->AbsensiModel->totaltamasukbykodejur($jurusan,$edition->datestart,$edition->datefinish)->row()->total; 
		$data['jurusan']	= $this->AbsensiModel->getjurbykodejur($jurusan)->row();
		$data['view'] 		= 'monitoringeproceeding/monitoringeproceeding_detail'; 
		$data['site'] 		= 'monitoring e-proceeding / TA/PA/Thesis masuk'; 
		$data['detail'] 	= 'ta'; 
		$this->load->view('main', $data);
    }
	
	function doc($id,$jurusan,$edisi) { 
		$edition			= $this->AbsensiModel->getEprocEditionById($edisi)->row(); 
		$data['edition']	= $edition;
		$data['data'] 		= $this->AbsensiModel->getdocbykodejurandstate($jurusan,$id,$edition->datestart,$edition->datefinish)->result();
		
		$data['total']		= $this->AbsensiModel->totaldocbykodejurandstate($jurusan,$id,$edition->datestart,$edition->datefinish)->row()->total; 
		$data['jurusan']	= $this->AbsensiModel->getjurbykodejur($jurusan)->row();
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
		$edition			= $this->AbsensiModel->getEprocEditionById($edisi)->row(); 
		$data['edition']	= $edition;
		$data['data']		= $this->AbsensiModel->getjurnalmasukbykodejur($jurusan,$edition->datestart,$edition->datefinish)->result(); 
		$data['total']		= $this->AbsensiModel->totaljurnalmasukbykodejur($jurusan,$edition->datestart,$edition->datefinish)->row()->total;
		$data['jurusan']	= $this->AbsensiModel->getjurbykodejur($jurusan)->row();
		$data['view'] 		= 'monitoringeproceeding/monitoringeproceeding_detail'; 
		$data['site'] 		= 'monitoring e-proceeding / jurnal masuk'; 
		$data['detail'] 	= '';
		$this->load->view('main', $data);
    }

     function publish($jurusan,$edisi) {
		$edition			= $this->AbsensiModel->getEprocEditionById($edisi)->row();
		$data['edition']	= $edition;	
		if ($edisi<=4) {
			$data['data'] 		= $this->AbsensiModel->getjurnalpublishbykodejur($jurusan,$edition->datestart,$edition->datefinish)->result(); 
			$data['total']		= $this->AbsensiModel->totaljurnalpublishbykodejur($jurusan,$edition->datestart,$edition->datefinish)->row()->total;
		}
		else {
			$data['data'] 		= $this->AbsensiModel->getdocbykodejurandstate($jurusan,'53',$edition->datestart,$edition->datefinish)->result(); 
			$data['total']		= $this->AbsensiModel->totaldocbykodejurandstate($jurusan,'53',$edition->datestart,$edition->datefinish)->row()->total;
		} 
		$data['jurusan']	= $this->AbsensiModel->getjurbykodejur($jurusan)->row();
		$data['view'] 		= 'monitoringeproceeding/monitoringeproceeding_detail'; 
		$data['site'] 		= 'monitoring e-proceeding / jurnal publish'; 
		$data['detail'] 	= '';
		$this->load->view('main', $data);
    } 
	
}

?>