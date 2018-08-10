<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clientes extends CI_Controller {

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
  	$this->load->model('Clientes_model');
	}

	public function list(){
		if($_POST){//PREGUNTAMOS SI HAY DATOS ENVIADOR POR POST
			$start = ($this->input->post('start')=="" || $this->input->post('start')==NULL) || !is_numeric($this->input->post('start'))? 0:$this->input->post('start');
			$limit = ($this->input->post('limit')=="" || $this->input->post('limit')==NULL) || !is_numeric($this->input->post('limit'))? 10:$this->input->post('limit');
			$search = ($this->input->post('search')=="") ? NULL:$this->input->post('search');
			$clientes = $this->Clientes_model->listar($start, $limit, $search);
			if($clientes!=FALSE){
				$r = array(
					'status' => 'success',
					'statusMessage' => 'Se encontraron '.$clientes->num_rows().' clientes',
					'data' => array()
				);
				foreach ($clientes->result() as $row){
					$r['data'][] = array(
						'id' 						=> $row->idCustomer,
						'tipo' 					=> $row->typeCustomer,
						'documento' 		=> $row->documentCustomer,
						'nombre' 				=> $row->nameCustomer,
						'apellido' 			=> $row->lastnameCustomer,
						'nit' 					=> $row->nitCustomer,
						'digito' 				=> $row->businessDigitCustomer,
						'razon_social' 	=> $row->businessNameCustomer
					);
				}
			} else {
				$r = array(
					'status' => 'danger',
					'statusMessage' => 'No se encontraron clientes',
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
				$validate = ( $this->input->post('type') != "1" || $this->input->post('type') != "2" ) ? FALSE:$validate;
				$validate = ( $this->input->post('phone') == "" || $this->input->post('phone') == NULL ) ? FALSE:$validate;
				$validate = ( $this->input->post('address') == "" || $this->input->post('address') == NULL ) ? FALSE:$validate;
				//VERIFICAMOS SI ES PERSONA NATURAL O JURIDICA
				if($this->input->post('type')=="1"){
					//RESETEAMOS CAMPOS DE PERSONA JURIDICA
					$nit = NULL; $digit = NULL; $company = NULL;
					//DECLARAMOS CAMPOS DE PERSONA NATURAL PARA ENVIAR A LA DATABASE
					$document = $this->input->post('document');
					$names = $this->input->post('name');
					$lastnames = $this->input->post('lastname');
					//VALIDAMOS LOS CAMPOS OBLIGATORIOS DE PERSONA NATURAL
					$validate = ( $document == "" || $document == NULL ) ? FALSE:$validate;
					$validate = ( $names == "" || $names == NULL ) ? FALSE:$validate;
					$validate = ( $lastnames == "" || $lastnames == NULL ) ? FALSE:$validate;
				} elseif($this->input->post('type')=="2"){
					//RESETEAMOS CAMPOS DE PERSONA NATURAL
					$document = NULL; $names = NULL; $lastnames = NULL;
					//DECLARAMOS CAMPOS DE PERSONA JURIDICA PARA ENVIAR A LA DATABASE
					$nit = $this->input->post('nit');
					$digit = $this->input->post('digit');
					$company = $this->input->post('company');
					//VALIDAMOS LOS CAMPOS OBLIGATORIOS DE PERSONA JURIDICA
					$validate = ( $nit == "" || $nit == NULL ) ? FALSE:$validate;
					$validate = ( $digit == "" || $digit == NULL ) ? FALSE:$validate;
					$validate = ( $company == "" || $company == NULL ) ? FALSE:$validate;
				} else {

				}
				//CONSULTAMOS SI EXISTE PERSONA O EMPRESA
				switch ($this->input->post('type')) {
					case '1':
						$cliente = $this->Clientes_model->consultarPersona($document);
						$messageCustomerExist = 'Ya existe una persona registrada con el número de identificación '.$document;
						break;
					case '2':
						$cliente = $this->Clientes_model->consultarEmpresa($nit);
						$messageCustomerExist = 'Ya existe una empresa registrada con el nit '.$nit;
						break;
				}
				if($validate){//COMPROVAMOS VALIDACIÓN DE CAMPOS
					//CONSULTAMOS SI SE VA A ACTUALIZAR O A REGISTRAR EN LA BASE DE DATOS
					switch ($this->input->post('action')) {
						case '1'://SI ES PARA INGRESAR
							if($cliente->num_rows() > 0 ){//VERIFICAMOS SI CLIENTE YA EXISTE
									$r = array(
										'status' => 'danger',
										'statusMessage' => $messageCustomerExist
									);
							} else {//SI NO EXISTE CLIENTE SE REGISTRA
								$data =array(//CREAMOS DATOS PARA LA BASE DE DATOS
					          'typeCustomer'     								=> $this->input->post('type'),
					          'documentCustomer'	  						=> $document,
					          'nameCustomer'	  	 							=> $names,
					          'lastnameCustomer'	 							=> $lastnames,
					          'nitCustomer '	 									=> $nit,
					          'businessDigitCustomer'   				=> $digit,
										'businessNameCustomer'						=> $company,
										'phoneCustomer'		 								=> $this->input->post('phone'),
					          'cellphoneCustomer'     					=> ($this->input->post('cellphone') == "") ? NULL:$this->input->post('cellphone'),
					          'emailCustomer'	     							=> ($this->input->post('email') == "") ? NULL:$this->input->post('email'),
					          'addressCustomer'	   							=> $this->input->post('address'),
					          'cityCustomer_fkCities'				    => ($this->input->post('city') == "") ? NULL:$this->input->post('city'),
										'statusCustomer'	 								=> '1',
										'createdBy_Customer'							=> 1,
										'createdAt_Customer'							=> date('Y-m-d H:i:s')
				        );
								//SE PASAN LOS DATOS AL MODELO PARA REGISTRAR EN LA BASE DE DATOS
								$response = $this->Clientes_model->registrar($data);
								$r = array(
									'status' => $response['status'],
									'statusMessage' => $response['message'],
									'data' => $data
								);
								//REGISTRAMOS LA ACTIVIDAD REALIZADA
								$maxId = $this->Logs_model->maxId('idCustomer', 'customers');
								$this->Logs_model->iLog(3, $response['message'], $response['status'], 'customers', $maxId, 1);
							}
							break;
						case '2'://SI ES PARA ACTUALIZAR
							//VERIFICAMOS QUE SI SE PASE LA IDENTIFICACIÓN DEL USUARIO
							if(( $this->input->post('idCustomer') == "" || $this->input->post('idCustomer') == NULL ) ? FALSE:TRUE){
								$valorCliente = $cliente->row();
								$data =array(//CREAMOS DATOS PARA LA BASE DE DATOS
										'statusCustomer'	 								=> '1',
										'updatedBy_Customer'							=> 1,
										'updatedAt_Customer'							=> date('Y-m-d H:i:s')
								);
								$existUpdate = FALSE;
								$rowsUpdate = '';
								//VERIFICAMOS SI ES PERSONA NATURAL O JURIDICA
								if($this->input->post('type')=="1"){
									//VERIFICAMOS SI CAMBIO EL DOCUMENTO DE IDENTIDAD
									if($document != $valorCliente->documentCustomer){
										$rowsUpdate .= '<li><del>Documento <strong>'.$valorCliente->documentCustomer.'</strong> por <strong>'.$document.'</strong></del>(No se permite cambiar documento de identidad)</li>';
									}
									//VERIFICAMOS SI CAMBIO EL NOMBRE(S)
									if($names != $valorCliente->nameCustomer){
										$data['nameCustomer'] = $names;
										$existUpdate = TRUE;
										$rowsUpdate .= '<li>Nombre(s) <strong>'.$valorCliente->nameCustomer.'</strong> por <strong>'.$names.'</strong></li>';
									}
									//VERIFICAMOS SI CAMBIO EL APELLIDO(S)
									if($lastnames != $valorCliente->lastnameCustomer){
										$data['lastnameCustomer'] = $lastnames;
										$existUpdate = TRUE;
										$rowsUpdate .= '<li>Nombre(s) <strong>'.$valorCliente->lastnameCustomer.'</strong> por <strong>'.$lastnames.'</strong></li>';
									}
								} elseif($this->input->post('type')=="2"){
									//VERIFICAMOS SI CAMBIO EL NIT
									if($nit != $valorCliente->nitCustomer){
										$rowsUpdate .= '<li><del>Nit <strong>'.$valorCliente->nitCustomer.'</strong> por <strong>'.$nit.'</strong></del>(No se permite cambiar nit)</li>';
									}
									//VERIFICAMOS SI CAMBIO EL DIGITO DE VERIFICACIÓN
									if($digit != $valorCliente->businessDigitCustomer){
										$rowsUpdate .= '<li><del>Digito Verificación <strong>'.$valorCliente->businessDigitCustomer.'</strong> por <strong>'.$digit.'</strong></del>(No se permite cambiar el digito de verificación)</li>';
									}
									//VERIFICAMOS SI CAMBIO LA RAZÓN SOCIAL
									if($company != $valorCliente->businessNameCustomer){
										$data['businessNameCustomer'] = $company;
										$existUpdate = TRUE;
										$rowsUpdate .= '<li>Razón Social <strong>'.$valorCliente->businessNameCustomer.'</strong> por <strong>'.$company.'</strong></li>';
									}
								}
								//VERIFICAMOS SI CAMBIO EL TELÉFONO
								if($this->input->post('phone') != $valorCliente->phoneCustomer){
									$data['phoneCustomer'] = $this->input->post('phone');
									$existUpdate = TRUE;
									$rowsUpdate .= '<li>Teléfono <strong>'.$valorCliente->phoneCustomer.'</strong> por <strong>'.$this->input->post('phone').'</strong></li>';
								}
								//VERIFICAMOS SI CAMBIO EL CELULAR
								$cell = ($this->input->post('cellphone') == "") ? NULL:$this->input->post('cellphone');
								if($cell != $valorCliente->cellphoneCustomer){
									$data['cellphoneCustomer'] = $cell;
									$existUpdate = TRUE;
									if($valorCliente->cellphoneCustomer==NULL){
										$valorCliente->cellphoneCustomer = '(Ningún Valor)';
									}
									if($cell==NULL){
										$cell = '(Ningún Valor)';
									}
									$rowsUpdate .= '<li>Celular <strong>'.$valorCliente->cellphoneCustomer.'</strong> por <strong>'.$cell.'</strong></li>';
								}
								//VERIFICAMOS SI CAMBIO EL EMAIL
								$email = ($this->input->post('email') == "") ? NULL:$this->input->post('email');
								if($email != $valorCliente->emailCustomer){
									$data['emailCustomer'] = $email;
									$existUpdate = TRUE;
									if($valorCliente->emailCustomer==NULL){
										$valorCliente->emailCustomer = '(Ningún Valor)';
									}
									if($email==NULL){
										$email = '(Ningún Valor)';
									}
									$rowsUpdate .= '<li>Email <strong>'.$valorCliente->emailCustomer.'</strong> por <strong>'.$email.'</strong></li>';
								}
								//VERIFICAMOS SI CAMBIO LA DIRECCIÓN
								if($this->input->post('address') != $valorCliente->addressCustomer){
									$data['addressCustomer'] = $this->input->post('address');
									$existUpdate = TRUE;
									$rowsUpdate .= '<li>Dirección <strong>'.$valorCliente->addressCustomer.'</strong> por <strong>'.$this->input->post('address').'</strong></li>';
								}
								//VERIFICAMOS SI CAMBIO LA ZONA O CIUDAD
								$idCiudad = ($this->input->post('city') == "") ? NULL:$this->input->post('city');
								if($idCiudad != $valorCliente->cityCustomer_fkCities){
									$data['cityCustomer_fkCities'] = $idCiudad;
									$existUpdate = TRUE;
									if($valorCliente->cityCustomer_fkCities==NULL){
										$actualCiudad = '(Ningún Valor)';
									} else {//IDENTIFICAMOS NOMBRE DE CIUDAD O ZONA
										$actualCiudad = $this->Logs_model->consultarCiudadFullTexto($valorCliente->cityCustomer_fkCities);
										if($actualCiudad==FALSE){
											$actualCiudad = '(Zona o Ciudad Desconocida)';
										}
									}
									if($idCiudad==NULL){
										$nuevaCiudad = '(Ningún Valor)';
									} else {//IDENTIFICAMOS NOMBRE DE CIUDAD O ZONA
										$nuevaCiudad = $this->Logs_model->consultarCiudadFullTexto($idCiudad);
										if($nuevaCiudad==FALSE){
											$nuevaCiudad = '(Zona o Ciudad Desconocida)';
										}
									}
									$rowsUpdate .= '<li>Zona o Ciudad <strong>'.$actualCiudad.'</strong> por <strong>'.$nuevaCiudad.'</strong></li>';
								}

								if($existUpdate){
									$response = $this->Clientes_model->actualizar($data, $this->input->post('idCustomer'));
									$messageUpdate = 'Se actualizaron los siguientes valores:<br><ul>'.$rowsUpdate.'</ul>';
									$this->Logs_model->iLog(4, $messageUpdate, $response['status'], 'customers', $this->input->post('idCustomer'), 1);
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
							} else {
								$r = array(
									'status' => 'danger',
									'statusMessage' => 'No se identifica cliente a actualizar'
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
			//VERIFICAMOS QUE SI SE PASE LA IDENTIFICACIÓN DEL USUARIO
			if(( $this->input->post('idCustomer') == "" || $this->input->post('idCustomer') == NULL ) ? FALSE:TRUE){
				$response = $this->Clientes_model->eliminar($this->input->post('idCustomer'));
				$this->Logs_model->iLog(5, $response['message'], $response['status'], 'customers', $this->input->post('idCustomer'), 1);
				$r = array(
					'status' => $response['status'],
					'statusMessage' => $response['message']
				);
			} else {
				$r = array(
					'status' => 'danger',
					'statusMessage' => 'No se identifica cliente a eliminar'
				);
			}
			echo json_encode($r);
		} else {
			show_404();
		}
	}
}
