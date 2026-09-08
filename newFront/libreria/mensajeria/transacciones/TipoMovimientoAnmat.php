<?php

/**
 * class TipoMovimientoANMAT
 * 
*/

abstract class TipoMovimientoAnmat
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
	private $_tipoMovimientoDe;

    /**
	 * 
	 * @access private
	 */
	private $_arrError ;
    
    //set y get correspondientes
    public function setTipoMovimientoId($id){
        $this->_tipoMovimientoId = $id;
    }
    public function getTipoMovimientoId(){
        return $this->_tipoMovimientoId;
    }

    public function setTipoMovimientoNombre($nombre){
        $this->_tipoMovimientoNombre = $nombre;
    }
    
    public function getTipoMovimientoNombre(){
        return $this->_tipoMovimientoNombre;
    }

    public function setTipoMovimientoDe($tipoMovimientoDe){

	$this->_tipoMovimientoDe = $tipoMovimientoDe;

    }
    public function getTipoMovimientoDe(){

	return $this->_tipoMovimientoDe;
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
