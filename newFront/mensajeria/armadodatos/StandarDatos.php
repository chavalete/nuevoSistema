<?php
require_once 'StandarDato.php';
/*
 * Para armar un objeto con los parametros
 * @author chava
 */
class standarDatos
{
    private $_colStandares;

    public function __construct($standar){
        $this->agregarStandar($standar);
    }
    
    //seteo de datos
    public function agregarStandar($standar){
        foreach($standar AS  $parametro){
            $this->_colStandares[] = new standarDato($parametro);
        }
    }
    
    public function getColStandares(){
        return $this->_colStandares;
    }

}

?>