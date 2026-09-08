<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/TipoProductoExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteTipoProductos
{
    private $_colTipoProductos = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    private $_db;

    public function __construct(){

	$this->_objFuncionesComunes = NEW FuncionesComunes();
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
    }
    public function cargarTipoProductos($id){
	$this->_colTipoProductos[$id] = NEW TipoProductosExtendido();
	$this->_colTipoProductos[$id]->cargarMe($id);
    }
    public function setColObjTipoProductos($objTipoProductos){
	$this->_colTipoProductos[$objTipoProductos->getTipoProductoId()] = $objTipoProductos;
    }
    public function getColObjTipoProductos(){
	return $this->_colTipoProductos;
    }
    public function cargarTipoProductosLazzy($arrIds){
    
	foreach($arrIds as $id){
	    if($id['tipo_producto_id']!=NULL){
		$ids.= "'" . $id['tipo_producto_id'] . "',";
	    }else{
		$ids.= "'" . $id['tipo_producto_id'] . "',";
	    }
	}
	$ids=substr($ids, 0, -1);
	
        
        $this->_db->addSelect('*');
	
        
        $this->_db->addFrom('datos_tipos_productos');
       
        $this->_db->addWhere("tipo_producto_id IN (" . $ids . ")" );
	
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        
	foreach($resultado AS $detalle){
	    
	    $objTipoProductos = NEW TipoProductoExtendido();
	    $objTipoProductos->cargarme($detalle);
	    $this->setColObjTipoProductos($objTipoProductos);
	}
    }
    
    
    public function buscarTipoProductos($arrParametros){
	$db_dM = NEW FrenteAlmacenamiento('dmelmac');

	$db_dM->addSelect('tipo_producto_id');
	
	$db_dM->addFrom('datos_tipos_productos');

	//**ANALIZO EL WHERE**//

	if($arrParametros['stringBuscar']){
	    $db_dM->addWhere('tipo_producto_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
	}
	if($arrParametros['tipoProductos'] !=null && $arrParametros['desdeAlta']==null){
	    $db_dM->addWhere('tipo_producto_id = \'' . $arrParametros['tipoProductos'] . '\'');
	}

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenTipoProductos($arrParametros);
	$db_dM->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='tipoProductos'){
	    $db_dM->addLimit(100);
	}
	//*FIN LIMIT*//
	$db_dM->generarSelect();

	$arr =  $db_dM->ejecutar();
	
	//ahora seteamos el total y la pagina
	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro){
	    $ultimoRegistro = $this->getTotal();
	}	

	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
	    $arrIds[$i]['tipo_producto_id'] = $arr[$i]['tipo_producto_id'];
	}
        //var_dump($arrIds);
	$this->cargarTipoProductosLazzy($arrIds);
    }
    public function setOrdenTipoProductos($arrParametros){
	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){
	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'tipo_producto_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenTipoProductos(){
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
	foreach($this->_colTipoProductos AS  $tipoProductos){
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $tipoProductos->getTipoProductoId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $sucursal->getTipoProductoNombre(),
                ),
            );
	}		
	return $arr;
    }
    public function getDetallesAutocompletar(){
	foreach($this->_colTipoProductos AS $tipoProductos){
	 $arr[]   =   array(
            'label' =>  $tipoProductos->getTipoProductoNombre(),
            'value' =>  $tipoProductos->getTipoProductoId()
            );
	}		
	return $arr;
    }
    public function salvarMe($arrParametros){
	$sucursalExtendido = NEW SucursalExtendido();
	$sucursalExtendido->salvarMe($arrParametros);
    }
    public function actualizarMe($arrParametros){
	$sucursalExtendido = NEW SucursalExtendido();
	$sucursalExtendido->actualizarMe($arrParametros);
    }
}
?>