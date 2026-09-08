<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'DatoTransaccionExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mensajeria/dmelmac/TransaccionesANMAT.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/FrenteProductosAnmat.php';

/*
 * dMELMAC 
 * chava
 */

/**
 * Description of FrenteTransacciones
 *
 * @author by dMELMAC
 */

class FrenteTransacciones
{
    private $_db;
    private $_colObjDatoTransaccion= Array();
        
    private $_arrError = Array();

    //** NECESARIOS PARA EL FRENTE DE FRENTES//
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;

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
    
    public function setArrError($arrError){
        $this->_arrError = $arrError;
    }
    
    public function getArrError(){
        return $this->_arrError ;
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
	$this->_db->addSelect('*');
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
        $FrenteTipoMovimientoAnmat = NEW FrenteTipoMovimientoAnmat();
        $FrenteTipoMovimientoAnmat->cargarTipoMovimientoLazzy($resultado);
        $arrTipoMovimientoAnmat= $FrenteTipoMovimientoAnmat->getColObjTipoMovimientoAnmat();
        $FrenteErrorAnmatExtendido = NEW FrenteErrorAnmatExtendido();
        $FrenteErrorAnmatExtendido->cargarErrorAnmatLazzy($resultado);
        $arrErrorAnmatExtendido = $FrenteErrorAnmatExtendido->getColObjErrorAnmat();
        
        foreach($resultado AS $datos){
       	    //echo $datos[error_id];exit;
	    $objDatosTransaccion= NEW DatoTransaccionExtendido();
	    $objDatosTransaccion->cargarme($datos);
	    $objDatosTransaccion->setObjGlnOrigen($arrObjGlnOrigen[$datos[gln_origen]]);
	    $objDatosTransaccion->setObjGlnDestino($arrObjGlnDestino[$datos[gln_destino]]);
	    $objDatosTransaccion->setObjTipoMovimiento($arrTipoMovimientoAnmat[$datos[id_evento]]);
	    $objDatosTransaccion->setObjErrorAnmat($arrErrorAnmatExtendido[$datos[error_id]]);
	    //var_dump($objDatosTransaccion);exit;
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
    public function cargarTransaccionCompleta($id){

        $this->_db->addSelect('transaccion_id, transaccion_detalle_id');
	$this->_db->addFrom('datos_transacciones_anmat INNER JOIN detalle_transacciones_anmat USING(transaccion_id)');
        $this->_db->addWhere('transaccion_id = \'' . $id . '\'');
        $this->_db->addWhere('autogestion = false');
        
        $this->_db->generarSelect();
        $resultadoEnvio = $this->_db->ejecutar();
        
        $this->cargarLosDatosLazzy($resultadoEnvio);
	$this->cargarLosDetallesLazzy($resultadoEnvio);
    }

    public function cargarTransaccionCabecera($id){

        $objTransaccion= new DatoTransaccionExtendido();
        $objTransaccion->cargarme($id);
        $this->setColObjDatoTransaccion($objTransaccion);
    }

    public function buscarTransaccion ($arrParametros){


	$this->_db->addSelect('transaccion_id');
	$this->_db->addFrom('datos_transacciones_anmat');
	$this->_db->addFrom('INNER JOIN detalle_transacciones_anmat USING(transaccion_id)');


	//**ANALIZO EL WHERE**//
	if($arrParametros['stringBuscar']){

	    //$this->_db->addWhere('id_movimiento ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
	}

	//**where faso = true**/
	if($arrParametros['origen'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('gln_origen = \'' . $arrParametros[origen] . '\'');
	}
	if($arrParametros['nroReferencia'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('referencia_nro= \'' . $arrParametros[nroReferencia] . '\'');
	}
	if($arrParametros['codigo'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('trazabilidad_codigo = \'' . $arrParametros[codigo] . '\'');
	}
	if($arrParametros['nombreProducto'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('producto_id = \'' . $arrParametros['nombreProducto'] . '\'');
	}
	if($arrParametros['origenNombre'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('gln_origen = \'' . $arrParametros['origenNombre'] . '\'');
	}
	if($arrParametros['destinoNombre'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('gln_destino = \'' . $arrParametros['destinoNombre'] . '\'');
	}
	if($arrParametros['tipoMovimientoMensajeria'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('id_evento = \'' . $arrParametros['tipoMovimientoMensajeria'] . '\'');
	}
	if($arrParametros['destino'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('gln_destino = \'' . $arrParametros[destino] . '\'');
	}
	if($arrParametros['lote'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('lote_nro = \'' . $arrParametros[lote] . '\'');
	}
	if($arrParametros['valor'] !=null && $arrParametros[desdeAlta]==null){
            $this->_db->addWhere('trazabilidad_codigo = \'' . $arrParametros[valor] . '\'');
        }
	if($arrParametros['remito'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('nro_remito = \'' . $arrParametros[remito] . '\'');
	}
	if($arrParametros['facturas'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('nro_factura = \'' . $arrParametros[facturas] . '\'');
	}
	if($arrParametros['sucursalMensajeria'] !=null){
	    $this->_db->addWhere('codigo_sucursal = \'' . $arrParametros[sucursalMensajeria] . '\'');
	}
	if($arrParametros['fechaDesde'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('fecha_hora_transaccion::date >= \'' . $arrParametros[fechaDesde] . '\'');
	}
	if($arrParametros['fechaHasta'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('fecha_hora_transaccion::date <= \'' . $arrParametros[fechaHasta] . '\'');
	}
	
	
	if($arrParametros['tipoMovimientoId']!=0){
	    if($arrParametros['tipoMovimientoId']==1){
		$this->_db->addWhere('error_id > 0');
		$this->_db->addWhere('no_requiere_autogestion = \'' . 'f' . '\'');
		$this->_db->addWhere('autogestion = \'' . 'f' . '\'');
	    }elseif($arrParametros['tipoMovimientoId']==2){
		$this->_db->addWhere('codigo_transaccion > 0');
		$this->_db->addWhere('no_requiere_autogestion = \'' . 'f' . '\'');
		$this->_db->addWhere('autogestion = \'' . 'f' . '\'');
	    }else{
		$this->_db->addWhere('no_requiere_autogestion = \'' . 't' . '\'');
	    }
	}else{
	    $this->_db->addWhere('autogestion = \'' . 'f' . '\'');
	    $this->_db->addWhere('no_requiere_autogestion = \'' . 'f' . '\'');
	}
	
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenTransacciones($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['desdeBusqueda']==false){
	    $this->_db->addLimit(500);
	}

	switch($arrParametros['ordenarPor']){

	    case "gln_origen":
		    $this->_db->addGroup('gln_origen,');
		    break;
	    case "gln_destino":
		    $this->_db->addGroup('gln_destino,');
		    break;
	    case "tipo_movimiento_id":
		    $this->_db->addGroup('tipo_movimiento_id,');
		    break;
	    case "error_id_anmat":
		    $this->_db->addFrom('INNER JOIN relacion_error_id_error_anmat USING (error_id)');
		    $this->_db->addGroup('error_id_anmat,');
		    break;
	    case "codigo_transaccion":
		    $this->_db->addGroup('codigo_transaccion,');
		    break;
	}


	$this->_db->addGroup('transaccion_id');
	//*FIN LIMIT*//
	
	$this->_db->generarSelect();

	//echo $this->_db->getQry();
	$arr =  $this->_db->ejecutar();
	
	if(count($arr)==0){
	
	    return;
	}
	//ahora seteamos el total y la pagina
	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro)	{
	    $ultimoRegistro = $this->getTotal();
	}
	for($i=$primerRegistro;$i<$ultimoRegistro;$i++)	{     
	    $arrIds[$i]['transaccion_id']= $arr[$i]['transaccion_id'];
	}
	$this->cargarLosDatosLazzy($arrIds);
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

    public function getDetalles(){

    
	foreach($this->_colObjDatoTransaccion AS $objTransaccion){


		if($objTransaccion->getErrorId()> 0) {
		    $enviar=true;
		    $id = $objTransaccion->getTransaccionId();
		}else{
		    $enviar=false;
		    $id = $objTransaccion->getTransaccionId();
		}

		$arr []   =    array
		    (
			//id si o si un solo string (sin espacios en blanco)
			"id"            =>  $id,
			//define las herramientas
			"herramientas"   =>  array(
			    "editable"      =>  false,
			    "cancelable"    =>  true,
			    "detalles"      =>  true,
			    "imprimir"      =>  false,
			    "autogestion"      =>  false,
			    "enviarTransaccion"      =>  false
			    
			),
			//las celdas propiamente dichas, en el orden en que estan declaradas las columnas
			"cell"          => array(
			    $objTransaccion->getCodigoTransaccion(),
			    $objTransaccion->getFechaHoraTransaccion(),
			    $objTransaccion->getObjGlnOrigen()->getGlnNombre() . " " . $objTransaccion->getObjGlnOrigen()->getGln(),
			    $objTransaccion->getObjGlnDestino()->getGlnNombre() . " " . $objTransaccion->getObjGlnDestino()->getGln(),
			    $objTransaccion->getObjTipoMovimiento()->getTipoMovimientoNombre() . " " . $objTransaccion->getObjTipoMovimiento()->getTipoMovimientoDe(),
			    $objTransaccion->getNroRemito() . $objTransaccion->getNroFactura(),
			    $objTransaccion->getNroReferencia(),
			    $objTransaccion->getObjErrorAnmat()->getErrorDesc()
			),
		    );

	}
	return $arr;
    }

    public function armarDetalles($id){

	$this->cargarTransaccionCompleta($id);

	foreach($this->_colObjDatoTransaccion AS $objTransaccion){
		$propiedadesTransaccion =   array(
			
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
			    "class"	=>'',
			    "value"     =>  $objTransaccion->getObjGlnOrigen()->getGlnNombre(),

			),
			array(
			    "display"   =>  'GLN Origen',
			    "name"      =>  'gln_origen',
			    "class"	=>'',
			    "editable"  =>  false,
			    "value"     =>  $objTransaccion->getGlnOrigen(),

			),
			array(
			    "display"   =>  'Nombre Destino',
			    "name"      =>  'gln_destino',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objTransaccion->getObjGlnDestino()->getGlnNombre(),

			),
			array(
			    "display"   =>  'Destino',
			    "name"      =>  'gln_destino',
			    "class"	=>'',
			    "editable"  =>  false,
			    "value"     =>  $objTransaccion->getGlnDestino(),

			),
			array(
			    "display"   =>  'Tipo Movimiento',
			    "name"      =>  'tipo_movimiento_id',
			    "class"	=>'autocomplete',
			    "editable"  =>  false,
			    "value"     =>  $objTransaccion->getObjTipoMovimiento()->getTipoMovimientoNombre() . " " . $objTransaccion->getObjTipoMovimiento()->getTipoMovimientoDe(),
			),
			array(
			    "display"   =>  'Nro Factura',
			    "name"      =>  'nro_factura',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objTransaccion->getNroFactura(),

			),
			array(
			    "display"   =>  'Nro. Remito',
			    "name"      =>  'nro_remito',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objTransaccion->getNroRemito(),

			),
			array(
			    "display"   =>  'Nro. Referencia',
			    "name"      =>  'referencia_nro',
			    "editable"  =>  $arrParametros[edicion],
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
		    "id"            =>  $detalle->getDetalleTransaccionId(),
		    //define las herramientas
		    "herramientas"   =>  array(
			 "editable"      =>  false,
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
		    "editable"  =>  $arrParametros[edicion],
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
		    "editable"  =>  $arrParametros[edicion],
		    "class"	=>'',
		),
		array(
		    "display"   =>  'Lote',
		    "name"      =>  'lote',
		    "editable"  =>  $arrParametros[edicion],
		    "class"	=>'',

		),
		array(
		    "display"   =>  'Lote Vence',
		    "name"      =>  'lote_vencimiento',
		    "editable"  =>  $arrParametros[edicion],
		    "class"	=>'datePicker',
		) ,
	    ),
	    "celdas"    =>  $arr
	);
	
	$array_devolver =   array(
	    "propiedades"       =>  $propiedadesTransaccion,
	    "listadoDetalles"   =>  $detallesTransaccion
	);
	    return $array_devolver;
    }
    public function actualizarMe($arrParametros){

	$datoTransaccionExtentido = NEW DatoTransaccionExtendido();
	$datoTransaccionExtentido->actualizarMe($arrParametros);
    }
    public function actualizarDetalles($arrParametros){

	$detalleTransaccionExtentido = NEW DetalleTransaccionExtendido();
	$detalleTransaccionExtentido->actualizarMe($arrParametros);
    }
    public function salvarDato($objTransaccion){

	$datoTransaccionExtentido = NEW DatoTransaccionExtendido();
	$datoTransaccionExtentido->salvarme($objTransaccion);

    }
    public function salvarDetalle($objDetalleTransaccion){

	$detalleTransaccionExtentido = NEW DetalleTransaccionExtendido();
	$detalleTransaccionExtentido->salvarme($objDetalleTransaccion);

    }

    public function enviarTransaccion($id){

	$FrenteAutogesion = NEW FrenteAutogestion();
	$FrenteAutogesion->cargarAutogestionCompleta($id);
	
        foreach($FrenteAutogesion->getColObjDatoTransaccion() AS $objDatoAutogestion){
        
	    $this->salvarDato($objDatoAutogestion);
	    
	    foreach($objDatoAutogestion->getColObjDetalleTransaccion() AS $objDetalleAutogestion){
		
		$objDetalleAutogestion->setTransaccionId($objDatoAutogestion->getTransaccionId());
		$this->salvarDetalle($objDetalleAutogestion);
	    }
		    
        }   
	$objTransaccionesANMAT = new TransaccionesANMAT();
	foreach($FrenteAutogesion->getColObjDatoTransaccion() AS $objDatoAutogestion){
		
		$this->cargarTransaccionCompleta($objDatoAutogestion->getTransaccionId());
	}
	foreach($this->getColObjDatoTransaccion() AS $objTransaccion){

	    $objTransaccionesANMAT->preprarDatosEnvio($objTransaccion);

	}

	if(count($resultado) > 0){
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
    public function noAutogestionar($id){

	$datoTransaccionExtentido = NEW DatoTransaccionExtendido();
	$datoTransaccionExtentido->noAutogestionar($id);
    }
}
