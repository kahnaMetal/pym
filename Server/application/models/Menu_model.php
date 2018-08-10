<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Menu_model extends CI_Model
{
	public function construct()
	{
		parent::__construct();
	}

	public function menu($rol){
		$this->db->join('apps_permisos','id_menu_item = app_fk','right');
		$this->db->where('rol_fk',$rol);
		$this->db->where('ver','Y');
		$this->db->order_by("parent", "asc");
		$this->db->order_by("order_items", "asc");
		$query = $this->db->get('apps_menu');
		if($query->num_rows()>0){
			return $query->result();
		} else {
			return FALSE;
		}	
	}
}