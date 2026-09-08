<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/administracion/DetalleRemito.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
 
class DetalleRemitoExtendido extends DetalleRemito
{
    private $_objFuncionesComunes;
    private $_db;
    
    public function __construct(){
        $this->_db = NEW FrenteAlmacenamiento();
    }

    public function getDataDB($id){
	
        $this->_db->addSelect('*');
        
        $this->_db->addFrom('detalle_remitos');

        $this->_db->addWhere('remito_id = \'' . $id .'\'' );

        $this->_db->generarSelect();
        
        //echo $this->_db->getQry();

        $resultado =  $this->_db->ejecutar();
        
        return $resultado[0];
    
    }
    function cargarMe($datos) {
	
        if(is_numeric($datos)){
            $resultado = $this->getDataDB($datos);
        }else{
            $resultado = $datos;
        }
        $this->setImporteDetalle($resultado['importe_detalle']);
        $this->setImporteUnitario($resultado['importe_unitario']);
        $this->setProductoId($resultado['producto_id']);
        $this->setCantidad($resultado['cantidad']);
        $this->setRemitoDetalleId($resultado['remito_detalle_id']);
        $this->setRemitoId($resultado['remito_id']);
        $this->setDescuento($resultado['descuento']);
	
    }
    function salvarMe($arrParametros){
	
        $this->_db->addCamposTabla('remito_id');
        $this->_db->addCamposValue('\'' . $arrParametros['remito_id'] .'\'');
        
        if($arrParametros['productoId']!= null){
            $this->_db->addCamposTabla('producto_id');
            $this->_db->addCamposValue('\'' . $arrParametros['productoId'] .'\'');
        }
        if($arrParametros['cantidad']!= null){
            $this->_db->addCamposTabla('cantidad');
            $this->_db->addCamposValue('\'' . $arrParametros['cantidad'] .'\'');
        }
        if($arrParametros['importeDetalle']!= null){
            $this->_db->addCamposTabla('importe_detalle');
            $this->_db->addCamposValue('\'' . $arrParametros['importeDetalle'] .'\'');
        }
        if($arrParametros['importeUnitario']!= null){
            $this->_db->addCamposTabla('importe_unitario');
            $this->_db->addCamposValue('\'' . $arrParametros['importeUnitario'] .'\'');
        }

        $this->_db->addFrom('detalle_remitos');
	
        $this->_db->generarInsert();
        
        return  $this->_db->getQry();
    }
    function actualizarMe($arrParametros){

        if($arrParametros['campos']['estados']!=false){
            $this->_db->addCamposUpdate('estado_id = \'' . $arrParametros['campos']['estados'] .'\'');
        }
        
        
        $this->_db->addFrom('datos_compras');
        $this->_db->addWhere('compra_id = \'' . $arrParametros['id'] .'\'');
            
        //generar qry
        $this->_db->generarUpdate();   
        //asigamos el resulta que es un array a una variable
        //$resultado = $this->_db->ejecutar();

    }
}
