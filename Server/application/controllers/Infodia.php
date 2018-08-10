<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Infodia extends CI_Controller {

	public function __construct(){
		header('Access-Control-Allow-Origin: *');
	    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	    $method = $_SERVER['REQUEST_METHOD'];
	    if($method == "OPTIONS") {
	        die();
	    }
        parent::__construct();
        $this->load->model('infodia_model');       
	}

	public function consultaCreditos(){
		$options = $this->infodia_model->consultaCreditos($_POST);
		if($options==FALSE){
			$options = 0;
		}
		print_r(json_encode($options));
	}

	public function consultaAbonos(){
		$options = $this->infodia_model->consultaAbonos($_POST);
		if($options==FALSE){
			$options = 0;
		}
		print_r(json_encode($options));
	}
}
