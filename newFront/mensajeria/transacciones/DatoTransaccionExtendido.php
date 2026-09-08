<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/mensajeria/transacciones/DatoTransaccion.php';
require_once 'DetalleTransaccionExtendido.php';
require_once 'TipoMovimientoAnmatExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mensajeria/errores/ErrorAnmatExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/GlnOrigenExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/GlnDestinoExtendido.php';

class DatoTransaccionExtendido extends DatoTransaccion
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
	
	$this->setTransaccionId($id);

	$db_dM = new FrenteAlmacenamiento('');

	$this->_db->addSelect('transaccion_id');
        $this->_db->addSelect('tipo_movimiento_id');
        $this->_db->addSelect('id_evento');
	$this->_db->addSelect('codigo_transaccion');
        $this->_db->addSelect('id_movimiento');
        $this->_db->addSelect('to_char(fecha_evento,\'DD-MM-YYYY\') AS f_evento');
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
	
    
	
	$this->_db->addFrom('datos_transacciones_anmat');

	
	$this->_db->addWhere('transaccion_id = \'' . $this->getTransaccionId() . '\'');
	$this->_db->addWhere('autogestion = false');

	//echo $this->getTransaccionId();
	$this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
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
	
        $this->setTransaccionId($resultado[transaccion_id]);
	$this->setIdMovimiento($resultado[id_movimiento]);
	$this->setFechaEvento($resultado[f_evento]);
	$this->setHoraEvento($resultado[h_evento]);
	$this->setGlnOrigen($resultado[gln_origen]);
	$this->setCuitOrigen($resultado[cuit_origen]);
	$this->setGlnDestino($resultado[gln_destino]);
	$this->setCuitDestino($resultado[cuit_destino]);
	$this->setCodigoSucursal($resultado[codigo_sucursal]);
	$this->setApellido($resultado['apellido']);
	$this->setNombres($resultado['nombres']);
	$this->setNroDocumento($resultado['nro_documento']);
	$this->setSexo($resultado['sexo']);
	$this->setTipoDocumento($resultado['tipo_documento']);
	$this->setDireccion($resultado['direccion']);
	$this->setLocalidad($resultado['localidad']);
	$this->setNro($resultado['nro']);
	$this->setPiso($resultado['piso']);
	$this->setDepto($resultado['dpto']);
	$this->setNroPostal($resultado['n_postal']);
	$this->setTelefono($resultado['telefono']);
	$this->setIdObraSocial($resultado['id_obra_social']);
	$this->setNroAsociado($resultado['nro_asociado']);
	$this->setNroReferencia($resultado['referencia_nro']);
	$this->setDisponeNombre($resultado['dispone_nombre']);
	$this->setTipoMovimientoId($resultado['tipo_movimiento_id']);
	$this->setIdEvento($resultado['id_evento']);
	$this->setEsReversa($resultado['es_reversa']);
	//var_dump($arrTransaccion);exit;
		
	if($resultado['nro_factura'] != NULL && $resultado['nro_factura'] != '0'){
	    $this->setNroFactura($resultado['nro_factura']);
	}
	if($resultado['nro_remito'] != NULL && $resultado['nro_remito'] != '0'){
	    $this->setNroRemito($resultado['nro_remito']);
	}
	$this->setCodigoTransaccion($resultado[codigo_transaccion]);
	$this->setFechaHoraTransaccion($resultado[fecha_hora_transaccion]);
	$this->setErrorId($resultado[error_id]);
	
