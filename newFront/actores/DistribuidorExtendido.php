<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/actores/Distribuidor.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

class DistribuidorExtendido extends Distribuidor
{
    private $_distribuidorActivoMensaje;
    private $_distribuidorActivoDesc;
    private $_distribuidorNoActivoDesc;
    
    public function setDistribuidorActivoDesc($distribuidorActivoDesc){
	$this->_distribuidorActivoDesc = $distribuidorActivoDesc;
    }
    public function getDistribuidorActivoDesc(){
	return $this->_distribuidorActivoDesc;
    }
    public function setDistribuidorNoActivoDesc($distribuidorNoActivoDesc){
	$this->_distribuidorNoActivoDesc = $distribuidorNoActivoDesc;
    }
    public function getDistribuidorNoActivoDesc(){
	return $this->_distribuidorNoActivoDesc;
    }
    public function setDistribuidorActivoMensaje($distribuidorActivoMensaje){
	$this->_distribuidorActivoMensaje= $distribuidorActivoMensaje;
    }
    public function getDistribuidorActivoMensaje(){
	return $this->_distribuidorActivoMensaje;
    }
    public function __construct(){
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
    }
    public function getDataDB($id){
	
	$this->_db->addSelect('distribuidor_id');
	$this->_db->addSelect('distribuidor_nombre');
	$this->_db->addSelect('distribuidor_mail');
	$this->_db->addSelect('distribuidor_activo AS distribuidor_activo');
	$this->_db->addSelect('\'Activo\'  AS distribuidor_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS distribuidor_no_activo_desc');
	$this->_db->addSelect('CASE WHEN distribuidor_activo THEN \'Activo\' ELSE \'Inactivo\' END AS distribuidor_activo_mensaje');
	$this->_db->addSelect('observaciones');
	$this->_db->addSelect('distribuidor_telefono');
	
	$this->_db->addFrom('datos_distribuidores');

	$this->_db->addWhere('distribuidor_id = \'' . $id .'\'' );

	$this->_db->generarSelect();

	$resultado =  $this->_db->ejecutar();
	
	return $resultado[0];
    
    }
    
    public function cargarMe($datos){
	
	if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}

	$this->setDistribuidorId($resultado['distribuidor_id']);
	$this->setDistribuidorNombre($resultado['distribuidor_nombre']);
	$this->setDistribuidorMail($resultado['distribuidor_mail']);
	$this->setDistribuidorActivoDesc($resultado['distribuidor_activo_desc']);
	$this->setDistribuidorNoActivoDesc($resultado['distribuidor_no_activo_desc']);
	$this->setDistribuidorActivoMensaje($resultado['distribuidor_activo_mensaje']);
	$this->setDistribuidorActivo($resultado['distribuidor_activo']);
	$this->setObs($resultado['observaciones']);
	$this->setDistribuidorTelefono($resultado['distribuidor_telefono']);
    }

    public function salvarMe($arrParametros){


	$this->_db->addCamposTabla('seq_datos_distribuidores_distribuidor_id');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();

	$this->setDistribuidorId($id[0]['nextval']);
	$this->setDistribuidorNombre($arrParametros['distribuidores']);
	$this->setDistribuidorMail($arrParametros['distribuidorMail']);
	$this->setObs($arrParametros['observaciones']);
	$this->setDistribuidorTelefono($arrParametros['distribuidorTelefono']);

       
        $this->_db->addFrom('datos_distribuidores');

        $this->_db->addCamposTabla('distribuidor_id');
        $this->_db->addCamposTabla('distribuidor_nombre');
        $this->_db->addCamposTabla('observaciones');
        $this->_db->addCamposTabla('distribuidor_telefono');
        $this->_db->addCamposTabla('distribuidor_mail');
        $this->_db->addCamposTabla('distribuidor_usuario_alta');
	

        $this->_db->addCamposValue($this->getDistribuidorId());
        $this->_db->addCamposValue('\'' . $this->getDistribuidorNombre() . '\'');
        $this->_db->addCamposValue('\'' . $this->getObs() .'\'');
        $this->_db->addCamposValue('\'' . $this->getDistribuidorTelefono() .'\'');
        $this->_db->addCamposValue('\'' . $this->getDistribuidorMail() .'\'');
        $this->_db->addCamposValue($_SESSION['usuarioId']);
	
        $this->_db->generarInsert();

	    //echo $this->_db->getQry();exit;
	$resultado = $this->_db->ejecutar();

	if($resultado==0){
	    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al cargar el distribuidor'
		    );
		echo json_encode($arrDevolver);exit;
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'Distribuidor cargado con exito'
		    );
		echo json_encode($arrDevolver);exit;
	}

    }
    public function actualizarMe($arrParametros){

	$this->_db = new FrenteAlmacenamiento('dmelmac');

	$this->_db->addCamposUpdate('distribuidor_nombre = \'' . $arrParametros['campos']['distribuidor_nombre'] .'\'');
	$this->_db->addCamposUpdate('observaciones = \'' . $arrParametros['campos']['observaciones'] .'\'');	
	$this->_db->addCamposUpdate('distribuidor_telefono= \'' . $arrParametros['campos']['distribuidor_telefono'] . '\'');
	$this->_db->addCamposUpdate('distribuidor_mail= \'' . $arrParametros['campos']['distribuidor_mail'] . '\''); 
	if($arrParametros['campos']['distribuidor_activo'] == 1){
		$this->_db->addCamposUpdate('distribuidor_activo = true');
	}else{
		$this->_db->addCamposUpdate('distribuidor_activo = false');
	}
	
	$this->_db->addFrom('datos_distribuidores');
	$this->_db->addWhere('distribuidor_id = \'' . $arrParametros['id'] .'\'');
	    
	//generar qry
	$this->_db->generarUpdate();   
	//asigamos el resulta que es un array a una variable
	$resultado = $this->_db->ejecutar();
	
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