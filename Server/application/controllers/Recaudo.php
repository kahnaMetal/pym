<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Recaudo extends CI_Controller {

	public function __construct(){
		header('Access-Control-Allow-Origin: *');
	    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	    $method = $_SERVER['REQUEST_METHOD'];
	    if($method == "OPTIONS") {
	        die();
	    }
        parent::__construct();
        $this->load->model('recaudo_model');       
	}

	public function consultaCliente(){
		$clientes = $this->recaudo_model->clientes($this->input->get('q'), $this->input->get('id'), $this->input->get('global'));
		if($clientes>0){
			foreach ($clientes as $entry) {
	            $rows[] = $entry;
	        }
		} else {
			$rows = NULL;
		}
		print_r(json_encode($rows));		
	}

	public function consultaClienteGen(){
		$clientes = $this->recaudo_model->clientesGen($this->input->get('q'));
		if($clientes>0){
			foreach ($clientes as $entry) {
	            $rows[] = $entry;
	        }
		} else {
			$rows = NULL;
		}
		print_r(json_encode($rows));		
	}

	public function recaudoCliente(){
		$recaudos = $this->recaudo_model->recaudoCliente($_POST['id']);
		if($recaudos==FALSE){
			$recaudos = 0;
		}
		print_r(json_encode($recaudos));
	}

	public function borrarRecaudo(){
		$response = $this->recaudo_model->borrarRecaudo($_POST);
		switch ($response) {
			case '1':
				$detalle = 'Se elimino recaudo de <b>$'.$_POST['val'].'</b> correspondiente al crédito <b>'.$_POST['cre'];
				$this->Logs_model->ingresaLogs('crear', 21, $detalle, $_POST['sess']);
				break;
			case '2':
				$detalle = 'Error eliminando recaudo de <b>$'.$_POST['val'].'</b> correspondiente al crédito <b>'.$_POST['cre'];
				$this->Logs_model->ingresaLogs('error', 21, $detalle, $_POST['sess']);
				break;
			default:
				$detalle = 'Error de servidor';
				$this->Logs_model->ingresaLogs('error', 21, $detalle, $_POST['sess']);
		}
		echo $response;
	}

	public function abonos(){
		$recaudos = $this->recaudo_model->abonos($_POST['id']);
		if($recaudos==FALSE){
			$recaudos = 0;
		}
		print_r(json_encode($recaudos));
	}

	public function addColection(){
		$response = $this->recaudo_model->ingresarRecaudo($_POST);
		$res = explode("::", $response);
		/*if($response==1 && $_POST['desembolso']>200){
			$response = 4;
		}*/
		switch ($res[0]) {
			case '1':
				$detalle = 'Se registro nuevo recaudo para el credito # <b>'.$_POST['id_credito'].'</b>
							<br><strong>Saldo Restante: </strong> '.$res[1];
				$this->Logs_model->ingresaLogs('crear', 9, $detalle, $_POST['sess']);
				break;
			case '2':
				$detalle = 'Error relacionando recaudo para el credito # <b>'.$_POST['id_credito'].'</b>';
				$this->Logs_model->ingresaLogs('info', 9, $detalle, $_POST['sess']);
				break;
			case '3':
				$detalle = 'Error registrando recaudo para el credito # <b>'.$_POST['id_credito'].'</b>';
				$this->Logs_model->ingresaLogs('error', 9, $detalle, $_POST['sess']);
				break;	
			case '4':
				$detalle = 'Error registrando cobros para  el recaudo del credito # <b>'.$_POST['id_credito'].'</b>';
				$this->Logs_model->ingresaLogs('error', 9, $detalle, $_POST['sess']);
				break;	
			default:
				$detalle = 'Error de servidor';
				$this->Logs_model->ingresaLogs('error', 9, $detalle, $_POST['sess']);
		}
		echo $res[0].'||'.$detalle;
	}
}
