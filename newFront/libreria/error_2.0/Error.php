<?php

/**
 * Description of error
 *
 * @author Dmelmac
 */

class Error2 {
    
    private $_detalle;
    private $_archivo;
        
    public function __construct($arrParametros) {
            $this->_detalle = $arrParametros['detalle'];
            $this->_archivo = $arrParametros['archivo'];
            
     }
     
    public function getDetalle(){
        return $this->_detalle;
    }

    public function getArchivo(){
        return $this->_archivo;
    }
     

}


