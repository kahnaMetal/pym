<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {

	public function __construct(){
		header('Access-Control-Allow-Origin: *');
	    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	    $method = $_SERVER['REQUEST_METHOD'];
	    if($method == "OPTIONS") {
	        die();
	    }
        parent::__construct();
        $this->load->model('usuarios_model');       
	}

	public function roles(){
		$options = $this->usuarios_model->roles();
		if($options==FALSE){
			$options = 0;
		}
		print_r(json_encode($options));
	}

	public function users(){
		$options = $this->usuarios_model->users();
		if($options==FALSE){
			$options = 0;
		}
		print_r(json_encode($options));
	}

	public function rolPermisos(){
		$options = $this->usuarios_model->rolPermisos($_POST['id']);
		if($options==FALSE){
			$options = 0;
		}
		print_r(json_encode($options));
	}

	public function detalleUsuario(){
		$options = $this->usuarios_model->detalleUsuario($_POST['id']);
		if($options==FALSE){
			$options = 0;
		}
		print_r(json_encode($options));
	}

	public function addRol(){		
		$response = $this->usuarios_model->ingresarRol($_POST);
		switch ($response) {
			case '1':
				$detalle = 'Se registro nuevo rol con el nombre <b>'.$_POST['rol'].'</b>';
				$this->Logs_model->ingresaLogs('crear', 15, $detalle, $_POST['sess']);
				break;
			case '2':
				$detalle = 'Ya existe un rol con el nombre <b>'.$_POST['rol'].'</b>';
				$this->Logs_model->ingresaLogs('info', 15, $detalle, $_POST['sess']);
				break;
			case '3':
				$detalle = 'Error registrando nuevo rol con el nombre <b>'.$_POST['rol'].'</b>';
				$this->Logs_model->ingresaLogs('error', 15, $detalle, $_POST['sess']);
				break;
			case '4':
				$detalle = 'Se registro nuevo rol con el nombre <b>'.$_POST['rol'].'</b> pero no se asignaron los respectivos permisos';
				$this->Logs_model->ingresaLogs('info', 15, $detalle, $_POST['sess']);
				break;
			default:
				$detalle = 'Error de servidor';
				$this->Logs_model->ingresaLogs('error', 15, $detalle, $_POST['sess']);
		}
		echo $response.'||'.$detalle;
	}

	public function addUser(){		
		$response = $this->usuarios_model->ingresarUser($_POST);
		switch ($response) {
			case '1':
				$detalle = 'Se registro nuevo usuario con el nombre <b>'.$_POST['usuario'].'</b>';
				$this->Logs_model->ingresaLogs('crear', 15, $detalle, $_POST['sess']);
				break;
			case '2':
				$detalle = 'Ya existe un usuario con el nombre <b>'.$_POST['usuario'].'</b>';
				$this->Logs_model->ingresaLogs('info', 15, $detalle, $_POST['sess']);
				break;
			case '3':
				$detalle = 'Error registrando nuevo usuario con el nombre <b>'.$_POST['usuario'].'</b>';
				$this->Logs_model->ingresaLogs('error', 15, $detalle, $_POST['sess']);
				break;
			default:
				$detalle = 'Error de servidor';
				$this->Logs_model->ingresaLogs('error', 15, $detalle, $_POST['sess']);
		}
		echo $response.'||'.$detalle;
	}

	public function updateRol(){
		$response = $this->usuarios_model->actualizarRol($_POST);
		switch ($response) {
			case '1':
				$detalle = 'Se actualizaron los permisos para el rol con el nombre <b>'.$_POST['rol'].'</b>';
				$this->Logs_model->ingresaLogs('crear', 15, $detalle, $_POST['sess']);
				break;
			case '2':
				$detalle = 'Error actualizando los permisos para el rol con el nombre <b>'.$_POST['rol'].'</b>';
				$this->Logs_model->ingresaLogs('error', 15, $detalle, $_POST['sess']);
				break;
			default:
				$detalle = 'Error de servidor';
				$this->Logs_model->ingresaLogs('error', 15, $detalle, $_POST['sess']);
		}
		echo $response.'||'.$detalle;
	}

	public function updateUser(){
		$response = $this->usuarios_model->actualizarUsuario($_POST);
		switch ($response) {
			case '1':
				$detalle = 'Se actualizaron los datos de usuario para el usuario con el nombre <b>'.$_POST['user'].'</b>';
				$this->Logs_model->ingresaLogs('crear', 15, $detalle, $_POST['sess']);
				break;
			case '2':
				$detalle = 'Error actualizando los datos de usuario para el usuario con el nombre <b>'.$_POST['user'].'</b>';
				$this->Logs_model->ingresaLogs('error', 15, $detalle, $_POST['sess']);
				break;
			default:
				$detalle = 'Error de servidor';
				$this->Logs_model->ingresaLogs('error', 15, $detalle, $_POST['sess']);
		}
		echo $response.'||'.$detalle;
	}
}
