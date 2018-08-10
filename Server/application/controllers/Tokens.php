<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tokens extends CI_Controller {

	public function __construct(){
		header('Access-Control-Allow-Origin: *');
	    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	    $method = $_SERVER['REQUEST_METHOD'];
	    if($method == "OPTIONS") {
	        die();
	    }
        parent::__construct();
        $this->load->model('tokens_model');       
	}

	public function loadTokens(){
		$tokens = $this->tokens_model->loadTokens();
		if($tokens==FALSE){
			$tokens = 0;
		}
		print_r(json_encode($tokens));
	}

	public function refuseToken(){		
		$response = $this->tokens_model->refuseToken($_POST);
		switch ($response) {
			case '1':
				$detalle = 'Se rechazo token para crédito con id <b>'.$_POST["id"].'</b>';
				$this->Logs_model->ingresaLogs('crear', 17, $detalle, $_POST['sess']);
				break;
			case '2':
				$detalle = 'Error rechazando token para crédito con id <b>'.$_POST["id"].'</b>';
				$this->Logs_model->ingresaLogs('error', 17, $detalle, $_POST['sess']);
				break;
			default:
				$detalle = 'Error de servidor';
				$this->Logs_model->ingresaLogs('error', 17, $detalle, $_POST['sess']);
		}
		echo $response.'||'.$detalle;
	}

	public function approveToken(){		
		$response = $this->tokens_model->approveToken($_POST);
		$res = explode('||', $response);
		switch ($res[0]) {
			case '1':
				$detalle = 'Se rechazo token para crédito con id <b>'.$_POST["id"].'</b>';
				$this->Logs_model->ingresaLogs('crear', 17, $detalle, $_POST['sess']);
				break;
			case '2':
				$detalle = 'Error rechazando token para crédito con id <b>'.$_POST["id"].'</b>';
				$this->Logs_model->ingresaLogs('error', 17, $detalle, $_POST['sess']);
				break;
			default:
				$detalle = 'Error de servidor';
				$this->Logs_model->ingresaLogs('error', 17, $detalle, $_POST['sess']);
		}
		if(isset($res[1])){
			echo $res[0].'||'.$detalle.'||'.$res[1];
		} else {
			echo $res[0].'||'.$detalle;
		}
	}
}
