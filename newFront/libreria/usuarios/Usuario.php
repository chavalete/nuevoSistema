<?php
/**
 * Description of Usuarios
 * Para menejo de usuarios y en forma de coleccion
 * @author chava
 */
abstract class Usuario 
{
    private $_id;
    private $_nombreCompleto;
    private $_nombreUsuario;
    private $_pwd;
    private $_fechaAlta;
    private $_esPrivilegiado;
    private $_ultimoAcceso;
    private $_activo;
    private $_fechaInactivacion;
    private $_usuarioBas;
    private $_subseccionInicio;

    public function __construct(){
        
    }

    public function setUsuarioId($id){
	$this->_id = $id;
    }
    public function getUsuarioId(){
	return $this->_id;
    }
    public function setNombreUsuario($nombreUsuario){
        $this->_nombreUsuario = $nombreUsuario;
    }
    public function getNombreUsuario(){
        return $this->_nombreUsuario;
    }
    public function setPwd($pwd){
        $this->_pwd = $pwd;
    }
    public function getPwd(){
        return $this->_pwd;
    }
    public function setNombreCompleto($nombreCompleto){
        $this->_nombreCompleto = $nombreCompleto;
    }
    public function getNombreCompleto(){
        return $this->_nombreCompleto;
    }
    public function setFechaAlta($fecha){
        $this->_fechaAlta = $fecha;
    }
    public function getFechaAlta(){
        return $this->_fechaAlta;
    }
    public function setActivo($activo){
        $this->_activo = $activo;
    }
    public function getActivo(){
        return $this->_activo;
    }
    public function setFechaInactivacion(){
        $this->_fechaInactivacion = date("Y-m-d H:i:s");
    }
    public function getFechaInactivacion(){
        return $this->_fechaInactivacion;
    }
    public function setUltimoAcceso(){
        $this->_ultimoAcceso = date("Y-m-d H:i:s");
    }
    public function getUltimoAcceso(){
        return $this->_ultimoAcceso;
    }
    public function setEsPrivilegiado($esPrivilegiado){
        $this->_esPrivilegiado = $esPrivilegiado;
    }
    public function getEsPrivilegiado(){
        return $this->_esPrivilegiado;
    }
    public function setUsuarioBas($usuario){
        $this->_usuarioBas = $usuario;
    }
    public function getUsuarioBas(){
        return $this->_usuarioBas;
    }

    public function setSubseccionInicio($sub){
        $this->_subseccionInicio = $sub;
    }
    public function getSubseccionInicio(){
        return $this->_subseccionInicio;
    }

    abstract function cargarMe($id);
    abstract function actualizarMe($arrParametros);
    abstract function salvarMe($arrParametros);
}
