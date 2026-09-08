<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/libreria/Marca.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
CLASE DE MARCAS
*/

class MarcaExtendido extends Marca
{
    private $_marcaActivoMensaje;
    private $_marcaActivoDesc;
    private $_marcaNoActivoDesc;
    
    public function setMarcaActivoDesc($marcaActivoDesc){
	$this->_marcaActivoDesc = $marcaActivoDesc;
    }
    public function getMarcaActivoDesc(){
	return $this->_marcaActivoDesc;
    }
    public function setMarcaNoActivoDesc($marcaNoActivoDesc){
	$this->_marcaNoActivoDesc = $marcaNoActivoDesc;
    }
    public function getMarcaNoActivoDesc(){
	return $this->_marcaNoActivoDesc;
    }
    public function setMarcaActivoMensaje($marcaActivoMensaje){
	$this->_marcaActivoMensaje= $marcaActivoMensaje;
    }
    public function getMarcaActivoMensaje(){
	return $this->_marcaActivoMensaje;
    }
    public function getDataDB($id){
	
	$db_dM->addSelect('marca_id');
	$db_dM->addSelect('marca_nombre');
	$db_dM->addSelect('observaciones');
	$db_dM->addSelect('marca_activa AS marca_activo');
	$db_dM->addSelect('\'Activo\'  AS marca_activo_desc');
	$db_dM->addSelect('\'Inactivo\'  AS marca_no_activo_desc');
	$db_dM->addSelect('CASE WHEN marca_activa THEN \'Activo\' ELSE \'Inactivo\' END AS marca_activo_mensaje');
	
	$db_dM->addFrom('datos_marcas');

	$db_dM->addWhere('marca_id = \'' . $id .'\'' );

	$db_dM->generarSelect();

	$resultado =  $this->_db->ejecutar();
	
	return $resultado[0];
    
    }
    public function cargarMe($datos){
	
	if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}

	$this->setMarcaId($resultado['marca_id']);
	$this->setMarcaNombre($resultado['marca_nombre']);
	$this->setObs($resultado['observaciones']);
	$this->setMarcaActivoDesc($resultado['marca_activo_desc']);
	$this->setMarcaNoActivoDesc($resultado['marca_no_activo_desc']);
	$this->setMarcaActivoMensaje($resultado['marca_activo_mensaje']);
	$this->setMarcaActiva($resultado['marca_activo']);

    }
	
    public function salvarMe($arrParametros){
    
	$db_dM = NEW FrenteAlmacenamiento('dmelmac');

	$db_dM->addCamposTabla('seq_datos_marcas_id');
	$db_dM->generarProximo();

	$id = $db_dM->ejecutar();

	$this->setMarcaId($id[0]['nextval']);
	$this->setMarcaNombre($arrParametros['marcas']);
	$this->setObs($arrParametros['observaciones']);
       
        $db_dM->addFrom('datos_marcas');

        $db_dM->addCamposTabla('marca_id');
        $db_dM->addCamposTabla('marca_nombre');
        $db_dM->addCamposTabla('observaciones');
	

        $db_dM->addCamposValue($this->getMarcaId());
        $db_dM->addCamposValue('\'' . $this->getMarcaNombre() . '\'');
        $db_dM->addCamposValue('\'' . $this->getObs() .'\'');
	
        $db_dM->generarInsert();

	$resultado = $db_dM->ejecutar();

	if($resultado==0){
	    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error marca ingresada con exito'
		    );
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'Marca cargada con exito'
		    );
	}
	echo json_encode($arrDevolver);exit;

    }
	
    public function actualizarMe($arrParametros){

	$db_dM = new FrenteAlmacenamiento('dmelmac');

	$db_dM->addCamposUpdate('marca_nombre = \'' . $arrParametros['campos']['marca_nombre'] .'\'');
	$db_dM->addCamposUpdate('observaciones = \'' . $arrParametros['campos']['observaciones'] .'\'');	
	
	if($arrParametros['campos']['marca_activo'] == 1){
		$db_dM->addCamposUpdate('marca_activa = true');
	}else{
		$db_dM->addCamposUpdate('marca_activa = false');
	}
	$db_dM->addFrom('datos_marcas');
	$db_dM->addWhere('marca_id = \'' . $arrParametros['id'] .'\'');
	    
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

	$db_dM->addSelect('marca_id');
	
	$db_dM->addFrom('datos_marcas');
	
	$db_dM->addWhere('marca_nombre = \'' . $nombre .'\'' );

	$db_dM->generarSelect();

	$arr =  $db_dM->ejecutar();

	return $arr[0]['marca_id'];
    }
}