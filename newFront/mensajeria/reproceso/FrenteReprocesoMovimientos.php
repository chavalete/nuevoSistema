<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mensajeria/dmelmac/enviarReproceso.php';
require_once 'ReprocesoMovimientos.php';

class FrenteReprocesoMovimientos
{
    private $_db_dM;
    


    public function __construct() {

        $this->_db_dM = new FrenteAlmacenamiento('dmelmac');
    }

    public function identificarMovimiento($arrParametros){
	

	//var_dump($arrParametros);exit;

	if($arrParametros['tipoMovimientoId']=='recepciones'){

	    $objReprocesoMovimientos = NEW ReprocesoMovimientos();
	    $objReprocesoMovimientos->reprocesarRecepciones($arrParametros['movimientos']);
	}else{
	    $objReprocesoMovimientos = NEW ReprocesoMovimientos();
	    $objReprocesoMovimientos->reprocesarDistribuciones($arrParametros['movimientos']);
	}
	$this->enviarReprocesos();

    }
    public function enviarReprocesos(){
    
	$objEnvio = NEW EnviarReproceso();
	$objEnvio->buscarMovimientosReproceso();

    }
    
}
