<?php 
abstract class Proveedor
{
    private $_proveId;
    private $_proveNombre;
    private $_proveCuit;
    private $_categoriaId;
    private $_obs;
    private $_proveActivo;
    private $_proveFechaAlta;
    private $_proveUsuarioAlta;
    private $_proveDireccion;
    private $_proveTelefono;
    private $_objCategoria;

    public function __construct(){
    }
    public function setProveId($id){
	$this->_proveId = $id;
    }
    public function getProveId(){
	return $this->_proveId;
    }
    public function setProveNombre($nombre){
	$this->_proveNombre = $nombre;
    }
    public function getProveNombre(){
	return $this->_proveNombre;
    }
    public function setProveCuit($cuit){
	$this->_proveCuit = $cuit;
    }
    public function getProveCuit(){
	return $this->_proveCuit;
    }
    public function setCategoriaId($categoriaId){
	$this->_categoriaId = $categoriaId;
    }
    public function getCategoriaId(){
	return $this->_categoriaId;
    }
    public function setObs($obs){
	$this->_obs = $obs;
    }
    public function getObs(){
	return $this->_obs;
    }
    public function setProveActivo($activo){
	$this->_proveActivo = $activo;
    }
    public function getProveActivo(){
	return $this->_proveActivo;
    }
    public function setProveAlta($fechaAlta){
	$this->_provefechaAlta = $fechaAlta;
    }
    public function getProveAlta(){
	return $this->_proveFechaAlta;
    }
    public function setProveUsuarioAlta($usuarioId){
	$this->_proveUsuarioAlta = $usuarioId;
    }
    public function getProveUsuarioAlta(){
	return $this->_proveUsuarioAlta;
    }
    public function setProveDireccion($direccion){
	$this->_proveDireccion = $direccion;
    }
    public function getProveDireccion(){
	return $this->_proveDireccion;
    }
    public function setProveTelefono($proveTelefono){
	$this->_proveTelefono = $proveTelefono;
    }
    public function getProveTelefono(){
	return $this->_proveTelefono;
    }
    public function setObjCategoria($objCategoria){
	$this->_objCategoria = $objCategoria;
    }
    public function getObjCategoria(){
	return $this->_objCategoria;
    }
    
    abstract function cargarMe($id);
    abstract function salvarMe($arrParametros);
    abstract function actualizarMe($arrParametros);
}