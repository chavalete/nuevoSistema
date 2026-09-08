<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/actores/Vendedor.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

class VendedorExtendido extends Vendedor
{
    private $_vendedorActivoMensaje;
    private $_vendedorActivoDesc;
    private $_vendedorNoActivoDesc;
    
    public function setVendedorActivoDesc($vendedorActivoDesc){
	$this->_vendedorActivoDesc = $vendedorActivoDesc;
    }
    public function getVendedorActivoDesc(){
	return $this->_vendedorActivoDesc;
    }
    public function setVendedorNoActivoDesc($vendedorNoActivoDesc){
	$this->_vendedorNoActivoDesc = $vendedorNoActivoDesc;
    }
    public function getVendedorNoActivoDesc(){
	return $this->_vendedorNoActivoDesc;
    }
    public function setVendedorActivoMensaje($vendedorActivoMensaje){
	$this->_vendedorActivoMensaje= $vendedorActivoMensaje;
    }
    public function getVendedorActivoMensaje(){
	return $this->_vendedorActivoMensaje;
    }
    
    public function getDataDb($id){
	
	$db_dM = new FrenteAlmacenamiento('dmelmac');

	$db_dM->addSelect('vendedor_id');
	$db_dM->addSelect('vendedor_nombre');
	$db_dM->addSelect('vendedor_mail');
	$db_dM->addSelect('vendedor_activo AS vendedor_activo');
	$db_dM->addSelect('\'Activo\'  AS vendedor_activo_desc');
	$db_dM->addSelect('\'Inactivo\'  AS vendedor_no_activo_desc');
	$db_dM->addSelect('CASE WHEN vendedor_activo THEN \'Activo\' ELSE \'Inactivo\' END AS vendedor_activo_mensaje');
	$db_dM->addSelect('observaciones');
	$db_dM->addSelect('vendedor_telefono');
	
	$db_dM->addFrom('datos_vendedores');

	$db_dM->addWhere('vendedor_id = \'' . $id .'\'' );

	$db_dM->generarSelect();

	$arr =  $db_dM->ejecutar();

	return $arr[0];
    }
    public function cargarMe($paramentros){
    
	if(is_numeric($paramentros)){
	    $resultado = $this->getDataDB($paramentros);
	}else{
	    $resultado = $paramentros;
	}
	
	$this->setVendedorId($resultado['vendedor_id']);
	$this->setVendedorNombre($resultado['vendedor_nombre']);
	$this->setVendedorMail($resultado['vendedor_mail']);
	$this->setVendedorActivoDesc($resultado['vendedor_activo_desc']);
	$this->setVendedorNoActivoDesc($resultado['vendedor_no_activo_desc']);
	$this->setVendedorActivoMensaje($resultado['vendedor_activo_mensaje']);
	$this->setVendedorActivo($resultado['vendedor_activo']);
	$this->setObs($resultado['observaciones']);
	$this->setVendedorTelefono($resultado['vendedor_telefono']);
    }

    public function salvarMe($arrParametros){

	$db_dM = NEW FrenteAlmacenamiento('dmelmac');

	$db_dM->addCamposTabla('seq_datos_vendedores_vendedor_id');
	$db_dM->generarProximo();

	$id = $db_dM->ejecutar();

	$this->setVendedorId($id[0]['nextval']);
	$this->setVendedorNombre($arrParametros['vendedores']);
	$this->setVendedorMail($arrParametros['vendedorMail']);
	$this->setObs($arrParametros['observaciones']);
	$this->setVendedorTelefono($arrParametros['vendedorTelefono']);

       
        $db_dM->addFrom('datos_vendedores');

        $db_dM->addCamposTabla('vendedor_id');
        $db_dM->addCamposTabla('vendedor_nombre');
        $db_dM->addCamposTabla('observaciones');
        $db_dM->addCamposTabla('vendedor_telefono');
        $db_dM->addCamposTabla('vendedor_mail');
        $db_dM->addCamposTabla('vendedor_usuario_alta');
	

        $db_dM->addCamposValue($this->getVendedorId());
        $db_dM->addCamposValue('\'' . $this->getVendedorNombre() . '\'');
        $db_dM->addCamposValue('\'' . $this->getObs() .'\'');
        $db_dM->addCamposValue('\'' . $this->getVendedorTelefono() .'\'');
        $db_dM->addCamposValue('\'' . $this->getVendedorMail() .'\'');
        $db_dM->addCamposValue($_SESSION['usuarioId']);
	
        $db_dM->generarInsert();

	    //echo $db_dM->getQry();exit;
	$resultado = $db_dM->ejecutar();

	if($resultado==0){
	    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al cargar el vendedor'
		    );
		echo json_encode($arrDevolver);exit;
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'Vendedor cargado con exito'
		    );
		echo json_encode($arrDevolver);exit;
	}

    }
    public function actualizarMe($arrParametros){

	$db_dM = new FrenteAlmacenamiento('dmelmac');

	$db_dM->addCamposUpdate('vendedor_nombre = \'' . $arrParametros['campos']['vendedor_nombre'] .'\'');
	$db_dM->addCamposUpdate('observaciones = \'' . $arrParametros['campos']['observaciones'] .'\'');	
	$db_dM->addCamposUpdate('vendedor_telefono= \'' . $arrParametros['campos']['vendedor_telefono'] . '\'');
	$db_dM->addCamposUpdate('vendedor_mail= \'' . $arrParametros['campos']['vendedor_mail'] . '\''); 
	if($arrParametros['campos']['vendedor_activo'] == 1){
		$db_dM->addCamposUpdate('vendedor_activo = true');
	}else{
		$db_dM->addCamposUpdate('vendedor_activo = false');
	}
	$db_dM->addFrom('datos_vendedores');
	$db_dM->addWhere('vendedor_id = \'' . $arrParametros['id'] .'\'');
	    
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
}
