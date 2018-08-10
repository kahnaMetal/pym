<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Validate_model extends CI_Model
{
	public function construct()
	{
		parent::__construct();
	}

	public function login($login){
		$l = explode(':', $login);
		$this->db->where('id_user', $l[0]);
		$query = $this->db->get('users');
		$result = $query->row();
		if($query->num_rows()>0 && md5($result->user)==$l[1]){
			return $result;
		} else {
			return FALSE;
		}
	}

	/*public function insert_comment($nombre,$email,$asunto,$mensaje)
	{
		$hora = date("H:i:s");
 
		$fecha = date('Y-m-d');
		
		 $data = array(
            'nombre' => $nombre,
            'email' => $email,
            'asunto' => $asunto,
            'mensaje' => $mensaje,
            'hora' => $hora,
            'fecha' => $fecha
        );
        return $this->db->insert('mensajes', $data);
	}*/
}