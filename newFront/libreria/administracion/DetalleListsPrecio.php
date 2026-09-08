<?php
/**
 * class DatoIFacturaCompra
 * 
 */
abstract class DetalleListaPrecio 
{
    private $_listaId;
    private $_productoId;
    private $_productoPventa;
    private $_objProducto;
    private $_descuento;
    
    private $_arrError;
    
    public function setListaId($listaId){
	$this->_listaId = $listaId;
    }
    public function getListaId(){
	return $this->_listaId;
    }
    public function setProductoId($productoId){
	$this->_productoId = $productoId;
    }
    public function getProductoId(){
	return $this->_productoId;
    }
    public function setProductoPventa($productoPventa){
	$this->_productoPventa = $productoPventa;
    }
    public function getProductoPventa(){
	return $this->_productoPventa;
    }
    public function setObjProducto($objProducto){
	$this->_objProducto = $objProducto;
    }
    public function getObjProducto(){
	return $this->_objProducto;
    }
    public function setDescuento($descuento){
        $this->_descuento = $descuento;
    }
    public function getDescuento(){
        return $this->_descuento;
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

}

?>