/*
	$this->setObjGlnOrigen(new GlnOrigenExtendido());
	$this->getObjGlnOrigen()->cargarme($arrTransaccion[0]['gln_origen']);
	$this->setObjGlnDestino(new GlnDestinoExtendido());
	$this->getObjGlnDestino()->cargarme($arrTransaccion[0]['gln_destino']);
	$this->setObjTipoMovimiento(new TipoMovimientoAnmatExtendido());
	$this->getObjTipoMovimiento()->cargarMe($arrTransaccion[0]['id_evento']);
	$this->setObjErrorAnmat(new ErrorAnmatExtendido());
	$this->getObjErrorAnmat()->cargarme($arrTransaccion[0]['error_id']);
  */ 
	
    }

    public function salvarme($objTransaccion){

	$this->_db->addCamposTabla('seq_dato_transaccion_transaccion_id');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();

	$objTransaccion->setReprocesadoAnteriorId($objTransaccion->getTransaccionId());

	
	$objTransaccion->setTransaccionId(($id[0]['nextval']));

        $this->_db->addFrom('datos_transacciones_anmat');

        $this->_db->addCamposTabla('transaccion_id');
        $this->_db->addCamposTabla('id_evento');
        $this->_db->addCamposTabla('id_movimiento');
        $this->_db->addCamposTabla('fecha_evento');
        $this->_db->addCamposTabla('hora_evento');
        $this->_db->addCamposTabla('gln_origen');
        $this->_db->addCamposTabla('cuit_origen');
        $this->_db->addCamposTabla('gln_destino');
        $this->_db->addCamposTabla('cuit_destino');
        $this->_db->addCamposTabla('nro_remito');
        $this->_db->addCamposTabla('nro_factura');
	$this->_db->addCamposTabla('reprocesado_anterior_id');
	$this->_db->addCamposTabla('codigo_sucursal');
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
	$this->_db->addCamposTabla('es_reversa');
	$this->_db->addCamposTabla('tipo_movimiento_id');
    
        
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
	if($objTransaccion->getReprocesadoAnteriorId()== NULL){
	    $this->_db->addCamposValue('0');
	}else{
	    $this->_db->addCamposValue('\'' . $objTransaccion->getReprocesadoAnteriorId(). '\'');
	}
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
	$this->_db->addCamposValue('\'' . $objTransaccion->getEsReversa() . '\'');
	$this->_db->addCamposValue('\'' . $objTransaccion->getTipoMovimientoId() . '\'');

	$insertTransaccion=$this->_db->generarInsert();
	//echo $this->_db->getQry();exit;

        $resultado = $this->_db->ejecutar();
	
	if(count($resultado)==0){
	     $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al salvar los datos de cabecera'
		    );
	echo json_encode($arrDevolver);exit;
	}        
    }
    public function actualizarMe($arrParametros){

	$db_dM = new FrenteAlmacenamiento('');

	if(is_numeric($arrParametros['campos']['tipo_movimiento_id'])){

	    $db_dM->addCamposUpdate('tipo_movimiento_id= \'' . $arrParametros['campos']['tipo_movimiento_id'] .'\'');
	}

	if(is_numeric($arrParametros['campos']['gln_destino'] != NULL)){
	    $db_dM->addCamposUpdate('gln_destino= \'' . $arrParametros['campos']['gln_destino'] .'\'');
	}else{
	        $db_dM->addCamposUpdate('gln_destino= NULL');
	}
	if(is_numeric($arrParametros['campos']['gln_origen'])){
	    $db_dM->addCamposUpdate('gln_origen= \'' . $arrParametros['campos']['gln_origen'] .'\'');
	}
	if($arrParametros['campos']['nro_factura']!='null'){
	    $db_dM->addCamposUpdate('nro_factura= \'' . $arrParametros['campos']['nro_factura'] .'\'');
	}
	if($arrParametros['campos']['nro_remito']!='null'){
	    $db_dM->addCamposUpdate('nro_remito= \'' . $arrParametros['campos']['nro_remito'] .'\'');
	}
	$db_dM->addFrom('datos_transacciones_anmat');
	$db_dM->addWhere('transaccion_id = \'' . $arrParametros['campos']['transaccion_id'] .'\'');
	    
	$db_dM->generarUpdate();   
	//asigamos el resulta que es un array a una variable
	//echo $db_dM->getQry();exit;
	$resultado= $db_dM->ejecutar();

	if(count($resultado)== 0){
		$arrDevolver = array(
		    "soyError" => false,
		    "nivel" => 0,
		    "mensaje" =>"Actualizado"
		    );
	}else{

		$arrDevolver = array(
		    "soyError" => true,
		    "nivel" => 1,
		    "mensaje" =>"Error en la actualizacion"
		    );
	}
	echo json_encode($arrDevolver);exit;
    }
    public function noAutogestionar($id){
	
	$db_dM = new FrenteAlmacenamiento('');

	$db_dM->addCamposUpdate('no_requiere_autogestion= true');
	$db_dM->addCamposUpdate('no_requiere_autogestion_usuario_id = \'' . $_SESSION['usuarioId'] .'\'');
	$db_dM->addCamposUpdate('no_requiere_autogestion_fecha_hora = now()');

	$db_dM->addFrom('datos_transacciones_anmat');
	$db_dM->addWhere('referencia_nro = \'' . $id .'\'');
	    
	$db_dM->generarUpdate();   
	//asigamos el resulta que es un array a una variable
	//echo $db_dM->getQry();exit;
	$resultado= $db_dM->ejecutar();

	
	if(count($resultado) == 0){
		$arrDevolver = array(
		    "soyError" => true,
		    "nivel" => 0,
		    "mensaje" =>"No se pudo cancelar la transaccion"
		    );
	}else{

		$arrDevolver = array(
		    "soyError" => false,
		    "nivel" => 1,
		    "mensaje" =>"Transaccion Actualizada"
		    );
	}
	echo json_encode($arrDevolver);
    }
    
    public function grabarCodigoTransaccion($codigo){
        $this->_db->addCamposUpdate('codigo_transaccion = ' . $codigo);
        $this->_db->addCamposUpdate('fecha_hora_transaccion = current_timestamp');
        $this->_db->addFrom('datos_transacciones_anmat');
        $this->_db->addWhere('transaccion_id = ' . $this->getTransaccionId());
        $this->_db->generarUpdate();
        $this->_db->ejecutar();
        if(count($this->_db->getArrError()) > 0 ){
            $this->setArrError($this->_db_dM->getArrError());            
        }
        $this->setCodigoTransaccion($codigo);
    }

    public function grabarErrorAnmat($error){
        //llamo a la clase que maneja el error y lo guarda
        $errorANMAT = new ErrorAnmatExtendido();
        $errorANMAT->salvarme($error[0]->return->errores);
        //guado el id del error
        $this->_db->addCamposUpdate('error_id = ' . $errorANMAT->getErrorId() /* seq de error */);
        $this->_db->addCamposUpdate('fecha_hora_transaccion = current_timestamp');
        $this->_db->addFrom('datos_transacciones_anmat');
        $this->_db->addWhere('transaccion_id = ' . $this->getTransaccionId());
        $this->_db->generarUpdate();
        $this->_db->ejecutar();
        if(count($this->_db->getArrError()) > 0 ){
            $this->setArrError($this->_db->getArrError());            
        }
        $this->setErrorId($errorANMAT->getErrorId());
    }
    public function cargarMensaje(){
    
	$this->_db->addSelect('error_id,codigo_transaccion');
	$this->_db->addFrom('datos_transacciones_anmat');

	$this->_db->addWhere('transaccion_id = \'' . $this->getTransaccionId() . '\'');

	$this->_db->generarSelect();

	$arrError = $this->_db->ejecutar();
	
	if($arrError[0]['error_id']!= NULL){
		$errorANMAT = new ErrorAnmatExtendido();
		$errorANMAT->cargarme($arrError[0]['error_id']);
		$arrDevolver = array(
			"soyError" =>false ,
			"nivel" => 0,
			"mensaje" =>"Transaccion enviada con error: " . $errorANMAT->getErrorDesc()
			);
		echo json_encode($arrDevolver);
	}else{
		$arrDevolver = array(
			"soyError" =>false ,
			"nivel" => 0,
			"mensaje" =>"Transaccion enviada codigo: " . $arrError[0]['codigo_transaccion']
			);
		echo json_encode($arrDevolver);
	}
    }
    public function actualizarIdEvento($idEvento){
        $this->_db->addCamposUpdate('id_evento=' . $idEvento);
        $this->_db->addFrom('datos_transacciones_anmat');
        $this->_db->addWhere('transaccion_id = ' . $this->getTransaccionId());
        $this->_db->generarUpdate();
        $this->_db->ejecutar();
        if(count($this->_db->getArrError()) > 0 ){
            $this->setArrError($this->_db->getArrError());            
        }
    }
}
