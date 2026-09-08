<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of presentacion
 *
 * @author root
 */
abstract class Presentacion {
    
    private $_presentacionId;
    private $_desc;
    private $_presentacioActiva;
    private $_presentacionFechaHora;
    private $_presentacionUsuarioId;
    private $_presentacionCancelada;
    private $_presentacionCanceladaUsuarioId;
    private $_presentacionCanceladaFechaHora;
    
    abstract public function __construct();
    
    public function setPresentacionId($id){
	$this->_presentacionId = $id;
    }
    public function getPresentacionId(){
	return $this->_presentacionId;
    }
    
    public function setDesc($desc){
	$this->_desc = $desc;
    }
    public function getDesc(){
	return $this->_desc;
    }
    public function setPresentacionActiva($presentacionActiva){
	$this->_presentacioActiva = $presentacionActiva;
    }
    public function getPresentacionActiva(){
	return $this->_presentacioActiva;
    }
    public function setPresentacionFechaHora($presentacionFechaHora){
	$this->_presentacionFechaHora = $presentacionFechaHora;
    }
    public function getPresentacionFechaHora(){
	return $this->_presentacionFechaHora;
    }
    public function setPresentacionUsuarioId($presentacionUsuarioId){
	$this->_presentacionUsuarioId = $presentacionUsuarioId;
    }
    public function getPresentacionUsuarioId(){
	return $this->_presentacionUsuarioId;
    }
    public function setPresentacionCancelada($presentacionCancelada){
	$this->_presentacionCancelada = $presentacionCancelada;
    }
    public function getPresentacionCancelada(){
	return $this->_presentacionCancelada;
    }
    public function setPresentacionCanceladaFechaHora($presentacionCanceladaFechaHora){
	$this->_presentacionCanceladaFechaHora = $presentacionCanceladaFechaHora;
    }
    public function getPresentacionCanceladaFechaHora(){
	return $this->_presentacionCanceladaFechaHora;
    }
    public function setPresentacionCanceladaUsuarioId($presentacionCanceladaUsuarioId){
	$this->_presentacionCanceladaUsuarioId = $presentacionCanceladaUsuarioId;
    }
    public function getPresentacionCanceladaUsuarioId(){
	return $this->_presentacionCanceladaUsuarioId;
    }
    abstract function cargarMe($id);
    abstract function salvarMe($arrParametros);
    abstract function actualizarMe($arrParametros);
    
}
