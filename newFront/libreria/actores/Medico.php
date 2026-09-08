<?php

abstract class Medico
{
    private $_medicoId;
    private $_medicoNombre;
    private $_matriculaNro;
    private $_obs;
    private $_medicoActivo;
    private $_medicoFechaAlta;
    private $_medicoUsuarioAlta;
    private $_medicoTelefono;

    public function __construct(){
    }    
    public function setMedicoId($id){
	$this->_medicoId = $id;
    }
    public function getMedicoId(){
	return $this->_medicoId;
    }
    public function setMedicoNombre($nombre){
	$this->_medicoNombre = $nombre;
    }
    public function getMedicoNombre(){
	return $this->_medicoNombre;
    }
    public function setMatriculaNro($matriculaNro){
	$this->_matriculaNro = $matriculaNro;
    }
    public function getMatriculaNro(){
	return $this->_matriculaNro;
    }
    public function setObs($obs){
	$this->_obs = $obs;
    }
    public function getObs(){
	return $this->_obs;
    }
    public function setMedicoActivo($activo){
	$this->_medicoActivo = $activo;
    }
    public function getMedicoActivo(){
	return $this->_medicoActivo;
    }
    public function setMedicoFechaAlta($fechaAlta){
	$this->_medicoFechaAlta = $fechaAlta;
    }
    public function getMedicoFechaAlta(){
	return $this->_medicoFechaAlta;
    }
    public function setMedicoUsuarioAlta($usuarioId) {
	$this->_medicoUsuarioAlta = $usuarioId;
    }
    public function getMedicoUsuarioAlta(){
	return $this->_medicoUsuarioAlta;
    }
    public function setMedicoTelefono($medicoTelefono){
	$this->_medicoTelefono = $medicoTelefono;
    }
    public function getMedicoTelefono(){
	return $this->_medicoTelefono;
    }
    abstract function cargarMe($id);
    abstract function salvarMe($arrParamatros);
    abstract function actualizarMe($arrParamatros);
}