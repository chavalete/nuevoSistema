<?php

/**
 * Description of PresentacionesProductos
 *
 * @author Guillo
 */
abstract class PresentacionesProductos {
    
    private $_presentacionId;
    private $_productoId;
    private $_gtin;
    private $_unidadVenta;
    private $_unidadDistribucion;
    private $_unidades;
    private $_relacionId;
    private $_relacionActiva;
    private $_relacionUsuarioId;
    private $_relacionUsuarioFechaHora;
    private $_relacionInactivaUsuarioId;
    private $_relacionInactivaFechaHora;

    abstract public function __construct();

    public function setPresentacionId($id){
        $this->_presentacionId = $id;
    }
    
    public function getPresentacionId(){
        return $this->_presentacionId;
    }

    public function setProductoId ($id){
        $this->_productoId = $id;
    }
    
    public function getProductoId(){
        return $this->_productoId;
    }
    
    public function setGtin ($gtin){
        $this->_gtin = $gtin;
    }
    
    public function getGtin(){
        return $this->_gtin;
    }
    
    public function setUnidadVenta ($bool){
        $this->_unidadVenta = $bool;
    }
    
    public function getUnidadVenta(){
        return $this->_unidadVenta;
    }
    
    public function setUnidadDistribucion($bool){
        $this->_unidadDistribucion = $bool;
    }
    
    public function getUnidadDistribucion(){
        return $this->_unidadDistribucion;
    }

    public function setUnidades($unidades)
    {
	$this->_unidades= $unidades;
    }
    
    public function getUnidades()
    {
	return $this->_unidades;
    }
    
    public function setRelacionId ($id){
        $this->_relacionId = $id;
    }
    
    public function getRelacionId(){
        return $this->_relacionId;
    }
    public function setRelacionUsuarioId($relacionUsuarioId){
	$this->_relacionUsuarioId = $relacionUsuarioId;
    }
    public function setRelacionActiva($relacionActiva){
	$this->_relacionActiva = $relacionActiva;
    }
    public function getRelacionActiva(){
	return $this->_relacionActiva;
    }
    public function getRelacionUsuarioId(){
	return $this->_relacionUsuarioId;
    }
    public function setRelacionFechaHora($relacionFechaHora){
	$this->_relacionInactivaFechaHora = $relacionFechaHora;
    }
    public function getRelacionFechaHora(){
	return $this->_relacionFechaHora;
    }
    public function setRelacionInactivaUsuarioId($relacionInactivaUsuarioId){
	$this->_relacionInactivaUsuarioId = $relacionInactivaUsuarioId;
    }
    public function getRelacionInactivaUsuarioId(){
	return $this->_relacionInactivaUsuarioId;
    }
    public function setRelacionInactivaFechaHora($relacionInactivaFechaHora){
	$this->_relacionInactivaFechaHora = $relacionInactivaFechaHora;
    }
    public function getRelacionInactivaFechaHora(){
	return $this->_relacionInactivaFechaHora;
    }
    abstract function cargarMe($relacionId);
    abstract function salvarMe($arrParametros);
    abstract function actualizarMe($arrParametros);
    
}
