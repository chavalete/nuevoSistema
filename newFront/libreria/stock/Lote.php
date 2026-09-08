<?php

abstract class Lote
{
    private $_loteId;
    private $_lote;
    private $_letraGriegaId;
    private $_loteVencimiento;
    private $_loteActivo;
    private $_obs;
    private $_productoId;

    abstract public function __construct();
    
    public function setLoteId($id)
    {
	$this->_loteId = $id;
    }
    public function getLoteid()
    {
	return $this->_loteId;
    }
    public function setLote($lote)
    {
	$this->_lote = $lote;
    }
    public function getLote()
    {
	return $this->_lote;
    }
    public function setLetraGriegaId($letraGriegaId)
    {
	$this->_letraGriegaId = $letraGriegaId;
    }
    public function getLetraGriegaId()
    {
	return $this->_letraGriegaId;
    }
    public function setLoteVencimiento($loteVencimiento)
    {
	$this->_loteVencimiento = $loteVencimiento;
    }
    public function getLoteVencimiento()
    {
	return $this->_loteVencimiento;
    }
    public function setLoteActivo($activo)
    {
	$this->_loteActivo = $activo;
    }
    public function getLoteActivo()
    {
	return $this->_loteActivo;
    }
    public function setObs($obs){
	$this->_obs = $obs;
    }
    public function getObs(){
	return $this->_obs;
    }

    public function setProductoId($id){
	$this->_productoId = $id;
    }
    public function getProductoId(){
	return $this->_productoId;
    }

    
    abstract function cargarMe($id);
    abstract function salvarMe($arrParametros);
    abstract function actualizarMe();
}