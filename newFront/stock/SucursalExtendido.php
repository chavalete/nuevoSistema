<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/stock/Sucursal.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
CLASE DE SUCURSALES
*/

class SucursalExtendido extends Sucursal
{
    public function cargarMe($id)
    {
	$db_dM = new FrenteAlmacenamiento('dmelmac');

	$db_dM->addSelect('sucursal_id');
	$db_dM->addSelect('sucursal_nombre');
	$db_dM->addSelect('observaciones');
	$db_dM->addSelect('CASE WHEN sucursal_activa THEN \'Activa\' ELSE \'Inactiva\' END AS sucursal_activa');
	
	$db_dM->addFrom('datos_sucursales');

	$db_dM->addWhere('sucursal_id = \'' . $id .'\'' );

	$db_dM->generarSelect();
        //echo $db_dM->getQry();
	$arr =  $db_dM->ejecutar();

	$this->setSucursalId($arr['0']['sucursal_id']);
	$this->setSucursalNombre($arr['0']['sucursal_nombre']);
	$this->setObs($arr['0']['observaciones']);
	$this->setSucursalActiva($arr['0']['sucursal_activa']);

    }
	
    public function salvarMe($arrParametros)
    {
	$db_dM = NEW FrenteAlmacenamiento('dmelmac');

	$db_dM->addCamposTabla('seq_datos_sucursales_id');
	$db_dM->generarProximo();

	$id = $db_dM->ejecutar();

	$this->setSucursalId($id[0]['nextval']);
	$this->setSucursalNombre($arrParametros['sucursales']);
	$this->setObs($arrParametros['observaciones']);
       
        $db_dM->addFrom('datos_sucursales');

        $db_dM->addCamposTabla('sucursal_id');
        $db_dM->addCamposTabla('sucursal_nombre');
        $db_dM->addCamposTabla('observaciones');
	

        $db_dM->addCamposValue($this->getSucursalId());
        $db_dM->addCamposValue('\'' . $this->getSucursalNombre() . '\'');
        $db_dM->addCamposValue('\'' . $this->getObs() .'\'');
	
        $db_dM->generarInsert();

	$resultado = $db_dM->ejecutar();

	if($resultado==0){
	    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al cargar la sucursal'
		    );
		echo json_encode($arrDevolver);exit;
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'Sucursal cargada con exito'
		    );
		echo json_encode($arrDevolver);exit;
	}

    }
	
    public function actualizarMe($arrParametros){
	
	$db_dM = new FrenteAlmacenamiento('dmelmac');

	$db_dM->addCamposUpdate('sucursal_nombre = \'' . $arrParametros['campos']['sucursal_nombre'] .'\'');
	$db_dM->addCamposUpdate('observaciones = \'' . $arrParametros['campos']['observaciones'] .'\'');	
	$db_dM->addFrom('datos_sucursales');
	$db_dM->addWhere('sucursal_id = \'' . $arrParametros['id'] .'\'');
	    
	//generar qry
	$db_dM->generarUpdate();   
	//asigamos el resulta que es un array a una variable
	$resultado = $db_dM->ejecutar();
	
	if(count($resultado)> 0){
		$arrDevolver = array(
		    "soyError" => false,
		    "nivel" => 0,
		    "mensaje" =>"Actualizado"
		    );
	}else{

		$arrDevolver = array(
		    "soyError" => true,
		    "nivel" => 1,
		    "mensaje" =>"Error en la actualizacion"
		    );
	}
	echo json_encode($arrDevolver);
    }
    public function buscarPorNombre($nombre){

	$db_dM = new FrenteAlmacenamiento('dmelmac');

	$db_dM->addSelect('sucursal_id');
	
	$db_dM->addFrom('datos_sucursales');
	
	$db_dM->addWhere('sucursal_nombre = \'' . $nombre .'\'' );

	$db_dM->generarSelect();

	$arr =  $db_dM->ejecutar();

	return $arr[0]['sucursal_id'];
    }
}