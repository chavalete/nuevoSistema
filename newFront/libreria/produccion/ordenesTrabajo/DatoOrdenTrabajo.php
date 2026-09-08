<?php
/**
 * class DatoIOrdenTrabajo
 * 
 */
abstract class DatoOrdenTrabajo 
{
    private $_ordenId;
    private $_ordenFechaHora;
    private $_ordenNro;
    private $_objEstado;
    private $_objEstanteria;
    private $_ordenObs;
    private $_ordenUsuarioId;
    private $_ordenPreparado;
    private $_ordenPreparadoFechaHora;
    private $_ordenPreparadoUsuarioId;
    private $_ordenCancelado;
    private $_ordenCanceladoFechaHora;
    private $_ordenCanceladoUsuarioId;
    private $_objProductoDestino;
    private $_productoDestinoCantidad;
    private $_lote;
    private $_loteVencimiento;
    private $_arrError;
    private $_colObjDetalleOrdenTrabajo;
    private $_ordenFinalizada;
    private $_ordenFinalizadaFechaHora;
    private $_ordenFinalizadaUsuarioId;
   
    public function setOrdenTrabajoId($ordenId){
   	$this->_ordenId = $ordenId;
    }
    public function getOrdenTrabajoId(){
	return $this->_ordenId;
    }
    public function setOrdenTrabajoFechaHora($ordenFechaHora){
	$this->_ordenFechaHora = $ordenFechaHora;
    }
    public function getOrdenTrabajoFechaHora(){
	return $this->_ordenFechaHora;
    }
    public function setObjProductoDestino($objProductoDestino){
	$this->_objProductoDestino = $objProductoDestino;
    }
    public function getObjProductoDestino(){
	return $this->_objProductoDestino;
    }
    public function setOrdenTrabajoNro($ordenNro){
	$this->_ordenNro = $ordenNro;
    }
    public function getOrdenTrabajoNro(){
	return $this->_ordenNro;
    }
    public function setObjEstado($objEstado){
	$this->_objEstado = $objEstado;
    }
    public function getObjEstado(){
	return $this->_objEstado;
    }
    public function setOrdenTrabajoObs($ordenObs){
	$this->_ordenObs = $ordenObs;
    }
    public function getOrdenTrabajoObs(){
	return $this->_ordenObs;
    }
    public function setOrdenTrabajoUsuarioId($ordenUsuarioId){
	$this->_ordenUsuarioId = $ordenUsuarioId;
    }
    public function getOrdenTrabajoUsuarioId(){
	return $this->_ordenUsuarioId;
    }
    public function setOrdenTrabajoPreparado($ordenPreparado){
	$this->_ordenPreparado = $ordenPreparado;
    }
    public function getOrdenTrabajoPreparado(){
	return $this->_ordenPreparado;
    }
    public function setOrdenTrabajoPreparadoFechaHora($ordenPreparadoFechaHora){
	$this->_ordenPreparadoFechaHora = $ordenPreparadoFechaHora;
    }
    public function getOrdenTrabajoPreparadoFechaHora(){
	return $this->_ordenPreparadoFechaHora;
    }
    public function setOrdenTrabajoPreparadoUsuarioId($ordenPreparadoUsuarioId){
	$this->_ordenPreparadoUsuarioId = $ordenPreparadoUsuarioId;
    }
    public function getOrdenTrabajoPreparadoUsuarioId(){
	return $this->_ordenPreparadoUsuarioId;
    }
    public function setOrdenTrabajoCancelado($ordenCancelado){
	$this->_ordenCancelado = $ordenCancelado;
    }
    public function getOrdenTrabajoCancelado(){
	return $this->_ordenCancelado;
    }
    public function setOrdenTrabajoCanceladoFechaHora($ordenCanceladoFechaHora){
	$this->_ordenCanceladoFechaHora = $ordenCanceladoFechaHora;
    }
    public function getOrdenTrabajoCanceladoFechaHora(){
	return $this->_ordenCanceladoFechaHora;
    }
    public function setOrdenTrabajoCanceladoUsuarioId($ordenCanceladoUsuarioId){
	$this->_ordenCanceladoUsuarioId = $ordenCanceladoUsuarioId;
    }
    public function getOrdenTrabajoCanceladoUsuarioId(){
	return $this->_ordenCanceladoUsuarioId;
    }
    public function setArrError ($error){
        $this->_arrError = $error;
    }
    public function setColObjDetalleOrdenTrabajo($objDetalleOrdenTrabajo){
        
	$this->_colObjDetalleOrdenTrabajo[] = $objDetalleOrdenTrabajo;
    }
    
    public function getColObjDetalleOrdenTrabajo(){
        
	return $this->_colObjDetalleOrdenTrabajo;
    }
    public function setObjEstanteria($objEstanteria){
	$this->_objEstanteria = $objEstanteria;
    }
    public function getObjEstanteria(){
	return $this->_objEstanteria;
    }
    public function setProductoDestinoCantidad($cantidad){
	$this->_productoDestinoCantidad = $cantidad;
    }
    public function getProductoDestinoCantidad(){
	return $this->_productoDestinoCantidad;
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
    public function setOrdenFinalizada($ordenFinalizada){
	$this->_ordenFinalizada = $ordenFinalizada;
    }
    public function getOrdenFinalizada(){
	return $this->_ordenFinalizada;
    }
    public function getArrError (){
        return $this->_arrError;
    }    
    //metodos comunes
    abstract function cargarMe($id);
    abstract function salvarMe($arrParametros);
    abstract function actualizarMe($arrParametros);

}

?>
