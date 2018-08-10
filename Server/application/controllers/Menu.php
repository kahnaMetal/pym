<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends CI_Controller {

	public function __construct(){
		header('Access-Control-Allow-Origin: *');
	    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	    $method = $_SERVER['REQUEST_METHOD'];
	    if($method == "OPTIONS") {
	        die();
	    }
        parent::__construct();
        $this->load->model('menu_model');       
	}

	public function index(){		
		if(isset($_POST['rol'])){
			$m = $this->menu_model->menu($_POST['rol']);
			if($m!=FALSE){
				$array = array(
					'data' => $m,
					'menu' => 1
				);
			} else {				
				$array = array(
					'menu' => 0
				);
			}
		} else {
			$array = array(
				'menu' => 0
			);
		}
		print_r(json_encode($array));
	}
}
