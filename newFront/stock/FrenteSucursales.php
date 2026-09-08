<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/SucursalExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteSucursales
{
    private $_colSucursales = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;

    public function __construct(){

	$this->_objFuncionesComunes = NEW FuncionesComunes();
    }
    public function cargarSucursal($id){
	$this->_colSucursales[$id] = NEW SucursalExtendido();
	$this->_colSucursales[$id]->cargarMe($id);
    }
    public function buscarSucursales($arrParametros){
	$db_dM = NEW FrenteAlmacenamiento('dmelmac');

	$db_dM->addSelect('sucursal_id');
	
	$db_dM->addFrom('datos_sucursales');

	//**ANALIZO EL WHERE**//

	if($arrParametros['stringBuscar']){
	    $db_dM->addWhere('sucursal_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
	}
	if($arrParametros['sucursales'] !=null && $arrParametros['desdeAlta']==null){
	    $db_dM->addWhere('sucursal_id = \'' . $arrParametros['sucursales'] . '\'');
	}

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenSucursales($arrParametros);
	$db_dM->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='sucursales'){
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
	    $this->cargarSucursal($arr[$i]['sucursal_id']);
	}
    }
    public function setOrdenSucursales($arrParametros){
	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){
	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'sucursal_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenSucursales(){
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
	foreach($this->_colSucursales AS $sucursal){
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $sucursal->getSucursalId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $sucursal->getSucursalNombre(),
		    $sucursal->getObs(),
                    $sucursal->getSucursalActiva()
                ),
            );
	}		
	return $arr;
    }
    public function getDetallesAutocompletar(){
	foreach($this->_colSucursales AS $sucursal){
	 $arr[]   =   array(
            'label' =>  $sucursal->getSucursalNombre(),
            'value' =>  $sucursal->getSucursalId()
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