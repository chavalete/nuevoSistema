<?php

/**
 * Description of Herramientas
 *
 * @author guillo   
 */

abstract class Herramienta {
    
    private $_nombreHerramientas;
            
    public function __construct(){
        
    }

    public function setNombreHerramientas($param){
        $this->_nombreHerramientas= $param;
    }
    
    public function getNombreHerramientas(){
        return $this->_nombreHerramientas;
    }
    
    
    abstract function cargarMe($id);
    abstract function actualizarMe($arrParametros);
    abstract function salvarMe($arrParametros);    
    
}
