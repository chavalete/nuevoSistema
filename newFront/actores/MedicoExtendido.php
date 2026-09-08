<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/actores/Medico.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

class MedicoExtendido extends Medico
{
    private $_medicoActivoMensaje;
    private $_medicoActivoDesc;
    private $_medicoNoActivoDesc;
    
    public function setMedicoActivoDesc($medicoActivoDesc){
	$this->_medicoActivoDesc = $medicoActivoDesc;
    }
    public function getMedicoActivoDesc(){
	return $this->_medicoActivoDesc;
    }
    public function setMedicoNoActivoDesc($medicoNoActivoDesc){
	$this->_medicoNoActivoDesc = $medicoNoActivoDesc;
    }
    public function getMedicoNoActivoDesc(){
	return $this->_medicoNoActivoDesc;
    }
    public function setMedicoActivoMensaje($medicoActivoMensaje){
	$this->_medicoActivoMensaje= $medicoActivoMensaje;
    }
    public function getMedicoActivoMensaje(){
	return $this->_medicoActivoMensaje;
    }
    
    public function cargarMe($id){
	
	$db_dM = new FrenteAlmacenamiento('dmelmac');

	$db_dM->addSelect('medico_id');
	$db_dM->addSelect('medico_nombre');
	$db_dM->addSelect('matricula_nro');
	$db_dM->addSelect('medico_activo AS medico_activo');
	$db_dM->addSelect('\'Activo\'  AS medico_activo_desc');
	$db_dM->addSelect('\'Inactivo\'  AS medico_no_activo_desc');
	$db_dM->addSelect('CASE WHEN medico_activo THEN \'Activo\' ELSE \'Inactivo\' END AS medico_activo_mensaje');
	$db_dM->addSelect('observaciones');
	$db_dM->addSelect('medico_telefono');
	
	$db_dM->addFrom('datos_medicos');

	$db_dM->addWhere('medico_id = \'' . $id .'\'' );

	$db_dM->generarSelect();

	//echo $db_dM->getQry();
	$arr =  $db_dM->ejecutar();


	$this->setMedicoId($arr['0']['medico_id']);
	$this->setMedicoNombre($arr['0']['medico_nombre']);
	$this->setMatriculaNro($arr['0']['matricula_nro']);
	$this->setMedicoActivoDesc($arr['0']['medico_activo_desc']);
	$this->setMedicoNoActivoDesc($arr['0']['medico_no_activo_desc']);
	$this->setMedicoActivoMensaje($arr['0']['medico_activo_mensaje']);
	$this->setMedicoActivo($arr['0']['medico_activo']);
	$this->setObs($arr['0']['observaciones']);
	$this->setMedicoTelefono($arr['0']['medico_telefono']);
    }

    public function salvarMe($arrParametros){

	$db_dM = NEW FrenteAlmacenamiento('dmelmac');

	$db_dM->addCamposTabla('seq_datos_medicos_id');
	$db_dM->generarProximo();

	$id = $db_dM->ejecutar();

	$this->setMedicoId($id[0]['nextval']);
	$this->setMedicoNombre($arrParametros['medicos']);
	$this->setMatriculaNro($arrParametros['matriculaNro']);
	$this->setObs($arrParametros['observaciones']);
	$this->setMedicoTelefono($arrParametros['medicoTelefono']);

       
        $db_dM->addFrom('datos_medicos');

        $db_dM->addCamposTabla('medico_id');
        $db_dM->addCamposTabla('medico_nombre');
        $db_dM->addCamposTabla('observaciones');
        $db_dM->addCamposTabla('medico_telefono');
        $db_dM->addCamposTabla('medico_usuario_alta');
	

        $db_dM->addCamposValue($this->getMedicoId());
        $db_dM->addCamposValue('\'' . $this->getMedicoNombre() . '\'');
        $db_dM->addCamposValue('\'' . $this->getObs() .'\'');
        $db_dM->addCamposValue('\'' . $this->getMedicoTelefono() .'\'');
        $db_dM->addCamposValue($_SESSION['usuarioId']);
	
        $db_dM->generarInsert();

	$resultado = $db_dM->ejecutar();

	if($resultado==0){
	    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al cargar el medico'
		    );
		echo json_encode($arrDevolver);exit;
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'Medico cargado con exito'
		    );
		echo json_encode($arrDevolver);exit;
	}

    }
    public function actualizarMe($arrParametros){

	$db_dM = new FrenteAlmacenamiento('dmelmac');

	$db_dM->addCamposUpdate('medico_nombre = \'' . $arrParametros['campos']['medico_nombre'] .'\'');
	$db_dM->addCamposUpdate('observaciones = \'' . $arrParametros['campos']['observaciones'] .'\'');	
	$db_dM->addCamposUpdate('medico_telefono= \'' . $arrParametros['campos']['medico_telefono'] . '\''); 
	
	if($arrParametros['campos']['medico_activo'] == 1){
		$db_dM->addCamposUpdate('medico_activo = true');
	}else{
		$db_dM->addCamposUpdate('medico_activo = false');
	}
	$db_dM->addFrom('datos_medicos');
	$db_dM->addWhere('medico_id = \'' . $arrParametros['id'] .'\'');
	    
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