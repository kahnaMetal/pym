<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Base extends CI_Controller {

	public function __construct(){
		header('Access-Control-Allow-Origin: *');
	    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	    $method = $_SERVER['REQUEST_METHOD'];
	    if($method == "OPTIONS") {
	        die();
	    }
        parent::__construct();
        $this->load->model('base_model');       
	}

	public function consultaUsuario(){
		$usuarios = $this->base_model->usuarios($this->input->get('q'));
		if($usuarios>0){
			foreach ($usuarios as $entry) {
	            $rows[] = $entry;
	        }
		} else {
			$rows = NULL;
		}
		print_r(json_encode($rows));		
	}

	public function addBase(){
		$response = $this->base_model->ingresarBase($_POST);
		switch ($response) {
			case '1':
				$detalle = 'Se registro base para usuario # <b>'.$_POST['user'].'</b>';
				$this->Logs_model->ingresaLogs('crear', 20, $detalle, $_POST['sess']);
				break;
			case '2':
				$detalle = 'Error registrando base para usuario # <b>'.$_POST['user'].'</b>';
				$this->Logs_model->ingresaLogs('error', 20, $detalle, $_POST['sess']);
				break;
			case '3':
				$detalle = 'El usuario # <b>'.$_POST['user'].'</b> ya tiene una base registrada';
				$this->Logs_model->ingresaLogs('error', 20, $detalle, $_POST['sess']);
				break;
			default:
				$detalle = 'Error de servidor';
				$this->Logs_model->ingresaLogs('error', 20, $detalle, $_POST['sess']);
		}
		echo $response.'||'.$detalle;
	}
}
