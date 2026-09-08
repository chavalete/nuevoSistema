<?php

/**
 * Description of DetalleOrdenTrabajo
 *
 * @author dMELMAC
 */
abstract class DetalleOrdenTrabajo
{
    private $_ordenId;
    private $_objProducto;
    private $_ordenCantidad;
    private $_ordenDetalleId;
    private $_arrError;
    
    public function setOrdenTrabajoId($ordenId){
	$this->_ordenId = $ordenId;    
    }
    public function getOrdenTrabajoId(){
	return $this->_ordenId;
    }
    public function setObjProducto($objProducto){
	$this->_objProducto = $objProducto;
    }
    public function getObjProducto(){
	return $this->_objProducto;
    }
    public function setOrdenTrabajoCantidad($ordenCantidad){
	$this->_ordenCantidad = $ordenCantidad;
    }
    public function getOrdenTrabajoCantidad(){
	return $this->_ordenCantidad;
    }
    public function setOrdenTrabajoDetaleId($ordenDetalleId){
	$this->_ordenDetalleId = $ordenDetalleId;
    }
    public function getOrdenTrabajoDetalleId(){
	return $this->_ordenDetalleId;
    }
    public function setArrError ($error){
        $this->_arrError = $error;
    }
    public function getArrError (){
        return $this->_arrError;
    }    
    //metodos comunes
    abstract function cargarMe($id);
    abstract function salvarMe($arrParametros);
    abstract function actualizarMe($arrParametros);

//put your code here
}

?>
