<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/MedicoExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteMedicos
{
    private $_colMedicos = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    public function __construct(){

	$this->_objFuncionesComunes = new FuncionesComunes();
    }
    public function cargarMedico($id){
	
	$this->_colMedicos[$id] = NEW MedicoExtendido();
	$this->_colMedicos[$id]->cargarMe($id);
    }
    public function buscarMedicos($arrParametros){
	
	$db_dM = NEW FrenteAlmacenamiento('dmelmac');

	$db_dM->addSelect('medico_id');

	$db_dM->addFrom('datos_medicos');

	//**ANALIZO EL WHERE**//
	if($arrParametros['stringBuscar']){
    
	    $db_dM->addWhere('medico_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
	}
	//**where faso = true**/
	if($arrParametros['medicos'] !=null && $arrParametros['desdeAlta'] == null){
	    
	    $db_dM->addWhere('medico_id = \'' . $arrParametros['medicos'] . '\'');
	}
	if($arrParametros['nroMatricula'] !=null && $arrParametros['desdeAlta'] == null){
	    
	    $db_dM->addWhere('matricula_nro = \'' . $arrParametros[nroMatricula] . '\'');
	}
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenMedicos($arrParametros);
	$db_dM->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='medicos'){
	    
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
	    
	    $this->cargarMedico($arr[$i]['medico_id']);
	}
    }
    public function setOrdenMedicos($arrParametros){

	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'medico_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenMedicos(){

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

	foreach($this->_colMedicos AS $medico){

	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $medico->getMedicoId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $medico->getMedicoNombre(),
                    $medico->getMedicoTelefono(),
		    $medico->getObs(),
                    Array
			(
			"value" => $medico->getMedicoActivo(),
			"label" =>$medico->getMedicoActivoMensaje(),
			"select" => 
			    Array 
			    (
				Array(
				"value" =>true,
				"label" =>$medico->getMedicoActivoDesc()

				),
				Array

				(
				"value" =>false,
				"label" =>$medico->getMedicoNoActivoDesc()

				)		
			    )
			)
                ),
            );
	}		
	return $arr;
    }
    public function getDetallesAutocompletar(){

	foreach($this->_colMedicos AS $medico){
	 $arr[]   =   array(
            'label' =>  $medico->getMedicoNombre(),
            'value' =>  $medico->getMedicoId()
            );
	}		
	return $arr;
    }
    public function salvarMe($arrParametros){

	$medicoExtendido = NEW MedicoExtendido();
	$medicoExtendido->salvarMe($arrParametros);
    }

    public function actualizarMe($arrParametros){

	$medicoExtendido = NEW MedicoExtendido();
	$medicoExtendido->actualizarMe($arrParametros);
    }
}
?>