<?php

abstract class Distribuidor
{
    private $_distribuidorId;
    private $_distribuidorNombre;
    private $_distribuidorMail;
    private $_obs;
    private $_distribuidorActivo;
    private $_distribuidorFechaAlta;
    private $_distribuidorUsuarioAlta;
    private $_distribuidorTelefono;

    public function __construct(){
    }    
    public function setDistribuidorId($id){
	$this->_distribuidorId = $id;
    }
    public function getDistribuidorId(){
	return $this->_distribuidorId;
    }
    public function setDistribuidorNombre($nombre){
	$this->_distribuidorNombre = $nombre;
    }
    public function getDistribuidorNombre(){
	return $this->_distribuidorNombre;
    }
    public function setDistribuidorMail($distribuidorMail){
	$this->_distribuidorMail = $distribuidorMail;
    }
    public function getDistribuidorMail(){
	return $this->_distribuidorMail;
    }
    public function setObs($obs){
	$this->_obs = $obs;
    }
    public function getObs(){
	return $this->_obs;
    }
    public function setDistribuidorActivo($activo){
	$this->_distribuidorActivo = $activo;
    }
    public function getDistribuidorActivo(){
	return $this->_distribuidorActivo;
    }
    public function setDistribuidorFechaAlta($fechaAlta){
	$this->_distribuidorFechaAlta = $fechaAlta;
    }
    public function getDistribuidorFechaAlta(){
	return $this->_distribuidorFechaAlta;
    }
    public function setDistribuidorUsuarioAlta($usuarioId) {
	$this->_distribuidorUsuarioAlta = $usuarioId;
    }
    public function getDistribuidorUsuarioAlta(){
	return $this->_distribuidorUsuarioAlta;
    }
    public function setDistribuidorTelefono($distribuidorTelefono){
	$this->_distribuidorTelefono = $distribuidorTelefono;
    }
    public function getDistribuidorTelefono(){
	return $this->_distribuidorTelefono;
    }
    abstract function cargarMe($id);
    abstract function salvarMe($arrParamatros);
    abstract function actualizarMe($arrParamatros);
}