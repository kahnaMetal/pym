<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Productos_model extends CI_Model
{
	public function construct(){
		parent::__construct();
	}

	public function consultarProductoNombre($s){
		$this->db->select('
			idProduct,
			nameProduct,
			imgProduct,
			categoryProduct,
			qtyProduct,
			valueProduct
		');
		$this->db->where('statusProduct', '1');
		$this->db->where('nameProduct', $s);
		return $this->db->get('products');
	}

	public function consultarProducto($id){
		$this->db->select('
			idProduct,
			nameProduct,
			imgProduct,
			categoryProduct,
			qtyProduct,
			valueProduct
		');
		$this->db->where('statusProduct', '1');
		$this->db->where('idProduct', $id);
		return $this->db->get('products');
	}

	public function listar($s, $l, $c){
		$this->db->select('
			idProduct,
			nameProduct,
			imgProduct,
			categoryProduct,
			qtyProduct,
			valueProduct
		');
		$this->db->where('statusProduct', '1');
		if($c!=NULL){
			$this->db->group_start();
			$this->db->like('nameProduct', $c, 'both');
			$this->db->or_where('categoryProduct', $c);
			$this->db->group_end();
		}
		$query = $this->db->get('products', $l, $s);
		if($query->num_rows() > 0 ){
        return $query;
    } else {
    		return FALSE;
    }
	}

	public function registrar($d){
		$this->db->trans_begin();
		$this->db->insert('products',$d);
		if($this->db->trans_status() === FALSE){//SI HUBO ERROR REGISTRANDO EN LA BASE DE DATOS
			$this->db->trans_rollback();//REVERSAR CAMBIOS
			return array(
				'status' => 'danger',
				'message' => 'Error registrando producto en la base de datos'
			);
		} else {//SI EL REGISTRO FUE SATISFACTORIO
			$this->db->trans_commit();
			return array(
				'status' => 'success',
				'message' => 'Producto registrado satisfactoriamente'
			);
		}
	}

	public function actualizar($d, $id){
		$this->db->trans_begin();
		$this->db->where('idProduct',$id);
		$this->db->update('products',$d);
		if($this->db->trans_status() === FALSE){//SI HUBO ERROR REGISTRANDO EN LA BASE DE DATOS
			$this->db->trans_rollback();//REVERSAR CAMBIOS
			return array(
				'status' => 'danger',
				'message' => 'Error actualizando producto en la base de datos'
			);
		} else {//SI EL REGISTRO FUE SATISFACTORIO
			$this->db->trans_commit();
			return array(
				'status' => 'success',
				'message' => 'Producto actualizado satisfactoriamente'
			);
		}
	}

	public function eliminar($id){
		$this->db->trans_begin();
		$d =array(//CREAMOS DATOS PARA LA BASE DE DATOS
				'statusProduct'	 							=> '0',
				'deletedBy_Product'						=> 1,
				'deletedAt_Product'						=> date('Y-m-d H:i:s')
		);
		$this->db->where('idProduct', $id);
		$this->db->update('products', $d);
		if($this->db->trans_status() === FALSE){//SI HUBO ERROR REGISTRANDO EN LA BASE DE DATOS
			$this->db->trans_rollback();//REVERSAR CAMBIOS
			return array(
				'status' => 'danger',
				'message' => 'Error eliminando producto en la base de datos'
			);
		} else {//SI EL REGISTRO FUE SATISFACTORIO
			$this->db->trans_commit();
			return array(
				'status' => 'success',
				'message' => 'Producto eliminado satisfactoriamente'
			);
		}
	}

}
