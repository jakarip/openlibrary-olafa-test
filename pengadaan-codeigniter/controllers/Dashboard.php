<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Member_Model', 'dm', TRUE); 
		$this->load->model('Usermodel', 'um', TRUE);

		if (!$this->session->userdata('user_login_apps')) redirect('login');
	}
	
	public function index()
	{  
		redirect('bookprocurement/dashboard');
		// 		$data['view'] = 'frontend/dashboard/index';
		// 		$iuser = $this->session->userdata(); 
		// 		$data['dt'] = $this->um->getbyone(array('master_data_user' => $iuser['username']))->row(); 

        // $this->load->view('frontend/tpl', $data);
	}

    public function info()
    {   
        $data['view'] 	= 'frontend/dashboard/index';
        $data['check']  = $this->dm->check($this->session->userdata('participant_login_info')->par_id);

        $this->load->view('frontend/tpl', $data); 
    }




	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
}

?>