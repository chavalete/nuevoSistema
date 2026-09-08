<?php
//Almacenamiento
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

//Padre
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/trazabilidad/DatoMovimiento.php';

//Movimiento
require_once 'TipoMovimientoTrazaExtendido.php';

//Actores extendidos
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/ProveedorExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/ClienteExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/PacienteExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/ObraSocialExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/MedicoExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/VendedorExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/DistribuidorExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/CustodianteExtendido.php';

//Mail
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mail/MailConfirmacionPedido.php';


/**
 * Description of datoMovimientoALtaExtendido
 *
 * @author dMELMAC
 */

class DatoMovimientoExtendido extends DatoMovimiento 
{
    /**
	 * 
	 * @access private
	 */
    private $_db;
    
    /**
     * 
     * @access private
     */
    private $_despachoNro;

	/**
	 * 
	 * @access private
	 */

    private $_nroTramiteAnmat;
    private $_nroOrdenCompra;
    private $_nroNotaCarga;
    private $_cantDiasAlquiler;
    private $_movimientoFinalizado;
    private $_movimientoFinalizadoUsuarioId;
    private $_movimientoFinalizadoFechaHora;
    private $_domicilioEntrega;
    private $_fechaVencimientoAlquiler;
    private $_fechaInicioAlquiler;
    private $_descuento;
    private $_idEstado;
    private $_importeMovimiento;
    private $_pedidoId;
    
    private $_custodianteId;
    private $_clienteId;
    private $_factura_id;
    private $_tipoComprobante;
    private $_impreso;
    private $_tipoEntrega;
    private $_usuarioNombre;
    private $_idLista;
    private $_formaPago;
    
    public function setFormaPago($forma){
        $this->_formaPago = $forma;
    }
    public function getFormaPago(){
        return $this->_formaPago;
    }
    public function setListaId($id){
        $this->_idLista = $id;
    }
    public function getListaId(){
        return $this->_idLista;
    }
    public function setTipoComprobante($tipoComprobante){
        $this->_tipoComprobante = $tipoComprobante;
    }
    public function getTipoComprobante(){
        return $this->_tipoComprobante;
    }
    public function setTipoEntrega($tipoEntrega){
        $this->_tipoEntrega = $tipoEntrega;
    }
    public function getTipoEntrega(){
        return $this->_tipoEntrega;
    }

    public function setImporteMovimiento($importe){
        $this->_importeMovimiento = $importe;
    }
    public function getImporteMovimiento(){
        return $this->_importeMovimiento;
    }
    
    public function setIdEstado($idEstado){
        $this->_idEstado = $idEstado;
    }
    public function getIdEstado(){
        return $this->_idEstado;
    }
    public function setRelacionNombre($relacionNombre){
        $this->_relacionNombre = $relacionNombre;
    }
    public function getRelacionNombre(){
        return $this->_relacionNombre;
    }
    public function setRelacionId($relacionId){
        $this->_relacionId = $relacionId;
    }
    public function getRelacionId(){
        return $this->_relacionId;
    }
    public function setClienteId($clienteId){
        $this->_clienteId = $clienteId;
    }
    public function getClienteId(){
        return $this->_clienteId;
    }
    public function setDescuento($descuento){
	$this->_descuento = $descuento;
    }
    public function getDescuento(){
	return $this->_descuento;
    }    
    
