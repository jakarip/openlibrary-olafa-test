<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Lists extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Common_Model', 'cm', TRUE);
		$this->load->model('Document_Model', 'dm', TRUE);
		$this->load->model('Referral_Model', '', TRUE);
		$this->load->model('Usermodel', 'um', TRUE);
		$this->load->model('ApiModel', '', TRUE);  
		$this->load->library("Encrypt");
		$this->load->helper('form');
		

	}
	 
	public function index()
	{
		if ($this->session->userdata('user_login')!=1) {
			header("Location: /");
			die();
		}
		else{ 
			$workflow = $this->dm->getWorkflow()->result(); 
			$data['workflow'][''] = 'Semua Workflow';		
			foreach($workflow as $row){
				$data['workflow'][$row->id] = $row->name;
			} 
			$type = $this->dm->getKnowledgeType()->result(); 
			$data['type'][''] = 'Semua Jenis Pustaka';		
			foreach($type as $row){
				$data['type'][$row->id] = $row->name;
			}
			$data['status'][''] = 'Semua Status';
			$data['status'][1] = 'Ongoing';
			$data['status'][2] = 'Archived';
			$data['view'] = 'document/lists/index';
			$data['title']	= 'Document';		
			$data['icon']	= 'icon-user';  
			$this->load->view('document/tpl', $data);
		}
	} 
	
	public function keys($var="")
	{   
	 
        if(empty($var))
        {
			header("Location: /");
			die();
        }
         else
        {  
			$var = base64_decode($var);
			$var = json_decode($var, true);  
		 
			// if($var['date']!=date('Y-m-d')){
				// header("location: /");
				// die();
			// }
			
			$temp = $this->um->checkstatus($var['code'])->row();  
			if($temp){
				$datas['user_login'] = true;
				$datas['user_doc']['id'] = $temp->id;
				$datas['user_doc']['username'] 		= $temp->master_data_user;
				$datas['user_doc']['fullname'] 		= $temp->master_data_fullname;
				$datas['user_doc']['membertype']	= $temp->member_type_id;  
				$datas['user_doc']['classtype'] 	= $temp->member_class_id;  
				$this->session->set_userdata($datas); 
				return redirect('document/lists/'.$data);  
			}
			else { 
				header("location: /");
				die();
			}  
        }
	} 
	
	
	public function json()
    {
        if(!$this->input->is_ajax_request()) return false; 
        $columns = array( 
            array( 'db' => 'wd_date', 'dt' => 0 ),
            array( 'db' => 'jenis_workflow', 'dt' => 1 ),
            array( 'db' => 'title', 'dt' => 2 ),
            array( 'db' => 'subjek', 'dt' => 3 ),
            array( 'db' => 'jenis_katalog', 'dt' => 4 ), 
            array( 'db' => 'state_name', 'dt' => 5),
            array( 'db' => 'wd_status', 'dt' => 6 ),
            array( 'db' => 'master_data_fullname', 'dt' => 7 ),
            array( 'db' => 'master_data_user', 'dt' => 8 ),
        );
		
		$workflow = $this->input->post('workflow');
		$type = $this->input->post('type');
		$status = $this->input->post('status');
		$attribute = $this->input->post('attribute');
		$onlyforme = $this->input->post('onlyforme');
		$dates_acceptance_option 	= $this->input->post('dates_acceptance_option'); 
		$dates_acceptance 				= $this->input->post('dates_acceptance'); 

        $this->datatables->set_cols($columns);
        $param	= $this->datatables->query(); 
		// $param['order'] = 'order by wd_date desc';
		// print_r($param);
		
		
		
		
		$option = "";
		if($dates_acceptance_option!='all'){
			$temp = explode(' - ',$dates_acceptance);

			$date1 = y_convert_date($temp[0], 'Y-m-d');
			$date2 = y_convert_date($temp[1], 'Y-m-d');
			$option .= ($option!="" ? " and" : "")." (wd_date between '$date1' and '$date2')";
		} 
		

		$session = $this->session->userdata('user_doc');  
		
		if($workflow!=""){
			if(empty($param['where']))
				$param['where'] = " where w_id='$workflow'";
			else
				$param['where'] .= " and w_id='$workflow'"; 
		}
		
		if($type!=""){
			if(empty($param['where']))
				$param['where'] = " where kt_id='$type'";
			else
				$param['where'] .= " and kt_id='$type'";
		}
		
		if($status!=""){
			if(empty($param['where']))
				$param['where'] = " where wd_status='$status'";
			else
				$param['where'] .= " and wd_status='$status'";
		}
		
		if($attribute!=""){
			if(empty($param['where']))
				$param['where'] = " where (can_edit_state='1' or can_edit_attribute='1'  or wd_member_id='".$session['id']."' )";
			else
				$param['where'] .= " and (can_edit_state='1' or can_edit_attribute='1'  or wd_member_id='".$session['id']."' )";
		} 
		
		
		if($onlyforme!=""){
			if(empty($param['where']))
				$param['where'] = " where allow_only_id='".$session['id']."'";
			else
				$param['where'] .= " and allow_only_id='".$session['id']."'";
		}   
		
		if ($option!=""){ 
			if(empty($param['where'])) 	$param['where'] = "WHERE (".$option.")";
			else $param['where'] .= "AND (".$option.")";
		}

        $result = $this->dm->dtquery($param)->result();
		
        $filter = $this->dm->dtfiltered();
        $total	= $this->dm->dtcount();
        $output = $this->datatables->output($total, $filter);

        foreach($result as $row)
        {
            $btn = "";
            if($row->wd_status == '1')
            {
                $label = '<label class="label label-warning"><strong>Ongoing</strong></label>';
            }
            else
            {
                $label = '<label class="label label-success"><strong>Archived</strong></label>'; 
            } 
			
			if($row->can_edit_state=='1'){
				$can_edit_state = '<button href="#" title="anggota diperbolehkan untuk mengubah state dokumen" class="btn btn-xs btn-icon btn-success"><i class="icon-database-insert"></i></button>';
			}
			else {
				$can_edit_state = '<button href="#" title="anggota tidak diperbolehkan untuk mengubah state dokumen" class="btn btn-xs btn-icon btn-default"><i class="icon-database-insert"></i></button>';
			}
			
			
			if($row->can_edit_attribute=='1'){
				$can_edit_attribute = '<button href="#" title="anggota diperbolehkan untuk mengubah atribut dokumen" class="btn btn-xs btn-icon btn-success"><i class="icon-pencil5"></i></button>';
			}
			else {
				$can_edit_attribute = '<button href="#" title="anggota tidak diperbolehkan untuk mengubah atribut dokumen" class="btn btn-xs btn-icon btn-default"><i class="icon-pencil5"></i></button>';
			}
			
			if($row->can_upload=='1'){
				$can_upload = '<button href="#" title="anggota diperbolehkan untuk mengupload attachment" class="btn btn-xs btn-icon btn-success"><i class="icon-file-upload"></i></button>';
			}
			else {
				$can_upload = '<button href="#" title="anggota tidak diperbolehkan untuk mengupload attachment" class="btn btn-xs btn-icon btn-default"><i class="icon-file-upload"></i></button>';
			}
			
			if($row->can_download=='1'){
				$can_download = '<button href="#" title="anggota diperbolehkan untuk mendownload attachment" class="btn btn-xs btn-icon btn-success"><i class="icon-file-download"></i></button>';
			}
			else {
				$can_download = '<button href="#" title="anggota tidak diperbolehkan untuk mendownload attachment" class="btn btn-xs btn-icon btn-default"><i class="icon-file-download"></i></button>';
			}
			
			if($row->can_comment=='1'){
				$can_comment = '<button href="#" title="anggota diperbolehkan untuk memberikan komentar" class="btn btn-xs btn-icon btn-success"><i class="icon-bubbles8"></i></button>';
			}
			else {
				$can_comment = '<button href="#" title="anggota tidak diperbolehkan untuk memberikan komentar" class="btn btn-xs btn-icon btn-default"><i class="icon-bubbles8"></i></button>';
			}  
			
			if(strtolower($row->jenis_member)=='administrasi'){ 
				$jenis_member = '<button href="#" title="anggota memiliki akses sebagai operator sistem" class="btn btn-xs btn-icon btn-success"><i class="icon-user"></i></button>';
			}
			else if($session['id']==$row->wd_member_id){
				$jenis_member = '<button href="#" title="anggota memiliki akses sebagai pemilik dokumen" class="btn btn-xs btn-icon btn-success"><i class="icon-user"></i></button>';
			} 
			else { 
			$jenis_member = "";
			}
			
			$transfer = "";
			if($row->final_state_id==$row->latest_state_id and $row->wd_status=='1' and $session['membertype']=='1'){
				// $transfer = '<a href="/document/'.$row->wd_id.'/transfer.html" title="Transfer" class="btn btn-xs btn-icon btn-success"><i class="icon-calendar3"></i></a>';
				$transfer = ' <a href="index.php/document/lists/transfer/'.$row->wd_id.'" title="Transfer" class="btn btn-xs btn-icon btn-success"><i class="icon-calendar3"></i></a>';
			}
			
			$deletes = "";
			if($session['membertype']=='1'){

				$deletes = '<a href="javascript:del('.$row->wd_id.', \''.$row->title.'\')" title="Delete Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-trash"></i></a>';
			}
			
            $rows = array (
                "<strong>".$row->master_data_user."</strong><br>".$row->master_data_fullname."<br>".$row->wd_date."<br>ID : ".$row->wd_id, 
				$row->jenis_workflow,
				$row->title,
				$row->subjek,
				$row->jenis_katalog,
				$can_edit_state.' '.$can_edit_attribute.' '.$can_upload.'<br><br>'.$can_download.' '.$can_comment.' '.$jenis_member,
                '<strong>'.$row->state_name."</strong><br>as ".$row->jenis_member.($row->allow_only_username!==null?"<br><br>Only for ".$row->allow_only_username."<br>".$row->allow_only_name:''),
                $label,
                '<a href="index.php/document/lists/edit/'.$row->wd_id.'" target="blank" title="Lihat Detail / Melakukan Approval" class="btn btn-xs btn-icon btn-primary"><i class="icon-book"></i></a> '.$deletes.' '.$transfer
            );

            $output['data'][] = $rows;
        }

        echo json_encode( $output );
    }
	
	public function transfer($id)
	{
 
		if (!$this->session->userdata('user_login')) {
			header("Location: /");
			die();
		}
		else{   
			$session = $this->session->userdata('user_doc');   
			
			$wd = $this->dm->getWorkflowDocumentbyId($id,$session['membertype'])->row(); 
			if($wd->status=='1'){
				//workflow_document ngubah status jadi 2
				$date_now				= date('Y-m-d H:i:s');
				$inp['updated_by'] 		= $session['username'];
				$inp['updated_at'] 		= $date_now;
				$inp['status'] = 2;  
				$this->dm->edit($id,$inp); 
	
				$inp2['knowledge_type_id'] = $wd->knowledge_type_id;
				$inp2['classification_code_id']  = 1;
				$inp2['item_location_id'] = 9; 
				$inp2['faculty_code']  = $wd->C_KODE_FAKULTAS;
				$inp2['course_code']  = $wd->C_KODE_PRODI;
				$inp2['title'] = $wd->title;
				$inp2['author'] = $wd->master_data_fullname;
				$inp2['knowledge_subject_id'] = $wd->knowledge_subject_id; 
				$inp2['author_type'] = 1;
				$inp2['editor'] = ucwords(strtolower($wd->lecturer_name)).($wd->lecturer2_name!=""?", ".ucwords(strtolower($wd->lecturer2_name)):"");
				$inp2['publisher_name'] = 'Universitas Telkom, '.$wd->NAMA_PRODI;
				$inp2['publisher_city'] = 'Bandung';
				$inp2['published_year'] = date('Y'); 
				$inp2['origination'] = '2';
				$inp2['supplier'] = 'Universitas Telkom, '.ucwords(strtolower($wd->NAMA_FAKULTAS));
				$inp2['price'] = 0;
				$inp2['entrance_date'] = date('Y-m-d');
				$inp2['abstract_content'] = $wd->abstract_content; 
				$inp2['penalty_cost'] = 0;
				$inp2['rent_cost'] = 0;
				$inp2['created_by'] = $session['username'];
				$inp2['created_at'] = date('Y-m-d H:i:s'); 
				$inp2['wd_id'] = $wd->id;

				$book = $this->dm->getLastBook(date("y").'.'.sprintf("%02d",$wd->knowledge_type_id))->row(); 
				$temp = explode(".", $book->code); 

				if($book) $code = date("y").'.'.sprintf("%02d",$wd->knowledge_type_id).'.'.sprintf("%03d",($temp[2]+1));
				else $code = date("y").'.'.sprintf("%02d",$wd->knowledge_type_id).'.001';
				$inp2['softcopy_path'] 	= $code;
				$inp2['code'] 			= $code;
				$ids = $this->dm->add_custom($inp2,'knowledge_item');

				$existing_file  = $this->dm->getDocumentFile($wd->id)->result(); 

				//copy file
				$path_doc = '../../../../../../../data/batik/symfony_projects/book/'.$wd->master_data_user.'/'; 
				$path_catalog = '../../../../../../../data/batik/symfony_projects/book/'.$code.'/'; 
				if(!is_dir($path_catalog)) mkdir($path_catalog, 0777, true);
				
				foreach($existing_file as $row){
					// print_r($row);
					$path_doc_file 		= $path_doc.$row->location;
					$path_catalog_file 	= $path_catalog.$code.'_'.$row->utname.'.'.$row->utextension;
					if(file_exists($path_doc_file)){
						copy($path_doc_file,$path_catalog_file);
					}  
				}
				 
				
				// $curriculum_year = '2020';
				$dt = $this->dm->getWorkflowDocumentSubjectByDocumentId($wd->id)->result(); 
			 
				foreach($dt as $row){
					$inp3['knowledge_item_id'] = $ids;
					$inp3['master_subject_id'] = $row->id;
					$this->dm->add_custom($inp3,'knowledge_item_subject');
				}  
  
				$koleksi['knowledge_item_id'] 		= $ids;
				$koleksi['knowledge_type_id'] 		= $wd->knowledge_type_id;
				$koleksi['item_location_id'] 		= 9;
				$koleksi['code'] 					= $code.'-1';
				$koleksi['faculty_code'] 			= $wd->C_KODE_FAKULTAS;
				$koleksi['course_code'] 			= $wd->C_KODE_PRODI;
				$koleksi['origination'] 			= '2';
				$koleksi['supplier'] 				= 'Universitas Telkom, '.ucwords(strtolower($wd->NAMA_FAKULTAS)); 
				$koleksi['price'] 					= '0';
				$koleksi['entrance_date']	 		= date('Y-m-d');
				$koleksi['status'] 					= '1';
				$koleksi['created_by']			 	= $session['username'];
				$koleksi['created_at'] 				= date('Y-m-d H:i:s'); 
				$ids2 = $this->dm->add_custom($koleksi,'knowledge_stock');

				// 1. ngubah status wd
				// 2. insert ke catalog
				// 3. buat folder di book
				// 4. copy file ke folder di book
				header("Location: https://openlibrary.telkomuniversity.ac.id/knowledgeitem/".$ids."/edit.html");
				die();
			}
			else {
				// header("Location: /open/index.php/document/lists");
				// die();
			}

				// redirect('https://openlibrary.telkomuniversity.ac.id/knowledgeitem/'.$id.'/edit.html'); 
		}
	} 
	
	public function add()
	{
		if (!$this->session->userdata('user_login')) {
			header("Location: /");
			die();
		}
		else{  
			$session = $this->session->userdata('user_doc');  
			$workflow = $this->dm->getWorkflowbyMemberType($session['membertype'])->result(); 
			$data['workflow'][''] = 'Pilih Workflow';		
			foreach($workflow as $row){
				$data['workflow'][$row->id] = $row->name;
			}    
			
			$unit = $this->dm->getUnit()->result(); 
			$data['unit'][''] = 'Pilih Unit';		
			foreach($unit as $row){
				$data['unit'][$row->C_KODE_PRODI] = $row->NAMA_PRODI;
			}  
			
			// $subject = $this->dm->getSubject()->result(); 
			// $data['subject'][''] = 'Pilih Subject';		
			// foreach($subject as $row){
				// $data['subject'][$row->id] = $row->name;
			// }  
			 
			$data['view'] = 'document/lists/add';
			$data['title']	= 'New Document';		
			$data['icon']	= 'icon-file-plus';  
			$this->load->view('document/tpl', $data);
		}
	} 

    public function getknowledgetype()
	{   
		if(!$this->input->is_ajax_request()) return false; 
		$dt = $this->dm->getKnowledgeTypeByWorkflowId($this->input->post('id'))->result();
		echo json_encode($dt);
	} 

    public function getfile()
	{   
		if(!$this->input->is_ajax_request()) return false; 
		$dt = $this->dm->getUploadTypeByWorkflowId($this->input->post('id'))->result();
		echo json_encode($dt);
	} 

    public function getmastersubject()
	{   
		if(!$this->input->is_ajax_request()) return false; 
		// $curriculum_year = '2020';
		$dt = $this->dm->getMasterSubjectByUnitId($this->input->post('id'))->result();
		echo json_encode($dt);
	}  
	
	public function getsubjectid()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('searchTerm')) return false;
		
		$s = strtolower($this->input->post('searchTerm'));
		$dbs = $this->dm->getSubject($s)->result();
		
		$result = array(); 
		foreach($dbs as $db)
			$result[] = array('id' => $db->id,
							  'text' => $db->name);
		
		echo json_encode($result);
	}

	public function add_comment()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('comment')) return false;
		
		$session = $this->session->userdata('user_doc');
		
		$date_now				= date('Y-m-d H:i:s');

		$latest_state_id = $this->input->post('latest_state_id_old');

		$item['document_id'] = $this->input->post('wd_id');
		$item['comment']  = $this->input->post('comment'); 
		$item['created_at']  = $date_now; 
		$item['member_id']  = $session['id'];
  

		$state = $this->dm->getDocumentStateId($item['document_id'],$latest_state_id)->row();

		$item['document_state_id'] = $state->id;
		$id = $this->dm->add_custom($item,'workflow_comment'); 


		$html = '
		<div  id="append_comment'.$id.'">
			<div class="row">   
				<div class="form-group form-group-sm"> 
					<label for="pus_name" class="col-sm-0 control-label"></label>
					<div class="col-sm-12"> 
						<label for="pus_name" class="control-label"><b><i class="icon-user"></i> '.$session['username'].' - '.$session['fullname'].'</b></label><div class="delete_comment" data-id="'.$id.'" style="text-align: right;float:right;cursor:pointer"><i class="icon-bin"></i></div><br>
						<div style="padding: 5px 10px;background-color: #fafafa;border: 1px solid #dedede;border-radius: 3px;">
							'.$item['comment'].'
							<br>
							<div style="font-size: 8pt;text-align: right;">
								'.$date_now.'
							</div>
						</div> 
						<div class="row">   
							<div class="form-group form-group-sm">
								<div class="col-sm-12">
									&nbsp;
								</div> 
							</div>  
						</div>  
						<div id="append_reply'.$id.'">
						</div>	
						<div class="row">    
							<div class="form-group form-group-sm" style="display:none" id="show_reply'.$id.'">
								<label for="pus_name" class="col-sm-1 control-label"></label>
								<div class="col-sm-11">
									<textarea type="text" name="reply'.$id.'" id="reply'.$id.'" class="form-control"></textarea>
								<br> 
								</div>
							</div>   
						</div>    

						<div class="row">   
							<label for="pus_name" class="col-sm-1 control-label"></label>
							<div class="form-group form-group-sm">
								<div class="col-sm-11">
									<div data-id="'.$id.'" class="btn btn-primary btn-labeled add_reply">
										<b><i class="icon-comments"></i></b>Add Reply
									</div>    
								</div>
							</div>  
						</div>  
					</div>
				</div>  
			</div> 
			<div class="row">   
				<div class="form-group form-group-sm">
					<div class="col-sm-12">
						&nbsp;
					</div>
				</div>  
			</div> 
		</div>';

		echo json_encode(array('status'=>TRUE,'message'=>$html));
	}  
	
	public function add_reply()
	{ 
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('comment')) return false;
		
		$session = $this->session->userdata('user_doc');
		
		$date_now				= date('Y-m-d H:i:s');

		$latest_state_id = $this->input->post('latest_state_id_old');

		$item['document_id'] = $this->input->post('wd_id');
		$item['comment']  = $this->input->post('comment'); 
		$item['parent_id']  = $this->input->post('parent_id');
		$item['created_at']  = $date_now; 
		$item['member_id']  = $session['id'];
  

		$state = $this->dm->getDocumentStateId($item['document_id'],$latest_state_id)->row();

		$item['document_state_id'] = $state->id;
		$id = $this->dm->add_custom($item,'workflow_comment');  


		$html = '
		<div id="append_reply2'.$id.'">	
			<div class="row">   
				<div class="form-group form-group-sm"> 
					<label for="pus_name" class="col-sm-1 control-label"></label>
					<div class="col-sm-11">
						<label for="pus_name" class="control-label"><b><i class="icon-bubbles"></i> '.$session['username'].' - '.$session['fullname'].'</b></label><div class="delete_reply" data-id="'.$id.'" style="text-align: right;float:right;cursor:pointer"><i class="icon-bin"></i></div><br>
						<div style="padding: 5px 10px;background-color: #fafafa;border: 1px solid #dedede;border-radius: 3px;">
							'.$item['comment'].'
							<br> 
							<div style="font-size: 8pt;text-align: right;">
								'.$date_now.'
							</div>
						</div>
						
					</div>
				</div>  
			</div> 
			<div class="row">   
				<div class="form-group form-group-sm">
					<div class="col-sm-12">
						&nbsp;
					</div>
				</div>  
			</div> 
		</div>
		';

		echo json_encode(array('status'=>TRUE,'message'=>$html));
	}  

	public function delete_comment_reply()
	{ 
		if(!$this->input->is_ajax_request()) return false; 
		
		$session = $this->session->userdata('user_doc');
		
		$id				= $this->input->post('id');
		$this->dm->delete_document_comment($id);
		 

		echo json_encode(array('status'=>TRUE));
	} 
	
	public function getlecturerid()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('searchTerm')) return false;
		
		$s = $this->input->post('searchTerm');
		$dbs = $this->dm->getlecturer($s)->result();
		
		$result = array(); 
		foreach($dbs as $db)
			$result[] = array('id' => $db->id,
							  'text' => '( '.$db->master_data_number.') - '.$db->master_data_fullname);
		
		echo json_encode($result);
	}  
	
	public function edit($id)
	{ 
		if (!$this->session->userdata('user_login')) {
			header("Location: /");
			die();
		}
		else{   
			$session = $this->session->userdata('user_doc');   
			
			$data['wd'] = $this->dm->getWorkflowDocumentbyId($id,$session['membertype'])->row(); 
			

			if(($data['wd']->start_state_id == $data['wd']->latest_state_id) and ($data['wd']->member_id!=$session['id'] and $session['membertype']!='1')){
				header("Location: /open/index.php/document/lists");
				die();
			}
			$next = $this->dm->getNextState($data['wd']->latest_state_id)->result(); 


			$data['next'][''] = 'Pilih Next State';		
			foreach($next as $row){
				$data['next'][$row->id] = $row->name;
			}    
			
			$unit = $this->dm->getUnit()->result();  	
			foreach($unit as $row){
				$data['unit'][$row->C_KODE_PRODI] = $row->NAMA_PRODI;
			}   
			
			
			// $curriculum_year = '2020';
			$dt = $this->dm->getDocumentMasterSubjectByUnitId($data['wd']->course_code,$data['wd']->id)->result(); 
			$data['master_subject'] = "";
			$data['master_subject_view'] = ""; 
			foreach($dt as $row){
				if($row->total!=0){
					$data['master_subject_view'] .= $row->code.' - '.$row->name.'<br>';
				}
				$data['master_subject'] .= '<option value="'.$row->id.'" '.($row->total!=0?'selected':'').'>'.$row->code.' - '.$row->name.'</option>';
			}  
			
			
			
			$dt = $this->dm->getDocumentSdgs($data['wd']->id)->result(); 
			$temp = array();
			if($dt){ 
				foreach($dt as $key=> $row){
					$temp[$key] = $row->sdgs_kode;
				}
			} 
			
			$data['sdgs_existing'] = $temp;  
			$data['sdgs'] = $this->cm->sdgs(); 
			
			$data['sdgs_view'] = "";  
			$dt = $this->cm->sdgs();
			foreach($dt as $key=>$row){
				if(in_array($key,$temp)){
					$data['sdgs_view'] .= $row.'<br>';
				} 
			}   
			
			$data['existing_file']  = $this->dm->getDocumentFile($data['wd']->id)->result(); 
			$data['document_state'] = $this->dm->getDocumentState($data['wd']->id)->result(); 

			
			$where = "where document_id='".$data['wd']->id."'and parent_id is null";
			$comment_parent = $this->dm->getDocumentComment($where)->result(); 

			$comment = array();
			if($comment_parent){
				foreach($comment_parent as $key=>$row){
					$comment[$key]['id'] = $row->id;
					$comment[$key]['user'] = $row->master_data_user; 
					$comment[$key]['name'] = $row->master_data_fullname; 
					$comment[$key]['comment'] = $row->comment;
					$comment[$key]['created_at'] = $row->created_at; 
					$comment[$key]['reply'] = array();
					
					$where = "where document_id='".$data['wd']->id."'and parent_id='".$row->id."'";
					$comment_reply = $this->dm->getDocumentComment($where)->result(); 
					if($comment_reply){ 
						foreach($comment_reply as $key2=>$row2){
							$comment[$key]['reply'][$key2]['id'] = $row2->id;
							$comment[$key]['reply'][$key2]['user'] = $row2->master_data_user;
							$comment[$key]['reply'][$key2]['name'] = $row2->master_data_fullname;
							$comment[$key]['reply'][$key2]['comment'] = $row2->comment;
							$comment[$key]['reply'][$key2]['created_at'] = $row2->created_at;  
						}
					}
				}
			}
				
			$data['comment']	= $comment;

			// echo "<pre>";
			// print_r($data['comment']); 
			// echo "</pre>";
			 
			$data['view'] = 'document/lists/edit';
			$data['title']	= 'Edit Document';		
			$data['icon']	= 'icon-book';  
			$this->load->view('document/tpl', $data);
		}
	}  
	
	public function save()
    {
        $inp 			= $this->input->post('inp'); 
        $master_subject = $this->input->post('master_subject'); 
        $upload_type 	= $_FILES['upload_type'];  
		$session = $this->session->userdata('user_doc');
		
		//workflow_document
		$workflow = $this->dm->getWorkflowbyId($inp['workflow_id'])->row();
		$date_now				= date('Y-m-d H:i:s');
		$inp['member_id'] 		= $session['id'];
		$inp['status'] 			= '1';
		$inp['created_by'] 		= $session['username'];
		$inp['created_at'] 		= $date_now;
		// $inp['updated_by'] 		= null;
		// $inp['updated_at'] 		= null;
		$inp['latest_state_id'] = $workflow->start_state_id; 
		$wd_id = $this->dm->add($inp);
		
		//workflow_document_subject
		if($master_subject){
			$temp = array();
			foreach($master_subject as $row){
				$temp['workflow_document_id'] = $wd_id;
				$temp['master_subject_id'] 	  = $row;
				$this->dm->add_custom($temp,'workflow_document_subject');
			}
		}
		
		//workflow_document_state
		$state = $this->dm->getWorkflowStatebyId($workflow->start_state_id,$workflow->id)->row();
		$temp2 = array();
		$temp2['document_id'] 	= $wd_id;
		$temp2['member_id'] 	= $session['id'];
		$temp2['state_id'] 		= $workflow->start_state_id;
		$temp2['open_date'] 	= $date_now;
		if($state->rule_type==1){
			$temp2['allowed_member_id'] = $session['id'];
			$temp2['open_by'] 			= $session['id'];
		
		} 
		$this->dm->add_custom($temp2,'workflow_document_state');
		
		
		
		//workflow_document_file
		if($upload_type){ 
			$upload = array(); 
			$dt = $this->dm->getUploadTypeByWorkflowId($workflow->id)->result();
			foreach($dt as $key=> $row){
				$upload['id'][$row->id] = $row->id;
				$upload['ext'][$row->id] = $row->extension;
				$upload['name'][$row->id] = $row->name;
				$upload['title'][$row->id] = $row->title;
			}  
			$upPath = '../../../../../../../data/batik/symfony_projects/book/'.$session['username'].'/'; 
			if(!file_exists($upPath)) mkdir($upPath, 0777, true);
			
			$temp3 = array();
			foreach($upload_type['name'] as $key=>$row){
				if($row!=""){ 
					$filename=$upload_type["tmp_name"][$key];
					$extension=$upload['ext'][$key];
					$newfilename= $session['username'].'_'.$wd_id.'_'.$upload['name'][$key].'.'.$extension;
					move_uploaded_file($filename, $upPath.$newfilename); 
					
					$temp3['document_id'] 		= $wd_id;
					$temp3['upload_type_id'] 	= $key;
					$temp3['name'] 				= $upload['title'][$key];
					$temp3['location'] 			= $newfilename;
					$temp3['created_by'] 		= $session['username'];
					$temp3['created_at'] 		= $date_now;
					$temp3['updated_by'] 		= $session['username'];
					$temp3['updated_at'] 		= $date_now;
					$this->dm->add_custom($temp3,'workflow_document_file');
				}
			}
		}
		redirect('document/lists/edit/'.$wd_id);
    } 
	
	public function update()
    {
        $inp 					= $this->input->post('inp'); 
        $master_subject 		= $this->input->post('master_subject'); 
        $workflow_id 			= $this->input->post('workflow_id'); 
        $wd_id 					= $this->input->post('wd_id'); 
        $latest_state_id 		= $this->input->post('latest_state_id'); 
        $latest_state_id_old 	= $this->input->post('latest_state_id_old');
        $sdgs					= $this->input->post('sdgs');
        $upload_type 			= $_FILES['upload_type'];   
		$session 				= $this->session->userdata('user_doc');
		
		$member_doc = $this->dm->getWorkflowDocumentMember($wd_id)->row();
		//workflow_document 
		// date('Y-m-d H:i:s', strtotime('-7 hours'));
		$date_now				= date('Y-m-d H:i:s');
		$inp['updated_by'] 		= $session['username'];
		$inp['updated_at'] 		= $date_now;
		if($latest_state_id!="") $inp['latest_state_id'] = $latest_state_id;  
		$this->dm->edit($wd_id,$inp);
		
		
		$wd = $this->dm->getWorkflowDocumentbyId($wd_id,$session['membertype'])->row(); 
 
		//ngubah yg melakukan approve jika dosen pembimbing 1 berhalangan
		if($wd->latest_state_id=='1' and $session['membertype']=='1' and $latest_state_id=='' and $inp['approved_id']!='0' and $inp['approved_id']!=null){ 
			$this->dm->edit_approved_document_state($wd_id,$latest_state_id_old,$inp['approved_id']); 
		}
		//====================================================

		//workflow_document_subject
		if($master_subject){
			$temp = array();
			$this->dm->delete_workflow_document_subject($wd_id);
			foreach($master_subject as $row){
				$temp['workflow_document_id'] = $wd_id;
				$temp['master_subject_id'] 	  = $row;
				$this->dm->add_custom($temp,'workflow_document_subject');
			}
		} 
		
		//workflow_document_state
		if($latest_state_id!=""){ 
			$state = $this->dm->getstatebyid($latest_state_id)->row(); 
			$this->dm->edit_workflow_document_state($wd_id,$latest_state_id_old,$session['id']); 
			
			$temp2 = array();
			$temp2['document_id'] 	= $wd_id;
			$temp2['member_id'] 	= $session['id'];
			$temp2['state_id'] 		= $latest_state_id;
			$temp2['open_date'] 	= $date_now;
			if($state->rule_type==1 or $state->rule_type==0){
  
				if($latest_state_id=='2'){
					$temp2['allowed_member_id'] = $member_doc->member_id;
					$temp2['open_by'] 			= $member_doc->member_id; 
					// ".$state->name."  
					$messages 		= $session['fullname']." telah meminta dilakukan revisi karya ilmiah";  
					$title = "Karya Ilmiah - Need Revision";  
				}
				else{
					$approved_status = array(3,4,52,53,64,91);
					if(in_array($latest_state_id,$approved_status)){  
						$messages 		= $session['fullname']." telah melakukan approval karya ilmiah";
						$title = "Karya Ilmiah - Approved";  
					}
				} 

				$itemnotif['notif_id_member'] 	= $member_doc->master_data_user;
				$itemnotif['notif_type'] 	= 'karyailmiah';
				$itemnotif['notif_content'] 	= $messages;
				$itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
				$itemnotif['notif_status'] 	= 'unread';
				$itemnotif['notif_id_detail'] 	= $wd_id;

				$notif_id = $this->dm->add_custom($itemnotif,'notification_mobile'); 

				$token = $this->dm->getTokenNotificationMobile($member_doc->member_id)->row(); 

				$notif_content = $messages;
				$notif_id_detail = $wd_id;
				$notif_type = 'karyailmiah'; 
				$token = $token->master_data_token;

				NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);    
			
			} 
			//rule_type = 2  Khusus Pembimbing Akademik
			else if($state->rule_type==2){
				//jika workflow skripsi ta pa, ambil lecturer dari kolom lecturer_id tabel workflow_document

				if($workflow_id=='1'){
					$temp2['allowed_member_id'] = $inp['lecturer_id'];
					$temp2['open_by'] 			= $inp['lecturer_id']; 

					$messages 		= $session['fullname']." meminta approval karya ilmiah";

					$itemnotif['notif_id_member'] 	= $wd->lecturer_username;
					$itemnotif['notif_type'] 	= 'karyailmiah';
					$itemnotif['notif_content'] 	= $messages;
					$itemnotif['notif_date'] 	= date('Y-m-d H:i:s');
					$itemnotif['notif_status'] 	= 'unread'; 
					$itemnotif['notif_id_detail'] 	= $wd_id;


					$notif_id = $this->dm->add_custom($itemnotif,'notification_mobile'); 

					$token = $this->dm->getTokenNotificationMobile($inp['lecturer_id'])->row();
	
					$title = "Karya Ilmiah - Request Approval";  
					$notif_content = $messages;
					$notif_id_detail = $wd_id;
					$notif_type = 'karyailmiah';
					$token = $token->master_data_token;
	
					NotificationMobile($title,$notif_id,$notif_content,$notif_id_detail,$notif_type,$token);    

				}
				else {
					$lecturer = $this->dm->smk_t_tra_mhs_skripsi($member_doc->master_data_user)->row();
					if($lecturer){
						$member = $this->dm->getmembers($lecturer->C_KODE_DOSEN_PEMBIMBING_SATU)->row();
						if($member){ 
							$temp2['allowed_member_id'] = $member->id;
							$temp2['open_by'] 			= $member->id;
						}
					}
				}
			}
			// print_r($temp2);

			$this->dm->add_custom($temp2,'workflow_document_state');
			
		} 
		
		//workflow_document_sdgs
		if($sdgs){
			 
			$mastersdgs = $this->cm->sdgs(); 
			$this->dm->delete_workflow_document_sdgs($wd_id);
			$tempsdgs = array();
			foreach($sdgs as $key=>$row){
				$tempsdgs['document_id'] = $wd_id;
				$tempsdgs['sdgs_kode'] = $key;
				$tempsdgs['sdgs_name'] = $mastersdgs[$key];
				$this->dm->add_custom($tempsdgs,'workflow_document_sdgs');
			}
		} 
		
		//workflow_document_file
		if($upload_type){ 
			$upload = array();
			$dt = $this->dm->getuploadtypebyworkflowid($workflow_id)->result();
			foreach($dt as $key=> $row){
				$upload['id'][$row->id] = $row->id;
				$upload['ext'][$row->id] = $row->extension;
				$upload['name'][$row->id] = $row->name;
				$upload['title'][$row->id] = $row->title;
				$upload['title'][$row->id] = $row->title;
			}   
			
			$uppath = '../../../../../../../data/batik/symfony_projects/book/'.$member_doc->master_data_user.'/'; 
			if(!file_exists($uppath)) mkdir($uppath, 0777, true);
			
			$temp3 = array();
			foreach($upload_type['name'] as $key=>$row){
				if($row!=""){ 
					$filename=$upload_type["tmp_name"][$key];
					$extension=$upload['ext'][$key];
					$newfilename= $member_doc->master_data_user.'_'.$wd_id.'_'.$upload['name'][$key].'.'.$extension;
					move_uploaded_file($filename, $uppath.$newfilename); 
					
					$check_existing_file = $this->dm->check_existing_file($wd_id,$newfilename)->row();
					if(!$check_existing_file){
						$temp3['document_id'] 		= $wd_id;
						$temp3['upload_type_id'] 	= $key;
						$temp3['name'] 				= $upload['title'][$key];
						$temp3['location'] 			= $newfilename;
						$temp3['created_by'] 		= $session['username'];
						$temp3['created_at'] 		= $date_now;
						$temp3['updated_by'] 		= $session['username'];
						$temp3['updated_at'] 		= $date_now;
						$this->dm->add_custom($temp3,'workflow_document_file');
					}
				}
			}
		}
		redirect('document/lists/edit/'.$wd_id);
    }  
	
	public function delete()
	{
		if(!$this->input->is_ajax_request()) return false; 
		if(!$this->input->post('id')) return false;
		
		$id = $this->input->post('id');
		echo json_encode(array('status' => 'ok;', 'text' => 'Data yang di hapus ID : '.$id));
		// if( $this->dm->delete($id) )
		// 	echo json_encode(array('status' => 'ok;', 'text' => 'Data yang di hapus ID : '.$id));
		// else
		// 	echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menghapus Data'));
	}
	 
	
	function logout()
	{
		$data = array('login' => NULL, 'user' => NULL);
		$this->session->set_flashdata($data);
        $this->session->sess_destroy();
        
        $this->session->unset_userdata('user_login'); 

		header("Location: /");
		die();
	} 
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/

	 
}

?>