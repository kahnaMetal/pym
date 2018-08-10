<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login_model extends CI_Model
{
	public function construct()
	{
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

	public function login($username,$password){
		$this->db->where('user', trim(strtolower($username), " \t\n\r\0\x0B"));
		$this->db->where('passwd', $password);
		$query = $this->db->get('users');
		$result = $query->row();		
		$this->session->set_userdata('user', $result);
		if($query->num_rows()>0){
			$data = array(
		        'tipo' => 'login',
		        'app' => 0,
		        'user' => $result->id_user,
		        'detalle' => 'Acceso satisfactorio a la plataforma. ',
		        'fecha' => date('Y-m-d H:i:s'),
		        'ip' => $this->getRealIP(),
		        'agent' => $_SERVER['HTTP_USER_AGENT']
		    );
		    $this->db->insert('apps_logs', $data);
			return $result->id_user;
		} else {
			$data = array(
		        'tipo' => 'login',
		        'app' => 0,
		        'detalle' => 'Intento fallido para acceder a la plataforma para usuario '.$username.'.',
		        'fecha' => date('Y-m-d H:i:s'),
		        'ip' => $this->getRealIP(),
		        'agent' => $_SERVER['HTTP_USER_AGENT']
		    );
		    $this->db->insert('apps_logs', $data);
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