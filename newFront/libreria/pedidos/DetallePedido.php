<?php

/**
 * Description of DetallePedido
 *
 * @author dMELMAC
 */
abstract class DetallePedido
{
    private $_pedidoId;
    private $_objProducto;
    private $_pedidoCantidad;
    private $_pedidoUnidades;
    private $_pedidoDetalleId;
    private $_arrError;
    
    public function setPedidoId($pedidoId){
	$this->_pedidoId = $pedidoId;    
    }
    public function getPedidoId(){
	return $this->_pedidoId;
    }
    public function setObjProducto($objProducto){
	$this->_objProducto = $objProducto;
    }
    public function getObjProducto(){
	return $this->_objProducto;
    }
    public function setPedidoCantidad($pedidoCantidad){
	$this->_pedidoCantidad = $pedidoCantidad;
    }
    public function getPedidoCantidad(){
	return $this->_pedidoCantidad;
    }
    public function setPedidoUnidades($pedidoUni){
	$this->_pedidoUnidades= $pedidoUni;
    }
    public function getPedidoUnidades(){
	return $this->_pedidoUnidades;
    }
    public function setPedidoDetaleId($pedidoDetalleId){
	$this->_pedidoDetalleId = $pedidoDetalleId;
    }
    public function getPedidoDetalleId(){
	return $this->_pedidoDetalleId;
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
