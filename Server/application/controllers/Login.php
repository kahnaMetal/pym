<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct(){
		header('Access-Control-Allow-Origin: *');
	    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	    $method = $_SERVER['REQUEST_METHOD'];
	    if($method == "OPTIONS") {
	        die();
	    }
        parent::__construct();                  
	}

	public function index(){		
		if(isset($_POST['password'])){			
			$this->load->model('login_model');
			$id = $this->login_model->login($_POST['username'],md5($_POST['password']));		
			if($id>0){
				$array = array(
					'ses' => $id.':'.md5(trim(strtolower($_POST['username']), " \t\n\r\0\x0B")),
					'ctr' => 1
				);
			} else {				
				$array = array(
					'ctr' => 0
				);
			}
			print_r(json_encode($array));
		}
	}

	public function kill()
	{		
		$this->session->sess_destroy();
		// INICIO REMOVEMOS CACHE <!-- en algunos navegadores puede acceder, ésto es porque la página sigue existiendo en la caché del navegador-->
		$this->output->set_header('Last-Modified:'.gmdate('D, d M Y H:i:s').'GMT');
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header('Cache-Control: post-check=0, pre-check=0',false);
		$this->output->set_header('Pragma: no-cache');
		// FIN REMOVEMOS CACHE 
		echo 1;
	}
}
