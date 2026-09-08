<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/PresentacionExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

/*
CLASE DE PRESENTACIONES
*/

class FrentePresentaciones
{
    private $_colPresentacion = Array();
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;

    public function __construct() {
	$this->_objFuncionesComunes = new FuncionesComunes();
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
    }
    public function cargarPresentacion($id){

	$this->_colPresentacion[$id] = PresentacionExtendido::cargarMe($id);
	//$this->_colProductos->cargarMe($id);	
    }
    
    public function setColPresentaciones($objPresentacion){
	$this->_colPresentacion[$objPresentacion->getPresentacionId()] = $objPresentacion;
    }
    
    public function getColPresentaciones(){
	return $this->_colPresentacion;
    }
    
    public function cargarPresentacionesLazzy($arrIds){
    
	foreach($arrIds as $id){
	    if($id['presentacion_id']!=NULL){
		$ids.= "'" . $id['presentacion_id'] . "',";
	    }
	}
	$ids=substr($ids, 0, -1);
	
        $this->_db->addSelect('*');
        $this->_db->addSelect('dp.presentacion_activa AS presentacion_activo');
	$this->_db->addSelect('\'Activa\'  AS presentacion_activo_desc');
	$this->_db->addSelect('\'Inactiva\'  AS presentacion_no_activo_desc');
	$this->_db->addSelect('CASE WHEN dp.presentacion_activa THEN \'Activa\' ELSE \'Inactiva\' END AS presentacion_activo_mensaje');
	
        $this->_db->addFrom('datos_presentaciones dp');
       
        $this->_db->addWhere("presentacion_id IN (" . $ids . ")" );
	
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
	foreach($resultado AS $detalle){
	    
	    $objPresentacion= NEW PresentacionExtendido();
	    $objPresentacion->cargarme($detalle);
	    $this->setColPresentaciones($objPresentacion);
	}
    }

    public function buscarPresentaciones($arrParametros){

	$this->_db->addSelect('presentacion_id');
	
	$this->_db->addFrom('datos_presentaciones');

	//**ANALIZO EL WHERE**//

	if($arrParametros['stringBuscar']){
	    $this->_db->addWhere('descripcion ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 18;
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
	}

	//**where faso = true**/
	if($arrParametros['presentaciones'] !=null){
	    $this->_db->addWhere('presentacion_id = \'' . $arrParametros['presentaciones'] . '\'');
	}
	if($arrParametros['presentacion_cancelada'] !=0){
	    $this->_db->addWhere('presentacion_cancelada =  \'' . $arrParametros['presentacionCancelada'] . '\'');
	}
        
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenPresentaciones($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/

	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='presentaciones'){
	    $this->_db->addLimit(100);
	}
	//*FIN LIMIT*//
	$this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
	$arr =  $this->_db->ejecutar();

	//ahora seteamos el total y la pagina
	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);

	if($this->getTotal() < $ultimoRegistro){
	    $ultimoRegistro = $this->getTotal();
	}	

	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
	    $arrIds[$i]['presentacion_id'] = $arr[$i]['presentacion_id'];
	}
        //var_dump($arrIds);
	$this->cargarPresentacionesLazzy($arrIds);
    }
    
    
    
    public function setOrdenPresentaciones($parametros) {

	if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'descripcion';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenPresentaciones(){
	
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

	foreach($this->_colPresentacion AS $presentacion){

	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $presentacion->getPresentacionId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $presentacion->getDesc(),		    
                    Array
			(
			"value" => $presentacion->getPresentacionActiva(),
			"label" =>$presentacion->getPresentacionActivoMensaje(),
			"select" => 
			    Array 
			    (
				Array(
				"value" =>true,
				"label" =>$presentacion->getPresentacionActivoDesc()

				),
				Array

				(
				"value" =>false,
				"label" =>$presentacion->getPresentacionNoActivoDesc()

				)		
			    )
			)
                ),
            );
	}		
	return $arr;
    }
    
    public function getDetallesAutocompletar(){

	foreach($this->_colPresentacion AS $presentacion){
                $arr[]=   
                    array(
                        'label' => $presentacion->getDesc(),
                        'value' => $presentacion->getPresentacionId()
                    );
            }		
	return $arr;
    }
    
    public function salvarMe($arrParametros) {

	$presentacionExtendido = NEW presentacionExtendido();
	$presentacionExtendido->salvarMe($arrParametros);
    }

    public function actualizarMe($arrParametros) {

	$presentacionExtendido = NEW presentacionExtendido();
	$presentacionExtendido->actualizarMe($arrParametros);
    }

}
?>
