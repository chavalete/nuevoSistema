<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/EstadoExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteEstados
{
    private $_colEstados = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    public function __construct(){

	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
	$this->_objFuncionesComunes = new FuncionesComunes();
    }
    public function cargarEstado($id){

	$this->_colEstados[$id] = NEW EstadoExtendido();
	$this->_colEstados[$id]->cargarMe($id);
    }
    public function setColObjEstados($objEstado){
    
	$this->_colEstados[$objEstado->getEstadoId()] = $objEstado;
    }
    public function getColObjEstados(){
	return $this->_colEstados;
    }
    public function cargarEstadosLazzy($arrIds){
   
    
	foreach($arrIds as $id){
	    if($id['estado_id']!=null){
		$ids.= "'" . $id['estado_id'] . "',";
	    }elseif($id['hoja_estado_id']!=null){
		$ids.= "'" . $id['hoja_estado_id'] . "',";
	    }else{
		$ids.= "'" . $id['orden_estado_id'] . "',";
	    }
	}
	
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
	$this->_db->addSelect('estado_activo AS estado_activo');
	$this->_db->addSelect('\'Activo\'  AS estado_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS estado_no_activo_desc');
	$this->_db->addSelect('CASE WHEN estado_activo THEN \'Activo\' ELSE \'Inactivo\' END AS estado_activo_mensaje');
        $this->_db->addFrom('datos_estados');
        $this->_db->addWhere("estado_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
//echo $this->_db->getQry();exit;
	
        $resultado = $this->_db->ejecutar();
        
	foreach($resultado AS $detalle){
	    $objEstado = NEW EstadoExtendido();
	    $objEstado->cargarMe($detalle);
	    $this->setColObjEstados($objEstado);
	    
	}
    }
    
    public function buscarEstados($arrParametros){

	

	$this->_db->addSelect('estado_id');
	
	$this->_db->addFrom('datos_estados');

	//**ANALIZO EL WHERE**//

	if($arrParametros['stringBuscar']){
	    $this->_db->addWhere('estado_desc ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
	}
	if($arrParametros['estados'] !=null && $arrParametros['desdeAlta']==null){
	    $this->_db->addWhere('estado_id = \'' . $arrParametros['estados'] . '\'');
	}

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenEstados($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='estados'){

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
	    $arrIds[$i]['estado_id'] = $arr[$i]['estado_id'];
	}
	$this->cargarEstadosLazzy($arrIds);
    }
    public function setOrdenEstados($arrParametros){

	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'estado_desc';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenEstados(){

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

	foreach($this->_colEstados AS $estado){
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $estado->getEstadoId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $estado->getEstadoNombre(),
		    $estado->getObs(),
                    Array
			(
			"value" => $estado->getEstadoActiva(),
			"label" =>$estado->getEstadoActivoMensaje(),
			"select" => 
			    Array 
			    (
				Array(
				"value" =>true,
				"label" =>$estado->getEstadoActivoDesc()

				),
				Array

				(
				"value" =>false,
				"label" =>$estado->getEstadoNoActivoDesc()

				)		
			    )
			)
                ),
            );
	}		
	return $arr;
    }
    public function getDetallesAutocompletar(){

	foreach($this->_colEstados AS $estado){
	 $arr[]   =   array(
            'label' =>  $estado->getEstadoNombre(),
            'value' => $estado->getEstadoId()
            );
	}		
	return $arr;
    }
    public function salvarMe($arrParametros){
	$estadoExtendido = NEW EstadoExtendido();
	$estadoExtendido->salvarMe($arrParametros);
    }
    public function actualizarMe($arrParametros){
	$estadoExtendido = NEW EstadoExtendido();
	$estadoExtendido->actualizarMe($arrParametros);
    }
}
?>
