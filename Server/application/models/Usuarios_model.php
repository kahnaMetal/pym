<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Usuarios_model extends CI_Model
{
	public function construct()
	{
		parent::__construct();
	}

	public function roles(){
		$this->db->where('id_rol !=', 1);
		$query = $this->db->get('roles');
		$result = $query->result();
		if($query->num_rows()>0){
			return $result;
		} else {
			return FALSE;
		}
	}

	public function users(){
		$this->db->where('id_user !=', 1);
		$this->db->order_by('apellidos, nombres, user', 'ASC');
		$query = $this->db->get('users');
		$result = $query->result();
		if($query->num_rows()>0){
			return $result;
		} else {
			return FALSE;
		}
	}

	public function rolPermisos($id){
		$this->db->where('rol_fk', $id);
		$this->db->join('apps_menu','id_menu_item = app_fk','left');
		$this->db->order_by('id_menu_item', 'ASC');
		$query = $this->db->get('apps_permisos');
		$result = $query->result();
		if($query->num_rows()>0){
			return $result;
		} else {
			return FALSE;
		}
	}

	public function detalleUsuario($id){
		$this->db->where('id_user', $id);
		$this->db->select('id_user, user, nombres, apellidos, email, notas, fk_rol, status');
		$query = $this->db->get('users');
		$result = $query->row();
		if($query->num_rows()>0){
			return $result;
		} else {
			return FALSE;
		}
	}

	public function ingresarUser($d){
		$this->db->where('user', trim(strtolower($d['usuario']), " \t\n\r\0\x0B"));
		$this->db->select('id_user');
		$query = $this->db->get('users');
		$result = $query->result();
		if($query->num_rows()>0){
			return 2;
		} else {
			$data = array(
		        'nombres' => $d['nombres'],
		        'apellidos' => $d['apellidos'],
		        'user' => trim(strtolower($d['usuario']), " \t\n\r\0\x0B"),
		        'passwd' => md5($d['reclave']),
		        'create' => date('Y-m-d H:i:s'),
		        'email' => $d['email'],
		        'notas' => trim($d['notas'], " \t\n\r\0\x0B"),
		        'fk_rol' => $d['rol'],
		        'status' => $d['estado']
			);
			if($this->db->insert('users', $data)){
				return 1;
			} else {
				return 3;
			}
		}
	}

	public function actualizarUsuario($d){
		$data = array(
	        'nombres' => $d['nombres'],
	        'apellidos' => $d['apellidos'],
	        'email' => $d['email'],
	        'notas' => trim($d['notas'], " \t\n\r\0\x0B"),
	        'fk_rol' => $d['rol'],
	        'status' => $d['estado']
		);
		$this->db->where('id_user', $d['id_user']);
		if($this->db->update('users', $data)){
			return 1;
		} else {
			return 3;
		}
	}

	public function ingresarRol($d){
		$ok = 0;
		$this->db->where('rol', trim(ucwords(strtolower($d['rol'])), " \t\n\r\0\x0B"));
		$this->db->select('id_rol');
		$query = $this->db->get('roles');
		$result = $query->result();
		if($query->num_rows()>0){
			return 2;
		} else {
			$data = array(
		        'rol' => trim(ucwords(strtolower($d['rol'])), " \t\n\r\0\x0B"),
		        'rol_create' => date('Y-m-d H:i:s'),
		        'status' => '1'
			);
        	if($this->db->insert('roles', $data)){
        		$this->db->select_max('id_rol');            
            	$res1 = $this->db->get('roles');
                $max = $res1->row()->id_rol;
                foreach ($d as $clave => $valor) {
				    if($clave!='rol' && $clave!='sess'){
				    	$data = array(
		        			'app_fk' => trim($clave, "p-"),
					        'rol_fk' => $max
						);
						for($i=0;$i<count($valor);$i++){
							if($valor[$i]!=NULL && $valor[$i]!=''){
								$data[$valor[$i]]='Y';
							} else {
								$data['ver']='N';
							}
						}
						if($this->db->insert('apps_permisos', $data)){
			        		$ok = 1;
			        	} else {		    	
					    	return 4;		    	
					    }
				    }
				}
				if($ok==1){
					$tablero1 = array(
	        			'app_fk' => 1,
				        'rol_fk' => $max,
				        'ver' => 'Y'
					);
					$tablero2 = array(
	        			'app_fk' => 6,
				        'rol_fk' => $max,
				        'ver' => 'Y'
					);
					$this->db->insert('apps_permisos', $tablero1);
					$this->db->insert('apps_permisos', $tablero2);
					return 1;
				} else {
					return 4; 
				}	
        	} else {		    	
		    	return 3;		    	
		    }	
		}	
	}

	public function actualizarRol($d){
		$ok = 0; $clearper = 0;
        foreach ($d as $clave => $valor) {
		    if($clave!='rol' && $clave!='idRol' && $clave!='sess'){
		    	$data = array();
				for($i=0;$i<count($valor);$i++){
					if($valor[$i]!=NULL && $valor[$i]!=''){
						$data[$valor[$i]]='Y';
					} else {
						$data['ver']='N';
					}
				}
				$this->db->where('app_fk', trim($clave, "p-"));
				$this->db->where('rol_fk', $d['idRol']);
				$this->db->select('id_relacion');
				$query = $this->db->get('apps_permisos');
				$result = $query->result();
				if($query->num_rows()==0){
					$data['app_fk']=trim($clave, "p-");
					$data['rol_fk']=$d['idRol'];
					if($this->db->insert('apps_permisos', $data)){
		        		$ok = 1;
		        	} else {
				    	return 2;
				    }
				} else {
					if($clearper==0){
						$clearper++;
						$borrar_permisos = array(
							'ver' => 'N',
							'crear' => 'N',
							'editar' => 'N',
							'borrar' => 'N',
							'logs' => 'N',
							'imprimir' => 'N',
							'reportes' => 'N' 
						);
						$this->db->where('rol_fk', $d['idRol']);
						$this->db->update('apps_permisos', $borrar_permisos);
					}
					$this->db->where('app_fk', trim($clave, "p-"));
					$this->db->where('rol_fk', $d['idRol']);
					if($this->db->update('apps_permisos', $data)){
		        		$ok = 1;
		        	} else {
				    	return 2;   	
				    }
				}
		    }
		}
		if($ok==1){
			return 1;
		} else {
			return 2; 
		}
	}
}
