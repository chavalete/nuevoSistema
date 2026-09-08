<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/actores/Paciente.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
class PacienteExtendido extends Paciente
{
    private $_pacienteActivoMensaje;
    private $_pacienteActivoDesc;
    private $_pacienteNoActivoDesc;
    private $_objObraSocial;
    
    
    public function setPacienteActivoDesc($pacienteActivoDesc){
	$this->_pacienteActivoDesc = $pacienteActivoDesc;
    }
    public function getPacienteActivoDesc(){
	return $this->_pacienteActivoDesc;
    }
    public function setPacienteNoActivoDesc($pacienteNoActivoDesc){
	$this->_pacienteNoActivoDesc = $pacienteNoActivoDesc;
    }
    public function getPacienteNoActivoDesc(){
	return $this->_pacienteNoActivoDesc;
    }
    public function setPacienteActivoMensaje($pacienteActivoMensaje){
	$this->_pacienteActivoMensaje= $pacienteActivoMensaje;
    }
    public function getPacienteActivoMensaje(){
	return $this->_pacienteActivoMensaje;
    }
    public function setObjObraSocial ($obj){
        $this->_objObraSocial = $obj;
    }
    
    public function getObjObraSocial (){
        return $this->_objObraSocial;
    }
    public function getDataDB($id){
	
	$this->_db = new FrenteAlmacenamiento('dmelmac');

	$this->_db->addSelect('paciente_id');
	$this->_db->addSelect('paciente_nombre');
	$this->_db->addSelect('paciente_activo AS paciente_activo');
	$this->_db->addSelect('\'Activo\'  AS paciente_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS paciente_no_activo_desc');
	$this->_db->addSelect('CASE WHEN paciente_activo THEN \'Activo\' ELSE \'Inactivo\' END AS paciente_activo_mensaje');
	$this->_db->addSelect('observaciones');
	$this->_db->addSelect('nro_afiliado');
	$this->_db->addSelect('paciente_telefono');
	$this->_db->addSelect('obra_social_id');
	
	$this->_db->addFrom('datos_pacientes');

	$this->_db->addWhere('paciente_id = \'' . $id .'\'' );

	$this->_db->generarSelect();

	$resultado =  $this->_db->ejecutar();
	
	return $resultado[0];
    
    }
    
    
    function cargarMe($datos){
    
	if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}

	$this->setPacienteId($resultado['paciente_id']);
	$this->setPacienteNombre($resultado['paciente_nombre']);
	$this->setPacienteActivoDesc($resultado['paciente_activo_desc']);
	$this->setPacienteNoActivoDesc($resultado['paciente_no_activo_desc']);
	$this->setPacienteActivoMensaje($resultado['paciente_activo_mensaje']);
	$this->setPacienteActivo($resultado['paciente_activo']);
	$this->setObs($resultado['observaciones']);
	$this->setNroAfiliado($resultado['nro_afiliado']);
	$this->setPacienteTelefono($resultado['paciente_telefono']);
	$obraSocial = new ObraSocialExtendido();
        if($resultado['obra_social_id'] > 0){
            $obraSocial->cargarMe($resultado['obra_social_id']);
        }
        $this->setObjObraSocial($obraSocial);
	
    }
    function salvarMe($arrParametros){
    
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');

	$this->_db->addCamposTabla('seq_datos_pacientes_id');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();

	$this->setPacienteId($id[0]['nextval']);
	$this->setPacienteNombre($arrParametros['pacientes']);
	$this->setObs($arrParametros['observaciones']);
	$this->setNroAfiliado($arrParametros['nroAfiliado']);
	$this->setPacienteTelefono($arrParametros['pacienteTelefono']);

       
        $this->_db->addFrom('datos_pacientes');

        $this->_db->addCamposTabla('paciente_id');
        $this->_db->addCamposTabla('paciente_nombre');
        $this->_db->addCamposTabla('observaciones');
        $this->_db->addCamposTabla('paciente_usuario_alta');
        
        $this->_db->addCamposValue($this->getPacienteId());
        $this->_db->addCamposValue('\'' . $this->getPacienteNombre() . '\'');
        $this->_db->addCamposValue('\'' . $this->getObs() .'\'');
        
        $this->_db->addCamposValue($_SESSION['usuarioId']);
        
	
	
        $this->_db->generarInsert();

	$resultado = $this->_db->ejecutar();

	if($resultado==0){
	    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al cargar el paciente'
		    );
		echo json_encode($arrDevolver);exit;
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'Paciente cargado con exito'
		    );
		echo json_encode($arrDevolver);exit;
	}


    }
    function actualizarMe($arrParametros){
    
	$this->_db = new FrenteAlmacenamiento('dmelmac');

	$this->_db->addCamposUpdate('paciente_nombre = \'' . $arrParametros['campos']['paciente_nombre'] .'\'');
	$this->_db->addCamposUpdate('observaciones = \'' . $arrParametros['campos']['observaciones'] .'\'');
	$this->_db->addCamposUpdate('nro_afiliado= \'' . $arrParametros['campos']['nro_afiliado'] .'\'');
	$this->_db->addCamposUpdate('paciente_telefono= \'' . $arrParametros['campos']['paciente_telefono'] .'\'');	

/*	if($arrParametros['campos']['obrasSociales']  != 'false'){
	    $this->_db->addCamposUpdate('obra_social_id= \'' . $arrParametros['campos']['obrasSociales'] .'\'');
	}
*/	
	if($arrParametros['campos']['paciente_activo'] == 1){
		$this->_db->addCamposUpdate('paciente_activo = true');
	}else{
		$this->_db->addCamposUpdate('paciente_activo = false');
	}
	$this->_db->addFrom('datos_pacientes');
	$this->_db->addWhere('paciente_id = \'' . $arrParametros['id'] .'\'');
	    
	//generar qry
	$this->_db->generarUpdate();   
	//echo $this->_db->getQry();
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
