<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'GlnDestinoExtendido.php';

/*
 * dMELMAC 
 * 
 */

/**
 * Description of FrenteGln
 *
 * @author by dMELMAC
 */

class FrenteGln
{
    private $_db;
    private $_colObjGln= Array();
        
    private $_arrError = Array();

    //** NECESARIOS PARA EL FRENTE DE FRENTES//
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    private $_colObjGlnOrigen=Array();
    private $_colObjGlnDestino=Array();
    //** FIN NECESIARIOS**//

    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
	$this->_objFuncionesComunes = new FuncionesComunes();

        
    }
    
    public function setColObjGln($obj){
        $this->_colObjGln[] = $obj;
    }
    
    public function getColObjGln(){
        return $this->_colObjGln;
    }
    public function setColObjGlnOrigen($obj){
    
	$this->_colObjGlnOrigen[$obj->getGln()] = $obj;
	
    }
    public function getColObjGlnOrigen(){
	return $this->_colObjGlnOrigen;
    }
    public function setColObjGlnDestino($obj){
    
	$this->_colObjGlnDestino[$obj->getGln()] = $obj;
    }
    public function getColObjGlnDestino(){
	return $this->_colObjGlnDestino;
    }
    public function setArrError($arrError){
        $this->_arrError = $arrError;
    }
    
    public function getArrError(){
        return $this->_arrError ;
    }


    public function cargarGln($arrIds){

        foreach($arrIds as $id){
	    $ids.= "'" . $id['gln'] . "',";
	}
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
        $this->_db->addFrom('datos_gln');
        $this->_db->addWhere("gln IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        $creados=0;
        
	if(count($resultado)>0){
	    foreach($resultado AS $detalle){
	
		$objGlnDestino = NEW GlnDestinoExtendido();
		$objGlnDestino->cargarme($detalle);
		$this->setColObjGln($objGlnDestino);
		$creados++;
	    }
	}
    }
    
    public function cargarGlnOrigenLazzy($arrIds){
    
	foreach($arrIds as $id){
		$ids.= "'" . $id['gln_origen'] . "',";
	    
	}
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
        $this->_db->addFrom('datos_gln');
        $this->_db->addWhere("gln IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        $creados=0;
        $indiceGln=1;
        if(count($resultado)>0){
	    foreach($resultado AS $detalle){
		$resultadoGln[$indiceGln] = $detalle['gln'];
		$objGlnOrigen = NEW GlnOrigenExtendido();
		$objGlnOrigen ->cargarme($detalle);
		$this->setColObjGlnOrigen($objGlnOrigen);
		$creados++;
		$indiceGln++;
	    }
	}
	foreach($arrIds AS $ids){
	    
	    if(@!array_search($ids['gln_origen'], $resultadoGln)){
	
		$arrGlnIncorrectos[]=$ids['gln_origen'];
		
	    }
	}
	$indiceGln=0;  
	if($creados<count($arrIds)){
	
	    for($i=$creados;$i<count($arrIds);$i++){
		
		if($arrGlnIncorrectos[$indiceGln]!=NULL){
		    $objGlnOrigen = NEW GlnOrigenExtendido();
		    $objGlnOrigen->setGln($arrGlnIncorrectos[$indiceGln]);
		    $this->setColObjGlnOrigen($objGlnOrigen);
		}else{
		    $objGlnOrigen = NEW GlnOrigenExtendido();
		    $this->setColObjGlnOrigen($objGlnOrigen);
		}
	    }
	}
	//var_dump($this->getColObjGlnOrigen());exit;
    }
    public function cargarGlnDestinoLazzy($arrIds){
    
	foreach($arrIds as $id){
	    if($id['gln_destino']!=NULL){
		$ids.= "'" . $id['gln_destino'] . "',";
	    }
	}
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
        $this->_db->addFrom('datos_gln');
        $this->_db->addWhere("gln IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        $creados=0;
        $indiceGln=1;
        
	if(count($resultado)>0){
	    foreach($resultado AS $detalle){
	
		$resultadoGln[$indiceGln] = $detalle['gln'];
		$objGlnDestino = NEW GlnDestinoExtendido();
		$objGlnDestino->cargarme($detalle);
		$this->setColObjGlnDestino($objGlnDestino);
		$creados++;
		$indiceGln++;
	    }
	}
	
	foreach($arrIds AS $ids){
	    
	    if(@!array_search($ids['gln_destino'], $resultadoGln)){
	
		$arrGlnIncorrectos[]=$ids['gln_destino'];
		
	    }
	}
	$indiceGln=0;    
	if($creados<count($arrIds)){
	
	    for($i=$creados;$i<count($arrIds);$i++){
		if($arrGlnIncorrectos[$indiceGln]!=NULL){
		    $objGlnDestino = NEW GlnDestinoExtendido();
		    $objGlnDestino->setGln($arrGlnIncorrectos[$indiceGln]);
		    $this->setColObjGlnDestino($objGlnDestino);
		}else{
		    $objGlnDestino = NEW GlnDestinoExtendido();
		    $this->setColObjGlnDestino($objGlnDestino);
		}
		$indiceGln++;
	    }
	}
    }
    public function buscarGln ($arrParametros){


	$this->_db->addSelect('gln');
	$this->_db->addFrom('datos_gln');
	

	//**ANALIZO EL WHERE**//
	if($arrParametros['stringBuscar']){

	    $this->_db->addWhere('gln_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
	}

	//**where faso = true**/
	if($arrParametros['gln_origen'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('gln_origen = \'' . $arrParametros[origen] . '\'');
	}
	if($arrParametros['gln_destino'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('gln_origen = \'' . $arrParametros[origen] . '\'');
	}
	
	//*seteo el orden*/
	$this->setOrdenGln($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='transacciones'){
	    $this->_db->addLimit(2200);
	}

	$this->_db->addGroup('gln');
	//*FIN LIMIT*//
	
	$this->_db->generarSelect();

	//echo $this->_db->getQry();
	$arr =  $this->_db->ejecutar();
	
	//ahora seteamos el total y la pagina
	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro)	{
	    $ultimoRegistro = $this->getTotal();
	}

	for($i=$primerRegistro;$i<$ultimoRegistro;$i++)	{   
	    
	    $arrIds[$i]['gln']=$arr[$i]['gln'];
	}
	$this->cargarGln($arrIds);
    }

    public function getError(){
        return $this->_arrError;
    }

    //**PARA EL FRENTE**//
    public function setOrdenGln($parametros){

	if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){
	    $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'gln';
	    $this->_ordenColumnas['ordenarOrden']  =   'desc';
	}
    }

    public function getOrdenGln(){

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

    public function getDetallesAutocompletar(){
	foreach($this->_colObjGln AS $gln)
	{
	 $arr[]   =   array(
            'label' =>  $gln->getGlnNombre(),
            'value' =>  $gln->getGln());
	}		
	return $arr;
    }
}
