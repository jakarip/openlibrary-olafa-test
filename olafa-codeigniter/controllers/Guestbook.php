<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Guestbook extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Common_Model', 'cm', TRUE);
		$this->load->model('Member_Model', 'dm', TRUE);
		$this->load->model('Referral_Model', '', TRUE);
		$this->load->model('Usermodel', 'um', TRUE);
		$this->load->model('ApiModel', '', TRUE);  
		$this->load->library("Encrypt");
		$this->load->helper('form');
	}
	
	public function index()
	{
		//if ($this->session->userdata('student_login')) redirect('Pendaftaran/home'); 
		$this->load->view('frontend/guestbook');
	}    
	
	
	public function member()
	{
		if(!$this->input->post('inp')) return false; 
  
		$inp = $this->input->post('inp');   
		$inp['guestbook_date']	= date('Y-m-d H:i:s'); 
		$this->ApiModel->addMember('guestbook',$inp);
			 
		$callback = array('status' => 'success', 'text' => 'Terimakasih '.$inp['guestbook_fullname'].' sudah mengisi buku tamu.'); 
				 
		 
		$this->session->set_flashdata('login_log', $callback);
		redirect('guestbook');  
    } 
	
	
	
	function url_query_encode($array = array())
	{
		return str_replace('/', '_', rtrim(base64_encode(gzcompress(serialize($array))), '='));
	}


	function url_query_decode($str = '')
	{
		return (is_string($str) && strlen($str)) ? @unserialize(gzuncompress(base64_decode(str_replace('_', '/', $str)))) : FALSE;
	}
	
	function blacklistMail($user){
		
		$blacklistMail[] = '@gmail.';
		$blacklistMail[] = '@yahoo.';
		$blacklistMail[] = '@hotmail.';
		$blacklistMail[] = '@rocketmail.';
		$blacklistMail[] = '@kompas.';
		$blacklistMail[] = '@facebook.';
		$blacklistMail[] = '@tandex.';
		$blacklistMail[] = '@fastmail.';
		$blacklistMail[] = '@ymail.';
	
		
		 $status = "true";
		foreach ($blacklistMail as $row){
			if (strpos($user,$row)!==false) {
				$status = "false";
			}
		} 
		
		return $status;
	}
	
	function checkEmail($type,$inp){

		$lemdikti = $this->cm->form_lemdikti_email(); 
		$ptasuh 	= $this->cm->form_ptasuh_email(); 
		
		
		if($type=='lemdikti') {
			$status = "false"; 
			if (strpos(strtolower($inp['email_lemdikti']),$lemdikti[$inp['institution_lemdikti']])!==false) {
				$status = "true";
			}
		}
		else {
			$status = "false"; 
			if (strpos(strtolower($inp['email_ptasuh']),$ptasuh[$inp['institution_ptasuh']])!==false) {
				$status = "true";
			}
		} 
		
		return $status;
	}

	
	
	function whitelistMail($user){
		
		$blacklistMail[] = '@gmail.';
	
		
		 $status = "false";
		foreach ($blacklistMail as $row){
			if (strpos($user,$row)!==false) {
				$status = "false";
			}
		} 
		
		return $status;
	}
	
	function logout()
	{
		$data = array('participant_login' => NULL, 'participant_login_info' => NULL, 'setting' => NULL);
		// $this->session->set_flashdata($data);
        // $this->session->sess_destroy();
        
        $this->session->unset_userdata('user_login'); 

		redirect('/');
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/

	private function random_str($length)
    {
        $keyspace = str_shuffle('ACDEFGHJKLMNPQRTUVWXY123456789');
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[mt_rand(0, $max)];
        }
        return implode('', $pieces);
    }

    private function generate_uuid()
    {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0C2f ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0x2Aff ), mt_rand( 0, 0xffD3 ), mt_rand( 0, 0xff4B )
        );
    }

    public function send_email($data)
    {
        $data['url'] = base64_encode("signature_pmb#_#".$data['par_active_code']."#_#".$data['par_email']);
        $data['setting'] = y_load_setting();

        $body = $this->load->view('frontend/login/email_register', $data, true);

        echo y_send_email($data['par_email'], '['.$data['setting']['website_name'].'] Pendaftaran Akun Baru PMB', $body);
    }
}

?>