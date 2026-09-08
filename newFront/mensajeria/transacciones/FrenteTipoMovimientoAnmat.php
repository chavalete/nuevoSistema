<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'TipoMovimientoAnmatExtendido.php';

/*
 * dMELMAC 
 * 
 */

/**
 * Description of FrenteTransacciones
 *
 * @author by dMELMAC
 */

class FrenteTipoMovimientoAnmat
{
    private $_db;
    private $_colObjTipoMovimientosAnmat= Array();
        
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
    
    public function setColObjTipoMovimientoAnmat($obj){
        $this->_colObjTipoMovimientosAnmat[$obj->getTipoMovimientoId()] = $obj;
    }
    
    public function getColObjTipoMovimientoAnmat(){
        return $this->_colObjTipoMovimientosAnmat;
    }
    
    public function setArrError($arrError){
        $this->_arrError = $arrError;
    }
    
    public function getArrError(){
        return $this->_arrError ;
    }
    
    public function cargarTipoMovimientoAnmat($id){
        $objTipoMovimiento= new tipoMovimientoAnmatExtendido();
        $objTipoMovimiento->cargarme($id);
        $this->setColObjTipoMovimientoAnmat($objTipoMovimiento);
    }
    public function cargarTipoMovimientoLazzy($arrIds){
    
	foreach($arrIds as $id){
	    $ids.= "'" . $id['id_evento'] . "',";
	}
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
        $this->_db->addFrom('tipo_movimientos_anmat');
        $this->_db->addWhere("movimiento_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
	foreach($resultado AS $detalle){
	
	    $objTipoMovimientoAnmat = NEW TipoMovimientoAnmatExtendido();
	    $objTipoMovimientoAnmat->cargarme($detalle);
	    $this->setColObjTipoMovimientoAnmat($objTipoMovimientoAnmat);
	}
    }
    
    public function buscarTipoMovimientoAnmat ($arrParametros){


	$this->_db->addSelect('movimiento_id');
	$this->_db->addFrom('tipo_movimientos_anmat');
	
	//**ANALIZO EL WHERE**//
	if($arrParametros['stringBuscar']){

	    $this->_db->addWhere('movimiento_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 16;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
	}

	//**where faso = true**/


	if($arrParametros['tipoMovimiento'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('fecha_hora_transaccion::date <= \'' . $this->_objFuncionesComunes->parsearAutocompletar($arrParametros['tipoMovimiento']) . '\'');
	}
    
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenTipoMovimeintoAnmat($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='transacciones')
	{
	    $this->_db->addLimit(100);
	}
	$this->_db->addGroup('movimiento_id');
	//*FIN LIMIT*//
	
	$this->_db->generarSelect();
	$arr =  $this->_db->ejecutar();
	
	//echo $this->_db->getQry();
	//ahora seteamos el total y la pagina
	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro)
	{
	    $ultimoRegistro = $this->getTotal();
	}
	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
	    $arrIds[$i]['id_evento']=$arr[$i]['movimiento_id'];
	}
	$this->cargarTipoMovimientoLazzy($arrIds);
    }

    public function getError(){
        return $this->_arrError;
    }

    //**PARA EL FRENTE**//
    public function setOrdenTipoMovimeintoAnmat($parametros)
    {
	if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != "")
	{
	    $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
	}else
	{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'movimiento_id';
	    $this->_ordenColumnas['ordenarOrden']  =   'desc';
	}
    }

    public function getOrdenTipoMovimientoAnmat()
    {
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


		$arr []   =    array
		    (
			//id si o si un solo string (sin espacios en blanco)
			"id"            =>  $objTransaccion->getTransaccionId(),
			//define las herramientas
			"herramientas"   =>  array(
			    "editable"      =>  false,
			    "cancelable"    =>  true,
			    "detallesTransacciones"      =>  true,
			    "imprimir"      =>  false,
			    "autogestion"      =>  true,
			    "enviarTransaccion"      =>  true
			    
			),
			//las celdas propiamente dichas, en el orden en que estan declaradas las columnas
			"cell"          => array(
			    $objTransaccion->getCodigoTransaccion(),
			    $objTransaccion->getFechaHoraTransaccion(),
			    $objTransaccion->getGlnOrigen(),
			    $objTransaccion->getGlnDestino(),
			    $objTransaccion->getObjTipoMovimiento()->getTipoMovimientoNombre() . " " . $objTransaccion->getObjTipoMovimiento()->getTipoMovimientoDe(),
			    $objTransaccion->getNroRemito() . $objTransaccion->getNroFactura(),
			    $objTransaccion->getErrorDesc()
			),
		    );

	}
	return $arr;
    }
    public function getDetallesAutocompletar()
    {
	foreach($this->_colObjTipoMovimientosAnmat AS $tipoMovimiento)
	{
	 $arr[]   =   array(
            'label' =>  $tipoMovimiento->getTipoMovimientoNombre() . " " . "(" . $tipoMovimiento->getTipoMovimientoDe() . ")",
            'value' => $tipoMovimiento->getTipoMovimientoId()
	    );
	}		
	return $arr;
    }
}