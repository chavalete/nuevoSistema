<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/CustodianteExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

class FrenteCustodiantes {
    private $_colCustodiantes = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    public function __construct(){
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
	$this->_objFuncionesComunes = new FuncionesComunes();
    }
    public function cargarCustodiante($id){
	
	$this->_colCustodiantes[$id] = NEW CustodianteExtendido();
	$this->_colCustodiantes[$id]->cargarMe($id);
    }
    public function setColObjCustodiante($objCustodiante){
    
	$this->_colCustodiantes[$objCustodiante->getCustodianteId()] = $objCustodiante;
    }
    public function getColObjCustodiante(){
	return $this->_colCustodiantes;
    }
    public function cargarCustodiantesLazzy($arrIds){
    //var_dump($arrIds);
	foreach($arrIds as $id){
	    if($id['custodiante_id']!=NULL){
		$ids.= "'" . $id['custodiante_id'] . "',";
	    }
	}
	
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
	$this->_db->addSelect('custodiante_activo AS custodiante_activo');
	$this->_db->addSelect('\'Activo\'  AS custodiante_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS custodiante_no_activo_desc');
	$this->_db->addSelect('CASE WHEN custodiante_activo THEN \'Activo\' ELSE \'Inactivo\' END AS custodiante_activo_mensaje');
        $this->_db->addFrom('datos_custodiantes');
        $this->_db->addWhere("custodiante_id IN (" . $ids . ")" );
	$this->_db->generarSelect();

	//echo $this->_db->getQry();
	
        $resultado = $this->_db->ejecutar();
        
        
        $creados=0;
        
	if(count($resultado)>0){
	    foreach($resultado AS $detalle){
		$objCustodiante = NEW CustodianteExtendido();
		$objCustodiante->cargarMe($detalle);
		$this->setColObjCustodiante($objCustodiante);
		$creados++;
	    }
	}
	if($creados<count($arrIds)){
	    for($i=$creados;$i<count($arrIds);$i++){
		$objCustodiante = NEW CustodianteExtendido();
		$this->setColObjCustodiante($objCustodiante);
	    }
	}
	
    }
    public function buscarCustodiantes($arrParametros){

	$this->_db->addSelect('custodiante_id');

	$this->_db->addFrom('datos_custodiantes');

	//**ANALIZO EL WHERE**//
	if($arrParametros['stringBuscar']){
    
	    $this->_db->addWhere('custodiante_nombre_completo ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
	}
	//**where faso = true**/
	if($arrParametros['custodiantes'] !=null && $arrParametros['desdeAlta'] == null){
	    
	    $this->_db->addWhere('custodiante_id = \'' . $arrParametros['custodiantes'] . '\'');
	}
	if($arrParametros['custodiante_mail'] !=null && $arrParametros['desdeAlta'] == null){
	    
	    $this->_db->addWhere('custodiante_mail = \'' . $arrParametros[custodianteMail] . '\'');
	}
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenCustodiantes($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='custodiantes'){
	    
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
	    $arrIds[$i]['custodiante_id'] = $arr[$i]['custodiante_id'];
	}
	$this->cargarCustodiantesLazzy($arrIds);
    }
    public function setOrdenCustodiantes($arrParametros){

	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'custodiante_nombre_completo';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenCustodiantes(){

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

	foreach($this->_colCustodiantes AS $custodiante){

	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $custodiante->getCustodianteId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $custodiante->getCustodianteNombreCompleto(),
                    $custodiante->getCustodianteTelefono(),
                    $custodiante->getCustodianteMail(),
		    $custodiante->getObs(),
                    Array
			(
			"value" => $custodiante->getCustodianteActivo(),
			"label" =>$custodiante->getCustodianteActivoMensaje(),
			"select" => 
			    Array 
			    (
				Array(
				"value" =>true,
				"label" =>$custodiante->getCustodianteActivoDesc()

				),
				Array

				(
				"value" =>false,
				"label" =>$custodiante->getCustodianteNoActivoDesc()

				)		
			    )
			)
                ),
            );
	}		
	return $arr;
    }
    public function getDetallesAutocompletar(){

	foreach($this->_colCustodiantes AS $custodiante){
	 $arr[]   =   array(
            'label' =>  $custodiante->getCustodianteNombreCompleto(),
            'value' =>  $custodiante->getCustodianteId()
            );
	}		
	return $arr;
    }
    public function salvarme($arrParametros){
	$custodianteExtendido = NEW CustodianteExtendido();
	$custodianteExtendido->salvarMe($arrParametros);
    }

    public function actualizarMe($arrParametros){

	$custodianteExtendido = NEW CustodianteExtendido();
	$custodianteExtendido->actualizarMe($arrParametros);
    }
}
