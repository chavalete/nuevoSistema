<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/libreria/Provincia.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
CLASE DE CATEGORIAS
*/

class ProvinciaExtendido extends Provincia
{
    private $_provinciaActivoMensaje;
    private $_provinciaActivoDesc;
    private $_provinciaNoActivoDesc;
    
    
    public function __construct(){
	
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
    
    }
    
    public function setProvinciaActivoDesc($provinciaActivoDesc){
	$this->_provinciaActivoDesc = $provinciaActivoDesc;
    }
    public function getProvinciaActivoDesc(){
	return $this->_provinciaActivoDesc;
    }
    public function setProvinciaNoActivoDesc($ProvinciaNoActivoDesc){
	$this->_provinciaNoActivoDesc = $ProvinciaNoActivoDesc;
    }
    public function getProvinciaNoActivoDesc(){
	return $this->_provinciaNoActivoDesc;
    }
    public function setProvinciaActivoMensaje($ProvinciaActivoMensaje){
	$this->_provinciaActivoMensaje= $ProvinciaActivoMensaje;
    }
    public function getProvinciaActivoMensaje(){
	return $this->_provinciaActivoMensaje;
    }

    public function getDataDB($id){
	
	$this->_db = new FrenteAlmacenamiento('dmelmac');

	$this->_db->addSelect('provincia_id');
	$this->_db->addSelect('provincia_nombre');
	$this->_db->addSelect('provincia_obs');
	$this->_db->addSelect('provincia_activa  AS provincia_activo');
	$this->_db->addSelect('\'Activa\'  AS provincia_activa_desc');
	$this->_db->addSelect('\'Inactiva\'  AS provincia_no_activa_desc');
	$this->_db->addSelect('CASE WHEN provincia_activa THEN \'Activo\' ELSE \'Inactivo\' END AS provincia_activo_mensaje');
	$this->_db->addFrom('datos_provincias');

	$this->_db->addWhere('provincia_id = \'' . $id .'\'' );

	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
	$arr =  $this->_db->ejecutar();
	
	return $resultado[0];
    
    }
    

    public function cargarMe($datos){

	if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}

	$this->setProvinciaId($resultado['provincia_id']);
	$this->setProvinciaNombre($resultado['provincia_nombre']);
	$this->setObs($resultado['provincia_obs']);
	$this->setProvinciaActivoDesc($resultado['provincia_activo_desc']);
	$this->setProvinciaNoActivoDesc($resultado['provincia_no_activo_desc']);
	$this->setProvinciaActivoMensaje($resultado['provincia_activo_mensaje']);
	$this->setProvinciaActiva($resultado['provincia_activa']);

    }
	
    public function salvarMe($arrParametros){
	

	$this->_db->addCamposTabla('seq_datos_provincias_id');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();

	$this->setProvinciaId($id[0]['nextval']);
	$this->setProvinciaNombre($arrParametros['provincias']);
	$this->setObs($arrParametros['observaciones']);
       
        $this->_db->addFrom('datos_Provincias');

        $this->_db->addCamposTabla('Provincia_id');
        $this->_db->addCamposTabla('Provincia_nombre');
        $this->_db->addCamposTabla('provincia_obs');
        $this->_db->addCamposTabla('Provincia_usuario_id');
        
	

        $this->_db->addCamposValue($this->getProvinciaId());
        $this->_db->addCamposValue('\'' . $this->getProvinciaNombre() . '\'');
        $this->_db->addCamposValue('\'' . $this->getObs() .'\'');
        $this->_db->addCamposValue('\'' . $_SESSION[usuarioId] .'\'');
	
        $this->_db->generarInsert();

        //echo $this->_db->getQry();
        
	$resultado = $this->_db->ejecutar();

	if($resultado==0){
	    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"Error al guardar el Provincia"
		    );
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 0,
		      "mensaje" =>"Provincia cargada con exito"
		    );
	}
	echo json_encode($arrDevolver);
    }
	
    public function actualizarMe($arrParametros){
	

	$this->_db->addCamposUpdate('provincia_nombre = \'' . $arrParametros['campos']['provincia_nombre'] .'\'');
	
	if($arrParametros['campos']['provincia_obs']!=null){
	    $this->_db->addCamposUpdate('provincia_obs = \'' . $arrParametros['campos']['provincia_obs'] .'\'');
	}
	if($arrParametros['campos']['provincia_activa'] == 1){
		$this->_db->addCamposUpdate('provincia_activa = true');
	}else{
		$this->_db->addCamposUpdate('provincia_activa = false');
	}
	
	$this->_db->addFrom('datos_provincias');
	$this->_db->addWhere('provincia_id = \'' . $arrParametros['id'] .'\'');
	    
	//generar qry
	$this->_db->generarUpdate();   
	//echo $this->_db->getQry();//asigamos el resulta que es un array a una variable
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
	echo json_encode($arrDevolver);exit;
    }
    public function buscarPorNombre($nombre){


	$this->_db->addSelect('Provincia_id');
	
	$this->_db->addFrom('datos_Provincias');
	
	$this->_db->addWhere('Provincia_nombre = \'' . $nombre .'\'' );

	$this->_db->generarSelect();

	$arr =  $this->_db->ejecutar();

	return $arr[0]['Provincia_id'];
    }
}
