<?php

/**
 * Description of Seccion
 *
 * @author Guillo
 */
abstract class Subseccion {
    
    private $_nombreSubseccion;
            
    public function __construct(){
        
    }
    
    public function setNombreSubseccion($param){
        $this->_nombreSubseccion = $param;
    }
    
    public function getNombreSubseccion(){
        return $this->_nombreSubseccion;
    }
    
    abstract function cargarMe($id);
    abstract function actualizarMe($arrParametros);
    abstract function salvarMe($arrParametros);    
}
