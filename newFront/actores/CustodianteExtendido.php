<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/actores/Custodiante.php';

class CustodianteExtendido extends Custodiante{

    private $_db;

    private $_custodianteActivoMensaje;
    private $_custodianteActivoDesc;
    private $_custodianteNoActivoDesc;
    
    public function setCustodianteActivoDesc($custodianteActivoDesc){
	$this->_custodianteActivoDesc = $custodianteActivoDesc;
    }
    public function getCustodianteActivoDesc(){
	return $this->_custodianteActivoDesc;
    }
    public function setCustodianteNoActivoDesc($custodianteNoActivoDesc){
	$this->_custodianteNoActivoDesc = $custodianteNoActivoDesc;
    }
    public function getCustodianteNoActivoDesc(){
	return $this->_custodianteNoActivoDesc;
    }
    public function setCustodianteActivoMensaje($custodianteActivoMensaje){
	$this->_custodianteActivoMensaje= $custodianteActivoMensaje;
    }
    public function getCustodianteActivoMensaje(){
	return $this->_custodianteActivoMensaje;
    }
    
    public function __construct(){
	$this->_db = new FrenteAlmacenamiento('dmelmac');
    }
    
    public function getDataDB($id){
	
	$this->_db->addSelect('custodiante_id');
	$this->_db->addSelect('custodiante_nombre_completo');
	$this->_db->addSelect('custodiante_mail');
	$this->_db->addSelect('custodiante_activo');
	$this->_db->addSelect('\'Activo\'  AS custodiante_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS custodiante_no_activo_desc');
	$this->_db->addSelect('CASE WHEN custodiante_activo THEN \'Activo\' ELSE \'Inactivo\' END AS custodiante_activo_mensaje');
	$this->_db->addSelect('observaciones');
	$this->_db->addSelect('custodiante_telefono');
	
	$this->_db->addFrom('datos_custodiantes');

	$this->_db->addWhere('custodiante_id = \'' . $id .'\'' );

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

	$this->setCustodianteId($resultado['custodiante_id']);
	$this->setCustodianteNombreCompleto($resultado['custodiante_nombre_completo']);
	$this->setCustodianteMail($resultado['custodiante_mail']);
	$this->setCustodianteActivoDesc($resultado['custodiante_activo_desc']);
	$this->setCustodianteNoActivoDesc($resultado['custodiante_no_activo_desc']);
	$this->setCustodianteActivoMensaje($resultado['custodiante_activo_mensaje']);
	$this->setCustodianteActivo($resultado['custodiante_activo']);
	$this->setObs($resultado['observaciones']);
	$this->setCustodianteTelefono($resultado['custodiante_telefono']);
    }
    
    public function salvarMe($arrParametros){

    
    
	$this->_db->addCamposTabla('seq_datos_custodiantes_custodiante_id');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();

	$this->setCustodianteId($id[0]['nextval']);
	$this->setCustodianteNombreCompleto($arrParametros['custodiantes']);
	$this->setCustodianteMail($arrParametros['custodianteMail']);
	$this->setObs($arrParametros['observaciones']);
	$this->setCustodianteTelefono($arrParametros['custodianteTelefono']);

        $this->_db->addFrom('datos_custodiantes');

        $this->_db->addCamposTabla('custodiante_id');
        $this->_db->addCamposTabla('custodiante_nombre_completo');
        $this->_db->addCamposTabla('observaciones');
        $this->_db->addCamposTabla('custodiante_telefono');
        $this->_db->addCamposTabla('custodiante_mail');
        $this->_db->addCamposTabla('custodiante_alta_usuario_id');
	

        $this->_db->addCamposValue($this->getCustodianteId());
        $this->_db->addCamposValue('\'' . $this->getCustodianteNombreCompleto() . '\'');
        $this->_db->addCamposValue('\'' . $this->getObs() .'\'');
        $this->_db->addCamposValue('\'' . $this->getCustodianteTelefono() .'\'');
        $this->_db->addCamposValue('\'' . $this->getCustodianteMail() .'\'');
        $this->_db->addCamposValue($_SESSION['usuarioId']);
	
        $this->_db->generarInsert();

	//echo $this->_db->getQry();exit;
	$resultado = $this->_db->ejecutar();

	if($resultado==0){
	    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al cargar el custodiante'
		    );
		echo json_encode($arrDevolver);exit;
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'Custodiante cargado con exito'
		    );
		echo json_encode($arrDevolver);exit;
	}

    }
    
    public function actualizarMe($arrParametros){

	$this->_db = new FrenteAlmacenamiento('dmelmac');

	$this->_db->addCamposUpdate('custodiante_nombre_completo = \'' . $arrParametros['campos']['custodiante_nombre'] .'\'');
	$this->_db->addCamposUpdate('observaciones = \'' . $arrParametros['campos']['observaciones'] .'\'');	
	$this->_db->addCamposUpdate('custodiante_telefono= \'' . $arrParametros['campos']['custodiante_telefono'] . '\'');
	$this->_db->addCamposUpdate('custodiante_mail= \'' . $arrParametros['campos']['custodiante_mail'] . '\''); 
	if($arrParametros['campos']['custodiante_activo'] == 1){
		$this->_db->addCamposUpdate('custodiante_activo = true');
	}else{
		$this->_db->addCamposUpdate('custodiante_activo = false');
	}
	
	$this->_db->addFrom('datos_custodiantes');
	$this->_db->addWhere('custodiante_id = \'' . $arrParametros['id'] .'\'');
	    
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
