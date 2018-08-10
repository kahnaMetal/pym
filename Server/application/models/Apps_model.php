<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Apps_model extends CI_Model{

	public function __construct()
	{
        parent::__construct();        
	}

	public function apps(){		
		$this->db->join('apps_permisos','id_menu_item = id_app_fk','right');
		$this->db->where('cc_fk',$this->session->userdata('user')->cc);
		$this->db->order_by("parent", "asc");
		$this->db->order_by("order_items", "asc");
		$consulta = $this->db->get('apps_menu');
		return $consulta->result();		
	}

}
