<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/administracion/DatoCobro.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
 
class DatoCobroExtendido extends DatoCobro
{
    private $_objFuncionesComunes;
    private $_db;
    
    public function __construct(){
        $this->_db = NEW FrenteAlmacenamiento();
        $this->_objFuncionesComunes = NEW FuncionesComunes();
    }

    public function setRelacionNombre($relacionNombre){
        $this->_relacionNombre = $relacionNombre;
    }
    public function getRelacionNombre(){
        return $this->_relacionNombre;
    }
    public function getDataDB($id){
    
        $this->_db->addSelect('*');
        $this->_db->addFrom('datos_cobros');
        $this->_db->addWhere('cobro_id = \'' . $id .'\'' );
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
        $this->setCobroId($resultado['cobro_id']);
        $this->setCobroFecha($resultado['fecha_cobro']);
        $this->setOrdenCobroNro($resultado['orden_cobro_nro']);
        $this->setCobroClienteId($resultado['cliente_id']);
        //$this->setCobroLoteId($resultado['lote_id']);
        $this->setCobroObs($resultado['obscobro']);
        $this->setCobroUsuarioId($resultado['cobro_usuario_id']);
        $this->setCobroTotal($resultado['importe_total_cobro']);
        $this->setCobroCancelada($resultado['cobro_cancelado']);
        $this->setCobroCanceladaFechaHora($resultado['cobro_cancelado_fecha_hora']);
        $this->setCobroCanceladaUsuarioId($resultado['cobro_cancelado_usuario_id']);
        $this->setRelacionNombre($resultado['relacion_nombre']);
        
    }
    function salvarMe($arrParametros){
	       
        if($arrParametros['cobroId']){
            $this->_db->addCamposTabla('cobro_id');
            $this->_db->addCamposValue($arrParametros['cobroId']);
        }
        if($arrParametros['cobroFecha']){
            $this->_db->addCamposTabla('fecha_cobro');
            $this->_db->addCamposValue('\'' . $arrParametros['cobroFecha'] .'\'');
        }
        if($arrParametros['clienteId']!= null){
            $this->_db->addCamposTabla('cliente_id');
            $this->_db->addCamposValue('\'' . $arrParametros['clienteId'] .'\'');
        }
        if($arrParametros['relacionId']!= null){
            $this->_db->addCamposTabla('relacion_id');
            $this->_db->addCamposValue('\'' . $arrParametros['relacionId'] .'\'');
        }
        if($arrParametros['observaciones']!= null){
            $this->_db->addCamposTabla('observaciones');
            $this->_db->addCamposValue('\'' . $arrParametros['observaciones'] .'\'');
        }
        if($arrParametros['importeTotal']!= null){
            $this->_db->addCamposTabla('importe_total_cobro');
            $this->_db->addCamposValue('\'' . $arrParametros['importeTotal'] .'\'');
        }
        
        $this->_db->addCamposTabla('cobro_usuario_id');
        $this->_db->addCamposValue($_SESSION['usuarioId']);
        $this->_db->addCamposTabla('sucursal_id');
        $this->_db->addCamposValue($_SESSION['sucursalId']);
	
        $this->_db->addFrom('datos_cobros');
	
        $this->_db->generarInsert();
        
        //echo $this->_db->getQry();exit;
        
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
    public function actualizar($arrParametros){
        $this->_db->addCamposUpdate('fecha_cobro  = \'' . $arrParametros['campos']['cobro_fecha'] .'\'');
        $this->_db->addCamposUpdate('actualizado_usuario_id  = \'' . $_SESSION['usuarioId'] .'\'');
        $this->_db->addCamposUpdate('actualizado_fecha_hora  = now()');
        $this->_db->addFrom('datos_cobros');
        $this->_db->addWhere('cobro_id = \'' . $arrParametros['id'] .'\'');

        //generar qry
        $this->_db->generarUpdate();
        $this->_db->ejecutar();

        $qry="SELECT * FROM datos_cobros WHERE fecha_cobro='{$arrParametros['campos']['cobro_fecha']}' AND cobro_id = {$arrParametros['id']};";
        $this->_db->setQry($qry);
        $resultado =  $this->_db->ejecutar();
        if(count($resultado)>0){
            return true;
        }else{
            return false;
        }
    }
}
