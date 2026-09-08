<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mensajeria/armadodatos/FrenteArmadoDatos.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mensajeria/soap/FrenteSoap.php';


class TransaccionesANMAT
{
    private $_db_dM;
    private $_anmatCliente;
    private $_arrError = Array();
    
    public function __construct() {
        $this->_db_dM = new FrenteAlmacenamiento('dmelmac');
    }

    public function enviarTransacciones($datoTransaccion){
            $arrAnmat=Array();
            $respuesta = Array();
            //echo "\n Enviando transaccion nro "  . $datoTransaccion->getTransaccionId() . "...";
            //es un movimiento de reversa?
            if(!$datoTransaccion->getEsReversa()){
                //scienza es el destino de la transaccion
                //echo "el tipo de movimiento es ? " . $datoTransaccion->getIdEvento();exit;
                //echo "es destino ? " . $this->getSoyDestino($datoTransaccion->getIdEvento());exit;
                if ($this->getSoyDestino($datoTransaccion->getIdEvento())){
                    //recorro el array de detalles
                    foreach ($datoTransaccion->getColObjDetalleTransaccion() AS $detalleTransaccion){
                        $arrAnmat=Array();
                        $x=0;
                        //scienza genero las trazas?                       
			//echo substr($detalleTransaccion->getNumeroSerial(),0,13); 
                        if(substr($detalleTransaccion->getTrazabilidadCodigo(),0,13) == $datoTransaccion->getGlnDestino()){
                            switch ($datoTransaccion->getIdEvento()){
                                case 41:
                                    $arrAnmat[$x]['id_evento'] = 838;
                                    $datoTransaccion->actualizarIdEvento($arrAnmat[$x]['id_evento']);
                                    break;
                                case 43:
                                    $arrAnmat[$x]['id_evento'] = 837;
                                    $datoTransaccion->actualizarIdEvento($arrAnmat[$x]['id_evento']);
                                    break;
                                case 44:
                                    $arrAnmat[$x]['id_evento'] = 839;
                                    $datoTransaccion->actualizarIdEvento($arrAnmat[$x]['id_evento']);
                                    break;
                                default :
                                    $arrAnmat[$x]['id_evento'] = $datoTransaccion->getIdEvento();
                                    break;
                            }
                            $arrAnmat[$x]['f_evento'] = $datoTransaccion->getFechaEvento();
                            $arrAnmat[$x]['h_evento'] = $datoTransaccion->getHoraEvento();
                            $arrAnmat[$x]['gln_origen'] = $datoTransaccion->getGlnOrigen();
                            $arrAnmat[$x]['cuit_origen'] = $datoTransaccion->getCuitOrigen();
                            $arrAnmat[$x]['gln_destino'] = $datoTransaccion->getGlnDestino();
                            $arrAnmat[$x]['cuit_destino'] = $datoTransaccion->getCuitDestino();
                            $arrAnmat[$x]['n_factura'] = $datoTransaccion->getNroFactura();
                            $arrAnmat[$x]['n_remito'] = $datoTransaccion->getNroRemito();
                            $arrAnmat[$x]['apellido'] = $datoTransaccion->getApellido();
                            $arrAnmat[$x]['nombres'] = $datoTransaccion->getNombres();
                            $arrAnmat[$x]['n_documento'] = $datoTransaccion->getNroDocumento();
                            $arrAnmat[$x]['sexo'] = $datoTransaccion->getSexo();
                            $arrAnmat[$x]['tipo_documento'] = $datoTransaccion->getTipoDocumento();
                            $arrAnmat[$x]['direccion'] = $datoTransaccion->getDireccion();
                            $arrAnmat[$x]['localidad'] = $datoTransaccion->getLocalidad();
                            $arrAnmat[$x]['numero'] = $datoTransaccion->getNro();
                            $arrAnmat[$x]['piso'] = $datoTransaccion->getPiso();
                            $arrAnmat[$x]['dpto'] = $datoTransaccion->getDepto();
                            $arrAnmat[$x]['n_postal'] = $datoTransaccion->getNroPostal();
                            $arrAnmat[$x]['telefono'] = $datoTransaccion->getTelefono();
                            $arrAnmat[$x]['id_obra_social'] = $datoTransaccion->getIdObraSocial();
                            $arrAnmat[$x]['nro_asociado'] = $datoTransaccion->getNroAsociado();
                            $arrAnmat[$x]['codigo_sucursal'] = $datoTransaccion->getCodigoSucursal();
                            $arrAnmat[$x]['numero_serial'] = $detalleTransaccion->getTrazabilidadCodigo();
                            $arrAnmat[$x]['lote'] = $detalleTransaccion->getLote();
                            $arrAnmat[$x]['vencimiento'] = $detalleTransaccion->getLoteVencimiento();
                            $arrAnmat[$x]['gtin'] = $detalleTransaccion->getProductoGtin();

                            //armado del estandar de datos
                            $medicamentosDTOS = new FrenteArmadoDatos($arrAnmat, 'medicamentosDTO');
                            // Coneccion soap , le enviamos contra quien vamos a abrir la conexion , sucursal del usuario del sistema anmat
                            $this->_anmatCliente = new FrenteSoap('anmat', $datoTransaccion->getCodigoSucursal());
                            $this->_anmatCliente->enviarMedicamentos($medicamentosDTOS->getColDatosEnviar());
                            $respuesta = $this->_anmatCliente->getUltimaRespuesta();
                            if($respuesta[0]->return->resultado == true){
                                $datoTransaccion->grabarCodigoTransaccion($respuesta[0]->return->codigoTransaccion);
                            }else{
                                    //llamar al error -- Rechazo anmat
                                $datoTransaccion->grabarErrorAnmat($respuesta);
                            }
                        //fin
                        }else{
                            // por cada detalle verifico si existe en pendientes 
                            $idTransaccionAConfirmar = $this->getIdTransaccionAConfirmar($detalleTransaccion->getTrazabilidadCodigo(), $detalleTransaccion->getProductoGtin(), $detalleTransaccion->getLote(), $datoTransaccion->getGlnOrigen() ,$datoTransaccion->getGlnDestino());
                            if($idTransaccionAConfirmar > 0){ 
                                $this->_anmatCliente = new FrenteSoap('anmat', $datoTransaccion->getCodigoSucursal());
                                $arrDatosConfirmacion = Array('p_ids_transac' => $idTransaccionAConfirmar , 
                                                          'f_operacion' => $datoTransaccion->getFechaEvento()
                                                         );
                                $this->_anmatCliente->enviarConfirmacionTransacc($arrDatosConfirmacion);
                                $respuesta = $this->_anmatCliente->getUltimaRespuesta();
                                //var_dump($this->_anmatCliente->getUltimaRespuesta());
                                if($respuesta[0]->return->resultado == true){
                                    //actualizamos el codigo de la transaccion 
                                    $datoTransaccion->grabarCodigoTransaccion($respuesta[0]->return->id_transac_asociada);
                                    //actualizamos el pendiente a confirmado
                                    $this->_db_dM->addCamposUpdate('id_estado =3');
				    $this->_db_dM->addCamposUpdate('id_transac_asociada = ' . $respuesta[0]->return->id_transac_asociada);
                                    $this->_db_dM->addFrom('transacciones_pendientes_anmat');
                                    $this->_db_dM->addWhere('id_transaccion_anmat = ' . $idTransaccionAConfirmar);
				    $this->_db_dM->generarUpdate();
                                    $updPend = $this->_db_dM->ejecutar();    
				    //ECHO "pENDIENTE aCTUALIZADO!!";
                                }else{
                                    //grabar el error -- Rechazo anmat
                                    $datoTransaccion->grabarErrorAnmat($respuesta);
                                }
                            }else{
                                //llamar el error -- No exite la traza como pendiente
                                $error[0] = new stdClass();
                                $error[0]->return = new stdClass();
                                $error[0]->return->errores = new stdClass();
                                $error[0]->return->errores->_c_error = 999999;
                                $error[0]->return->errores->_d_error = "El numero_serial " . $detalleTransaccion->getTrazabilidadCodigo() . " no se encuentra como pendiente.";
                                $datoTransaccion->grabarErrorAnmat($error);                            
                            }
                        }
                    }//fin
                }else{
                    $x=0;
                    foreach($datoTransaccion->getColObjDetalleTransaccion() AS $detalleTransaccion){
                        $arrAnmat=Array();
                        $arrAnmat[$x]['id_evento'] = $datoTransaccion->getIdEvento();
                        $arrAnmat[$x]['f_evento'] = $datoTransaccion->getFechaEvento();
                        $arrAnmat[$x]['h_evento'] = $datoTransaccion->getHoraEvento();
                        $arrAnmat[$x]['gln_origen'] = $datoTransaccion->getGlnOrigen();
                        $arrAnmat[$x]['cuit_origen'] = $datoTransaccion->getCuitOrigen();
                        $arrAnmat[$x]['gln_destino'] = $datoTransaccion->getGlnDestino();
                        $arrAnmat[$x]['cuit_destino'] = $datoTransaccion->getCuitDestino();
                        $arrAnmat[$x]['n_factura'] = $datoTransaccion->getNroFactura();
                        $arrAnmat[$x]['n_remito'] = $datoTransaccion->getNroRemito();
                        $arrAnmat[$x]['apellido'] = $datoTransaccion->getApellido();
                        $arrAnmat[$x]['nombres'] = $datoTransaccion->getNombres();
                        $arrAnmat[$x]['n_documento'] = $datoTransaccion->getNroDocumento();
                        $arrAnmat[$x]['sexo'] = $datoTransaccion->getSexo();
                        $arrAnmat[$x]['tipo_documento'] = $datoTransaccion->getTipoDocumento();
                        $arrAnmat[$x]['direccion'] = $datoTransaccion->getDireccion();
                        $arrAnmat[$x]['localidad'] = $datoTransaccion->getLocalidad();
                        $arrAnmat[$x]['numero'] = $datoTransaccion->getNro();
                        $arrAnmat[$x]['piso'] = $datoTransaccion->getPiso();
                        $arrAnmat[$x]['dpto'] = $datoTransaccion->getDepto();
                        $arrAnmat[$x]['n_postal'] = $datoTransaccion->getNroPostal();
                        $arrAnmat[$x]['telefono'] = $datoTransaccion->getTelefono();
                        $arrAnmat[$x]['id_obra_social'] = $datoTransaccion->getIdObraSocial();
                        $arrAnmat[$x]['nro_asociado'] = $datoTransaccion->getNroAsociado();
                        $arrAnmat[$x]['codigo_sucursal'] = $datoTransaccion->getCodigoSucursal();
                        $arrAnmat[$x]['numero_serial'] = $detalleTransaccion->getTrazabilidadCodigo();
                        $arrAnmat[$x]['lote'] = $detalleTransaccion->getLote();
                        $arrAnmat[$x]['vencimiento'] = $detalleTransaccion->getLoteVencimiento();
                        
                        $arrAnmat[$x]['gtin'] = $detalleTransaccion->getProductoGtin();
                    
                    //armado del estandar de datos
                        $medicamentosDTOS = new FrenteArmadoDatos($arrAnmat, 'medicamentosDTO');
                        //Coneccion soap , le enviamos contra quien vamos a abrir la conexion , sucursal del usuario del sistema anmat
                        $this->_anmatCliente = new FrenteSoap('anmat', $datoTransaccion->getCodigoSucursal());
                        $this->_anmatCliente->enviarMedicamentos($medicamentosDTOS->getColDatosEnviar());
                        //var_dump($this->_anmatCliente->getUltimoEnvio());
                        $respuesta = $this->_anmatCliente->getUltimaRespuesta();
                        if($respuesta[0]->return->resultado == true){
                            $datoTransaccion->grabarCodigoTransaccion($respuesta[0]->return->codigoTransaccion);
                        }else{
                                //llamar al error -- Rechazo anmat
                            $datoTransaccion->grabarErrorAnmat($respuesta);
                        }
                    }
                }
            }else{
                foreach($datoTransaccion->getColObjDetalleTransaccion() AS $detalleTransaccion){
                    //1-BUSCO LA TRANSACCION PARA EL id_movimiento QUE TENGA EL DATO DE LA TRANSACCION
                    $this->_db_dM->addSelect('codigo_transaccion');
                    $this->_db_dM->addFrom('datos_transacciones_anmat');
                    $this->_db_dM->addFrom('datos_transacciones_anmat INNER JOIN detalle_transacciones_anmat USING (transaccion_id)');
                    $this->_db_dM->addWhere('trazabilidad_codigo =\'' . $detalleTransaccion->getTrazabilidadCodigo() . '\'' );
                    $this->_db_dM->addWhere('referencia_nro = \'' . $datoTransaccion->getReferenciaNro() . '\'');
                    $this->_db_dM->addWhere('codigo_transaccion > 0 ');
                    $this->_db_dM->generarSelect();
                    $rstdoIdMovSza = $this->_db_dM->ejecutar();
                    //echo $this->_db_dM->getQry();exit;
                    if(count($rstdoIdMovSza) > 0){//EXISTE 
                        //ENVIO LA CANCELACION CON EL ID DE TRANSACCION
                        //print_r($rstdoIdMovSza[0]['codigo_transaccion']);
                        $this->_anmatCliente = new FrenteSoap('anmat', $datoTransaccion->getCodigoSucursal());
                        $this->_anmatCliente->enviarCancelacTransacc($rstdoIdMovSza[0]['codigo_transaccion']); 
                        $respuesta = $this->_anmatCliente->getUltimaRespuesta();
                        if($respuesta[0]->return->resultado == true){
                            $this->_db_dM->addCamposUpdate('codigo_transaccion = ' . $respuesta[0]->return->codigoTransaccion);
                            $this->_db_dM->addCamposUpdate('fecha_hora_transaccion = current_timestamp');
                            $this->_db_dM->addFrom('datos_transacciones_anmat');
                            $this->_db_dM->addWhere('transaccion_id = ' . $datoTransaccion->getTransaccionId());
                            $this->_db_dM->generarUpdate();
                            $this->_db_dM->ejecutar();
                        }else{
                            //grabar el error -- Rechazo anmat
                            $datoTransaccion->grabarErrorAnmat($respuesta);
                        }
                    }else{
                        //NO EXISTE 
                        $error[0] = new stdClass();
                        $error[0]->return = new stdClass();
                        $error[0]->return->errores = new stdClass();
                        $error[0]->return->errores->_c_error = 000001;
                        $error[0]->return->errores->_d_error = "No se Encontro la transaccion a cancelar, verifique el movimiento enviado";
                        $datoTransaccion->grabarErrorAnmat($error);                            
                    }
                }
            }          	
    }
    
