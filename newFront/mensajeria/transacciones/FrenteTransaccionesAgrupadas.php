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
 * Description of FrenteTransaccionesAgrupadas
 *
 * @author by dMELMAC
 */

class FrenteTransaccionesAgrupadas
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
        $this->_db->addWhere('autogestion = false');
        $this->_db->addWhere('codigo_transaccion IS NULL');
        
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


    public function buscarTransaccionAgrupada ($arrParametros){


	$this->_db->addSelect('transaccion_id,referencia_nro');
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
	    $this->_db->addWhere('producto_gtin = \'' . $arrParametros['nombreProducto'] . '\'');
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
	if($arrParametros['fechaDesde'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('fecha_hora_transaccion::date >= \'' . $arrParametros[fechaDesde] . '\'');
	}
	if($arrParametros['fechaHasta'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('fecha_hora_transaccion::date <= \'' . $arrParametros[fechaHasta] . '\'');
	}
	if($arrParametros['sucursalMensajeria'] !=null){
	    $this->_db->addWhere('codigo_sucursal = \'' . $arrParametros[sucursalMensajeria] . '\'');
	}
	
	if($arrParametros['desdeBusqueda']==true && $arrParametros['tipoMovimientoId']!=0){
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
	$this->setOrdenTransaccionesAgrupadas($arrParametros);
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


	//$this->_db->addGroup('referencia_nro');
	//*FIN LIMIT*//
	
	$this->_db->generarSelect();

	//echo $this->_db->getQry();exit;
	$resultado =  $this->_db->ejecutar();
	
	if(count($resultado)==0){
	
	    return;
	}
	
	$antReferenciaNro = NULL;
	$indice = 0;
	for($i=0;$i< count($resultado); ++$i) {
	
	    if($resultado[$i]['referencia_nro'] != $antReferenciaNro){
		    
		    $this->_db->addSelect('transaccion_id,referencia_nro,now()');
		    $this->_db->addFrom('datos_transacciones_anmat');
		    $this->_db->addWhere('referencia_nro = \'' . $resultado[$i]['referencia_nro'] . '\'');
		    $this->_db->addWhere('error_id > 0');
		    $this->_db->addWhere('autogestion = false');
		    $this->_db->generarSelect();

		    //echo $this->_db->getQry();
		    
		    $resultadoError =  $this->_db->ejecutar();
		    if(count($resultadoError)>0){
			//echo "entra";
			$arrData[$indice]['transaccion_id'] = $resultadoError[0]['transaccion_id'];
		    }else{
			$arrData[$indice]['transaccion_id'] = $resultado[$i]['transaccion_id'];
		    }
		    $indice++;
	    }
	    $antReferenciaNro = $resultado[$i]['referencia_nro'];
	}

	//ahora seteamos el total y la pagina
	$this->setTotal(count($arrData));
	$this->setPagina($arrParametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro)	{
	    $ultimoRegistro = $this->getTotal();
	}
	
	for($i=$primerRegistro;$i<$ultimoRegistro;$i++)	{     
	    $arrIds[$i]['transaccion_id']= $arrData[$i]['transaccion_id'];
	}
	//var_dump($arrIds);exit;
	$this->cargarLosDatosLazzy($arrIds);
    }

    public function getError(){
        return $this->_arrError;
    }

    //**PARA EL FRENTE**//
    public function setOrdenTransaccionesAgrupadas($parametros){
	if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){
	    $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'transaccion_id';
	    $this->_ordenColumnas['ordenarOrden']  =   'DESC';
	}
    }

    public function getOrdenTransaccionesAgrupadas(){
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
	//echo $objTransaccion->getTransaccionId();
	    if($_SESSION['usuarioRecepcion']=='t'){

		$enviar = false;
	    }elseif($objTransaccion->getErrorId()> 0){
		    $enviar=true;
		}else{
		    $enviar=false;
	    };
		$arr []   =    array
		    (
			//id si o si un solo string (sin espacios en blanco)
			"id"            =>  $objTransaccion->getNroReferencia(),
			//define las herramientas
			"herramientas"   =>  array(
			    "editable"      =>  false,
			    "cancelable"    =>  $enviar,
			    "detalles"      =>  true,
			    "imprimir"      =>  false,
			    "autogestion"      =>  $enviar,
			    "enviarTransaccion"      =>  $enviar
			    
			),
			//las celdas propiamente dichas, en el orden en que estan declaradas las columnas
			"cell"          => array(
			    $objTransaccion->getNroReferencia(),
			    $objTransaccion->getCodigoTransaccion(),
			    $objTransaccion->getObjGlnOrigen()->getGlnNombre() . " " . $objTransaccion->getObjGlnOrigen()->getGln(),
			    $objTransaccion->getObjGlnDestino()->getGlnNombre() . " " . $objTransaccion->getObjGlnDestino()->getGln(),
			    $objTransaccion->getObjTipoMovimiento()->getTipoMovimientoNombre() . " " . $objTransaccion->getObjTipoMovimiento()->getTipoMovimientoDe(),
			    $objTransaccion->getNroRemito() . $objTransaccion->getNroFactura(),
			    $objTransaccion->getObjErrorAnmat()->getErrorDesc()
			),
		    );

	}
	return $arr;
    }

    public function armarDetallesAgrupados($referenciaNro){

	$this->cargarTransaccionCompleta($referenciaNro);
	
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
			    "editable"  =>  $arrParametros[edicion],
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
			    "editable"  =>  $arrParametros[edicion],
			    "value"     =>  $objTransaccion->getGlnDestino(),

			),
			array(
			    "display"   =>  'Tipo Movimiento',
			    "name"      =>  'tipo_movimiento_id',
			    "class"	=>'autocomplete',
			    "editable"  =>  $arrParametros[edicion],
			    "value"     =>  $objTransaccion->getObjTipoMovimiento()->getTipoMovimientoNombre() . " " . $objTransaccion->getObjTipoMovimiento()->getTipoMovimientoDe(),
			),
			array(
			    "display"   =>  'Nro Factura',
			    "name"      =>  'nro_factura',
			    "editable"  =>  $arrParametros[edicion],
			    "class"	=>'',
			    "value"     =>  $objTransaccion->getNroFactura(),

			),
			array(
			    "display"   =>  'Nro. Remito',
			    "name"      =>  'nro_remito',
			    "editable"  =>  $arrParametros[edicion],
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
			    "editable"  =>  $arrParametros[edicion],
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
			$detalle->getCodigoTransaccion(),
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

		    "display"   =>  'Codigo Transaccion',
		    "name"      =>  'trazabilidad_codigo',
		    "editable"  =>  false,
		    "class"	=>'',
		),array(

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
	    "editable_cabecera" => false,
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

    public function enviarTransaccion($referenciaNro){

    
	$this->setCabeceraCargada(0);
	$FrenteAutogesionAgrupada = NEW FrenteAutogestionAgrupada();
	$FrenteAutogesionAgrupada->cargarAutogestionCompletaParaEnvio($referenciaNro);
	
        foreach($FrenteAutogesionAgrupada->getColObjDatoTransaccion() AS $objDatoAutogestion){
	    $this->salvarDato($objDatoAutogestion);
	    
	    foreach($objDatoAutogestion->getColObjDetalleTransaccion() AS $objDetalleAutogestion){
		
		$objDetalleAutogestion->setTransaccionId($objDatoAutogestion->getTransaccionId());
		$this->salvarDetalle($objDetalleAutogestion);
	    }    
        }
        //exit;
	foreach($FrenteAutogesionAgrupada->getColObjDatoTransaccion() AS $objDatoAutogestionAgrupada){
		if($this->getCabeceraCargada() == 0 ){
		    $this->cargarTransaccionCompletaParaEnvio($objDatoAutogestionAgrupada->getNroReferencia());
		    $this->setCabeceraCargada(1);
		}
		
	}
	
	$objTransaccionesANMAT = new TransaccionesANMAT();
	$cantErrores=0;
	foreach($this->getColObjDatoTransaccion() AS $objTransaccion){

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
    public function noAutogestionar($arrParametros){

	$datoTransaccionExtentido = NEW DatoTransaccionExtendido();
	$datoTransaccionExtentido->noAutogestionar($arrParametros);
    }
}
