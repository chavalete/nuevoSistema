<?php

/**
 * class CodigoTrazabilidad
 * 
 */
abstract class TrazabilidadCodigo
{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

    /**
	 * 
	 * @access private
	 */
	private $_totalColumnas;

    /**
	 * 
	 * @access private
	 */
	private $_objIndice;

    
    /**
	 * 
	 * @access private
	 */
	private $_codigoId;

	/**
	 * 
	 * @access private
	 */
	private $_trazabilidadCodigo;
    
    private $_arrError;


    public function setCodigoId($id){
        $this->_codigoId = $id;
    } 
    
    public function getCodigoId(){
        return $this->_codigoId;
    }
    
    public function setTrazabilidadCodigo($codigo){
        $this->_trazabilidadCodigo= $codigo;
    }
    
    public function getTrazabilidadCodigo(){
        return $this->_trazabilidadCodigo;
    }

    public function setTotalColumnas($total){
        $this->_totalColumnas = $total;
    }
    
    public function getTotalColumnas(){
        return $this->_totalColumnas;
    }

    public function setObjIndice($obj){
        $this->_objIndice = $obj;
    }
    
    public function getObjIndice(){
        return $this->_objIndice;
    }

    public function setArrError ($error){
        $this->_arrError = $error;
    }
    
    public function getArrError (){
        return $this->_arrError;
    }
    //metodos comunes
    abstract function cargarme($id);
    abstract function salvarme();
    abstract function actualizarme();


} // end of CodigoTrazabilidad

