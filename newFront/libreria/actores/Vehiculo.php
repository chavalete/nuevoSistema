<?php

abstract class Vehiculo
{
    private $_vehiculoId;
    private $_vehiculoNombre;
    private $_vehiculoVolumen;
    private $_obs;
    private $_vehiculoActivo;
    private $_vehiculoFechaAlta;
    private $_vehiculoUsuarioAlta;
    private $_vehiculoTelefono;

    public function __construct(){
    }    
    public function setVehiculoId($id){
	$this->_vehiculoId = $id;
    }
    public function getVehiculoId(){
	return $this->_vehiculoId;
    }
    public function setVehiculoNombre($nombre){
	$this->_vehiculoNombre = $nombre;
    }
    public function getVehiculoNombre(){
	return $this->_vehiculoNombre;
    }
    public function setVehiculoVolumen($volumen){
	$this->_vehiculoVolumen = $volumen;
    }
    public function getVehiculoVolumen(){
	return $this->_vehiculoVolumen;
    }
    public function setObs($obs){
	$this->_obs = $obs;
    }
    public function getObs(){
	return $this->_obs;
    }
    public function setVehiculoActivo($activo){
	$this->_vehiculoActivo = $activo;
    }
    public function getVehiculoActivo(){
	return $this->_vehiculoActivo;
    }
    public function setVehiculoFechaAlta($fechaAlta){
	$this->_vehiculoFechaAlta = $fechaAlta;
    }
    public function getVehiculoFechaAlta(){
	return $this->_vehiculoFechaAlta;
    }
    public function setVehiculoUsuarioAlta($usuarioId) {
	$this->_vehiculoUsuarioAlta = $usuarioId;
    }
    public function getVehiculoUsuarioAlta(){
	return $this->_vehiculoUsuarioAlta;
    }
    public function setVehiculoTelefono($vehiculoTelefono){
	$this->_vehiculoTelefono = $vehiculoTelefono;
    }
    public function getVehiculoTelefono(){
	return $this->_vehiculoTelefono;
    }
    abstract function cargarMe($id);
    abstract function salvarMe($arrParamatros);
    abstract function actualizarMe($arrParamatros);
}
