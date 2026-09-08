<?php
/**
 * class DatoILiquidacion
 * 
 */
abstract class DatoLiquidacion
{
    private $_liquidacionId;
    private $_liquidacionFecha;
    private $_liquidacionFechaDesde;
    private $_liquidacionFechaHasta;
    private $_observaciones;
    private $_liquidacionUsuarioId;
    private $_colObjDetalleLiquidacion;
    private $_liquidacionTotal;
    private $_liquidacionCancelada;
    private $_liquidacionCanceladaFechaHora;
    private $_liquidacionCanceladaUsuarioId;
    private $_arrError;
    private $_vendedorId;
    private $_objVendedor;
    
    
    public function setColObjDetalleLiquidacion($objDetalleLiquidacion){
        $this->_colObjDetalleLiquidacion[] = $objDetalleLiquidacion;
    }
    public function getColObjDetalleLiquidacion(){
        return $this->_colObjDetalleLiquidacion;
    }
    public function setLiquidacionId($liquidacionId){
        $this->_liquidacionId = $liquidacionId;
    }
    public function getLiquidacionId(){
        return $this->_liquidacionId;
    }
    public function setLiquidacionFecha($liquidacionFecha){
        $this->_liquidacionFecha= $liquidacionFecha;
    }
    public function getLiquidacionFecha(){
        return $this->_liquidacionFecha;
    }
    public function setLiquidacionFechaDesde($liquidacionFechaDesde){
        $this->_liquidacionFechaDesde= $liquidacionFechaDesde;
    }
    public function getLiquidacionFechaDesde(){
        return $this->_liquidacionFechaDesde;
    }
    public function setLiquidacionFechaHasta($liquidacionFechaHasta){
        $this->_liquidacionFechaHasta= $liquidacionFechaHasta;
    }
    public function getLiquidacionFechaHasta(){
        return $this->_liquidacionFechaHasta;
    }
    public function setLiquidacionImporte($total){
        $this->_liquidacionTotal = $total;
    }
    public function getLiquidacionImporte(){
        return $this->_liquidacionTotal;
    }
    public function setLiquidacionCancelada($cancelada){
        $this->_liquidacionCancelada = $cancelada;
    }
    public function getLiquidacionCancelada(){
        return $this->_liquidacionCancelada;
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
    public function setObservaciones($observaciones){
        $this->_observaciones = $observaciones;
    }
    public function getObservaciones(){
        return $this->_observaciones;
    }
    //metodos comunes
    abstract function cargarMe($id);
    abstract function salvarMe($arrParametros);
    abstract function actualizarMe($arrParametros);

}

?>