    public function getSoyDestino($id){
        $this->_db_dM->addSelect('soy_destino');
        $this->_db_dM->addFrom('tipo_movimientos_anmat');
        $this->_db_dM->addWhere('movimiento_id = ' . $id);
        $this->_db_dM->generarSelect();
        $rstdo = $this->_db_dM->ejecutar();
        if(count($rstdo) > 0 ){
            return $rstdo[0]['soy_destino'];
        }else{
            echo "No se encontro el evento ANMAT";
        }
    }
    
    public function getIdTransaccionAConfirmar($traza,$gtin,$lote,$glnOrigen,$glnDestino){
        //select a la tabla pendientes_confirmacion_anmat
        $this->_db_dM->addSelect('id_transaccion');
        $this->_db_dM->addSelect('id_transaccion_anmat');
        $this->_db_dM->addFrom('transacciones_pendientes_anmat');
        $this->_db_dM->addWhere("numero_serial = trim('" . $traza . "')");
        $this->_db_dM->addWhere("gtin = '" . $gtin. "'");
        //$this->_db_dM->addWhere("gln_origen = '" . $glnOrigen . "'");
        $this->_db_dM->addWhere("id_estado = 1");
        //$this->_db_dM->addWhere(" lote   = trim('" . $lote . "')");
        $this->_db_dM->addWhere(" gln_destino = '" . $glnDestino . "'");
        
        $this->_db_dM->generarSelect('');
        //echo $this->_db_dM->getQry();exit;
        $resultado = $this->_db_dM->ejecutar();
        return $resultado[0]['id_transaccion_anmat'];
    }
}