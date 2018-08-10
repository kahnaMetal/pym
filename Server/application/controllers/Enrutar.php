<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Enrutar extends CI_Controller {

	public function __construct(){
		header('Access-Control-Allow-Origin: *');
	    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	    $method = $_SERVER['REQUEST_METHOD'];
	    if($method == "OPTIONS") {
	        die();
	    }
        parent::__construct();
        $this->load->model('enrutar_model');       
	}

	public function clientes(){
		$options = $this->enrutar_model->clientes($_POST['id']);
		if($options==FALSE){
			$options = 0;
		}
		print_r(json_encode($options));
	}

	public function route(){
		$options = $this->enrutar_model->route($_POST['id']);
		if($options==FALSE){
			$options = 0;
		}
		echo $options;
	}

	public function addRoute(){		
		$response = $this->enrutar_model->ordenarRuta($_POST);
		switch ($response) {
			case '1':
				$detalle = 'Se registro nueva ruta con <b>'.count($_POST["ruta"]).'</b> clientes';
				$this->Logs_model->ingresaLogs('crear', 10, $detalle, $_POST['sess']);
				break;
			case '2':
				$detalle = 'Se actualizo la ruta con <b>'.count($_POST["ruta"]).'</b> clientes';
				$this->Logs_model->ingresaLogs('info', 10, $detalle, $_POST['sess']);
				break;
			case '3':
				$detalle = 'Error registrando ruta';
				$this->Logs_model->ingresaLogs('error', 10, $detalle, $_POST['sess']);
				break;
			default:
				$detalle = 'Error de servidor';
				$this->Logs_model->ingresaLogs('error', 10, $detalle, $_POST['sess']);
		}
		echo $response.'||'.$detalle;
	}
}
