<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Infodia_model extends CI_Model
{
	public function construct()
	{
		parent::__construct();
	}
	
	public function consultaCreditos($d){
		if(!isset($d['fecha']) || $d['fecha']==''){
			$date = date('Y-m-d');
		} else {
			$date = $d['fecha'];
		}
		if($d['global']=='N'){
			$glo = 'AND id_user = "'.$d['usr'].'"';
		} else { $glo = ''; }
		$credito = $this->db->query("
			SELECT 
				id_user, nombres, apellidos, hora_credito,
				nombres_cliente, apellidos_cliente, id_credito,
				valor,  interes, dias, plazo, cobro, notas_credito, fecha_credito
			FROM users
			LEFT JOIN creditos
				ON usr_fk = id_user
			LEFT JOIN clientes
				ON cliente_fk = id_cliente
			WHERE creditos.status = '1' 
				AND clientes.status = '1'
				AND fecha_credito = '".$date."' ".$glo."
			ORDER BY 
				apellidos, nombres, apellidos_cliente, nombres_cliente
			ASC
		");
		if($credito->num_rows() > 0 ){
            return $credito->result();
        } else {
        	return FALSE;
        }
	}

	public function consultaAbonos($d){
		if(!isset($d['fecha']) || $d['fecha']==''){
			$date = date('Y-m-d');
		} else {
			$date = $d['fecha'];
		}
		if($d['global']=='N'){
			$glo = 'AND id_user = "'.$d['usr'].'"';
		} else { $glo = ''; }
		$abonos = $this->db->query("
			SELECT 
				id_user, nombres, apellidos, hora_recaudo,
				nombres_cliente, apellidos_cliente, id_recaudo,
				id_credito, valor_recaudo, tipo_recaudo, notas_recaudo 
			FROM users
			LEFT JOIN creditos
				ON usr_fk = id_user
			LEFT JOIN clientes
				ON cliente_fk = id_cliente
			LEFT JOIN recaudos
				ON credito_fk = id_credito
			WHERE (creditos.status = '1' || creditos.status = '4') 
				AND clientes.status = '1'
				AND fecha_recaudo = '".$date."' ".$glo."
			ORDER BY 
				apellidos, nombres, apellidos_cliente, nombres_cliente
			ASC
		");
		if($abonos->num_rows() > 0 ){
            return $abonos->result();
        } else {
        	return FALSE;
        }
	}
}
