<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Productos extends CI_Controller {

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
  	$this->load->model('Productos_model');
	}

	public function list(){
		if($_POST){//PREGUNTAMOS SI HAY DATOS ENVIADOR POR POST
			$start = ($this->input->post('start')=="" || $this->input->post('start')==NULL) || !is_numeric($this->input->post('start'))? 0:$this->input->post('start');
			$limit = ($this->input->post('limit')=="" || $this->input->post('limit')==NULL) || !is_numeric($this->input->post('limit'))? 10:$this->input->post('limit');
			$search = ($this->input->post('search')=="") ? NULL:$this->input->post('search');
			$productos = $this->Productos_model->listar($start, $limit, $search);
			if($productos!=FALSE){
				$r = array(
					'status' => 'success',
					'statusMessage' => 'Se encontraron '.$productos->num_rows().' productos',
					'data' => array()
				);
				foreach ($productos->result() as $row){
					$r['data'][] = array(
						'id' 						=> $row->idProduct,
						'nombre' 				=> $row->nameProduct,
						'imagen' 				=> $row->imgProduct,
						'categoria' 		=> $row->categoryProduct,
						'cantidad' 			=> $row->qtyProduct,
						'valor' 				=> $row->valueProduct
					);
				}
			} else {
				$r = array(
					'status' => 'danger',
					'statusMessage' => 'No se encontraron productos',
					'data' => array()
				);
			}
			echo json_encode($r);
		} else {
			show_404();
		}
	}

	public function insertUpdate(){
			if($_POST){//PREGUNTAMOS SI HAY DATOS ENVIADOR POR POST
				//VALIDAMOS LOS CAMPOS OBLIGATORIOS
				$validate = TRUE;
				$validate = ( $this->input->post('name') == "" || $this->input->post('name') == NULL ) ? FALSE:$validate;
				$validate = ( $this->input->post('qty') == "" || $this->input->post('qty') == NULL ) ? FALSE:$validate;
				$validate = ( $this->input->post('value') == "" || $this->input->post('value') == NULL ) ? FALSE:$validate;

				if($validate){//COMPROVAMOS VALIDACIÓN DE CAMPOS
					// CREAMOS LA CONFIGURACIÓN DE LOS ARCHIVOS A SUBIR
					$config = [
							"upload_path" 	=> './assets/images/products',
							"allowed_types"	=> 'png|jpg|jpeg|gif'
					];
					$this->load->library("upload", $config);// LLAMAMOS LIBRERIA DE CI PARA SUBIR ARCHIVOS
					//CONSULTAMOS SI SE VA A ACTUALIZAR O A REGISTRAR EN LA BASE DE DATOS
					switch ($this->input->post('action')) {
						case '1'://SI ES PARA INGRESAR
							//CONSULTAMOS SI EXISTE EL NOMBRE DEL PRODUCTO
							$productoNombre = $this->Productos_model->consultarProductoNombre($this->input->post('name'));
							if( $productoNombre->num_rows() > 0 ){//VERIFICAMOS SI PRODUCTO YA EXISTE
									$r = array(
										'status' => 'danger',
										'statusMessage' => 'Ya existe un producto registrado con el nombre '.$this->input->post('name')
									);
							} else {//SI NO EXISTE PRODUCTO SE REGISTRA
								if($this->upload->do_upload('img')){
									$infoImg = array("upload_data" => $this->upload->data());
									$data =array(//CREAMOS DATOS PARA LA BASE DE DATOS
						          'nameProduct'     								=> $this->input->post('name'),
						          'imgProduct'	  									=> $infoImg['upload_data']['file_name'],
						          'categoryProduct'	  	 						=> ($this->input->post('cat') == "") ? NULL:$this->input->post('cat'),
						          'qtyProduct'	 										=> $this->input->post('qty'),
						          'valueProduct'	 									=> $this->input->post('value'),
											'statusProduct'	 									=> '1',
											'createdBy_Product'								=> 1,
											'createdAt_Product'								=> date('Y-m-d H:i:s')
					        );
									//SE PASAN LOS DATOS AL MODELO PARA REGISTRAR EN LA BASE DE DATOS
									$response = $this->Productos_model->registrar($data);
									$r = array(
										'status' => $response['status'],
										'statusMessage' => $response['message'],
										'data' => $data
									);
									//REGISTRAMOS LA ACTIVIDAD REALIZADA
									$maxId = $this->Logs_model->maxId('idProduct', 'products');
									$this->Logs_model->iLog(3, $response['message'], $response['status'], 'products', $maxId, 1);
								} else {
									$r = array(
										'status' => 'danger',
										'statusMessage' => $this->upload->display_errors()
									);
								}
							}
							break;
						case '2'://SI ES PARA ACTUALIZAR
							//VERIFICAMOS QUE SI SE PASE LA IDENTIFICACIÓN DEL USUARIO
							if(( $this->input->post('idProduct') == "" || $this->input->post('idProduct') == NULL ) ? FALSE:TRUE){
								$producto = $this->Productos_model->consultarProducto($this->input->post('idProduct'));
								$valorProducto = $producto->row();
								$data =array(//CREAMOS DATOS PARA LA BASE DE DATOS
										'statusProduct'	 								=> '1',
										'updatedBy_Product'							=> 1,
										'updatedAt_Product'							=> date('Y-m-d H:i:s')
								);
								$existUpdate = FALSE;
								$rowsUpdate = '';
								$existNameProduct = FALSE;
								$dontUploadImage = FALSE;
								//VERIFICAMOS SI CAMBIO EL NOMBRE DEL PRODUCTO
								if($this->input->post('name') != $valorProducto->nameProduct){
									//CONSULTAMOS SI EXISTE EL NOMBRE DEL PRODUCTO
									$productoNombre = $this->Productos_model->consultarProductoNombre($this->input->post('name'));
									if($productoNombre->num_rows() > 0 ){//VERIFICAMOS SI PRODUCTO YA EXISTE
											$existNameProduct = TRUE;
									} else {//SI NO EXISTE PRODUCTO SE CREA ACTIVIDAD
											$data['nameProduct'] = $this->input->post('name');
											$existUpdate = TRUE;
											$rowsUpdate .= '<li>Nombre <strong>'.$valorProducto->nameProduct.'</strong> por <strong>'.$this->input->post('name').'</strong></li>';
									}
								}
								//VERIFICAMOS SI CAMBIO LA CATEGORIA
								$cat = ($this->input->post('cat') == "") ? NULL:$this->input->post('cat');
								if($cat != $valorProducto->categoryProduct){
									$data['categoryProduct'] = $cat;
									$existUpdate = TRUE;
									if($valorProducto->categoryProduct==NULL){
										$valorProducto->categoryProduct = '(Ningún Valor)';
									}
									if($cat==NULL){
										$cat = '(Ningún Valor)';
									}
									$rowsUpdate .= '<li>Categoría <strong>'.$valorProducto->categoryProduct.'</strong> por <strong>'.$cat.'</strong></li>';
								}
								//VERIFICAMOS SI CAMBIO LA CANTIDAD
								if($this->input->post('qty') != $valorProducto->qtyProduct){
									$data['qtyProduct'] = $this->input->post('qty');
									$existUpdate = TRUE;
									$rowsUpdate .= '<li>Cantidad <strong>'.$valorProducto->qtyProduct.'</strong> por <strong>'.$this->input->post('qty').'</strong></li>';
								}
								//VERIFICAMOS SI CAMBIO EL VALOR
								if($this->input->post('value') != $valorProducto->valueProduct){
									$data['valueProduct'] = $this->input->post('value');
									$existUpdate = TRUE;
									$rowsUpdate .= '<li>Valor <strong>$'.$valorProducto->valueProduct.'</strong> por <strong>$'.$this->input->post('value').'</strong></li>';
								}
								//VERIFICAMOS SI SE ENVIO UN ARCHIVO
								if( !empty($_FILES) ){
									if($this->upload->do_upload('img')){//VERIFICAMOS SI SUBIO EL ARCHIVO
										//ELIMINAMOS IMAGEN ANTERIOR
										unlink('./assets/images/products/'.$valorProducto->imgProduct);
										//ASIGNAMOS NUEVO VALOR
										$infoImg = array("upload_data" => $this->upload->data());
										$data['imgProduct'] = $infoImg['upload_data']['file_name'];
										$existUpdate = TRUE;
										$rowsUpdate .= '<li>Imagen <strong>'.$valorProducto->imgProduct.'</strong> por <strong>'.$infoImg['upload_data']['file_name'].'</strong></li>';
									} else {
										$dontUploadImage = TRUE;
									}
								}

								if($existNameProduct){
									$r = array(
										'status' => 'danger',
										'statusMessage' => 'Ya existe un producto registrado con el nombre '.$this->input->post('name')
									);
								} elseif($dontUploadImage){
									$r = array(
										'status' => 'danger',
										'statusMessage' => $this->upload->display_errors()
									);
								}else {
									if($existUpdate){
										$response = $this->Productos_model->actualizar($data, $this->input->post('idProduct'));
										$messageUpdate = 'Se actualizaron los siguientes valores:<br><ul>'.$rowsUpdate.'</ul>';
										$this->Logs_model->iLog(4, $messageUpdate, $response['status'], 'products', $this->input->post('idProduct'), 1);
										$messageStatus = $response['status'];
										$messageUpdate = $response['message'];
									} else {
										$messageStatus = 'warning';
										$messageUpdate = 'No se actualizo ningún registro, los datos enviados son iguales a los registrados';
									}
									$r = array(
										'status' => $messageStatus,
										'statusMessage' => $messageUpdate,
										'data' => $data
									);
								}
							} else {
								$r = array(
									'status' => 'danger',
									'statusMessage' => 'No se identifica producto a actualizar'
								);
							}
							break;
					}
				} else {
					$r = array(
						'status' => 'danger',
						'statusMessage' => 'Todos los campos con * son obligatorios'
					);
				}
				echo json_encode($r);
			} else {//SI NO HAY DATOS ENVIADOS POR POST MOSTRAMOS UN MENSAJE
				show_404();
			}
	}

	public function delete(){
		if($_POST){//PREGUNTAMOS SI HAY DATOS ENVIADOR POR POST
			//VERIFICAMOS QUE SI SE PASE LA IDENTIFICACIÓN DEL PRODUCTO
			if(( $this->input->post('idProduct') == "" || $this->input->post('idProduct') == NULL ) ? FALSE:TRUE){
				$response = $this->Productos_model->eliminar($this->input->post('idProduct'));
				$this->Logs_model->iLog(5, $response['message'], $response['status'], 'products', $this->input->post('idProduct'), 1);
				$r = array(
					'status' => $response['status'],
					'statusMessage' => $response['message']
				);
			} else {
				$r = array(
					'status' => 'danger',
					'statusMessage' => 'No se identifica producto a eliminar'
				);
			}
			echo json_encode($r);
		} else {
			show_404();
		}
	}
}
