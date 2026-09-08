<?php
require_once 'MedicamentosDTO.php';

class medicamentosDTOS 
{
    
    private $_colDatosEnviar = array();
    
    public function __construct($arrDatos,$standarDatos){
        $this->agregarDatos($arrDatos,$standarDatos);
    }
    
    public function agregarDatos($arrDatos , $standarDatos){
        //var_dump($arrDatos);exit;
        foreach($arrDatos AS $linea){
            $this->_colDatosEnviar[] = new medicamentosDTO($linea,$standarDatos);
        }
    }
    
    public function getColDatosEnviar(){
        return $this->_colDatosEnviar;
    }
}

?>