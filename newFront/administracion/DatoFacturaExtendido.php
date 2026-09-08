<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/administracion/DatoFactura.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
 
class DatoFacturaExtendido extends DatoFactura
{
    private $_objFuncionesComunes;
    private $_db;
    
    public function __construct(){
        $this->_db = NEW FrenteAlmacenamiento();
        $this->_objFuncionesComunes = NEW FuncionesComunes();
    }

    public function getDataDB($id){
    
        $this->_db->addSelect('factura_id, factura_total_fact, factura_faltan_fact');
        $this->_db->addFrom('datos_facturas');
        $this->_db->addWhere('factura_id = \'' . $id .'\'' );
        $this->_db->generarSelect();

        $resultado =  $this->_db->ejecutar();
        
        return $resultado[0];
    
    }
    function cargarMe($datos) {
        //var_dump($datos);
        if(is_numeric($datos)){
            $resultado = $this->getDataDB($datos);
        }else{
            $resultado = $datos;
        }
        
        $this->setFacturaId($resultado['factura_id']);
        $this->setFacturaFecha($resultado['factura_fecha']);
        $this->setFacturaNro($resultado['factura_nro']);
        $this->setFacturaClienteId($resultado['cliente_id']);
        //$this->setFacturaLoteId($resultado['lote_id']);
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
        $this->setFacturaEstadoId($resultado['id_estado']);
        $this->setVendedorId($resultado['vendedor_id']);
        
        
    }
    function salvarMe($arrParametros){
	       
        if($arrParametros['facturaId']){
            $this->_db->addCamposTabla('factura_id');
            $this->_db->addCamposValue($arrParametros['facturaId']);
        }
        if($arrParametros['clienteId']!= null){
            $this->_db->addCamposTabla('cliente_id');
            $this->_db->addCamposValue('\'' . $arrParametros['clienteId'] .'\'');
        }
        if($arrParametros['facturaFecha']!= null){
            $this->_db->addCamposTabla('factura_fecha');
            $this->_db->addCamposValue('\'' . $arrParametros['facturaFecha'] .'\'');
        }
        if($arrParametros['facturaLetra']!= null){
            $this->_db->addCamposTabla('factura_letra');
            $this->_db->addCamposValue('\'' . substr($arrParametros['facturaLetra'],0.1) .'\'');
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
        
        $this->_db->addCamposTabla('factura_usuario_id');
        $this->_db->addCamposValue($_SESSION['usuarioId']);
	
        $this->_db->addFrom('datos_facturas');
	
        $this->_db->generarInsert();
        
        return  $this->_db->getQry();
        
        //$arrQry['ctaCte'] = "SELECT fn_actualizar_stock_compras({$id[0]['nextval']},3);";
    }
    
    function actualizarMe($arrParametros){

        if($arrParametros['importeCancelado']!=false){
            $this->_db->addCamposUpdate('factura_faltan_fact = factura_faltan_fact - \'' . $arrParametros['campos']['estados'] .'\'');
        }
        $this->_db->addFrom('datos_facturas');
        $this->_db->addWhere('factura_id = \'' . $arrParametros['facturaId'] .'\'');
	    
        //generar qry
        $this->_db->generarUpdate();   
        
        return $this->_db->getQry();

    }
}
