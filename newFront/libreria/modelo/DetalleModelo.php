<?php

abstract class DetalleModelo 
{
    private $_modeloId;
    private $_display;
    private $_name;
    private $_align;
    private $_ordenNro;
    private $_detalleModeloId;
    private $_sortable;
    private $_class;
    private $_required;
    private $_editable;
    private $_modeloActivo;   
    private $_contenedor = Array();//
    
    public function __construct(){
    }
    public function setModeloId($modeloId){
	$this->_modeloId = $modeloId;
    }
    public function getModeloId(){
	return $this->_modeloId;
    }
    public function setDisplay($display){
	$this->_display = $display;
    }
    public function getDisplay(){
	return $this->_display;
    }
    public function setName($name){
	$this->_name = $name;
    }
    public function getName(){
	return $this->_name;
    }
    public function setAlign($align){
	$this->_align = $align;
    }
    public function getAlign(){
	return $this->_align;
    }
    public function setOrdenNro($ordenNro){
	$this->_ordenNro = $ordenNro;
    }
    public function getOrdenNro(){
	return $this->_ordenNro;
    }
    public function setDetalleModeloId($detalleId) {
	$this->_detalleModeloId = $detalleId;
    }
    public function getDetalleModeloId(){
	return $this->_detalleModeloId;
    }
    public function setSortable($sortable){
	$this->_sortable = $sortable;
    }
    public function getSortable(){
	return $this->_sortable;
    }
    public function setClass($class){
	$this->_class = $class;
    }
    public function getClass(){
	return $this->_class;
    }
    public function setRequired($required){
	$this->_required= $required;
    }
    public function getRequired(){
	return $this->_required;
    }
    public function setEditable($editable){
	$this->_editable = $editable;
    }
    public function getEditable(){
	return $this->_editable;
    }
    public function setModeloActivo($modelo){
	$this->_modeloActivo= $modelo;
    }
    public function getModeloActivo(){
	return $this->_modeloActivo;
    }
    public function setContenedor(){
        $this->_contenedor['modelo_id'] = &$this->getModeloId();
        $this->_contenedor['display'] = &$this->getDisplay();
        $this->_contenedor['name'] = &$this->getName();
        $this->_contenedor['sortable'] = &$this->getSortable();
        $this->_contenedor['align'] = &$this->getAlign();
        $this->_contenedor['editable'] = &$this->getEditable();
        $this->_contenedor['orden_nro'] = &$this->getOrdenNro();
        $this->_contenedor['detalle_modelo_id'] = &$this->getDetalleModeloId();
        $this->_contenedor['class'] =  $this->getClass();
        $this->_contenedor['required'] =  $this->getRequired();
    }
    public function getContenedor(){
	return $this->_contenedor;
    }
    
    abstract public function cargarMe($id);
    abstract public function salvarMe($id);
    abstract public function actualizarMe($id);
}
?>
