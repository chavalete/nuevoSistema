<?php

abstract class DetalleTransaccion
{       

    private $_detalleId;
    private $_finDetalle;
    private $_trazabilidadCodigo;
    private $_lote;
    private $_loteVencimiento;
    private $_transaccionId;
    private $_objProducto;
    private $_deMensajeId;
    private $_codigoTransaccion;
    
    
    public function __construct() {
        
    }
    
    public function setDetalleTransaccionId($detalleId){
        $this->_detalleId = $detalleId;
    }
    
    public function getDetalleTransaccionId(){
	
	return $this->_detalleId;
    }
    public function setTransaccionId($id){
	
	$this->_transaccionId = $id;
    }
    public function getTransaccionId(){
    
	return $this->_transaccionId;
    }
    public function setTrazabilidadCodigo($trazabilidadCodigo){

	$this->_trazabilidadCodigo = $trazabilidadCodigo;
    }
    public function getTrazabilidadCodigo(){

	return $this->_trazabilidadCodigo;
    }
    public function setLote($lote){

	$this->_lote = $lote;
    }
    public function getLote(){

	return $this->_lote;
    }
    public function setLoteVencimiento($loteVencimiento){

	$this->_loteVencimiento = $loteVencimiento;
    }

    public function getLoteVencimiento(){

	return $this->_loteVencimiento;
    }

    public function setObjProducto($objProducto){
    
	$this->_objProducto = $objProducto;
    }

    public function getObjProducto(){

	return $this->_objProducto;
    }

    public function setFinDetalle( boolean $finDetalle){
        $this->_finDetalle = $finDetalle;
    }

    public function setDeMensajeId($deMensajeId){

	$this->_deMensajeId = $deMensajeId;
    }
    public function getDeMensajeId(){

	return $this->_deMensajeId;
    }

    public function cargarMe($id){

    }
    
    public function salvarMe($arrParametros){

    }
    public function actualizarMe($arrParametros){

    }
}
