<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Tokens_model extends CI_Model
{
	public function construct()
	{
		parent::__construct();
	}

	public function loadTokens(){
		$contrato = $this->db->query("
			SELECT 
				id_credito AS id,
				fecha_credito AS fecha,
				CONCAT(apellidos_cliente, ',<br>', nombres_cliente) AS cliente, 
				CONCAT(apellidos, ',<br>', nombres) AS prestamista, 
				'Credito' AS tipo,
				CONCAT('Valor: ', valor, '<br>Interes: ', interes, '<br>Días ', dias) AS detalle,
				creditos.status AS estado,
				token
			FROM creditos
			INNER JOIN clientes
				ON cliente_fk = id_cliente
			INNER JOIN users
				ON usr_fk = id_user
			WHERE creditos.status = '2' || creditos.status = '3'
			ORDER BY 
				fecha_credito
			DESC
		");
		if($contrato->num_rows() > 0 ){
            return $contrato->result();
        } else {
        	return FALSE;
        }
	}

	public function refuseToken($d){
		if($d['type']=='Credito'){
			$filter = 'id_credito';
			$table = 'creditos';
		} elseif($d['type']=='Abono'){
			$filter = 'id_abono';
			$table = 'abonos';
		} else {
			return 3;
		}
		$data = array(
	        'status' => '0'
		);
		$this->db->where($filter, $d['id']);
		if($this->db->update($table, $data)){
			return 1;
		} else {
			return 2;
		}
	}

	public function approveToken($d){
		$token = strtoupper(substr(md5($_POST['id']), 0, 6));
		if($d['type']=='Credito'){
			$filter = 'id_credito';
			$table = 'creditos';
		} elseif($d['type']=='Abono'){
			$filter = 'id_abono';
			$table = 'abonos';
		} else {
			return 3;
		}
		$data = array(
	        'status' => '3',
	        'token' => $token
		);
		$this->db->where($filter, $d['id']);
		if($this->db->update($table, $data)){
			return '1||'.$token;
		} else {
			return 2;
		}
	}
}
