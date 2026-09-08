<?php
/**
 * Description of Botonera
 *
 * @author root
 */

abstract class Botonera {
    
    private $_nombreBoton;
            
    public function __construct(){
        
    }

    public function setNombreBoton($param){
        $this->_nombreBoton = $param;
    }
    
    public function getNombreBoton(){
        return $this->_usuarioId;
    }
    
    abstract function cargarMe($id);
    abstract function actualizarMe($arrParametros);
    abstract function salvarMe($arrParametros);    

}
