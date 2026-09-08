<?php
/**
 * class TipoMovimientoTraza
 * 
 */

abstract class TipoMovimientoTraza
{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * 
	 * @access private
	 */
	private $_tipoMovimientoId;

	/**
	 * 
	 * @access private
	 */
	private $_tipoMovimientoNombre;

    	/**
	 * 
	 * @access private
	 */
	private $_tipoMovimientoAlta;

    /**
	 * 
	 * @access private
	 */
	private $_arrError ;
    
    //set y get correspondientes
    public function setTipoMovimientoId($id){
        $this->_tipoMovimientoId = $id;
    }
    
    public function setTipoMovimientoNombre($nombre){
        $this->_tipoMovimientoNombre = $nombre;
    }
    
    public function setTipoMovimientoALta($bool){
        $this->_tipoMovimientoAlta = $bool;
    }

    public function getTipoMovimientoId(){
        return $this->_tipoMovimientoId;
    }
    
    public function getTipoMovimientoNombre(){
        return $this->_tipoMovimientoNombre;
    }
    
    public function getTipoMovimientoALta(){
        return $this->_tipoMovimientoAlta;
    }
    
    public function setArrError($error){
        $this->_arrError[] = $error;
    }

    public function getArrError(){
        return $this->_arrError;
    }
    
    //metodos comunes
    abstract function cargarMe($id);
    abstract function salvarMe();
    abstract function actualizarMe();

} // end of TipoMovimientoTraza
?>
