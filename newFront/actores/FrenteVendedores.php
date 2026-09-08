<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/VendedorExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteVendedores
{
    private $_colVendedores = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    public function __construct(){
	$this->_db = new FrenteAlmacenamiento();
	$this->_objFuncionesComunes = new FuncionesComunes();
    }
    public function cargarVendedor($id){
	$this->_colVendedores[$id] = NEW VendedorExtendido();
	$this->_colVendedores[$id]->cargarMe($id);
    }
    public function setColObjVendedores($objVendedor){
	$this->_colVendedores[$objVendedor->getVendedorId()] = $objVendedor;
    }
    public function getColObjVendedores(){
	return $this->_colVendedores;
    }
    public function cargarVendedoresLazzy($arrIds){
    
	//echo  count($arrIds);exit;
	foreach($arrIds as $id){
	    if($id['vendedor_id']!=null){
		$ids.= "'" . $id['vendedor_id'] . "',";
	    }
	}
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
	$this->_db->addSelect('vendedor_id');
	$this->_db->addSelect('vendedor_nombre');
	$this->_db->addSelect('vendedor_mail');
	$this->_db->addSelect('vendedor_activo AS vendedor_activo');
	$this->_db->addSelect('\'Activo\'  AS vendedor_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS vendedor_no_activo_desc');
	$this->_db->addSelect('CASE WHEN vendedor_activo THEN \'Activo\' ELSE \'Inactivo\' END AS vendedor_activo_mensaje');
	$this->_db->addSelect('observaciones');
	$this->_db->addSelect('vendedor_telefono');
	
	$this->_db->addFrom('datos_vendedores');
	$this->_db->addWhere("vendedor_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
	
	//echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        $creados=0;
        $antVendedorId=NULL;
	$indiceError=1;
	 if(count($resultado)>0){
	    foreach($resultado AS $detalle){
		if($detalle['vendedor_id'] != $antVendedorId){
		    $resultadoError[$indiceError] = $detalle['vendedor_id'];
		    $objVendedor= NEW VendedorExtendido();
		    $objVendedor->cargarMe($detalle);
		    $this->setColObjVendedores($objVendedor);
		    $creados++;
   	  	    $indiceError++;
		}
		$antVendedorId= $detalle['vendedor_id'];
	    }
	}
	foreach($arrIds AS $ids){
	    
	    if(@!array_search($ids['vendedor_id'], $resultadoError)){
	
		$arrErrorIncorrectos[]=$ids['vendedor_id'];
		
	    }
	}
	$indiceVendedor=0;  
	if($creados<count($arrIds)){
	
	    for($i=$creados;$i<count($arrIds);$i++){

		if($arrErrorIncorrectos[$indiceError]!=NULL){
	            $objVendedor= NEW VendedorExtendido();
	    	    $objVendedor->setVendedorId($arrErrorIncorrectos[$indiceError]);
		    $objVendedor->setVendedorNombre('');
		    $this->setColObjVendedores($objVendedor);	
		}else{
		    $objVendedor= NEW VendedorExtendido();
		    $this->setColObjVendedores($objVendedor);
		}
		$indiceError++;
	    }
	}
	
    }
    public function buscarVendedores($arrParametros){
	
	$db_dM = NEW FrenteAlmacenamiento('dmelmac');

	$db_dM->addSelect('vendedor_id');

	$db_dM->addFrom('datos_vendedores');

	//**ANALIZO EL WHERE**//
	if($arrParametros['stringBuscar']){
    
	    $db_dM->addWhere('vendedor_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
	}
	//**where faso = true**/
	if($arrParametros['vendedoresClientes'] !=null && $arrParametros['desdeAlta'] == null){
	    
	    $db_dM->addWhere('vendedor_id = \'' . $arrParametros['vendedoresClientes'] . '\'');
	}
	if($arrParametros['vendedor_mail'] !=null && $arrParametros['desdeAlta'] == null){
	    
	    $db_dM->addWhere('vendedor_mail = \'' . $arrParametros[vendedorMail] . '\'');
	}
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenVendedores($arrParametros);
	$db_dM->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='vendedores'){
	    
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
	    $arrIds[$i]['vendedor_id'] = $arr[$i]['vendedor_id'];
	}
	$this->cargarVendedoresLazzy($arrIds);
    }
    public function setOrdenVendedores($arrParametros){

	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'vendedor_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenVendedores(){

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

	foreach($this->_colVendedores AS $vendedor){

	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $vendedor->getVendedorId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $vendedor->getVendedorNombre(),
                    $vendedor->getVendedorTelefono(),
                    $vendedor->getObs(),
                    Array
			(
			"value" => $vendedor->getVendedorActivo(),
			"label" =>$vendedor->getVendedorActivoMensaje(),
			"select" => 
			    Array 
			    (
				Array(
				"value" =>true,
				"label" =>$vendedor->getVendedorActivoDesc()

				),
				Array

				(
				"value" =>false,
				"label" =>$vendedor->getVendedorNoActivoDesc()

				)		
			    )
			)
                ),
            );
	}		
	return $arr;
    }
    public function getDetallesAutocompletar(){

	foreach($this->_colVendedores AS $vendedor){
	 $arr[]   =   array(
            'label' =>  $vendedor->getVendedorNombre(),
            'value' =>  $vendedor->getVendedorId()
            );
	}		
	return $arr;
    }
    public function salvarMe($arrParametros){

	$vendedorExtendido = NEW VendedorExtendido();
	$vendedorExtendido->salvarMe($arrParametros);
    }

    public function actualizarMe($arrParametros){

	$vendedorExtendido = NEW VendedorExtendido();
	$vendedorExtendido->actualizarMe($arrParametros);
    }
}
?>
