<?php

abstract class Vendedor
{
    private $_vendedorId;
    private $_vendedorNombre;
    private $_vendedorMail;
    private $_obs;
    private $_vendedorActivo;
    private $_vendedorFechaAlta;
    private $_vendedorUsuarioAlta;
    private $_vendedorTelefono;

    public function __construct(){
    }    
    public function setVendedorId($id){
	$this->_vendedorId = $id;
    }
    public function getVendedorId(){
	return $this->_vendedorId;
    }
    public function setVendedorNombre($nombre){
	$this->_vendedorNombre = $nombre;
    }
    public function getVendedorNombre(){
	return $this->_vendedorNombre;
    }
    public function setVendedorMail($vendedorMail){
	$this->_vendedorMail = $vendedorMail;
    }
    public function getVendedorMail(){
	return $this->_vendedorMail;
    }
    public function setObs($obs){
	$this->_obs = $obs;
    }
    public function getObs(){
	return $this->_obs;
    }
    public function setVendedorActivo($activo){
	$this->_vendedorActivo = $activo;
    }
    public function getVendedorActivo(){
	return $this->_vendedorActivo;
    }
    public function setVendedorFechaAlta($fechaAlta){
	$this->_vendedorFechaAlta = $fechaAlta;
    }
    public function getVendedorFechaAlta(){
	return $this->_vendedorFechaAlta;
    }
    public function setVendedorUsuarioAlta($usuarioId) {
	$this->_vendedorUsuarioAlta = $usuarioId;
    }
    public function getVendedorUsuarioAlta(){
	return $this->_vendedorUsuarioAlta;
    }
    public function setVendedorTelefono($vendedorTelefono){
	$this->_vendedorTelefono = $vendedorTelefono;
    }
    public function getVendedorTelefono(){
	return $this->_vendedorTelefono;
    }
    abstract function cargarMe($id);
    abstract function salvarMe($arrParamatros);
    abstract function actualizarMe($arrParamatros);
}