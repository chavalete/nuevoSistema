<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'TransaccionesANMAT.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mensajeria/transacciones/DatoTransaccionExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mensajeria/transacciones/DetalleTransaccionExtendido.php';


class EnviarReproceso
{
    private $_db_dM;
    private $_colDatosTransaccion;
   
    public function __construct() {
        $this->_db_dM = new FrenteAlmacenamiento('dmelmac');
    }
    
    public function setColObjDatoTransaccion($obj){
        $this->_colObjDatoTransaccion[] = $obj;
    }
    
    public function getColObjDatoTransaccion(){
        return $this->_colObjDatoTransaccion;
    }


    public function buscarMovimientosReproceso(){
        //se trae todos los datos pendientes de prosesar para transaccionar
        $this->_db_dM->addSelect('f_evento');
        $this->_db_dM->addSelect('h_evento');
        $this->_db_dM->addSelect('gln_origen');
        $this->_db_dM->addSelect('cuit_origen');
        $this->_db_dM->addSelect('gln_destino');
        $this->_db_dM->addSelect('cuit_destino');
        $this->_db_dM->addSelect('n_remito');
        $this->_db_dM->addSelect('n_factura');
        $this->_db_dM->addSelect('vencimiento');
        $this->_db_dM->addSelect('gtin');
        $this->_db_dM->addSelect('lote');
        $this->_db_dM->addSelect('numero_serial');
        $this->_db_dM->addSelect('id_evento');
        $this->_db_dM->addSelect('apellido');
        $this->_db_dM->addSelect('nombres');
        $this->_db_dM->addSelect('n_documento');
        $this->_db_dM->addSelect('sexo');
        $this->_db_dM->addSelect('tipo_documento');
        $this->_db_dM->addSelect('direccion');
        $this->_db_dM->addSelect('localidad');
        $this->_db_dM->addSelect('numero');
        $this->_db_dM->addSelect('piso');
        $this->_db_dM->addSelect('dpto');
        $this->_db_dM->addSelect('n_postal');
        $this->_db_dM->addSelect('telefono');
        $this->_db_dM->addSelect('id_obra_social');
        $this->_db_dM->addSelect('nro_asociado');
        $this->_db_dM->addSelect('id_movimiento_sc');
        $this->_db_dM->addSelect('mensajeria_id');
        $this->_db_dM->addSelect('id_sucursal');
	$this->_db_dM->addSelect('referencia_nro');
	$this->_db_dM->addSelect('dispone_nombre');	
        $this->_db_dM->addSelect('soy_reproceso');
        
	$this->_db_dM->addFrom('tmp_mensajeria_anmat_reproceso');
	$this->_db_dM->addFrom('INNER JOIN datos_productos ON tmp_mensajeria_anmat_reproceso.gtin = producto_gtin');
        $this->_db_dM->addWhere('regristo_finalizado = true AND regristo_traspasado=false AND no_informar=false ');
        $this->_db_dM->addOrderBy('id_movimiento_sc, mensajeria_id');
        $this->_db_dM->generarSelect();
  	//echo $this->_db_dM->getQry();exit;
        $rstdoMensajeria= $this->_db_dM->ejecutar();
        //fin
        if(count($rstdoMensajeria) == 0 ){
            echo "No se encontraron registros para procesar.";
            exit;
        }

        $idMovimientoAnteriorSc = 0;
	$idEventoAnterior = 0;
        $arrFinalDatos=Array();

        foreach($rstdoMensajeria AS $datoMensajeria){
            
            $arrMensajeriaId[] = $datoMensajeria['mensajeria_id'];
            $arrDatosContenedor=Array();
            $arrDetalleContenedor=Array();
            
            if($datoMensajeria['id_movimiento_sc'] != $idMovimientoAnteriorSc || $datoMensajeria['id_evento'] != $idEventoAnterior){

		$objTransaccion = NEW DatoTransaccionExtendido();
                
                // genero el array de datos  // El arr carga la llave con el mismo nombre que en la db 
                $objTransaccion->setFechaEvento($datoMensajeria['f_evento']);
                $objTransaccion->setHoraEvento($datoMensajeria['h_evento']);
		$objTransaccion->setGlnOrigen($datoMensajeria['gln_origen']);
		$objTransaccion->setCuitOrigen($datoMensajeria['cuit_origen']);
		$objTransaccion->setGlnDestino($datoMensajeria['gln_destino']);
		$objTransaccion->setCuitDestino($datoMensajeria['cuit_destino']);
		$objTransaccion->setObjTipoMovimiento(new TipoMovimientoAnmatExtendido());
		$objTransaccion->getObjTipoMovimiento()->cargarMe($datoMensajeria['id_evento']);
		$objTransaccion->setObjErrorAnmat(new ErrorAnmatExtendido());
		$objTransaccion->getObjErrorAnmat()->cargarme($datoMensajeria['error_id']);
                
		if($datoMensajeria['n_factura'] != NULL && $datoMensajeria['n_factura'] != '0'){
		    $objTransaccion->setNroFactura($datoMensajeria['n_factura']);
		}
		if($datoMensajeria['n_remito'] != NULL && $datoMensajeria['n_remito'] != '0'){
		    $objTransaccion->setNroRemito($datoMensajeria['n_remito']);
		}
		
                $objTransaccion->setApellido($datoMensajeria['apellido']);
                $objTransaccion->setNombres($datoMensajeria['nombres']);
                $objTransaccion->setNroDocumento($datoMensajeria['n_documento']);
                $objTransaccion->setSexo($datoMensajeria['sexo']);
                $objTransaccion->setTipoDocumento($datoMensajeria['tipo_documento']);
                $objTransaccion->setDireccion($datoMensajeria['direccion']);
                $objTransaccion->setLocalidad($datoMensajeria['localidad']);
                $objTransaccion->setNro($datoMensajeria['numero']);
                $objTransaccion->setPiso($datoMensajeria['piso']);
                $objTransaccion->setDepto($datoMensajeria['dpto']);
                $objTransaccion->setNroPostal($datoMensajeria['n_postal']);
                $objTransaccion->setTelefono($datoMensajeria['telefono']);
                $objTransaccion->setIdObraSocial($datoMensajeria['id_obra_social']);
		$objTransaccion->setNroAsociado($datoMensajeria['nro_asociado']);
                $objTransaccion->setCodigoSucursal($datoMensajeria['id_sucursal']);    
                
                $objTransaccion->setIdMovimiento($datoMensajeria['id_movimiento_sc']);
                
                $objTransaccion->setDisponeNombre($datoMensajeria['dispone_nombre']);
		$objTransaccion->setNroReferencia($datoMensajeria['referencia_nro']);
		$objTransaccion->setSoyReproceso($datoMensajeria['soy_reproceso']);
		$objTransaccion->salvarMe($objTransaccion);
		$this->setColObjDatoTransaccion($objTransaccion);
            }
            
            $objDetalle = new DetalleTransaccionExtendido();
            
            $objDetalle->setTransaccionId($objTransaccion->getTransaccionId());
            $objDetalle->setDeMensajeId($datoMensajeria['mensajeria_id']);
            $objDetalle->setTrazabilidadCodigo($datoMensajeria['numero_serial']);
	    $objDetalle->setLote($datoMensajeria['lote']);
            $objDetalle->setLoteVencimiento($datoMensajeria['vencimiento']);
            $objDetalle->setProductoGtin($datoMensajeria['gtin']);
            
            $objDetalle->salvarMe($objDetalle);
	    $objTransaccion->setColObjDetalleTransaccion($objDetalle);
            //fin         
            $idMovimientoAnteriorSc =  $datoMensajeria['id_movimiento_sc'];
	    $idEventoAnterior = $datoMensajeria['id_evento']; 
        }

        //actualizamos la mensajeria ya traspasada a datos transacciones.
        //echo " Actualizando mensajerias ... ";
	$this->_db_dM->addFrom('tmp_mensajeria_anmat_reproceso');
        $this->_db_dM->addCamposUpdate('regristo_traspasado = true');
        $this->_db_dM->addWhere('mensajeria_id IN (' . implode(",", $arrMensajeriaId) . ')');
        $this->_db_dM->generarUpdate();

        $rstdoUpd = $this->_db_dM->ejecutar();

        $objTransaccionesANMAT = new TransaccionesANMAT();
        foreach($this->getColObjDatoTransaccion() AS $objTransaccion){

	    //echo $objTransaccion->getFechaEvento();exit;
            $objTransaccionesANMAT->preprarDatosEnvio($objTransaccion);

        }
        echo "Los Movimientos fueron reprocesados con exito, verifigue el listado de transacciones";
    }    
}
