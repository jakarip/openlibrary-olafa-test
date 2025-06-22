<?php

require APPPATH . '/libraries/REST_Controller.php';
 
class Rfidalarm extends REST_Controller {
 
    function __construct($config = 'rest') {
        parent::__construct($config);
		$this->load->model("RfidModel","rm");
    }
 
   
    function index_get() { 
		$roomid				= $this->input->get('roomid');
		$datetime			= date('Y-m-d H:i:s');
		 
		$alarm = $this->rm->GetAlarm($roomid,$datetime)->row();
		if($alarm){
			
			$cur_time=date($alarm->bk_enddate);
			$duration='-10 minutes';
			$startdate = date('Y-m-d H:i:s', strtotime($duration, strtotime($cur_time))); 
			
			if ($startdate < $datetime and $datetime < $alarm->bk_enddate) {
				$this->response(array('status' => 'success'), 200); 
				//echo $alarm->bk_enddate. " ".$startdate. " ".$datetime;				
			}
			else $this->response(array('status' => 'failed'), 502); 
		}
		else $this->response(array('status' => 'failed'), 502); 
			      
    }
}  

?>