    public function setFechaVencimientoAlquiler($fechaVencemiento){
	$this->_fechaVencimientoAlquiler = $fechaVencemiento;
    }
    public function getFechaVencimientoAlquiler(){
	return $this->_fechaVencimientoAlquiler;
    }
    public function setFechaInicioAlquiler($fechaInicioAlquiler){
	$this->_fechaInicioAlquiler = $fechaInicioAlquiler;
    }
    public function getFechaInicioAlquiler(){
	return $this->_fechaInicioAlquiler;
    }
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
    }

    public function setDespachoNro($despachoNro){
        $this->_despachoNro = $despachoNro;
    }
    
    public function getDespachoNro(){
        return $this->_despachoNro;
    }

    public function setNroTramiteAnmat($nro){
        $this->_nroTramiteAnmat= $nro;
    }
    
    public function getNroTramiteAnmat(){
        return $this->_nroTramiteAnmat;
    }
    public function setNroOrdenCompra($ordenCompra){
	$this->_nroOrdenCompra = $ordenCompra;
    }
    public function getNroOrdenCompra(){
	return $this->_nroOrdenCompra;
    }
    public function setNroNotaCarga($nroNotaCarga){
	$this->_nroNotaCarga = $nroNotaCarga;
    }
    public function getNroNotaCarga(){
	return $this->_nroNotaCarga;
    }
    public function setCantDiasAlquiler($cantDias){
	$this->_cantDiasAlquiler= $cantDias;
    }
    public function getCantDiasAlquiler(){
	return $this->_cantDiasAlquiler;
    }
    public function setMovimientoFinalizado($movimientoFinalizado){

	$this->_movimientoFinalizado = $movimientoFinalizado;
    }
    public function getMovimientoFinalizado(){
	return $this->_movimientoFinalizado;
    }
    public function setMovimientoFinalizadoUsuarioId($movimientoFinalizadoUsuarioId){
	$this->_movimientoFinalizadoUsuarioId = $movimientoFinalizadoUsuarioId;
    }
    public function getMovimientoFinalizadoUsuarioId(){
	return $this->_movimientoFinalizadoUsuarioId;
    }
    public function setMovimientoFinalizadoFechaHora($movimientoFinalizadoFechaHora){
	$this->_movimientoFinalizadoFechaHora = $movimientoFinalizadoFechaHora;
    }
    public function getMovimientoFinalizadoFechaHora(){
	return $this->_movimientoFinalizadoFechaHora;
    }
    public function setDomicilioEntrega($domicilioEntrega){
	$this->_domicilioEntrega = $domicilioEntrega;
    }
    public function getDomicilioEntrega(){
	return $this->_domicilioEntrega;
    }
	
    public function setPedidoId($id){
        $this->_pedidoId= $id;
    }
    public function getPedidoId(){
        return $this->_pedidoId;
    }
    
    public function setCustodianteId($id){
        $this->_custodianteId= $id;
    }
    public function getCustoduanteId(){
        return $this->_custodianteId;
    }
    public function setMovimientoImpreso($impreso){
        $this->_impreso = $impreso;
    }
    public function getMovimientoImpreso(){
        return $this->_impreso;
    }
    public function setUsuarioNombre($usuarioNombre){
        $this->_usuarioNombre = $usuarioNombre;
    }
    public function getUsuarioNombre(){
        return $this->_usuarioNombre;
    }

    
    public function actualizarme($arrParametros) {
        
	if($arrParametros['campos']['cantDiasAlquiler'] !=NULL){

	    $this->_db->addCamposUpdate('cant_dias_alquiler= \'' . $arrParametros['campos']['cantDiasAlquiler'] .'\'');
	}
        
        if($arrParametros['campos']['cliente_id'] != 'false' && $arrParametros['campos']['cliente_id'] != NULL){

	    $this->_db->addCamposUpdate('cliente_id= \'' . $arrParametros['campos']['cliente_id'] .'\'');
	}
	
	if($arrParametros['campos']['medicos'] != 'false' && $arrParametros['campos']['medicos'] != NULL){

	    $this->_db->addCamposUpdate('medico_id= \'' . $arrParametros['campos']['medicos'] .'\'');
	}
	if($arrParametros['campos']['pacientes'] != 'false' && $arrParametros['campos']['pacientes'] != NULL){

	    $this->_db->addCamposUpdate('paciente_id= \'' . $arrParametros['campos']['pacientes'] .'\'');
	}
	if($arrParametros['campos']['vendedores'] != 'false' && $arrParametros['campos']['vendedores'] != NULL){

	    $this->_db->addCamposUpdate('vendedor_id= \'' . $arrParametros['campos']['vendedores'] .'\'');
	}
	if($arrParametros['campos']['distribuidores'] != 'false' && $arrParametros['campos']['distribuidores'] != NULL){

	    $this->_db->addCamposUpdate('distribuidor_id= \'' . $arrParametros['campos']['distribuidores'] .'\'');
	}
        if($arrParametros['campos']['custodiantes'] != 'false' && $arrParametros['campos']['custodiantes'] != NULL){

	    $this->_db->addCamposUpdate('custodiante_id= \'' . $arrParametros['campos']['custodiantes'] .'\'');
	}
	
	if($arrParametros['campos']['nro_remito'] != 'false' && $arrParametros['campos']['nro_remito'] != NULL){

	    $this->_db->addCamposUpdate('remito_nro= \'' . $arrParametros['campos']['nro_remito'] .'\'');
	}
	if($arrParametros['campos']['domicilio_entrega'] != 'false' && $arrParametros['campos']['domicilio_entrega'] != NULL){

	    $this->_db->addCamposUpdate('domicilio_entrega= \'' . $arrParametros['campos']['domicilio_entrega'] .'\'');
	}
	if($arrParametros['campos']['obs'] != 'false' && $arrParametros['campos']['obs'] != NULL){

	    $this->_db->addCamposUpdate('obs= \'' . $arrParametros['campos']['obs'] .'\'');
	}
	if($arrParametros['campos']['fecha_inicio_alquiler'] != 'false' && $arrParametros['campos']['fecha_inicio_alquiler'] != NULL){

	    $ano= substr($arrParametros['campos']['fecha_inicio_alquiler'],6,4);
	    $mes = substr($arrParametros['campos']['fecha_inicio_alquiler'],3,2);
	    $dia = substr($arrParametros['campos']['fecha_inicio_alquiler'],0,2);
	    $fecha = $ano."/".$mes ."/".$dia;
	    $this->_db->addCamposUpdate('fecha_inicio_alquiler = \'' . $fecha .'\'');
	}
	
        if($arrParametros['campos']['nro_nota_carga'] != 'false' && $arrParametros['campos']['nro_nota_carga'] != NULL){
            $this->_db->addCamposUpdate('nro_nota_carga= \'' . $arrParametros['campos']['nro_nota_carga'] .'\'');
	}

	$this->_db->addFrom('datos_movimientos');
	
	$this->_db->addWhere('id_movimiento= \'' . $arrParametros['id'] .'\'');
	
        $this->_db->generarUpdate();   
	//asigamos el resulta que es un array a una variable
	//echo $this->_db->getQry();
	$resultado= $this->_db->ejecutar();

	if(count($resultado)==0){
	     $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al salvar los datos de cabecera'
		    );
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'Datos de cabecera actualizados'
		    );
	}
	echo json_encode($arrDevolver);exit;
        
    }
    
    public function salvarme($arrParametros) {
        //var_dump($arrParametros);exit;
        //generamos el id de movimiento alta
        $this->setIdMovimiento($arrParametros['id_movimiento']);
        $this->setMovimientoUsuarioId($arrParametros['movimiento_usuario_id']);
        
        //control campos mininos de cabecera
        if($arrParametros['id_movimiento'] == NULL){
            $arrDevolver = array(
	      "soyError" => true,
	      "nivel" => 1,
	      "mensaje" =>"El id_movimiento no puede ser nulo"
	      );
	    echo json_encode($arrDevolver);exit;
        }
        
        if($arrParametros['tipo_movimiento_id'] == NULL){
                $arrDevolver = array(
		  "soyError" => true,
		  "nivel" => 1,
		  "mensaje" =>"El tipo de movimiento no puede ser nulo"
	      );
	    echo json_encode($arrDevolver);exit;
              
        }
        
        if($arrParametros['movimiento_usuario_id'] == NULL){
                $arrDevolver = array(
		  "soyError" => true,
		  "nivel" => 1,
		  "mensaje" =>"Todo movimiento requiere usuario"
	      );
	    echo json_encode($arrDevolver);exit;
        }
         
        $this->setObjTipoMovimiento(new TipoMovimientoTrazaExtendido());
        $this->getObjTipoMovimiento()->cargarMe($arrParametros['tipo_movimiento_id']);
 
        if(count($this->getObjTipoMovimiento()->getArrError()) > 0) {
                $this->setArrError($this->getObjTipoMovimiento()->getArrError());
                return;
        }

        //exit;
        //aca arranca la magia de la creacion 
        if($this->getObjTipoMovimiento()->getTipoMovimientoALta() == TRUE && $this->getObjTipoMovimiento()->getTipoMovimientoId() !=3){
            //parametros minimos de una ALTA 
            // es una devolucion? solo las altas por devolucion traen de_id_movimiento
            if($arrParametros['factura_nro'] == NULL && $arrParametros['remito_nro'] == NULL && $this->getObjTipoMovimiento()->getTipoMovimientoId()!= 14 && 
		$this->getObjTipoMovimiento()->getTipoMovimientoId()!= 12 ){
                $arrDevolver = array(
		  "soyError" => true,
		  "nivel" => 1,
		  "mensaje" =>"Debe de ingresar remito o factura"
	      );
	      echo json_encode($arrDevolver);exit;
            }else{
                //armo los campos y los valores 
                if($arrParametros['factura_nro'] != NULL ){
                    $this->setFacturaNro($arrParametros['factura_nro']);
                    $this->_db->addCamposTabla('factura_nro');
                    $this->_db->addCamposValue("'" . $this->getFacturaNro() . "'");
                }
                //armo los campos y los valores
                if($arrParametros['remito_nro'] != NULL){
                    $this->setRemitoNro($arrParametros['remito_nro']);
                    $this->_db->addCamposTabla('remito_nro');
                    $this->_db->addCamposValue("'" . $this->getRemitoNro() . "'");
                }
                /*
		if($this->getObjTipoMovimiento()->getTipoMovimientoId()==3){
		    $arrParametros['cliente_id'] = 3;
		    $this->_db->addCamposTabla('cliente_id');
		    $this->_db->addCamposValue($arrParametros['cliente_id']);
		}
                 * 
                 */
            }   
            if($arrParametros['control'] != 1 ){ //si el flag esta en 1 no controla campos necesarios (ej: traspasos de stock no tienen prove_id, despano_nro, etc)
                
		if(!is_numeric($arrParametros['prove_id'])){
                    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"Ingrese un proveedor"
		    );
		  echo json_encode($arrDevolver);exit;
                }
                 //armo los campos y los valores no obligatorios
                if($arrParametros['despacho_nro'] != NULL){
                    $this->setDespachoNro($arrParametros['despacho_nro']);
                    $this->_db->addCamposTabla('despacho_nro');
                    $this->_db->addCamposValue("'" . $this->getDespachoNro() ."'");
                }
                if($arrParametros['tipo_comprobante'] != NULL){
                    $this->setTipoComprobante($arrParametros['tipo_comprobante']);
                    $this->_db->addCamposTabla('tipo_comprobante');
                    $this->_db->addCamposValue("'" . $this->getTipoComprobante() ."'");
                }
                //armo los campos y los valores no obligatorios
                if($arrParametros['nro_tramite_anmat'] != NULL){
                    $this->setNroTramiteAnmat($arrParametros['nro_tramite_anmat']);
                    $this->_db->addCamposTabla('nro_tramite_anmat');
                    $this->_db->addCamposValue("'" . $this->getNroTramiteAnmat()  . "'");
                }
                if($arrParametros['obra_social_id'] != NULL){
                    $this->_db->addCamposTabla('obra_social_id');
                    $this->_db->addCamposValue($arrParametros['obra_social_id']);
                }
                //armo los campos y los valores
                $this->_db->addCamposTabla('prove_id');
                $this->_db->addCamposValue($arrParametros['prove_id']);
                
                if($arrParametros['cliente_id']!=NULL){
                    $this->_db->addCamposTabla('cliente_id');
                    $this->_db->addCamposValue($arrParametros['cliente_id']);
                }
                if($arrParametros['importeEntrada']>0){
                    $this->_db->addCamposTabla('movimiento_total_fact');
                    $this->_db->addCamposValue($arrParametros['importeEntrada']);
                }

            }
        }else{
            //BAJA
            //parametros minimos de una baja
            //armo los campos y los valores 
            if($arrParametros['factura_nro'] != NULL ){
                $this->setFacturaNro($arrParametros['factura_nro']);
                $this->_db->addCamposTabla('factura_nro');
                $this->_db->addCamposValue("'". $this->getFacturaNro() . "'");
            }
            //armo los campos y los valores 
            if($arrParametros['remito_nro'] != NULL){
                $this->setRemitoNro($arrParametros['remito_nro']);
                $this->_db->addCamposTabla('remito_nro');
                $this->_db->addCamposValue("'" . $this->getRemitoNro() . "'");
            }

            if($arrParametros['medico_id'] !=NULL){
                $this->_db->addCamposTabla('medico_id');
                        $this->_db->addCamposValue($arrParametros['medico_id']);

            }
            if($arrParametros['paciente_id'] !=NULL){
                $this->_db->addCamposTabla('paciente_id');
                        $this->_db->addCamposValue($arrParametros['paciente_id']);

            }
            if($arrParametros['obra_social_id'] != NULL){
                        $this->_db->addCamposTabla('obra_social_id');
                        $this->_db->addCamposValue($arrParametros['obra_social_id']);
            }
            if($arrParametros['vendedor_id'] != NULL){
                        $this->_db->addCamposTabla('vendedor_id');
                        $this->_db->addCamposValue($arrParametros['vendedor_id']);
            }
            if($arrParametros['distribuidor_id'] != NULL){
                        $this->_db->addCamposTabla('distribuidor_id');
                        $this->_db->addCamposValue($arrParametros['distribuidor_id']);
            }                
            if($arrParametros['nro_orden_compra'] != NULL){
                $this->setNroOrdenCompra($arrParametros['nro_orden_compra']);
                $this->_db->addCamposTabla('orden_compra');
                $this->_db->addCamposValue("'" . $this->getNroOrdenCompra()  . "'");
            }
            if($arrParametros['nro_nota_carga'] != NULL){
                $this->setNroNotaCarga($arrParametros['nro_nota_carga']);
                $this->_db->addCamposTabla('nro_nota_carga');
                $this->_db->addCamposValue("'" . $this->getNroNotaCarga()  . "'");
            }
            if($arrParametros['pedido_id'] != NULL){
                $this->setPedidoId($arrParametros['pedido_id']);
                $this->_db->addCamposTabla('pedido_id');
                $this->_db->addCamposValue("'" . $this->getPedidoId()  . "'");
            }
            if($arrParametros['pedido_id'] != NULL){
                $this->setPedidoId($arrParametros['pedido_id']);
                $this->_db->addCamposTabla('pedido_id');
                $this->_db->addCamposValue("'" . $this->getPedidoId()  . "'");
            }
            
            if($arrParametros['control'] != 1 ){
                if($arrParametros['cliente_id'] == NULL){
                        $arrError['detalle'] = 'El cliente_id no puede ser nulooo';
                        $arrError['archivo'] = __FILE__;
                        $this->setArrError($arrError);
                        return;
                }
                
            }
            if($arrParametros['cliente_id']!=NULL){
                $this->_db->addCamposTabla('cliente_id');
                $this->_db->addCamposValue($arrParametros['cliente_id']);
            }
            if($arrParametros['relacion_id']!=NULL){
                $this->_db->addCamposTabla('relacion_id');
                $this->_db->addCamposValue($arrParametros['relacion_id']);
            }
            if($arrParametros['guia_id']!=NULL){
                $this->_db->addCamposTabla('guia_id');
                $this->_db->addCamposValue($arrParametros['guia_id']);
            }
            
            
        }
        if($this->getObjTipoMovimiento()->getTipoMovimientoId() ==3){
                $this->_db->addCamposTabla('prove_id');
                $this->_db->addCamposValue($arrParametros['prove_id']);
                $this->_db->addCamposTabla('movimiento_total_fact');
                $this->_db->addCamposValue($arrParametros['importeEntrada']);
        }

        if($arrParametros['descuento'] != NULL){
            $this->setDescuento($arrParametros['descuento']);
            $this->_db->addCamposTabla('descuento');
            $this->_db->addCamposValue("'" . $this->getDescuento()  . "'");
        }
        if($arrParametros['idEstado'] != NULL){
            $this->setIdEstado($arrParametros['idEstado']);
            $this->_db->addCamposTabla('id_estado');
            $this->_db->addCamposValue("'" . $this->getIdEstado()  . "'");
        }
        if($arrParametros['observaciones'] != NULL){
            $this->setObs($arrParametros['observaciones']);
            $this->_db->addCamposTabla('obs');
            $this->_db->addCamposValue("'" . $this->getObs()  . "'");
        }
        
        if($arrParametros['domicilio_entrega'] != NULL){
            $this->setDomicilioEntrega($arrParametros['domicilio_entrega']);
            $this->_db->addCamposTabla('domicilio_entrega');
            $this->_db->addCamposValue("'" . $this->getDomicilioEntrega()  . "'");
        }
        //FIN de control y armado de los datos de cabecera para un movimiento de alta o de baja.
        //Si paso paso todo bien , vamos a armar y dentro del objeto generar el detalle del movimiento alta / baja  si corresponde
        
        // Si paso todo bien genero efectivamente el dato_movimiento
        if($arrParametros['de_id_movimiento'] > 0){
            $this->_db->addCamposTabla('de_id_movimiento');
            $this->_db->addCamposValue($arrParametros['de_id_movimiento']);
        }
        
        if($arrParametros['a_id_movimiento'] > 0){
            $this->_db->addCamposTabla('a_id_movimiento');
            $this->_db->addCamposValue($arrParametros['a_id_movimiento']);
        }
        
        if($arrParametros['fecha_inicio_alquiler'] !=  NULL ){
            $this->_db->addCamposTabla('fecha_inicio_alquiler');
            $this->_db->addCamposValue("'" . $arrParametros['fecha_inicio_alquiler'] . "'");
        }
        if($arrParametros['tipoEntrega'] !=  NULL ){
            $this->_db->addCamposTabla('tipo_entrega');
            $this->_db->addCamposValue("'" . $arrParametros['tipoEntrega'] . "'");
        }
        if($arrParametros['idLista'] !=  NULL ){
            $this->_db->addCamposTabla('lista_id');
            $this->_db->addCamposValue("'" . $arrParametros['idLista'] . "'");
        }
        if($arrParametros['de_sucursal_id']>0){
            $this->_db->addCamposTabla('de_sucursal_id');
            $this->_db->addCamposValue("'" . $arrParametros['de_sucursal_id'] . "'");
        }
        if($arrParametros['a_sucursal_id']>0){
            $this->_db->addCamposTabla('a_sucursal_id');
            $this->_db->addCamposValue("'" . $arrParametros['a_sucursal_id'] . "'");
        }
        if($arrParametros['traspasar'] ==true){
            $this->_db->addCamposTabla('traspasar_movimiento');
            $this->_db->addCamposValue("'". $arrParametros['traspasar']. "'");
        }
        $this->_db->addCamposTabla('id_movimiento');
        $this->_db->addCamposTabla('tipo_movimiento_id');
        $this->_db->addCamposTabla('movimiento_usuario_id');
        $this->_db->addCamposTabla('sucursal_id');
        $this->_db->addCamposValue($this->getIdMovimiento());
        $this->_db->addCamposValue($this->getObjTipoMovimiento()->getTipoMovimientoId());
        $this->_db->addCamposValue($arrParametros['movimiento_usuario_id']);
        $this->_db->addCamposValue($_SESSION['sucursalId']);

        $this->_db->addFrom('datos_movimientos');
        
        $this->_db->generarInsert();
        
        //echo $this->_db->getQry();exit;
        return $this->_db->getQry();
    }
    
    public function cargarme($id){

        $this->_db->addSelect('id_movimiento, 
            to_char(movimiento_fecha_hora, \'DD-MM-YYYY HH24:MI\') AS movimiento_fecha_hora, 
            tipo_movimiento_id, 
            cliente_id, 
            prove_id, 
            medico_id, 
            paciente_id,
            orden_compra,
            nro_nota_carga');
        $this->_db->addSelect('obra_social_id, 
            despacho_nro,
            nro_tramite_anmat,
            factura_nro, 
            remito_nro, 
            de_id_movimiento,
            a_id_movimiento,
            movimiento_usuario_id, 
            detalle_id,
            cant_dias_alquiler,
            vendedor_id,
            distribuidor_id,
            obs,
            movimiento_total_fact,
 	    datos_movimientos.descuento,
 	    detalle_movimientos.descuento,
            pedido_id,
            tipo_comprobante,
	    movimiento_finalizado,
	    movimiento_finalizado_usuario_id,
	    movimiento_finalizado_fecha_hora,
	    domicilio_entrega,
        recepcion_liberada_usuario_id,
            to_char(recepcion_liberada_fecha_hora,\'DD-MM-YYYY\') AS recepcion_liberada_fecha_hora,

            CASE WHEN recepcion_liberada THEN \'Liberado\' ELSE \'Bloqueado\' END AS recepcion_mensaje,
	    to_char(fecha_vencimiento_alquiler,\'DD-MM-YYYY\') AS fecha_vencimiento_alquiler ,
            to_char(fecha_inicio_alquiler,\'DD-MM-YYYY\') AS fecha_inicio_alquiler,
            custodiante_id, relacion_id,id_estado, impreso,lista_id');
        $this->_db->addFrom('datos_movimientos');
        $this->_db->addFrom(' INNER JOIN detalle_movimientos USING (id_movimiento)');
        $this->_db->addWhere(' id_movimiento = ' . $id );
        $this->_db->addOrderBy('producto_id ASC');
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        //var_dump($resultado);exit;
        
        //verificamos que el movimiento exista en la db
        if(count($resultado) == 0){
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"El Movimiento/Renovacion que intenta cargar no existe"
		    );
		  echo json_encode($arrDevolver);exit;
        }
        
        //set de los parametros de cabecera
        $this->setIdMovimiento($resultado[0]['id_movimiento']);
        $this->setMovimientoFechaHora($resultado[0]['movimiento_fecha_hora']);
        
        $prove = new ProveedorExtendido();
        if($resultado[0]['prove_id'] > 0){
            $prove->cargarMe($resultado[0]['prove_id']);

        }
        $this->setObjProve($prove);
        
        $this->setClienteId($resultado[0]['cliente_id']);
        $cliente = new ClienteExtendido();
        if($resultado[0]['cliente_id'] != null ){
            $cliente->cargarMe($resultado[0]['cliente_id']);
        }
        $this->setObjCliente($cliente);
        
        $this->setIdEstado($resultado[0]['id_estado']);
        $estado = new EstadoExtendido();
        if($resultado[0]['id_estado'] != null ){
            $estado->cargarMe($resultado[0]['id_estado']);
        }
        
        $this->setObjEstado($estado);
        
        
        $medico = new MedicoExtendido();
        if($resultado[0]['medico_id'] > 0){
            $medico->cargarMe($resultado[0]['medico_id']);
        }
        $this->setObjMedico($medico);
        
        $paciente = new PacienteExtendido();
        if($resultado[0]['paciente_id'] > 0){
            $paciente->cargarMe($resultado[0]['paciente_id']);
        }
        $this->setObjPaciente($paciente);
        
        $obraSocial = new ObraSocialExtendido();
        if($resultado[0]['obra_social_id'] > 0){
            $obraSocial->cargarMe($resultado[0]['obra_social_id']);
        }
        $this->setObjObraSocial($obraSocial);
        $this->setObjMedico($medico);
        
        $custodia = new CustodianteExtendido();
        if($resultado[0]['custodiante_id'] > 0){
            $custodia->cargarMe($resultado[0]['custodiante_id']);
        }
        $this->setObjCustodia($custodia);
        
        $this->setDespachoNro($resultado[0]['despacho_nro']);
        $this->setImporteMovimiento($resultado[0]['movimiento_total_fact']);
        
        if($resultado[0]['factura_nro'] != NULL){
	    $this->setFacturaNro($resultado[0]['factura_nro']);
	}
	if($resultado[0]['remito_nro'] != NULL){
	    $this->setRemitoNro($resultado[0]['remito_nro']);
	}
	if($resultado[0]['nro_tramite_anmat'] != NULL){
	    $this->setNroTramiteAnmat($resultado[0]['nro_tramite_anmat']);
	}
        if($resultado[0]['orden_compra'] != NULL){
	    $this->setNroOrdenCompra($resultado[0]['orden_compra']);
	}
	if($resultado[0]['nro_nota_carga'] != NULL){
	    $this->setNroNotaCarga($resultado[0]['nro_nota_carga']);
	}
	if($resultado[0]['cant_dias_alquiler'] != NULL){
	    $this->setCantDiasAlquiler($resultado[0]['cant_dias_alquiler']);
	}
	if($resultado[0]['descuento'] > 0){
	    $this->setDescuento($resultado[0]['descuento']);
	}
	if($resultado[0]['obs'] != NULL){
	    $this->setObs($resultado[0]['obs']);
	}
	if($resultado[0]['tipo_comprobante'] != NULL){
	    $this->setTipoComprobante($resultado[0]['tipo_comprobante']);
	}
        $vendedor = new VendedorExtendido();
        if($resultado[0]['vendedor_id'] > 0){
            $vendedor->cargarMe($resultado[0]['vendedor_id']);
        }
        $this->setObjVendedor($vendedor);
        
        $distribuidor = new DistribuidorExtendido();
        if($resultado[0]['distribuidor_id'] > 0){
            $distribuidor->cargarMe($resultado[0]['distribuidor_id']);
        }
        $this->setObjDistribuidor($distribuidor);
        
        $pedido = new DatoPedidoExtendido();
        if($resultado[0]['pedido_id']>0){
	    $pedido->cargarMe($resultado[0]['pedido_id']);
        }
        $this->setObjPedido($pedido);
        
        $this->setDeIdMovimiento($resultado[0]['de_id_movimiento']);
        
        $this->setAIdMovimiento($resultado[0]['a_id_movimiento']);
        
        $this->setMovimientoUsuarioId($resultado[0]['movimiento_usuario_id']);
        //echo $resultado[0]['movimiento_finalizado'];exit;
        
	$this->setMovimientoFinalizado($resultado[0]['movimiento_finalizado']);
	
	if($resultado[0]['movimiento_finalizado_usuario_id'] != NULL){
	    $this->setMovimientoFinalizadoUsuarioId($resultado[0]['movimiento_finalizado_usuario_id']);
	}
	if($resultado[0]['movimiento_finalizado_fecha_hora'] != NULL){
	    $this->setMovimientoFinalizadoFechaHora($resultado[0]['movimiento_finalizado_fecha_hora']);
	}
        if($resultado[0]['domicilio_entrega'] != NULL){
	    $this->setDomicilioEntrega($resultado[0]['domicilio_entrega']);
	}
	if($resultado[0]['fecha_inicio_alquiler'] != NULL){
	    $this->setFechaInicioAlquiler($resultado[0]['fecha_inicio_alquiler']);
	}
	if($resultado[0]['fecha_vencimiento_alquiler'] != NULL){
	    $this->setFechaVencimientoAlquiler($resultado[0]['fecha_vencimiento_alquiler']);
	}
	if($resultado[0]['pedido_id'] != NULL){
	    $this->setPedidoId($resultado[0]['pedido_id']);
	}
    if($resultado[0]['relacion_id'] != NULL){
	    $this->setRelacionId($resultado[0]['relacion_id']);
	}
	
	if($resultado[0]['impreso'] != NULL){
	//echo "entra";exit;
	    $this->setMovimientoImpreso($resultado[0]['impreso']);
	}
	if($resultado[0]['lista_id'] != NULL){
	//echo "entra";exit;
	    $this->setListaId($resultado[0]['lista_id']);
	}
        $this->setRecepcionLiberadaFechaHora($resultado[0]['recepcion_liberada_fecha_hora']);
        $this->setRecepcionLiberadaUsuarioId($resultado[0]['recepcion_liberada_usuario_id']);
        $this->setRecepcionLiberada($resultado[0]['recepcion_liberada']);
        $this->setRecepcionLiberadaDesc($resultado[0]['recepcion_liberada_desc']);
        $this->setRecepcionBloqueadaDesc($resultado[0]['recepcion_bloqueada_desc']);
        $this->setRecepcionMensaje($resultado[0]['recepcion_mensaje']);

        //set del objTipoMovimiento
        $objTipoMovimientos = new TipoMovimientoTrazaExtendido();
        $objTipoMovimientos->cargarme($resultado[0]['tipo_movimiento_id']);
        $this->setObjTipoMovimiento($objTipoMovimientos);
        
        //set de los parametros de detalle
        foreach($resultado AS $rstdo){
            switch ($this->getObjTipoMovimiento()->getTipoMovimientoId()){
                case 1:
                    $objDetalleMovimiento = new DetalleMovimientoRecepcionExtendido();
                    $objDetalleMovimiento->cargarme($rstdo['detalle_id']);  
                    if(count($objDetalleMovimiento->getArrError()) > 0 ){
                        $this->setArrError($objDetalleMovimiento->getArrError());
                        return;
                    }
                    break;
                case 2:
                    $objDetalleMovimiento = new DetalleMovimientoSAlidaExtendido();
                    $objDetalleMovimiento->cargarme($rstdo['detalle_id']);  
                    if(count($objDetalleMovimiento->getArrError()) > 0 ){
                        $this->setArrError($objDetalleMovimiento->getArrError());
                        return;
                    }
                    break;
                case 3:
                    $objDetalleMovimiento = new DetalleMovimientoDevolucionExtendido();
                    $objDetalleMovimiento->cargarMe($rstdo['detalle_id']);  
                    if(count($objDetalleMovimiento->getArrError()) > 0 ){
                        $this->setArrError($objDetalleMovimiento->getArrError());
                        return;
                    }
                    break;
                case 4:
                    $objDetalleMovimiento = new DetalleMovimientoTraspasoExtendido();
                    $objDetalleMovimiento->cargarMe($rstdo['detalle_id']);  
                    if(count($objDetalleMovimiento->getArrError()) > 0 ){
                        $this->setArrError($objDetalleMovimiento->getArrError());
                        return;
                    }
                    break;
                case 5:
                    $objDetalleMovimiento = new DetalleMovimientoTraspasoExtendido();
                    $objDetalleMovimiento->cargarMe($rstdo['detalle_id']);  
                    if(count($objDetalleMovimiento->getArrError()) > 0 ){
                        $this->setArrError($objDetalleMovimiento->getArrError());
                        return;
                    }
                    break;
                case 6:
                    $objDetalleMovimiento = new DetalleMovimientoAnulacionExtendido();
                    $objDetalleMovimiento->cargarMe($rstdo['detalle_id']);  
                    if(count($objDetalleMovimiento->getArrError()) > 0 ){
                        $this->setArrError($objDetalleMovimiento->getArrError());
                        return;
                    }
                case 7:
                    $objDetalleMovimiento = new DetalleMovimientoRenovacionExtendido();
                    $objDetalleMovimiento->cargarMe($rstdo['detalle_id']);  
                    if(count($objDetalleMovimiento->getArrError()) > 0 ){
                        $this->setArrError($objDetalleMovimiento->getArrError());
                        return;
                    }
                    break;
                case 8:
                    $objDetalleMovimiento = new DetalleMovimientoCustodiaExtendido();
                    $objDetalleMovimiento->cargarMe($rstdo['detalle_id']);  
                    if(count($objDetalleMovimiento->getArrError()) > 0 ){
                        $this->setArrError($objDetalleMovimiento->getArrError());
                        return;
                    }
                    break;
                case 9:
                    $objDetalleMovimiento = new DetalleMovimientoCustodiaExtendido();
                    $objDetalleMovimiento->cargarMe($rstdo['detalle_id']);  
                    if(count($objDetalleMovimiento->getArrError()) > 0 ){
                        $this->setArrError($objDetalleMovimiento->getArrError());
                        return;
                    }
                    break;
                case 10:
                    $objDetalleMovimiento = new DetalleMovimientoRoboRoturaExtendido();
                    $objDetalleMovimiento->cargarMe($rstdo['detalle_id']);  
                    if(count($objDetalleMovimiento->getArrError()) > 0 ){
                        $this->setArrError($objDetalleMovimiento->getArrError());
                        return;
                    }
                    break;
                case 11:
                    $objDetalleMovimiento = new DetalleMovimientoDesconsolidadoExtendido();
                    $objDetalleMovimiento->cargarMe($rstdo['detalle_id']);  
                    if(count($objDetalleMovimiento->getArrError()) > 0 ){
                        $this->setArrError($objDetalleMovimiento->getArrError());
                        return;
                    }
                    break;
                case 12:
                    $objDetalleMovimiento = new DetalleMovimientoDesconsolidadoExtendido();
                    $objDetalleMovimiento->cargarMe($rstdo['detalle_id']);  
                    if(count($objDetalleMovimiento->getArrError()) > 0 ){
                        $this->setArrError($objDetalleMovimiento->getArrError());
                        return;
                    }
                    break;
                case 13:
                    $objDetalleMovimiento = new DetalleMovimientoConsolidadoExtendido();
                    $objDetalleMovimiento->cargarMe($rstdo['detalle_id']);  
                    if(count($objDetalleMovimiento->getArrError()) > 0 ){
                        $this->setArrError($objDetalleMovimiento->getArrError());
                        return;
                    }
                    break;
                case 14:
                    $objDetalleMovimiento = new DetalleMovimientoConsolidadoExtendido();
                    $objDetalleMovimiento->cargarMe($rstdo['detalle_id']);  
                    if(count($objDetalleMovimiento->getArrError()) > 0 ){
                        $this->setArrError($objDetalleMovimiento->getArrError());
                        return;
                    }
                    break;
		case 15:
                    $objDetalleMovimiento = new DetalleMovimientoOrdenTrabajoExtendido();
                    $objDetalleMovimiento->cargarMe($rstdo['detalle_id']);  
                    if(count($objDetalleMovimiento->getArrError()) > 0 ){
                        $this->setArrError($objDetalleMovimiento->getArrError());
                        return;
                    }
                    break;
		case 16:
                    $objDetalleMovimiento = new DetalleMovimientoPorProduccionExtendido();
                    $objDetalleMovimiento->cargarMe($rstdo['detalle_id']);  
                    if(count($objDetalleMovimiento->getArrError()) > 0 ){
                        $this->setArrError($objDetalleMovimiento->getArrError());
                        return;
                    }
                    break;
        case 17:
                    $objDetalleMovimiento = new DetalleMovimientoReventaExtendido();
                    $objDetalleMovimiento->cargarMe($rstdo['detalle_id']);  
                    if(count($objDetalleMovimiento->getArrError()) > 0 ){
                        $this->setArrError($objDetalleMovimiento->getArrError());
                        return;
                    }
                    break;
                default : 
                    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"EL movimiento que intentar realizar no fue vinculado con la clase que lo ejecuta"
		    );
		    echo json_encode($arrDevolver);exit;
                    break;                    
            }
            //cargamos la coleccion de objetos detalle
            $this->setColObjDetalleMovimiento($objDetalleMovimiento);
        }    
    }
    public function cargarmeNew($datos){
	//var_dump($datos);exit;
	if(is_numeric($datos)){	
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}
	$this->setIdMovimiento($resultado['id_movimiento']);
    $this->setMovimientoFechaHora($resultado['movimiento_fecha_hora']);
        
    $this->setDespachoNro($resultado['despacho_nro']);
    $this->setImporteMovimiento($resultado['movimiento_total_fact']);
        
    if($resultado['factura_nro'] != NULL){
	    $this->setFacturaNro($resultado['factura_nro']);
	}
	if($resultado['remito_nro'] != NULL){
	    $this->setRemitoNro($resultado['remito_nro']);
	}
	if($resultado['estado_id'] != NULL){
	    $this->setIdEstado($resultado['estado_id']);
	}
	if($resultado['nro_tramite_anmat'] != NULL){
	    $this->setNroTramiteAnmat($resultado['nro_tramite_anmat']);
	}
        if($resultado['orden_compra'] != NULL){
	    $this->setNroOrdenCompra($resultado['orden_compra']);
	}
	if($resultado['nro_nota_carga'] != NULL){
	    $this->setNroNotaCarga($resultado['nro_nota_carga']);
	}
	if($resultado['cant_dias_alquiler'] != NULL){
	    $this->setCantDiasAlquiler($resultado['cant_dias_alquiler']);
	}
	if($resultado['obs'] != NULL){
	    $this->setObs($resultado['obs']);
	}
        //$this->setObjPedido($pedido);
        
        if($resultado['pedido_nro_ext']>0){
            $this->setPedidoNroExt($resultado['pedido_nro_ext']);
        }
        
        $this->setDeIdMovimiento($resultado['de_id_movimiento']);
        
        $this->setAIdMovimiento($resultado['a_id_movimiento']);
        
        $this->setMovimientoUsuarioId($resultado['movimiento_usuario_id']);
        //echo $resultado['movimiento_finalizado'];exit;
        
	$this->setMovimientoFinalizado($resultado['movimiento_finalizado']);
	
	if($resultado['movimiento_finalizado_usuario_id'] != NULL){
	    $this->setMovimientoFinalizadoUsuarioId($resultado['movimiento_finalizado_usuario_id']);
	}
	if($resultado['movimiento_finalizado_fecha_hora'] != NULL){
	    $this->setMovimientoFinalizadoFechaHora($resultado['movimiento_finalizado_fecha_hora']);
	}
        if($resultado['domicilio_entrega'] != NULL){
	    $this->setDomicilioEntrega($resultado['domicilio_entrega']);
	}
	if($resultado['fecha_inicio_alquiler'] != NULL){
	    $this->setFechaInicioAlquiler($resultado['fecha_inicio_alquiler']);
	}
	if($resultado['fecha_vencimiento_alquiler'] != NULL){
	    $this->setFechaVencimientoAlquiler($resultado['fecha_vencimiento_alquiler']);
	}
	if($resultado['pedido_id'] != NULL){
	    $this->setPedidoId($resultado['pedido_id']);
	}
	if($resultado['relacion_nombre']!=null){ 
        $this->setRelacionNombre($resultado['relacion_nombre']);
	}
	if($resultado['tipo_comprobante']!=null){ 
        $this->setTipoComprobante($resultado['tipo_comprobante']);
	}
	if($resultado['impreso']!=null){ 
        $this->setMovimientoImpreso($resultado['impreso']);
	}
	if($resultado['lista_id']!=null){
        $this->setListaId($resultado['lista_id']);
	}
	if($resultado['tipo_entrega']!=null){ 
        $this->setTipoEntrega($resultado['tipo_entrega']);
	}
	if($resultado['forma_pago']!=null){
        $this->setFormaPago($resultado['forma_pago']);
	}
	if($resultado['usuario_nombre']!=null){ 
        $this->setUsuarioNombre($resultado['usuario_nombre']);
	}
	$this->setRecepcionLiberadaFechaHora($resultado['recepcion_liberada_fecha_hora']);
    $this->setRecepcionLiberadaUsuarioId($resultado['recepcion_liberada_usuario_id']);
    $this->setRecepcionLiberada($resultado['recepcion_liberada']);
    $this->setRecepcionLiberadaDesc($resultado['recepcion_liberada_desc']);
    $this->setRecepcionBloqueadaDesc($resultado['recepcion_bloqueada_desc']);
    $this->setRecepcionMensaje($resultado['recepcion_mensaje']);
	$objTipoMovimientos = new TipoMovimientoTrazaExtendido();
    $objTipoMovimientos->cargarme($resultado['tipo_movimiento_id']);
    $this->setObjTipoMovimiento($objTipoMovimientos);
    
    
    }

}
