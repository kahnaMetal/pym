<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Parametrizacion extends CI_Controller {

	public function __construct(){
		header('Access-Control-Allow-Origin: *');
	    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	    $method = $_SERVER['REQUEST_METHOD'];
	    if($method == "OPTIONS") {
	        die();
	    }
        parent::__construct();  
	}

	public function allParams(){
		$params = $this->Logs_model->params();
		if($params>0){
			print_r(json_encode($params));	
		} else {
			echo 0;
		}		
	}

	/*public function consultaParametro(){
		$clientes = $this->clientes_model->clientes($this->input->get('q'), $this->input->get('id'));
		if($clientes>0){
			foreach ($clientes as $entry) {
	            $rows[] = $entry;
	        }
		} else {
			$rows = NULL;
		}
		print_r(json_encode($rows));		
	}*/

	public function updateParams(){
		$response = $this->Logs_model->updateParams($_POST);
		switch ($response) {
			case '1':
				$detalle = 'Se actualizaron los parametros del sistema';
				$this->Logs_model->ingresaLogs('crear', 22, $detalle, $_POST['sess']);
				break;
			case '2':
				$detalle = 'Error actualizando los parametros del sistema';
				$this->Logs_model->ingresaLogs('error', 22, $detalle, $_POST['sess']);
				break;
			default:
				$detalle = 'Error de servidor';
				$this->Logs_model->ingresaLogs('error', 22, $detalle, $_POST['sess']);
		}
		echo $response;
	}
}
