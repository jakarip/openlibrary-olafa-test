<?php
class Document_Model extends CI_Model 
{
	
	private $table = 'workflow_document';
	private $id    = 'id';

	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	// Query for new datatables purpose ;
	//--------------------------------------------------------------------------------------------------------------------------
	function dtquery($param)
	{  
		$session = $this->session->userdata('user_doc');    
		if($session['membertype']=="1"){
			return $this->db->query("SELECT SQL_CALC_FOUND_ROWS * from (select w.id w_id,kt.id kt_id, wd.id wd_id,m.master_data_user,m.master_data_fullname,wsp.id wsp_id, wd.created_at wd_date,ws.name state_name,wd.status wd_status,w.name jenis_workflow,title,w.final_state_id,wd.latest_state_id,wd.member_id wd_member_id,
			ks.name subjek, kt.name jenis_katalog,m2.id allow_only_id, m2.master_data_user allow_only_username,m2.master_data_fullname allow_only_name, wsp.*,
			(select name from member_type where id='".$session['membertype']."') jenis_member 
			FROM ".$this->table." wd
			left join workflow w on w.id=wd.workflow_id 
			left join knowledge_subject ks on ks.id=knowledge_subject_id 
			left join knowledge_type kt on kt.id=knowledge_type_id 
			left join member m on m.id=member_id
			left join workflow_document_state wds on (wds.document_id=wd.id and latest_state_id=wds.state_id and close_date is null)
			left join workflow_state ws on ws.id=latest_state_id 
			left join member m2 on m2.id=wds.allowed_member_id
			left join workflow_state_permission wsp on (wsp.state_id=wd.latest_state_id and wsp.member_type_id='".$session['membertype']."') 
			where (wsp.member_type_id='".$session['membertype']."')) a
			$param[where] $param[order] $param[limit]");
		}
		else {   
			return $this->db->query("SELECT SQL_CALC_FOUND_ROWS * from (select wd.member_id wdmemberid, w.id w_id,kt.id kt_id, wd.id wd_id,m.master_data_user,m.master_data_fullname,wsp.id wsp_id, wd.created_at wd_date,ws.name state_name,wd.status wd_status,w.name jenis_workflow,title,w.final_state_id,wd.latest_state_id,wd.member_id wd_member_id,
			ks.name subjek, kt.name jenis_katalog,m2.id allow_only_id, m2.master_data_user allow_only_username,m2.master_data_fullname allow_only_name, wsp.*,
			(select name from member_type where id='".$session['membertype']."') jenis_member 
			FROM ".$this->table." wd
			left join workflow w on w.id=wd.workflow_id 
			left join knowledge_subject ks on ks.id=knowledge_subject_id 
			left join knowledge_type kt on kt.id=knowledge_type_id 
			left join member m on m.id=member_id
			left join workflow_document_state wds on (wds.document_id=wd.id and latest_state_id=wds.state_id and close_date is null)
			left join workflow_state ws on ws.id=latest_state_id 
			left join member m2 on m2.id=wds.allowed_member_id
			left join workflow_state_permission wsp on (wsp.state_id=wd.latest_state_id and wsp.member_type_id='".$session['membertype']."') 
			where (wsp.member_type_id='".$session['membertype']."' or wd.member_id='".$session['id']."') and (wds.allowed_member_id='".$session['id']."' or wd.member_id='".$session['id']."' or (wds.allowed_member_id is null and ws.rule_type!='1') or (wds.id is null and wd.member_id='".$session['id']."'))) a
			$param[where] $param[order] $param[limit]");
		}
	}
	
	function dtfiltered()
	{
		$result = $this->db->query('SELECT FOUND_ROWS() as jumlah')->row();
		
		return $result->jumlah;
	}
	
