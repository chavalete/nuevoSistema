<?php

/**
 * Description of Seccion
 *
 * @author Guillo
 */
abstract class Seccion {
    
    private $_nombreSeccion;
    
    public function setNombreSubseccion($param){
        $this->_nombreSeccion = $param;
    }
    
    public function getNombreSubseccion(){
        return $this->_nombreSeccion;
    }
    
    public function __construct(){}
    
    
    abstract function cargarMe($id);
    abstract function actualizarMe($arrParametros);
    abstract function salvarMe($arrParametros);    
}
