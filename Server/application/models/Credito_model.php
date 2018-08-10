<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Credito_model extends CI_Model
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
				id_cliente AS id, 
				CONCAT( 
					apellidos_cliente, 
					', ', 
					nombres_cliente, 
					', (CPF: ',
					cpf,
					' | RG: ',
					rg,
					')' 
				) AS text
			FROM clientes
			LEFT JOIN m_clientes_users
				ON fk_cliente = id_cliente
			WHERE clientes.status = '1' ".$restr."
			AND
			(
				apellidos_cliente LIKE '%".$like."%' ESCAPE '!'
				OR
				nombres_cliente LIKE '%".$like."%' ESCAPE '!'
				OR
				cpf LIKE '%".$like."%' ESCAPE '!'
				OR
				rg LIKE '%".$like."%' ESCAPE '!'
			)
			ORDER BY 
				apellidos_cliente,
				nombres_cliente,
				cpf,
				rg
			ASC
		");
		if($contrato->num_rows() > 0 ){
            return $contrato->result();
        } 
	}

	public function creditoCliente($id){
		$query = $this->db->query("
			SELECT 
				id_credito,
				valor,
				interes,
				fecha_credito,
				status,
				'".date('Y-m-d')."' AS ahora,
				MIN(fecha_cobro) AS fecha_cobro,
				MAX(fecha_recaudo) AS ult_recaudo
			FROM creditos
			LEFT JOIN
				(SELECT 
						credito_fk, 
						fecha_cobro
					FROM cobros 
					WHERE 
						fecha_cobro > '".date('Y-m-d')."'
						AND estado_cobro = '0' 
					ORDER BY fecha_cobro ASC 
				) AS c
			ON c.credito_fk = id_credito
			LEFT JOIN (SELECT credito_fk, fecha_recaudo FROM recaudos) AS d ON d.credito_fk = id_credito
			WHERE creditos.status != '4'
				AND creditos.status != '0'
				AND cliente_fk = '".$id."'
            GROUP BY id_credito    
			ORDER BY 
				fecha_credito 
			DESC
		");
		$result = $query->result();
		if($query->num_rows()>0){
			return $result;
		} else {
			return FALSE;
		}
	}

	public function ingresarCredito($d){
		$hoy = date('Y-m-d');
		$hora = date('H:i:s');
		if($_POST['desembolso']>1000){
			$st = '2';
		} else {
			$st = '1';
		}
		if($d['plazo']==1){
			$d['dia'] = 8;
		}
		$u = explode(':', $_POST['sess']);
		$data = array(
	        'cliente_fk' => $d['id_cliente'],
	        'usr_fk' => $u[0],
	        'valor' => $d['desembolso'],
	        'interes' => $d['interes'],
	        'dias' => $d['tiempo'],
	        'fecha_credito' => $hoy,
	        'hora_credito' => $hora,
	        'plazo' => $d['plazo'],
	        'cobro' => $d['dia'],
	        //'abono' => $d['valabono'],
	        'notas_credito' => trim($d['notas'], " \t\n\r\0\x0B"),
	        'status' => $st
		);
		if($this->db->insert('creditos', $data)){
			/*******INICIO MAX ID DE CREDITOS PARA OBTENER EL CREDITO RECIENTE INGRESADO*******/
			$this->db->select_max('id_credito');            
            $res1 = $this->db->get('creditos');
            $max = $res1->row()->id_credito;
            /*******FIN MAX ID DE CREDITOS PARA OBTENER EL CREDITO RECIENTE INGRESADO*******/
			$total = $d['desembolso'] * (1+($d['interes']/100));
			$fi = strtotime(date('Y-m-d 01:00:00'));
			$ff = $fi+($d['tiempo']*24*60*60);
			$cuota = 0; $semana = 0; $fechasPagos = [];
			for($i=$fi+86400; $i<=$ff; $i+=86400){
				if(date('w', $i) != 0 ){
					switch ($d['plazo']) {
						case 1:
							$fechasPagos[$cuota] = date('Y-m-d', $i);
							$cuota++;
							break;
						case 2:
							if($d['dia'] == date('w', $i)){
								$fechasPagos[$cuota] = date('Y-m-d', $i);
								$cuota++;
							}
							break;
						case 3:
							if($d['dia'] == date('w', $i)) {
								if($semana==2){
									$fechasPagos[$cuota] = date('Y-m-d', $i);
									$cuota++;
									$semana = 0;
								} else {
									$semana++;
								}
							}
							break;
						case 4:
							if($d['dia'] == date('w', $i)) {
								if($semana==4){
									$fechasPagos[$cuota] = date('Y-m-d', $i);
									$cuota++;
									$semana = 0;
								} else {
									$semana++;
								}
							}
							break;
					}
				} else {
					$ff += 86400;
				}
			}
			$fechaFinal = date('Y-m-d', $ff);
			if($cuota>0){
				$data = array();
				for($i=0;$i<$cuota;$i++){
					$a = array(
						'fecha_cobro' => $fechasPagos[$i],
						'valor_cuota' => round(($total/$cuota), 2),
						'credito_fk' => $max
					);
					array_push($data, $a);
				}
			} else {
				$data = array(
					'fecha_cobro' => $fechaFinal,
					'valor_cuota' => $total,
					'credito_fk' => $max
				);
			}

			if($this->db->insert_batch('cobros', $data)){
				return 1;
			} else {
				return 5;
			}
		} else {
			return 3;
		}
	}
}
