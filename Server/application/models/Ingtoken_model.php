<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ingtoken_model extends CI_Model
{
	public function construct()
	{
		parent::__construct();
	}

	public function credito($id){
		$this->db->where('token', $id);
		$this->db->where('status', '3');
		$query = $this->db->get('creditos');
		$result = $query->row();
		if($query->num_rows()>0){
			return $result;
		} else {
			return FALSE;
		}
	}

	public function registerToken($token){
		$data = array(
	        'status' => '1'
		);
		$this->db->where('token', $token);
		if($this->db->update('creditos', $data)){
			return 1;		
		} else {
			return 3;
		}
	}
}
