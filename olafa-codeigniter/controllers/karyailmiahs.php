<?php

class KaryaIlmiah extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
		$this->load->model('KaryaIlmiahModel');
    }

    function index() { 
		$tmp			 = array();
		$data['jurusan'] = $this->KaryaIlmiahModel->getjurusan()->result();
		foreach ($data['jurusan'] as $row) {
			$nama	 = $row->nama_prodi;
			$jumlah  = $row->jml_ta;
			
			if(in_array($nama, $tmp)) {
				$data['datax'][$row->nama]['jml_tapa'] = $data['datax'][$row->nama]['jml_tapa']+$row->jml_ta; 
			} else {
				$data['datax'][$nama] = array(	'nama_fakultas'   	=> $row->nama_fakultas,
												'nama_prodi'   		=> $row->nama_prodi,
												'jml_tapa' 		=> $row->jml_ta, 
												'kode'   		=> $row->c_kode_prodi);
				$tmp[] = $row->nama_prodi;
			}
		}
		$data['site'] 	 = 'karya ilmiah'; 
		$data['view'] 	 = 'karyailmiah/karyailmiah'; 
		$this->load->view('main', $data);
    }
	
	function detail($kodejurusan)
	{
		$data['ta_pa'] 		= $this->KaryaIlmiahModel->get_ta_pa($kodejurusan, '', '')->result(); 
		
		 
		$data['jurusan']	= $this->KaryaIlmiahModel->getjurusanbyid($kodejurusan)->row(); 
		$data['view'] 			= 'karyailmiah/karyailmiah_detail'; 
		$data['site'] 	 = 'karya ilmiah detail';
		$this->load->view('main',$data);
	} 
}

?>