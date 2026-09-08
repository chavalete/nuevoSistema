<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/administracion/DatoLiquidacion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
 
class DatoLiquidacionExtendido extends DatoLiquidacion
{
    private $_objFuncionesComunes;
    private $_db;
    
    public function __construct(){
        $this->_db = NEW FrenteAlmacenamiento();
        $this->_objFuncionesComunes = NEW FuncionesComunes();
    }

    public function getDataDB($id){
    
        $this->_db->addSelect('*');
        $this->_db->addFrom('datos_liquidaciones');
        $this->_db->addWhere('liquidacion_id = \'' . $id .'\'' );
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
        $this->setLiquidacionId($resultado['liquidacion_id']);
        $this->setLiquidacionFecha($resultado['liquidacion_fecha']);
        $this->setLiquidacionFechaDesde($resultado['fecha_desde']);
        $this->setLiquidacionFechaHasta($resultado['fecha_hasta']);
        $this->setLiquidacionImporte($resultado['liquidacion_importe']);
        $this->setVendedorId($resultado['vendedor_id']);
        $this->setObservaciones($resultado['observaciones']);
        $this->setLiquidacionCancelada($resultado['liquidacion_cancelada']);
        
        
    }
    function salvarMe($arrParametros){
	       
        if($arrParametros['liquidacionId']!= null){
            $this->_db->addCamposTabla('liquidacion_id');
            $this->_db->addCamposValue('\'' . $arrParametros['liquidacionId'] .'\'');
        }
        if($arrParametros['fechaDesde']!= null){
            $this->_db->addCamposTabla('fecha_desde');
            $this->_db->addCamposValue('\'' . $arrParametros['fechaDesde'] .'\'');
        }
        if($arrParametros['fechaHasta']!= null){
            $this->_db->addCamposTabla('fecha_hasta');
            $this->_db->addCamposValue('\'' . $arrParametros['fechaHasta'] .'\'');
        }
        if($arrParametros['observaciones']!= null){
            $this->_db->addCamposTabla('observaciones');
            $this->_db->addCamposValue('\'' . $arrParametros['observaciones'] .'\'');
        }
        if($arrParametros['vendedorId']!=null){
            $this->_db->addCamposTabla('vendedor_id');
            $this->_db->addCamposValue('\'' . $arrParametros['vendedorId'] .'\'');
        }
        if($arrParametros['importeTotal']!=null){
            $this->_db->addCamposTabla('liquidacion_importe');
            $this->_db->addCamposValue('\'' . $arrParametros['importeTotal'] .'\'');
        }
        
        $this->_db->addCamposTabla('liquidacion_usuario_id');
        $this->_db->addCamposValue($_SESSION['usuarioId']);
	
        $this->_db->addFrom('datos_liquidaciones');
	
        $this->_db->generarInsert();
        //echo $this->_db->getQry();exit;
    
        return  $this->_db->getQry();
    }
    
    function actualizarMe($id){

        $this->_db->addCamposUpdate('liquidacion_cancelada = true');
        $this->_db->addCamposUpdate('liquidacion_cancelada_fecha = now()');
        $this->_db->addCamposUpdate("liquidacion_cancelada_usuario_id  = $_SESSION[usuarioId]");
        $this->_db->addFrom('datos_liquidaciones');
        $this->_db->addWhere('liquidacion_id = \'' . $id .'\'');
	    
        //generar qry
        $this->_db->generarUpdate();   
        
        return $this->_db->getQry();

    }
}
