<?php
/**
 * class DatoIFactura
 * 
 */
abstract class DatoPromocion
{
    private $_promocionId;
    private $_promocionFecha;
    private $_promocionNombre;
    private $_promocionCantidad;
    private $_promocionPrecio;
    private $_subfamiliaId;
    private $_objSubfamilia;
    private $_promocionActiva;
    private $_observaciones;
    private $_subfamiliaNombre;
    private $_fechaVencimiento;
    private $_productoNombre;
    private $_porcentajeDesc;
    private $_fechaInicio;
    private $_idSucursal;

    public function setPorcentajeDesc($porcentaje){
        $this->_porcentajeDesc = $porcentaje;
    }
    public function getPorcentajeDesc(){
        return $this->_porcentajeDesc;
    }
    public function setFechaInicio($fecha){
        $this->_fechaInicio = $fecha;
    }
    public function getFechaInicio(){
        return $this->_fechaInicio;
    }
    public function setIdSucursal($id){
        $this->_idSucursal = $id;
    }
    public function getIdSucursal(){
        return $this->_idSucursal;
    }
    public function setPromocionId($id){
        $this->_promocionId = $id;
    }
    public function getPromocionId(){
        return $this->_promocionId;
    }
    public function setPromocionFecha($fecha){
        $this->_promocionFecha = $fecha;
    }
    public function getPromocionFecha(){
        return $this->_promocionFecha;
    }
    public function setPromocionNombre($nombre){
        $this->_promocionNombre = $nombre;
    }
    public function getPromocionNombre(){
        return $this->_promocionNombre;
    }
    public function setPromocionCantidad($cantidad){
        $this->_promocionCantidad = $cantidad;
    }
    public function getPromocionCantidad(){
        return $this->_promocionCantidad;
    }
    public function setPromocionPrecio($precio){
        $this->_promocionPrecio = $precio;
    }
    public function getPromocionPrecio(){
        return $this->_promocionPrecio;
    }
    public function setSubfamiliaId($subdamiliaId){
        $this->_subfamiliaId = $subdamiliaId;
    }
    public function getSubfamiliaId(){
        return $this->_subfamiliaId;
    }
    public function setObjSubfamilia($obj){
        $this->_objSubfamilia = $obj;
    }
    public function getObjSubfamilia(){
        return $this->_objSubfamilia;
    }
    public function setPromocionActiva($activa){
        $this->_promocionActiva = $activa;
    }
    public function getPromocionActiva(){
        return $this->_promocionActiva;
    }
    public function setObservaciones($obs){
        $this->_observaciones = $obs;
    }
    public function getObservaciones(){
        return $this->_observaciones;
    }
    public function setSubfamiliaNombre($nombre){
        $this->_subfamiliaNombre = $nombre;
    }
    public function getSubfamiliaNombre(){
        return $this->_subfamiliaNombre;
    }
    public function setPromocionVencimiento($fecha){
        $this->_fechaVencimiento = $fecha;
    }
    public function getPromocionVencimiento(){
        return $this->_fechaVencimiento;
    }
    public function setProductoNombre($productoNombre){
        $this->_productoNombre = $productoNombre;
    }
    public function getProductoNombre(){
        return $this->_productoNombre;
    }
    //metodos comunes
    abstract function cargarMe($id);
    abstract function salvarMe($arrParametros);
    abstract function actualizarMe($arrParametros);

}

?>
