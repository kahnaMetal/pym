<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ubicaciones_model extends CI_Model
{
	public function construct(){
		parent::__construct();
	}

	public function hijos($p){
		$this->db->select('
			idCity,
			nameCity
		');
		$this->db->where('statusCity', '1');
		$this->db->where('parentCity', $p);
		$this->db->order_by('nameCity', 'ASC');
		$query = $this->db->get('cities');
		if($query->num_rows() > 0 ){
        return $query;
    } else {
    		return FALSE;
    }
	}

}
