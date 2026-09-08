<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mensajeria/armadodatos/FrenteArmadoDatos.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mensajeria/soap/FrenteSoap.php';
require_once 'ErrorANMAT.php';

class TransaccionesANMAT
{
    private $_db_dM;
    private $_anmatCliente;
    
    public function __construct() {

        $this->_db_dM = new FrenteAlmacenamiento('dmelmac');
        $this->_objFuncionesComunes = new FuncionesComunes();  
        
    }

    public function enviarTransacciones($arrAnmat){
	    
            //echo "\n Enviando transaccion nro "  . $datoTransaccion->getTransaccionId() . "...";
            // para cada transaccion unset de las respuesta.
            unset($respuesta); 
            //fin
            if($arrAnmat[0]['id_evento'] != -99){
                //armado del estandar de datos
                $medicamentosDTOS = new FrenteArmadoDatos($arrAnmat, 'medicamentosDTO');
                //fin
                // Coneccion soap , le enviamos contra quien vamos a abrir la conexion , sucursal del usuario del sistema anmat
                $this->_anmatCliente = new FrenteSoap('anmat', $arrAnmat[0]['id_agente'] );
                $this->_anmatCliente->enviarMedicamentos($medicamentosDTOS->getColDatosEnviar());
                $respuesta = $this->_anmatCliente->getUltimaRespuesta();
                
                if($respuesta[0]->return->resultado == true){
                     	$this->_db_dM->addCamposUpdate('codigo_transaccion = ' . $respuesta[0]->return->codigoTransaccion);
                        $this->_db_dM->addCamposUpdate('fecha_hora_transaccion = current_timestamp');
			$this->_db_dM->addCamposUpdate('error_id = null');
                }else{
		      	//genero el id unido de error
                    	$this->_db_dM->addSelect('seq_error_transaccion_error_id');
                    	$this->_db_dM->generarProximo();
                    	$arrErrorId = $this->_db_dM->ejecutar();
                    	$errorId = $arrErrorId[0]['nextval'];
                    	// fin
                    	//llamo a la clase que maneja el error y lo guarda
                    	$errorANMAT = new ErrorANMAT($errorId);
                    //print_r($respuesta);
                    	$errorANMAT->salvarError($respuesta[0]->return->errores);

                        //guado el id del error en datos_transacciones y seguimooo!!
                    	$this->_db_dM->addCamposUpdate('error_id = ' . $errorId /* seq de error */);
                    	$this->_db_dM->addCamposUpdate('fecha_hora_transaccion = current_timestamp');
                    }      
                    $this->_db_dM->addFrom('datos_transacciones_anmat');
                    $this->_db_dM->addWhere('transaccion_id = ' . $arrAnmat['0'][transaccion_id]);
                    $this->_db_dM->generarUpdate();
                    echo $this->_db_dM->getQry();
                    $rstdoUpdate = $this->_db_dM->ejecutar();    
                    //var_dump($this->_anmatCliente->getUltimoEnvio()) . "<br>";
		
            }else{
            //print_r($datoTransaccion->getIdMovimientoSza());
                //1-BUSCO LA TRANSACCION PARA EL id_movimiento QUE TENGA EL DATO DE LA TRANSACCION
                $this->_db_dM->addSelect('codigo_transaccion');
                $this->_db_dM->addFrom('datos_transacciones_anmat');
                $this->_db_dM->addWhere('id_movimiento = ? AND codigo_transaccion > 0 ');

                $this->_db_dM->generarSelect();
                $rstdoIdMovSza = $this->_db_dM->ejecutar(Array(Array($arrAnmat['0'][transaccion_id])));
                //var_dump($rstdoIdMovSza);
                if(count($rstdoIdMovSza) > 0){//EXISTE 
                    //ENVIO LA CANCELACION CON EL ID DE TRANSACCION
                //print_r($rstdoIdMovSza[0]['codigo_transaccion']);
                    $this->_anmatCliente->enviarCancelacTransacc($rstdoIdMovSza[0]['codigo_transaccion']); 
            	    $respuesta = $this->_anmatCliente->getUltimaRespuesta();
                    if($respuesta[0]->return->resultado == true){
                        $this->_db_dM->addCamposUpdate('codigo_transaccion = ' . $respuesta[0]->return->codigoTransaccion);
                        $this->_db_dM->addCamposUpdate('fecha_hora_transaccion = current_timestamp');
                    }else{
                    //print_r($respuesta);
                        //genero el id unido de error
                        $this->_db_dM->addSelect('seq_error_transaccion_error_id');
                        $this->_db_dM->generarProximo();
                        $arrErrorId = $this->_db_dM->ejecutar();
                        $errorId = $arrErrorId[0]['nextval'];
                        // fin
                        //llamo a la clase que maneja el error y lo guarda
                        unset($errorANMAT);
                        $errorANMAT = new ErrorANMAT($errorId);
                        $errorANMAT->salvarError($respuesta[0]->return->errores);

                        //guado el id del error en datos_transacciones y seguimooo!!
                        $this->_db_dM->addCamposUpdate('error_id = ' . $errorId /* seq de error */);
                        $this->_db_dM->addCamposUpdate('fecha_hora_transaccion = current_timestamp');

                    }
                $this->_db_dM->addFrom('datos_transacciones_anmat');
                $this->_db_dM->addWhere('transaccion_id = ' . $arrAnmat['0'][transaccion_id]);
                $this->_db_dM->generarUpdate();
                //echo $this->_db_dM->getQry();
                $rstdoUpdate = $this->_db_dM->ejecutar();

                }else{
                    //NO EXISTE , BORRO EL REGISTRO DE DETALLE TRANSACCIONES.
                    $this->_db_dM->addWhere('transaccion_id = ?');
                    $this->_db_dM->addFrom('detalle_transacciones_anmat');
                    $this->_db_dM->generarDelete();
                    $this->_db_dM->ejecutar(Array(Array($arrAnmat['0'][transaccion_id])));
                    //NO EXISTE , BORRO EL REGISTRO DE DATOS TRANSACCIONES 
                    $this->_db_dM->addWhere('transaccion_id = ?');
                    $this->_db_dM->addFrom('datos_transacciones_anmat');
                    $this->_db_dM->generarDelete();
                    $this->_db_dM->ejecutar(Array(Array($arrAnmat['0'][transaccion_id])));
                }
            }
	     if($respuesta[0]->return->resultado == true){
		echo "La transaccion ha sido enviada exitosamente, codigo transaccion: ". $respuesta[0]->return->codigoTransaccion;
	     }else{
		$error = $respuesta[0]->return->errores;
		echo "Error en la transaccion : " . $error->_d_error;
	    }  
    }
    public function preprarDatosEnvio($objTransaccion){

	
        foreach($objTransaccion->getColObjDetalleTransaccion() AS $objDetalle){
                
            $arrDato[transaccion_id] = $objTransaccion->getTransaccionId();
            $arrDato[numero_serial] = $objDetalle->getTrazabilidadCodigo();
            $arrDato[lote] = $objDetalle->getLote();
            $arrDato[vencimiento] = $this->_objFuncionesComunes->convertirFormatoFechaAnmat($objDetalle->getLoteVencimiento());
            $arrDato[gtin] = $objDetalle->getProductoGtin();
            $arrDato[id_evento] = $objTransaccion->getObjTipoMovimiento()->getTipoMovimientoId();
            $arrDato[f_evento] = $this->_objFuncionesComunes->convertirFormatoFechaAnmat($objTransaccion->getFechaEvento());
            $arrDato[h_evento] = $objTransaccion->getHoraEvento();
            $arrDato[gln_origen] = $objTransaccion->getGlnOrigen();
            $arrDato[cuit_origen] = $objTransaccion->getCuitOrigen();
            $arrDato[gln_destino] =$objTransaccion->getGlnDestino();
            $arrDato[cuit_destino] =$objTransaccion->getCuitDestino();
            $arrDato[n_factura] = $objTransaccion->getNroFactura();
            $arrDato[n_remito] =$objTransaccion->getNroRemito();
            $arrDato[apellido] =$objTransaccion->getApellido();
            $arrDato[nombres] =$objTransaccion->getNombres();
            $arrDato[n_documento] =$objTransaccion->getNroDocumento();
            $arrDato[sexo] =$objTransaccion->getSexo();
            $arrDato[tipo_documento] =$objTransaccion->getTipoDocumento();
            $arrDato[direccion] =$objTransaccion->getDireccion();
            $arrDato[localidad] =$objTransaccion->getLocalidad();
            $arrDato[numero] =$objTransaccion->getNro();
            $arrDato[piso] =$objTransaccion->getPiso();
            $arrDato[dpto] =$objTransaccion->getDepto();
            $arrDato[n_postal] =$objTransaccion->getNroPostal();
            $arrDato[telefono] =$objTransaccion->getTelefono();
            $arrDato[id_obra_social] =$objTransaccion->getIdObraSocial();
            $arrDato[id_agente] = $objTransaccion->getCodigoSucursal();
                        
            $arrDatoF[]= $arrDato;

        }
        
        $this->enviarTransacciones($arrDatoF);
    } 	
}
