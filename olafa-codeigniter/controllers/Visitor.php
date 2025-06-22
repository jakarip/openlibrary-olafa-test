<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH.'/vendor/autoload.php'; 
class Visitor extends CI_Controller {
	
	function __construct() {
        parent::__construct();  
		$this->load->model('Dashboard_Model');  
		$this->load->model('ApiModel'); 
		$this->load->model('MonitoringEproceedingModel'); 
		if (!$this->session->userdata('language')) $this->session->set_userdata(array('language' => 'ina')); 
		
	
    }

	 
	public function index()
	{	  
		 
		$location = "and item_location_id in (1,3,4,5,6,7,8,9)";
		
		// //end google analytics
		$data['pengunjung']['year']			= $this->Dashboard_Model->get_year_visitor($location)->row();
		$data['pengunjung']['month']		= $this->Dashboard_Model->get_month_visitor($location)->row();
		$data['pengunjung']['day'] 			= $this->Dashboard_Model->get_day_visitor($location)->row(); 
		$data['pengunjung']['checkout'] 	= $this->Dashboard_Model->get_day_visitor_checkout($location)->row(); 
  
		
		$data['menu'] = "visitor"; 
		$this->load->view('theme_visitor',$data);
	}

	

	 
	public function report()
	{	  
		 
		
		// //end google analytics
		$pengunjung['year']			= $this->Dashboard_Model->get_year_visitor()->row();
		$pengunjung['month']		= $this->Dashboard_Model->get_month_visitor()->row();
		$pengunjung['day'] 			= $this->Dashboard_Model->get_day_visitor()->row(); 
		$pengunjung['checkout'] 	= $this->Dashboard_Model->get_day_visitor_checkout()->row(); 
  
		
		echo '<div class="col-md-3 col-sm-12">
		<table class="table info_dashboard" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<td class="desc_dashboard" align="center" style="font-size:50px;height:100px;">'.date("Y").'</td> 
				</tr> 
				<tr>
					<td align="center" style="font-size:50px;height:100px;">'.number_format($pengunjung['year']->total,0,'','.').'</td> 
				</tr>   
			</thead>
		</table>
	</div> 
	<div class="col-md-3 col-sm-12">
		<table class="table info_dashboard" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<td class="desc_dashboard" align="center" style="font-size:50px;height:100px;">'.date("F Y").'</td> 
				</tr> 
				<tr>
					<td align="center" style="font-size:50px;height:100px;">'.number_format($pengunjung['month']->total,0,'','.').'</td> 
				</tr>   
			</thead>
		</table>
	</div> 
	 
	<div class="col-md-3 col-sm-12">
		<table class="table info_dashboard" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<td class="desc_dashboard" align="center" style="font-size:50px;height:100px;">Check In Hari Ini</td> 
				</tr> 
				<tr>
					<td align="center" style="font-size:50px;height:100px;">'.number_format($pengunjung['day']->total,0,'','.').'</td> 
				</tr>   
			</thead>
		</table>
	</div>  
	 
	<div class="col-md-3 col-sm-12">
		<table class="table info_dashboard" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<td class="desc_dashboard" align="center" style="font-size:50px;height:100px;">Check Out Hari Ini</td> 
				</tr> 
				<tr>
					<td align="center" style="font-size:50px;height:100px;">'.number_format($pengunjung['checkout']->total,0,'','.').'</td> 
				</tr>   
			</thead>
		</table>
	</div>  
	
	';
	}
	
	
	
}
