<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/libreria/cotizacionDolar.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
CLASE DE CATEGORIAS
*/

class cotizacionDolarExtendido extends CotizacionDolar
{
    
    public function __construct(){
    	$this->_db = new FrenteAlmacenamiento('dmelmac');
    }
    public function getDataDB($id){
	
	$this->_db->addSelect('cotizacion_id');
	$this->_db->addSelect('cotizacion');
	$this->_db->addSelect('cotizacion_fecha_hora');
	$this->_db->addSelect('observaciones');
	$this->_db->addSelect('cotizacion_usuario_id');
	$this->_db->addSelect('cotizacion_procesada');
	$this->_db->addSelect('cotizacion_procesada_fecha_hora');
	$this->_db->addSelect('cotizacion_procesada_usuario_id');
	$this->_db->addFrom('datos_cotizacion_dolar');

	$this->_db->addWhere('cotizacion_id = \'' . $id .'\'' );

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

	$this->setCotizacionId($resultado['cotizacion_id']);
	$this->setCotizacion($resultado['cotizacion']);
	$this->setObs($resultado['observaciones']);
	$this->setCotizacionUsuarioId($resultado['cotizacion_usuario_id']);
	$this->setCotizacionFechaHora($resultado['cotizacion_fecha_hora']);
	$this->setCotizacionProcesada($resultado['cotizacion_procesada']);
	$this->setCotizacionProcesadaFechaHora($resultado['cotizacion_procesada_fecha_hora']);
	$this->setCotizacionProcesadaUsuarioId($resultado['cotizacion_procesada_usuario_id']);
    }
	
    public function salvarMe($arrParametros){


	$this->_db->addCamposTabla('seq_datos_cotizacion_idx');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();

	$this->setCotizacionId($id[0]['nextval']);
	$this->setCotizacion($arrParametros['cotizacion']);
	$this->setObs($arrParametros['observaciones']);
	$this->setCotizacionUsuarioId($_SESSION['usuarioId']);
       
        $this->_db->addFrom('datos_cotizacion_dolar');

        $this->_db->addCamposTabla('cotizacion_id');
        $this->_db->addCamposTabla('cotizacion');
        $this->_db->addCamposTabla('observaciones');
        $this->_db->addCamposTabla('cotizacion_usuario_id');
	

        $this->_db->addCamposValue($this->getCotizacionId());
        $this->_db->addCamposValue('\'' . $this->getCotizacion() . '\'');
        $this->_db->addCamposValue('\'' . $this->getObs() .'\'');
        $this->_db->addCamposValue('\'' . $this->getCotizacionUsuarioId() .'\'');
	
        $this->_db->generarInsert();

        return $this->_db->getQry();
    }
	
    public function actualizarMe($arrParametros){
	

	$this->_db->addCamposUpdate('categoria_nombre = \'' . $arrParametros['campos']['categoria_nombre'] .'\'');
	$this->_db->addCamposUpdate('observaciones = \'' . $arrParametros['campos']['observaciones'] .'\'');	
	if($arrParametros['campos']['categoria_activa'] == 1){
		$this->_db->addCamposUpdate('categoria_activa = true');
	}else{
		$this->_db->addCamposUpdate('categoria_activa = false');
	}
	
	$this->_db->addFrom('datos_categorias');
	$this->_db->addWhere('categoria_id = \'' . $arrParametros['id'] .'\'');
	    
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

	$this->_db = new FrenteAlmacenamiento('dmelmac');

	$this->_db->addSelect('categoria_id');
	
	$this->_db->addFrom('datos_categorias');
	
	$this->_db->addWhere('categoria_nombre = \'' . $nombre .'\'' );

	$this->_db->generarSelect();

	$arr =  $this->_db->ejecutar();

	return $arr[0]['categoria_id'];
    }
}
