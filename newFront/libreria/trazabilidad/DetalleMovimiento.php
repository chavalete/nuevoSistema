<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DetalleMovimiento
 *
 * @author root
 */
abstract class DetalleMovimiento
{
    
    private $_detalleId;
    private $_idMovimiento;
    private $_objSucursal;
    private $_objAlmacen;
    private $_objEstanteria;
    private $_objProducto;
    private $_objLote;
    private $_cantidad;
    private $_unidades;
    private $_sucursalId;
    
    private $_colObjDatoTraza = Array();
    
    private $_arrError;

    public function setIdMovimiento ($id){
        $this->_idMovimiento = $id;
    }
    
    public function getIdMovimiento (){
        return $this->_idMovimiento;
    }
    
    public function setDetalleId($id){
        $this->_detalleId = $id;
    }
    
    public function getDetalleId (){
        return $this->_detalleId;
    }
    
    public function setObjSucursal ($obj){
        $this->_objSucursal = $obj;
    }
    
    public function getObjSucursal (){
        return $this->_objSucursal;
    }
    
    public function setObjAlmacen ($obj){
        $this->_objAlmacen = $obj;
    }
    
    public function getObjAlmacen (){
        return $this->_objAlmacen;
    }
    
    public function setObjEstanteria ($obj){
        $this->_objEstanteria = $obj;
    }
    
    public function getObjEstanteria (){
        return $this->_objEstanteria;
    }
    
    public function setObjProducto ($obj){
        $this->_objProducto = $obj;
    }
    
    public function getObjProducto (){
        return $this->_objProducto;
    }
    
    public function setObjLote ($obj){
        $this->_objLote = $obj;
    }
    
    public function getObjLote (){
        return $this->_objLote;
    }
    
    public function setCantidad ($cantidad){
        $this->_cantidad = $cantidad;
    }
    
    public function getCantidad (){
        return $this->_cantidad;
    }
    
    public function setUnidades($unidades){
        $this->_unidades = $unidades;
    }
    
    public function getUnidades (){
        return $this->_unidades;
    }
    
    public function setColObjDatoTraza($obj){
        $this->_colObjDatoTraza[] = $obj;
    }
    
    public function getColObjDatoTraza(){
        return $this->_colObjDatoTraza;
    }


    public function setArrError ($error){
        $this->_arrError = $error;
    }
    public function setSucursalId($id){
        $this->_sucursalId = $id;
    }
    public function getSucursalId(){
        return $this->_sucursalId;
    }
    public function getArrError (){
        return $this->_arrError;
    }    
    //metodos comunes
    abstract function cargarMe($id);
    abstract function salvarMe($arrParametros);
    abstract function actualizarMe();

//put your code here
}

?>
