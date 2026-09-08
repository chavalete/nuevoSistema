<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/ProvinciaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteProvincias
{
    private $_colProvincias = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    public function __construct(){

	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
	$this->_objFuncionesComunes = new FuncionesComunes();
    }
    public function cargarProvincia($id){

	$this->_colProvincias[$id] = NEW ProvinciaExtendido();
	$this->_colProvincias[$id]->cargarMe($id);
    }
    public function setColObjProvincias($objProvincia){
    
	$this->_colProvincias[$objProvincia->getProvinciaId()] = $objProvincia;
    }
    public function getColObjProvincias(){
	return $this->_colProvincias;
    }
    public function cargarProvinciasLazzy($arrIds){
    
	foreach($arrIds as $id){
	    $ids.= "'" . $id['provincia_id'] . "',";
	}
	
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
	$this->_db->addSelect('provincia_activa AS provincia_activo');
	$this->_db->addSelect('\'Activa\'  AS provincia_activo_desc');
	$this->_db->addSelect('\'Inactiva\'  AS provincia_no_activo_desc');
	$this->_db->addSelect('CASE WHEN provincia_activa THEN \'Activo\' ELSE \'Inactivo\' END AS provincia_activo_mensaje');
        $this->_db->addFrom('datos_provincias');
        $this->_db->addWhere("provincia_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
	//echo $this->_db->getQry();
	
        $resultado = $this->_db->ejecutar();
        
	foreach($resultado AS $detalle){
	    $objProvincia = NEW ProvinciaExtendido();
	    $objProvincia->cargarMe($detalle);
	    $this->setColObjProvincias($objProvincia);
	    
	}
    }
    
    public function buscarProvincias($arrParametros){

	

	$this->_db->addSelect('provincia_id');
	
	$this->_db->addFrom('datos_provincias');

	//**ANALIZO EL WHERE**//

	if($arrParametros['stringBuscar']){
	    $this->_db->addWhere('provincia_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
	}
	if($arrParametros['provincias'] !=null && $arrParametros['desdeAlta']==null){
	    $this->_db->addWhere('provincia_id = \'' . $arrParametros['provincias'] . '\'');
	}

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenProvincias($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='provincias'){

	    $this->_db->addLimit(100);
	}
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
	
	if($this->getTotal() < $ultimoRegistro){
	    
	    $ultimoRegistro = $this->getTotal();
	}	

	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
	    $arrIds[$i]['provincia_id'] = $arr[$i]['provincia_id'];
	}
	$this->cargarProvinciasLazzy($arrIds);
    }
    public function setOrdenProvincias($arrParametros){

	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'provincia_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenProvincias(){

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

	foreach($this->_colProvincias AS $provincia){
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $provincia->getProvinciaId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $provincia->getProvinciaNombre(),
		    $provincia->getObs(),
                    Array
			(
			"value" => $provincia->getProvinciaActiva(),
			"label" =>$provincia->getProvinciaActivoMensaje(),
			"select" => 
			    Array 
			    (
				Array(
				"value" =>true,
				"label" =>$provincia->getProvinciaActivoDesc()

				),
				Array

				(
				"value" =>false,
				"label" =>$provincia->getProvinciaNoActivoDesc()

				)		
			    )
			)
                ),
            );
	}		
	return $arr;
    }
    public function getDetallesAutocompletar(){

	foreach($this->_colProvincias AS $provincia){
	 $arr[]   =   array(
            'label' =>  $provincia->getProvinciaNombre(),
            'value' => $provincia->getProvinciaId()
            );
	}		
	return $arr;
    }
    public function salvarMe($arrParametros){
	$provinciaExtendido = NEW ProvinciaExtendido();
	$provinciaExtendido->salvarMe($arrParametros);
    }
    public function actualizarMe($arrParametros){
	$provinciaExtendido = NEW ProvinciaExtendido();
	$provinciaExtendido->actualizarMe($arrParametros);
    }
}
?>