<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Logs_model extends CI_Model{

	public function __construct(){
        parent::__construct();
	}

	private function getRealIP(){
    	if (isset($_SERVER["HTTP_CLIENT_IP"])){
        	return $_SERVER["HTTP_CLIENT_IP"];
    	} elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
        	return $_SERVER["HTTP_X_FORWARDED_FOR"];
    	} elseif (isset($_SERVER["HTTP_X_FORWARDED"])){
        	return $_SERVER["HTTP_X_FORWARDED"];
    	} elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])){
        	return $_SERVER["HTTP_FORWARDED_FOR"];
    	} elseif (isset($_SERVER["HTTP_FORWARDED"])){
        	return $_SERVER["HTTP_FORWARDED"];
    	} else {
        	return $_SERVER["REMOTE_ADDR"];
    	}
	}

	public function consultarCiudadFullTexto($id, $stringCity = ''){
		$this->db->select('nameCity, parentCity');
		$this->db->where('statusCity', 1);
		$this->db->where('idCity', $id);
		$ciudad = $this->db->get('cities');
		$v = $ciudad->row();
		if($ciudad->num_rows() > 0 ){
			if($stringCity==''){
				$stringCity .= $v->nameCity;
			} else {
				$stringCity .= ', '.$v->nameCity;
			}		
			if($v->parentCity == 0){
				return $stringCity;
			} else {
				return $this->consultarCiudadFullTexto($v->parentCity, $stringCity);
			}
		} else {
			return FALSE;
		}
	}

	public function maxId($r, $t){
		$this->db->select('MAX('.$r.') AS id');
		$res1 = $this->db->get($t);
		return $res1->row()->id;
	}

	public function iLog($t, $m, $s, $z, $r, $u){
		$now = date('Y-m-d H:i:s');
		$data = array(
				'typeLog' => $t,
				'detailLog' => $m,
				'dateLog' => $now,
				'ipLog' => $this->getRealIP(),
				'agentLog' => $_SERVER['HTTP_USER_AGENT'],
				'statusLog' => $s,
				'table_LogFK' => $z,
				'row_LogFK' => $r,
				'user_LogFK' => $u,
		);
		$this->db->insert('logs', $data);
	}

}
