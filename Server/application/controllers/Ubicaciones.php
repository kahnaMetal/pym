<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ubicaciones extends CI_Controller {

	public function __construct(){
		//MODIFICAMOS CABECERAS PARA PERMITIR PETICIONES EXTERNAS
		header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    $method = $_SERVER['REQUEST_METHOD'];
    if($method == "OPTIONS") { die(); }
		//HEREDAMOS EL CONSTRUCTOR
  	parent::__construct();
		//LLAMAMOS LOS MODELOS NECESARIOS PARA TRABAJAR
  	$this->load->model('Ubicaciones_model');
	}

	public function childrens(){
		if($_POST){//PREGUNTAMOS SI HAY DATOS ENVIADOR POR POST

			$validate = ($this->input->post('parent')=="" || $this->input->post('parent')==NULL || !is_numeric($this->input->post('parent'))) ? FALSE:TRUE;
			if($validate){
				$cities = $this->Ubicaciones_model->hijos($this->input->post('parent'));
				if($cities!=FALSE){
					$r = array(
						'status' => 'success',
						'statusMessage' => 'Se encontraron '.$cities->num_rows().' ubicaciones',
						'cities' => array()
					);
					foreach ($cities->result() as $row){
						$r['cities'][] = array(
							'id' 						=> $row->idCity,
							'loc' 					=> $row->nameCity
						);
					}
				} else {
					$r = array(
						'status' => 'danger',
						'statusMessage' => 'No se encontraron ciudades',
						'cities' => array()
					);
				}
			} else {
				$r = array(
					'status' => 'danger',
					'statusMessage' => 'No se identifica rango de ciudades a buscar',
					'cities' => array()
				);
			}
			echo json_encode($r);
		} else {
			show_404();
		}
	}

}
