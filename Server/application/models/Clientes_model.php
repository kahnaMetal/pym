<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Clientes_model extends CI_Model
{
	public function construct(){
		parent::__construct();
	}

	public function consultarPersona($id){
		$this->db->select('
			documentCustomer,
			nameCustomer,
			lastnameCustomer,
			phoneCustomer,
			cellphoneCustomer,
			emailCustomer,
			addressCustomer,
			cityCustomer_fkCities
		');
		$this->db->where('statusCustomer', '1');
		$this->db->where('documentCustomer', $id);
		return $this->db->get('customers');
	}

	public function consultarEmpresa($id){
		$this->db->select('
			nitCustomer,
			businessDigitCustomer,
			businessNameCustomer,
			phoneCustomer,
			cellphoneCustomer,
			emailCustomer,
			addressCustomer,
			cityCustomer_fkCities
		');
		$this->db->where('statusCustomer', '1');
		$this->db->where('nitCustomer', $id);
		return $this->db->get('customers');
	}

	public function listar($s, $l, $c){
		$this->db->select('
			idCustomer,
			typeCustomer,
			documentCustomer,
			nameCustomer,
			lastnameCustomer,
			nitCustomer,
			businessDigitCustomer,
			businessNameCustomer,
			phoneCustomer,
			cellphoneCustomer,
			emailCustomer,
			addressCustomer
		');
		$this->db->where('statusCustomer', '1');
		if(is_numeric($c)){
			$this->db->group_start();
			$this->db->where('documentCustomer', $c);
			$this->db->or_where('nitCustomer', $c);
			$this->db->group_end();
		} else {
			if($c!=NULL){
				$this->db->group_start();
				$this->db->like('nameCustomer', $c, 'both');
				$this->db->or_like('lastnameCustomer', $c, 'both');
				$this->db->or_like('businessNameCustomer', $c, 'both');
				$this->db->or_where("CONCAT(nameCustomer, ' ', lastnameCustomer) LIKE '%".$c."%'", NULL, FALSE);
				$this->db->group_end();
			}
		}
		$query = $this->db->get('customers', $l, $s);
		if($query->num_rows() > 0 ){
        return $query;
    } else {
    		return FALSE;
    }
	}

	public function registrar($d){
		$this->db->trans_begin();
		$this->db->insert('customers',$d);
		if($this->db->trans_status() === FALSE){//SI HUBO ERROR REGISTRANDO EN LA BASE DE DATOS
			$this->db->trans_rollback();//REVERSAR CAMBIOS
			return array(
				'status' => 'danger',
				'message' => 'Error registrando cliente en la base de datos'
			);
		} else {//SI EL REGISTRO FUE SATISFACTORIO
			$this->db->trans_commit();
			return array(
				'status' => 'success',
				'message' => 'Cliente registrado satisfactoriamente'
			);
		}
	}

	public function actualizar($d, $id){
		$this->db->trans_begin();
		$this->db->where('idCustomer',$id);
		$this->db->update('customers',$d);
		if($this->db->trans_status() === FALSE){//SI HUBO ERROR REGISTRANDO EN LA BASE DE DATOS
			$this->db->trans_rollback();//REVERSAR CAMBIOS
			return array(
				'status' => 'danger',
				'message' => 'Error actualizando cliente en la base de datos'
			);
		} else {//SI EL REGISTRO FUE SATISFACTORIO
			$this->db->trans_commit();
			return array(
				'status' => 'success',
				'message' => 'Cliente actualizado satisfactoriamente'
			);
		}
	}

	public function eliminar($id){
		$this->db->trans_begin();
		$d =array(//CREAMOS DATOS PARA LA BASE DE DATOS
				'statusCustomer'	 								=> '0',
				'deletedBy_Customer'							=> 1,
				'deletedAt_Customer'							=> date('Y-m-d H:i:s')
		);
		$this->db->where('idCustomer',$id);
		$this->db->update('customers',$d);
		if($this->db->trans_status() === FALSE){//SI HUBO ERROR REGISTRANDO EN LA BASE DE DATOS
			$this->db->trans_rollback();//REVERSAR CAMBIOS
			return array(
				'status' => 'danger',
				'message' => 'Error eliminando cliente en la base de datos'
			);
		} else {//SI EL REGISTRO FUE SATISFACTORIO
			$this->db->trans_commit();
			return array(
				'status' => 'success',
				'message' => 'Cliente eliminado satisfactoriamente'
			);
		}
	}

}
