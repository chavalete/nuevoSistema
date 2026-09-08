<?php

    abstract class DatoModelo 
{
    private $_modeloId;
    private $_modeloNombre;
    private $_fechaAlta;
    private $_usuarioId;
    private $_modeloActivo;
    private $_colObjDetalleModelo;
    private $_observaciones;
    
    public function setModeloId($Id){
        $this->_modeloId = $Id;
    }
    public function getModeloId(){
        return $this->_modeloId;
    }
    public function setModeloNombre($modelo){
        $this->_modeloNombre = $modelo;
    }
    public function getModeloNombre(){
        return $this->_modeloNombre;
    }
    public function setFechaAlta($fecha){
        $this->_fechaAlta = $fecha;
    }
    public function getFechaAlta(){
        return $this->_fechaAlta;
    }
    public function setUsuarioId($usu){
        $this->_usuarioId = $usu;
    }
    public function getUsuarioId(){
        return $this->_usuarioId;
    }
    public function setModeloActivo($modelo){
        $this->_modeloActivo = $modelo;
    }
    public function getModeloActivo(){
        return $this->_modeloActivo;
    }
    public function setDetalleModelo($objDetalle){
        $this->_colObjDetalleModelo[] = $objDetalle;
    }
    public function getDetalleModelo(){
        return $this->_colObjDetalleModelo;
    }
    public function setObservaciones($obs){
        $this->_observaciones = $obs;
    }
    public function getObservaciones(){
        return $this->_observaciones;
    }
    abstract public function cargarMe($id);
    abstract public function salvarMe($id);
    abstract public function actualizarMe($id);
    
}
