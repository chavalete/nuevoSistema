<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/libreria/Localidad.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
CLASE DE CATEGORIAS
*/

class LocalidadExtendido extends Localidad
{
    private $_localidadActivoMensaje;
    private $_localidadActivoDesc;
    private $_localidadNoActivoDesc;
    
    
    public function __construct(){
	
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
    
    }
    
    public function setLocalidadActivoDesc($localidadActivoDesc){
	$this->_localidadActivoDesc = $localidadActivoDesc;
    }
    public function getLocalidadActivoDesc(){
	return $this->_localidadActivoDesc;
    }
    public function setLocalidadNoActivoDesc($LocalidadNoActivoDesc){
	$this->_localidadNoActivoDesc = $LocalidadNoActivoDesc;
    }
    public function getLocalidadNoActivoDesc(){
	return $this->_localidadNoActivoDesc;
    }
    public function setLocalidadActivoMensaje($LocalidadActivoMensaje){
	$this->_localidadActivoMensaje= $LocalidadActivoMensaje;
    }
    public function getLocalidadActivoMensaje(){
	return $this->_localidadActivoMensaje;
    }

    public function getDataDB($id){
	
	$this->_db = new FrenteAlmacenamiento('dmelmac');

	$this->_db->addSelect('localidad_id');
	$this->_db->addSelect('localidad_nombre');
	$this->_db->addSelect('localidad_obs');
	$this->_db->addSelect('localidad_prov_id');
	$this->_db->addSelect('localidad_activa AS localidad_activo');
	$this->_db->addSelect('\'Activa\'  AS localidad_activo_desc');
	$this->_db->addSelect('\'Inactiva\'  AS localidad_no_activo_desc');
	$this->_db->addSelect('CASE WHEN localidad_activa THEN \'Activo\' ELSE \'Inactivo\' END AS localidad_activo_mensaje');
	$this->_db->addFrom('datos_localidades');

	$this->_db->addWhere('localidad_id = \'' . $id .'\'' );

	$this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
	$resultado =  $this->_db->ejecutar();
	
	return $resultado[0];
    }
    

    public function cargarMe($datos){
	
	if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}

	$this->setLocalidadId($resultado['localidad_id']);
	$this->setLocalidadNombre($resultado['localidad_nombre']);
	$this->setObs($resultado['localidad_obs']);
	$this->setLocalidadActivoDesc($resultado['localidad_activo_desc']);
	$this->setLocalidadNoActivoDesc($resultado['localidad_no_activo_desc']);
	$this->setLocalidadActivoMensaje($resultado['localidad_activo_mensaje']);
	$this->setLocalidadActiva($resultado['localidad_activa']);

        
	$objProvincia = new ProvinciaExtendido();
        if($resultado['localidad_prov_id'] != NULL){
            $objProvincia->cargarMe($resultado['localidad_prov_id']);
        }
        $this->setObjProvincia($objProvincia);
    
    }
	
    public function salvarMe($arrParametros){
	

	$this->_db->addCamposTabla('seq_datos_loca_idx');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();

	$this->setLocalidadId($id[0]['nextval']);
	$this->setLocalidadNombre($arrParametros['localidades']);
	$this->setObs($arrParametros['observaciones']);
       
        $this->_db->addFrom('datos_localidades');

        $this->_db->addCamposTabla('localidad_id');
        $this->_db->addCamposTabla('localidad_nombre');
        $this->_db->addCamposTabla('localidad_prov_id');
        $this->_db->addCamposTabla('localidad_obs');
        $this->_db->addCamposTabla('localidad_usuario_id');
        
	

        $this->_db->addCamposValue($this->getLocalidadId());
        $this->_db->addCamposValue('\'' . $this->getLocalidadNombre() . '\'');
        $this->_db->addCamposValue('\'' . $arrParametros['provincias'] . '\'');
        $this->_db->addCamposValue('\'' . $this->getObs() .'\'');
        $this->_db->addCamposValue('\'' . $_SESSION[usuarioId] .'\'');
	
        $this->_db->generarInsert();

        //echo $this->_db->getQry();
        
	$resultado = $this->_db->ejecutar();

	if($resultado==0){
	    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"Error al guardar el Localidad"
		    );
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 0,
		      "mensaje" =>"Localidad cargada con exito"
		    );
	}
	echo json_encode($arrDevolver);
    }
	
    public function actualizarMe($arrParametros){
	

	$this->_db->addCamposUpdate('localidad_nombre = \'' . $arrParametros['campos']['localidad'] .'\'');
        
        $this->_db->addCamposUpdate('localidad_prov_id = \'' . $arrParametros['campos']['provincias'] .'\'');
	
	if($arrParametros['campos']['observaciones']!=null){
	    $this->_db->addCamposUpdate('localidad_obs = \'' . $arrParametros['campos']['observaciones'] .'\'');
	}
	if($arrParametros['campos']['localidad_activa'] == 1){
		$this->_db->addCamposUpdate('localidad_activa = true');
	}else{
		$this->_db->addCamposUpdate('localidad_activa = false');
                $this->_db->addCamposUpdate('localidad_inactiva_fecha_hora = now()');                
                $this->_db->addCamposUpdate('localidad_inactiva_usuario_id = ' . $_SESSION[usuarioId] . ';'); 
	}
	
        $this->_db->addFrom('datos_localidades');
	$this->_db->addWhere('localidad_id = \'' . $arrParametros['id'] .'\'');
	    
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


	$this->_db->addSelect('Localidad_id');
	
	$this->_db->addFrom('datos_Localidads');
	
	$this->_db->addWhere('Localidad_nombre = \'' . $nombre .'\'' );

	$this->_db->generarSelect();

	$arr =  $this->_db->ejecutar();

	return $arr[0]['Localidad_id'];
    }
}
