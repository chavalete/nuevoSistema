<?php
/**
 * class Remitos
 * 
 */
abstract class DatoRemito 
{
    private $_remitoId;
    private $_proveId;
    private $_clienteId;
    private $_remitoNro;
    private $_facturaId;
    private $_idMovimiento;
    private $_remitoTotal;
    private $_remitoFacturado;
    private $_remitoCancelado;
    private $_remitoFecha;
    private $_objCliente;
    private $_objFactura;
    private $_arrError;
    private $_colObjDetalleRemito;

    
    
    public function setColObjDetalleRemito($objDetalleRemito){
        $this->_colObjDetalleRemito[] = $objDetalleRemito;
    }
    public function getColObjDetalleRemito(){
        return $this->_colObjDetalleRemito;
    }
    public function setIdMovimento($idMovimiento){
        $this->_idMovimiento = $idMovimiento;
    }
    public function getIdMovimiento(){
        return $this->_idMovimiento;
    }
    public function setFacturaId($facturaId){
        $this->_facturaId = $facturaId;
    }
    public function getFacturaId(){
        return $this->_facturaId;
    }
    public function setRemitoTotal($importeTotal){
        $this->_remitoTotal = $importeTotal;
    }
    public function getRemitoTotal(){
        return $this->_remitoTotal;
    }
    public function setRemitoFacturado($remitoFacturado){
        $this->_remitoFacturado = $remitoFacturado;
    }
    public function getRemitoFacturado(){
        return $this->_remitoFacturado;
    }
    public function setRemitoCancelado($remitoCancelado){
        $this->_remitoCancelado = $remitoCancelado;
    }
    public function getRemitoCancelado(){
        return $this->_remitoCancelado;
    }
    /***/
    public function setRemitoId($remitoId){
        $this->_remitoId = $remitoId;
    }
    public function getRemitoId(){
        return $this->_remitoId;
    }
    public function setClienteId($clienteId){
        $this->_clienteId = $clienteId;
    }
    public function getClienteId(){
        return $this->_clienteId;
    }
    public function setRemitoNro($remitoNro){
        $this->_remitoNro = $remitoNro;
    }
    public function getRemitoNro(){
        return $this->_remitoNro;
    }
    public function setRemitoFecha($remitoFecha){
        $this->_remitoFecha = $remitoFecha;
    }
    public function getRemitoFecha(){
        return $this->_remitoFecha;
    }
    public function setObjCliente($objCliente){
        $this->_objCliente = $objCliente;
    }
    public function getObjCliente(){
        return $this->_objCliente;
    }
    public function setArrError ($arrError){
        $this->_arrError = $arrError;
    }
    public function getArrError (){
        return $this->_arrError;
    }
    public function setObjFactura($objFactura){
        $this->_objFactura = $objFactura;
    }
    public function getObjFactura(){
        return $this->_objFactura;
    }
    //metodos comunes
    abstract function cargarMe($id);
    abstract function salvarMe($arrParametros);
    abstract function actualizarMe($arrParametros);

}

?>
    
