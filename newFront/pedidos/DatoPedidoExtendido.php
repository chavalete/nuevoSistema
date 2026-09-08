<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/pedidos/DatoPedido.php';
require_once 'DetallePedidoExtendido.php';
require_once 'DetallePedidoPorLoteExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/EstadoExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/FrentePacientes.php';

class DatoPedidoExtendido extends DatoPedido
{
    private $_db;
    
    private $_pedidoFacturaNro;
    private $_pedidoRemitoNro;
    private $_renovacionId;


    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
        
    }  
    
    public function setPedidoFacturaNro($nro) {
        $this->_pedidoFacturaNro = $nro;
    }


    public function getPedidoFacturaNro() {
        return $this->_pedidoFacturaNro;
    }
    
    public function setPedidoRemitoNro($nro) {
        $this->_pedidoRemitoNro = $nro;
    }


    public function getPedidoRemitoNro() {
        return $this->_pedidoRemitoNro;
    }
    
    public function setRenovacionId($id) {
        $this->_renovacionId = $id;
    }


    public function getRenovacionId() {
        return $this->_renovacionId;
    }

    public function getDataDB($id){
	
	$this->setPedidoId($id);

	$this->_db->addSelect('pedido_id');
        $this->_db->addSelect('pedido_fecha_hora');
        $this->_db->addSelect('cliente_id');
	$this->_db->addSelect('paciente_id');
        $this->_db->addSelect('pedido_nro');
        $this->_db->addSelect('pedido_nro_ext');
        $this->_db->addSelect('estado_id');
        $this->_db->addSelect('pedido_calle');
        $this->_db->addSelect('pedido_altura');
        $this->_db->addSelect('pedido_piso');
        $this->_db->addSelect('pedido_depto');
        $this->_db->addSelect('pedido_tel');
        $this->_db->addSelect('codigo_postal');
	$this->_db->addSelect('localidad_id');
	$this->_db->addSelect('paciente_id');
	$this->_db->addSelect('pedido_obs');
	$this->_db->addSelect('pedido_usuario_id');
	$this->_db->addSelect('pedido_preparado');
	$this->_db->addSelect('pedido_preparado_fecha');
	$this->_db->addSelect('pedido_preparado_usuario_id');
	$this->_db->addSelect('pedido_cancelado');
	$this->_db->addSelect('pedido_cancelado_fecha_hora');
	$this->_db->addSelect('pedido_cancelado_usuario_id');  
	$this->_db->addSelect('pedido_factura_nro');
	$this->_db->addSelect('pedido_remito_nro');  
        $this->_db->addSelect('renovacion_id');  
	        
	$this->_db->addFrom('datos_pedidos');
	
	$this->_db->addWhere('pedido_id = \'' . $this->getPedidoId() . '\'');

	$this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
	$resultado = $this->_db->ejecutar();

	//verificamos que el movimiento exista en la db
        if(count($resultado) == 0){
            $errorDetalle = 'El pedido que intenta cargar no existe';
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
	$this->setPedidoId($resultado['pedido_id']);
	$this->setPedidoFechaHora($resultado['pedido_fecha_hora']);
	$this->setPedidoNro($resultado['pedido_nro']);
        $this->setPedidoNroExt($resultado['pedido_nro_ext']);
        $this->setPedidoCalle($resultado['pedido_calle']);
        $this->setPedidoAltura($resultado['pedido_altura']);
        $this->setPedidoPiso($resultado['pedido_piso']);
        $this->setPedidoDepto($resultado['pedido_depto']);
        $this->setPedidoTelefono($resultado['pedido_tel']);
        $this->setCodigoPostal($resultado['codigo_postal']);
        $this->setPedidoObs($resultado['pedido_obs']);
        $this->setPedidoUsuarioId($resultado['pedido_usuario_id']);
	$this->setPedidoPreparado($resultado['pedido_preparado']);
	$this->setPedidoPreparadoFechaHora($resultado['pedido_preparado_fecha']);
	$this->setPedidoPreparadoUsuarioId($resultado['pedido_preparado_usuario_id']);
	$this->setPedidoCancelado($resultado['pedido_cancelado']);
	$this->setPedidoCanceladoFechaHora($resultado['pedido_cancelado_fecha_hora']);
	$this->setPedidoCanceladoUsuarioId($resultado['pedido_cancelado_usuario_id']);
        $this->setPedidoFacturaNro($resultado['pedido_factura_nro']);
	$this->setPedidoRemitoNro($resultado['pedido_remito_nro']);
        $this->setRenovacionId($resultado['renovacion_id']);
        
        $this->setObjEstado(new EstadoExtendido());
	$this->getObjEstado()->cargarMe($resultado['estado_id']);        
        $this->setObjLocalidad(new FrenteLocalidades());
        if($resultado['localidad_id']!=NULL){
            $this->getObjLocalidad()->cargarLocalidad($resultado['localidad_id']);
        }
        
	$objPaciente = new FrentePacientes();
        if($resultado['paciente_id']!=NULL){
            $objPaciente->cargarPaciente($resultado['paciente_id']);
            $colObjPaciente = $objPaciente->getColObjPaciente();
            $this->setObjPaciente($colObjPaciente[$resultado['paciente_id']]);
	    $this->getObjPaciente()->cargarMe($colObjPaciente[$resultado['paciente_id']]->getPacienteId());
        }	        
    }

    public function salvarme($arrParametros){
        
        if($arrParametros['idPedido'] != null){
            $this->_db->addCamposTabla('pedido_id');
            $this->_db->addCamposValue("'" . $arrParametros['idPedido'] . "'");
        }else{
            $errorDetalle = 'No se recibio el id del pedido';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
        
        if($arrParametros['remitos'] == null && $arrParametros['facturas'] == null ){
            $errorDetalle = 'Al menos debe completar un campo - REMITO NRO / FACTURA NRO';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }        
        
        if($arrParametros['remitos'] != null ){
                $this->_db->addCamposTabla('pedido_remito_nro');
                $this->_db->addCamposValue("'" . $arrParametros['remitos'] . "'");    
	}
	if($arrParametros['facturas'] != null ){
                $this->_db->addCamposTabla('pedido_factura_nro');
                $this->_db->addCamposValue("'" . $arrParametros['facturas'] . "'");    
	}
        if($arrParametros['pedido_fecha_hora'] != null){
            $this->_db->addCamposTabla('pedido_fecha_hora');
            $this->_db->addCamposValue("'" . $arrParametros['pedido_fecha_hora'] . "'");    
        }        
        
        if($arrParametros['clientes'] != null){
            $this->_db->addCamposTabla('cliente_id');
            $this->_db->addCamposValue("'" . $arrParametros['clientes'] . "'");    
        }else{
               $errorDetalle = 'No se recibio el cliente';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
        
        if($arrParametros['idPedido'] != null){
            $this->_db->addCamposTabla('pedido_nro');
            $this->_db->addCamposValue("'" . $arrParametros['idPedido'] . "'");
        }else{
            $errorDetalle = 'No se recibio el pedido_id';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
        
        if($arrParametros['pedido_nro_ext'] != null){
            $this->_db->addCamposTabla('pedido_nro_ext');
            $this->_db->addCamposValue("'" . $arrParametros['pedido_nro_ext'] . "'");
        }
 
        if($arrParametros['estadoId'] != null){
            $this->_db->addCamposTabla('estado_id');
            $this->_db->addCamposValue("'" . $arrParametros['estadoId'] . "'");
        }else{
            $errorDetalle = 'No se recibio el estado';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
        
        if($arrParametros['calles'] != null){
            $this->_db->addCamposTabla('pedido_calle');
            $this->_db->addCamposValue("'" . $arrParametros['calles'] . "'");
        }
        
        if($arrParametros['alturas'] != null){
            $this->_db->addCamposTabla('pedido_altura');
            $this->_db->addCamposValue("'" . $arrParametros['alturas'] . "'");
        }
        
        if($arrParametros['piso'] != null){
            $this->_db->addCamposTabla('pedido_piso');
            $this->_db->addCamposValue("'" . $arrParametros['piso'] . "'");
        }
        
        if($arrParametros['depto'] != null){
            $this->_db->addCamposTabla('pedido_depto');
            $this->_db->addCamposValue("'" . $arrParametros['depto'] . "'");
        }
        
        if($arrParametros['telefono'] != null){
            $this->_db->addCamposTabla('pedido_tel');
            $this->_db->addCamposValue("'" . $arrParametros['telefono'] . "'");
        }
        
        if($arrParametros['codigoPostal'] != null){
            $this->_db->addCamposTabla('codigo_postal');
            $this->_db->addCamposValue("'" . $arrParametros['codigoPostal'] . "'");
        }

        if($arrParametros['localidades'] != null){
            $this->_db->addCamposTabla('localidad_id');
            $this->_db->addCamposValue("'" . $arrParametros['localidades'] . "'");
        }
        
        
        if($arrParametros['pacientes'] != null){
            $this->_db->addCamposTabla('paciente_id');
            $this->_db->addCamposValue("'" . $arrParametros['pacientes'] . "'");
        }
        
        if($arrParametros['renovacionId'] != null){
            $this->_db->addCamposTabla('renovacion_id');
            $this->_db->addCamposValue("'" . $arrParametros['renovacionId'] . "'");
        }
                
        if($arrParametros['observaciones'] != null){
            $this->_db->addCamposTabla('pedido_obs');
            $this->_db->addCamposValue("'" . $arrParametros['observaciones'] . "'");
        }

        if($_SESSION['usuarioId'] != null){
            $this->_db->addCamposTabla('pedido_usuario_id');
            $this->_db->addCamposValue("'" . $_SESSION['usuarioId']  . "'");
        }else{
            $errorDetalle = 'No se recibio el usuario';
            $errorArchivo = __FILE__;
            $this->setArrError($arrError);
        }
        
        
        
        $this->_db->addFrom('datos_pedidos');
        
        $this->_db->generarInsert();
        
        //echo $this->_db->getQry();exit;
        return $this->_db->getQry();
          
	
    }
    public function actualizarMe($arrParametros){
    
	    $this->_db->addCamposUpdate('estado_id = ' . $arrParametros[hojaEstadoId] );
            $this->_db->addWhere('pedido_id = \'' . $arrParametros[pedidoId] .'\'');
            
            $this->_db->addFrom('datos_pedidos');
            $this->_db->generarUpdate();
            return $this->_db->getQry();
    
    }

    public function cancelarMe(){
        if($this->getPedidoCancelado()!= true){
        
            $this->_db->addCamposUpdate('pedido_cancelado = true');
            $this->_db->addCamposUpdate('pedido_cancelado_fecha_hora = now()');
            $this->_db->addCamposUpdate('pedido_cancelado_usuario_id = ' . $_SESSION['usuarioId'] );
            $this->_db->addCamposUpdate('estado_id  = 3');
            
            $this->_db->addWhere('pedido_id = \'' . $this->getPedidoId() .'\'');
            
            $this->_db->addFrom('datos_pedidos');
            $this->_db->generarUpdate();
            return $this->_db->getQry();
        }else{
            $arrDevolver = array(
                                "soyError" => false,
                                "nivel" => 0,
                                "alertados" => $arrAlertador,
                                "mensaje" => "El pedido ya fue cancelado"
                                );
            echo json_encode($arrDevolver);exit;
        }
    }
}
