<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Detclientes extends CI_Controller{

	public function __construct(){
		header('Access-Control-Allow-Origin: *');
	    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	    $method = $_SERVER['REQUEST_METHOD'];
	    if($method == "OPTIONS") {
	        die();
	    }
        parent::__construct();
        $this->load->model('detclientes_model');       
	}

	public function listado($user, $borrar){			
		$response = $this->detclientes_model->clientes($_POST, $user);
		//$_POST['iDisplayLength'], $_POST['iDisplayStart']							
		if($response=='0'){
			$total_clientes = $this->detclientes_model->total_clientes($_POST, $user);
			$filtrado_de = 0;
			$aaData = array();
		} else {
			$filtrado_de = count($response);
			$total_clientes = $this->detclientes_model->total_clientes($_POST, $user);
			
			foreach($response as $cliente) {
				if($cliente->apellidos==NULL && $cliente->nombres==NULL){
					$cli = '<b>No registra prestamo aún</b>';
				} else {
					$cli = $cliente->apellidos.', '.$cliente->nombres;
				}
				if($borrar=='Y'){
					$timesDel = '<button data-id="'.$cliente->id_credito.'" class="btn red no-padding delete-credito"><i class="fa fa-times"></i></button>';
				} else {
					$timesDel = '';
				}
				if($cliente->atrasos>=3){
					$aaData[] = array(	 
		        	"DT_RowId" => $cliente->id_credito,
			        	'0' => '<span class="font-red">'.$cliente->id_credito.'</span>',
			            '1' => '<span class="font-red">'.$cli.'</span>',
			            '2' => '<span class="font-red">'.$cliente->apellidos_cliente.', '.$cliente->nombres_cliente.'</span>',
			            '3' => '<span class="font-red">$'.round(($cliente->valor * ( ($cliente->interes / 100) + 1 ) ) - $cliente->abono, 2).'</span>',
			            '4' => '<span class="font-red">'.$cliente->atrasos.'</span>',
			            '5' => $timesDel, 
			            '6' => ''
			        ); 
				} else {
					if($cliente->atrasos=='' || $cliente->atrasos==NULL){$cliente->atrasos = 0;}
					$aaData[] = array(	 
			        	"DT_RowId" => $cliente->id_credito,
			        	'0' => $cliente->id_credito,   	
			            '1' => $cli,
			            '2' => $cliente->apellidos_cliente.', '.$cliente->nombres_cliente,
			            '3' => '$'.round(($cliente->valor * ( ($cliente->interes / 100) + 1 ) ) - $cliente->abono, 2),
			            '4' => $cliente->atrasos, 
			            '5' => $timesDel, 
			            '6' => ''
			        ); 	
				}
		    }
		}		

	    $aa = array(
	        'sEcho' => $_POST['sEcho'],
	        'iTotalRecords' => $filtrado_de,
	        'iTotalDisplayRecords' => $total_clientes,
	        'cadena_post' => $_POST,
	        'aaData' => $aaData);
	 
	    print_r(json_encode($aa));
	}

	public function borrarCredito(){
		$response = $this->detclientes_model->borrarCredito($_POST['id']);
		switch ($response) {
			case '1':
				$detalle = 'Se elimino crédito # <b>'.$_POST['id'].'</b>';
				$this->Logs_model->ingresaLogs('crear', 19, $detalle, $_POST['sess']);
				break;
			case '2':
				$detalle = 'Error eliminando crédito #  <b>'.$_POST['id'].'</b>';
				$this->Logs_model->ingresaLogs('error', 19, $detalle, $_POST['sess']);
				break;
			default:
				$detalle = 'Error de servidor';
				$this->Logs_model->ingresaLogs('error', 19, $detalle, $_POST['sess']);
		}
		echo $response;
	}
}
