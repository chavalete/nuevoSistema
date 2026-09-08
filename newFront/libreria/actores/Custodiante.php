<?php
/**
 * Description of custodiante
 *
 * @author guillo
 */
abstract class Custodiante {
    
    private $_custodianteId;
    private $_custodianteNombreCompleto;
    private $_custodianteMail;
    private $_obs;
    private $_custodianteActivo;
    private $_custodianteFechaHoraAlta;
    private $_custodianteUsuarioAlta;
    private $_custodianteTelefono;

    public function __construct(){
    }    
    public function setCustodianteId($id){
	$this->_custodianteId = $id;
    }
    public function getCustodianteId(){
	return $this->_custodianteId;
    }
    public function setCustodianteNombreCompleto($nombre){
	$this->_custodianteNombreCompleto = $nombre;
    }
    public function getCustodianteNombreCompleto(){
	return $this->_custodianteNombreCompleto;
    }
    public function setCustodianteMail($custodianteMail){
	$this->_custodianteMail = $custodianteMail;
    }
    public function getCustodianteMail(){
	return $this->_custodianteMail;
    }
    public function setObs($obs){
	$this->_obs = $obs;
    }
    public function getObs(){
	return $this->_obs;
    }
    public function setCustodianteActivo($activo){
	$this->_custodianteActivo = $activo;
    }
    public function getCustodianteActivo(){
	return $this->_custodianteActivo;
    }
    public function setCustodianteFechaAlta($fechaAlta){
	$this->_custodianteFechaAlta = $fechaAlta;
    }
    public function getCustodianteFechaAlta(){
	return $this->_custodianteFechaAlta;
    }
    public function setCustodianteUsuarioAlta($usuarioId) {
	$this->_custodianteUsuarioAlta = $usuarioId;
    }
    public function getCustodianteUsuarioAlta(){
	return $this->_custodianteUsuarioAlta;
    }
    public function setCustodianteTelefono($custodianteTelefono){
	$this->_custodianteTelefono = $custodianteTelefono;
    }
    public function getCustodianteTelefono(){
	return $this->_custodianteTelefono;
    }
    abstract function cargarMe($id);
    abstract function salvarMe($arrParamatros);
    abstract function actualizarMe($arrParamatros);

}
