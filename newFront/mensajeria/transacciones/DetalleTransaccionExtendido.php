<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/mensajeria/transacciones/DetalleTransaccion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/ProductoExtendidoAnmat.php';

class DetalleTransaccionExtendido extends DetalleTransaccion
{

    private $_db;
    private $_productoGtin;
    private $_codigoTransaccion;

    public function setProductoGtin($productoGtin){

	$this->_productoGtin = $productoGtin;
    }
    public function getProductoGtin(){

	return $this->_productoGtin;
    }
    public function __construct() {
     
	$this->_db = new FrenteAlmacenamiento();
    }
    public function getArrError(){
        return $this->_arrError;
    }
    public function setArrError($arrError){
        $this->_arrError = $arrError;
    }
    public function setCodigoTransaccion($codigoTransaccion){
    
	$this->_codigoTransaccion = $codigoTransaccion;
    }
    public function getCodigoTransaccion(){
	return $this->_codigoTransaccion;
    }
    function getDataDB($id){
	
	$this->_db->addSelect('transaccion_id');
	$this->_db->addSelect('transaccion_detalle_id');
	$this->_db->addSelect('trazabilidad_codigo');
	$this->_db->addSelect('lote_nro');
	$this->_db->addSelect('lote_vencimiento AS lote_vencimiento');
	$this->_db->addSelect('producto_gtin');
	$this->_db->addSelect('de_mensaje_id');
        $this->_db->addFrom('detalle_transacciones_anmat');
        $this->_db->addWhere('transaccion_detalle_id = ' . $id );
       
        $this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        return $resultado;
    
    }
    public function cargarme($datos){

	//var_dump($datos);exit;
        if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}
        
        $this->setTransaccionId($resultado['transaccion_id']);
        $this->setDetalleTransaccionId($resultado[transaccion_detalle_id]);
	$this->setTrazabilidadCodigo($resultado[trazabilidad_codigo]);
	$this->setLote($resultado[lote_nro]);
	$this->setLoteVencimiento($resultado[lote_vencimiento]);
	//$this->setDeMensajeId($resultado[0]['de_mensaje_id']);
	$this->setProductoGtin($resultado['producto_gtin']);
	$this->setCodigoTransaccion($resultado['codigo_transaccion']);

    }

    public function salvarme($objDetalleTransaccion){
    
	$this->_db->addCamposTabla('seq_detalle_transacciones_transaccion_detalle_id');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();

	$objDetalleTransaccion->setDetalleTransaccionId(($id[0]['nextval']));


        $this->_db->addFrom('detalle_transacciones_anmat');

        $this->_db->addCamposTabla('transaccion_id');
        $this->_db->addCamposTabla('trazabilidad_codigo');
        $this->_db->addCamposTabla('lote_nro');
        $this->_db->addCamposTabla('lote_vencimiento');
        $this->_db->addCamposTabla('producto_gtin');
	$this->_db->addCamposTabla('de_mensaje_id');
	$this->_db->addCamposTabla('transaccion_detalle_id');
              
        $this->_db->addCamposValue($objDetalleTransaccion->getTransaccionId());
        $this->_db->addCamposValue('\'' . $objDetalleTransaccion->getTrazabilidadCodigo() . '\'');
        $this->_db->addCamposValue('\'' . $objDetalleTransaccion->getLote() . '\'');
        $this->_db->addCamposValue('\'' . $objDetalleTransaccion->getLoteVencimiento() . '\'');
        $this->_db->addCamposValue('\'' . $objDetalleTransaccion->getProductoGtin() . '\'');
	$this->_db->addCamposValue('0');
	$this->_db->addCamposValue($objDetalleTransaccion->getDetalleTransaccionId());
	
	$this->_db->generarInsert();
	//echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        if(count($resultado)==0){
	     $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al salvar los detalles'
		    );
	echo json_encode($arrDevolver);exit;
	}


    }
    public function actualizarme($arrParametros){

	$this->_db->addCamposUpdate('producto_gtin= \'' . $arrParametros['campos']['producto_gtin'] .'\'');
	$this->_db->addCamposUpdate('lote_nro= \'' . $arrParametros['campos']['lote'] .'\'');
	$this->_db->addCamposUpdate('lote_vencimiento= \'' . $arrParametros['campos']['lote_vencimiento'] .'\'');
	$this->_db->addCamposUpdate('trazabilidad_codigo = \'' . $arrParametros['campos']['trazabilidad_codigo'] .'\'');	
	
	$this->_db->addFrom('detalle_transacciones_anmat');
	$this->_db->addWhere('transaccion_detalle_id = \'' . $arrParametros['id'] .'\'');
	    
	$this->_db->generarUpdate();   
	//echo $this->_db->getQry();
	$resultado= $this->_db->ejecutar();

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
}
