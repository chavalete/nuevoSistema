<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/CondicionIvaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteCondicionIva
{
    private $_colCondicionIva = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    public function __construct(){

	$this->_objFuncionesComunes = new FuncionesComunes();
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
    }
    public function cargarCondicionIva($id){

	$this->_colCondicionIva[$id] = NEW CondicionIvaExtendido();
	$this->_colCondicionIva[$id]->cargarMe($id);
    }
    public function setColObjCondicionIva($objCondicionIva){
    
	$this->_colCondicionIva[$objCondicionIva->getCondicionIvaId()] = $objCondicionIva;
    }
    public function getColObjCondicionIva(){
	return $this->_colCondicionIva;
    }
    public function cargarCondicionLazzy($arrIds){
    
	foreach($arrIds as $id){
	    if($id['condicion_iva_id']!=NULL){
		$ids.= "'" . $id['condicion_iva_id'] . "',";
	    }
	}
	
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
	
        $this->_db->addFrom('datos_condiciones_iva');
        $this->_db->addWhere("condicion_iva_id IN (" . $ids . ")" );
	$this->_db->generarSelect();

	//echo $this->_db->getQry();
	
        $resultado = $this->_db->ejecutar();
        
        
        $creados=0;
        
	if(count($resultado)>0){
	    foreach($resultado AS $detalle){
		$objCondicionIva = NEW CondicionIvaExtendido();
		$objCondicionIva->cargarMe($detalle);
		$this->setColObjCondicionIva($objCondicionIva);
		$creados++;
	    }
	}
	if($creados<count($arrIds)){
	    for($i=$creados;$i<count($arrIds);$i++){
		$objCondicionIva = NEW CondicionIvaExtendido();
		$this->setColObjCondicionIva($objCondicionIva);
	    }
	}
	
    }
    public function buscarCondicionIva($arrParametros){

	

	$this->_db->addSelect('condicion_iva_id');
	
	$this->_db->addFrom('datos_condiciones_iva');

	//**ANALIZO EL WHERE**//

	if($arrParametros['stringBuscar']){
	    $this->_db->addWhere('descripcion ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
	}
	if($arrParametros['condicion_ventas'] !=null && $arrParametros['desdeAlta']==null){
	    $this->_db->addWhere('condicion_iva_id = \'' . $arrParametros['condicion_ventas'] . '\'');
	}

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenCondicionIvas($arrParametros);
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
	    $arrIds[$i]['condicion_iva_id'] = $arr[$i]['condicion_iva_id'];
	}
	$this->cargarCondicionLazzy($arrIds);
    }
    public function setOrdenCondicionIvas($arrParametros){

	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'descripcion';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenCondicionIvas(){

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

	foreach($this->_colCondicionIvas AS $condicion_iva){

	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $condicion_iva->getCondicionIvaId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $condicion_iva->getCondicionIvaNombre(),
		    $condicion_iva->getObs(),
                    Array
			(
			"value" => $condicion_iva->getCondicionIvaActiva(),
			"label" =>$condicion_iva->getCondicionIvaActivoMensaje(),
			"select" => 
			    Array 
			    (
				Array(
				"value" =>true,
				"label" =>$condicion_iva->getCondicionIvaActivoDesc()

				),
				Array

				(
				"value" =>false,
				"label" =>$condicion_iva->getCondicionIvaNoActivoDesc()

				)		
			    )
			)
                ),
            );
	}		
	return $arr;
    }
    public function getDetallesAutocompletar(){

	foreach($this->_colCondicionIva AS $condicion_iva){
	 $arr[]   =   array(
            'label' =>  $condicion_iva->getDescripcion(),
            'value' => $condicion_iva->getCondicionIvaId()
            );
	}		
	return $arr;
    }
    public function salvarMe($arrParametros){
	$condicion_ivaExtendido = NEW CondicionIvaExtendido();
	$condicion_ivaExtendido->salvarMe($arrParametros);
    }
    public function actualizarMe($arrParametros){
	$condicion_ivaExtendido = NEW CondicionIvaExtendido();
	$condicion_ivaExtendido->actualizarMe($arrParametros);
    }
}
?>
