<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/DistribuidorExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteDistribuidores
{
    private $_colDistribuidores = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    public function __construct(){
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
	$this->_objFuncionesComunes = new FuncionesComunes();
    }
    public function cargarDistribuidor($id){
	
	$this->_colDistribuidores[$id] = NEW DistribuidorExtendido();
	$this->_colDistribuidores[$id]->cargarMe($id);
    }
    public function setColObjDistribuidor($objDistribuidor){
    
	$this->_colDistribuidors[$objDistribuidor->getDistribuidorId()] = $objDistribuidor;
    }
    public function getColObjDistribuidor(){
	return $this->_colDistribuidors;
    }
    public function cargarDistribuidoresLazzy($arrIds){
    
	foreach($arrIds as $id){
	    if($id['distribuidor_id']!=NULL){
		$ids.= "'" . $id['distribuidor_id'] . "',";
	    }
	}
	
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
	$this->_db->addSelect('distribuidor_activo AS distribuidor_activo');
	$this->_db->addSelect('\'Activo\'  AS distribuidor_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS distribuidor_no_activo_desc');
	$this->_db->addSelect('CASE WHEN distribuidor_activo THEN \'Activo\' ELSE \'Inactivo\' END AS distribuidor_activo_mensaje');
        $this->_db->addFrom('datos_distribuidores');
        $this->_db->addWhere("distribuidor_id IN (" . $ids . ")" );
	$this->_db->generarSelect();

	//echo $this->_db->getQry();
	
        $resultado = $this->_db->ejecutar();
        
        
        $creados=0;
        
	if(count($resultado)>0){
	    foreach($resultado AS $detalle){
		$objDistribuidor = NEW DistribuidorExtendido();
		$objDistribuidor->cargarMe($detalle);
		$this->setColObjDistribuidor($objDistribuidor);
		$creados++;
	    }
	}
	if($creados<count($arrIds)){
	    for($i=$creados;$i<count($arrIds);$i++){
		$objDistribuidor = NEW DistribuidorExtendido();
		$this->setColObjDistribuidor($objDistribuidor);
	    }
	}
	
    }
    public function buscarDistribuidores($arrParametros){

	$this->_db->addSelect('distribuidor_id');

	$this->_db->addFrom('datos_distribuidores');

	//**ANALIZO EL WHERE**//
	if($arrParametros['stringBuscar']){
    
	    $this->_db->addWhere('distribuidor_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $this->_db->addWhere('distribuidor_activo=true');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
	}
	//**where faso = true**/
	if($arrParametros['distribuidores'] !=null && $arrParametros['desdeAlta'] == null){
	    
	    $this->_db->addWhere('distribuidor_id = \'' . $arrParametros['distribuidores'] . '\'');
	}
	if($arrParametros['distribuidor_mail'] !=null && $arrParametros['desdeAlta'] == null){
	    
	    $this->_db->addWhere('distribuidor_mail = \'' . $arrParametros[distribuidorMail] . '\'');
	}
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenDistribuidores($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='distribuidores'){
	    
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
	    
	    $this->cargarDistribuidor($arr[$i]['distribuidor_id']);
	}
    }
    public function setOrdenDistribuidores($arrParametros){

	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'distribuidor_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenDistribuidores(){

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

	foreach($this->_colDistribuidores AS $distribuidor){

	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $distribuidor->getDistribuidorId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $distribuidor->getDistribuidorNombre(),
                    $distribuidor->getDistribuidorTelefono(),
                    $distribuidor->getObs(),
                    Array
			(
			"value" => $distribuidor->getDistribuidorActivo(),
			"label" =>$distribuidor->getDistribuidorActivoMensaje(),
			"select" => 
			    Array 
			    (
				Array(
				"value" =>true,
				"label" =>$distribuidor->getDistribuidorActivoDesc()

				),
				Array

				(
				"value" =>false,
				"label" =>$distribuidor->getDistribuidorNoActivoDesc()

				)		
			    )
			)
                ),
            );
	}		
	return $arr;
    }
    public function getDetallesAutocompletar(){

	foreach($this->_colDistribuidores AS $distribuidor){
	 $arr[]   =   array(
            'label' =>  $distribuidor->getDistribuidorNombre(),
            'value' =>  $distribuidor->getDistribuidorId()
            );
	}		
	return $arr;
    }
    public function salvarMe($arrParametros){

	$distribuidorExtendido = NEW DistribuidorExtendido();
	$distribuidorExtendido->salvarMe($arrParametros);
    }

    public function actualizarMe($arrParametros){

	$distribuidorExtendido = NEW DistribuidorExtendido();
	$distribuidorExtendido->actualizarMe($arrParametros);
    }
}
?>
