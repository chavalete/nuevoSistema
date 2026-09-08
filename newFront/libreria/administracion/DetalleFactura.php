<?php
/**
 * class DatoIDetalleFacturaVenta
 * 
 */
abstract class DetalleFactura
{
    private $_facturaId;
    private $_objProducto;
    private $_cantidad;
    private $_importeUnitario;
    private $_importeDetalle;
    private $_importeIva;
    private $_facturaDetalleId;
    private $_productoId;
    private $_remitoNro;
    private $_arrError;
    
    public function setRemitoNro($remitoNro){
        $this->_remitoNro = $remitoNro;
    }
    public function getRemitoNro(){
        return $this->_remitoNro;
    }
    public function setImporteIva($iva){
        $this->_importeIva = $iva;
    }
    public function getImporteIva(){
        return $this->_importeIva;
    }
    public function setImporteDetalle($importeDetalle){
        $this->_importeDetalle= $importeDetalle;
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
    public function setFacturaId($ffacturaId){
        $this->_facturaid= $facturaId;
    }
    public function getFacturaId(){
        return $this->_facturaId;
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
    public function setDetalleFacturaId($facturaDetalleId){
        $this->_facturaDetalleId=  $facturaDetalleId;
    }
    public function getDetalleFacturaId(){
        return $this->_facturaDetalleId;
    }
    //metodos comunes
    abstract function cargarMe($id);
    abstract function salvarMe($arrParametros);
    abstract function actualizarMe($arrParametros);

}

?>
