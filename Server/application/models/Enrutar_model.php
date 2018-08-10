<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Enrutar_model extends CI_Model
{
	public function construct()
	{
		parent::__construct();
	}
	
	public function route($id){
		$one = explode(':', $id);
		$this->db->where('usr_fk', $one[0]);
		$this->db->select('ruta');
		$query = $this->db->get('rutas');
		$result = $query->row();
		if($query->num_rows()>0){
			return $result->ruta;
		} else {
			return FALSE;
		}
	}

	public function clientes($id){
		$d = explode(':', $id);
		$contrato = $this->db->query("
			SELECT 
				id_cliente, cpf, rg, nombres_cliente, apellidos_cliente
			FROM clientes
			LEFT JOIN m_clientes_users
				ON fk_cliente = id_cliente
			WHERE clientes.status = '1' AND fk_user = ".$d[0]." 
			ORDER BY 
				apellidos_cliente,
				nombres_cliente,
				cpf,
				rg
			ASC
		");
		if($contrato->num_rows() > 0 ){
            return $contrato->result();
        } else {
        	return FALSE;
        }
	}

	public function ordenarRuta($d){
		$a = explode(':', $d['sess']);
		$this->db->where('usr_fk', $a[0]);
		$this->db->select('id_ruta');
		$query = $this->db->get('rutas');
		$result = $query->row();
		if($query->num_rows()>0){
			$data = array(
		        'ruta' => $d['multiple_value']
			);
			$this->db->where('usr_fk', $a[0]);
			if($this->db->update('rutas', $data)){
				return 2;
			} else {
				return 3;
			}
		} else {
			$data = array(
		        'ruta' => $d['multiple_value'],
		        'usr_fk' => $a[0]
			);
			if($this->db->insert('rutas', $data)){
				return 1;
			} else {
				return 3;
			}
		}
	}
}
