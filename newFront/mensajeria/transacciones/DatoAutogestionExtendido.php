<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/mensajeria/transacciones/DatoTransaccion.php';
require_once 'DetalleAutogestionExtendido.php';
require_once 'TipoMovimientoAnmatExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mensajeria/errores/ErrorAnmatExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/GlnOrigenExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/GlnDestinoExtendido.php';

class DatoAutogestionExtendido extends DatoTransaccion
{
    private $_db;
    private $_glnOrigenNombre;
    private $_glnDestinoNombre;
    
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
    }  

    public function setGlnOrigenNombre($nombre){
	$this->_glnOrigenNombre = $nombre;
    }
    public function getGlnOrigenNombre(){
	return $this->_glnOrigenNombre;
    }
    public function setGlnDestinoNombre($nombre){
	$this->_glnDestinoNombre = $nombre;
    }
    public function getGlnDestinoNombre(){
	return $this->_glnDestinoNombre;
    }
    public function getDataDB($id){
    
	$db_dM = new FrenteAlmacenamiento('');

	$this->_db->addSelect('transaccion_id');
        $this->_db->addSelect('id_evento');
	$this->_db->addSelect('codigo_transaccion');
        $this->_db->addSelect('id_movimiento');
        $this->_db->addSelect('to_char(fecha_evento,\'YYYY-MM-DD\') AS f_evento');
        $this->_db->addSelect('hora_evento AS h_evento');
        $this->_db->addSelect('gln_origen');
        $this->_db->addSelect('cuit_origen');
        $this->_db->addSelect('gln_destino');
        $this->_db->addSelect('cuit_destino');
        $this->_db->addSelect('nro_remito AS n_remito');
        $this->_db->addSelect('nro_factura AS n_factura');
	$this->_db->addSelect('to_char(fecha_hora_transaccion, \'DD-MM-YYYY HH24:MI\') AS fecha_hora_transaccion');
	$this->_db->addSelect('error_id');
	$this->_db->addSelect('codigo_sucursal');
	$this->_db->addSelect('apellido');
	$this->_db->addSelect('nombres');
	$this->_db->addSelect('nro_documento');
	$this->_db->addSelect('sexo');
	$this->_db->addSelect('tipo_documento');
	$this->_db->addSelect('direccion');
	$this->_db->addSelect('localidad');
	$this->_db->addSelect('nro');
	$this->_db->addSelect('piso');
	$this->_db->addSelect('dpto');
	$this->_db->addSelect('n_postal');
	$this->_db->addSelect('telefono');
	$this->_db->addSelect('id_obra_social');
	$this->_db->addSelect('nro_asociado');
	$this->_db->addSelect('referencia_nro');
	$this->_db->addSelect('dispone_nombre');
	$this->_db->addSelect('es_reversa');
	$this->_db->addSelect('tipo_movimiento_id');
	
	$this->_db->addFrom('datos_autogestion');

	
	$this->_db->addWhere('transaccion_id = \'' . $id . '\'');

	//echo $this->getTransaccionId();
	$this->_db->generarSelect();
	echo $this->_db->getQry();exit;
	$arrTransaccion = $this->_db->ejecutar();

	//verificamos que el movimiento exista en la db
        if(count($arrTransaccion) == 0){
            $errorDetalle = 'La transaccion que intenta cargar no existe';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }

    }
    
    public function cargarme($datos){
    
	if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	
	    $resultado = $datos;
	}
	
	$this->setTransaccionId($datos[transaccion_id]);
	$this->setIdMovimiento($datos[id_movimiento]);
	$this->setFechaEvento($datos[f_evento]);
	$this->setHoraEvento($datos[h_evento]);
	$this->setGlnOrigen($datos[gln_origen]);
	$this->setCuitOrigen($datos[cuit_origen]);
	$this->setGlnDestino($datos[gln_destino]);
	$this->setCuitDestino($datos[cuit_destino]);
	$this->setCodigoSucursal($datos[codigo_sucursal]);
	$this->setApellido($datos['apellido']);
	$this->setNombres($datos['nombres']);
	$this->setNroDocumento($datos['nro_documento']);
	$this->setSexo($datos['sexo']);
	$this->setTipoDocumento($datos['tipo_documento']);
	$this->setDireccion($datos['direccion']);
	$this->setLocalidad($datos['localidad']);
	$this->setNro($datos['nro']);
	$this->setPiso($datos['piso']);
	$this->setDepto($datos['dpto']);
	$this->setNroPostal($datos['n_postal']);
	$this->setTelefono($datos['telefono']);
	$this->setIdObraSocial($datos['id_obra_social']);
	$this->setNroAsociado($datos['nro_asociado']);
	$this->setNroReferencia($datos['referencia_nro']);
	$this->setDisponeNombre($datos['dispone_nombre']);
	$this->setTipoMovimientoId($datos['tipo_movimiento_id']);
	//echo $this->getTipoMovimientoId();exit;
	$this->setIdEvento($datos['id_evento']);
	$this->setEsReversa($datos['es_reversa']);

		
	if($datos['nro_factura'] != NULL && $datos['nro_factura'] != '0'){
	    $this->setNroFactura($datos['nro_factura']);
	}
	if($datos['nro_remito'] != NULL && $datos['nro_remito'] != '0'){
	    $this->setNroRemito($datos['nro_remito']);
	}
	$this->setCodigoTransaccion($datos[codigo_transaccion]);
	$this->setFechaHoraTransaccion($datos[fecha_hora_transaccion]);
	$this->setErrorId($datos[error_id]);
   
	
    }

    public function salvarme($objTransaccion){

	$this->_db->addCamposTabla('tmp_datos_trans_tmp_id_sq');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();
	
	$objTransaccion->setTmpId(($id[0]['nextval']));

        $this->_db->addFrom('datos_transacciones_anmat');

        $this->_db->addCamposTabla('transaccion_id');
        $this->_db->addCamposTabla('tipo_movimiento_id');
        $this->_db->addCamposTabla('id_movimiento');
        $this->_db->addCamposTabla('fecha_evento');
        $this->_db->addCamposTabla('hora_evento');
        $this->_db->addCamposTabla('gln_origen');
        $this->_db->addCamposTabla('cuit_origen');
        $this->_db->addCamposTabla('gln_destino');
        $this->_db->addCamposTabla('cuit_destino');
        $this->_db->addCamposTabla('nro_remito');
        $this->_db->addCamposTabla('nro_factura');
	$this->_db->addCamposTabla('error_id');
	$this->_db->addCamposTabla('id_sucursal');
	$this->_db->addCamposTabla('apellido');
	$this->_db->addCamposTabla('nombres');
	$this->_db->addCamposTabla('nro_documento');
	$this->_db->addCamposTabla('sexo');
	$this->_db->addCamposTabla('tipo_documento');
	$this->_db->addCamposTabla('direccion');
	$this->_db->addCamposTabla('localidad');
	$this->_db->addCamposTabla('nro');
	$this->_db->addCamposTabla('piso');
	$this->_db->addCamposTabla('dpto');
	$this->_db->addCamposTabla('n_postal');
	$this->_db->addCamposTabla('telefono');
	$this->_db->addCamposTabla('id_obra_social');
	$this->_db->addCamposTabla('nro_asociado');
	$this->_db->addCamposTabla('referencia_nro');
	$this->_db->addCamposTabla('dispone_nombre');
	$this->_db->addCamposTabla('tmp_id');
    
        
        $this->_db->addCamposValue($objTransaccion->getTransaccionId());

        if($objTransaccion->getObjTipoMovimiento()->getTipoMovimientoId() ==NULL){
	    $this->_db->addCamposValue('0');
	}else{
	    $this->_db->addCamposValue($objTransaccion->getObjTipoMovimiento()->getTipoMovimientoId());
	}

	$this->_db->addCamposValue($objTransaccion->getIdMovimiento());
        $this->_db->addCamposValue('\'' . $objTransaccion->getFechaEvento() . '\'');
        $this->_db->addCamposValue('\'' . $objTransaccion->getHoraEvento() . '\'');
        $this->_db->addCamposValue('\'' . $objTransaccion->getGlnOrigen()  . '\'');
        $this->_db->addCamposValue('\'' . $objTransaccion->getCuitOrigen() . '\'');
        $this->_db->addCamposValue('\'' . $objTransaccion->getGlnDestino() . '\'');
        $this->_db->addCamposValue('\'' . $objTransaccion->getCuitDestino() . '\'');
        $this->_db->addCamposValue('\'' . $objTransaccion->getNroRemito() . '\'');
        $this->_db->addCamposValue('\'' . $objTransaccion->getNroFactura() . '\'');
	$this->_db->addCamposValue($objTransaccion->getErrorId());
	$this->_db->addCamposValue('\'' . $objTransaccion->getCodigoSucursal() . '\'');
	$this->_db->addCamposValue('\'' . $objTransaccion->getApellido() . '\'');
	$this->_db->addCamposValue('\'' . $objTransaccion->getNombres() . '\'');
	$this->_db->addCamposValue('\'' . $objTransaccion->getNroDocumento() . '\'');
	$this->_db->addCamposValue('\'' . $objTransaccion->getSexo() . '\'');
	$this->_db->addCamposValue('\'' . $objTransaccion->getTipoDocumento() . '\'');
	$this->_db->addCamposValue('\'' . $objTransaccion->getDireccion() . '\'');
	$this->_db->addCamposValue('\'' . $objTransaccion->getLocalidad() . '\'');
	$this->_db->addCamposValue('\'' . $objTransaccion->getNro() . '\'');
	$this->_db->addCamposValue('\'' . $objTransaccion->getPiso() . '\'');
	$this->_db->addCamposValue('\'' . $objTransaccion->getDepto() . '\'');
	$this->_db->addCamposValue('\'' . $objTransaccion->getNroPostal() . '\'');
	$this->_db->addCamposValue('\'' . $objTransaccion->getTelefono() . '\'');
	$this->_db->addCamposValue('\'' . $objTransaccion->getIdObraSocial() . '\'');
	$this->_db->addCamposValue('\'' . $objTransaccion->getNroAsociado() . '\'');
	$this->_db->addCamposValue('\'' . $objTransaccion->getNroReferencia() . '\'');
	$this->_db->addCamposValue('\'' . $objTransaccion->getDisponeNombre() . '\'');
	$this->_db->addCamposValue('\'' . $objTransaccion->getTmpId() . '\'');

	$insertTransaccion=$this->_db->generarInsert();
	//echo $this->_db->getQry();exit;

        $this->_db->ejecutar();
	
	if(count($insertTransaccion) == 0){
            $errorDetalle = 'La transaccion que intenta cargar no existe';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }

        
    }
    public function actualizarme($arrParametros){

	$db_dM = new FrenteAlmacenamiento('');

	if($arrParametros['campos']['tipo_movimiento_id'] !='false'){

	    $db_dM->addCamposUpdate('id_evento= \'' . $arrParametros['campos']['tipo_movimiento_id'] .'\'');
	}
	    
	if(is_numeric($arrParametros['campos']['gln_destino'])){
	    $db_dM->addCamposUpdate('gln_destino= \'' . $arrParametros['campos']['gln_destino'] .'\'');
	}else{
	    $db_dM->addCamposUpdate('gln_destino= NULL');
	}
	if(is_numeric($arrParametros['campos']['gln_origen'])){
	    $db_dM->addCamposUpdate('gln_origen= \'' . $arrParametros['campos']['gln_origen'] .'\'');
	}
	if($arrParametros['campos']['nro_factura']!=null){
	    $db_dM->addCamposUpdate('nro_factura= \'' . $arrParametros['campos']['nro_factura'] .'\'');
	}
	if($arrParametros['campos']['nro_remito']!=null){
	    $db_dM->addCamposUpdate('nro_remito= \'' . $arrParametros['campos']['nro_remito'] .'\'');
	}
	$db_dM->addFrom('datos_autogestion');
	if($arrParametros['idsToGroup'] == NULL){
	    $db_dM->addWhere('transaccion_id= \'' . $arrParametros['id'] .'\'');
	}else{
	    $ids = substr($arrParametros['idsToGroup'], 0, -1); 
	    $db_dM->addWhere('transaccion_id IN  (' . $ids . ')');
	}
	    
	$db_dM->generarUpdate();   
	//asigamos el resulta que es un array a una variable
	//echo $db_dM->getQry();exit;
	$resultado= $db_dM->ejecutar();

	if(count($resultado)==0){
	     $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al salvar los datos de cabecera'
		    );
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'Datos de cabecera actualizados'
		    );
	}
	echo json_encode($arrDevolver);exit;
    }
}
