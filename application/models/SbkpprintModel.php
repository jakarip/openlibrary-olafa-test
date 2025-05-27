<?php
class SbkpprintModel extends CI_Model {
	
	/**
	 * Constructor
	 */
	 
	private $table 	= 'free_letter';
	private $id		= 'id';
	 
	function __construct()
	{
		parent::__construct();
	}
	
	function getallProdi()
	{ 
		return $this->db->query("select jenis_eproc,c_kode_prodi, nama_prodi,nama_fakultas from t_mst_prodi left join t_mst_fakultas using(c_kode_fakultas) order by jenis_eproc,nama_fakultas, nama_prodi");
	} 
	
	function getKnowledgeItem($id)
	{ 
		return $this->db->query("select * from knowledge_item where id='$id'"); 
	} 
	
	function getProdi($id)
	{ 
		return $this->db->query("select * from t_mst_prodi order by prodi_name"); 
	}  
	
	function getSBKP($where)
	{ 
		return $this->db->query("select * from free_letter f $where order by created_at desc"); 
	}  
	
	function getProdiFak($username)
	{   
	
		return $this->db->query("select *,(penalty-penalty_payment)sisa from (select m.*,SINGKATAN,
		(select sum(amount) total from member m left join rent_penalty rp on m.id=rp.member_id where master_data_user='$username' and m.id
		not in(select username_id from amnesty_denda where username_id=m.id))penalty,
		(select sum(amount) total from member m left join rent_penalty_payment rp on m.id=rp.member_id where master_data_user='$username')penalty_payment,
		(select count(*) from amnesty_denda where username_id=m.id) amnesty
		from member m left join t_mst_prodi tmp on tmp.c_kode_prodi=master_data_course left join t_mst_fakultas tmf on tmf.c_kode_fakultas=tmp.c_kode_fakultas where master_data_user='$username')a"); 
	}   
	
