<?php
class CustomModel extends CI_Model 
{ 
	
	/**
	 * Constructor
	 */
	private $mn 	= '';
	function __construct()
	{
		parent::__construct();
		$this->mn = $this->load->database('menu', TRUE);
	}	
	
	function getParentMenu()
	{ 
		return $this->mn->query("select menu_id,menu_name_ina,menu_name_eng,menu_icon,menu_url,
		(select count(*) total from md_menu where menu_parent_id=ma.menu_id and menu_display='yes') child, 
		(SELECT group_concat(menu_url SEPARATOR ', ') from md_menu where menu_parent_id=ma.menu_id group by menu_parent_id )url_child 
		from md_menu ma 
		left join md_ugmenu on um_menu_id=menu_id
		left join md_usergroup on ug_id=um_ug_id
		where ug_name='".$this->session->userdata('usergroup')."' and menu_parent_id is null and menu_display='yes' 
		order by menu_display_order, menu_id");
	}
	
	function getChildMenu($parent)
	{
		return $this->mn->query("select menu_id,menu_name_ina,menu_name_eng,menu_icon,menu_url from md_menu 
		left join md_ugmenu on um_menu_id =menu_id
		left join md_usergroup on ug_id=um_ug_id
		where ug_name='".$this->session->userdata('usergroup')."' and menu_parent_id='".$parent."' and menu_display='yes'  
		order by menu_display_order");
	}
	
	
	
	function getParentMenuExceptCoreMenu()
	{
		return $this->mn->query("select * from md_menu where menu_id > 15 and menu_parent_id is null");
	} 
	
	function getUserGroup()
	{
		return $this->mn->query("select * from md_uguser 
		left join md_usergroup on ug_id=uu_ug_id 
		left join md_user on user_id=uu_user_id 
		where user_username='".$this->session->userdata('username')."' and ug_name!='".$this->session->userdata('usergroup')."' 
		order by ug_name");
	}
	
	function getUserGroupDefaultMenu($usergroup="")
	{
		if (empty($usergroup)) $usergroup = $this->session->userdata('usergroup'); 
		return $this->mn->query("select * from (select *, (select count(*) total from md_menu where menu_parent_id=ma.menu_id) child from md_ugmenu 
		left join md_menu ma on menu_id=um_menu_id
		left join md_usergroup on ug_id=um_ug_id 
		where ug_name='$usergroup' and menu_url is not null and menu_url!='') a 
		where child=0 order by menu_parent_id,menu_display_order limit 1
		");
		
	}  
	
	function checkCurrentMenubyUserGroup($url,$usergroup)
	{	  
		return $this->mn->query("select * from md_ugmenu 
		left join md_menu on menu_id=um_menu_id
		left join md_usergroup on ug_id=um_ug_id
		where menu_url='$url' and ug_name='$usergroup'");
		
	} 
	
	function getCurrentMenuName($url)
	{
		return $this->mn->query("select *,(select menu_name_eng from md_menu where menu_id=mm.menu_parent_id) menu_eng,(select menu_name_ina from md_menu where menu_id=mm.menu_parent_id) menu_ina
		from md_menu mm where menu_url='$url'");
	} 
	
	function getLang($var)
	{
		return $this->mn->query("select * from md_language where lang_var='$var'");
	} 	 
	
}
?>