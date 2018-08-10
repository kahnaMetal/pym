<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Balance_model extends CI_Model
{
	public function construct(){
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
	
	public function balanceGeneral($date, $usr){
		$base = $this->db->query("
			SELECT valor_base 
			FROM bases 
			WHERE usr_fk = '".$usr."' 
			AND fecha_base = '".$date."';"
		);
		if($base->num_rows() == 0 ){
			$datosBaseAnterior = $this->db->query("
				SELECT valor_base, fecha_base
				FROM bases 
				WHERE usr_fk = '".$usr."' 
				AND fecha_base < '".$date."' 
				ORDER BY fecha_base DESC 
				LIMIT 1;"
			);
			if($datosBaseAnterior->num_rows() != 0 ){
				$baseAnterior = $datosBaseAnterior->row()->valor_base;
			} else {
				$baseAnterior = 0;
			}

			$totRecaudado = $this->db->query("
				SELECT SUM(valor_recaudo) AS tot
				FROM recaudos 
				INNER JOIN creditos 
					ON credito_fk=id_credito 
				WHERE usr_fk = '".$usr."' 
					AND fecha_recaudo < '".$date."'
			")->row()->tot;

			$totCreditado = $this->db->query("
				SELECT SUM(valor) AS tot
				FROM creditos
				WHERE usr_fk = '".$usr."' 
					AND fecha_credito < '".$date."'
					AND (status = '1' OR status = '4')
			")->row()->tot;

			$totGastos = $this->db->query("
				SELECT SUM(valor_gasto) AS tot
				FROM gastos
				WHERE usr_fk = '".$usr."' 
					AND fehca_gasto < '".$date."'
			")->row()->tot;

			$baseActual = $baseAnterior + $totRecaudado - $totCreditado - $totGastos;
		} else {
			$baseActual = $base->row()->valor_base; 
		}

		$totalesDia = $this->db->query("
			SELECT 
			    '".$date."' AS fecha_actual,
			    (SELECT SUM( ( valor + (valor * (interes/100) ) ) / dias ) FROM creditos WHERE usr_fk = '".$usr."' AND status = '1'AND fecha_credito <= '".$date."') AS tot_recaudar,
			    (SELECT SUM(valor_recaudo) FROM recaudos LEFT JOIN creditos ON credito_fk=id_credito WHERE usr_fk = '".$usr."' AND fecha_recaudo = '".$date."') AS recaudo, 
			    (SELECT SUM(valor) FROM creditos WHERE usr_fk = '".$usr."' AND fecha_credito = '".$date."' AND (status = '1' || status = '4')) AS credito, 
			    (SELECT SUM(valor_gasto) FROM gastos WHERE usr_fk = '".$usr."' AND fehca_gasto = '".$date."') AS gastos, 
			    (SELECT valor_base FROM bases WHERE usr_fk = '".$usr."' AND fecha_base = '".$date."') AS base, 
			    '".$baseActual."' AS base_ayer, 
			    (SELECT COUNT(DISTINCT id_credito) FROM cobros LEFT JOIN creditos ON cobros.credito_fk = creditos.id_credito WHERE usr_fk = '".$usr."' AND (creditos.status = '1') AND fecha_credito <= '".$date."') AS qty_creditos,
			    (SELECT COUNT(id_recaudo) FROM recaudos LEFT JOIN creditos ON credito_fk=id_credito WHERE usr_fk = '".$usr."' AND fecha_recaudo = '".$date."' AND (creditos.status = '1')) AS visitados, 
			    (SELECT COUNT(id_cliente) FROM clientes LEFT JOIN creditos ON cliente_fk=id_cliente WHERE usr_fk = '".$usr."' AND fecha_registro = '".$date."' AND (creditos.status = '1')) AS nuevos_clientes 
			FROM cobros LEFT JOIN creditos ON cobros.credito_fk = creditos.id_credito 
			WHERE fecha_cobro = '".$date."' AND usr_fk = '".$usr."' AND (creditos.status = '1')
		"); //estado_cobro = '0' AND  || creditos.status = '4'
		/*$totalesDia = $this->db->query("
			SELECT 
			    '".$date."' AS fecha_actual, 
			    SUM(valor_cuota) AS tot_recaudar, 
			    (SELECT SUM(valor_recaudo) FROM recaudos LEFT JOIN creditos ON credito_fk=id_credito WHERE usr_fk = '".$usr."' AND fecha_recaudo = '".$date."') AS recaudo, 
			    (SELECT SUM(valor) FROM creditos WHERE usr_fk = '".$usr."' AND fecha_credito = '".$date."' AND (status = '1' || status = '4')) AS credito, 
			    (SELECT SUM(valor_gasto) FROM gastos WHERE usr_fk = '".$usr."' AND fehca_gasto = '".$date."') AS gastos, 
			    (SELECT valor_base FROM bases WHERE usr_fk = '".$usr."' AND fecha_base = '".$date."') AS base, 
			    '".$baseActual."' AS base_ayer, 
			    (SELECT COUNT(DISTINCT id_credito) FROM cobros LEFT JOIN creditos ON cobros.credito_fk = creditos.id_credito WHERE fecha_cobro <= '".$date."' AND usr_fk = '".$usr."' AND (creditos.status = '1')) AS qty_creditos,
			    (SELECT COUNT(id_recaudo) FROM recaudos LEFT JOIN creditos ON credito_fk=id_credito WHERE usr_fk = '".$usr."' AND fecha_recaudo = '".$date."' AND (creditos.status = '1')) AS visitados, 
			    (SELECT COUNT(id_cliente) FROM clientes LEFT JOIN creditos ON cliente_fk=id_cliente WHERE usr_fk = '".$usr."' AND fecha_registro = '".$date."' AND (creditos.status = '1')) AS nuevos_clientes 
			FROM cobros LEFT JOIN creditos ON cobros.credito_fk = creditos.id_credito 
			WHERE fecha_cobro = '".$date."' AND usr_fk = '".$usr."' AND (creditos.status = '1')
		"); */
		if($totalesDia->num_rows() > 0 ){
            return $totalesDia->row();
        } else {
        	return FALSE;
        }
	}

	public function detalleGastos($date, $usr){
		if($usr!=''){
			$usrD = "AND usr_fk = ".$usr.";";
		} else { $usrD = ''; }
		$gastos = $this->db->query("
			SELECT 
				valor_gasto, motivo_gasto, desc_gasto, hora_gasto 
			FROM gastos 
			WHERE fehca_gasto = '".$date."' ".$usrD);
		if($gastos->num_rows() > 0 ){
            return $gastos->result();
        } else {
        	return FALSE;
        }
	}
}
/*LEFT JOIN creditos
				ON usr_fk = id_user
			LEFT JOIN clientes
				ON cliente_fk = id_cliente
			LEFT JOIN recaudos
				ON credito_fk = id_credito*/
