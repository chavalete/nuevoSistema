<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/trazabilidad/libreria/libreria/Estado.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/trazabilidad/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
CLASE DE CATEGORIAS
*/

class EstadoExtendido extends Estado
{
    private $_estadoActivoMensaje;
    private $_estadoActivoDesc;
    private $_estadoNoActivoDesc;
    
    
    public function __construct(){
	
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
    
    }
    
    public function setEstadoActivoDesc($estadoActivoDesc){
	$this->_estadoActivoDesc = $estadoActivoDesc;
    }
    public function getEstadoActivoDesc(){
	return $this->_estadoActivoDesc;
    }
    public function setEstadoNoActivoDesc($estadoNoActivoDesc){
	$this->_estadoNoActivoDesc = $estadoNoActivoDesc;
    }
    public function getEstadoNoActivoDesc(){
	return $this->_estadoNoActivoDesc;
    }
    public function setEstadoActivoMensaje($estadoActivoMensaje){
	$this->_estadoActivoMensaje= $estadoActivoMensaje;
    }
    public function getEstadoActivoMensaje(){
	return $this->_estadoActivoMensaje;
    }

    public function getDataDB($id){
	
	$this->_db = new FrenteAlmacenamiento('dmelmac');

	$this->_db->addSelect('estado_id');
	$this->_db->addSelect('estado_desc');
	$this->_db->addSelect('obs');
	$this->_db->addSelect('estado_activo AS estado_activo');
	$this->_db->addSelect('\'Activo\'  AS estado_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS estado_no_activo_desc');
	$this->_db->addSelect('CASE WHEN estado_activo THEN \'Activo\' ELSE \'Inactivo\' END AS estado_activo_mensaje');
	$this->_db->addFrom('datos_estados');

	$this->_db->addWhere('estado_id = \'' . $id .'\'' );

	$this->_db->generarSelect();

	//echo $this->_db->getQry();
	$arr =  $this->_db->ejecutar();
	
	return $resultado[0];
    
    }
    

    public function cargarMe($datos){

	if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}

	$this->setEstadoId($resultado['estado_id']);
	$this->setEstadoNombre($resultado['estado_desc']);
	$this->setObs($resultado['obs']);
	$this->setEstadoActivoDesc($resultado['estado_activo_desc']);
	$this->setEstadoNoActivoDesc($resultado['estado_no_activo_desc']);
	$this->setEstadoActivoMensaje($resultado['estado_activo_mensaje']);
	$this->setEstadoActiva($resultado['estado_activo']);
	$this->setEstadoNombre($resultado['estado_desc']);

    }
	
    public function salvarMe($arrParametros){
	

	$this->_db->addCamposTabla('seq_datos_estado_id');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();

	$this->setEstadoId($id[0]['nextval']);
	$this->setEstadoNombre($arrParametros['estados']);
	$this->setObs($arrParametros['observaciones']);
       
        $this->_db->addFrom('datos_estados');

        $this->_db->addCamposTabla('estado_id');
        $this->_db->addCamposTabla('estado_desc');
        $this->_db->addCamposTabla('obs');
        $this->_db->addCamposTabla('estado_usuario_id');
        
        $this->_db->addCamposValue($this->getEstadoId());
        $this->_db->addCamposValue('\'' . $this->getEstadoNombre() . '\'');
        $this->_db->addCamposValue('\'' . $this->getObs() .'\'');
        $this->_db->addCamposValue('\'' . $_SESSION[usuarioId] .'\'');
	
        $this->_db->generarInsert();

        //echo $this->_db->getQry();
        
	$resultado = $this->_db->ejecutar();

	if($resultado==0){
	    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"Error al guardar el estado"
		    );
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 0,
		      "mensaje" =>"Estado cargado con exito"
		    );
	}
	echo json_encode($arrDevolver);
    }
	
    public function actualizarMe($arrParametros){
	

	$this->_db->addCamposUpdate('estado_desc = \'' . $arrParametros['campos']['estado_desc'] .'\'');
	
	if($arrParametros['campos']['observaciones']!=null){
	    $this->_db->addCamposUpdate('obs = \'' . $arrParametros['campos']['observaciones'] .'\'');
	}
	if($arrParametros['campos']['estado_activo'] == 1){
		$this->_db->addCamposUpdate('estado_activo = true');
	}else{
		$this->_db->addCamposUpdate('estado_activo = false');
	}
	
	$this->_db->addFrom('datos_estados');
	$this->_db->addWhere('estado_id = \'' . $arrParametros['id'] .'\'');
	    
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


	$this->_db->addSelect('estado_id');
	
	$this->_db->addFrom('datos_estados');
	
	$this->_db->addWhere('estado_nombre = \'' . $nombre .'\'' );

	$this->_db->generarSelect();

	$arr =  $this->_db->ejecutar();

	return $arr[0]['estado_id'];
    }
}
