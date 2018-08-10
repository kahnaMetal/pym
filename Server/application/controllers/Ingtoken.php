<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ingtoken extends CI_Controller {

	public function __construct(){
		header('Access-Control-Allow-Origin: *');
	    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	    $method = $_SERVER['REQUEST_METHOD'];
	    if($method == "OPTIONS") {
	        die();
	    }
        parent::__construct();
        $this->load->model('ingtoken_model');       
	}

	public function registerToken(){
		$credito = $this->ingtoken_model->credito($_POST['token']);
		if($credito!=NULL){
			$response = $this->ingtoken_model->registerToken($_POST['token']);
		} else {
			$response = '2';
		}
		switch ($response) {
			case '1':
				$detalle = 'Se valido el token <b>'.$_POST['token'].'</b> correctamente y se activo el crédito # <b>'.$credito->id_credito.'</b> solicitado el <b>'.$credito->fecha_credito.'</b>';
				$this->Logs_model->ingresaLogs('crear', 18, $detalle, $_POST['sess']);
				break;
			case '2':
				$detalle = 'El TOKEN ingresado es invalido';
				$this->Logs_model->ingresaLogs('info', 18, $detalle, $_POST['sess']);
				break;
			case '3':
				'Error validando el token <b>'.$_POST['token'].'</b> para el crédito # <b>'.$credito->id_credito.'</b> solicitado el <b>'.$credito->fecha_credito.'</b>';
				$this->Logs_model->ingresaLogs('error', 18, $detalle, $_POST['sess']);
				break;
			default:
				$detalle = 'Error de servidor';
				$this->Logs_model->ingresaLogs('error', 18, $detalle, $_POST['sess']);
		}
		echo $response.'||'.$detalle;
	}
}
