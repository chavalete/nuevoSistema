<?php
/*
CLASE DE PRODUCTOS
*/

abstract class Producto
{
    private $_productoId; /*integer*/
    private $_productoNombre;/*varchar*/
    private $_productoPresentacion;/*varchar*/
    private $_marcaId;/*intenger*/
    private $_proveId;/*intenger*/
    private $_productoActivo;/*boolean default true*/
    private $_productoFechaAlta;/*date*/
    private $_productoUsuarioAlta;/*integer*/
    private $_productoGtin;/*integer*/
    private $_objProveedor;
    private $_objMarca;
    private $_objTipoProducto;
    private $_unidades;
    private $_forma;
    private $_productoPcosto;
    private $_productoPrecio;

    
    
    public function __construct()
    {
    }
    public function setProductoId($id){
	$this->_productoId = $id;
    }
    public function getProductoId(){
	return $this->_productoId;
    }
    public function setProductoNombre($nombre){
	$this->_productoNombre = $nombre;
    }
    public function getProductoNombre(){
	return $this->_productoNombre;
    }
    public function setProductoPresentacion($presentacion){
	$this->_productoPresentacion = $presentacion;
    }
    public function getProductoPresentacion(){
	return $this->_productoPresentacion;
    }
    public function setMarcaId($marcaId){
	$this->_marcaId = $marcaId;
    }
    public function getMarcaId(){
	return $this->_marcaId;
    }
    public function setProveId($proveId){
	$this->_proveId = $proveId;
    }
    public function getProveId(){
	return $this->_proveId;
    }
    public function setProductoActivo($productoActivo){
	$this->_productoActivo = $productoActivo;
    }
    public function getProductoActivo(){
	return $this->_productoActivo;
    }
    public function setProductoAlta($fechaAlta){
	$this->_productoFechaAlta = $fechaAlta;
    }
    public function getProductoAlta(){
	return $this->_productoFechaAlta;
    }
    public function setProductoUsuarioAlta($usuarioId){
	$this->_productoUsuarioAlta = $usuarioId;
    }
    public function getProductoUsuarioAlta() {
	return $this->_productoUsuarioAlta;
    }
    public function setProductoGtin($productoGtin){
	$this->_productoGtin = $productoGtin;
    
    }
    public function getProductoGtin(){
	return $this->_productoGtin;
    }
    public function setObjProveedor($objProveedor){
	$this->_objProveedor = $objProveedor;
    }
    public function getObjProveedor(){
	return $this->_objProveedor;
    }
    public function setObjMarca($objMarca){
	$this->_objMarca = $objMarca;
    }
    public function getObjMarca(){
	return $this->_objMarca;
    }
    public function setObjTipoProducto($objTipoProducto){
	$this->_objTipoProducto = $objTipoProducto;
    }
    public function getObjTipoProducto(){
	return $this->_objTipoProducto;
    }
    
    public function setUnidades($unidades){
	$this->_unidades = $unidades;
    }
    public function getUnidades(){
	return $this->_unidades;
    }
    
    public function setForma($forma){
	$this->_unidades = $forma;
    }
    
    public function getForma(){
	return $this->_forma;
    }
    public function setProductoPrecio($productoPrecio){
	$this->_productoPrecio = $productoPrecio;
    }
    public function getProductoPrecio(){
	return $this->_productoPrecio;
    }
    public function setProductoPcosto($productoPcosto){
	$this->_productoPcosto = $productoPcosto;
    }
    public function getProductoPcosto(){
	return $this->_productoPcosto;
    }
    
    abstract public function cargarMe($id);
    
    abstract public function salvarMe($arrParametros);
    
    abstract public function actualizarMe($arrParametros);
}