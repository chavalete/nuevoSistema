<?php

abstract class MovimientoTrazabilidad
{
    private $_movimientoFechaHora;
    private $_objTipoMovimientoTraza;
    private $_objDatoMovimiento;
    private $_objDetalleMovimiento;
    private $_arrError = Array();
    
   
    
    public function setMovimientoFechaHora($movimientoFechaHora){
        $this->_movimientoFechaHora = $movimientoFechaHora;
    }
    
    public function getMovimientoFechaHora(){
        return $this->_movimientoFechaHora;
    }
    
    public function setObjProducto($objProducto){
        $this->_objProducto = $objProducto;
    }
    
    public function getObjProducto(){
        return $this->_objProducto;
    }
    
    public function setObjTipoMovimiento($objTipoMovimiento){
        $this->_objTipoMovimientoTraza = $objTipoMovimiento;
    }
    
    public function getObjTipoMovimiento(){
        return $this->_objTipoMovimientoTraza;
    }
    
    public function setObjDatoMovimiento($objMovimiento){
        $this->_objDatoMovimiento = $objMovimiento;
    }
    
    public function getObjDatoMovimiento(){
        return $this->_objDatoMovimiento;
    }
    
    public function setObjDetalleMovimiento($objDetalleMovimiento){
        $this->_objDetalleMovimiento = $objDetalleMovimiento;
    }
    
    public function getObjDetalleMovimiento(){
        return $this->_objDetalleMovimiento;
    }
    
    public function setArrError($arrError){
        $this->_arrError = $arrError;
    }
    
    public function getArrError(){
        return $this->_arrError;
    }


    abstract function cargarMe($id);
    abstract function salvarMe();
    abstract function actualizarMe();
    
}