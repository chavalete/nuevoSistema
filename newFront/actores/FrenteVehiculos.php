<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/VehiculoExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteVehiculos
{
    private $_colVehiculos = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    public function __construct(){
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
	$this->_objFuncionesComunes = new FuncionesComunes();
    }
    public function cargarVehiculo($id){
	
	$this->_colVehiculos[$id] = NEW VehiculoExtendido();
	$this->_colVehiculos[$id]->cargarMe($id);
    }
    public function setColObjVehiculo($objVehiculo){
    
	$this->_colVehiculos[$objVehiculo->getVehiculoId()] = $objVehiculo;
    }
    public function getColObjVehiculo(){
	return $this->_colVehiculos;
    }
    public function cargarVehiculosLazzy($arrIds){
    
	foreach($arrIds as $id){
	    if($id['vehiculo_id']!=NULL){
		$ids.= "'" . $id['vehiculo_id'] . "',";
	    }
	}
	
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
	$this->_db->addSelect('vehiculo_activo AS vehiculo_activo');
	$this->_db->addSelect('\'Activo\'  AS vehiculo_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS vehiculo_no_activo_desc');
	$this->_db->addSelect('CASE WHEN vehiculo_activo THEN \'Activo\' ELSE \'Inactivo\' END AS vehiculo_activo_mensaje');
        $this->_db->addFrom('datos_vehiculoes');
        $this->_db->addWhere("vehiculo_id IN (" . $ids . ")" );
	$this->_db->generarSelect();

	//echo $this->_db->getQry();
	
        $resultado = $this->_db->ejecutar();
        
        
        $creados=0;
        
	if(count($resultado)>0){
	    foreach($resultado AS $detalle){
		$objVehiculo = NEW VehiculoExtendido();
		$objVehiculo->cargarMe($detalle);
		$this->setColObjVehiculo($objVehiculo);
		$creados++;
	    }
	}
	if($creados<count($arrIds)){
	    for($i=$creados;$i<count($arrIds);$i++){
		$objVehiculo = NEW VehiculoExtendido();
		$this->setColObjVehiculo($objVehiculo);
	    }
	}
	
    }
    public function buscarVehiculos($arrParametros){

	$this->_db->addSelect('vehiculo_id');

	$this->_db->addFrom('datos_vehiculos');

	//**ANALIZO EL WHERE**//
	if($arrParametros['stringBuscar']){
    
	    $this->_db->addWhere('vehiculo_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $this->_db->addWhere('vehiculo_activo');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
	}
	//**where faso = true**/
	if($arrParametros['vehiculos'] !=null && $arrParametros['desdeAlta'] == null){
	    
	    $this->_db->addWhere('vehiculo_id = \'' . $arrParametros['vehiculos'] . '\'');
	}
	if($arrParametros['vehiculo_mail'] !=null && $arrParametros['desdeAlta'] == null){
	    
	    $this->_db->addWhere('vehiculo_mail = \'' . $arrParametros[vehiculoMail] . '\'');
	}
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenVehiculos($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='vehiculoes'){
	    
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
	    
	    $this->cargarVehiculo($arr[$i]['vehiculo_id']);
	}
    }
    public function setOrdenVehiculos($arrParametros){

	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'vehiculo_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenVehiculos(){

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

	foreach($this->_colVehiculos AS $vehiculo){

	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $vehiculo->getVehiculoId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $vehiculo->getVehiculoNombre(),
                    $vehiculo->getVehiculoVolumen(),
                    $vehiculo->getObs(),
                    Array
			(
			"value" => $vehiculo->getVehiculoActivo(),
			"label" =>$vehiculo->getVehiculoActivoMensaje(),
			"select" => 
			    Array 
			    (
				Array(
				"value" =>true,
				"label" =>$vehiculo->getVehiculoActivoDesc()

				),
				Array

				(
				"value" =>false,
				"label" =>$vehiculo->getVehiculoNoActivoDesc()

				)		
			    )
			)
                ),
            );
	}		
	return $arr;
    }
    public function getDetallesAutocompletar(){

	foreach($this->_colVehiculos AS $vehiculo){
	 $arr[]   =   array(
            'label' =>  $vehiculo->getVehiculoNombre(),
            'value' =>  $vehiculo->getVehiculoId()
            );
	}		
	return $arr;
    }
    public function salvarMe($arrParametros){

	$vehiculoExtendido = NEW VehiculoExtendido();
	$vehiculoExtendido->salvarMe($arrParametros);
    }

    public function actualizarMe($arrParametros){

	$vehiculoExtendido = NEW VehiculoExtendido();
	$vehiculoExtendido->actualizarMe($arrParametros);
    }
}
?>
