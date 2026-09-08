<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/EstanteriaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteEstanterias
{
    private $_colEstanterias = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    public function __construct(){
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
	$this->_objFuncionesComunes = new FuncionesComunes();
    }
    public function cargarEstanteria($id){
	$this->_colEstanterias[$id] = NEW EstanteriaExtendido();
	$this->_colEstanterias[$id]->cargarMe($id);
    }
    public function setColEstanterias($objEstanteria){
	$this->_colEstanterias[$objEstanteria->getEstanteriaId()] = $objEstanteria;
    }
    public function getColEstanterias(){
	return $this->_colEstanterias;
    }
    public function cargarEstanteriasLazzy($arrIds){
    
	foreach($arrIds as $id){
	    if($id['estanteria_id']!=NULL){
		$ids.= "'" . $id['estanteria_id'] . "',";
	    }
	}
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
        $this->_db->addFrom('datos_estanterias');
        $this->_db->addSelect('datos_estanterias.observaciones');
	$this->_db->addSelect('estanteria_activa AS estanteria_activo');
	$this->_db->addSelect('\'Activo\'  AS estanteria_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS estanteria_no_activo_desc');
	$this->_db->addSelect('CASE WHEN estanteria_activa THEN \'Activo\' ELSE \'Inactivo\' END AS estanteria_activo_mensaje');
        $this->_db->addWhere("estanteria_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
                
        $FrenteAlmacenes = NEW FrenteAlmacenes();
        $FrenteAlmacenes->cargarAlmacenesLazzy($resultado);
        $arrObjAlmacenes = $FrenteAlmacenes->getColAlmacenes();
        
	foreach($resultado AS $detalle){
	    $objEstanteria = NEW EstanteriaExtendido();
	    $objEstanteria->cargarme($detalle);
	    $objEstanteria->setObjAlmacen($arrObjAlmacenes[$detalle[almacen_id]]);
	    $this->setColEstanterias($objEstanteria);
	}
    }
    public function buscarEstanterias($arrParametros){

	$this->_db->addSelect('estanteria_id');
	$this->_db->addFrom('datos_estanterias');	

	//**ANALIZO EL WHERE**//

	if($arrParametros['stringBuscar']){

	    $this->_db->addWhere('estanteria_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
	}
	//**where faso = true**/
	if($arrParametros['estanterias'] !=null && $arrParametros['desdeAlta']==null)
	{
	    $this->_db->addWhere('estanteria_id = \'' . $arrParametros['estanterias'] . '\'');
	}
	if($arrParametros['estanteriasEntradas'] !=null && $arrParametros['desdeAlta']==null)
	{
	    $this->_db->addWhere('estanteria_id = \'' . $arrParametros['estanteriasEntradas'] . '\'');
	}
	if($arrParametros['almacenes'] !=null  && $arrParametros['desdeAlta']==null)
	{
	    $this->_db->addWhere('almacen_id = \'' . $arrParametros['almacenes'] . '\'');
	}



	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenEstanterias($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='estanterias'){
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
	    $arrIds[$i]['estanteria_id'] = $arr[$i]['estanteria_id'];
	}
	$this->cargarEstanteriasLazzy($arrIds);
    }
    public function setOrdenEstanterias($arrParametros){
	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != "")
	{
	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'estanteria_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenEstanterias(){
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
	foreach($this->_colEstanterias AS $estanteria){
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $estanteria->getEstanteriaId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $estanteria->getEstanteriaNombre(),
                    $estanteria->getObjAlmacen()->getAlmacenNombre(),
		    $estanteria->getObs(),
                    Array
			(
			"value" => $estanteria->getEstanteriaActiva(),
			"label" =>$estanteria->getEstanteriaActivoMensaje(),
			"select" => 
			    Array 
			    (
				Array(
				"value" =>true,
				"label" =>$estanteria->getEstanteriaActivoDesc()

				),
				Array

				(
				"value" =>false,
				"label" =>$estanteria->getEstanteriaNoActivoDesc()

				)		
			    )
			)
                ),
            );
	    $total++;
	}
	return $arr;
    }
    public function getDetallesAutocompletar(){
	foreach($this->_colEstanterias AS $estanteria){
	 $arr[]   =   array(
            'label' =>  $estanteria->getEstanteriaNombre(),
            'value' =>  $estanteria->getEstanteriaId()
            );
	}		
	return $arr;
    }
    public function salvarMe($arrParametros){
	$estanteriaExtendido = NEW EstanteriaExtendido();
	$estanteriaExtendido->salvarMe($arrParametros);
    }
    public function actualizarMe($arrParametros){
	$estanteriaExtendido = NEW EstanteriaExtendido();
	//$arrParametros[campos][almacen_id] = $this->_objFuncionesComunes->parsearAutocompletar($arrParametros[campos][almacen_nombre]);
	$estanteriaExtendido->actualizarMe($arrParametros);
    }
}
?>