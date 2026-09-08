<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mensajeria/transacciones/TipoMovimientoAnmatExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/GlnOrigenExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/GlnDestinoExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/mensajeria/mensajeria/MensajeriaAnmat.php';

class MensajeriaAnmatExtendido extends MensajeriaAnmat
{
    private $_db;
    private $_dispensaHora;
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
    }  


    public function setDispensaHora($dispensaHora){

	$this->_dispensaHora = $dispensaHora;

    }
    public function getDispensaHora(){

	return $this->_dispensaHora;
    }

    public function getDataDB($id){
	$this->setMensajeriaId($id);

	$db_dM = new FrenteAlmacenamiento('');

	$this->_db->addSelect('fecha_hora_movimiento');
	$this->_db->addSelect('id_movimiento_sc');
	$this->_db->addSelect('f_evento');
	$this->_db->addSelect('h_evento');
	$this->_db->addSelect('gln_origen');
	$this->_db->addSelect('cuit_origen');
	$this->_db->addSelect('gln_destino');
	$this->_db->addSelect('cuit_destino');
	$this->_db->addSelect('n_factura');
	$this->_db->addSelect('n_remito');
	$this->_db->addSelect('vencimiento');
	$this->_db->addSelect('gtin');
	$this->_db->addSelect('lote');
	$this->_db->addSelect('numero_serial');
	$this->_db->addSelect('id_evento');
	$this->_db->addSelect('apellido');
	$this->_db->addSelect('nombres');
	$this->_db->addSelect('n_documento');
	$this->_db->addSelect('sexo');
	$this->_db->addSelect('tipo_documento');
	$this->_db->addSelect('direccion');
	$this->_db->addSelect('localidad');
	$this->_db->addSelect('numero');
	$this->_db->addSelect('piso');
	$this->_db->addSelect('dpto');
	$this->_db->addSelect('n_postal');
	$this->_db->addSelect('telefono');
	$this->_db->addSelect('id_obra_social');
	$this->_db->addSelect('id_tipo_movimiento_sc');
	$this->_db->addSelect('regristo_finalizado');
	$this->_db->addSelect('regristo_traspasado');
	$this->_db->addSelect('id_agente');
	$this->_db->addSelect('soy_reproceso');
	$this->_db->addSelect('soy_remito_drog_fcia');
	$this->_db->addSelect('codigo_sucursal');
	$this->_db->addSelect('fecha_hora_transmision');
	$this->_db->addSelect('recepcion_informada_fcia');
	$this->_db->addSelect('to_char(fecha_hora_transmision_recepcion_fcia, \'DD-MM-YYYY HH24:MI\') AS fecha_hora_transmision_recepcion_fcia');
	$this->_db->addSelect('dispensa_informada_fcia');
	$this->_db->addSelect('es_agrupador');
	$this->_db->addSelect('referencia_nro');
	$this->_db->addSelect('nro_asociado');
	$this->_db->addSelect('dispone_nombre');
	$this->_db->addSelect('no_informar');
	$this->_db->addSelect('no_informar_usuario_id');
	$this->_db->addSelect('no_informar_fecha_hora');
	$this->_db->addSelect('insertada_fecha_hora +  interval \'72 hours\' < now() AS dispensa_hora');
	$this->_db->addFrom('mensajeria_anmat');

	
	$this->_db->addWhere('mensajeria_id = \'' . $this->getMensajeriaId() . '\'');

	//echo $this->getTransaccionId();
	$this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
	$arrMensajeria = $this->_db->ejecutar();

	//verificamos que el movimiento exista en la db
        if(count($arrMensajeria) == 0){
            $errorDetalle = 'La mensajeria que intenta cargar no existe';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
    }
    public function cargarme($datos){

	//var_dump($datos);exit;
	$this->setMensajeriaId($datos['mensajeria_id']);
	$this->setFechaHoraMovimiento($datos['fecha_hora_movimiento']);
	$this->setIdMovimientoSc($datos['id_movimiento_sc']);
	$this->setFechaEvento($datos['f_evento']);
	$this->setHoraEvento($datos['h_evento']);
	/*
	$this->setObjGlnOrigen(new GlnOrigenExtendido());
	$this->getObjGlnOrigen()->cargarme($arrMensajeria[0]['gln_origen']);
	$this->setCuitOrigen($arrMensajeria[0]['cuit_origen']);
	$this->setObjGlnDestino(new GlnDestinoExtendido());
	$this->getObjGlnDestino()->cargarme($arrMensajeria[0]['gln_destino']);
	*/
	$this->setCuitDestino($datos['cuit_destino']);
	
	if($datos['n_factura'] != NULL && $datos['n_factura'] != '0'){
	    $this->setNroFactura($datos['n_factura']);
	}
	if($datos['n_remito'] != NULL && $datos['n_remito'] != '0'){
	    $this->setNroRemito($datos['n_remito']);
	}
	$this->setVencimiento($datos['vencimiento']);	
	/*
	$this->setObjProducto(new ProductoExtendidoAnmat());
        $this->getObjProducto()->cargarme($arrMensajeria[0]['gtin']);
	*/
	$this->setLote($datos['lote']);
	$this->setNroSerial($datos['numero_serial']);
	/*
	$this->setObjTipoMovimiento(new TipoMovimientoAnmatExtendido());
	$this->getObjTipoMovimiento()->cargarMe($arrMensajeria[0]['id_evento']);
	*/
	$this->setApellido($datos['apellido']);
	$this->setNombres($datos['nombres']);
	$this->setNroDocumento($datos['n_documento']);
	$this->setSexo($datos['sexo']);
	$this->setTipoDocumento($datos['tipo_documento']);
	$this->setDireccion($datos['direccion']);
	$this->setLocalidad($datos['localidad']);
	$this->setNumero($datos['numero']);
	$this->setPiso($datos['piso']);
	$this->setDpto($datos['dpto']);
	$this->setNroPostal($datos['n_postal']);
	$this->setTelefono($datos['telefono']);
	$this->setIdObraSocial($datos['id_obra_social']);
	$this->setTelefono($datos['piso']);
	$this->setIdTipoMovimientoSc($datos['id_tipo_movimiento_sc']);
	$this->setRegristoFinalizado($datos['regristo_finalizado']);
	$this->setRegristoTraspasado($datos['regristo_traspasado']);
	$this->setIdAgente($datos['id_agente']);
	$this->setSoyReproceso($datos['soy_reproceso']);
	$this->setSoyRemitoDrogFcia($datos['soy_remito_drog_fcia']);
	$this->setIdSucursal($datos['id_sucursal']);
	$this->setFechaHoraTrasmision($datos['fecha_hora_transmision']);
	$this->setRepecionInformadaFcia($datos['recepcion_informada_fcia']);
	$this->setFechaHoraTrasmisionRecepFcia($datos['fecha_hora_transmision_recepcion_fcia']);
	$this->setDispensaInformadaFcia($datos['dispensa_informada_fcia']);
	$this->setEsAgrupador($datos['es_agrupador']);
	$this->setReferenciaNro($datos['referencia_nro']);
	$this->setNroAsociado($datos['id_sucursal']);
	$this->setDisponeNombre($datos['dispone_nombre']);
	$this->setNoInformar($datos['no_informar']);
	$this->setNoInformarUsuarioId($datos['no_informar_usuario_id']);
	$this->setNoInformarFechaHora($datos['no_informar_fecha_hora']);
	$this->setDispensaHora($datos['dispensa_hora_posta']);
	
    }

    public function salvarme($arrParametros){
        
    }
    public function actualizarme($arrParametros){

	
    }
    public function bloquearPorMovimiento($arrParametros){

	$this->_db->addCamposUpdate('no_informar= true');
	$this->_db->addCamposUpdate('no_informar_usuario_id= \'' . $_SESSION['usuarioId'] .'\'');
	$this->_db->addCamposUpdate('no_informar_fecha_hora = now()');

	$this->_db->addFrom('mensajeria_anmat');
	$this->_db->addWhere('id_movimiento_sc = \'' . $arrParametros['id'] .'\'');
	$this->_db->addWhere('id_evento = \'' . '101' . '\'');
	    
	$this->_db->generarUpdate();   

	//asigamos el resulta que es un array a una variable
	//echo $db_dM->getQry();exit;
	$resultado= $this->_db->ejecutar();

	if(count($resultado) == 0){
            $arrError['detalle']= 'Error en bloqueo de dispensa';
            $arrError['archivo'] = __FILE__;
            $this->setArrError($arrError);
	    echo 0;exit;
        }else{
	    echo 1;exit;
	}
    }
    public function bloquearDetalles($arrParametros){

    
	$this->_db->addCamposUpdate('no_informar= \''. $arrParametros['bloqueado'] . '\'');
	$this->_db->addCamposUpdate('no_informar_usuario_id= \'' . $_SESSION['usuarioId'] .'\'');
	$this->_db->addCamposUpdate('no_informar_fecha_hora = now()');

	$this->_db->addFrom('mensajeria_anmat');
	$this->_db->addWhere('mensajeria_id = \'' . $arrParametros['id'] .'\'');
	$this->_db->addWhere('id_evento = \'' . '111' . '\'');
	    
	$this->_db->generarUpdate();   

	//asigamos el resulta que es un array a una variable
	//echo $this->_db->getQry();exit;
	$resultado= $this->_db->ejecutar();

	if(count($resultado)==0){
	     $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error bloquear'
		    );
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'Registro Bloqueado'
		    );
	}
	echo json_encode($arrDevolver);exit;
    }
}