	function dtcount()
	{
		$session = $this->session->userdata('user_doc');    
		if($session['membertype']=="1"){
			$result = $this->db->query("SELECT count(*) total  FROM ".$this->table." wd
			left join workflow w on w.id=wd.workflow_id 
			left join knowledge_subject ks on ks.id=knowledge_subject_id 
			left join knowledge_type kt on kt.id=knowledge_type_id 
			left join member m on m.id=member_id
			left join workflow_document_state wds on (wds.document_id=wd.id and latest_state_id=wds.state_id and close_date is null)
			left join workflow_state ws on ws.id=latest_state_id
			left join member m2 on m2.id=wds.allowed_member_id
			left join workflow_state_permission wsp on (wsp.state_id=wd.latest_state_id and wsp.member_type_id='".$session['membertype']."') 
			where ks.active='1' and kt.active='1' and (wsp.member_type_id='".$session['membertype']."')")->row();
		}
		else {
			$result = $this->db->query("SELECT count(*) total  FROM ".$this->table." wd
			left join workflow w on w.id=wd.workflow_id 
			left join knowledge_subject ks on ks.id=knowledge_subject_id 
			left join knowledge_type kt on kt.id=knowledge_type_id 
			left join member m on m.id=member_id
			left join workflow_document_state wds on (wds.document_id=wd.id and latest_state_id=wds.state_id and close_date is null)
			left join workflow_state ws on ws.id=latest_state_id
			left join member m2 on m2.id=wds.allowed_member_id
			left join workflow_state_permission wsp on (wsp.state_id=wd.latest_state_id and wsp.member_type_id='".$session['membertype']."') 
			where ks.active='1' and kt.active='1' and (wsp.member_type_id='".$session['membertype']."' and wd.member_id='".$session['id']."') and (wds.allowed_member_id='".$session['id']."' or (wds.allowed_member_id is null and ws.rule_type!='1') or (wds.id is null and wd.member_id='".$session['id']."'))")->row();
		}
		
		return $result->total; 
	}
	//--------------------------------------------------------------------------------------------------------------------------
	
	
	function getall()
	{
		return $this->db->get($this->table);
	}
	
	function getbyquery($param)
	{
		return $this->db->query("SELECT * FROM ".$this->table." $param[where] $param[order] $param[limit]");
	}
	
	function countbyquery($param)
	{
		$result = $this->db->query("SELECT COUNT(".$this->id.") as jumlah FROM ".$this->view." $param[where]")->row();
		
		if(!empty($result))
			return $result->jumlah;
		else
			return 0;
	}
	
	function countall()
	{
		return $this->db->count_all($this->table);
	}
	
	function getby($item)
	{
		$this->db->where($item);
		return $this->db->get($this->table);
	}
	
	function getbyid($id)
	{
		$this->db->where($this->id, $id);
		return $this->db->get($this->table);
	}
	
	function add($item)
	{
		$this->db->insert($this->table, $item);
		return $this->db->insert_id();
	}
	
	function edit($id, $item)
	{
		$this->db->where($this->id, $id);
		return $this->db->update($this->table, $item);
	}
	
	function edit_t_mst_user_login($id, $item)
	{
		$this->db->where('C_USERNAME', $id);
		return $this->db->update('masterdata.t_mst_user_login', $item);
	}
	
	function delete($id)
	{
		$this->db->where($this->id, $id);
		return $this->db->delete($this->table);
	}

	
	 
	function delete_t_mst_user_login($id)
	{
		$this->db->where('c_username', $id);
		return $this->db->delete('masterdata.t_mst_user_login');
	}
	
	
	function delete_t_mst_pegawai($id)
	{
		$this->db->where('c_nip', $id);
		return $this->db->delete('masterdata.t_mst_pegawai');
	}
	
	
	function delete_vfs_users($id)
	{
		$this->db->where('usr', $id);
		return $this->db->delete('masterdata.vfs_users');
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/ 
	
	function aktivasi($where, $item)
	{
		$this->db->where($where);
		return $this->db->update($this->table, $item);
	}
	
	function getMember($id)
	{
		return $this->db->query("SELECT * FROM ".$this->table." m left join masterdata.t_mst_user_login on master_data_user=c_username WHERE m.id = '$id' ");
	}
	
	function getWorkflow()
	{
		$session = $this->session->userdata('user_doc');  
		return $this->db->query("SELECT * FROM workflow w left join workflow_member_type wmt on w.id=wmt.workflow_id where member_type_id='".$session['membertype']."' order by name ");
	}
	
	function getWorkflowbyId($id)
	{
		return $this->db->query("SELECT * FROM workflow where id='$id' order by name ");
	}
	
	function getWorkflowStatebyId($id,$workflow_id)
	{
		return $this->db->query("SELECT * FROM workflow_state where id='$id' and workflow_id='$workflow_id' order by name ");
	}
	
	function getWorkflowbyMemberType($id)
	{
		return $this->db->query("SELECT * FROM workflow w left join workflow_member_type wmt on w.id=wmt.workflow_id where member_type_id='$id' order by name ");
	}
	
	function getUnit()
	{
		// return $this->db->query("SELECT * FROM t_mst_prodi where (nama_prodi not like '%Pindahan%' and nama_prodi not like '%International%' and nama_prodi not like '%Internasional%') order by nama_prodi ");
		return $this->db->query("SELECT * FROM t_mst_prodi order by nama_prodi ");
	} 

    function getSubject($name)
    {
        return $this->db->query("SELECT * FROM knowledge_subject  where lower(name) like'%$name%' order by name limit 20");
    }
	
	function getKnowledgeTypeByWorkflowId($id)
	{
		return $this->db->query("SELECT kt.* from workflow_knowledge_type wkt left join knowledge_type kt on kt.id=wkt.knowledge_type_id 
		where workflow_id='$id' order by name");
	}
	
	function getUploadTypeByWorkflowId($id)
	{
		return $this->db->query("SELECT kt.* from workflow_upload_type wkt left join upload_type kt on kt.id=wkt.upload_type_id 
		where workflow_id='$id' order by title");
	}
	
	function getMasterSubjectByUnitId($id)
	{
		return $this->db->query("SELECT * from master_subject where course_code='$id' order by name");
	}
	
	function getKnowledgeType()
	{
		return $this->db->query("SELECT * FROM knowledge_type where active='1' order by name ");
	}
	
	function add_custom($item,$table)
	{
		$this->db->insert($table, $item);
		return $this->db->insert_id();
	}
	
	function getWorkflowDocumentbyId($id,$type)
	{  
		return $this->db->query("select start_state_id,tmp.NAMA_PRODI,tmp.C_KODE_PRODI,tmp.C_KODE_FAKULTAS,NAMA_FAKULTAS,wd.*,w.id w_id,ks.id ks_id,ks.name ks_name,w.name w_name,ws.name state_name,kt.name jenis_katalog,
		wsp.can_comment,wsp.can_edit_state,wsp.can_edit_attribute,wsp.can_upload,wsp.can_download,wsp.id wsp_id, m.master_data_user,m.master_data_fullname,lecturer_id,lecturer2_id, lecturer.master_data_fullname lecturer_name, lecturer.master_data_number lecturer_number, lecturer.master_data_user lecturer_username, lecturer2.master_data_fullname lecturer2_name,lecturer2.master_data_number lecturer2_number
		, approved.master_data_fullname approved_name,approved.master_data_number approved_number
		from workflow_document wd 
		left join workflow w on w.id=wd.workflow_id
		left join knowledge_type kt on kt.id=wd.knowledge_type_id
		left join member m on m.id=wd.member_id 
		left join member lecturer on lecturer.id=wd.lecturer_id
		left join member lecturer2 on lecturer2.id=wd.lecturer2_id
		left join member approved on approved.id=wd.approved_id
		left join knowledge_subject ks on ks.id=wd.knowledge_subject_id
		left join t_mst_prodi tmp on tmp.C_KODE_PRODI=wd.course_code
		left join t_mst_fakultas tmf on tmf.C_KODE_FAKULTAS=tmp.C_KODE_FAKULTAS
		left join workflow_state_permission wsp on (wsp.state_id=wd.latest_state_id and wsp.member_type_id='$type') 
		left join workflow_state ws on ws.id = wd.latest_state_id where wd.id='$id'");
	}
	
	function getNextState($id)
	{
		return $this->db->query("select ws.* from workflow_transition wt left join workflow_task tsk on tsk.id=task_id left join workflow_state ws on ws.id=next_state_id where wt.state_id='$id' order by name");
	}
	
	function getWorkflowDocumentSubjectByDocumentId($id)
	{
		return $this->db->query("select ms.* from workflow_document_subject left join master_subject ms on ms.id=master_subject_id
where workflow_document_id='$id' order by name");
	}
	
	function getDocumentMasterSubjectByUnitId($id,$wd_id)
	{  
		return $this->db->query("SELECT *,(select count(*)total from workflow_document_subject where workflow_document_id='$wd_id' and master_subject_id=ms.id)total from master_subject ms where course_code='$id' order by name");
	}
	
	function getDocumentFile($id)
	{ 
		return $this->db->query("SELECT wdf.*,ut.name utname, ut.extension utextension from workflow_document_file wdf left join upload_type ut on ut.id=upload_type_id where document_id='$id' order by name");
	}
	
	function getDocumentState($id)
	{ 
		return $this->db->query("select ws.name state_name, master_data_user, master_data_fullname,mt.name, open_date,close_date from workflow_document_state wds
		left join workflow_state ws on ws.id=wds.state_id
		left join member m on m.id=wds.open_by
		left join member_type mt on mt.id=m.member_type_id
		where document_id='$id' order by wds.id asc");
	} 
	
	function getDocumentStateId($wd_id,$state_id)
	{ 
		return $this->db->query("select id from workflow_document_state where document_id='$wd_id' and state_id='$state_id' and close_date is null");
	} 
	
	function getDocumentComment($where) 
	{  
		return $this->db->query("select wc.*,master_data_user,master_data_fullname from workflow_comment wc
		left join member m on member_id=m.id
		$where order by created_at");
	} 

	function delete_document_comment($id)
	{ 
		return $this->db->query("delete from workflow_comment  where id='$id' or parent_id='$id'");
	} 
	function getStateById($id)
	{ 
		return $this->db->query("select * from workflow_state where id='$id'");
	} 
	
	function edit_workflow_document_state($wd_id,$state_id,$user_id)
	{
		return $this->db->query("update workflow_document_state set close_date='".date('Y-m-d H:i:s')."',open_by='$user_id' where document_id='$wd_id' and state_id='$state_id'");
	}
	
	function edit_approved_document_state($wd_id,$state_id,$user_id)
	{ 
		return $this->db->query("update workflow_document_state set allowed_member_id='$user_id' where close_date is null and document_id='$wd_id' and state_id='$state_id'");
	}
	
	function delete_workflow_document_subject($wd_id)
	{
		return $this->db->query("delete from workflow_document_subject  where workflow_document_id='$wd_id'");
	} 
	
	function smk_t_tra_mhs_skripsi($username)
	{
		return $this->db->query("select * from masterdata.smk_t_tra_mhs_skripsi where c_npm='$username'");
	}
	
	function getMembers($username)
	{
		return $this->db->query("select * from member where master_data_user='$username'");
	}
	
	function check_existing_file($wd_id,$location)
	{
		return $this->db->query("select * from workflow_document_file where document_id='$wd_id' and location='$location'");
	}  
	
	function getDocumentSdgs($wd_id)
	{ 
		return $this->db->query("select * from workflow_document_sdgs where document_id='$wd_id'");
	}  
	
	function delete_workflow_document_sdgs($wd_id)
	{
		return $this->db->query("delete from workflow_document_sdgs where document_id='$wd_id'");
	}   
	
	function getWorkflowDocumentMember($id)
	{
		return $this->db->query("select member_id,master_data_user from workflow_document wd left join member m on m.id=wd.member_id where wd.id='$id'");
	} 
	
    public function getTokenNotificationMobile($id){ 
        return $this->db->query("select master_data_token from batik.member where id='$id'");
    }   
	
	function getlecturer($term)
	{ 
		return $this->db->query("SELECT * FROM member WHERE (master_data_user like '%$term%' OR master_data_fullname like '%$term%') and (member_type_id='3' or  member_type_id='4' or  member_type_id='7' or  member_type_id='1') and status='1' order by master_data_fullname limit 0,25 ");
	} 
	
	function getLastBook($type)
	{ 
		return $this->db->query("SELECT code FROM knowledge_item where code like '$type%' order by id desc limit 1 ");
	} 
	
}
?>