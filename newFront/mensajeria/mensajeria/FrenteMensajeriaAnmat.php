<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'MensajeriaAnmatExtendido.php';


/**
 * Description of FrenteTransacciones
 *
 * @author by dMELMAC
 */

class FrenteMensajeriaAnmat
{
    private $_db;
    private $_colObjMensajeriaAnmat= Array();
        
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
    
    public function setColObjMensajeriaAnmat($obj){
        $this->_colObjMensajeriaAnmat[$obj->getIdMovimientoSc()] = $obj;
    }
    
    public function getColObjMensajeriaAnmat(){
        return $this->_colObjMensajeriaAnmat;
    }
    
    public function setArrError($arrError){
        $this->_arrError = $arrError;
    }
    
    public function getArrError(){
        return $this->_arrError ;
    }

    
    public function cargarMensajeriaCompleta($id){

	$this->_db->addSelect('mensajeria_id');
	$this->_db->addFrom('mensajeria_anmat');
	$this->_db->addWhere('id_movimiento_sc = \'' . $id . '\'');
	$this->_db->addWhere('id_evento = \'' . '111' . '\'');
	$this->_db->generarSelect();

	$resultado =  $this->_db->ejecutar();

        $this->cargarLosDatosLazzy($resultado);
	$this->cargarLosDetallesLazzy($resultado);
    }

    
    public function cargarMensajeriaCabecera($id){
    
        $objMensajeria= new MensajeriaAnmatExtendido();
        $objMensajeria->cargarme($id);
        
        $this->setColObjMensajeriaAnmat($objMensajeria);
    }

