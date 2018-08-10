<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Gastos_model extends CI_Model
{
	public function construct()
	{
		parent::__construct();
	}

	public function usuarios($like){
		$user = $this->db->query("
			SELECT 
				id_user AS id, 
				CONCAT( 
					apellidos, 
					', ', 
					nombres, 
					' (USUARIO: ',
					user,
					')' 
				) AS text
			FROM users
			WHERE fk_rol != '1' 
				AND (
					apellidos LIKE '%".$like."%' ESCAPE '!'
					OR
					nombres LIKE '%".$like."%' ESCAPE '!'
					OR
					user LIKE '%".$like."%' ESCAPE '!'
				)
			ORDER BY 
				apellidos,
				nombres
			ASC
		");
		if($user->num_rows() > 0 ){
            return $user->result();
        } 
	}

	public function ingresarGasto($d){
		$s = explode(':', $d['sess']);
		$data = array(
	        'motivo_gasto' => $d['motivo'],
	        'valor_gasto' => $d['valor'],
	        'desc_gasto' => trim($d['desc'], " \t\n\r\0\x0B"),
	        'fehca_gasto' => date('Y-m-d'),
	        'hora_gasto' => date('H:i:s'),
	        'usr_fk' => $d['user']//$s[0]
		);
		if($this->db->insert('gastos', $data)){
			return 1;
		} else {
			return 2;
		}
	}
}