	public function member($username){
        return $this->db->query(" 
		select * from member mm join t_mst_prodi tmp on tmp.c_kode_prodi=master_data_course where (master_data_user like '%$username%' or master_data_number like '%$username%' or master_data_fullname like '%$username%') and member_type_id in (4,5,6,7,9,10,12,25)");
    }
	
	public function sbkpprint($id){
        return $this->db->query(" 
		select * from free_letter fl left join member m on m.id=fl.registration_number left join t_mst_prodi tmp on tmp.c_kode_prodi=master_data_course left join t_mst_fakultas tmf on tmf.c_kode_fakultas=tmp.c_kode_prodi where fl.id='$id'");
    }
	public function auto_inc(){
        return $this->db->query(" 
		SELECT AUTO_INCREMENT 
        FROM information_schema.TABLES 
        WHERE TABLE_SCHEMA = 'batik' 
        AND TABLE_NAME = 'free_letter'");
    }
	
	function getbyid($id)
	{
		$this->db->where($this->id, $id);
		return $this->db->get($this->table);
	} 

    public function save($data){
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
	
	function add($item)
	{
		return $this->db->insert($this->table, $item);
	}
	
	function edit($id, $item)
	{
		$this->db->where($this->id, $id);
		$this->db->update($this->table, $item);
	}
	
	function edit_member($id, $item)
	{
		$this->db->where('id', $id);
		$this->db->update('member', $item);
	}

    public function update($where, $data){
        $this->db->update($this->table, $data, $where);
        return $this->db->affected_rows();
    }  
	
	function getDocument($id)
	{ 
		return $this->db->query("select * from workflow_document where id='$id'"); 
	}  
	function updateDocument($id,$status,$latest_state_id)
	{ 
		
		if ($latest_state_id!='5') $this->db->query("update workflow_document set latest_state_id='$status', created_at='".date('Y-m-d')."',created_by='scheduler_change_status' where id='$id'"); 
		
		return $this->db->query("update workflow_document_state set state_id='$status' where document_id='$id' and id in (select id from (select max(id) id from workflow_document_state wds where wds.document_id='$id' and wds.state_id!='5')a)"); 
	}  
	
	function getLastEprocEdition()
	{ 
		return $this->db->query("select * from journal_eproc_edition order by eproc_edition_id desc limit 1"); 
	} 
	
	function getLastEprocEditionYear()
	{ 
		return $this->db->query("select * from journal_eproc_edition where year='".date('Y')."' group by year"); 
	} 
	
	function getLastEprocYear($year)
	{ 
		return $this->db->query("select * from journal_eproc_edition where year='$year'"); 
	} 
	
	function getEprocEdition()
	{ 
		return $this->db->query("select * from journal_eproc_edition order by eproc_edition_id desc"); 
	} 
	
	function getEprocEditionYear()
	{ 
		return $this->db->query("select * from journal_eproc_edition  group by year order by eproc_edition_id desc"); 
	} 
	
	function getEprocEditionById($id)
	{ 
		return $this->db->query("select * from journal_eproc_edition where eproc_edition_id='$id'"); 
	} 
	
	function totaltamasukbykodejur($jurusan,$datestart,$datefinish)
	{ 
		if ($jurusan!="") $jurusan =  "and m.master_data_course='$jurusan'";
		
		return $this->db->query("select count(id) total from(
		select master_data_user id from workflow_document wdd  
		left join workflow_state_sort_id wss on wss.id_state=wdd.latest_state_id  
		left join member m on wdd.member_id=m.id
		left join workflow_state ws on ws.id=wdd.latest_state_id
		where   wdd.workflow_id='1'  
		and wss.id_ws = 
		(select max(id_ws) from workflow_document wd left join workflow_state_sort_id wid on wid.id_state=wd.latest_state_id  
		where wd.member_id=wdd.member_id and workflow_id='1' ) $jurusan and (wdd.created_at between '$datestart 00:00:00' and '$datefinish 23:59:59')   group by  wdd.member_id order by wdd.member_id ) a");  
	}
	
	function gettamasukbykodejur($jurusan,$datestart,$datefinish)
	{  
		
		return $this->db->query("select master_data_user, master_data_fullname, title,ws.name state_name from workflow_document wdd  
		left join workflow_state_sort_id wss on wss.id_state=wdd.latest_state_id  left join member m on wdd.member_id=m.id
		left join workflow_state ws on ws.id=wdd.latest_state_id
		
		where   wdd.workflow_id='1'  
		and wss.id_ws = 
		(select max(id_ws) from workflow_document wd left join workflow_state_sort_id wid on wid.id_state=wd.latest_state_id  
		where wd.member_id=wdd.member_id and workflow_id='1' ) and m.master_data_course='$jurusan' and (wdd.created_at between '$datestart 00:00:00' and '$datefinish 23:59:59')   group by  wdd.member_id order by wdd.member_id"); 
	}  
	
	function getdocbykodejurandstate($jurusan,$state,$datestart,$datefinish)
	{ 
		
		return $this->db->query("select wdd.id, master_data_user, master_data_fullname, title,(SELECT count( * )
FROM free_letter
WHERE member_number = m.master_data_user
) free_letter, (select count(*) from workflow_document_file where document_id=wdd.id and upload_type_id='83') file
			from workflow_document wdd  left join workflow_state_sort_id wss on wss.id_state=wdd.latest_state_id  
			left join member m on wdd.member_id=m.id 
			
			where    workflow_id='1' and latest_state_id='$state'
			and wss.id_ws = 
			(select max(id_ws) from workflow_document wd left join workflow_state_sort_id wid on wid.id_state=wd.latest_state_id  
			where wd.member_id=wdd.member_id and workflow_id='1') and m.master_data_course='$jurusan' and (wdd.created_at between '$datestart 00:00:00' and '$datefinish 23:59:59')    group by wdd.member_id order by free_letter desc
			");
	}
	 
	
	function totaldocbykodejurandstate($jurusan,$state,$datestart,$datefinish)
	{ 
		if ($jurusan!="") $jurusan =  "and m.master_data_course='$jurusan'";
		return $this->db->query("select count(id) total from (select wdd.id
			 from workflow_document wdd  left join workflow_state_sort_id wss on wss.id_state=wdd.latest_state_id  
			 left join member m on wdd.member_id=m.id 
			 left join workflow_document_file wf on wdd.id=document_id 
			
			where    workflow_id='1' and latest_state_id='$state'
			and wss.id_ws = 
			(select max(id_ws) from workflow_document wd left join workflow_state_sort_id wid on wid.id_state=wd.latest_state_id  
			where wd.member_id=wdd.member_id and workflow_id='1'  ) $jurusan and (wdd.created_at between '$datestart 00:00:00' and '$datefinish 23:59:59')   group by wdd.member_id order by wdd.member_id )a
			");
	} 
	
	function getjurnalmasukbykodejur($jurusan,$datestart,$datefinish)
	{ 
		return $this->db->query("select wdd.id, master_data_user, master_data_fullname, title
			  from workflow_document wdd  left join workflow_state_sort_id wss on wss.id_state=wdd.latest_state_id  
			 left join workflow_document_file wf on wdd.id=document_id 
			 left join member m on wdd.member_id=m.id 
			
			where    workflow_id='1'  and wf.upload_type_id='16'
			and wss.id_ws = 
			(select max(id_ws) from workflow_document wd left join workflow_state_sort_id wid on wid.id_state=wd.latest_state_id  
			where wd.member_id=wdd.member_id and workflow_id='1') and m.master_data_course='$jurusan' and (wdd.created_at between '$datestart 00:00:00' and '$datefinish 23:59:59')    group by wdd.member_id order by wdd.member_id
			");
	}
	
	function totaljurnalmasukbykodejur($jurusan,$datestart,$datefinish)
	{ 
		if ($jurusan!="") $jurusan =  "and m.master_data_course='$jurusan'";
		return $this->db->query("select count(id) total from (select wdd.id
			 from workflow_document wdd  left join workflow_state_sort_id wss on wss.id_state=wdd.latest_state_id  
			 left join workflow_document_file wf on wdd.id=document_id 
			 left join member m on wdd.member_id=m.id 
			
			where    workflow_id='1'  and wf.upload_type_id='16'
			and wss.id_ws = 
			(select max(id_ws) from workflow_document wd left join workflow_state_sort_id wid on wid.id_state=wd.latest_state_id  
			where wd.member_id=wdd.member_id and workflow_id='1') $jurusan and (wdd.created_at between '$datestart 00:00:00' and '$datefinish 23:59:59')   group by wdd.member_id order by wdd.member_id )a
			");
	} 
	
	function getjurnalpublishbykodejur($jurusan,$datestart,$datefinish)
	{
		return $this->db->query("select nim master_data_user, nama master_data_fullname, judul title,  eproc_id id  from journal_eproc where kode_prodi='$jurusan' and created_date between '$datestart 00:00:00' and '$datefinish 23:59:59'  and file!='' group by nim order by nama ");
	} 
	
	function totaljurnalpublishbykodejur($jurusan,$datestart,$datefinish)
	{ 
		return $this->db->query("select count(id) total from (select eproc_id id  from journal_eproc where kode_prodi='$jurusan' and created_date between '$datestart 00:00:00' and '$datefinish 23:59:59' and file!=''group by nim order by nim)a "); 
	}  
	
 
	function getarchivejurnalstatusbykodejur($jurusan,$state,$datestart,$datefinish)
	{ 
		$where = "";
		if ($jurusan!="") $where = "and m.master_data_course='$jurusan'" ;
		if($state=='5') {
			
					return $this->db->query("select nama_fakultas, nama_prodi,master_data_user, ki.code, 
							ki.id,ki.title,master_data_fullname,editor,latest_state_id,wdd.updated_by
							from workflow_document wdd  
							left join knowledge_item ki on ki.title=wdd.title
							left join workflow_state_sort_id wss on wss.id_state=wdd.latest_state_id  
							left join member m on wdd.member_id=m.id 
							left join workflow_document_state wds on wds.document_id=wdd.id
							left join t_mst_prodi tp on C_KODE_PRODI=m.master_data_course
							left join t_mst_fakultas tf on tf.C_KODE_FAKULTAS=tp.C_KODE_FAKULTAS
							
							where     wdd.workflow_id='1' and (wdd.created_at 
							between '$datestart 00:00:00' and '$datefinish 23:59:59') and state_id='5'
							  $where
							 group by wdd.member_id order by nama_fakultas,nama_prodi");
		 
	 
		}
		else {  
				return $this->db->query("select nama_fakultas, nama_prodi,master_data_user, ki.code,ki.id, 
							ki.title,master_data_fullname,editor,latest_state_id,wdd.updated_by
							from workflow_document wdd  
							left join knowledge_item ki on ki.title=wdd.title
							left join workflow_state_sort_id wss on wss.id_state=wdd.latest_state_id  
							left join member m on wdd.member_id=m.id 
							left join workflow_document_state wds on wds.document_id=wdd.id
							left join t_mst_prodi tp on C_KODE_PRODI=m.master_data_course
							left join t_mst_fakultas tf on tf.C_KODE_FAKULTAS=tp.C_KODE_FAKULTAS
							
							where     wdd.workflow_id='1' and (wdd.created_at 
							between '$datestart 00:00:00' and '$datefinish 23:59:59')  and state_id='$state' and latest_state_id='5'
							  $where AND wds.id = (
			SELECT
				max(id)
			FROM
				workflow_document_state  
			WHERE
				document_id = wdd.id and state_id!='5'
				)
							 group by wdd.member_id order by nama_fakultas,nama_prodi");
		}
	} 
	
	function getarchivejurnalstatusbykodejurperitem($jurusan,$datestart,$datefinish,$id)
	{ 
		$where = "";
		if($jurusan!="") $where = " and m.master_data_course='$jurusan'";
		$add = "and state_id='53'";
		if ($id!="") $add = " and ki.id='$id'";
		return $this->db->query("select nama_fakultas, nama_prodi,master_data_user, ki.code,ki.id, 
							ki.title,master_data_fullname,editor,latest_state_id,wdd.updated_by
							from workflow_document wdd  
							left join knowledge_item ki on ki.title=wdd.title
							left join workflow_state_sort_id wss on wss.id_state=wdd.latest_state_id  
							left join member m on wdd.member_id=m.id 
							left join workflow_document_state wds on wds.document_id=wdd.id
							left join t_mst_prodi tp on C_KODE_PRODI=m.master_data_course
							left join t_mst_fakultas tf on tf.C_KODE_FAKULTAS=tp.C_KODE_FAKULTAS
							
							where     wdd.workflow_id='1' and (wdd.created_at 
							between '$datestart 00:00:00' and '$datefinish 23:59:59')  and latest_state_id='5'
							 $where $add AND wds.id = (
			SELECT
				max(id)
			FROM
				workflow_document_state  
			WHERE
				document_id = wdd.id and state_id!='5'
				)
							 group by wdd.member_id order by nama_fakultas,nama_prodi");
	}
	
	function generateEproc($jurusan,$datestart,$datefinish)
	{ 
	
		return $this->db->query("select nama_fakultas, nama_prodi,master_data_user, ki.code,ki.id, 
							ki.title,master_data_fullname,editor,latest_state_id,wdd.updated_by
							from workflow_document wdd  
							left join knowledge_item ki on ki.title=wdd.title
							left join workflow_state_sort_id wss on wss.id_state=wdd.latest_state_id  
							left join member m on wdd.member_id=m.id 
							left join workflow_document_state wds on wds.document_id=wdd.id
							left join t_mst_prodi tp on C_KODE_PRODI=m.master_data_course
							left join t_mst_fakultas tf on tf.C_KODE_FAKULTAS=tp.C_KODE_FAKULTAS 
							where  ki.author=m.master_data_fullname and  wdd.workflow_id='1' and (wdd.created_at 
							between '$datestart 00:00:00' and '$datefinish 23:59:59')  and latest_state_id='5'
							  and m.master_data_course='$jurusan' and state_id='53' AND wds.id = (
			SELECT
				max(id)
			FROM
				workflow_document_state  
			WHERE
				document_id = wdd.id and state_id!='5'
				) and ki.title is not null
							 group by wdd.member_id order by nama_fakultas,nama_prodi,ki.title");
	}
	
	function getJurusanPerFakultas($id)
	{
		return $this->db->query("select * from t_mst_prodi where c_kode_fakultas in ($id) order by c_kode_fakultas");
	}  
	 
	function totalarchivejurnalstatusbykodejur($jurusan,$state,$datestart,$datefinish)
	{ 
		$where = "";
		if ($jurusan!="") $where = "and m.master_data_course='$jurusan'" ;
		return $this->db->query("select count(nama_fakultas) total from (select nama_fakultas, nama_prodi,master_data_user, ki.code, 
					ki.title,master_data_fullname,editor,latest_state_id,wdd.updated_by
					from workflow_document wdd  
					left join workflow_document_file wf on wdd.id=document_id 
					left join knowledge_item ki on ki.title=wdd.title
					left join workflow_state_sort_id wss on wss.id_state=wdd.latest_state_id  
					left join member m on wdd.member_id=m.id 
					left join workflow_document_state wds on wds.document_id=wdd.id
					left join t_mst_prodi tp on C_KODE_PRODI=m.master_data_course
					left join t_mst_fakultas tf on tf.C_KODE_FAKULTAS=tp.C_KODE_FAKULTAS
					
					where     wdd.workflow_id='1' and (wdd.created_at 
					between '$datestart 00:00:00' and '$datefinish 23:59:59') and state_id='$state' and latest_state_id='5'
					  $where AND wds.id = (
	SELECT
		max(id)
	FROM
		workflow_document_state  
	WHERE
		document_id = wdd.id and state_id!='5'
)
					 group by wdd.member_id order by nama_fakultas,nama_prodi)a "); 
	}  
	
	function listeprocperprodi($jurusan,$state,$datestart,$datefinish)
	{ 
		return $this->db->query("select nama_fakultas, nama_prodi,
					ki.title title,master_data_fullname,editor
					from workflow_document wdd  
					left join knowledge_item ki on ki.title=wdd.title
					left join workflow_state_sort_id wss on wss.id_state=wdd.latest_state_id  
					left join member m on wdd.member_id=m.id 
					left join workflow_document_state wds on wds.document_id=wdd.id
					left join t_mst_prodi tp on C_KODE_PRODI=m.master_data_course
					left join t_mst_fakultas tf on tf.C_KODE_FAKULTAS=tp.C_KODE_FAKULTAS
					
					
					where     wdd.workflow_id='1' and (wdd.created_at 
					between '2015-08-16 00:00:00' and '2015-11-29 23:59:59') and state_id='53' and latest_state_id='5'
					  and tp.nama_prodi='$jurusan' AND wds.id = (
	SELECT
		max(id)
	FROM
		workflow_document_state  
	WHERE
		document_id = wdd.id and state_id!='5'
)
					 group by wdd.member_id order by nama_fakultas,nama_prodi"); 
	}
	 
	function getState($id)
	{  
		
		return $this->db->query("select * from workflow_state where id='$id'"); 
	}  				
	 
	function getmkbykodejur($jurusan)
	{
		return $this->db->query("select ms.code kode_mk,ms.semester,ms.name nama_mk, SUBSTR(ms.code,-1) sks, (SELECT COUNT(*) FROM knowledge_item_subject WHERE master_subject_id = ms.id) as jmljudul,ms.id id_kuliah FROM t_mst_prodi tp left join master_subject ms on tp.c_kode_prodi=ms.course_code WHERE tp.c_kode_prodi ='$jurusan' AND ms.curriculum_code = '2014' ORDER BY ms.semester,ms.name");
	}
	
	function getjurbykodejur($jurusan)
	{
		return $this->db->query("select c_kode_prodi, nama_prodi from t_mst_prodi where c_kode_prodi = '$jurusan'"); 
	} 
	
	function getbukuref($kode, $where, $limit)
	{
		return $this->db->query("select ki.code kode_buku, cc.code klasifikasi, ki.title, ki.author,eks, (select count(*) from knowledge_stock ks where status='1' and knowledge_item_id=ki.id) tersedia 
		from knowledge_item_subject kis 
		left join (SELECT ki.id,ki.code, ki.title,ki.classification_code_id,ki.author, count(ki.id) eks
		FROM knowledge_item ki
		LEFT JOIN knowledge_stock ks ON ki.id = ks.knowledge_item_id group by ki.id) ki on kis.knowledge_item_id=ki.id 
		left join classification_code cc on cc.id=ki.classification_code_id
		 left join master_subject ms on kis.master_subject_id=ms.id
		where $where kis.master_subject_id='$kode' and ms.curriculum_code = '2014' order by ki.title
		");
	}			
	
	
	function getEprocList()
	{
		return $this->db->query("select * from journal_eproc_list order by list_name"); 
	}  		
	
	
	function getEprocListById($id)
	{
		return $this->db->query("select * from journal_eproc_list where list_id='$id'"); 
	}  
	
	function getProdiByEprocList($filter)
	{ 
		return $this->db->query("select jenis_eproc,c_kode_prodi, nama_prodi,nama_fakultas from t_mst_prodi left join t_mst_fakultas using(c_kode_fakultas) where c_kode_fakultas in ($filter) order by jenis_eproc,nama_fakultas, nama_prodi");
	}  
	
	function getArticleEprocByIdEdition($id,$list_id)
	{  
		return $this->db->query("select * from journal_eproc_article where jea_id_eproc_edition='$id' and jea_id_list='$list_id'"); 
	} 
	
}
?>