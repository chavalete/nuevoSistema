<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'MedicamentosDTOS.php';
require_once 'StandarDatos.php';

class FrenteArmadoDatos
{
    private $_db;
    private $_parametros;
    private $_standarDatos;
    
 
    //le llega un array ya definido con las caracteristicas necesarias y el estandar a utilizar
    public function __construct($arrDatos, $standar) {
               
        $this->_db = new FrenteAlmacenamiento();
        
        $this->_standarDatos = new standarDatos($this->getStandarDatosDB($standar));
        
        if($standar === 'medicamentosDTO'){
            $this->_parametros = new medicamentosDTOS($arrDatos, $this->_standarDatos);
        }
        if($standar === 'medicamentosDTOSerie'){
            $this->_parametros = new medicamentosDTOSeries($arrDatos, $this->_standarDatos);
        }
        if($standar === 'cancelacTransacc'){
            $this->_parametros = new cancelacTransaccs($arrDatos, $this->_standarDatos);
        }
    }
    
    public function getStandarDatosDB($standar){
        
        //tabla de con los parametros que vamos cargar
	    $this->_db->addSelect('*');
	    $this->_db->addFrom($standar);
        
        //generar qry
        $this->_db->generarSelect();
        
        //eL array que resulta lo devolvemos!
        return  $this->_db->ejecutar();
        
    }
    
    public function getColDatosEnviar(){
        return $this->_parametros->getColDatosEnviar();
    }
}
