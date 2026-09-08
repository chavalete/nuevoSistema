<?php
/**
 * class DatoIFactura
 * 
 */
abstract class DatoFactura 
{
    private $_facturaId;
    private $_facturaFecha;
    private $_facturaLetra;
    private $_facturaPuestoVenta;
    private $_facturaNro;
    private $_facturaClienteId;
    private $_facturaObs;
    private $_facturaUsuarioId;
    private $_objCliente;
    private $_colObjDetalleFactura;
    private $_facturaTotal;
    private $_facturaCancelada;
    private $_facturaCanceladaFechaHora;
    private $_facturaCanceladaUsuarioId;
    private $_facturaIva;
    private $_facturaFaltaPagar;
    private $_facturaPaga;
    private $_arrError;
    private $_idEstado;
    private $_objEstado;
    private $_vendedorId;
    private $_objVendedor;
    
    public function setFacturaLetra($letra){
        $this->_facturaLetra = $letra;
    }
    public function getFacturaLetra(){
        return $this->_facturaLetra;
    }
    public function setFacturaPuestoVenta($puesto){
        $this->_facturaPuestoVenta = $puesto;
    }
    public function getFacturaPuestoVenta(){
        return $this->_facturaPuestoVenta;
    }
    public function setFacturaFaltanFact($falta){
        $this->_facturaFaltaPagar = $falta;
    }
    public function getFacturaFaltanFact(){
        return $this->_facturaFaltaPagar;
    }
    public function setFacturaPaga($paga){
        $this->_facturaPaga = $paga;
    }
    public function getFacturaPaga(){
        return $this->_facturaPaga;
    }
    public function setFacturaIva($iva){
        $this->_facturaIva = $iva;
    }
    public function getFacturaIva(){
        return $this->_facturaIva;
    }
    public function setColObjDetalleFactura($objDetalleFactura){
        $this->_colObjDetalleFactura[] = $objDetalleFactura;
    }
    public function getColObjDetalleFactura(){
        return $this->_colObjDetalleFactura;
    }
    public function setFacturaId($facturaId){
        $this->_facturaId = $facturaId;
    }
    public function getFacturaId(){
        return $this->_facturaId;
    }
    public function setFacturaFecha($facturaFecha){
        $this->_facturaFecha= $facturaFecha;
    }
    public function getFacturaFecha(){
        return $this->_facturaFecha;
    }
    public function setFacturaNro($facturaNro){
        $this->_facturaNro = $facturaNro;
    }
    public function getFacturaNro(){
        return $this->_facturaNro;
    }
    public function setFacturaClienteId($facturaClienteId){
        $this->_facturaClienteId= $facturaClienteId;
    }
    public function getFacturaClienteId(){
        return $this->_facturaClienteId;
    }
    public function setObjCliente($objCliente){
        $this->_objCliente = $objCliente;
    }
    public function getObjCliente(){
        return $this->_objCliente;
    }
    public function setFacturaObs($facturaObs){
        $this->_facturaObs = $facturaObs;
    }
    public function getFacturaObs(){
        return $this->_facturaObs;
    }
    public function setFacturaUsuarioId($facturaUsuarioId){
        $this->_facturaUsuarioId = $facturaUsuarioId;
    }
    public function getFacturaUsuarioId(){
        return $this->_facturaUsuarioId;
    }
    public function setArrError ($error){
        $this->_arrError = $error;
    }
    public function getArrError (){
        return $this->_arrError;
    }    
    public function setFacturaTotal($facturaTotal){
        $this->_facturaTotal= $facturaTotal;
    }
    public function getFacturaTotal(){
        return $this->_facturaTotal;
    }
    public function setFacturaCancelada($facturaCancelada){
        $this->_facturaCancelada=  $facturaCancelada;
    }
    public function getFacturaCancelada(){
        return $this->_facturaCancelada;
    }
    public function setFacturaCanceladaFechaHora($facturaCanceladaFechaHora){
        $this->_facturaCanceladaFechaHora=  $facturaCanceladaFechaHora;
    }
    public function getFacturaCanceladaFechaHora(){
        return $this->_facturaCanceladaFechaHora;
    }
    public function setFacturaCanceladaUsuarioId($facturaCanceladaUsuarioId){
        $this->_facturaCanceladaUsuarioId=  $facturaCanceladaUsuarioId;
    }
    public function getFacturaCanceladaUsuarioId(){
        return $this->_facturaCanceladaUsuarioId;
    }
    public function setFacturaEstadoId($facturaEstadoId){
        $this->_idEstado = $facturaEstadoId;
    }
    public function getFacturaEstadoId(){
        return $this->_idEstado;
    }
    public function setObjEstado($objEstado){
        $this->_objEstado = $objEstado;
    }
    public function getObjEstado(){
        return $this->_objEstado;
    }
    public function setVendedorId($vendedorId){
        $this->_vendedorId = $vendedorId;
    }
    public function getVendedorId(){
        return $this->_vendedorId;
    }
    public function setObjVendedor($objVendedor){
        $this->_objVendedor = $objVendedor;
    }
    public function getObjVendedor(){
        return $this->_objVendedor;
    }
    
    
    //metodos comunes
    abstract function cargarMe($id);
    abstract function salvarMe($arrParametros);
    abstract function actualizarMe($arrParametros);

}

?>
