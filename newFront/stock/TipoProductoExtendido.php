<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/stock/TipoProducto.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
CLASE DE SUCURSALES
*/

class TipoProductoExtendido extends TipoProducto
{
    public function getDataDB($id) {
	$db_dM = new FrenteAlmacenamiento('dmelmac');

	$db_dM->addSelect('tipo_producto_id');
	$db_dM->addSelect('tipo_producto_nombre');
	
	$db_dM->addFrom('datos_tipos_productos');

	$db_dM->addWhere('tipo_producdo_id = \'' . $id .'\'' );

	$db_dM->generarSelect();
        //echo $db_dM->getQry();
	$arr =  $db_dM->ejecutar();

	return $arr[0];
	

    }
    public function cargarMe($datos) {
	
	if(is_numeric($datos)){
		$resultado = $this->getDataDB($datos);
	}else{
		$resultado = $datos;
	}
	$this->setTipoProductoId($resultado['tipo_producto_id']);
	$this->setTipoProductoNombre($resultado['tipo_producto_nombre']);
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