<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/remesas/RemesaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteRemesa
{
    private $_colRemsas = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;

    
    public function __construct()
    {}

   


    public function cargarRemesa($id){
	
	$this->_colRemesas[$id] = NEW RemesaExtendido();
	$this->_colRemesas[$id]->cargarme($id);

    }
    public function buscarRemesa($arrParametros){

	$db_dM = NEW FrenteAlmacenamiento('dmelmac');

	$db_dM->addSelect('remesa_id');

	$db_dM->addFrom('datos_remesas');

	//**ANALIZO EL WHERE**//

if($arrParametros['stringBuscar']){

	    $db_dM->addWhere('id_movimiento ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
	}

	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenRemesa($arrParametros);
	$db_dM->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='importadorRemesa'){

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
		$primerRegistro=0;
		$ultimoRegistro=100;
	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){
	    
	    $this->cargarRemesa($arr[$i]['remesa_id']);
	}
    }
    public function setOrdenRemesa($arrParametros){
	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){
	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'remesa_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenRemesa(){
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

	foreach($this->_colRemesas AS $remesa){

	    
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $remesa->getRemesaId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  false,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $remesa->getRemesaNombre(),
		    $remesa->getRemesaNro(),
                    $remesa->getFechaImportacion()
                ),
            );
	}		
	return $arr;
    }
    
    public function salvarme($arrParametros){

	$pacienteExtendido = NEW PacienteExtendido();
	$pacienteExtendido->salvarMe($arrParametros);
    }
}
?>