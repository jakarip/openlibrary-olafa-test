<?php
class Questionnaire_model extends CI_Model 
{
	
	private $table 			= 'questionnaire';
	private $id    			= 'q_id';
	private $table_detail 	= 'questionnaire_question';
	private $id_detail    	= 'qq_id';
	
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
		return $this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM ".$this->table." 
								 $param[where] $param[order] $param[limit]");
	}
	
	function dtfiltered()
	{
		$result = $this->db->query('SELECT FOUND_ROWS() as jumlah')->row();
		
		return $result->jumlah;
	}
	
	function dtcount()
	{
		return $this->db->count_all($this->table);
	}
	
	function dtquery_detail($param)
	{ 
		return $this->db->query("SELECT SQL_CALC_FOUND_ROWS a.*, ( select @rownum := @rownum + 1 from ( select @rownum := 0 ) d2 ) 
          as rownumber, (select count(*) from questionnaire_options where qo_id_question=qq_id and qo_active='1')total FROM ".$this->table_detail." a  $param[where] $param[order] $param[limit]");
	} 
	
	function dtcount_detail()
	{
		return $this->db->count_all($this->table_detail);
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
	
	function getbyid_detail($id)
	{
		return $this->db->query("SELECT * from questionnaire_question qq left join questionnaire q on q_id=qq_id_questionnaire WHERE qq_id='$id'");
	}
	
	function getbyid_option($id)
	{ 
		return $this->db->query("SELECT * from questionnaire_options qo WHERE qo_id_question='$id' order by qo_id");
	}
	
	function add($item)
	{
		$this->db->insert($this->table, $item);
		return $this->db->insert_id();
	}
	
	function add_detail($item)
	{
		$this->db->insert($this->table_detail, $item);
		return $this->db->insert_id();
	}
	
	function add_option($item)
	{
		$this->db->insert('questionnaire_options', $item);
		return $this->db->insert_id();
	}
	
	function edit($id, $item)
	{
		$this->db->where($this->id, $id);
		return $this->db->update($this->table, $item);
	}
	
	function edit_detail($id, $item)
	{
		$this->db->where($this->id_detail, $id);
		return $this->db->update($this->table_detail, $item);
	}
	
	function edit_option($id, $item)
	{
		$this->db->where('qo_id', $id);
		return $this->db->update('questionnaire_options', $item);
	}
	
	function delete($id)
	{
		$this->db->where($this->id, $id);
		return $this->db->delete($this->table);
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
	
	function getalls()
	{
		return $this->db->query("SELECT *,
			(SELECT GROUP_CONCAT(qo_id ORDER BY qo_id SEPARATOR '|')  FROM questionnaire_options qt WHERE qt.qo_id_question=qq_id and qo_active='1') pilihan_id,
			(SELECT GROUP_CONCAT(qo_option ORDER BY qo_id SEPARATOR '|') FROM questionnaire_options qt WHERE qt.qo_id_question=qq_id and qo_active='1') pilihan,
			(SELECT GROUP_CONCAT(qo_text ORDER BY qo_id SEPARATOR '|') FROM questionnaire_options qt WHERE qt.qo_id_question=qq_id and qo_active='1') text
			FROM questionnaire qo LEFT JOIN questionnaire_question qq ON qq_id_questionnaire=qo.q_id WHERE q_active='1' and qq_active='1'");
	}

    function get_response($id)
    {
        $this->db->where('qr_id_user', $id);
        return $this->db->get('questionnaire_response');
    } 

    function edit_response($id, $item)
    {
       $this->db->where('qr_id', $id);
       return $this->db->update('questionnaire_response', $item);
    }

    function add_response($item)
    {
        return $this->db->insert('questionnaire_response', $item);
    } 
	
	function edit_status($item)
	{ 
		return $this->db->update($this->table, $item);
	}
}
?>