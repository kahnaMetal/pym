<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Balance extends CI_Controller {

	public function __construct(){
		header('Access-Control-Allow-Origin: *');
	    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	    $method = $_SERVER['REQUEST_METHOD'];
	    if($method == "OPTIONS") {
	        die();
	    }
        parent::__construct();
        $this->load->model('balance_model');       
	}

	public function consultaUsuario(){
		$usuarios = $this->balance_model->usuarios($this->input->get('q'));
		if($usuarios>0){
			foreach ($usuarios as $entry) {
	            $rows[] = $entry;
	        }
		} else {
			$rows = NULL;
		}
		print_r(json_encode($rows));		
	}

	public function general(){
		if(!isset($_POST['fecha']) || $_POST['fecha']==''){
			$fecha = date('Y-m-d');
		} else {
			$fecha = $_POST['fecha'];
		}
		$options = $this->balance_model->balanceGeneral($fecha, $_POST['usr']);
		if($options==FALSE){
			$options = 0;
		}
		print_r(json_encode($options));
	}

	public function detalleGastos(){
		if(!isset($_POST['fecha']) || $_POST['fecha']==''){
			$fecha = date('Y-m-d');
		} else {
			$fecha = $_POST['fecha'];
		}
		if(isset($_POST['usr'])){
			$usr = $_POST['usr'];
		} else {
			$usr = '';
		}
		$options = $this->balance_model->detalleGastos($fecha, $usr);
		if($options==FALSE){
			$options = 0;
		}
		print_r(json_encode($options));
	}
	
}
