<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Base_model extends CI_Model
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

	public function ingresarBase($d){
		$user = $this->db->query("
			SELECT 
				id_base
			FROM bases
			WHERE usr_fk = '".$d['user']."'
		");
		if($user->num_rows() > 0 ){
            return 3;
        } else {
        	$data = array(
		        'valor_base' => $d['base'],
		        'fecha_base' => date('Y-m-d'),
		        'usr_fk' => $d['user']//$s[0]
			);
			if($this->db->insert('bases', $data)){
				return 1;
			} else {
				return 2;
			}
        }
	}
}
