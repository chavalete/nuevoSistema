<?php
/*
CLASE DE CONDICION DE IVA
*/
abstract class CondicionIva
{

    private $_condicionIvaId; /*integer*/
    private $_descripcion;/*varchar*/
    
    public function __construct()
    {
    }
    public function setCondicionIvaId($id){
	$this->_condicionIvaId = $id;
    }
    public function getCondicionIvaId(){
	return $this->_condicionIvaId ;
    }
    public function setDescripcion($nombre){
	$this->_descripcion = $nombre;
    }
    public function getDescripcion(){
	return $this->_descripcion;
    }
    abstract public function cargarMe($id);
    
    abstract public function salvarMe($arrParamatros);
    
    abstract public function actualizarMe($arrParamatros);
}
