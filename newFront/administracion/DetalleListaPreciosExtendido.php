<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/administracion/DetalleListsPrecio.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';


class DetalleListaPreciosExtendido extends DetalleListaPrecio
{
    private $_objFuncionesComunes;
    private $_db;
    private $_cantidadPromo;
    private $_precioPromo;
    
    public function setCantidadPromocion($cantidad){
        $this->_cantidadPromo = $cantidad;
    }
    public function getCantidadPromocion(){
        return $this->_cantidadPromo;
    }
    public function setPrecioPromo($precioPromo){
        $this->_precioPromo = $precioPromo;
    }
    public function getPrecioPromo(){
        return $this->_precioPromo;
    }
    public function __construct(){
	$this->_db = NEW FrenteAlmacenamiento();
    }

    public function getDataDB($id){
	
	
    
    }
    function cargarMe($datos) {
	
	if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}
	$this->setListaId($resultado['lista_id']);
	$this->setProductoId($resultado['producto_id']);
	$this->setProductoPventa($resultado['producto_pventa']);
	$this->setDescuento($resultado['descuento']);
	$this->setCantidadPromocion($resultado['cantidad_promocion']);
	$this->setPrecioPromo($resultado['precio_promocion']);
	

    }
    function salvarMe($arrParametros){	        

        $this->_db->addCamposTabla('factura_id');
	$this->_db->addCamposValue('\'' . $arrParametros['facturaId'] .'\'');
        
        if($arrParametros['pesoFacturado']!= null){
            $this->_db->addCamposTabla('peso_facturado');
            $this->_db->addCamposValue('\'' . $arrParametros['pesoFacturado'] .'\'');
        }
        if($arrParametros['precioFacturado']!= null){
            $this->_db->addCamposTabla('precio_facturado');
            $this->_db->addCamposValue('\'' . $arrParametros['precioFacturado'] .'\'');
        }
        if($arrParametros['remitoId']!= null){
            $this->_db->addCamposTabla('remito_id');
            $this->_db->addCamposValue('\'' . $arrParametros['remitoId'] .'\'');
        }
	$this->_db->addFrom('detalle_compras_facturas');
	
        $this->_db->generarInsert();
        
        
        return  $this->_db->getQry();
        
    }
    function actualizarMe($arrParametros){

	
	list($listaId,$productoId) = explode("|",$arrParametros[id]);
	$this->_db->addCamposUpdate('producto_pventa = \'' . $arrParametros['campos']['producto_pventa'] .'\'');
	$this->_db->addCamposUpdate('cantidad_promocion = \'' . $arrParametros['campos']['unidades_promo'] .'\'');
	$this->_db->addCamposUpdate('precio_promocion = \'' . $arrParametros['campos']['precio_promo'] .'\'');
	
	$this->_db->addFrom('lista_precios_10');
	$this->_db->addWhere('producto_id = \'' . $productoId .'\'');
	    
	//generar qry
	$this->_db->generarUpdate();   
	//echo $this->_db->getQry();exit;
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
