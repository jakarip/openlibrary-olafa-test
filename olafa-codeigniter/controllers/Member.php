<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Member_Model', 'dm', TRUE); 
		$this->load->model('Usermodel', 'um', TRUE);
		$this->load->library('ciqrcode');
		
		if (!$this->session->userdata('login')) {
			header("Location: /");
			die();
		}
	}
	
	public function index()
	{
		$data['view'] = 'frontend/member/index';
		$data['title']	= 'Profile Anggota';		
		$data['icon']	= 'icon-user';
		$iuser = $this->session->userdata('user');
		
		$params['data'] = $iuser['username'];
		$params['level'] = 'H';
		$params['size'] = 10;
		$params['savename'] = FCPATH.'tes.png';
		$this->ciqrcode->generate($params);

		$data['qrcode'] = '<img src="'.base_url().'tes.png" />';
		
	 
		$data['dt'] = $this->um->checkstatus($iuser['username'])->row();   
        $this->load->view('frontend/tpl', $data);
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