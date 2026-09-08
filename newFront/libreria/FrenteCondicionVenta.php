<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/CondicionVentaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteCondicionVenta
{
    private $_colCondicionVenta = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    public function __construct(){

	$this->_objFuncionesComunes = new FuncionesComunes();
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
    }
    public function cargarCondicionVenta($id){

	$this->_colCondicionVenta[$id] = NEW CondicionVentaExtendido();
	$this->_colCondicionVenta[$id]->cargarMe($id);
    }
    public function setColObjCondicionVenta($objCondicionVenta){
    
	$this->_colCondicionVenta[$objCondicionVenta->getCondicionVentaId()] = $objCondicionVenta;
    }
    public function getColObjCondicionVenta(){
	return $this->_colCondicionVenta;
    }
    public function cargarCondicionLazzy($arrIds){
    
	foreach($arrIds as $id){
	    if($id['condicion_venta_id']!=NULL){
		$ids.= "'" . $id['condicion_venta_id'] . "',";
	    }
	}
	
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
	$this->_db->addSelect('condicion_venta_activa AS condicion_venta_activo');
	$this->_db->addSelect('\'Activo\'  AS condicion_venta_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS condicion_venta_no_activo_desc');
	$this->_db->addSelect('CASE WHEN condicion_venta_activa THEN \'Activo\' ELSE \'Inactivo\' END AS condicion_venta_activo_mensaje');
        $this->_db->addFrom('datos_condicion_venta');
        $this->_db->addWhere("condicion_venta_id IN (" . $ids . ")" );
	$this->_db->generarSelect();

	//echo $this->_db->getQry();
	
        $resultado = $this->_db->ejecutar();
        
        
        $creados=0;
        
	if(count($resultado)>0){
	    foreach($resultado AS $detalle){
		$objCondicionVenta = NEW CondicionVentaExtendido();
		$objCondicionVenta->cargarMe($detalle);
		$this->setColObjCondicionVenta($objCondicionVenta);
		$creados++;
	    }
	}
	if($creados<count($arrIds)){
	    for($i=$creados;$i<count($arrIds);$i++){
		$objCondicionVenta = NEW CondicionVentaExtendido();
		$this->setColObjCondicionVenta($objCondicionVenta);
	    }
	}
	
    }
    public function buscarCondicionVenta($arrParametros){

	

	$this->_db->addSelect('condicion_venta_id');
	
	$this->_db->addFrom('datos_condicion_venta');

	//**ANALIZO EL WHERE**//

	if($arrParametros['stringBuscar']){
	    $this->_db->addWhere('condicion_venta_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
	}
	if($arrParametros['condicion_ventas'] !=null && $arrParametros['desdeAlta']==null){
	    $this->_db->addWhere('condicion_venta_id = \'' . $arrParametros['condicion_ventas'] . '\'');
	}

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenCondicionVentas($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='condicion_ventas'){

	    $this->_db->addLimit(100);
	}
	//*FIN LIMIT*//
	$this->_db->generarSelect();
	//echo $this->_db->getQry();
	$arr =  $this->_db->ejecutar();
	
	//ahora seteamos el total y la pagina
	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro){
	    
	    $ultimoRegistro = $this->getTotal();
	}	

	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
	    $arrIds[$i]['condicion_venta_id'] = $arr[$i]['condicion_venta_id'];
	}
	$this->cargarCondicionLazzy($arrIds);
    }
    public function setOrdenCondicionVentas($arrParametros){

	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'condicion_venta_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenCondicionVentas(){

	return $this->_ordenColumnas;
    }

    public function setTotal($total){

	$this->_total = $total;
    }
    public function getTotal(){

	return $this->_total;
    }
    //pagina 1 por defecto amigo
    public function setPagina($pagina=1){

	$this->_pagina = $pagina;
    }
    public function getPagina(){

	return $this->_pagina;
    }

    public function getDetalles(){

	foreach($this->_colCondicionVentas AS $condicion_venta){

	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $condicion_venta->getCondicionVentaId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $condicion_venta->getCondicionVentaNombre(),
		    $condicion_venta->getObs(),
                    Array
			(
			"value" => $condicion_venta->getCondicionVentaActiva(),
			"label" =>$condicion_venta->getCondicionVentaActivoMensaje(),
			"select" => 
			    Array 
			    (
				Array(
				"value" =>true,
				"label" =>$condicion_venta->getCondicionVentaActivoDesc()

				),
				Array

				(
				"value" =>false,
				"label" =>$condicion_venta->getCondicionVentaNoActivoDesc()

				)		
			    )
			)
                ),
            );
	}		
	return $arr;
    }
    public function getDetallesAutocompletar(){

	foreach($this->_colCondicionVenta AS $condicion_venta){
	 $arr[]   =   array(
            'label' =>  $condicion_venta->getCondicionVentaNombre(),
            'value' => $condicion_venta->getCondicionVentaId()
            );
	}		
	return $arr;
    }
    public function salvarMe($arrParametros){
	$condicion_ventaExtendido = NEW CondicionVentaExtendido();
	$condicion_ventaExtendido->salvarMe($arrParametros);
    }
    public function actualizarMe($arrParametros){
	$condicion_ventaExtendido = NEW CondicionVentaExtendido();
	$condicion_ventaExtendido->actualizarMe($arrParametros);
    }
}
?>