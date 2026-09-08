<?php
/**
 * class DatoIDetalleFacturaVenta
 * 
 */
abstract class DetalleCobro
{
    private $_facturaId;
    private $_cobroId;
    private $_facturaPendiente;
    private $_importeDetalle;
    private $_formaPagoId;
    private $_comprobanteNro;
    private $_bancoId;
    private $_fechaCheque;
    private $_detalleCobroId;
    private $_arrError;
    private $_formaPagoNombre;
    private $_bancoNombre;
    private $_objFactura;
    
    public function setObjFactura($objFactura){
        $this->_objFactura = $objFactura;
    }
    public function getObjCliente(){
        return $this->_objCliente;
    }
    public function setFormaPagoNombre($forma){
        $this->_formaPagoNombre = $forma;
    }
    public function getFormaPagoNombre(){
        return $this->_formaPagoNombre;
    }
    public function setBancoNombre($banco){
        $this->_bancoNombre = $banco;
    }
    public function getBancoNombre(){
        return $this->_bancoNombre;
    }
    public function setCobroId($id){
        $this->_cobroId = $id;
    }
    public function getCobroid(){
        return $this->_cobroId;
    }
    public function setFacturaPendiente($pendiente){
        $this->_facturaPendiente = $pendiente;
    }
    public function getFacturaPendiente(){
        return $this->_facturaPendiente;
    }
    public function setFormaPagoId($formaId){
        $this->_formaPagoId = $formaId;
    }
    public function getFormaPagoid(){
        return $this->_formaPagoId;
    }
    public function setComprobanteNro($comprobanteNro){
        $this->_comprobanteNro = $comprobanteNro;
    }
    public function getComprobanteNro(){
        return $this->_comprobanteNro;
    }
    public function setBancoId($bancoId){
        $this->_bancoId = $bancoId;
    }
    public function getBancoid(){
        return $this->_bancoId;
    }
    public function setFechaCheque($fecha){
        $this->_fechaCheque = $fecha;
    }
    public function getFechaCheque(){
        return $this->_fechaCheque;
    }
    public function setImporteDetalle($importeDetalle){
        $this->_importeDetalle= $importeDetalle;
    }
    public function getImporteDetalle(){
        return $this->_importeDetalle;
    }
    public function setFacturaId($ffacturaId){
        $this->_facturaid= $facturaId;
    }
    public function getFacturaId(){
        return $this->_facturaId;
    }
    public function setArrError ($error){
        $this->_arrError = $error;
    }
    public function getArrError (){
        return $this->_arrError;
    }
    public function setDetalleCobroId($detalleId){
        $this->_detalleCobroId=  $detalleId;
    }
    public function getDetalleCobroId(){
        return $this->_detalleCobroId;
    }
    //metodos comunes
    abstract function cargarMe($id);
    abstract function salvarMe($arrParametros);
    abstract function actualizarMe($arrParametros);

}

?>
