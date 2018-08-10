<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perfil extends CI_Controller {

	public function __construct(){
		header('Access-Control-Allow-Origin: *');
	    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	    $method = $_SERVER['REQUEST_METHOD'];
	    if($method == "OPTIONS") {
	        die();
	    }
        parent::__construct();
        $this->load->model('perfil_model');       
	}

	public function changePasswd(){
		$response = $this->perfil_model->changePasswd($_POST);
		switch ($response) {
			case '1':
				$detalle = 'Se actualizo la contraseña del usuario';
				$this->Logs_model->ingresaLogs('crear', 14, $detalle, $_POST['sess']);
				break;
			case '2':
				$detalle = 'Error actualizando la contraseña del usuario';
				$this->Logs_model->ingresaLogs('error', 14, $detalle, $_POST['sess']);
				break;
			default:
				$detalle = 'Error de servidor';
				$this->Logs_model->ingresaLogs('error', 14, $detalle, $_POST['sess']);
		}
		echo $response.':'.$detalle;
	}

	public function updateUser(){
		$response = $this->perfil_model->updateUser($_POST);
		switch ($response) {
			case '1':
				$detalle = 'Se actualizaron los datos del usuario con el nombre <b>'.$_POST['apellidos'].', '.$_POST['nombres'].'</b>';
				$this->Logs_model->ingresaLogs('crear', 14, $detalle, $_POST['sess']);
				break;
			case '2':
				$detalle = 'Error actualizando los datos del usuario con el nombre <b>'.$_POST['apellidos'].', '.$_POST['nombres'].'</b>';
				$this->Logs_model->ingresaLogs('error', 14, $detalle, $_POST['sess']);
				break;
			default:
				$detalle = 'Error de servidor';
				$this->Logs_model->ingresaLogs('error', 14, $detalle, $_POST['sess']);
		}
		echo $response.':'.$detalle;
	}
}
