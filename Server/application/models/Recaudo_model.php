<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Recaudo_model extends CI_Model
{
	public function construct()
	{
		parent::__construct();
	}

	public function clientes($like, $id, $glo){
		$d = explode(':', $id);
		if($glo=="N"){
			$restr = "AND fk_user = ".$d[0];
		} else {
			$restr = '';
		}
		$contrato = $this->db->query("
			SELECT 
				id_credito AS id, 
				CONCAT( 
					apellidos_cliente, 
					', ', 
					nombres_cliente, 
					', (Crédito # ',
					id_credito,
					' del ',
					fecha_credito,
					')' 
				) AS text
			FROM clientes
			LEFT JOIN m_clientes_users
				ON fk_cliente = id_cliente
			INNER JOIN creditos
				ON cliente_fk = id_cliente
			WHERE clientes.status = '1' ".$restr."
			AND creditos.status = '1' 
			AND
			(
				apellidos_cliente LIKE '%".$like."%' ESCAPE '!'
				OR
				nombres_cliente LIKE '%".$like."%' ESCAPE '!'
				OR
				id_credito = '".$like."'
			)
			ORDER BY 
				apellidos_cliente,
				nombres_cliente,
				id_credito
			ASC
		");
		if($contrato->num_rows() > 0 ){
            return $contrato->result();
        } 
	}

	public function clientesGen($like){
		$contrato = $this->db->query("
			SELECT 
				id_credito AS id, 
				CONCAT( 
					apellidos_cliente, 
					', ', 
					nombres_cliente, 
					', (Crédito # ',
					id_credito,
					' del ',
					fecha_credito,
					')' 
				) AS text
			FROM clientes
			LEFT JOIN m_clientes_users
				ON fk_cliente = id_cliente
			INNER JOIN creditos
				ON cliente_fk = id_cliente
			WHERE clientes.status = '1'
			AND (creditos.status = '1' || creditos.status = '4') 
			AND
			(
				apellidos_cliente LIKE '%".$like."%' ESCAPE '!'
				OR
				nombres_cliente LIKE '%".$like."%' ESCAPE '!'
				OR
				id_credito = '".$like."'
			)
			ORDER BY 
				apellidos_cliente,
				nombres_cliente,
				id_credito
			ASC
		");
		if($contrato->num_rows() > 0 ){
            return $contrato->result();
        } 
	}

	public function abonos($id){
		$abonos = $this->db->query("
			SELECT id_recaudo, credito_fk, valor_recaudo, fecha_recaudo, hora_recaudo FROM recaudos WHERE credito_fk = ".$id." ORDER BY fecha_recaudo DESC, hora_recaudo DESC
		");
		if($abonos->num_rows() > 0 ){
            return $abonos->result();
        }
	}

	public function recaudoCliente($id){
		$contrato = $this->db->query("
			SELECT 
				cpf, rg, nombres_cliente, apellidos_cliente, dir_cobro, barrio_cobro, ciudad_cobro,
				id_credito, fecha_credito, cobro, abono, valor, interes,
				valor_cuota, fecha_cobro,
				(SELECT COUNT(id_cobro) FROM cobros WHERE fecha_cobro < '".date('Y-m-d')."' AND estado_cobro = '0' AND credito_fk = '".$id."' ) AS atrasadas,
				(SELECT MAX(fecha_recaudo) FROM recaudos WHERE credito_fk = '".$id."' ) AS ult_recaudo
			FROM clientes
			INNER JOIN creditos
				ON cliente_fk = id_cliente
			LEFT JOIN
				(SELECT 
						credito_fk, 
						fecha_cobro,
						valor_cuota
					FROM cobros 
					WHERE 
						fecha_cobro > '".date('Y-m-d')."'
						AND estado_cobro = '0'
						AND credito_fk = '".$id."' 
					ORDER BY fecha_cobro ASC
					LIMIT 1
				) AS c
			ON c.credito_fk = id_credito
			WHERE id_credito = '".$id."'
			AND creditos.status = '1'
			AND clientes.status = '1'
		");
		if($contrato->num_rows() > 0 ){
            return $contrato->row();
        } 
	}	

	public function ingresarRecaudo($d){
		$u = explode(':', $d['sess']);
		$hora = date('H:i:s');
		$data = array(
	        'credito_fk' => $d['id_credito'],
	        'valor_recaudo' => $d['valor'],
	        'fecha_recaudo' => date('Y-m-d'),
	        'hora_recaudo' => $hora,
	        'tipo_recaudo' => $d['fpago'],
	        'notas_recaudo' => trim($d['notas'], " \t\n\r\0\x0B")
		);
		if($this->db->insert('recaudos', $data)){
			$this->db->set('abono', '(abono + '.$d['valor'].')', FALSE);
			$this->db->where('id_credito',$d['id_credito']);
			if($this->db->update('creditos')){
				$abono = $this->db->query("
					SELECT 
						abono
					FROM creditos
					WHERE id_credito = '".$d['id_credito']."' 
				")->row()->abono;
				$totalApagar = $this->db->query("
					SELECT 
						(valor*(1+(interes/100))) AS totPay
					FROM creditos
					WHERE id_credito = '".$d['id_credito']."' 
				")->row()->totPay;
				$restante = $totalApagar-$abono;
				$qty = floor($abono/$d['valorCuota']);
				///////////////////////////////////////////////////////
				if($qty>0){
					$data = array(
				        'estado_cobro' => '1'
					);
					$this->db->where('credito_fk',$d['id_credito']);
					$this->db->limit($qty);
					if($this->db->update('cobros', $data)){
	           			return '1::'.$restante;
	           		} else {
	           			return '4::E';
	           		}	
				} else {
					return '1::'.$restante;
				}
			} else {
				return '2::E';
			}
		} else {
			return '3::E';
		}
	}

	public function borrarRecaudo($d){
		$this->db->where('id_recaudo', $d['id']);
		if($this->db->delete('recaudos')){
			$this->db->set('abono', '(abono - '.$d['val'].')', FALSE);
			$this->db->where('id_credito',$d['cre']);
			if($this->db->update('creditos')){
				$abono = $this->db->query("
					SELECT 
						abono
					FROM creditos
					WHERE id_credito = '".$d['cre']."' 
				")->row()->abono;
				$c = $this->db->query("
					SELECT 
						(valor*(1+(interes/100))) AS totPay, dias
					FROM creditos
					WHERE id_credito = '".$d['cre']."' 
				")->row();
				$qty = floor($abono/ ( ($c->totPay)/($c->dias) ) );
				///////////////////////////////////////////////////////
				$data = array(
			        'estado_cobro' => '0'
				);
				$this->db->where('credito_fk',$d['cre']);
				if($this->db->update('cobros', $data)){
					if($qty>0){
						$data = array(
					        'estado_cobro' => '1'
						);
						$this->db->where('credito_fk',$d['cre']);
						$this->db->limit($qty);
						if($this->db->update('cobros', $data)){
							return 1;
						} else { 
							return 2;
						}
					} else {
						return 1;
					}
           		} else {
           			return 2;
           		}
			} else {
				return 2;
			}
		} else {
			return 2;
		}
	}
}
