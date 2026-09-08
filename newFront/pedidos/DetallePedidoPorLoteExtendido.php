<?php
//Almacenamiento
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
//Libreria
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/pedidos/DetallePedido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/ProductoExtendidoAnmat.php';

class DetallePedidoPorLoteExtendido extends DetallePedido
{

    private $_db;
    private $_productoGtin;
    private $_objLote;
    private $_objEstanteria;

    
    
    public function __construct() {
     
	$this->_db = new FrenteAlmacenamiento();
    }
    public function getArrError(){
        return $this->_arrError;
    }
    public function setArrError($arrError){
        $this->_arrError = $arrError;
    }
    public function setObjLote($objLote){
	$this->_objLote = $objLote;
    }
    public function getObjLote(){
	return $this->_objLote;
    }
    public function setObjEstanteria($objEstanteria){
	$this->_objEstanteria = $objEstanteria;
    }
    public function getObjEstanteria(){
	return $this->_objEstanteria;
    }
    
    function getDataDB($id){
	
	$this->_db->addSelect('pedido_id');
	$this->_db->addSelect('producto_id');
	$this->_db->addSelect('cantidad');
        $this->_db->addSelect('unidades');
	$this->_db->addSelect('pedido_detalle_id');
        $this->_db->addFrom('pedidos_lotes');
        $this->_db->addWhere('pedido_detalle_id = ' . $id );
       
        $this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        return $resultado[0];
    
    }
    public function cargarme($datos){

	//var_dump($datos);exit;
        if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}
        
        $this->setPedidoId($resultado['pedido_id']);
        $this->setPedidoCantidad($resultado['cantidad']);
	$this->setPedidoDetaleId($resultado['pedido_detalle_id']);
        $this->setPedidoUnidades($resultado['unidades']);

    }

    public function salvarme($arrParametros){
        if($arrParametros['idPedido'] != null){
            $this->_db->addCamposTabla('pedido_id');
            $this->_db->addCamposValue("'" . $arrParametros['idPedido'] . "'");
        }else{
            $errorDetalle = 'No se recibio el pedido_id';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
        
        if($arrParametros['pedidoDetalleId'] != null){
            $this->_db->addCamposTabla('pedido_detalle_id');
            $this->_db->addCamposValue("'" . $arrParametros['pedidoDetalleId'] . "'");
        }else{
            $errorDetalle = 'No se recibio el pedido_detalle_id';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
        
        if($arrParametros['productoId'] != null){
            $this->_db->addCamposTabla('producto_id');
            $this->_db->addCamposValue("'" . $arrParametros['productoId'] . "'");
        }else{
            $errorDetalle = 'No se recibio el producto';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
        if($arrParametros['cantidad'] != null){
            $this->_db->addCamposTabla('cantidad');
            $this->_db->addCamposValue("'" . $arrParametros['cantidad'] . "'");
        }else{
            $errorDetalle = 'No se recibio la cantidad solicitada';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
        if($arrParametros['unidades'] != null){
            $this->_db->addCamposTabla('unidades');
            $this->_db->addCamposValue("'" . $arrParametros['unidades'] . "'");
        }else{
            $errorDetalle = 'No se recibio las unidades solicitadas';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
        
        if(count($this->getArrError()) > 0){
            return;
        }
        
        $this->_db->addFrom('detalle_pedidos');
        
        $this->_db->generarInsert();
        
        //echo $this->_db->getQry();exit;
        return $this->_db->getQry();
        

    }
    public function actualizarme($arrParametros){
    }

}
