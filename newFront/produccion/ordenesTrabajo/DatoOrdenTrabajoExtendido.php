<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/produccion/ordenesTrabajo/DatoOrdenTrabajo.php';
require_once 'DetalleOrdenTrabajoExtendido.php';
require_once 'DetalleOrdenTrabajoPorLoteExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/EstadoExtendido.php';

class DatoOrdenTrabajoExtendido extends DatoOrdenTrabajo
{
    private $_db;
    
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
        
    }  
    
    public function getDataDB($id){
	
	$this->setOrdenTrabajoId($id);

	$this->_db->addSelect('orden_id');
        $this->_db->addSelect('orden_fecha_hora');
        $this->_db->addSelect('orden_nro');
        $this->_db->addSelect('producto_destino_id');
        $this->_db->addSelect('producto_destino_cantidad');
        $this->_db->addSelect('lote');
        $this->_db->addSelect('lote_vencimiento');
        $this->_db->addSelect('estanteria_id');
        $this->_db->addSelect('orden_estado_id');
        $this->_db->addSelect('observaciones');
	$this->_db->addSelect('orden_usuario_id');
	$this->_db->addSelect('orden_preparada');
	$this->_db->addSelect('orden_preparada_fecha_hora');
	$this->_db->addSelect('orden_preparada_usuario_id');
	$this->_db->addSelect('orden_cancelada');
	$this->_db->addSelect('orden_cancelada_fecha_hora');
	$this->_db->addSelect('orden_cancelada_usuario_id');
	$this->_db->addSelect('orden_finalizada');
	$this->_db->addSelect('orden_finalizada_fecha_hora');
	$this->_db->addSelect('orden_finalizada_usuario_id');  
	        
	$this->_db->addFrom('datos_ordenes_trabajo');
	
	$this->_db->addWhere('orden_id = \'' . $this->getOrdenTrabajoId() . '\'');

	$this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
	$resultado = $this->_db->ejecutar();

	//verificamos que el movimiento exista en la db
        if(count($resultado) == 0){
            $errorDetalle = 'La orden que intenta cargar no existe';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
        return $resultado[0];
    }
    public function cargarme($datos){
	if(is_numeric($datos)){	
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}
	$this->setOrdenTrabajoId($resultado['orden_id']);
	$this->setOrdenTrabajoFechaHora($resultado['orden_fecha_hora']);
	$this->setOrdenTrabajoNro($resultado['orden_nro']);
        $this->setOrdenTrabajoUsuarioId($resultado['orden_usuario_id']);
	$this->setOrdenTrabajoPreparado($resultado['orden_preparada']);
	$this->setOrdenTrabajoPreparadoFechaHora($resultado['orden_preparada_fecha']);
	$this->setOrdenTrabajoPreparadoUsuarioId($resultado['orden_preparada_usuario_id']);
	$this->setOrdenTrabajoCancelado($resultado['orden_cancelada']);
	$this->setOrdenTrabajoCanceladoFechaHora($resultado['orden_cancelada_fecha_hora']);
	$this->setOrdenTrabajoCanceladoUsuarioId($resultado['orden_cancelad_usuario_id']);
	$this->setProductoDestinoCantidad($resultado['producto_destino_cantidad']);
	$this->setLote($resultado['lote']);
	$this->setLoteVencimiento($resultado['lote_vencimiento']);
	$this->setOrdenFinalizada($resultado['orden_finalizada']);
        
        /*$this->setObjEstado(new EstadoExtendido());
	$this->getObjEstado()->cargarMe($resultado['orden_estado_id']);
	*/
	/*$this->setObjProductoDestino(new ProductoExtendido());
	$this->getObjProductoDestino()->cargarMe($resultado['producto_destino_id']);        
        */
    }

    public function salvarme($arrParametros){
        
        if($arrParametros['idOrdenTrabajo'] != null){
            $this->_db->addCamposTabla('orden_id');
            $this->_db->addCamposValue("'" . $arrParametros['idOrdenTrabajo'] . "'");
        }else{
            $errorDetalle = 'No se recibio el id del orden';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
        
        if($arrParametros['idOrdenTrabajo'] != null){
            $this->_db->addCamposTabla('orden_nro');
            $this->_db->addCamposValue("'" . $arrParametros['idOrdenTrabajo'] . "'");
        }else{
            $errorDetalle = 'No se recibio el orden_id';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }

	if($arrParametros['productosDestino'] != null){
            $this->_db->addCamposTabla('producto_destino_id');
            $this->_db->addCamposValue("'" . $arrParametros['productosDestino'] . "'");
        }else{
            $errorDetalle = 'No se recibio el orden_id';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
 
        if($arrParametros['estadoId'] != null){
            $this->_db->addCamposTabla('orden_estado_id');
            $this->_db->addCamposValue("'" . $arrParametros['estadoId'] . "'");
        }else{
            $errorDetalle = 'No se recibio el estado';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }

                
        if($arrParametros['observaciones'] != null){
            $this->_db->addCamposTabla('observaciones');
            $this->_db->addCamposValue("'" . $arrParametros['observaciones'] . "'");
        }

        if($_SESSION['usuarioId'] != null){
            $this->_db->addCamposTabla('orden_usuario_id');
            $this->_db->addCamposValue("'" . $_SESSION['usuarioId']  . "'");
        }else{
            $errorDetalle = 'No se recibio el usuario';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
        
        
        
        $this->_db->addFrom('datos_ordenes_trabajo');
        
        $this->_db->generarInsert();
        
        //echo $this->_db->getQry();exit;
        return $this->_db->getQry();
          
	
    }
    public function actualizarme($arrParametros) {
        
	if($arrParametros['campos']['producto_destino_id'] != 'false' && $arrParametros['campos']['producto_destino_id'] !=NULL){

	    $this->_db->addCamposUpdate('producto_destino_id = \'' . $arrParametros['campos']['producto_destino_id'] .'\'');
	}
        if($arrParametros['campos']['cantidad'] != NULL){

	    $this->_db->addCamposUpdate('producto_destino_cantidad = \'' . $arrParametros['campos']['cantidad'] .'\'');
	}
	if($arrParametros['campos']['lote'] != NULL){

	    $this->_db->addCamposUpdate('lote= \'' . $arrParametros['campos']['lote'] .'\'');
	}
	if($arrParametros['campos']['lote_vence'] != 'false' && $arrParametros['campos']['lote_vence'] != NULL){

	    $ano= substr($arrParametros['campos']['lote_vence'],6,4);
	    $mes = substr($arrParametros['campos']['lote_vence'],3,2);
	    $dia = substr($arrParametros['campos']['lote_vence'],0,2);
	    $fecha = $ano."/".$mes ."/".$dia;
	    $this->_db->addCamposUpdate('lote_vencimiento = \'' . $fecha .'\'');
	}
	if($arrParametros['campos']['estanterias'] != 'false' && $arrParametros['campos']['estanterias'] != NULL){

	    $this->_db->addCamposUpdate('estanteria_id = \'' . $arrParametros['campos']['estanterias'] .'\'');
	}

	$this->_db->addFrom('datos_ordenes_trabajo');
	
	$this->_db->addWhere('orden_id= \'' . $arrParametros['id'] .'\'');
	
        $this->_db->generarUpdate();   
	//asigamos el resulta que es un array a una variable
	//echo $this->_db->getQry();
	$resultado= $this->_db->ejecutar();

	if(count($resultado)==0){
	     $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al salvar los datos de la orden'
		    );
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'Orden Actualizada'
		    );
	}
	echo json_encode($arrDevolver);exit;
        
    }

    public function cancelarMe($id){
        if($this->getOrdenTrabajoCancelado()!= true){
        
            $this->_db->addCamposUpdate('orden_cancelada = true');
            $this->_db->addCamposUpdate('orden_cancelada_fecha_hora = now()');
            $this->_db->addCamposUpdate('orden_cancelada_usuario_id = ' . $_SESSION['usuarioId'] );
            $this->_db->addCamposUpdate('orden_estado_id  = 3');
            
            $this->_db->addWhere('orden_id = \'' . $id .'\'');
            
            $this->_db->addFrom('datos_ordenes_trabajo');
            $this->_db->generarUpdate();
            return $this->_db->getQry();
        }else{
            $arrDevolver = array(
                                "soyError" => false,
                                "nivel" => 0,
                                "alertados" => $arrAlertador,
                                "mensaje" => "La orden ya fue cancelada con anterioridad"
                                );
            echo json_encode($arrDevolver);exit;
        }
    }
}
