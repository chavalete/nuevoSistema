<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'DatoAutogestionExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mensajeria/dmelmac/TransaccionesANMAT.php';

/*
 * dMELMAC 
 * chava
 */

/**
 * Description of FrenteAutogestion
 *
 * @author by dMELMAC
 */

class FrenteAutogestion
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
        $this->_colObjDatoTransaccion[] = $obj;
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

    
    public function cargarAutogestionCompleta($id){

        $objTransaccion= new DatoAutogestionExtendido();
        $objTransaccion->cargarme($id);
        $this->setColObjDatoTransaccion($objTransaccion);

	$this->_db->addSelect('transaccion_detalle_id');
	$this->_db->addFrom('detalle_transacciones_anmat');
        $this->_db->addWhere('transaccion_id = ' . $id );
       
	
        $this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();

	foreach($resultado AS $detalles){

	    $objDetalleTransaccion= new DetalleAutogestionExtendido();
	    $objDetalleTransaccion->cargarme($detalles['transaccion_detalle_id']);  
	    $objTransaccion->setColObjDetalleTransaccion($objDetalleTransaccion);
	}
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
