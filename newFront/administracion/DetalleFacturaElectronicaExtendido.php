<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/administracion/DetalleFacturaElectronica.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

class DetalleFacturaElectronicaExtendido extends DetalleFactura
{
    private $_objFuncionesComunes;
    private $_db;

    public function __construct(){
	    $this->_db = NEW FrenteAlmacenamiento();
	    $this->_objFuncionesComunes = NEW FuncionesComunes();
    }

    public function getDataDB($id){
        $this->_db->addSelect('*');
        $this->_db->addFrom('detalle_facturas_electronicas');
        $this->_db->addWhere('factura_id = \'' . $id .'\'' );
        $this->_db->generarSelect();

        $resultado =  $this->_db->ejecutar();
        return $resultado[0];
    }
    function cargarMe($datos) {
        if(is_numeric($datos)){
            $resultado = $this->getDataDB($datos);
        }else{
            $resultado = $datos;
        }
        $this->setFacturaId($resultado['factura_id']);
        $this->setProductoid($resultado['producto_id']);
        $this->setCantidad($resultado['cantidad']);
        $this->setRemitoNro($resultado['remito_nro']);
        $this->setImporteIva($resultado['importe_iva']);
        $this->setImporteUnitario($resultado['importe_unitario']);
        $this->setImporteDetalle($resultado['importe_detalle']);
        $this->setDetalleFacturaId($resultado['detalle_factura_id']);
    }
    function salvarMe($arrParametros){
        $this->_db->addCamposTabla('factura_id');
        $this->_db->addCamposValue('\'' . $arrParametros['facturaId'] .'\'');

        if($arrParametros['productoId']!= null){
            $this->_db->addCamposTabla('producto_id');
            $this->_db->addCamposValue('\'' . $arrParametros['productoId'] .'\'');
        }
        if($arrParametros['cantidad']!= null){
            $this->_db->addCamposTabla('cantidad');
            $this->_db->addCamposValue('\'' . $arrParametros['cantidad'] .'\'');
        }
        if($arrParametros['remitoNro']!= null){
            $this->_db->addCamposTabla('remito_nro');
            $this->_db->addCamposValue('\'' . $arrParametros['remitoNro'] .'\'');
        }
        if($arrParametros['importeIva']!= null){
            $this->_db->addCamposTabla('importe_iva');
            $this->_db->addCamposValue('\'' . $arrParametros['importeIva'] .'\'');
        }
        if($arrParametros['importeUnitario']!= null){
            $this->_db->addCamposTabla('importe_unitario');
            $this->_db->addCamposValue('\'' . $arrParametros['importeUnitario'] .'\'');
        }
        if($arrParametros['importeDetalle']!= null){
            $this->_db->addCamposTabla('importe_detalle');
            $this->_db->addCamposValue('\'' . $arrParametros['importeDetalle'] .'\'');
        }
        if($arrParametros['productoPventa']!= null){
            $this->_db->addCamposTabla('producto_pventa');
            $this->_db->addCamposValue('\'' . $arrParametros['productoPventa'] .'\'');
        }
        if($arrParametros['productoPcosto']!= null){
            $this->_db->addCamposTabla('producto_pcosto');
            $this->_db->addCamposValue('\'' . $arrParametros['productoPcosto'] .'\'');
        }
        if($arrParametros['descuento']!= null){
            $this->_db->addCamposTabla('descuento');
            $this->_db->addCamposValue('\'' . $arrParametros['descuento'] .'\'');
        }
        $this->_db->addFrom('detalle_facturas_electronicas');
        $this->_db->generarInsert();
        return  $this->_db->getQry();
    }
}
?>
