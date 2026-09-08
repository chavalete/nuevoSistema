<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/administracion/DatoRemito.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
 
class DatoRemitoExtendido extends DatoRemito
{
    private $_objFuncionesComunes;
    private $_db;
    
    public function __construct(){
    
        $this->_db = NEW FrenteAlmacenamiento();
    }

    public function getDataDB($id){
	
        $this->_db->addSelect('*');
        
        $this->_db->addFrom('datos_remitos');

        $this->_db->addWhere('remito_id = \'' . $id .'\'' );

        $this->_db->generarSelect();
        
        //echo $this->_db->getQry();

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
        $this->setIdMovimento($resultado['id_movimiento']);
        $this->setFacturaId($resultado['id_movimiento']);
        $this->setRemitoTotal($resultado['remito_total']);
        $this->setRemitoFacturado($resultado['remito_facturado']);
        $this->setRemitoCancelado($resultado['remito_cancelado']);
        $this->setRemitoId($resultado['remito_id']);
        $this->setRemitoFecha($resultado['remito_fecha']);
        $this->setClienteId($resultado['cliente_id']);
        $this->setRemitoNro($resultado['remito_nro']);
    }
    function salvarMe($arrParametros){
	       
        $this->_db->addCamposTabla('factura_id');
        $this->_db->addCamposValue($this->getFacturaVentaId());
        
        if($arrParametros['remitoId']!= null){
            $this->_db->addCamposTabla('remito_id');
            $this->_db->addCamposValue('\'' . $arrParametros['remitoId'] .'\'');
        }
        if($arrParametros['clienteId']!= null){
            $this->_db->addCamposTabla('cliente_id');
            $this->_db->addCamposValue('\'' . $arrParametros['clienteId'] .'\'');
        }
        if($arrParametros['remitoTotal']!= null){
            $this->_db->addCamposTabla('remito_total');
            $this->_db->addCamposValue('\'' . $arrParametros['remitoTotal'] .'\'');
        }
        if($arrParametros['idMovimiento']!= null){
            $this->_db->addCamposTabla('id_movimiento');
            $this->_db->addCamposValue('\'' . $arrParametros['idMovimiento'] .'\'');
        }
        
        $this->_db->addCamposTabla('remito_usuario_id');
        $this->_db->addCamposValue($_SESSION['usuarioId']);
		
        $this->_db->addFrom('datos_remitos');
	
        $this->_db->generarInsert();
        
        return  $this->_db->getQry();
        
    }
    
    function actualizarMe($arrParametros){
    
        
        $this->_db->addCamposUpdate('remito_facturado = true');
        
        if($arrParametros['facturaId']!=false){
            $this->_db->addCamposValue('\'' . $arrParametros['facturaId'] .'\'');
        }
        
        $this->_db->addFrom('datos_remitos');
        $this->_db->addWhere('remito_id = \'' . $arrParametros['remitoId'] .'\'');
            
        
        $this->_db->generarUpdate();   
        //asigamos el resulta que es un array a una variable
        return  $this->_db->getQry();
	}
}
