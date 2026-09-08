<?php
//ALMACENAMIENTO
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
//LIBRERIA
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/stock/PresentacionesProductos.php';

/**
 * Description of PresentacionesProductosExtendido
 *
 * @author Guillo
 */
class PresentacionesProductosExtendido extends PresentacionesProductos {
    
    private $_db;
    private $_objProducto;
    private $_objPresentacion;
    private $_productoPresentacionUnidadVentaMensaje;
    private $_productoPresentacionUnidadVentaDesc;
    private $_productoPresentacionNoUnidadVentaDesc;
    
    private $_productoPresentacionUnidadDistribucionMensaje;
    private $_productoPresentacionUnidadDistribucionDesc;
    private $_productoPresentacionNoUnidadDistribucionDesc;
    
    private $_productoPresentacionActivoMensaje;
    private $_productoPresentacionActivoDesc;
    private $_productoPresentacionNoActivoDesc;
    
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
    }
    public function setColObjProductos($objProducto){
	$this->_objProducto = $objProducto;
    }
    public function getColObjProductos(){
	return $this->_objProducto;
    }
    public function setColObjPresentaciones($objPresentacion){
	$this->_objPresentacion = $objPresentacion;
    }
    public function getColObjPresentaciones(){
	return $this->_objPresentacion;
    }
    public function setProductoPresentacionUnidadVendaDesc($productoPresentacionUnidadVentaDesc){
	$this->_productoPresentacionUnidadVentaDesc= $productoPresentacionUnidadVentaDesc;
    }
    public function getProductoPresentacionUnidadVentaDesc(){
	return $this->_productoPresentacionUnidadVentaDesc;
    }
    public function setProductoPresentacionNoUnidadVentaDesc($productoPresentacionNoUnidadVentaDesc){
	$this->_productoPresentacionNoUnidadVentaDesc= $productoPresentacionNoUnidadVentaDesc;
    }
    public function getProductoPresentacionNoUnidadVentaDesc(){
	return $this->_productoPresentacionNoUnidadVentaDesc;
    }
    public function setProductoPresentacionUnidadVentaMensaje($productoPresentacionUnidadVentaMensaje){
	$this->_productoPresentacionUnidadVentaMensaje= $productoPresentacionUnidadVentaMensaje;
    }
    public function getProductoPresentacionUnidadVentaMensaje(){
	return $this->_productoPresentacionUnidadVentaMensaje;
    }       
    public function setProductoPresentacionUnidadDistribucionDesc($productoPresentacionUnidadDistribucionDesc){
	$this->_productoPresentacionUnidadDistribucionDesc= $productoPresentacionUnidadDistribucionDesc;
    }
    public function getProductoPresentacionUnidadDistribucionDesc(){
	return $this->_productoPresentacionUnidadDistribucionDesc;
    }
    public function setProductoPresentacionNoUnidadDistribucionDesc($productoPresentacionNoUnidadDistribucionDesc){
	//echo $productoPresentacionNoUnidadDistribucionDesc;
	$this->_productoPresentacionNoUnidadDistribucionDesc= $productoPresentacionNoUnidadDistribucionDesc;
    }
    public function getProductoPresentacionNoUnidadDistribucionDesc(){
	return $this->_productoPresentacionNoUnidadDistribucionDesc;
    }
    public function setProductoPresentacionUnidadDistribucionMensaje($productoPresentacionUnidadDistribucionMensaje){
	$this->_productoPresentacionUnidadDistribucionMensaje= $productoPresentacionUnidadDistribucionMensaje;
    }
    public function getProductoPresentacionUnidadDistribucionMensaje(){
	return $this->_productoPresentacionUnidadDistribucionMensaje;
    }
    
    public function setProductoPresentacionActivoDesc($productoPresentacionActivoDesc){
	$this->_productoPresentacionActivoDesc = $productoPresentacionActivoDesc;
    }
    public function getProductoPresentacionActivoDesc(){
	return $this->_productoPresentacionActivoDesc;
    }
    public function setProductoPresentacionNoActivoDesc($productoPresentacionNoActivoDesc){
	$this->_productoPresentacionNoActivoDesc = $productoPresentacionNoActivoDesc;
    }
    public function getProductoPresentacionNoActivoDesc(){
	return $this->_productoPresentacionNoActivoDesc;
    }
    public function setProductoPresentacionActivoMensaje($productoPresentacionActivoMensaje){
	$this->_productoPresentacionActivoMensaje = $productoPresentacionActivoMensaje;
    }
    public function getProductoPresentacionActivoMensaje(){
	return $this->_productoPresentacionActivoMensaje;
    }

    public function getDataDB($relacionId){
        $this->_db->addSelect('pp.presentacion_id');
	$this->_db->addSelect('pp.producto_id');
	$this->_db->addSelect('pp.gtin');
	$this->_db->addSelect('pp.unidad_venta');
        $this->_db->addSelect('pp.unidad_distribucion');
        $this->_db->addSelect('pp.unidades');
        $this->_db->addSelect('pp.relacion_id');
        $this->_db->addSelect('pp.relacion_usuario_id');
        $this->_db->addSelect('pp.relacion_fecha_hora');
        $this->_db->addSelect('pp.relacion_inactiva_fecha_hora');
        $this->_db->addSelect('pp.relacion_inactiva_usuario_id');
        $this->_db->addSelect('pp.relacion_activa');
        $this->_db->addSelect('\'Si\'  AS producto_presentacion_unidad_venta_desc');
        $this->_db->addSelect('\'No\'  AS producto_presentacion_no_unidad_venta_desc');
        $this->_db->addSelect('CASE WHEN unidad_venta THEN \'Si\' ELSE \'No\' END AS producto_presentacion_unidad_venta_mensaje');
        
        $this->_db->addSelect('\'Si\'  AS producto_presentacion_unidad_distribucion_desc');
        $this->_db->addSelect('\'No\'  AS producto_presentacion_no_unidad_distribucion_desc');
        $this->_db->addSelect('CASE WHEN unidad_distribucion THEN \'Si\' ELSE \'No\' END AS producto_presentacion_unidad_distribucion_mensaje');
        
        $this->_db->addSelect('\'Si\'  AS producto_presentacion_activa_desc');
        $this->_db->addSelect('\'No\'  AS producto_presentacion_no_activa_desc');
        $this->_db->addSelect('CASE WHEN relacion_activa THEN \'Si\' ELSE \'No\' END AS producto_presentacion_activa_mensaje');
        
	
        $this->_db->addFrom('presentaciones_productos pp');
        
        $this->_db->addWhere('relacion_id =' . $relacionId);
        
        $this->_db->generarSelect();
        $resultado = $this->_db->ejecutar();
        //echo $this->_db->getQry();
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
		      "mensaje" =>'La presentación no existe o no fue relacionada al producto, Verificar.'
		    );
		echo json_encode($arrDevolver);exit;
        }
        
        $this->setPresentacionId($resultado['presentacion_id']);
        $this->setProductoId($resultado['producto_id']);
        $this->setGtin($resultado['gtin']);
        $this->setUnidadVenta($resultado['unidad_venta']);
        $this->setUnidadDistribucion($resultado['unidad_distribucion']);
        $this->setUnidades($resultado['unidades']);
        $this->setRelacionId($resultado['relacion_id']);
        $this->setRelacionActiva($resultado['relacion_activa']);
        $this->setProductoPresentacionUnidadVendaDesc($resultado['producto_presentacion_unidad_venta_desc']);
	$this->setProductoPresentacionNoUnidadVentaDesc($resultado['producto_presentacion_no_unidad_venta_desc']);
	$this->setProductoPresentacionUnidadVentaMensaje($resultado['producto_presentacion_unidad_venta_mensaje']);
	
	$this->setProductoPresentacionUnidadDistribucionDesc($resultado['producto_presentacion_unidad_distribucion_desc']);
	$this->setProductoPresentacionNoUnidadDistribucionDesc($resultado['producto_presentacion_no_unidad_distribucion_desc']);
	$this->setProductoPresentacionUnidadDistribucionMensaje($resultado['producto_presentacion_unidad_distribucion_mensaje']);
	
	$this->setProductoPresentacionActivoDesc($resultado['producto_presentacion_activa_desc']);
	$this->setProductoPresentacionNoActivoDesc($resultado['producto_presentacion_no_activa_desc']);
	$this->setProductoPresentacionActivoMensaje($resultado['producto_presentacion_activa_mensaje']);
        
    }

    function salvarMe($arrParametros){
        
        $this->_db->addCamposTabla('seq_relacion_id_productos_presentaciones');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();
	
	$this->setRelacionId($id[0]['nextval']);
	$this->setProductoId($arrParametros['productos']);
	$this->setPresentacionId($arrParametros['presentaciones']);
	$this->setGtin($arrParametros['gtin']);
	$this->setUnidadVenta($arrParametros['unidadVenta']);
	$this->setUnidadDistribucion($arrParametros['unidadDistribucion']);
	$this->setUnidades($arrParametros['unidades']);
	
        
        $this->_db->addFrom('presentaciones_productos');
        
        
        $this->_db->addCamposTabla('producto_id');
        $this->_db->addCamposValue('\'' . $this->getProductoId() . '\'');
        
        $this->_db->addCamposTabla('gtin');
        $this->_db->addCamposValue('\'' . $this->getGtin() . '\'');

        $this->_db->addCamposTabla('unidades');
        $this->_db->addCamposValue('\'' . $this->getUnidades() . '\'');
        
        $this->_db->addCamposTabla('relacion_id');
        $this->_db->addCamposValue('\'' . $this->getRelacionId() . '\'');
        
        $this->_db->addCamposTabla('relacion_usuario_id');
        $this->_db->addCamposValue('\'' . $_SESSION['usuarioId'] . '\'');
        
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
		      "mensaje" => "Relacion creada con exito"
		    );
        }
        echo json_encode($arrDevolver);exit;
    }
    
    public function actualizarMe($arrParametros){

	$this->_db->addCamposUpdate('gtin = \'' . $arrParametros['campos']['gtin'] .'\'');
	if(is_numeric($arrParametros['campos']['productos'])){
	    $this->_db->addCamposUpdate('producto_id = \'' . $arrParametros['campos']['productos'] .'\'');
	}
	if($arrParametros['campos']['relacion_activa'] == 1){
		$this->_db->addCamposUpdate('relacion_activa = true');
	}else{
		$this->_db->addCamposUpdate('relacion_activa = false');
	}
	
	$this->_db->addFrom('presentaciones_productos');
	$this->_db->addWhere('relacion_id = \'' . $arrParametros['id'] .'\'');
	    
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
