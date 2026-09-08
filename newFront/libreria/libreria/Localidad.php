<?php
/*
CLASE DE Localidades
*/
abstract class Localidad
{

    private $_localidadId; /*integer*/
    private $_localidadNombre;/*varchar*/
    private $_localidadActiva;/*boolean default true*/
    private $_localidadFechaAlta;/*date*/
    private $_localidadUsuarioAlta;/*integer*/	
    private $_obs;/*text*/	
    private $_objProvincia;

    public function __construct(){
    }
    public function setLocalidadId($id){
	$this->_localidadId = $id;
    }
    public function getLocalidadId(){
	return $this->_localidadId;
    }
    public function setLocalidadNombre($nombre){
	$this->_localidadNombre = $nombre;
    }
    public function getLocalidadNombre(){
	return $this->_localidadNombre;
    }
    public function setLocalidadActiva($localidadActiva){
	$this->_localidadActiva = $localidadActiva;
    }
    public function getLocalidadActiva(){
	return $this->_localidadActiva;
    }
    public function setLocalidadFechaAlta($fechaAlta){
	$this->_localidadFechaAlta = $fechaAlta;
    }
    public function getLocalidadFechaAlta(){
	return $this->_localidadFechaAlta;
    }
    public function setLocalidadUsuarioAlta($usuarioId){
	$this->_localidadUsuarioAlta = $usuarioId;
    }
    public function getLocalidadUsuarioAlta(){
	return $this->_localidadUsuarioAlta;
    }
    public function setObs($obs){
	$this->_obs = $obs;
    }
    public function getObs(){
	return $this->_obs;
    }
    public function setObjProvincia($objProvincia){
	$this->_objProvincia= $objProvincia;
    }
    public function getObjProvincia(){
	return $this->_objProvincia;
    }
    abstract public function cargarMe($id);
    
    abstract public function salvarMe($arrParamatros);
    
    abstract public function actualizarMe($arrParamatros);
}