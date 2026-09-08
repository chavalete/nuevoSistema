<?php
/**
 * Description of PresentacionExtendido
 *
 * @author Guillo
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/stock/Presentacion.php';


class PresentacionExtendido extends Presentacion{
    
    
    private $_db;
    private $_presentacionActivoMensaje;
    private $_presentacionActivoDesc;
    private $_presentacionNoActivoDesc;
    
    public function setPresentacionActivoDesc($presentacionActivoDesc){
	$this->_presentacionActivoDesc = $presentacionActivoDesc;
    }
    public function getPresentacionActivoDesc(){
	return $this->_presentacionActivoDesc;
    }
    public function setPresentacionNoActivoDesc($presentacionNoActivoDesc){
	$this->_presentacionNoActivoDesc = $presentacionNoActivoDesc;
    }
    public function getPresentacionNoActivoDesc(){
	return $this->_presentacionNoActivoDesc;
    }
    public function setPresentacionActivoMensaje($presentacionActivoMensaje){
	$this->_presentacionActivoMensaje = $presentacionActivoMensaje;
    }
    public function getPresentacionActivoMensaje(){
	return $this->_presentacionActivoMensaje;
    }
    
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
    }


    public function getDataDB($id){
        $this->_db->addSelect('presentacion_id');
	$this->_db->addSelect('descripcion');
	$this->_db->addSelect('presentacion_activa AS presentacion_activo');
	$this->_db->addSelect('\'Activo\'  AS presentacion_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS presentacion_no_activo_desc');
	$this->_db->addSelect('CASE WHEN presentacion_activa THEN \'Activa\' ELSE \'Inactiva\' END AS presentacion_activo_mensaje');
	$this->_db->addSelect('presentacion_usuario_id');
	$this->_db->addSelect('presentacion_fecha_hora');
	$this->_db->addSelect('presentacion_cancelada');
	$this->_db->addSelect('presentacion_cancelada_fecha_hora');
	$this->_db->addSelect('presentacion_cancelada_usuario_id');
	
	
	$this->_db->addFrom('datos_presentaciones');
        $this->_db->addWhere('presentacion_id = ' . $id );
       
        $this->_db->generarSelect();
        $resultado = $this->_db->ejecutar();
        //echo $this->_db->getQry();exit;
        return $resultado[0];
    }
    
    
    public function cargarMe($datos){
        
	if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}
        
        if(count($resultado) == 0){
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'La Presentacion no existe'
		    );
		echo json_encode($arrDevolver);exit;
        }
        //var_dump($resultado);exit;
        $this->setPresentacionId($resultado['presentacion_id']);
        $this->setDesc($resultado['descripcion']);
        $this->setPresentacionUsuarioId($resultado['presentacion_usuario_id']);
        $this->setPresentacionFechaHora($resultado['presentacion_fecha_hora']);
        $this->setPresentacionCancelada($resultado['presentacion_cancelada']);
        $this->setPresentacionCanceladaUsuarioId($resultado['presentacion_cancelada_usuario_id']);
        $this->setPresentacionCancelada($resultado['presentacion_cancelada_fecha_hora']);
                
        $this->setPresentacionActivoDesc($resultado['presentacion_activo_desc']);
	$this->setPresentacionNoActivoDesc($resultado['presentacion_no_activo_desc']);
	$this->setPresentacionActivoMensaje($resultado['presentacion_activo_mensaje']);
	$this->setPresentacionActiva($resultado['presentacion_activo']);
        
    }
    
    function salvarMe($arrParametros){
        
        
        $this->_db->addCamposTabla('seq_datos_presentaciones_presentacion_id');
	$this->_db->generarProximo();
        $resultado = $this->_db->ejecutar();
	$this->setPresentacionId($resultado[0]['nextval']);
	
        
        $this->_db->addFrom('datos_presentaciones');
        
        $this->_db->addCamposTabla('presentacion_id');
        $this->_db->addCamposValue($this->getPresentacionId());
        
        $this->_db->addCamposTabla('descripcion');
        $this->_db->addCamposValue('\'' . $arrParametros['presentaciones']. '\'');
        
        $this->_db->addCamposTabla('presentacion_usuario_id');
        $this->_db->addCamposValue('\'' . $_SESSION['usuarioId']. '\'');
        
        $this->_db->generarInsert();    
	//echo $this->_db->getQry(); exit;
	$resultado = $this->_db->ejecutar();
        
        if(count($this->_db->getArrError()) > 0){
            $arrError = $this->_db->getArrError();
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" => $arrError['detalle']
		    );
		
        }else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" => "Presentacion creada con exito"
		    );
        
        }
        echo json_encode($arrDevolver);exit;
    }
    
    function actualizarMe($arrParametros){
        
        $this->_db->addCamposUpdate('descripcion = \'' . $arrParametros['campos']['desc'] .'\'');
	
	if($arrParametros['campos']['presentacion_activa'] == 1){
		$this->_db->addCamposUpdate('presentacion_activa = true');
	}else{
		$this->_db->addCamposUpdate('presentacion_activa = false');
	}
	
	
	$this->_db->addFrom('datos_presentaciones');
	$this->_db->addWhere('presentacion_id = \'' . $arrParametros['id'] .'\'');
	    
	//generar qry
	$this->_db->generarUpdate();   
	//echo $this->_db->getQry();
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
