<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/MarcaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteMarcas
{
    private $_colMarcas = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    public function __construct(){
	$this->_objFuncionesComunes = new FuncionesComunes();
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
    }
    public function cargarMarca($id){

	$this->_colMarcas[$id] = NEW MarcaExtendido();
	$this->_colMarcas[$id]->cargarMe($id);
    }
    public function setColObjMarcas($objMarca){
	$this->_colMarcas[$objMarca->getMarcaId()] = $objMarca;
    }
    public function getColObjMarcas(){
	return $this->_colMarcas;
    }
    public function cargarMarcasLazzy($arrIds){
    
	foreach($arrIds as $id){
	    $ids.= "'" . $id['marca_id'] . "',";
	}
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
	$this->_db->addSelect('marca_activa AS marca_activo');
	$this->_db->addSelect('\'Activo\'  AS marca_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS marca_no_activo_desc');
	$this->_db->addSelect('CASE WHEN marca_activa THEN \'Activo\' ELSE \'Inactivo\' END AS marca_activo_mensaje');
        $this->_db->addFrom('datos_marcas');
        $this->_db->addWhere("marca_id IN (" . $ids . ")" );
	$this->_db->generarSelect();

        $resultado = $this->_db->ejecutar();
        
	foreach($resultado AS $detalle){
	    $objMarca = NEW MarcaExtendido();
	    $objMarca->cargarMe($detalle);
	    $this->setColObjMarcas($objMarca);
	}
    }
    public function buscarMarcas($arrParametros){


	$this->_db->addSelect('marca_id');
	
	$this->_db->addFrom('datos_marcas');

	//**ANALIZO EL WHERE**//

	if($arrParametros['stringBuscar']){
	    $this->_db->addWhere('marca_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
	}
	if($arrParametros['marcas'] !=null && $arrParametros['desdeAlta']==null){

	    $this->_db->addWhere('marca_id = \'' . $arrParametros['marcas'] . '\'');
	}
	$this->_db->addWhere('marca_activa=true');

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenMarcas($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='marcas'){
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
	    $arrIds[$i]['marca_id'] = $arr[$i]['marca_id'];
	}
	$this->cargarMarcasLazzy($arrIds);
    }
    public function setOrdenMarcas($arrParametros){

	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'marca_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenMarcas() {
	return $this->_ordenColumnas;
    }

    public function setTotal($total) {

	$this->_total = $total;
    }
    public function getTotal() {

	return $this->_total;
    }
    //pagina 1 por defecto amigo
    public function setPagina($pagina=1){
	
	$this->_pagina = $pagina;
    }
    public function getPagina(){

	return $this->_pagina;
    }

    public function getDetalles() {

	foreach($this->_colMarcas AS $marca){

	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $marca->getMarcaId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $marca->getMarcaNombre(),
		    $marca->getObs(),
                     Array
			(
			"value" => $marca->getMarcaActiva(),
			"label" =>$marca->getMarcaActivoMensaje(),
			"select" => 
			    Array 
			    (
				Array(
				"value" =>true,
				"label" =>$marca->getMarcaActivoDesc()

				),
				Array

				(
				"value" =>false,
				"label" =>$marca->getMarcaNoActivoDesc()

				)		
			    )
			)
                ),
            );
	}		
	return $arr;
    }
    public function getDetallesAutocompletar(){

	foreach($this->_colMarcas AS $marca){

	 $arr[]   =   array(
            'label' =>  $marca->getMarcaNombre(),
            'value' =>  $marca->getMarcaId()
            );
	}		
	return $arr;
    }
    public function salvarMe($arrParametros){

	$MarcaExtendido = NEW MarcaExtendido();
	$MarcaExtendido->salvarMe($arrParametros);
    }
    public function actualizarMe($arrParametros){

	$MarcaExtendido = NEW MarcaExtendido();
	$MarcaExtendido->actualizarMe($arrParametros);
    }
}
?>
