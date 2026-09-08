<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/actores/Vehiculo.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

class VehiculoExtendido extends Vehiculo
{
    private $_vehiculoActivoMensaje;
    private $_vehiculoActivoDesc;
    private $_vehiculoNoActivoDesc;
    
    public function setVehiculoActivoDesc($vehiculoActivoDesc){
	$this->_vehiculoActivoDesc = $vehiculoActivoDesc;
    }
    public function getVehiculoActivoDesc(){
	return $this->_vehiculoActivoDesc;
    }
    public function setVehiculoNoActivoDesc($vehiculoNoActivoDesc){
	$this->_vehiculoNoActivoDesc = $vehiculoNoActivoDesc;
    }
    public function getVehiculoNoActivoDesc(){
	return $this->_vehiculoNoActivoDesc;
    }
    public function setVehiculoActivoMensaje($vehiculoActivoMensaje){
	$this->_vehiculoActivoMensaje= $vehiculoActivoMensaje;
    }
    public function getVehiculoActivoMensaje(){
	return $this->_vehiculoActivoMensaje;
    }
    public function __construct(){
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
    }
    public function getDataDB($id){
	
	$this->_db->addSelect('vehiculo_id');
	$this->_db->addSelect('vehiculo_nombre');
	$this->_db->addSelect('vehiculo_volumen');
	$this->_db->addSelect('vehiculo_activo AS vehiculo_activo');
	$this->_db->addSelect('\'Activo\'  AS vehiculo_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS vehiculo_no_activo_desc');
	$this->_db->addSelect('CASE WHEN vehiculo_activo THEN \'Activo\' ELSE \'Inactivo\' END AS vehiculo_activo_mensaje');
	$this->_db->addSelect('observaciones');
	
	$this->_db->addFrom('datos_vehiculos');

	$this->_db->addWhere('vehiculo_id = \'' . $id .'\'' );

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

	$this->setVehiculoId($resultado['vehiculo_id']);
	$this->setVehiculoNombre($resultado['vehiculo_nombre']);
	$this->setVehiculoVolumen($resultado['vehiculo_volumen']);
	$this->setVehiculoActivoDesc($resultado['vehiculo_activo_desc']);
	$this->setVehiculoNoActivoDesc($resultado['vehiculo_no_activo_desc']);
	$this->setVehiculoActivoMensaje($resultado['vehiculo_activo_mensaje']);
	$this->setVehiculoActivo($resultado['vehiculo_activo']);
	$this->setObs($resultado['observaciones']);
	$this->setVehiculoTelefono($resultado['vehiculo_telefono']);
    }

    public function salvarMe($arrParametros){


	$this->setVehiculoNombre($arrParametros['vehiculos']);
	$this->setVehiculoVolumen($arrParametros['vehiculoVolumen']);
	$this->setObs($arrParametros['observaciones']);

       
        $this->_db->addFrom('datos_vehiculos');

        
        $this->_db->addCamposTabla('vehiculo_nombre');
        $this->_db->addCamposTabla('observaciones');
        $this->_db->addCamposTabla('vehiculo_volumen');
        $this->_db->addCamposTabla('vehiculo_usuario_id');
	

        $this->_db->addCamposValue('\'' . $this->getVehiculoNombre() . '\'');
        $this->_db->addCamposValue('\'' . $this->getObs() .'\'');
        $this->_db->addCamposValue('\'' . $this->getVehiculoVolumen() .'\'');
        $this->_db->addCamposValue($_SESSION['usuarioId']);
	
        $this->_db->generarInsert();

	    //echo $this->_db->getQry();exit;
	$resultado = $this->_db->ejecutar();

	if($resultado==0){
	    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al cargar el vehiculo'
		    );
		echo json_encode($arrDevolver);exit;
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'Vehiculo cargado con exito'
		    );
		echo json_encode($arrDevolver);exit;
	}

    }
    public function actualizarMe($arrParametros){

	$this->_db = new FrenteAlmacenamiento('dmelmac');

	$this->_db->addCamposUpdate('vehiculo_nombre = \'' . $arrParametros['campos']['vehiculo_nombre'] .'\'');
	$this->_db->addCamposUpdate('observaciones = \'' . $arrParametros['campos']['observaciones'] .'\'');	
	$this->_db->addCamposUpdate('vehiculo_volumen= \'' . $arrParametros['campos']['vehiculo_volumen'] . '\''); 
	if($arrParametros['campos']['vehiculo_activo'] == 1){
		$this->_db->addCamposUpdate('vehiculo_activo = true');
	}else{
		$this->_db->addCamposUpdate('vehiculo_activo = false');
	}
	
	$this->_db->addFrom('datos_vehiculos');
	$this->_db->addWhere('vehiculo_id = \'' . $arrParametros['id'] .'\'');
	    
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
