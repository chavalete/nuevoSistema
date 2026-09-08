<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
//PEDIDOS
require_once 'DatoPedidoExtendido.php';
/*
 * dMELMAC 
 * Guillo
 * chava
 * 
 */

/**
 * Description of FrentePedidos
 *
 * @author by dMELMAC
 */

class FrentePedidos
{
    private $_db;
    private $_colObjDatoPedido= Array();
        
    //private $_arrError = Array();
    private $_objFrenteError ;
    
    //** NECESARIOS PARA EL FRENTE DE FRENTES//
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    //** FIN NECESIARIOS**//

    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
	$this->_objFuncionesComunes = new FuncionesComunes();
        $this->_objFrenteError = new FrenteError();
    }
    public function setColObjDatoPedido($obj){
        $this->_colObjDatoPedido[$obj->getPedidoId()] = $obj;
    }
    public function getColObjDatoPedido(){
        return $this->_colObjDatoPedido;
    }
    public function cargarLosDatosLazzy($arrIds){
    
	if(is_array($arrIds)){
	    foreach($arrIds as $id){
		$ids.= $id['pedido_id'] . ",";
	   }
		$ids=substr($ids, 0, -1);
	}else{
		$ids=$arrIds;
	}
	$this->_db->addSelect('*');
        $this->_db->addFrom('datos_pedidos');
        $this->_db->addWhere("pedido_id IN (" . $ids . ")" );
        $this->_db->addOrderBy('pedido_id DESC');
        
	$this->_db->generarSelect();
        
        $resultado = $this->_db->ejecutar();
        
        if(count($resultado)==0){
	    return;
        }

        if($_POST['accion']=='prepararPedido'){ 
                if ($resultado[0][pedido_preparado]==true)  {
                    $arrDevolver = array(
                                "soyError" => true,
                                "nivel" => 0,
                                "alertados" => $arrAlertador,
                                "mensaje" => "El pedido ya fue validado" 
                                );
                    echo json_encode($arrDevolver);exit;
                }
                if ($resultado[0]['estado_id'] != 1 ){
                    $arrDevolver = array(
                                "soyError" => true,
                                "nivel" => 0,
                                "alertados" => $arrAlertador,
                                "mensaje" => "Verifique el estado del pedido, no es posible prepararlo." 
                                );
                
                    echo json_encode($arrDevolver);exit;
                }
        }

	$FrenteClientes = NEW FrenteClientes();
        $FrenteClientes->cargarClientesLazzy($resultado);
        $arrObjClientes= $FrenteClientes->getColObjCliente();
        
        $FrentePaciente = NEW FrentePacientes();
        $FrentePaciente->cargarPacientesLazzy($resultado);
        $arrObjPaciente= $FrentePaciente->getColObjPaciente();
        
        $FrenteEstados= NEW FrenteEstados();
        $FrenteEstados->cargarEstadosLazzy($resultado);
        $arrObjEstados= $FrenteEstados->getColObjEstados();
        
        //var_dump($resultado);exit;
        
        $FrenteLocalidades= NEW FrenteLocalidades();
        $FrenteLocalidades->cargarLocalidadesLazzy($resultado);
        $arrObjLocalidades= $FrenteLocalidades->getColObjLocalidades();

        foreach($resultado AS $datos){
       	    
	    $objDatosPedido= NEW DatoPedidoExtendido();
	    $objDatosPedido->cargarme($datos);
	    $objDatosPedido->setObjCliente($arrObjClientes[$datos[cliente_id]]);
	    $objDatosPedido->setObjPaciente($arrObjPaciente[$datos[paciente_id]]);
	    $objDatosPedido->setObjEstado($arrObjEstados[$datos[estado_id]]);
            if($datos[localidad_id] == NULL){
                $datos[localidad_id] = -1;
            }
            $objDatosPedido->setObjLocalidad($arrObjLocalidades[$datos[localidad_id]]);
	    
            $this->setColObjDatoPedido($objDatosPedido);
        }
    
    }
    
    public function cargarLosDetallesLazzy($arrIds){
    
	foreach($arrIds as $id){
	    $ids.= $id['pedido_detalle_id'] . ",";
	}
	
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
        $this->_db->addFrom('detalle_pedidos');
        $this->_db->addWhere("pedido_detalle_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        $FrenteProductos = NEW FrenteProductos();
        $FrenteProductos->cargarProductosLazzy($resultado);
        $arrObjProductos= $FrenteProductos->getColProductos();
        
        foreach($this->getColObjDatoPedido() AS $objPedido){      
	    foreach($resultado AS $detalle){
		$objDetallePedido= NEW DetallePedidoExtendido();
		$objDetallePedido->cargarme($detalle);
		$objDetallePedido->setObjProducto($arrObjProductos[$detalle[producto_id]]);
		$this->_colObjDatoPedido[$objPedido->getPedidoId()]->setColObjDetallePedido($objDetallePedido);
	    }
	}
    
    }

    public function cargarLosDetallesLazzyPorLote($arrIds){
    
	foreach($arrIds as $id){
	    $ids.= $id['pedido_detalle_id'] . ",";
	}	
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
        $this->_db->addFrom('pedidos_lotes');
        $this->_db->addWhere("pedido_detalle_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        $FrenteProductos = NEW FrenteProductos();
        $FrenteProductos->cargarProductosLazzy($resultado);
        $arrObjProductos= $FrenteProductos->getColProductos();
        
        $FrenteLotes= NEW FrenteLotes();
        $FrenteLotes->cargarLotesLazzy($resultado);
        $arrObjLotes= $FrenteLotes->getColObjLote();
        
        //var_dump($arrObjLotes); exit;
        
        $FrenteEstanterias = NEW FrenteEstanterias();
        $FrenteEstanterias->cargarEstanteriasLazzy($resultado);
        $arrObjEstanterias = $FrenteEstanterias->getColEstanterias();
        
        foreach($this->getColObjDatoPedido() AS $objPedido){
	    foreach($resultado AS $detalle){
		$objDetallePedido= NEW DetallePedidoPorLoteExtendido();
		$objDetallePedido->cargarme($detalle);
		$objDetallePedido->setObjProducto($arrObjProductos[$detalle[producto_id]]);
                $objDetallePedido->setObjLote($arrObjLotes[$detalle['lote_id']]);
                $objDetallePedido->setObjEstanteria($arrObjEstanterias[$detalle['estanteria_id']]);
                $this->_colObjDatoPedido[$objPedido->getPedidoId()]->setColObjDetallePedido($objDetallePedido);
	    }
	}
    }
    
    
    public function cargarPedidoCompleto($pedidoId){
    
        $this->_db->addSelect('pedido_id, pedido_detalle_id');
	$this->_db->addFrom('datos_pedidos INNER JOIN detalle_pedidos USING(pedido_id)');

	if(is_string($pedidoId)){
	    if(substr($pedidoId, 0,3) == '+C0' || substr($pedidoId, 0,3) == '+c0' ){
                $pedidoId = substr($pedidoId,3); 
            }        
            $this->_db->addWhere('pedido_id = \'' . $pedidoId . '\'');
	}else{
            if(substr($pedidoId['codigo'], 0,3) == '+C0' || substr($pedidoId['codigo'], 0,3) == '+c0' ){
                $pedidoId['codigo'] = substr($pedidoId['codigo'],3); 
            }
            $this->_db->addWhere('pedido_id = \'' . $pedidoId['codigo'] . '\'');      
	}
     
        $this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
        $resultadoCabeceras = $this->_db->ejecutar();
        
        if(count($resultadoCabeceras)==null){
	    $arrDevolver = array(
                                "soyError" => true,
                                "nivel" => 0,
                                "alertados" => $arrAlertador,
                                "mensaje" => "No existe el pedido ingresado" 
                                );
	    echo json_encode($arrDevolver);exit;
        }
        $this->cargarLosDatosLazzy($resultadoCabeceras['0']['pedido_id']);

	if($_POST['accion']=='prepararPedido' || $_POST['accion']=='validarPedido' ){
            $this->cargarLosDetallesLazzyPorLote($resultadoCabeceras);
	}else{
	    $this->cargarLosDetallesLazzy($resultadoCabeceras);
	}
    }


    public function buscarPedido($arrParametros){


	$this->_db->addSelect('pedido_id');
	$this->_db->addFrom('datos_pedidos');
	$this->_db->addFrom('INNER JOIN detalle_pedidos USING(pedido_id)');


	//**ANALIZO EL WHERE**//
	if($arrParametros['stringBuscar']){

	    //$this->_db->addWhere('id_movimiento ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
	}

	//**where faso = true**/
	if($arrParametros['clientes'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('cliente_id = \'' . $arrParametros[clientes] . '\'');
	}
	if($arrParametros['nroPedido'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('pedido_nro= \'' . $arrParametros[nroPedido] . '\'');
	}
	if($arrParametros['remitos'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('pedido_remito_nro = \'' . $arrParametros[remitos] . '\'');
	}
	if($arrParametros['facturas'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('pedido_factura_nro = \'' . $arrParametros[facturas] . '\'');
	}
	if($arrParametros['estados'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('estado_id= \'' . $arrParametros[estados] . '\'');
	}
	$this->_db->addWhere('pedido_cancelado = false ');
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenPedidos($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['desdeBusqueda']==false){
	    $this->_db->addLimit(500);
	}


	$this->_db->addGroup('pedido_id');
	//*FIN LIMIT*//
	
	$this->_db->generarSelect();

	//echo $this->_db->getQry();exit;
	$resultado =  $this->_db->ejecutar();
	
	if(count($resultado)==0){
	
	    return;
	}
	
	//ahora seteamos el total y la pagina
	$this->setTotal(count($resultado));
	$this->setPagina($arrParametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro)	{
	    $ultimoRegistro = $this->getTotal();
	}
	
	for($i=$primerRegistro;$i<$ultimoRegistro;$i++)	{     
	    $arrIds[$i]['pedido_id']= $resultado[$i]['pedido_id'];
	}
	//var_dump($arrIds);exit;
	$this->cargarLosDatosLazzy($arrIds);
    }

    
    //**PARA EL FRENTE**//
    public function setOrdenPedidos($parametros){
	if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){
	    $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'pedido_id';
	    $this->_ordenColumnas['ordenarOrden']  =   'DESC';
	}
    }

    public function getOrdenPedidos(){
	return $this->_ordenColumnas;
    }

    public function setTotal($total)
    {
	$this->_total = $total;
    }
    public function getTotal()
    {
	return $this->_total;
    }
    //pagina 1 por defecto amigo
    public function setPagina($pagina=1)
    {
	$this->_pagina = $pagina;
    }
    public function getPagina()
    {
	return $this->_pagina;
    }

    public function getDetalles(){

	foreach($this->_colObjDatoPedido AS $objPedido){
	
		$arr []   =    array
		    (
			//id si o si un solo string (sin espacios en blanco)
			"id"            =>  $objPedido->getPedidoId(),
			//define las herramientas
			"herramientas"   =>  array(
			    "editable"      =>  false,
			    "cancelable"    =>  true,
			    "detalles"      =>  true,
			    "imprimir"      =>  true,
			    "autogestion"      =>  false,
			    "enviarPedido"      =>  false
			    
			),
			//las celdas propiamente dichas, en el orden en que estan declaradas las columnas
			"cell"          => array(
			    $objPedido->getPedidoNro(),
			    $objPedido->getPedidoFechaHora(),
			    $objPedido->getObjCliente()->getClienteNombre(),
			    $objPedido->getObjEstado()->getEstadoNombre(),
			    $objPedido->getPedidoRemitoNro(),
			    $objPedido->getPedidoFacturaNro(),
			    $objPedido->getPedidoObs()
			),
		    );

	}
	return $arr;
    }

    public function armarDetalles($pedidoId){

	$this->cargarPedidoCompleto($pedidoId);
	
	foreach($this->_colObjDatoPedido AS $objPedido){
	
	
		$propiedadesPedido =   array(
			array(
			    "display"   =>  'Nro. Pedido',
			    "name"      =>  'pedido_nro',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getPedidoNro(),

			),
			array(
			    "display"   =>  'Fecha',
			    "name"      =>  'fecha_hora_pedido',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getPedidoFechaHora(),

			),
			array(
			    "display"   =>  'Cliente',
			    "name"      =>  'cliente_id',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getObjCliente()->getClienteNombre(),

			),
			array(
			    "display"   =>  'Paciente',
			    "name"      =>  'paciente_id',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getObjPaciente()->getPacienteNombre(),

			),
			array(
			    "display"   =>  'Estado',
			    "name"      =>  'estado_id',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getObjEstado()->getEstadoNombre(),

			),
			array(
			    "display"   =>  'Localidad',
			    "name"      =>  'localidad_id',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getObjLocalidad()->getLocalidadNombre(),

			)
		    );
	}
	foreach($objPedido->getColObjDetallePedido() AS $detalle){

    
	    $arr []   =    array
		(
		    //id si o si un solo string (sin espacios en blanco)
		    "id"            =>  $detalle->getPedidoDetalleId(),
		    //define las herramientas
		    "herramientas"   =>  array(
			 "editable"      =>  false,
			  ),
			 "bloqueado"     => false,	    
		    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
		    "cell"          => array(
			$detalle->getObjProducto()->getProductoNombre(),
			$detalle->getObjProducto()->getProductoPresentacion(),
			$detalle->getObjProducto()->getProductoGtin(),
			$detalle->getPedidoCantidad(),
		    ),
		);
	}

    
	$detallesPedido    =   array(
	    "bloqueable"=> false,
	    "modelo"    =>  array(
		array(

		    "display"   =>  'Producto',
		    "name"      =>  'producto_id',
		    "editable"  =>  false,
		    "class"	=>'',
		    
		),
		array(

		    "display"   =>  'Presentacion',
		    "name"      =>  'producto_presentacion',
		    "editable"  =>  false,
		    "class"	=>'',
		    
		),
		array(
		    "display"   =>  'GTIN',
		    "name"      =>  'producto_gtin',
		    "editable"  =>  $arrParametros[edicion],
		    "class"	=>'',
		),
		array(

		    "display"   =>  'Cantidad',
		    "name"      =>  'cantidad',
		    "editable"  =>  $arrParametros[edicion],
		    "class"	=>'',
		),

	    ),
	    "celdas"    =>  $arr
	);
	
	$array_devolver =   array(
	    "editable_cabecera" => false,
	    "propiedades"       =>  $propiedadesPedido,
	    "listadoDetalles"   =>  $detallesPedido
	);
	    return $array_devolver;
    }
    
    public function generarPedido($arrParametros){
    
        //control -> si es renovacion, si existe primero y luego verifica que el cliente y el paciente sean el mismo.
        if ($arrParametros['renovacionId'] != NULL) {
            $movimiento = new FrenteMovimiento();
            $movimiento->cargarMovimiento($arrParametros['renovacionId']);
            $arrMovimiento = $movimiento->getColObjDatoMovimiento();
            if($arrMovimiento[0]->getMovimientoFinalizado() == TRUE){
                    $arrError['detalle'] = "La renovación {$arrParametros['renovacionId']} ya fue finalizada, no es posible agregarle productos.";
                    $arrError['archivo'] = __FILE__;
                    $this->_objFrenteError->generarError($arrError);
                    $this->_objFrenteError->cargarError();
            }
            $objCliente = $arrMovimiento[0]->getObjCliente();
            $objPaciente = $arrMovimiento[0]->getObjPaciente();
            if($movimiento != NULL){
                if($arrParametros['clientes'] != $objCliente->getClienteId() || $arrParametros['pacientes'] !=  $objPaciente->getPacienteId()){
                    $arrError['detalle'] = "El cliente o Paciente ingresado no corresponde con los cargados en la renovación ({$arrParametros['renovacionId']}).";
                    $arrError['archivo'] = __FILE__;
                    $this->_objFrenteError->generarError($arrError);
                    $this->_objFrenteError->cargarError();
                }
            }
        }
        
        $this->_db->addCamposTabla('seq_datos_pedidos_pedidos_id');
	$this->_db->generarProximo();
        $resultado = $this->_db->ejecutar();
	$arrParametros['idPedido'] = $resultado[0]['nextval'];        
        $arrParametros['estadoId'] = 1; //En preparación
        
        
	$arrProductos = explode ("||",$arrParametros['productos']);
        
	array_pop($arrProductos);
	
        foreach ($arrProductos AS $linea){
            
            $loteAutomatico = 1;
            $loteId='NULL';
            
   
	    list($productoId, $unidades, $lote) = explode("#",$linea);
            
            //objProducto para controlar que las unidades ingresadas sea multiplo con las unidades del objeto
            $frenteProductos = new FrenteProductos();
            $frenteProductos->cargarProducto($productoId);
            $colObjProductos = $frenteProductos->getColProductos();
            if( $unidades%$colObjProductos[$productoId]->getUnidades() != 0 ){
                $arrError['detalle'] =  'La cantidad solicitada tiene que ser multiplo de ' . $colObjProductos[$productoId]->getUnidades();
                $this->_objFrenteError->generarError($arrError);
                $this->_objFrenteError->cargarError();
            }
            
            //$arrProductosFinal = explode("#",$linea);
            if($lote != null){
                $loteAutomatico = 0;
                $arrLote = array('lote' => $lote, 'producto_id' => $productoId); 
                $objFrenteLotes = new FrenteLotes();
                $objFrenteLotes->cargarLotePorLoteProducto($arrLote);
                $loteId = $objFrenteLotes->getUltimoLoteId();
            }
            
            $arrProductosFinal['idPedido'] = $arrParametros['idPedido'];
            $arrProductosFinal['productoId'] = $productoId;
            $arrProductosFinal['cantidad'] = $unidades / $colObjProductos[$productoId]->getUnidades() ;
            $arrProductosFinal['unidades'] = $unidades ;
                   
            //var_dump($arrProductosFinal);
            
            $this->_db->addCamposTabla('seq_detalle_pedidos_detalle_id');
            $this->_db->generarProximo();
            $resultado = $this->_db->ejecutar();
            $arrProductosFinal['pedidoDetalleId'] = $resultado[0]['nextval'];        
        
            $objDetallePedido = new DetallePedidoExtendido();
            $qryPedido['detalle'] .= $objDetallePedido->salvarme($arrProductosFinal);
            
            $qryPedido['detalle'] .= 'SELECT fn_movimiento_stock(' . $loteAutomatico .' , ' . $arrProductosFinal['idPedido'] . ', ' . $arrProductosFinal['pedidoDetalleId']  . ', ' . $arrProductosFinal['cantidad'] * -1 . ', ' . $arrProductosFinal['unidades'] * -1 . ', ' . $productoId . ',' . $loteId . '); ';
            //echo $qryPedido['detalle']; exit;
            if (count($objDetallePedido->getArrError()) > 0){
                $this->_objFrenteError->generarError($objDetallePedido->getArrError());
                $this->_objFrenteError->cargarError();
            }	
        }
        
	$objDatoPedido = new DatoPedidoExtendido();
        $qryPedido['dato'] = $objDatoPedido->salvarme($arrParametros);
        if (count($objDatoPedido->getArrError()) > 0){
            $this->_objFrenteError->generarError($objDatoPedido->getArrError());
            $this->_objFrenteError->cargarError();
        }
        
        $arrQry['log'] = "SELECT fn_log_historico_estados_pedidos(0,{$arrParametros['idPedido']},1,{$_SESSION['usuarioId']})";
        $qryFinal = $qryPedido['detalle'] . $qryPedido['dato'] . $arrQry['log'];
        
        //echo $qryFinal; exit;
        
        $this->_db->setQry($qryFinal);
        $this->_db->ejecutarTransaccion();
        //echo count($this->_db->getArrError());
        if (count($this->_db->getArrError()) > 0){
            $this->_objFrenteError->generarError($this->_db->getArrError());
            $this->_objFrenteError->setQry($qryFinal);
            $this->_objFrenteError->cargarError();
            //salvar el error
        }else{
            $arrDevolver = array(
                                "soyError" => false,
                                "nivel" => 0,
                                "alertados" => $arrAlertador,
                                "mensaje" => "Pedido generado - Nro Pedido :" . $arrParametros['idPedido'] 
                                );
        
        }
        echo json_encode($arrDevolver);exit;
    }

    public function cancelarPedido($id){
        //cargar el obj dato del pedido
        $objDatoPedido = new DatoPedidoExtendido();
        $objDatoPedido->cargarme($id);
        
        //chequear que no este validado
        if($objDatoPedido->getPedidoPreparado() == TRUE){
            $arrDevolver = array(
                                "soyError" => false,
                                "nivel" => 0,
                                "alertados" => $arrAlertador,
                                "mensaje" => " El pedido nro " . $objDatoPedido->getPedidoNro() . " ya fue validado con anterioridad, NO ES POSIBLE REALIZAR LA ANULACIÓN." 
                                );
            echo json_encode($arrDevolver);exit;
        }
        
        $qryCancelacion = $objDatoPedido->cancelarMe($id);
        
        $this->_db->addSelect('ABS(pl.cantidad)  AS cantidad, da.sucursal_id, da.almacen_id, de.estanteria_id, pl.lote_id, pl.producto_id');
        $this->_db->addFrom('pedidos_lotes pl');
        $this->_db->addFrom('INNER JOIN datos_estanterias de USING (estanteria_id)');
        $this->_db->addFrom('INNER JOIN datos_almacenes da USING (almacen_id)');
        $this->_db->addWhere('pedido_id = ' . $id );
        
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        foreach ($resultado as $detalle){
            $qryCancelacion .= 'SELECT fn_actualizar_stock_completo(' . $detalle['cantidad'] . ',' . $detalle['sucursal_id'] . ',' . $detalle['almacen_id'] . ',' . $detalle['estanteria_id'] . ',' . $detalle['lote_id'] . ',' . $detalle['producto_id'] . ');';
        }
        $qryCancelacion.= "SELECT fn_log_historico_estados_pedidos(0,{$id},3,{$_SESSION['usuarioId']})";
        //echo $qryCancelacion;exit;
        $this->_db->setQry($qryCancelacion);
        $this->_db->ejecutarTransaccion();
        //echo count($this->_db->getArrError());
        if (count($this->_db->getArrError()) > 0){
            $this->_objFrenteError->generarError($this->_db->getArrError());
            $this->_objFrenteError->setQry($qryCancelacion);
            $this->_objFrenteError->cargarError();
            //salvar el error
        }else{
            $arrDevolver = array(
                                "soyError" => false,
                                "nivel" => 0,
                                "alertados" => $arrAlertador,
                                "mensaje" => "El pedido nro. " . $objDatoPedido->getPedidoNro() . " fue cancelado con EXITO."
                                );
        }  
        echo json_encode($arrDevolver);exit;
    }
    public function actualizarEstado($arrParametros){
    
	$objDatoPedido = new DatoPedidoExtendido();
	$objDatoPedido->actualizar();
    
    }
    public function prepararPedido($arrParametros){
        //var_dump($arrParametros);
	$this->cargarPedidoCompleto($arrParametros);
	
	foreach($this->_colObjDatoPedido AS $objPedido){

            
		$propiedadesPedido =   array(
			array(
			    "display"   =>  'Nro. Pedido',
			    "name"      =>  'pedido_nro',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getPedidoNro(),

			),
			array(
			    "display"   =>  'Fecha',
			    "name"      =>  'fecha_hora_pedido',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getPedidoFechaHora(),

			),
			array(
			    "display"   =>  'Cliente',
			    "name"      =>  'cliente_id',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getObjCliente()->getClienteNombre(),

			),
			array(
			    "display"   =>  'Paciente',
			    "name"      =>  'paciente_id',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getObjPaciente()->getPacienteNombre(),

			),
			array(
			    "display"   =>  'Estado',
			    "name"      =>  'estado_id',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getObjEstado()->getEstadoNombre(),

			),
			array(
			    "display"   =>  'Localidad',
			    "name"      =>  'localidad_id',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     => $objPedido->getObjLocalidad()->getLocalidadNombre(),

			)
		    );
	}
	foreach($objPedido->getColObjDetallePedido() AS $detalle){

    
	    $arr []   =    array
		(
		    //id si o si un solo string (sin espacios en blanco)
		    "id"            =>  $detalle->getPedidoDetalleId(),
		    //define las herramientas
		    "herramientas"   =>  array(
			 "editable"      =>  false,
			  ),
			 "bloqueado"     => false,	    
		    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
		    "cell"          => array( $detalle->getObjProducto()->getCodigoReferencia(),
			$detalle->getObjProducto()->getProductoNombre() . " " . $detalle->getObjProducto()->getProductoPresentacion(),
			$detalle->getObjLote()->getLote(),
			abs($detalle->getPedidoCantidad()),
			$detalle->getObjEstanteria()->getEstanteriaNombre()
		    ),
		);
	}

    
	$detallesPedido    =   array(
	    "bloqueable"=> false,
	    "modelo"    =>  array(
		array(

		    "display"   =>  'Código Ref.',
		    "name"      =>  'codigo_referencia',
		    "editable"  =>  false,
		    "class"	=>'',
		    
		),
		array(

		    "display"   =>  'Presentación',
		    "name"      =>  'producto_presentacion',
		    "editable"  =>  false,
		    "class"	=>'',
		    
		),
		array(
		    "display"   =>  'Lote',
		    "name"      =>  'lote_id',
		    "editable"  =>  $arrParametros[edicion],
		    "class"	=>'',
		),
		array(

		    "display"   =>  'Cantidad',
		    "name"      =>  'cantidad',
		    "editable"  =>  $arrParametros[edicion],
		    "class"	=>'',
		),
		array(

		    "display"   =>  'Estanteria',
		    "name"      =>  'estanteria_id',
		    "editable"  =>  $arrParametros[edicion],
		    "class"	=>'',
		),

	    ),
	    "celdas"    =>  $arr
	);
	
	$array_devolver =   array(
	    "editable_cabecera" => false,
	    "propiedades"       =>  $propiedadesPedido,
	    "listadoDetalles"   =>  $detallesPedido
	);
	    return $array_devolver;
    }
    
    public function validarPedido($arrParametros){
        $this->cargarPedidoCompleto($arrParametros['id']);
        
        $arrCodigos = explode("||", $arrParametros['codigos']);
        array_pop($arrCodigos);
        
        //control de ingresos para codigos ingresados mas de una vez. 
        $arrFiltrado = array_unique($arrCodigos);
        if(count($arrFiltrado) != count($arrCodigos)){
            $arrCodigosRepetidos = array_diff_key($arrCodigos, $arrFiltrado);
            $txtCodigos = implode("', '", $arrCodigosRepetidos);
            $arrError['detalle'] = 'Códigos ingresados mas de una vez : ' . $txtCodigos;
            $arrError['archivo'] = __FILE__;
            $this->_objFrenteError->generarError($arrError);
            $this->_objFrenteError->cargarError();
        }
        //cargamos los objetos y de paso controla que todos los codigos existan
        $frenteTrazabilidad = new FrenteTrazabilidad();

        foreach($arrCodigos AS $codigo){
            list($tipo, $gtin, $serie, $vencimiento, $lote, $productoId) = explode("#", $codigo);
	    $frenteTrazabilidad->cargarPorCodigo($serie);
        }
        
                
	foreach($frenteTrazabilidad->getColObjDatoTrazabilidad() AS $objTrazabilidad){
            $objProducto = $objTrazabilidad->getObjProducto();
            $objLote = $objTrazabilidad->getObjLote();
            $objEstanteria = $objTrazabilidad->getObjEstanteria();
            
            $arrFinal[$objProducto->getProductoId()][$objLote->getLoteId()][$objEstanteria->getEstanteriaId()]['lote'] = $objLote->getLote();
            $arrFinal[$objProducto->getProductoId()][$objLote->getLoteId()][$objEstanteria->getEstanteriaId()]['estanteria'] = $objEstanteria->getEstanteriaNombre();
            $arrFinal[$objProducto->getProductoId()][$objLote->getLoteId()][$objEstanteria->getEstanteriaId()]['cantidad'] = 0  ;
            $arrFinal[$objProducto->getProductoId()][$objLote->getLoteId()][$objEstanteria->getEstanteriaId()]['ingreso'] += 1 * $objProducto->getUnidades() ;
            
        }
        
        foreach ($this->getColObjDatoPedido() as $objPedido){
            foreach($objPedido->getColObjDetallePedido() AS $objDetallePedido){
        	if($arrFinal[$objDetallePedido->getObjProducto()->getProductoId()][$objDetallePedido->getObjLote()->getLoteId()][$objDetallePedido->getObjEstanteria()->getEstanteriaId()]['ingreso'] == NULL){
                   $arrFinal[$objDetallePedido->getObjProducto()->getProductoId()][$objDetallePedido->getObjLote()->getLoteId()][$objDetallePedido->getObjEstanteria()->getEstanteriaId()]['ingreso'] = 0;
                }
                $arrFinal[$objDetallePedido->getObjProducto()->getProductoId()][$objDetallePedido->getObjLote()->getLoteId()][$objDetallePedido->getObjEstanteria()->getEstanteriaId()]['lote'] = $objDetallePedido->getObjLote()->getLote(); 
                $arrFinal[$objDetallePedido->getObjProducto()->getProductoId()][$objDetallePedido->getObjLote()->getLoteId()][$objDetallePedido->getObjEstanteria()->getEstanteriaId()]['estanteria'] = $objDetallePedido->getObjEstanteria()->getEstanteriaNombre(); 
                $arrFinal[$objDetallePedido->getObjProducto()->getProductoId()][$objDetallePedido->getObjLote()->getLoteId()][$objDetallePedido->getObjEstanteria()->getEstanteriaId()]['cantidad'] += $objDetallePedido->getPedidoUnidades(); 
                
            }
        }
        
        $arrTitulo = array ('Lote','Estanteria', 'Cant.', 'Ingr.' );
        $validado = TRUE; 
        
        foreach($arrFinal as $arrProductos){
            foreach ($arrProductos as $arrLotes){
                foreach ($arrLotes as $arrEstanteria ){
                        if(abs($arrEstanteria['cantidad']) != $arrEstanteria['ingreso']){
                            $validado = FALSE;
                            $color = "red";
                        }else {
                            $color = "blue";

                        }
                        $datos[] = array('color' => $color , $arrEstanteria['lote'] , $arrEstanteria['estanteria'] , abs($arrEstanteria['cantidad']), $arrEstanteria['ingreso']);
                }
            }
        }

      
        $arrDevolver = array('titulo' => $arrTitulo , 'datos' => $datos);
        
        if ($validado == FALSE){
            $arrError['detalle'] = $arrDevolver;
            $arrError['archivo'] = __FILE__;
            $this->_objFrenteError->generarError($arrError);
            $this->_objFrenteError->cargarErrorTabla();
        }

        //le paso la pelota al frenteMovimientos para que termine el trabajo
        $arrMovimiento['tipo'] = 'salida';
        $arrMovimiento['tipoMovimientoId'] = 2;
        $arrMovimiento['codigos'] = $arrParametros['codigos'];
        
        $objDatoPedido = array_shift($this->getColObjDatoPedido());
        
        $arrMovimiento['cliente'] = $objDatoPedido->getObjCliente()->getClienteId();
        $arrMovimiento['pedidoId'] = $objDatoPedido->getPedidoId();
        $arrMovimiento['renovacionId'] = $objDatoPedido->getRenovacionId();
        $arrMovimiento['domicilioEntrega'] = $objDatoPedido->getPedidoCalle() . " " . $objDatoPedido->getPedidoAltura();
        if($objDatoPedido->getPedidoPiso() != NULL){
             $arrMovimiento['domicilioEntrega'] .= " Piso " . $objDatoPedido->getPedidoPiso();
        }
        if($objDatoPedido->getPedidoDepto() != NULL){
             $arrMovimiento['domicilioEntrega'] .= " Dpto " . $objDatoPedido->getPedidoDepto();
        }
        if($objDatoPedido->getCodigoPostal() != NULL){
             $arrMovimiento['domicilioEntrega'] .= " CP " . $objDatoPedido->getCodigoPostal();
        }
        
        if($objDatoPedido->getObjLocalidad() != NULL){
            $objLocalidad = $objDatoPedido->getObjLocalidad();
            $arrMovimiento['domicilioEntrega'] .= "  " . $objLocalidad->getLocalidadNombre();
            $arrMovimiento['domicilioEntrega'] .= " - " . $objLocalidad->getObjProvincia()->getProvinciaNombre();
    	}
        
	if($objDatoPedido->getObjPaciente() != NULL){
            $objPaciente= $objDatoPedido->getObjPaciente();
            if($objPaciente->getPacienteId() > 0 ){
		$arrMovimiento['pacienteId'] = $objPaciente->getPacienteId();
       	    }
	 }	
    
    //var_dump($arrMovimiento);exit;

        $frenteMovimiento = new FrenteMovimiento();
        $frenteMovimiento->generarMovimiento($arrMovimiento);
    }
}

