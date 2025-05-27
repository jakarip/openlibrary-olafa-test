<?php
class BebasPustakaModel extends CI_Model {
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	} 
	
	 
	public function member($username){
		$username = addslashes($username); 
        return $this->db->query(" 
		select *, mm.id memberid  from member mm join t_mst_prodi tmp on tmp.c_kode_prodi=master_data_course where (master_data_user like '%$username%' or master_data_fullname like '%$username%' or master_data_number like '%$username%') and member_type_id in (5,6,9,10,12,25) order by master_data_user");
	}
	
	public function member_lecturer($username){
		$username = addslashes($username); 
        return $this->db->query(" 
		select *, mm.id memberid  from member mm join t_mst_prodi tmp on tmp.c_kode_prodi=master_data_course where (master_data_user like '%$username%' or master_data_fullname like '%$username%' or master_data_number like '%$username%') and member_type_id in (5,6,9,10,12,25) and status='1' order by master_data_user");
    }
	
	public function dokumen($username){ 
        return $this->db->query(" 
		select wd.id,title,ws.name,master_data_user,master_data_fullname,(select count(*) from workflow_document_file where document_id=wd.id)jml,latest_state_id from workflow_document wd
		join member wdf on wdf.id=wd.member_id 
		join workflow_state ws on ws.id=wd.latest_state_id
		where wd.member_id in ($username) and wd.workflow_id='1' order by master_data_user,latest_state_id");
    }
	
	public function getDocument($id){
        return $this->db->query(" 
		select ut.title,wdf.id,extension,location,master_data_user from workflow_document wd
			join workflow_document_file wdf on wd.id=wdf.document_id 
			join member mm on mm.id=wd.member_id 
			join upload_type ut on ut.id=wdf.upload_type_id 
		where wd.id='$id' order by ut.title");
    }
	
	
	
	public function get_file($id){
        return $this->db->query("  
			select location,master_data_user from workflow_document_file wdf 
			join workflow_document wd on wd.id=wdf.document_id 
			join member mm on mm.id=wd.member_id where wdf.id='$id'");
    }
	
	function delete_file($id)
	{
		return $this->db->query("DELETE FROM workflow_document_file WHERE id = '$id'");
	}
	
	function delete_document($id)
	{
		$this->db->query("DELETE FROM workflow_document WHERE id = '$id'");
		$this->db->query("DELETE FROM workflow_document_state WHERE document_id = '$id'");
		$this->db->query("DELETE FROM workflow_document_subject WHERE workflow_document_id = '$id'");
		return $this->db->query("DELETE FROM workflow_document_file WHERE document_id = '$id'");
	}
	
}
?>