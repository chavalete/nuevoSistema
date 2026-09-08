<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'DatoTransaccionExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mensajeria/dmelmac/TransaccionesANMAT.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/FrenteProductosAnmat.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/FrenteGln.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';
require_once 'FrenteTipoMovimientoAnmat.php';
require_once '../errores/FrenteErrorAnmatExtendido.php';
/*
 * dMELMAC 
 * chava
 */

/**
 * Description of FrenteTransaccionesAgrupadas
 *
 * @author by dMELMAC
 */

class FrenteTransaccionesReenvio
{
    private $_db;
    private $_colObjDatoTransaccion= Array();
        
    private $_arrError = Array();

    //** NECESARIOS PARA EL FRENTE DE FRENTES//
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    private $_cabeceraCargada;
    
    //** FIN NECESIARIOS**//

    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
	//$this->_objFuncionesComunes = new FuncionesComunes();

        
    }
    
    public function setColObjDatoTransaccion($obj){
	//var_dump($obj);exit;
        $this->_colObjDatoTransaccion[$obj->getTransaccionId()] = $obj;
    }
    
    public function getColObjDatoTransaccion(){
        return $this->_colObjDatoTransaccion;
    }
    
    public function setCabeceraCargada($cargada){
        $this->_cabeceraCargada = $cargada;
    }
    
    public function getCabeceraCargada(){
        return $this->_cabeceraCargada;
    }
    public function cargarTransaccionCompletaParaEnvio($referenciaNro){
       
        $this->_db->addSelect('transaccion_id, transaccion_detalle_id');
	$this->_db->addFrom('datos_transacciones_anmat INNER JOIN detalle_transacciones_anmat USING(transaccion_id)');
        $this->_db->addWhere('referencia_nro = \'' . $referenciaNro . '\'');
        $this->_db->addWhere('codigo_transaccion IS NULL AND fecha_hora_transaccion IS NULL AND error_id IS NULL AND no_requiere_autogestion = false AND autogestion = false');
        
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultadoEnvio = $this->_db->ejecutar();
        
        $this->cargarLosDatosLazzy($resultadoEnvio);
        $this->cargarLosDetallesLazzyEnvio($resultadoEnvio);
        
    }
    public function cargarLosDatosLazzy($arrIds){
    
	if(is_array($arrIds)){
	    foreach($arrIds as $id){
		$ids.= $id['transaccion_id'] . ",";
	   }
		$ids=substr($ids, 0, -1);
	}else{
		$ids=$arrIds;
	}
	$this->_db->addSelect('*,to_char(fecha_evento,\'DD/MM/YYYY\') AS f_evento, hora_evento AS h_evento');
        $this->_db->addFrom('datos_transacciones_anmat');
        $this->_db->addWhere("transaccion_id IN (" . $ids . ")" );
        $this->_db->addOrderBy('transaccion_id DESC');
        
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        $FrenteGlnOrigen = NEW FrenteGln();
        $FrenteGlnOrigen->cargarGlnOrigenLazzy($resultado);
        $arrObjGlnOrigen= $FrenteGlnOrigen->getColObjGlnOrigen();
               
        $FrenteGlnDestino = NEW FrenteGln();
        $FrenteGlnDestino->cargarGlnDestinoLazzy($resultado);
        $arrObjGlnDestino= $FrenteGlnDestino->getColObjGlnDestino();
        //var_dump($arrObjGlnDestino);exit;
        $FrenteTipoMovimientoAnmat = NEW FrenteTipoMovimientoAnmat();
        $FrenteTipoMovimientoAnmat->cargarTipoMovimientoLazzy($resultado);
        $arrTipoMovimientoAnmat= $FrenteTipoMovimientoAnmat->getColObjTipoMovimientoAnmat();
        $FrenteErrorAnmatExtendido = NEW FrenteErrorAnmatExtendido();
        $FrenteErrorAnmatExtendido->cargarErrorAnmatLazzy($resultado);
        $arrErrorAnmatExtendido = $FrenteErrorAnmatExtendido->getColObjErrorAnmat();
        
        foreach($resultado AS $datos){
       	    
	    $objDatosTransaccion= NEW DatoTransaccionExtendido();
	    $objDatosTransaccion->cargarme($datos);
	    $objDatosTransaccion->setObjGlnOrigen($arrObjGlnOrigen[$datos[gln_origen]]);
	    //var_dump($arrObjGlnOrigen[$datos[gln_origen]]);
	    $objDatosTransaccion->setObjGlnDestino($arrObjGlnDestino[$datos[gln_destino]]);
	    $objDatosTransaccion->setObjTipoMovimiento($arrTipoMovimientoAnmat[$datos[id_evento]]);
	    $objDatosTransaccion->setObjErrorAnmat($arrErrorAnmatExtendido[$datos[error_id]]);
	    
	    $this->setColObjDatoTransaccion($objDatosTransaccion);
        }
    
    }
    
    public function cargarLosDetallesLazzy($arrIds){
    
	foreach($arrIds as $id){
	    $ids.= $id['transaccion_detalle_id'] . ",";
	}
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
	$this->_db->addFrom('datos_transacciones_anmat');
        $this->_db->addFrom('INNER JOIN detalle_transacciones_anmat USING(transaccion_id)');
        $this->_db->addWhere("transaccion_detalle_id IN (" . $ids . ")" );
        $this->_db->addWhere('autogestion = false');
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        $FrenteProductosAnmat = NEW FrenteProductosAnmat();
        $FrenteProductosAnmat->cargarProductosLazzy($resultado);
        $arrObjProductos= $FrenteProductosAnmat->getColProductos();

        foreach($this->getColObjDatoTransaccion() AS $objTransaccion){
        
	    foreach($resultado AS $detalle){
	    
		$objDetalleTransaccion= NEW DetalleTransaccionExtendido();
		$objDetalleTransaccion->cargarme($detalle);
		$objDetalleTransaccion->setObjProducto($arrObjProductos[$detalle[producto_gtin]]);
		$objDetalleTransaccion->setCodigoTransaccion($detalle[codigo_transaccion]);
		$this->_colObjDatoTransaccion[$objTransaccion->getTransaccionId()]->setColObjDetalleTransaccion($objDetalleTransaccion);
	    }
	}
    
    }
    public function cargarLosDetallesLazzyEnvio($arrIds){
    
	foreach($arrIds as $id){
	    $ids.= $id['transaccion_detalle_id'] . ",";
	}
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*,to_char(lote_vencimiento,\'DD/MM/YYYY\') AS lote_vencimiento');
        $this->_db->addFrom('detalle_transacciones_anmat');
        $this->_db->addWhere("transaccion_detalle_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        $FrenteProductosAnmat = NEW FrenteProductosAnmat();
        $FrenteProductosAnmat->cargarProductosLazzy($resultado);
        $arrObjProductos= $FrenteProductosAnmat->getColProductos();

        
	foreach($resultado AS $detalle){
	
	    $objDetalleTransaccion = NEW DetalleTransaccionExtendido();
	    $objDetalleTransaccion->cargarme($detalle);
	    $objDetalleTransaccion->setObjProducto($arrObjProductos[$detalle[producto_gtin]]);
	    $this->_colObjDatoTransaccion[$objDetalleTransaccion->getTransaccionId()]->setColObjDetalleTransaccion($objDetalleTransaccion);
	}
    }
    
    public function cargarTransaccionCompleta($referenciaNro){
    
        $this->_db->addSelect('transaccion_id, codigo_transaccion, transaccion_detalle_id');
	$this->_db->addFrom('datos_transacciones_anmat INNER JOIN detalle_transacciones_anmat USING(transaccion_id)');
        $this->_db->addWhere('referencia_nro = \'' . $referenciaNro . '\'');      
        $this->_db->addWhere('autogestion = false');
        
        $this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
        $resultadoCabeceras = $this->_db->ejecutar();
        
        $this->cargarLosDatosLazzy($resultadoCabeceras['0']['transaccion_id']);
	$this->cargarLosDetallesLazzy($resultadoCabeceras);
    }

    public function enviarTransaccion(){

/*  
	SELECT count(*), insertada_fecha_hora::DATE, es_reversa FROM datos_transacciones_anmat WHERE codigo_transaccion IS NULL AND fecha_hora_transaccion IS NULL AND error_id IS NULL AND no_requiere_autogestion = false AND autogestion = false  GROUP BY insertada_fecha_hora::DATE, es_reversa  ORDER BY insertada_fecha_hora;
	
	SELECT count(referencia_nro), referencia_nro,insertada_fecha_hora::DATE, es_reversa FROM datos_transacciones_anmat WHERE codigo_transaccion IS NULL AND fecha_hora_transaccion IS NULL AND error_id IS NULL AND no_requiere_autogestion = false AND autogestion = false  GROUP BY referencia_nro, insertada_fecha_hora::DATE, es_reversa  ORDER BY insertada_fecha_hora;
*/
	$this->_db->addSelect('count(referencia_nro), referencia_nro,insertada_fecha_hora::DATE, es_reversa');
	$this->_db->addFrom('datos_transacciones_anmat');
        $this->_db->addWhere('codigo_transaccion IS NULL AND fecha_hora_transaccion IS NULL AND error_id IS NULL AND no_requiere_autogestion = false AND autogestion = false ');
        $this->_db->addWhere('insertada_fecha_hora::date = \'2013-04-11\'');
        $this->_db->addGroup('referencia_nro, insertada_fecha_hora::DATE, es_reversa');
        $this->_db->addOrderBy('insertada_fecha_hora');
        
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
	
	foreach($resultado AS $referecias){
	
		$this->cargarTransaccionCompletaParaEnvio($referecias['referencia_nro']);
	}
	$objTransaccionesANMAT = new TransaccionesANMAT();
	$cantErrores=0;
	foreach($this->getColObjDatoTransaccion() AS $objTransaccion){
	    echo "<pre>";
	    print_r($objTransaccion);
	    echo "<pre>";exit;
	    $objTransaccionesANMAT->enviarTransacciones($objTransaccion);
	    if($objTransaccion->getErrorId() !=NULL){
		$cantErrores++;
	    }
	}
	if($cantErrores==0){
		$arrDevolver = array(
			"soyError" =>false ,
			"nivel" => 0,
			"mensaje" =>"Transaccion enviada con exito"
			);
	}else{
		$arrDevolver = array(
			"soyError" =>false ,
			"nivel" => 0,
			"mensaje" =>"Transaccion enviada con error"
			);
	}
	echo json_encode($arrDevolver);
	
    }
}
$FrenteReenvio = NEW FrenteTransaccionesReenvio();
$FrenteReenvio->enviarTransaccion();