<?php

abstract class Cliente
{
    private $_clienteId;
    private $_clienteNombre;
    private $_clienteCuit;
    private $_categoriaId;
    private $_obs;
    private $_clienteActivo;
    private $_clienteFechaAlta;
    private $_clienteUsuarioAlta;
    private $_clienteDireccion;
    private $_clienteTelefono;
    private $_clienteContacto;
    private $_clienteRespInsc;
    private $_clienteGln;
    private $_clienteCalle;
    private $_clienteAltura;
    private $_clientePiso;
    private $_clienteDepto;
    private $_clienteCodidoPostal;
    private $_objLocalidad;
    private $_clienteLocalidad;
    private $_contendor = Array();
    private $_objCategoria;
    private $_objVendedor;
    private $_vendedorId;

    public function __construct()
    {
    }
    public function setClienteDireccion($clienteDireccion){
	$this->_clienteDireccion = $clienteDireccion;
	
    }
    public function getClienteDireccion(){
	return $this->_clienteDireccion;
    }
    public function setClienteTelefono($clienteTelefono){
	$this->_clienteTelefono = $clienteTelefono;
    }
    public function getClienteTelefono(){
	return $this->_clienteTelefono;
    }
    public function setClienteContacto($clienteContacto){
	$this->_clienteContacto = $clienteContacto;
    }
    public function getClienteContacto(){
	return $this->_clienteContacto;
    }
    public function setClienteRespInsc($clienteRespInsc){
	$this->_clienteRespInsc = $clienteRespInsc;
    }
    public function getClienteRespInsc(){
	return $this->_clienteRespInsc;
    }
    public function setClienteId($id){
	$this->_clienteId = $id;
    }
    public function getClienteId(){
	return $this->_clienteId;
    }
    public function setClienteNombre($nombre){
	$this->_clienteNombre = $nombre;
    }
    public function getClienteNombre(){
	return $this->_clienteNombre;
    }
    public function setClienteCuit($cuit) {
	$this->_clienteCuit = $cuit;
    }
    public function getClienteCuit(){
	return $this->_clienteCuit;
    }
    public function setCategoriaId($categoriaId){
	$this->_categoriaId = $categoriaId;
    }
    public function getCategoriaId() {
	return $this->_categoriaId;
    }
    public function setObs($obs) {
	$this->_obs = $obs;
    }
    public function getObs(){
	return $this->_obs;
    }
    public function setClienteActivo($activo){
	$this->_clienteActivo = $activo;
    }
    public function getClienteActivo(){
	return $this->_clienteActivo;
    }
    public function setClienteAlta($fechaAlta){
	$this->_clienteFechaAlta = $fechaAlta;
    }
    public function getClienteAlta(){
	return $this->_clienteFechaAlta;
    }
    public function setClienteUsuarioAlta($usuarioId){
	$this->_clienteUsuarioAlta = $usuarioId;
    }
    public function getClienteUsuarioAlta(){
	return $this->_clienteUsuarioAlta;
    }
    public function setObjCategoria ($obj){
        $this->_objCategoria = $obj;
    }    
    public function getObjCategoria (){
        return $this->_objCategoria;
    }
    public function setClienteGln($gln){
	$this->_clienteGln = $gln;
    }
    public function getClienteGln(){
	return $this->_clienteGln;
    }
    public function setClienteCalle($calle){
	$this->_clienteCalle = $calle;
    }
    public function getClienteCalle(){
	return $this->_clienteCalle;
    }
    public function setClienteAltura($altura){
	$this->_clienteAltura = $altura;
    }
    public function getClienteAltura(){
	return $this->_clienteAltura;
    }
    public function setClientePiso($piso){
	$this->_clientePiso = $piso;
    }
    public function getClientePiso(){
	return $this->_clientePiso;
    }
    public function setClienteDepto($depto){
	$this->_clienteDepto = $depto;
    }
    public function getClienteDepto(){
	return $this->_clienteDepto;
    }
    public function setClienteCodigoPostal($codigoPostal){
	$this->_clienteCodidoPostal = $codigoPostal;
    }
    public function getClienteCodigoPostal(){
	return $this->_clienteCodidoPostal;
    }
    public function setClienteLocalidad($localidadId){
	$this->_clienteLocalidad = $localidadId;
    }
    public function getClienteLocalidad(){
	return $this->_clienteLocalidad;
    }
    public function setObjLocalidad ($obj){
        $this->_objLocalidad = $obj;
    }    
    public function getObjLocalidad(){
        return $this->_objLocalidad;
    }
    
    public function setVendedorId($vendedorId){
	$this->_vendedorId= $vendedorId;
    }
    public function getVendedorId(){
	return $this->_vendedorId;
    }
    public function setObjVendedor ($obj){
        $this->_objVendedor = $obj;
    }    
    public function getObjVendedor(){
        return $this->_objVendedor;
    }
    
    abstract function cargarMe($id);
    abstract function salvarMe($parametros);
    abstract function actualizarMe($arrParametros);
}