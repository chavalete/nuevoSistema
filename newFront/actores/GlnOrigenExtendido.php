<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/actores/Gln.php';

class GlnOrigenExtendido EXTENDS gln
{
    private $_db;
    
    public function __construct(){

	$this->_db = NEW FrenteAlmacenamiento();
    }
    public function getDataDB($gln){
	
	$this->_db->addSelect('gln');
	$this->_db->addSelect('gln_nombre');
	$this->_db->addSelect('gln_cuit');
	$this->_db->addSelect('tipo_agente');

	$this->_db->addFrom('datos_gln');

	$this->_db->addWhere('gln = \'' . $gln . '\'');

	$this->_db->generarSelect();

	$arrGln = $this->_db->ejecutar();

	//verificamos que el movimiento exista en la db
        if(count($arrGln) == 0){
            $errorDetalle = 'El gln enviado no existe';
            $errorArchivo = __FILE__;
            
        }
    
    }
    public function cargarme($datos){

	if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}
	
	$this->setGln($resultado[gln]);
	$this->setGlnNombre($resultado[gln_nombre]);
	$this->setGlnCuit($resultado[glnCuit]);

    }
    public function salvarme(){}
    public function actualizarme(){}
}