    public function cargarLosDatosLazzy($arrIds){
    
	if(is_array($arrIds)){
	    
	    foreach($arrIds as $id){
	    
		$ids.= $id['mensajeria_id'] . ",";
	    }
		$ids=substr($ids, 0, -1);
	}else{
		$ids=$arrIds;
	}
	$this->_db->addSelect('*,insertada_fecha_hora +  \'72 hours\' AS dispensa_hora_posta');
        $this->_db->addFrom('mensajeria_anmat');
        $this->_db->addWhere("mensajeria_id IN (" . $ids . ")" );
        $this->_db->addOrderBy('mensajeria_id DESC');
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        
        $FrenteGlnOrigen = NEW FrenteGln();
        $FrenteGlnOrigen->cargarGlnOrigenLazzy($resultado);
        $arrObjGlnOrigen= $FrenteGlnOrigen->getColObjGlnOrigen();
        
        $FrenteTipoMovimientoAnmat = NEW FrenteTipoMovimientoAnmat();
        $FrenteTipoMovimientoAnmat->cargarTipoMovimientoLazzy($resultado);
        $arrTipoMovimientoAnmat= $FrenteTipoMovimientoAnmat->getColObjTipoMovimientoAnmat();
        
        
        
        
        foreach($resultado AS $datos){

	    $objMensajeria= new MensajeriaAnmatExtendido();
	    $objMensajeria->cargarMe($datos);
	    $objMensajeria->setObjGlnOrigen($arrObjGlnOrigen[$datos[gln_origen]]);
	    $objMensajeria->setObjTipoMovimiento($arrTipoMovimientoAnmat[$datos[id_evento]]);

	    //var_dump($objMensajeria);exit;
	    $this->setColObjMensajeriaAnmat($objMensajeria);
	    

        }
    }
    public function cargarLosDetallesLazzy($arrIds){
    
	foreach($arrIds as $id){
	    $ids.= $id['mensajeria_id'] . ",";
	}
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
	$this->_db->addFrom('mensajeria_anmat');
        $this->_db->addWhere("mensajeria_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        $FrenteProductosAnmat = NEW FrenteProductosAnmat();
        $FrenteProductosAnmat->cargarProductosLazzy($resultado);
        $arrObjProductos= $FrenteProductosAnmat->getColProductos();

        foreach($this->_colObjMensajeriaAnmat AS $objMensajeria){
        
	    foreach($resultado AS $detalle){
	    
		$objDetalleMensajeria= NEW MensajeriaAnmatExtendido();
		$objDetalleMensajeria->cargarme($detalle);
		$objDetalleMensajeria->setObjProducto($arrObjProductos[$detalle[gtin]]);

		
		$objMensajeria->setColObjDetalleMensajeria($objDetalleMensajeria);
		
		
		$this->setColObjMensajeriaAnmat($objMensajeria);
		
		/*echo "<pre>";
		print_r($this->_colObjMensajeriaAnmat);
		echo "<pre>";exit;
		*/
		
	    }
	}
    
    }
    public function buscarMensajeria($arrParametros){

	$this->_db->addSelect('id_movimiento_sc');
	$this->_db->addFrom('mensajeria_anmat');


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
	    $this->_db->addWhere('numero_serial = \'' . $arrParametros[codigo] . '\'');
	}
	if($arrParametros['productos'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('gtin = \'' . $arrParametros['productos'] . '\'');
	}
	if($arrParametros['origenNombre'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('gln_origen = \'' . $arrParametros['origenNombre'] . '\'');
	}
	if($arrParametros['lote'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('lote_nro = \'' . $arrParametros[lote] . '\'');
	}
	if($arrParametros['valor'] !=null && $arrParametros[desdeAlta]==null){
            $this->_db->addWhere('trazabilidad_codigo = \'' . $arrParametros[valor] . '\'');
        }
	if($arrParametros['remitos'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('n_remito = \'' . $arrParametros[remitos] . '\'');
	}
	if($arrParametros['facturas'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('n_factura = \'' . $arrParametros[facturas] . '\'');
	}
	if($arrParametros['sucursalId'] !=null && $arrParametros[sucursalId]!=0){
	    $this->_db->addWhere('id_sucursal = \'' . $arrParametros[sucursalId] . '\'');
	}
	if($arrParametros['desdeBusqueda']==true && $arrParametros['bloqueadas']==2){
		$this->_db->addWhere('no_informar = \'' . 't' . '\'');
	}else{
	    $this->_db->addWhere('no_informar = \'' . 'f' . '\'');
	}
		
	$this->_db->addWhere('regristo_traspasado = false');
	$this->_db->addWhere('id_evento = \'' . '111' . '\'');
	//$this->_db->addWhere('fecha_hora_transmision_recepcion_fcia IS NOT NULL ');
	
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenMensajeria($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='bloqueador'){
	    $this->_db->addLimit(2200);
	}

	switch($arrParametros['ordenarPor']){

	    case "gln_origen":
		    $this->_db->addGroup('gln_origen,');
		    break;
	}

	$this->_db->addGroup('id_movimiento_sc');
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
	//echo $this->getTotal();
	if($this->getTotal() < $ultimoRegistro)	{
	    
	    $ultimoRegistro = $this->getTotal();
	}elseif($ultimoRegistro == -1){
	    $ultimoRegistro = 19;
	}
	
	for($i=$primerRegistro;$i<$ultimoRegistro;$i++)	{

	    	$this->_db->addSelect('mensajeria_id');
		$this->_db->addFrom('mensajeria_anmat');
		$this->_db->addWhere('id_movimiento_sc = \'' . $arr[$i][id_movimiento_sc] . '\'');
		$this->_db->addWhere('id_evento = \'' . '111' . '\'');
		$this->_db->addLimit(1);
		$this->_db->generarSelect();
		
		$arrMensajeria =  $this->_db->ejecutar();
		//echo $this->_db->getQry();
		
		$arrIds[$i]['mensajeria_id']= $arrMensajeria[0]['mensajeria_id'];
	}
	
	$this->cargarLosDatosLazzy($arrIds);
    }

    public function getError(){
        return $this->_arrError;
    }

    //**PARA EL FRENTE**//
    public function setOrdenMensajeria($parametros){
	if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){
	    $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'id_movimiento_sc';
	    $this->_ordenColumnas['ordenarOrden']  =   'desc';
	}
    }

    public function getOrdenMensajeria(){
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

	foreach($this->_colObjMensajeriaAnmat AS $objMensajeria){
	
	    if($_SESSION['usuarioRecepcion']=='t'){

		$mostrar = false;
	    }else{
		$mostrar = true;
	    }

		$arr []   =    array
		    (
			//id si o si un solo string (sin espacios en blanco)
			"id"            =>  $objMensajeria->getIdMovimientoSc(),
			//define las herramientas
			"herramientas"   =>  array(
			    "editable"      =>  false,
			    "cancelableBloqueador"    =>  false,
			    "detallesMensajeria"      =>  $mostrar,
			    "imprimir"      =>  false,
			    "autogestion"      =>  false,
			    "enviarTransaccion"      =>  false
			    
			),
			//las celdas propiamente dichas, en el orden en que estan declaradas las columnas
			"cell"          => array(
			    $objMensajeria->getFechaHoraTrasmision(),
			    $objMensajeria->getObjGlnOrigen()->getGlnNombre() . " " . $objMensajeria->getObjGlnOrigen()->getGln(),
			    $objMensajeria->getNroRemito() . $objMensajeria->getNroFactura(),
			    $objMensajeria->getReferenciaNro(),
			    $objMensajeria->getDispensaHora()
			),
		    );

	}
	return $arr;
    }

    public function armarDetalles($id){

	
	$this->cargarMensajeriaCompleta($id);
	
	foreach($this->_colObjMensajeriaAnmat AS $objMensajeriaCabecera){

		$propiedadesMensajeria =   array(
			array(
			    "display"   =>  'Fecha',
			    "name"      =>  'fecha_hora_transmision_recepcion_fcia',
			    "editable"  =>  false,
			    "value"     =>  $objMensajeriaCabecera->getDispensaHora(),

			),
			array(
			    "display"   =>  'Nombre Origen',
			    "name"      =>  'gln_origen',
			    "editable"  =>  false,
			    "value"     =>  $objMensajeriaCabecera->getObjGlnOrigen()->getGlnNombre(),

			),
			array(
			    "display"   =>  'Nro Factura',
			    "name"      =>  'nro_factura',
			    "editable"  =>  $arrParametros[edicion],
			    "value"     =>  $objMensajeriaCabecera->getNroFactura(),

			),
			array(
			    "display"   =>  'Nro. Remito',
			    "name"      =>  'nro_remito',
			    "editable"  =>  $arrParametros[edicion],
			    "value"     =>  $objMensajeriaCabecera->getNroRemito(),

			)
		    );
	}
	foreach($objMensajeriaCabecera->getColObjDetalleMensajeria() AS $detalle){


    
	    $arr []   =    array
		(
		    //id si o si un solo string (sin espacios en blanco)
		    "id"            =>  $detalle->getMensajeriaId(),
		    //define las herramientas
		     "herramientas"   =>  array(
			 "editable"      =>  false,
			  ),
			 "bloqueado"     => $detalle->getNoinformar(),	    
		    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
		    "cell"          => array(
			$detalle->getNroSerial(),
			$detalle->getObjProducto()->getProductoNombre() ." ". $detalle->getObjProducto()->getProductoPresentacion(),
			$detalle->getLote(),
			$detalle->getVencimiento()
		    ),
		);
	}

    
	$detallesMensajeria    =   array(
	      "modelo"    =>  array(
		array(

		    "display"   =>  'Codigo Unico',
		    "name"      =>  'trazabilidad_codigo',
		    "editable"  =>  false,
		),
		array(
		    "display"   =>  'Producto',
		    "name"      =>  'producto_nombre',
		    "editable"  =>  true,
		),
		array(
		    "display"   =>  'Lote',
		    "name"      =>  'lote',
		    "editable"  =>  $arrParametros[edicion],

		),
		array(
		    "display"   =>  'Lote Vence',
		    "name"      =>  'lote_vencimiento',
		    "editable"  =>  $arrParametros[edicion],
		) ,
		array(
		    "display"   =>  'Herr.',
		    "name"      =>  'herramienta',
		    "editable"  =>  false,
		),

	    ),
	    "celdas"    =>  $arr
	);

	$array_devolver =   array(
	    "propiedades"       =>  $propiedadesMensajeria,
	    "listadoDetalles"   =>  $detallesMensajeria
	);
	    return $array_devolver;
    }
    
    public function bloquearPorMovimiento($arrParametros){

	$objMensajeria = new MensajeriaAnmatExtendido();
	$objMensajeria->bloquearPorMovimiento($arrParametros);
    }
    public function bloquearDetalles($arrParametros){

	$objMensajeria = new MensajeriaAnmatExtendido();
	$objMensajeria->bloquearDetalles($arrParametros);
    }
}
