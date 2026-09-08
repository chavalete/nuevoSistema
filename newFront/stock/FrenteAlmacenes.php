<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/AlmacenExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteAlmacenes
{
    private $_colAlmacenes = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;

    
    public function __construct(){
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
	$this->_objFuncionesComunes = NEW FuncionesComunes();
    }
    public function cargarAlmacen($id){
	$this->_colAlmacenes[$id] = NEW AlmacenExtendido();
	$this->_colAlmacenes[$id]->cargarMe($id);
    }
    public function setColAlmacenes($objAlmacen){
	$this->_colAlmacenes[$objAlmacen->getAlmacenId()] = $objAlmacen;
    }
    public function getColAlmacenes(){
	return $this->_colAlmacenes;
    }
    public function cargarAlmacenesLazzy($arrIds){
    
	foreach($arrIds as $id){
	    if($id['almacen_id']!=NULL){
		$ids.= "'" . $id['almacen_id'] . "',";
	    }
	}
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
	$this->_db->addSelect('datos_almacenes.observaciones AS observaciones');
	$this->_db->addSelect('almacen_activo AS almacen_activo');
	$this->_db->addSelect('\'Activo\'  AS almacen_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS almacen_no_activo_desc');
	$this->_db->addSelect('CASE WHEN almacen_activo THEN \'Activo\' ELSE \'Inactivo\' END AS almacen_activo_mensaje');
        $this->_db->addFrom('datos_almacenes');
        $this->_db->addFrom('INNER JOIN datos_sucursales USING(sucursal_id)');
        $this->_db->addWhere("almacen_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
                
        /*$FrenteAlmacenes = NEW FrenteAlmacenes();
        $FrenteAlmacenes->cargarAlmacenesLazzy($resultado);
        $arrObjAlmacenes = $FrenteAlmacenes->getColAlmacenes();
        */
	foreach($resultado AS $detalle){
	    $objAlmacen = NEW AlmacenExtendido();
	    $objAlmacen->cargarme($detalle);
	    //$objProducto->setObjAlmacen($arrObjAlmacenes[$detalle[almacen_id]]);
	    $this->setColAlmacenes($objAlmacen);
	}
    }
    public function buscarAlmacenes($arrParametros){
	
	$this->_db->addSelect('almacen_id');
	$this->_db->addFrom('datos_almacenes');

	if($arrParametros['stringBuscar']){
	    $this->_db->addWhere('almacen_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
	}
	//**where faso = true**/
	if($arrParametros['almacenes'] !=null && $arrParametros['desdeAlta']==null){
	    $this->_db->addWhere('almacen_id = \'' . $arrParametros['almacenes'] . '\'');
	}
	if($arrParametros['sucursales'] !=null && $arrParametros['desdeAlta']==null){
	    $this->_db->addWhere('sucursal_id = \'' . $arrParametros['sucursales'] . '\'');
	}
	//**fin where **/

	//*seteo el orden*/
	$this->setOrdenAlmacenes($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='almacenes'){
	    $this->_db->addLimit(100);
	}
	//*FIN LIMIT*//
	$this->_db->generarSelect();

	$arr =  $this->_db->ejecutar();

	//ahora seteamos el total y la pagina
	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro){
	    $ultimoRegistro = $this->getTotal();
	}	

	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
	    $arrIds[$i]['almacen_id'] = $arr[$i]['almacen_id'];
	}
	$this->cargarAlmacenesLazzy($arrIds);
    }
    public function setOrdenAlmacenes($arrParametros){

	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'almacen_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenAlmacenes(){

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
	
	foreach($this->_colAlmacenes AS $almacen){
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $almacen->getAlmacenId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $almacen->getAlmacenNombre(),
                    $almacen->getSucursalNombre(),
                    $almacen->getObs(),
                    Array
                    (
                    "value" => $almacen->getAlmacenActivo(),
                    "label" =>$almacen->getAlmacenActivoMensaje(),
                    "select" => 
                        Array 
                        (
                            Array(
                            "value" =>true,
                            "label" =>$almacen->getAlmacenActivoDesc()

                            ),
                            Array

                            (
                            "value" =>false,
                            "label" =>$almacen->getAlmacenNoActivoDesc()

                            )		
                        )
                    )
                ),
            );
	}		
	return $arr;
    }
    public function getDetallesAutocompletar(){

	foreach($this->_colAlmacenes AS $almacen){
	 $arr[]   =   array(
            'label' =>  $almacen->getAlmacenNombre(),
            'value' =>  $almacen->getAlmacenId()
            );
	}		
	return $arr;
    }
    public function salvarMe($arrParametros){
	$almacenExtendido = NEW almacenExtendido();
	$almacenExtendido->salvarMe($arrParametros);
    }
    public function actualizarMe($arrParametros){

	//$arrParametros[campos][sucursal_id] =$this->_objFuncionesComunes->parsearAutocompletar($arrParametros[campos][sucursal_nombre]);
	$almacenExtendido = NEW almacenExtendido();
	$almacenExtendido->actualizarMe($arrParametros);
    }
}
?>
