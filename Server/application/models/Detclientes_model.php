<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Detclientes_model extends CI_Model{

	public function __construct()
	{
        parent::__construct();        
	}

	public function clientes($data, $user){		
		$limit = ''; $where = ''; 
		$order = 'ORDER BY apellidos_cliente, nombres_cliente ASC';
		
		$aColumns = array( 'id_credito', 'cobrador', 'cliente', 'debeMayor', 'debeMenor', 'atrasosMayor', 'atrasosMenor');

		$aColumns_global = array( 'id_credito', 'nombres', 'apellidos', 'nombres_cliente', 'apellidos_cliente');

		// INICIO CONDICION PARA EL PAGINA
		if ( isset( $data['iDisplayStart'] ) && $data['iDisplayLength'] != '-1' ){
			$limit = "LIMIT ".$data['iDisplayStart'].", ".$data['iDisplayLength'];
		}
		// FIN CONDICION PARA EL PAGINA		

		// INICIO CONDICION PARA EL ORDENADO
		if ( isset( $data['iSortCol_0'] ) ){
			switch ($data['iSortCol_0']) {
				case '0':
					$order = " ORDER BY id_credito ".$data['sSortDir_0'];
					break;
				case '1':
					$order = " ORDER BY apellidos ".$data['sSortDir_0'].", nombres ".$data['sSortDir_0'];
					break;
				case '2':
					$order = " ORDER BY apellidos_cliente ".$data['sSortDir_0'].", nombres_cliente ".$data['sSortDir_0'];
					break;
				case '3':
					$order = " ORDER BY ((valor*((interes/100)+1))-abono) ".$data['sSortDir_0'];
					break;
				case '4':
					$order = " ORDER BY atrasos ".$data['sSortDir_0'];
					break;
			}
		}
		// FIN CONDICION PARA EL ORDENADO
	
		// INICIO CONDICION PARA LA BUSQUEDA		
		if ( $data['sSearch'] != "" ){
			$where .= ' AND (';
			for ( $i=0 ; $i<count($aColumns_global) ; $i++ ){
				if($i==0){
					$where .= $aColumns_global[$i].' = "'.$data['sSearch'].'"';
				}
				$where .= ' OR '.$aColumns_global[$i].' LIKE "%'.$data['sSearch'].'%"';
			}
			$where .= ')';
		}
		// FIN CONDICION PARA LA BUSQUEDA

		// INICIO FILTRO INDIVIDUAL
		for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			if ( $data['bSearchable_'.$i] == "true" && $data['sSearch_'.$i] != '' ){
				switch ($i) {
					case 0:
						$where .= " AND id_credito = '".$data['sSearch_'.$i]."'";
						break;
					case 1:
						$where .= " AND (nombres LIKE '%".$data['sSearch_'.$i]."%' OR apellidos LIKE '%".$data['sSearch_'.$i]."%')";
						break;
					case 2:
						$where .= " AND (nombres_cliente LIKE '%".$data['sSearch_'.$i]."%' OR apellidos_cliente LIKE '%".$data['sSearch_'.$i]."%')";
						break;
					case 3:
						$where .= " AND ((valor*((interes/100)+1))-abono) > '".$data['sSearch_'.$i]."'";
						break;
					case 4:
						$where .= " AND ((valor*((interes/100)+1))-abono) < '".$data['sSearch_'.$i]."'";
						break;
					case 5:
						$where .= " AND atrasos > '".$data['sSearch_'.$i]."'";
						break;
					case 6:
						$where .= " AND atrasos < '".$data['sSearch_'.$i]."'";
						break;
				}
			}
		}
		// FIN FILTRO INDIVIDUAL
	
		if($user=='-1' || $user== -1){
			$user = '';
		} else {
			$user = ' AND users.id_user = "'.$user.'"';
		}
		$consulta = $this->db->query("
			SELECT id_credito, id_cliente, id_user, nombres, apellidos, nombres_cliente, apellidos_cliente, valor, interes, abono, atrasos
			FROM creditos 
			LEFT JOIN users 
				ON usr_fk = id_user 
			LEFT JOIN clientes 
				ON cliente_fk = id_cliente
			LEFT JOIN 
				(SELECT 
					credito_fk, COUNT(id_cobro) AS atrasos
				FROM cobros 
				WHERE fecha_cobro < '".date('Y-m-d')."' 
					AND estado_cobro = '0'
				GROUP BY credito_fk
			) AS c
				ON c.credito_fk = id_credito
			WHERE creditos.status != '4'
				AND creditos.status != '0'".$user."
			AND clientes.status = '1'".$where."
				".$order."
				".$limit."
		");
		if($consulta->num_rows() > 0 ){
            return $consulta->result();
        } else {
        	return '0';
        }
	}

	public function total_clientes($data, $user){
		$where = '';		

		$aColumns = array( 'id_credito', 'cobrador', 'cliente', 'debeMayor', 'debeMenor', 'atrasosMayor', 'atrasosMenor');

		$aColumns_global = array( 'id_credito', 'nombres', 'apellidos', 'nombres_cliente', 'apellidos_cliente');	

		// INICIO CONDICION PARA LA BUSQUEDA		
		if ( $data['sSearch'] != "" ){
			$where .= ' AND (';
			for ( $i=0 ; $i<count($aColumns_global) ; $i++ ){
				if($i==0){
					$where .= $aColumns_global[$i].' = "'.$data['sSearch'].'"';
				}
				$where .= ' OR '.$aColumns_global[$i].' LIKE "%'.$data['sSearch'].'%"';
			}
			$where .= ')';			
		}
		// FIN CONDICION PARA LA BUSQUEDA
		
		// INICIO FILTRO INDIVIDUAL
		for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			if ( $data['bSearchable_'.$i] == "true" && $data['sSearch_'.$i] != '' ){
				switch ($i) {
					case 0:
						$where .= " AND id_credito = '".$data['sSearch_'.$i]."'";
						break;
					case 1:
						$where .= " AND (nombres LIKE '%".$data['sSearch_'.$i]."%' OR apellidos LIKE '%".$data['sSearch_'.$i]."%')";
						break;
					case 2:
						$where .= " AND (nombres_cliente LIKE '%".$data['sSearch_'.$i]."%' OR apellidos_cliente LIKE '%".$data['sSearch_'.$i]."%')";
						break;
					case 3:
						$where .= " AND ((valor*((interes/100)+1))-abono) > '".$data['sSearch_'.$i]."'";
						break;
					case 4:
						$where .= " AND ((valor*((interes/100)+1))-abono) < '".$data['sSearch_'.$i]."'";
						break;
					case 5:
						$where .= " AND atrasos > '".$data['sSearch_'.$i]."'";
						break;
					case 6:
						$where .= " AND atrasos < '".$data['sSearch_'.$i]."'";
						break;
				}
			}
		}
		// FIN FILTRO INDIVIDUAL
		if($user=='-1' || $user== -1){
			$user = '';
		} else {
			$user = ' AND users.id_user = "'.$user.'"';
		}
		$consulta = $this->db->query("
			SELECT id_credito, id_cliente, id_user, nombres, apellidos, nombres_cliente, apellidos_cliente, valor, interes, abono, atrasos
			FROM creditos 
			LEFT JOIN users 
				ON usr_fk = id_user 
			LEFT JOIN clientes 
				ON cliente_fk = id_cliente
			LEFT JOIN 
				(SELECT 
					credito_fk, COUNT(id_cobro) AS atrasos
				FROM cobros 
				WHERE fecha_cobro < '".date('Y-m-d')."' 
					AND estado_cobro = '0' 
				GROUP BY credito_fk) AS c
				ON c.credito_fk = id_credito
			WHERE creditos.status != '4'
				AND creditos.status != '0'".$user."
			AND clientes.status = '1'".$where);
		if($consulta->num_rows() > 0 ){
            return $consulta->num_rows();
        } else {
        	return 0;
        }
		/*$total = $this->db->count_all_results('clientes');
		return $total;*/
	}

	public function borrarCredito($id){
		$data = array(
	        'status' => '0'
		);
		$this->db->where('id_credito', $id);
		if($this->db->update('creditos', $data)){
			return 1;
		} else {
			return 2;
		}
	}
}
