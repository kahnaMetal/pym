<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Validate extends CI_Controller {

	public function __construct(){
		header('Access-Control-Allow-Origin: *');
	    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	    $method = $_SERVER['REQUEST_METHOD'];
	    if($method == "OPTIONS") {
	        die();
	    }
        parent::__construct();
        $this->load->model('validate_model');       
	}

	public function index(){		
		if(isset($_POST['login'])){
			$usr = $this->validate_model->login($_POST['login']);					
			if($usr!=FALSE){
				$array = array(
					'id' => $usr->id_user,
					'nombre' => $usr->nombres,
					'apellido' => $usr->apellidos,
					'fecha' => $usr->create,
					'email' => $usr->email,
					'rol' => $usr->fk_rol,
					'ctr' => $usr->status
				);
			} else {				
				$array = array(
					'ctr' => 0
				);
			}
			print_r(json_encode($array));
		}
	}
}
