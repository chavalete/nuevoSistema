<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/administracion/DatoFacturaElectronica.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

class DatoFacturaElectronicaExtendido extends DatoFactura
{
    private $_objFuncionesComunes;
    private $_db;
    private $_descuento;
    private $_colObjDetalleFactura = Array();

    public function setDescuento($descuento){
        $this->_descuento = $descuento;
    }
    public function getDescuento(){
        return $this->_descuento;
    }
    public function setColObjDetalleFactura($objDetalle){
        $this->_colObjDetalleFactura[] = $objDetalle;
    }
    public function getColObjDetalleFactura(){
        return $this->_colObjDetalleFactura;
    }

    public function __construct(){
        $this->_db = NEW FrenteAlmacenamiento();
        $this->_objFuncionesComunes = NEW FuncionesComunes();
    }

    public function getDataDB($id){
        $this->_db->addSelect('factura_id');
        $this->_db->addFrom('datos_facturas_electronicas');
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
        $this->setFacturaFecha($resultado['factura_fecha']);
        $this->setFacturaNro($resultado['factura_nro']);
        $this->setFacturaClienteId($resultado['cliente_id']);
        $this->setFacturaObs($resultado['observaciones']);
        $this->setFacturaUsuarioId($resultado['factura_usuario_id']);
        $this->setFacturaTotal($resultado['factura_total_fact']);
        $this->setFacturaCancelada($resultado['factura_cancelada']);
        $this->setFacturaCanceladaFechaHora($resultado['factura_cancelada_fecha_hora']);
        $this->setFacturaCanceladaUsuarioId($resultado['factura_cancelada_usuario_id']);
        $this->setFacturaFaltanFact($resultado['factura_faltan_fact']);
        $this->setFacturaPaga($resultado['factura_paga']);
        $this->setFacturaIva($resultado['factura_total_iva']);
        $this->setFacturaLetra($resultado['factura_letra']);
        $this->setFacturaPuestoVenta($resultado['factura_puesto_venta']);
        $this->setFacturaEnviada($resultado['factura_enviada']);
        $this->setFacturaCae($resultado['factura_cae']);
        $this->setCaeVencimiento($resultado['vencimiento_cae']);
        $this->setDescuento($resultado['descuento']);
    }
    function salvarMe($arrParametros){
        if(isset($arrParametros['facturaId']) && $arrParametros['facturaId']){
            $this->_db->addCamposTabla('factura_id');
            $this->_db->addCamposValue($arrParametros['facturaId']);
        }
        if($arrParametros['clienteId']!= null){
            $this->_db->addCamposTabla('cliente_id');
            $this->_db->addCamposValue('\'' . $arrParametros['clienteId'] .'\'');
        }
        if($arrParametros['facturaLetra']!= null){
            $this->_db->addCamposTabla('factura_letra');
            $this->_db->addCamposValue('\'' . substr($arrParametros['facturaLetra'],0,1) .'\'');
        }
        if($arrParametros['observaciones']!= null){
            $this->_db->addCamposTabla('observaciones');
            $this->_db->addCamposValue('\'' . $arrParametros['observaciones'] .'\'');
        }
        if($arrParametros['facturaTotal']!= null){
            $this->_db->addCamposTabla('factura_total_fact');
            $this->_db->addCamposValue('\'' . $arrParametros['facturaTotal'] .'\'');
            $this->_db->addCamposTabla('factura_faltan_fact');
            $this->_db->addCamposValue('\'' . $arrParametros['facturaTotal'] .'\'');
        }
        if($arrParametros['facturaIva']!=null){
            $this->_db->addCamposTabla('factura_total_iva');
            $this->_db->addCamposValue('\'' . $arrParametros['facturaIva'] .'\'');
        }
        if($arrParametros['facturaElectronica']!=null){
            $this->_db->addCamposTabla('factura_electronica');
            $this->_db->addCamposValue('\'' . $arrParametros['facturaElectronica'] .'\'');
        }
        if($arrParametros['facturaPuestoVenta']!=null){
            $this->_db->addCamposTabla('factura_puesto_venta');
            $this->_db->addCamposValue('\'' . $arrParametros['facturaPuestoVenta'] .'\'');
        }
        if($arrParametros['facturaTipo']!=null){
            $this->_db->addCamposTabla('factura_tipo');
            $this->_db->addCamposValue('\'' . $arrParametros['facturaTipo'] .'\'');
        }
        if(isset($arrParametros['deFacturaId']) && $arrParametros['deFacturaId']!=null){
            $this->_db->addCamposTabla('de_factura_id');
            $this->_db->addCamposValue('\'' . $arrParametros['deFacturaId'] .'\'');
        }
        if($arrParametros['descuento']!=null){
            $this->_db->addCamposTabla('descuento');
            $this->_db->addCamposValue('\'' . $arrParametros['descuento'] .'\'');
        }
        if($arrParametros['pedidoId']!=null){
            $this->_db->addCamposTabla('pedido_id');
            $this->_db->addCamposValue('\'' . $arrParametros['pedidoId'] .'\'');
        }
        if($arrParametros['idMovimiento']!=null){
            $this->_db->addCamposTabla('id_movimiento');
            $this->_db->addCamposValue('\'' . $arrParametros['idMovimiento'] .'\'');
        }
        if($arrParametros['refacturadaId']!=null){
            $this->_db->addCamposTabla('refacturada_id');
            $this->_db->addCamposValue('\'' . $arrParametros['refacturadaId'] .'\'');
        }

        $this->_db->addCamposTabla('factura_usuario_id');
        $this->_db->addCamposValue($_SESSION['usuarioId']);

        $this->_db->addFrom('datos_facturas_electronicas');
        $this->_db->generarInsert();
        return  $this->_db->getQry();
    }
}
?>
