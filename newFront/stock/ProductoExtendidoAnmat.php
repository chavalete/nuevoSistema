<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/stock/Producto.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

/*
CLASE DE PRODUCTOS
*/

class ProductoExtendidoAnmat extends Producto
{
    private $_marcaNombre;
    private $_proveNombre;
    private $_codigoGtin;
    private $_objFuncionesComunes;
    
    public function __construct()
    {
	$this->_objFuncionesComunes = NEW FuncionesComunes();
    }

    public function getDataDB($id){
	
	$db_dM = new FrenteAlmacenamiento();

	$db_dM->addSelect('producto_id');
	$db_dM->addSelect('producto_nombre');
	$db_dM->addSelect('producto_presentacion');
	$db_dM->addSelect('producto_gtin');
	
	$db_dM->addFrom('datos_productos_anmat');
	
	$db_dM->addWhere('producto_gtin = \'' . $id. '\'');
	
	$db_dM->generarSelect();

	$resultado = $db_dM->ejecutar();
	
	return $resultado;
    
    }
    public function cargarme($datos){

	if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}
	$this->setProductoId($resultado[producto_id]);
	$this->setProductoGtin($resultado[producto_gtin]);
	$this->setProductoNombre($resultado[producto_nombre]);
	$this->setProductoPresentacion($resultado[producto_presentacion]);
	
    }
    public function cargarmeByGtin($gtin){
	$db_dM = new FrenteAlmacenamiento();

	$db_dM->addSelect('producto_id');
	$db_dM->addSelect('producto_nombre');
	$db_dM->addSelect('producto_presentacion');
	$db_dM->addSelect('producto_gtin');
	
	$db_dM->addFrom('datos_productos_anmat');
	
	$db_dM->addWhere('producto_gtin = \'' . $gtin. '\'');
	
	$db_dM->generarSelect();

	//echo $db_dM->getQry();exit;

	$arrProducto = $db_dM->ejecutar();


//	var_dump($arrProducto);

	//**agregar objeto marca y proveedor**//

	$this->setProductoId($arrProducto[0][producto_id]);
	$this->setProductoGtin($arrProducto[0][producto_gtin]);
	$this->setProductoNombre($arrProducto[0][producto_nombre]);
	$this->setProductoPresentacion($arrProducto[0][producto_presentacion]);
    
    }
    
    
    public function salvarMe($arrParametros){

	echo "sin codidicar";

    }
    public function actualizarMe($arrParametros){

	$db_dM = new FrenteAlmacenamiento('dmelmac');

	$db_dM->addCamposUpdate('producto_nombre = \'' . $arrParametros['campos']['producto_nombre'] .'\'');
	$db_dM->addCamposUpdate('producto_presentacion = \'' . $arrParametros['campos']['producto_presentacion'] .'\'');
	$db_dM->addCamposUpdate('codigo_referencia = \'' . $arrParametros['campos']['codigo_referencia'] .'\'');
	if(is_numeric($arrParametros['campos']['prove_id'])){
	    $db_dM->addCamposUpdate('prove_id = \'' . $arrParametros['campos']['prove_id'] .'\'');
	}
	if(is_numeric($arrParametros['campos']['marca_id'])){
	    $db_dM->addCamposUpdate('marca_id = \'' . $arrParametros['campos']['marca_id'] .'\'');
	}
	$db_dM->addFrom('datos_productos');
	$db_dM->addWhere('producto_id = \'' . $arrParametros['id'] .'\'');
	    
	//generar qry
	$db_dM->generarUpdate();   
	//asigamos el resulta que es un array a una variable
	$db_dM->ejecutar();
    }
}