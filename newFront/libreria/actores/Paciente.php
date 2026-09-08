<?php 

abstract class Paciente
{
    private $_pacienteId;
    private $_pacienteNombre;
    private $_obs;
    private $_pacienteActivo;
    private $_pacienteFechaAlta;
    private $_pacienteUsuarioAlta;
    private $_pacienteTelefono;
    private $_nroAfiliado;
    

    public function __construct(){
    }
    public function setPacienteId($id){
	$this->_pacienteId = $id;
    }
    public function getPacienteId(){
	return $this->_pacienteId;
    }
    public function setPacienteNombre($nombre){
	$this->_pacienteNombre = $nombre;
    }
    public function getPacienteNombre(){
	return $this->_pacienteNombre;
    }
    public function setObs($obs){
	$this->_obs = $obs;
    }
    public function getObs(){
	return $this->_obs;
    }
    public function setPacienteActivo($activo){
	$this->_pacienteActivo = $activo;
    }
    public function getPacienteActivo(){
	return $this->_pacienteActivo;
    }
    public function setPacienteFechaAlta($fechaAlta){
	$this->_pacienteFechaAlta = $fechaAlta;
    }
    public function getPacienteFechaAlta(){
	return $this->_pacienteFechaAlta;
    }
    public function setPacienteUsuarioAlta($usuarioId){
	$this->_pacienteUsuarioAlta = $usuarioId;
    }
    public function getPacienteUsuarioAlta(){
	return $this->_pacienteUsuarioAlta;
    }
    public function setNroAfiliado($nroAfiliado){
	$this->_nroAfiliado = $nroAfiliado;
    }
    public function getNroAfiliado(){
	return $this->_nroAfiliado;
    }
    public function setPacienteTelefono($pacienteTelefono){
	$this->_pacienteTelefono = $pacienteTelefono;
    }
    public function getPacienteTelefono(){
	return $this->_pacienteTelefono;
    }
    abstract function cargarMe($id);
    abstract function salvarMe($arrParamametros);
    abstract function actualizarMe($arrParametros);
}