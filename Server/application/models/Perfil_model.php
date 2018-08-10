<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Perfil_model extends CI_Model
{
	public function construct()
	{
		parent::__construct();
	}

	public function changePasswd($d){
		$de = explode(':', $d['sess']);
		$data = array(
	        'passwd' => md5($d['clave'])
		);
		$this->db->where('id_user', $de[0]);
		if($this->db->update('users', $data)){
			return 1;
		} else {
			return 2;
		}
	}

	public function updateUser($d){
		$de = explode(':', $d['sess']);
		$data = array(
	        'nombres' => $d['nombres'],
	        'apellidos' => $d['apellidos'],
	        'email' => $d['email'],
	        'notas' => $d['notas']
		);
		$this->db->where('id_user', $de[0]);
		if($this->db->update('users', $data)){
			return 1;
		} else {
			return 2;
		}
	}
}
