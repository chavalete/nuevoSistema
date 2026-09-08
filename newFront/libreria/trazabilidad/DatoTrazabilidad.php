<?php
/**
 * class DatoTrazabilidad
 * 
 */

abstract class DatoTrazabilidad
{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/


    /**
	 * 
	 * @access private
	 */
	private $_trazabilidadId;

	/**
	 * 
	 * @access private
	 */
	private $_trazabilidadCodigo;

	/**
	 * 
	 * @access private
	 */
	private $_trazabilidadFechaAlta;

	/*
	 * 
	 * @access private
	 */
	private $_objSucursal;

	/**
	 * 
	 * @access private
	 */
	private $_objAlmacen;

	/**
	 * 
	 * @access private
	 */
	private $_objEstanteria;

	/**
	 * 
	 * @access private
	 */
	private $_objProducto;

	/**
	 * 
	 * @access private
	 */
	private $_objLote;
    
	/**
	 * 
	 * @access private
	 */    
        private $_enStock;

        /**
	 * 
	 * @access private
	 */
	private $_esAgrupador;

        

    public function setTrazabilidadId($id){
        $this->_trazabilidadId = $id;
    }

    public function getTrazabilidadId(){
        return $this->_trazabilidadId;
    }
    
    public function setTrazabilidadCodigo($codigo){
        $this->_trazabilidadCodigo = $codigo;
    }
    
    public function getTrazabilidadCodigo(){
        return $this->_trazabilidadCodigo;
    }
    
    public function setTrazabilidadFechaAlta($date){
        $this->_trazabilidadFechaAlta = $date;
    }
    public function getTrazabilidadFechaAlta(){
    
	return $this->_trazabilidadFechaAlta;
    }
    
    public function setObjSucursal($obj){
        $this->_objSucursal = $obj;
    }
    
    public function getObjSucursal(){
        return $this->_objSucursal;
    }

    public function setObjAlmacen($obj){
        $this->_objAlmacen = $obj;
    }
    
    public function getObjAlmacen(){
        return $this->_objAlmacen;
    }
    
    public function setObjEstanteria($obj){
        $this->_objEstanteria = $obj;
    }
    
    public function getObjEstanteria(){
        return $this->_objEstanteria;
    }
    
    public function setObjProducto($obj){
        $this->_objProducto = $obj;
    }
    
    public function getObjProducto(){
        return $this->_objProducto;
    }
    
    public function setObjLote($obj){
        $this->_objLote = $obj;
    }
    
    public function getObjLote(){
        return $this->_objLote;
    }
    
    
    public function setEnStock($bool){
        $this->_enStock = $bool;
    }
    
    public function getEnStock(){
        return $this->_enStock;
    }
    
    public function setEsAgrupador($bool){
        $this->_esAgrupador = $bool;
    }
    
    public function getEsAgrupador(){
        return $this->_esAgrupador;
    }
    
    public function setArrError ($error){
        $this->_arrError = $error;
    }
    
    public function getArrError (){
        return $this->_arrError;
    }
    
    public function setTrazabilidadPropia($bool ){
        $this->_trazabilidadPropia = $bool;
    }
    
    public function getTrazabilidadPropia(){
        return $this->_trazabilidadPropia;
    }
    

    //metodos comunes
    abstract function cargarme($id);
    abstract function salvarme($arrParametros);
    abstract function actualizarme();
    
} // end of DatoTrazabilidad
