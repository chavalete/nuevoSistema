<?php
/**
 * class DatoIDetalleFacturaVenta
 * 
 */
abstract class DetalleRemito
{
    private $_remitoId;
    private $_objProducto;
    private $_cantidad;
    private $_remitoDetalleId;
    private $_productoId;
    private $_importeDetalle;
    private $_importeUnitario;
    private $_descuento;
    private $_arrError;

    
    public function setRemitoId($remitoId){
        $this->_remitoId = $remitoId;
    }
    public function getRemitoid(){
        return $this->_remitoId;
    }
    public function setImporteDetalle($importeDetalle){
        $this->_importeDetalle = $importeDetalle;
    }
    public function getImporteDetalle(){
        return $this->_importeDetalle;
    }
    public function setImporteUnitario($importeUnitario){
        $this->_importeUnitario = $importeUnitario;
    }
    public function getImporteUnitario(){
        return $this->_importeUnitario;
    }
    public function setProductoId($productoId){
        $this->_productoId= $productoId;
    }
    public function getProductoId(){
        return $this->_productoId;
    }
    public function setObjProductos($objProducto){
        $this->_objProducto = $objProducto;
    }
    public function getObjProductos(){
        return $this->_objProducto;
    }
    public function setCantidad($cantidad){
        $this->_cantidad= $cantidad;
    }
    public function getCantidad(){
        return $this->_cantidad;
    }
    public function setArrError ($error){
        $this->_arrError = $error;
    }
    public function getArrError (){
        return $this->_arrError;
    }
    public function setRemitoDetalleId($remitoDetalleId){
        $this->_remitoDetalleId =  $remitoDetalleId;
    }
    public function getRemitoDetalleId(){
        return $this->_remitoDetalleId;
    }
    public function setDescuento($descuento){
        $this->_descuento = $descuento;
    }
    public function getDescuento(){
        return $this->_descuento;
    }
    //metodos comunes
    abstract function cargarMe($id);
    abstract function salvarMe($arrParametros);
    abstract function actualizarMe($arrParametros);

}

?>
