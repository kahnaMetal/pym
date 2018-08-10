<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Credito extends CI_Controller {

	public function __construct(){
		header('Access-Control-Allow-Origin: *');
	    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	    $method = $_SERVER['REQUEST_METHOD'];
	    if($method == "OPTIONS") {
	        die();
	    }
        parent::__construct();
        $this->load->model('credito_model');       
	}

	public function consultaCliente(){
		$clientes = $this->credito_model->clientes($this->input->get('q'), $this->input->get('id'), $this->input->get('global'));
		if($clientes>0){
			foreach ($clientes as $entry) {
	            $rows[] = $entry;
	        }
		} else {
			$rows = NULL;
		}
		print_r(json_encode($rows));		
	}

	public function creditoCliente(){
		$creditos = $this->credito_model->creditoCliente($_POST['id']);
		if($creditos==FALSE){
			$creditos = 0;
		}
		print_r(json_encode($creditos));
	}

	public function addCredit(){
		if($_POST['inputlVigentes']>2){
			$response = 2;
		} else {
			$response = $this->credito_model->ingresarCredito($_POST);
			if($response==1 && $_POST['desembolso']>1000){
				$response = 4;
			}
		}
		switch ($response) {
			case '1':
				$detalle = 'Se registro nuevo credito para el cliente <b>'.$_POST['cliente'].'</b>';
				$this->Logs_model->ingresaLogs('crear', 8, $detalle, $_POST['sess']);
				break;
			case '2':
				$detalle = 'No se pueden tener más de 2 creditos para el cliente <b>'.$_POST['cliente'].'</b>';
				$this->Logs_model->ingresaLogs('info', 8, $detalle, $_POST['sess']);
				break;
			case '3':
				$detalle = 'Error registrando credito para el cliente <b>'.$_POST['cliente'].'</b>';
				$this->Logs_model->ingresaLogs('error', 8, $detalle, $_POST['sess']);
				break;
			case '4':
				$detalle = 'El nuevo credito para el cliente <b>'.$_POST['cliente'].'</b> queda en espera de asignación de <b>Token</b> por tener un valor mayor a $1000';
				$this->Logs_model->ingresaLogs('error', 8, $detalle, $_POST['sess']);
				break;
			case '5':
				$detalle = 'Error registrando fechas de cobro para credito de cliente <b>'.$_POST['cliente'].'</b>';
				$this->Logs_model->ingresaLogs('error', 8, $detalle, $_POST['sess']);
				break;	
			default:
				$detalle = 'Error de servidor';
				$this->Logs_model->ingresaLogs('error', 8, $detalle, $_POST['sess']);
		}
		echo $response.'||'.$detalle;
	}
}
