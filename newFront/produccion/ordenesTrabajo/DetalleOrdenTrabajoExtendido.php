<?php
//Almacenamiento
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
//Libreria
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/produccion/ordenesTrabajo/DetalleOrdenTrabajo.php';


class DetalleOrdenTrabajoExtendido extends DetalleOrdenTrabajo
{

    private $_db;

    public function __construct() {
     
	$this->_db = new FrenteAlmacenamiento();
    }
    public function getArrError(){
        return $this->_arrError;
    }
    public function setArrError($arrError){
        $this->_arrError = $arrError;
    }
    function getDataDB($id){
	
	$this->_db->addSelect('orden_id');
	$this->_db->addSelect('producto_id');
	$this->_db->addSelect('cantidad');
	$this->_db->addSelect('orden_detalle_id');
        $this->_db->addFrom('detalle_ordenes_trabajo');
        $this->_db->addWhere('orden_detalle_id = ' . $id );
       
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
        
        $this->setOrdenTrabajoId($resultado['orden_id']);
        $this->setOrdenTrabajoCantidad($resultado['cantidad']);
	$this->setOrdenTrabajoDetaleId($resultado['orden_detalle_id']);

    }

    public function salvarme($arrParametros){
        if($arrParametros['idOrdenTrabajo'] != null){
            $this->_db->addCamposTabla('orden_id');
            $this->_db->addCamposValue("'" . $arrParametros['idOrdenTrabajo'] . "'");
        }else{
            $errorDetalle = 'No se recibio el orden_id';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
        
        if($arrParametros['ordenDetalleId'] != null){
            $this->_db->addCamposTabla('orden_detalle_id');
            $this->_db->addCamposValue("'" . $arrParametros['ordenDetalleId'] . "'");
        }else{
            $errorDetalle = 'No se recibio el orden_detalle_id';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
        
        if($arrParametros['0'] != null){
            $this->_db->addCamposTabla('producto_id');
            $this->_db->addCamposValue("'" . $arrParametros['0'] . "'");
        }else{
            $errorDetalle = 'No se recibio el producto';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
        if($arrParametros['1'] != null){
            $this->_db->addCamposTabla('cantidad');
            $this->_db->addCamposValue("'" . $arrParametros['1'] . "'");
        }else{
            $errorDetalle = 'No se recibio la cantidad solicitada';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
        
        if(count($this->getArrError()) > 0){
            return;
        }
        
        $this->_db->addFrom('detalle_ordenes_trabajo');
        
        $this->_db->generarInsert();
        
        //echo $this->_db->getQry();exit;
        return $this->_db->getQry();
        

    }
    public function actualizarme($arrParametros){
    }

}
