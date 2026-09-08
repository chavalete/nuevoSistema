<?php
/**
 * class DetalleLiquidacion
 * 
 */
abstract class DetalleLiquidacion
{
    private $_liquidacionId;
    private $_importeDetalle;
    private $_facturaId;
    private $_objFactura;
    private $_arrError;
    
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
    public function setLiquidacionId($id){
        $this->_liquidacionId= $id;
    }
    public function getLiquidacionId(){
        return $this->_liquidacionId;
    }
    public function setArrError ($error){
        $this->_arrError = $error;
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
