<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/remesas/Remesa.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

class RemesaExtendido extends Remesa
{
    function cargarme($id){
	
	$db_dM = new FrenteAlmacenamiento('dmelmac');

	$db_dM->addSelect('remesa_id');
	$db_dM->addSelect('remesa_nombre');
	$db_dM->addSelect('remesa_nro');
	$db_dM->addSelect('to_char(remesa_fecha_hora_importacion::date , \'DD-MM-YYYY\') AS fecha_importacion');
	
	$db_dM->addFrom('datos_remesas');

	$db_dM->addWhere('remesa_id = \'' . $id .'\'' );

	$db_dM->generarSelect();

	$arr =  $db_dM->ejecutar();

	$this->setRemesaId($arr['0']['remesa_id']);
	$this->setRemesaNombre($arr['0']['remesa_nombre']);
	$this->setFechaImportacion($arr['0']['fecha_importacion']);
	$this->setRemesaNro($arr['0']['remesa_nro']);
    }
    function salvarMe($arrParametros){
    }
    function actualizarMe($arrParametros){
    
    }
}