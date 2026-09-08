<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/actores/ObraSocial.php';


class ObraSocialExtendido extends ObraSocial
{
    private $_obraSocialActivoMensaje;
    private $_obraSocialActivoDesc;
    private $_obraSocialNoActivoDesc;
    
    public function setObraSocialActivoDesc($obraSocialActivoDesc){
	$this->_obraSocialActivoDesc = $obraSocialActivoDesc;
    }
    public function getObraSocialActivoDesc(){
	return $this->_obraSocialActivoDesc;
    }
    public function setObraSocialNoActivoDesc($obraSocialNoActivoDesc){
	$this->_obraSocialNoActivoDesc = $obraSocialNoActivoDesc;
    }
    public function getObraSocialNoActivoDesc(){
	return $this->_obraSocialNoActivoDesc;
    }
    public function setObraSocialActivoMensaje($obraSocialActivoMensaje){
	$this->_obraSocialActivoMensaje= $obraSocialActivoMensaje;
    }
    public function getObraSocialActivoMensaje(){
	return $this->_obraSocialActivoMensaje;
    }

    function cargarMe($id){
    
	$db_dM = new FrenteAlmacenamiento('dmelmac');

	$db_dM->addSelect('obra_social_id');
	$db_dM->addSelect('obra_social_nombre');
	$db_dM->addSelect('obra_social_activa AS obra_social_activa');
	$db_dM->addSelect('\'Activo\'  AS obra_social_activa_desc');
	$db_dM->addSelect('\'Inactivo\'  AS obra_social_no_activa_desc');
	$db_dM->addSelect('CASE WHEN obra_social_activa THEN \'Activo\' ELSE \'Inactivo\' END AS obra_social_activa_mensaje');
	$db_dM->addSelect('observaciones');
	
	$db_dM->addFrom('datos_obras_sociales');

	$db_dM->addWhere('obra_social_id = \'' . $id .'\'' );

	$db_dM->generarSelect();

	$arr =  $db_dM->ejecutar();


	$this->setObraSocialId($arr['0']['obra_social_id']);
	$this->setObraSocialNombre($arr['0']['obra_social_nombre']);
	$this->setObraSocialActivoDesc($arr['0']['obra_social_activa_desc']);
	$this->setObraSocialNoActivoDesc($arr['0']['obra_social_no_activa_desc']);
	$this->setObraSocialActivoMensaje($arr['0']['obra_social_activa_mensaje']);
	$this->setObraSocialActiva($arr['0']['obra_social_activa']);
	$this->setObs($arr['0']['observaciones']);

    }
    function salvarMe($arrParametros){
	
	$db_dM = NEW FrenteAlmacenamiento('dmelmac');

	$db_dM->addCamposTabla('seq_datos_obras_sociales_id');
	$db_dM->generarProximo();

	$id = $db_dM->ejecutar();

	$this->setObraSocialId($id[0]['nextval']);
	$this->setObraSocialNombre($arrParametros['obraSocial']);
	$this->setObs($arrParametros['observaciones']);

       
        $db_dM->addFrom('datos_obras_sociales');

        $db_dM->addCamposTabla('obra_social_id');
        $db_dM->addCamposTabla('obra_social_nombre');
        $db_dM->addCamposTabla('observaciones');
	

        $db_dM->addCamposValue($this->getObraSocialId());
        $db_dM->addCamposValue('\'' . $this->getObraSocialNombre() . '\'');
        $db_dM->addCamposValue('\'' . $this->getObs() .'\'');
	
        $db_dM->generarInsert();

	$resultado = $db_dM->ejecutar();

	if($resultado==0){
		$arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al cargar la obra social'
		    );
		echo json_encode($arrDevolver);exit;
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'Obra social cargada con exito'
		    );
		echo json_encode($arrDevolver);exit;
	}

    }
    function actualizarMe($arrParametros){

	$db_dM = new FrenteAlmacenamiento('dmelmac');

	$db_dM->addCamposUpdate('obra_social_nombre = \'' . $arrParametros['campos']['obra_social_nombre'] .'\'');
	$db_dM->addCamposUpdate('observaciones = \'' . $arrParametros['campos']['observaciones'] .'\'');	
	if($arrParametros['campos']['obra_social_activa'] == 1){
		$db_dM->addCamposUpdate('obra_social_activa = true');
	}else{
		$db_dM->addCamposUpdate('obra_social_activa = false');
	}
	$db_dM->addFrom('datos_obras_sociales');
	$db_dM->addWhere('obra_social_id = \'' . $arrParametros['id'] .'\'');
	    
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