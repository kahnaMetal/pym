<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gastos extends CI_Controller {

	public function __construct(){
		header('Access-Control-Allow-Origin: *');
	    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	    $method = $_SERVER['REQUEST_METHOD'];
	    if($method == "OPTIONS") {
	        die();
	    }
        parent::__construct();
        $this->load->model('gastos_model');       
	}

	public function consultaUsuario(){
		$usuarios = $this->gastos_model->usuarios($this->input->get('q'));
		if($usuarios>0){
			foreach ($usuarios as $entry) {
	            $rows[] = $entry;
	        }
		} else {
			$rows = NULL;
		}
		print_r(json_encode($rows));		
	}

	public function addSpending(){
		$response = $this->gastos_model->ingresarGasto($_POST);
		switch ($response) {
			case '1':
				$detalle = 'Se registro nuevo gasto con el motivo <b>'.$_POST['motivo'].'</b>';
				$this->Logs_model->ingresaLogs('crear', 11, $detalle, $_POST['sess']);
				break;
			case '2':
				$detalle = 'Error registrando nuevo gasto con el motivo <b>'.$_POST['motivo'].'</b>';
				$this->Logs_model->ingresaLogs('error', 11, $detalle, $_POST['sess']);
				break;
			default:
				$detalle = 'Error de servidor';
				$this->Logs_model->ingresaLogs('error', 11, $detalle, $_POST['sess']);
		}
		echo $response.'||'.$detalle;
	}
}
