<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mensajeria/errores/ErrorAnmatExtendido.php';


class FrenteErrorAnmatExtendido
{
    private $_db;    
    private $_arrError = Array();
    private $_colObjErrorAnmat=Array();
    
    public function __construct(){
	    
	$this->_db = new FrenteAlmacenamiento();
    }
    
    public function setArrError($arr){
        $this->_arrError = $arr;
    }

    public function getArrError(){
        return $this->_arrError;
    }
    public function setColObjErrorAnmat($obj){
    
	$this->_colObjErrorAnmat[$obj->getErrorId()] = $obj;
    }
    public function getColObjErrorAnmat(){
	return $this->_colObjErrorAnmat;
    }
    public function cargarErrorAnmatLazzy($arrIds){
    
	foreach($arrIds as $id){
	    if($id['error_id']!=NULL){
		$ids.= "'" . $id['error_id'] . "',";
	    }
	}
	
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
        $this->_db->addFrom('relacion_error_id_error_anmat');
        $this->_db->addWhere("error_id IN (" . $ids . ")" );
        $this->_db->addOrderBY('error_id,error_id_anmat DESC');
	$this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        $creados=0;
        
        $antErrorId=NULL;
	if(count($resultado)>0){
	    foreach($resultado AS $detalle){
		if($detalle['error_id'] != $antErrorId){
		    $objErrorAnmat = NEW ErrorAnmatExtendido();
		    $objErrorAnmat ->cargarme($detalle);
		    $this->setColObjErrorAnmat($objErrorAnmat);
		    $creados++;
		}
		$antErrorId = $detalle['error_id'];
	    }
	}
	//echo $creados;exit;
	if($creados<count($arrIds)){
	
	    for($i=$creados;$i<count($arrIds);$i++){

		$objErrorAnmat = NEW ErrorAnmatExtendido();
		$this->setColObjErrorAnmat($objErrorAnmat);
	    }
	}
    }
}