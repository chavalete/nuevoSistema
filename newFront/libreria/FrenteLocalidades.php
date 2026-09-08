<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/LocalidadExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteLocalidades
{
    private $_colLocalidades = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    public function __construct(){

	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
	$this->_objFuncionesComunes = new FuncionesComunes();
    }
    public function cargarLocalidad($id){

	$this->_colLocalidades[$id] = NEW LocalidadExtendido();
	$this->_colLocalidades[$id]->cargarMe($id);
    }
    public function setColObjLocalidades($objLocalidad){
    
	$this->_colLocalidades[$objLocalidad->getLocalidadId()] = $objLocalidad;
    }
    public function getColObjLocalidades(){
	return $this->_colLocalidades;
    }
    public function cargarLocalidadesLazzy($arrIds){
    
	foreach($arrIds as $id){
            if($id['localidad_id']!=NULL){
                $ids.= "'" . $id['localidad_id'] . "',";
            }else{
                $ids.= "'-1',";
            }
	}
	
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
	$this->_db->addSelect('localidad_prov_id AS provincia_id');
	$this->_db->addSelect('localidad_activa AS localidad_activo');
	$this->_db->addSelect('\'Activa\'  AS localidad_activo_desc');
	$this->_db->addSelect('\'Inactiva\'  AS localidad_no_activo_desc');
	$this->_db->addSelect('CASE WHEN localidad_activa THEN \'Activo\' ELSE \'Inactivo\' END AS localidad_activo_mensaje');
        $this->_db->addFrom('datos_localidades');
        $this->_db->addWhere("localidad_id IN (" . $ids . ")" );
	$this->_db->generarSelect();

	//echo $this->_db->getQry();exit;
	
        $resultado = $this->_db->ejecutar();
        
        //var_dump($resultado);exit;
        $FrenteProvincias = NEW FrenteProvincias();
        
        $FrenteProvincias->cargarProvinciasLazzy($resultado);
        
        $arrObjProvincias= $FrenteProvincias->getColObjProvincias();
        
	foreach($resultado AS $detalle){
	    $objLocalidad = NEW LocalidadExtendido();
	    $objLocalidad->cargarMe($detalle['localidad_id']);
	    //var_dump($detalle);
            $objLocalidad->setObjProvincia($arrObjProvincias[$detalle[localidad_prov_id]]);
	    $this->setColObjLocalidades($objLocalidad);
	    
	}
    }
    
    public function buscarLocalidades($arrParametros){

	

	$this->_db->addSelect('localidad_id');
	
	$this->_db->addFrom('datos_localidades');

	//**ANALIZO EL WHERE**//

	if($arrParametros['stringBuscar']){
	    $this->_db->addWhere('localidad_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
	}
	if($arrParametros['localidades'] !=null && $arrParametros['desdeAlta']==null){
	    $this->_db->addWhere('localidad_id = \'' . $arrParametros['localidades'] . '\'');
	}
	if($arrParametros['provincias'] !=null && $arrParametros['desdeAlta']==null){
	    $this->_db->addWhere('localidad_prov_id = \'' . $arrParametros['provincias'] . '\'');
	}
	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenLocalidades($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='localidades'){

	    $this->_db->addLimit(100);
	}
	//*FIN LIMIT*//
	$this->_db->generarSelect();

	//echo $this->_db->getQry();
	
	$arr =  $this->_db->ejecutar();
	
	if(count($arr)==0){
	    return;
	}
	
	//ahora seteamos el total y la pagina
	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro){
	    
	    $ultimoRegistro = $this->getTotal();
	}	

	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
	    $arrIds[$i]['localidad_id'] = $arr[$i]['localidad_id'];
	}
	$this->cargarLocalidadesLazzy($arrIds);
    }
    public function setOrdenLocalidades($arrParametros){

	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'localidad_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenLocalidades(){

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

	foreach($this->_colLocalidades AS $localidad){
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $localidad->getLocalidadId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $localidad->getLocalidadNombre(),
                    $localidad->getObjProvincia()->getProvinciaNombre(),
		    $localidad->getObs(),
                    Array
			(
			"value" => $localidad->getLocalidadActiva(),
			"label" =>$localidad->getLocalidadActivoMensaje(),
			"select" => 
			    Array 
			    (
				Array(
				"value" =>true,
				"label" =>$localidad->getLocalidadActivoDesc()

				),
				Array

				(
				"value" =>false,
				"label" =>$localidad->getLocalidadNoActivoDesc()

				)		
			    )
			)
                ),
            );
	}		
	return $arr;
    }
    public function getDetallesAutocompletar(){

	foreach($this->_colLocalidades AS $localidad){
	 $arr[]   =   array(
            'label' =>  $localidad->getLocalidadNombre() . "-(" .$localidad->getObjProvincia()->getProvinciaNombre() .")",
            'value' => $localidad->getLocalidadId()
            );
	}		
	return $arr;
    }
    public function salvarMe($arrParametros){
	$localidadExtendido = NEW LocalidadExtendido();
	$localidadExtendido->salvarMe($arrParametros);
    }
    public function actualizarMe($arrParametros){
	$localidadExtendido = NEW LocalidadExtendido();
	$localidadExtendido->actualizarMe($arrParametros);
    }
}
?>
