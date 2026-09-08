<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'DatoAutogestionExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mensajeria/dmelmac/TransaccionesANMAT.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/FrenteGln.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/FrenteProductosAnmat.php';
Require_once 'FrenteTipoMovimientoAnmat.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mensajeria/errores/FrenteErrorAnmatExtendido.php';
/*
 * dMELMAC 
 * chava
 */

/**
 * Description of FrenteAutogestion
 *
 * @author by dMELMAC
 */

class FrenteAutogestionAgrupada
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
	$this->_objFuncionesComunes = new FuncionesComunes();

        
    }
    
    public function setColObjDatoTransaccion($obj){
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
    public function cargarLosDatosLazzy($arrIds){
    
	if(is_array($arrIds)){
	    foreach($arrIds as $id){
		$ids.= $id['transaccion_id'] . ",";
	   }
		$ids=substr($ids, 0, -1);
	}else{
		$ids=$arrIds;
	}
	$this->_db->addSelect('*,to_char(fecha_evento,\'YYYY-MM-DD\') AS f_evento,hora_evento AS h_evento,to_char(fecha_hora_transaccion, \'DD-MM-YYYY HH24:MI\') AS fecha_hora_transaccion');
        $this->_db->addFrom('datos_autogestion');
        $this->_db->addWhere("transaccion_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        $FrenteGlnOrigen = NEW FrenteGln();
        $FrenteGlnOrigen->cargarGlnOrigenLazzy($resultado);
        $arrObjGlnOrigen= $FrenteGlnOrigen->getColObjGlnOrigen();
        
        $FrenteGlnDestino = NEW FrenteGln();
        $FrenteGlnDestino->cargarGlnDestinoLazzy($resultado);
        $arrObjGlnDestino= $FrenteGlnDestino->getColObjGlnDestino();
        $FrenteTipoMovimientoAnmat = NEW FrenteTipoMovimientoAnmat();
        $FrenteTipoMovimientoAnmat->cargarTipoMovimientoLazzy($resultado);
        $arrTipoMovimientoAnmat= $FrenteTipoMovimientoAnmat->getColObjTipoMovimientoAnmat();
        $FrenteErrorAnmatExtendido = NEW FrenteErrorAnmatExtendido();
        $FrenteErrorAnmatExtendido->cargarErrorAnmatLazzy($resultado);
        $arrErrorAnmatExtendido = $FrenteErrorAnmatExtendido->getColObjErrorAnmat();
        //var_dump($arrErrorAnmatExtendido);exit;
        foreach($resultado AS $datos){
       	    //echo $datos[error_id];exit;
	    $objDatosAutogestion = NEW DatoAutogestionExtendido();
	    $objDatosAutogestion->cargarme($datos);
	    $objDatosAutogestion->setObjGlnOrigen($arrObjGlnOrigen[$datos[gln_origen]]);
	    $objDatosAutogestion->setObjGlnDestino($arrObjGlnDestino[$datos[gln_destino]]);
	    $objDatosAutogestion->setObjTipoMovimiento($arrTipoMovimientoAnmat[$datos[id_evento]]);
	    $objDatosAutogestion->setObjErrorAnmat($arrErrorAnmatExtendido[$datos[error_id]]);
	    $this->setColObjDatoTransaccion($objDatosAutogestion);
        }
    
    }
    public function cargarLosDatosLazzyModal($arrIds){
    
	if(is_array($arrIds)){
	    foreach($arrIds as $id){
		$ids.= $id['transaccion_id'] . ",";
	   }
		$ids=substr($ids, 0, -1);
	}else{
		$ids=$arrIds;
	}
	$this->_db->addSelect('*,to_char(fecha_evento,\'YYYY-MM-DD\') AS f_evento,hora_evento AS h_evento,to_char(fecha_hora_transaccion, \'DD-MM-YYYY HH24:MI\') AS fecha_hora_transaccion');
        $this->_db->addFrom('datos_autogestion');
        $this->_db->addWhere("transaccion_id IN (" . $ids . ")" );
        $this->_db->addLimit(1);
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        $FrenteGlnOrigen = NEW FrenteGln();
        $FrenteGlnOrigen->cargarGlnOrigenLazzy($resultado);
        $arrObjGlnOrigen= $FrenteGlnOrigen->getColObjGlnOrigen();
        
        $FrenteGlnDestino = NEW FrenteGln();
        $FrenteGlnDestino->cargarGlnDestinoLazzy($resultado);
        $arrObjGlnDestino= $FrenteGlnDestino->getColObjGlnDestino();
        $FrenteTipoMovimientoAnmat = NEW FrenteTipoMovimientoAnmat();
        $FrenteTipoMovimientoAnmat->cargarTipoMovimientoLazzy($resultado);
        $arrTipoMovimientoAnmat= $FrenteTipoMovimientoAnmat->getColObjTipoMovimientoAnmat();
        $FrenteErrorAnmatExtendido = NEW FrenteErrorAnmatExtendido();
        $FrenteErrorAnmatExtendido->cargarErrorAnmatLazzy($resultado);
        $arrErrorAnmatExtendido = $FrenteErrorAnmatExtendido->getColObjErrorAnmat();
        //var_dump($arrErrorAnmatExtendido);exit;
        foreach($resultado AS $datos){
       	    //echo $datos[error_id];exit;
	    $objDatosAutogestion = NEW DatoAutogestionExtendido();
	    $objDatosAutogestion->cargarme($datos);
	    $objDatosAutogestion->setObjGlnOrigen($arrObjGlnOrigen[$datos[gln_origen]]);
	    $objDatosAutogestion->setObjGlnDestino($arrObjGlnDestino[$datos[gln_destino]]);
	    $objDatosAutogestion->setObjTipoMovimiento($arrTipoMovimientoAnmat[$datos[id_evento]]);
	    $objDatosAutogestion->setObjErrorAnmat($arrErrorAnmatExtendido[$datos[error_id]]);
	    $this->setColObjDatoTransaccion($objDatosAutogestion);
        }
    
    }
    public function cargarLosDetallesLazzy($arrIds){
    
	if(is_array($arrIds)){
	    foreach($arrIds as $id){
		$ids.= $id['transaccion_detalle_id'] . ",";
	   }
	}else{
		$ids=$arrIds;
	}
	
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
        $this->_db->addFrom('detalle_autogestion');
        $this->_db->addWhere("transaccion_detalle_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        $FrenteProductosAnmat = NEW FrenteProductosAnmat();
        $FrenteProductosAnmat->cargarProductosLazzy($resultado);
        $arrObjProductos= $FrenteProductosAnmat->getColProductos();

        foreach($this->getColObjDatoTransaccion() AS $objTransaccion){
        
	    foreach($resultado AS $detalle){
	    
		$objDetalleAutogestion = NEW DetalleAutogestionExtendido();
		$objDetalleAutogestion->cargarme($detalle);
		$objDetalleAutogestion->setObjProducto($arrObjProductos[$detalle[producto_gtin]]);
		$this->_colObjDatoTransaccion[$objTransaccion->getTransaccionId()]->setColObjDetalleTransaccion($objDetalleAutogestion);
	    }
	}
    }
    public function cargarLosDetallesLazzyEnvio($arrIds){
    
	foreach($arrIds as $id){
	    $ids.= $id['transaccion_detalle_id'] . ",";
	}
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
        $this->_db->addFrom('detalle_autogestion');
        $this->_db->addWhere("transaccion_detalle_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        $FrenteProductosAnmat = NEW FrenteProductosAnmat();
        $FrenteProductosAnmat->cargarProductosLazzy($resultado);
        $arrObjProductos= $FrenteProductosAnmat->getColProductos();

        
	foreach($resultado AS $detalle){
	
	    $objDetalleAutogestion = NEW DetalleAutogestionExtendido();
	    $objDetalleAutogestion->cargarme($detalle);
	    $objDetalleAutogestion->setObjProducto($arrObjProductos[$detalle[producto_gtin]]);
	    $this->_colObjDatoTransaccion[$objDetalleAutogestion->getTransaccionId()]->setColObjDetalleTransaccion($objDetalleAutogestion);
	}
    }
    public function cargarAutogestionCompletaParaEnvio($referenciaNro){
       
	$this->_db->addSelect('transaccion_id, codigo_transaccion, transaccion_detalle_id');
	$this->_db->addFrom('datos_autogestion INNER JOIN detalle_autogestion USING(transaccion_id)');
        $this->_db->addWhere('referencia_nro = \'' . $referenciaNro . '\'');      
        $this->_db->addWhere('autogestion = false');        
        $this->_db->generarSelect();
        $resultadoEnvio = $this->_db->ejecutar();
        
        
        $this->cargarLosDatosLazzy($resultadoEnvio);
        $this->cargarLosDetallesLazzyEnvio($resultadoEnvio);
	
    }
    
    public function cargarAutogestionCompleta($referenciaNro){

        $this->_db->addSelect('transaccion_id, codigo_transaccion, transaccion_detalle_id');
	$this->_db->addFrom('datos_autogestion INNER JOIN detalle_autogestion USING(transaccion_id)');
        $this->_db->addWhere('referencia_nro = \'' . $referenciaNro . '\'');      
        $this->_db->addWhere('autogestion = false');
        
        $this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
        $resultadoCabeceras = $this->_db->ejecutar();
        
	$this->cargarLosDatosLazzy($resultadoCabeceras['0']['transaccion_id']);
	$this->cargarLosDetallesLazzy($resultadoCabeceras);
    }

    public function getError(){
        return $this->_arrError;
    }

    //**PARA EL FRENTE**//
    public function setOrdenTransacciones($parametros){
	if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){
	    $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'transaccion_id';
	    $this->_ordenColumnas['ordenarOrden']  =   'desc';
	}
    }

    public function getOrdenTransacciones(){
	return $this->_ordenColumnas;
    }

    public function setTotal($total)
    {
	$this->_total = $total;
    }
    public function getTotal()
    {
	return $this->_total;
    }
    //pagina 1 por defecto amigo
    public function setPagina($pagina=1)
    {
	$this->_pagina = $pagina;
    }
    public function getPagina()
    {
	return $this->_pagina;
    }



    public function armarDetalles($id){

	$this->cargarAutogestionCompleta($id);

	foreach($this->_colObjDatoTransaccion AS $objTransaccion){
		$propiedadesTransaccion =   array(
			array(

			    "display"   =>  'Codigo Transaccion',
			    "name"      =>  'codigo_transaccion',
			    "editable"  =>   false,
			    "class"	=>'',
			    "value"     =>  $objTransaccion->getCodigoTransaccion(),
			),
			array(
			    "display"   =>  'Fecha',
			    "name"      =>  'fecha_hora_transaccion',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objTransaccion->getFechaHoraTransaccion(),

			),
			array(
			    "display"   =>  'Nombre Origen',
			    "name"      =>  'gln_origen',
			    "editable"  =>  false,
			    "class"	=>'autocomplete',
			    "value"     =>  $objTransaccion->getObjGlnOrigen()->getGlnNombre(),

			),
			array(
			    "display"   =>  'GLN Origen',
			    "name"      =>  'gln_origen',
			    "class"	=>'',
			    "editable"  =>  true,
			    "value"     =>  $objTransaccion->getGlnOrigen(),

			),
			array(
			    "display"   =>  'Nombre Destino',
			    "name"      =>  'gln_destino',
			    "editable"  =>  false,
			    "class"	=>'autocomplete',
			    "value"     =>  $objTransaccion->getObjGlnDestino()->getGlnNombre(),

			),
			array(
			    "display"   =>  'Destino',
			    "name"      =>  'gln_destino',
			    "class"	=>'',
			    "editable"  =>  true,
			    "value"     =>  $objTransaccion->getGlnDestino(),

			),
			array(
			    "display"   =>  'Tipo Movimiento',
			    "name"      =>  'tipo_movimiento_id',
			    "class"	=>'autocomplete',
			    "editable"  =>  true,
			    "value"     =>  $objTransaccion->getObjTipoMovimiento()->getTipoMovimientoNombre() . " " . $objTransaccion->getObjTipoMovimiento()->getTipoMovimientoDe(),
			),
			array(
			    "display"   =>  'Nro Factura',
			    "name"      =>  'nro_factura',
			    "editable"  =>  true,
			    "class"	=>'',
			    "value"     =>  $objTransaccion->getNroFactura(),

			),
			array(
			    "display"   =>  'Nro. Remito',
			    "name"      =>  'nro_remito',
			    "editable"  =>  true,
			    "class"	=>'',
			    "value"     =>  $objTransaccion->getNroRemito(),

			),
			array(
			    "display"   =>  'Nro. Referencia',
			    "name"      =>  'referencia_nro',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objTransaccion->getNroReferencia(),

			),
			array(
			    "display"   =>  'Dispone Desc',
			    "name"      =>  'dispone_nombre',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objTransaccion->getDisponeNombre(),

			)
		    );
	}
	foreach($objTransaccion->getColObjDetalleTransaccion() AS $detalle){
    
	    $arr []   =    array
		(
		    //id si o si un solo string (sin espacios en blanco)
		    "id"            =>  $detalle->getTransaccionId(),
		    //define las herramientas
		    "herramientas"   =>  array(
			 "editable"      =>  true,
			  ),
			 "bloqueado"     => false,	    
		    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
		    "cell"          => array(
			$detalle->getTrazabilidadCodigo(),
			$detalle->getObjProducto()->getProductoNombre() ." ". $detalle->getObjProducto()->getProductoPresentacion(),
			$detalle->getProductoGtin(),
			$detalle->getLote(),
			$detalle->getLoteVencimiento()
		    ),
		);
	}

    
	$detallesTransaccion    =   array(
	    "bloqueable"=> false,
	    "modelo"    =>  array(
		array(

		    "display"   =>  'Codigo Unico',
		    "name"      =>  'trazabilidad_codigo',
		    "editable"  =>  true,
		    "class"	=>'',
		),
		array(
		    "display"   =>  'Producto',
		    "name"      =>  'producto_nombre',
		    "editable"  =>  false,
		    "class"	=>'',
		),
		array(
		    "display"   =>  'PRODUCTO GTIN',
		    "name"      =>  'producto_gtin',
		    "editable"  =>  true,
		    "class"	=>'',
		),
		array(
		    "display"   =>  'Lote',
		    "name"      =>  'lote',
		    "editable"  =>  true,
		    "class"	=>'',

		),
		array(
		    "display"   =>  'Lote Vence',
		    "name"      =>  'lote_vencimiento',
		    "editable"  =>  true,
		    "class"	=>'datePicker',
		) ,
		array(
		    "display"   =>  'Herr.',
		    "name"      =>  'herramienta',
		),

	    ),
	    "celdas"    =>  $arr
	);
	
	$array_devolver =   array(
	    "propiedades"       =>  $propiedadesTransaccion,
	    "listadoDetalles"   =>  $detallesTransaccion
	);
	    return $array_devolver;
    }
    public function actualizarme($arrParametros){

	$datoAutogestionExtentido = NEW DatoAutogestionExtendido();
	$datoAutogestionExtentido->actualizarme($arrParametros);
    }
    public function actualizarDetalles($arrParametros){

	$detalleAutogestionExtendido = NEW DetalleAutogestionExtendido();
	$detalleAutogestionExtendido->actualizarme($arrParametros);
    }
    public function salvarDato($objTransaccion){

	$datoTransaccionExtentido = NEW DatoTransaccionExtendido();
	$datoTransaccionExtentido->salvarme($objTransaccion);

    }
    public function salvarDetalle($objDetalleTransaccion){

	$detalleTransaccionExtentido = NEW DetalleTransaccionExtendido();
	$detalleTransaccionExtentido->salvarme($objDetalleTransaccion);

    }

    public function enviarTransaccion(){
/*
	$objTransaccionesANMAT = new TransaccionesANMAT();

	foreach($this->getColObjDatoTransaccion() AS $objTransaccion){

	    $objTransaccionesANMAT->preprarDatosEnvio($objTransaccion);

	}
*/
	if(count($resultado)== 0){
		$arrDevolver = array(
		    "soyError" => false,
		    "nivel" => 0,
		    "mensaje" =>"Transaccion Enviada"
		    );
	}else{

		$arrDevolver = array(
		    "soyError" => true,
		    "nivel" => 1,
		    "mensaje" =>"No se pudo enviar la transaccion"
		    );
	}
	echo json_encode($arrDevolver);
    }
    public function noAutogestionar($arrParametros){

	$datoTransaccionExtentido = NEW DatoTransaccionExtendido();
	$datoTransaccionExtentido->noAutogestionar($arrParametros);
    }
}
