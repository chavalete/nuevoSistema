<?php
//ALMACENAMIENTO
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
//TRAZABILIDAD
require_once 'DatoMovimientoExtendido.php';
require_once 'DatoTrazabilidadExtendido.php';

/**
 * Description of FrenteTrazabilidad
 *
 * @author by dMELMAC
 */

class FrenteMovimiento
{
    private $_db;
    private $_colObjDatoMovimiento = Array();
    private $_colObjDetalleMovimiento = Array();
    
    //private $_arrError = Array();
    private $_objFrenteError;

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
    
    public function setColObjDatoMovimiento($obj){
        $this->_colObjDatoMovimiento[] = $obj;
    }
    
    public function getColObjDatoMovimiento(){
        return $this->_colObjDatoMovimiento;
    }
    
    public function setColObjDetalleMovimiento($obj){
        $this->_colObjDetalleMovimiento[] = $obj;
    }
    
    public function getColObjDetalleMovimiento(){
        return $this->_colObjDetalleMovimiento;
    }
    
    public function generarMovimiento($arrParametros){
        try {
        //validacion capocha para safar duplicados
        $arrLineas = explode ("||",$arrParametros[productos]);
        $arrExistencia=array();
        array_pop($arrLineas);
                
            foreach ($arrLineas AS $linea){
        
                list( $productoId,$cantidad, $productoPcosto,$vencimiento) = explode("#",$linea);
                
                $claveUnica = $productoId . "_" . $vencimiento;

                if (isset($arrExistencia[$claveUnica])) {
                    $qry="SELECT producto_nombre || ' - ' || producto_presentacion AS nombre FROM datos_productos WHERE producto_id = $productoId;";
                    $this->_db->setQry($qry);
                    $resultado = $this->_db->ejecutar();
                    $arrDevolver = array(
                                    "soyError" => true,
                                    "nivel" => 0,
                                    "alertados" => $arrAlertador,
                                    "mensaje" => "El producto " . $resultado[0][nombre] . " esta duplicado "
                                    );
                    echo json_encode($arrDevolver);exit;

                }else{
                    $arrExistencia[$claveUnica] = true;
                }
            }
	    $this->_db->addSelect(seq_datos_movimientos_id_mov);
	    $this->_db->generarProximo();
	    $resultado = $this->_db->ejecutar();

	    $arrParametros['idMovimiento'] = $resultado[0]['nextval'];
	
            
            $objFrenteProductos = new FrenteProductos();
            $objFrenteEstanteria = NEW FrenteEstanterias();


            if($arrParametros['tipo']=='entradas' ){
                $arrParametros['tipoMovimientoId'] = 1 ;
                if($arrParametros['idSucursal']==null){
                    $arrParametros['idSucursal'] = $_SESSION['sucursalId'];
                }
                $arrLineas = explode ("||",$arrParametros[productos]);
                array_pop($arrLineas);
                
                foreach ($arrLineas AS $linea){
		    
                    list( $productoId,$cantidad, $productoPcosto,$vencimiento) = explode("#",$linea);
                    
                    
                    //lotes
                    $objFrenteLotes = NEW FrenteLotes();
                    $lote=1;
                    //$vencimiento='31-12-2040';
                    $objFrenteLotes->validarLote($lote, $productoId, $vencimiento,$arrParametros['idSucursal']);
                    
                   
                    //aca se llena el array de detalles del movimiento
                    $arrDetalle[] = Array(
                                        'tipo_movimiento_id' => $arrParametros['tipoMovimientoId'],
                                        'id_movimiento' => $arrParametros['idMovimiento'],
                                        'sucursal_id' => $arrParametros['idSucursal'],
                                        'almacen_id' => null,
                                        'estanteria_id' => null,
                                        'producto_id' => $productoId,
                                        'lote_id'=> $objFrenteLotes->getUltimoLoteId(),
                                        'cantidad' => $cantidad,
                                        'unidades' => $cantidad,
                                        'costo'=>$productoPcosto
                                        );
                    $importeEntrada = $importeEntrada + ($cantidad*$productoPcosto);

                }
                
                $arrDato[] = Array(
                                'id_movimiento' => $arrParametros['idMovimiento'],
                                'prove_id' => $arrParametros['proveedores'] ,
                                'tipo_movimiento_id' => $arrParametros['tipoMovimientoId']  ,
                                'despacho_nro' => $arrParametros['despacho'] ,
                                'factura_nro' => $arrParametros['facturas'],
                                'remito_nro' => $arrParametros['remitos'],
                                'nro_tramite_anmat' => $arrParametros['nroTramite'],
                                'arr_parametros_detalle' => $arrDetalle,
                                'movimiento_usuario_id' => $_SESSION['usuarioId'],
                                'observaciones' =>	$arrParametros['observaciones'],
                                'tipo_comprobante' =>	$arrParametros['tipoComprobante'],
                                'importeEntrada' =>     $importeEntrada
                                
                                );
                
            }else{
                switch ($arrParametros['tipoMovimientoId']){
                    case 6:
                        $qryChequeo="SELECT orden_cobro_nro FROM datos_cobros_facturas INNER JOIN datos_cobros USING(cobro_id) INNER JOIN datos_facturas USING(factura_id) WHERE id_movimiento =".$arrParametros['id']." AND cobro_cancelado=false LIMIT 1;";
                        
                        $this->_db->setQry($qryChequeo);
                        $resultado = $this->_db->ejecutar();
                        
                        if(count($resultado)>0){
                            $arrDevolver = array(
                                        "soyError" => true,
                                        "nivel" => 0,
                                        "mensaje" => "Imposible anular factura, imputada a orden: ".$resultado[0][orden_cobro_nro].""
                                        );
                
                            echo json_encode($arrDevolver);exit;                                               
                        }

                        $objCabecera = new DatoMovimientoExtendido();
                        $objCabecera->cargarme($arrParametros['id']);
                        $arrDetalle[]= Array(   'tipo_movimiento_id' => $arrParametros['tipoMovimientoId'],
                                                'codigos'=>Array(),
                                                'de_id_movimiento' => $objCabecera->getIdMovimiento(),
                                                'id_movimiento' => $arrParametros['idMovimiento']
                                            );
                        $arrDato[] =
                            Array(
                            'control' => 1, // false pasa por el control de cliente_id que no puede ser nulo;
                            'id_movimiento' => $arrParametros['idMovimiento']   ,    
                            'tipo_movimiento_id' => $arrParametros['tipoMovimientoId'] ,
                            'movimiento_usuario_id' => $_SESSION['usuarioId'],
                            'factura_nro' => $objCabecera->getFacturaNro(),
                            'remito_nro' => $objCabecera->getRemitoNro(),
                            'de_id_movimiento' => $objCabecera->getIdMovimiento(),
                            'cliente_id' => $objCabecera->getClienteId(),
                            'relacion_id' => $objCabecera->getRelacionId(),
                            'idEstado' => 28,
                            
                        ); 
                        //var_dump($arrDato);exit;
                        break;
                        case 3:
                                $arrLineas = explode ("||",$arrParametros[productos]);
                                array_pop($arrLineas);
                
                                foreach ($arrLineas AS $linea){
		    
                                    list( $productoId,$cantidad) = explode("#",$linea);

                                    $qryDev="SELECT producto_nombre, sum(cantidad) AS cantidad, df.cliente_id, df.id_movimiento, importe_unitario, df.id_estado, dm.tipo_movimiento_id, dc.rentabilidad FROM datos_facturas df INNER JOIN detalle_facturas USING(factura_id) INNER JOIN datos_productos USING(producto_id) INNER JOIN datos_movimientos dm  USING (id_movimiento) INNER JOIN datos_clientes dc ON df.cliente_id = dc.cliente_id WHERE producto_id = $productoId AND df.factura_id='$arrParametros[facturas]' AND df.cliente_id = $arrParametros[clientes] AND factura_cancelada=false AND df.id_estado<>25 GROUP BY producto_nombre, df.cliente_id, df.id_movimiento, importe_unitario, df.id_estado,dm.tipo_movimiento_id,dc.rentabilidad;";
				    //echo $qryDev;exit;

                                    $this->_db->setQry($qryDev);
                                    $resultado = $this->_db->ejecutar();
                        
                                    if(count($resultado)==0){
                                        $arrDevolver = array(
                                                    "soyError" => true,
                                                    "nivel" => 0,
                                                    "mensaje" => "Valide que los productos seleccionados existan en el remito de origen"
                                                    );
                            
                                        echo json_encode($arrDevolver);exit;                                               
                                    }
                                    
                                    if($cantidad > $resultado[0]['cantidad']){
                                        
                                        $arrDevolver = array(
                                                    "soyError" => true,
                                                    "nivel" => 0,
                                                    "mensaje" => "La cantidad a devolver $cantidad del producto ".$resultado[0][producto_nombre]. "es mayor a la cantidad del remito ". $resultado[0][cantidad].""
                                                    );
                                        echo json_encode($arrDevolver);exit;
                                    
                                    }
                                    if($resultado[0]['id_estado']!=24 && $resultado[0]['tipo_movimiento_id']==2 && $resultado[0]['rentabilidad']==true){
                                        $arrDevolver = array(
                                                    "soyError" => true,
                                                    "nivel" => 0,
                                                    "mensaje" => "Valide que el remito este en condiciones para realizar la devolucion"
                                                    );
                                        echo json_encode($arrDevolver);exit;
                                    }
                                    $qryLotes="SELECT dem.lote_id, abs(cantidad) AS cantidad  FROM detalle_movimientos dem INNER JOIN datos_lotes USING(lote_id) WHERE id_movimiento={$resultado[0][id_movimiento]} AND dem.producto_id = $productoId ORDER BY lote_vencimiento;";
                                    //echo $qryLotes;exit;
                                    $this->_db->setQry($qryLotes);
                                    $resultadoLotes = $this->_db->ejecutar();
                                    foreach($resultadoLotes AS $lotes){
                                        if($cantidad<=$lotes['cantidad'] && $cantidad>0){
                                            $arrDetalle[] = Array(
                                                'tipo_movimiento_id' => $arrParametros['tipoMovimientoId'],
                                                'id_movimiento' => $arrParametros['idMovimiento'],
                                                'sucursal_id' => null,
                                                'almacen_id' => null,
                                                'estanteria_id' => null,
                                                'producto_id' => $productoId,
                                                'lote_id'=> $lotes['lote_id'],
                                                'cantidad' => $cantidad,
                                                'unidades' => $cantidad,
                                                'factura' => $arrParametros['facturas'],
                                                'de_id_movimiento' =>$resultado[0]['id_movimiento'],
                                                'producto_pventa' =>$resultado[0]['importe_unitario']
                                                );
                                            //var_dump($arrDetalle);exit;
                                            $importeDevolucion = $importeDevolucion + ($cantidad*$resultado[0]['importe_unitario']);
                                            $cantidad=0;

                                        }elseif($cantidad>0){
                                            $cantidad =  $cantidad - $lotes['cantidad'];
                                            $arrDetalle[] = Array(
                                                'tipo_movimiento_id' => $arrParametros['tipoMovimientoId'],
                                                'id_movimiento' => $arrParametros['idMovimiento'],
                                                'sucursal_id' => null,
                                                'almacen_id' => null,
                                                'estanteria_id' => null,
                                                'producto_id' => $productoId,
                                                'lote_id'=> $lotes['lote_id'],
                                                'cantidad' => $lotes['cantidad'],
                                                'unidades' => $lotes['cantidad'],
                                                'factura' => $arrParametros['facturas'],
                                                'de_id_movimiento' =>$resultado[0]['id_movimiento'],
                                                'producto_pventa' =>$resultado[0]['importe_unitario']
                                                );
                                            $importeDevolucion = $importeDevolucion + ($lotes['cantidad']*$resultado[0]['importe_unitario']);
                                        }
                                    }
                                }
                                $arrDato[] = Array(
                                'id_movimiento' => $arrParametros['idMovimiento'],
                                'cliente_id' => $arrParametros['clientes'] ,
                                'prove_id' => 2 ,
                                'tipo_movimiento_id' => $arrParametros['tipoMovimientoId']  ,
                                'factura_nro' => $arrParametros['facturas'],
                                'remito_nro' => $arrParametros['remitos'],
                                'arr_parametros_detalle' => $arrDetalle,
                                'movimiento_usuario_id' => $_SESSION['usuarioId'],
                                'observaciones' =>	$arrParametros['observaciones'],
                                'de_id_movimiento' =>$resultado[0]['id_movimiento'],
                                'importeEntrada' =>     $importeDevolucion
                                );
                            break;
                    case 17:
                        $arrProductos = explode ("||",$arrParametros['productos']);
                        array_pop($arrProductos);
                        
                        foreach ($arrProductos AS $producto){
                            list($productoId,$costo, $precio, $cantidad) = explode("#", $producto);
                            $lote=1;
                            $arrProductosFinal[] = Array(
                            'cantidad' => $cantidad ,
                            'lote' =>  $lote,
                            'costo' =>  $costo,
                            'precio' =>  $precio,
                            'lote' =>  $lote,
                            'productoId' =>  $productoId
                            );
                        }
                            $arrParametros['cliente'] = $arrParametros['clientes'];
                            $arrDetalle[] = Array(  'tipo_movimiento_id' => $arrParametros['tipoMovimientoId'],
                                'productos' => $arrProductosFinal,
                                'id_movimiento' => $arrParametros['idMovimiento'],
                                'clienteId'=> $arrParametros['cliente'],
                            );
                        $objCliente = NEW ClienteExtendido();
                            $objCliente ->cargarMe($arrParametros['cliente']);                           
                        $arrDato[] =
                                Array(
                                    'id_movimiento' => $arrParametros['idMovimiento'],
                                    'tipo_movimiento_id' => $arrParametros['tipoMovimientoId'],
                                    'cliente_id' => $arrParametros['cliente'],
                                    'relacion_id'=> $arrParametros['relaciones'],
                                    'guia_id'=> $arrParametros['guiaId'],
                                    'paciente_id' => $arrParametros['pacientes'],
                                    'vendedor_id' => $objCliente->getVendedorId(),
                                    'idLista' => 10,
                                    'movimiento_usuario_id' => $_SESSION['usuarioId'],
                                    'factura_nro' => $arrParametros['facturas'],
                                    'remito_nro' => $arrParametros['remitos'],
                                    'nro_orden_compra' => $arrParametros['nroOrden'],
                                    'nro_nota_carga' => $arrParametros['nroNotaCarga'],
                                    'pedido_id' => $arrParametros['pedidoId'],
                                    'descuento' => $arrParametros['descuento'],
                                    'domicilio_entrega' => $arrParametros['domicilioEntrega'],
                                    'paciente_id' => $arrParametros['pacienteId'],
                                    'observaciones' =>	$arrParametros['observaciones'],
                                    'idEstado' => 25//para reventa que siempre sea como cobrado
                                );
                    
                    break;
                    default :
                        if($arrParametros['tipoMovimientoId'] == NULL){
                            $arrParametros['tipoMovimientoId'] = 2;
                        }
                        
                        $arrProductos = explode ("||",$arrParametros['productos']);
                        array_pop($arrProductos);
                        
                        foreach ($arrProductos AS $producto){
				list($productoId, $cantidad, $subfamiliaId) = explode("#", $producto);
				if($cantidad==0){
                                $arrDevolver = array(
                                            "soyError" => true,
                                            "nivel" => 0,
                                            "mensaje" => "Cantidad debes de ser mayor a 0"
                                            );

                                echo json_encode($arrDevolver);exit;
                            }
                            $lote=1;
                            $arrProductosFinal[] = Array(
                            'cantidad' => $cantidad ,
                            'lote' =>  $lote,
                            'productoId' =>  $productoId,
                            'subfamiliaId' =>  $subfamiliaId
                            );
                        }
                        //echo $arrParametros['clientes'];
                            $arrParametros['cliente'] = $arrParametros['clientes'];
                            $arrDetalle[] = Array(  'tipo_movimiento_id' => $arrParametros['tipoMovimientoId'],
                                'productos' => $arrProductosFinal,
                                'id_movimiento' => $arrParametros['idMovimiento'],
                                'clienteId'=> $arrParametros['cliente'],
                                'idLista' => $arrParametros['idLista'],

                            );

                        if($arrParametros['renovacionId'] == NULL){
                            
                            $objCliente = NEW ClienteExtendido();
                            $objCliente ->cargarMe($arrParametros['cliente']);                           
                            
                            if($arrParametros['cliente'] ==2 || $arrParametros['cliente']==1 || $arrParametros['cliente']==3 || $arrParametros['cliente']==491 || $arrParametros['cliente']==3027 || $arrParametros['cliente']==5371 || $arrParametros['cliente']==5591){
                                if($objCliente->getAIdSucursal()>0){
                                    $aIdSucursal = $objCliente->getAIdSucursal();
                                    $traspasarMovimiento=true;
                                }
                                $idEstado=25;
                            }else{
                                $idEstado=24;
                            }
                            //echo $idEstado;exit;
                            
                            $arrDato[] =
                                Array(
                                    'id_movimiento' => $arrParametros['idMovimiento'],
                                    'tipo_movimiento_id' => $arrParametros['tipoMovimientoId'],
                                    'cliente_id' => $arrParametros['cliente'],
                                    'idLista' => $arrParametros['idLista'],
                                    'relacion_id'=> $arrParametros['relaciones'],
                                    'tipoEntrega'=> $arrParametros['tipoEntrega'],
                                    'paciente_id' => $arrParametros['pacientes'],
                                    'vendedor_id' => $objCliente->getVendedorId(),
                                    'obra_social_id' => $arrParametros['obraSocial'] ,
                                    'movimiento_usuario_id' => $_SESSION['usuarioId'],
                                    'factura_nro' => $arrParametros['facturas'],
                                    'remito_nro' => $arrParametros['remitos'],
                                    'nro_orden_compra' => $arrParametros['nroOrden'],
                                    'nro_nota_carga' => $arrParametros['nroNotaCarga'],
                                    'pedido_id' => $arrParametros['pedidoId'],
                                    'descuento' => $arrParametros['descuento'],
                                    'domicilio_entrega' => $arrParametros['domicilioEntrega'],
                                    'paciente_id' => $arrParametros['pacienteId'],
                                    'observaciones' =>	$arrParametros['observaciones'],
                                    'idEstado' => $idEstado,//pedido preparado.
                                    'de_id_movimiento'=>$arrParametros['de_id_movimiento'],
                                    'de_sucursal_id'=>$_SESSION['sucursalId'],
                                    'a_sucursal_id'=>$aIdSucursal,
                                    'traspasar'=>$traspasarMovimiento

                                );
                        } else {
                            $arrDato = array();
                        }                      
                    }
                }
            //salvamos
           //var_dump($arrDato);exit;
            foreach($arrDato as $parametros){
                $objDato = new DatoMovimientoExtendido();
                $arrQry['dato'] = $arrQry['dato'] . $objDato->salvarme($parametros);
                if(count($objDato->getArrError()) > 0){
                    $this->_objFrenteError->generarError($objDato->getArrError());
                }
                if($parametros['guia_id']!=NULL){
                    $arrQry['guia']="UPDATE datos_guias SET guia_en_remito=true WHERE guia_id=$parametros[guia_id];";
                }
                $this->setColObjDatoMovimiento($objDato);
            }
	    foreach ($arrDetalle AS $detalle){
		
                //** SI SE CREAN NUEVOS MOVIMIENTOS , HAY QUE CREAR UN OBJ PARA EL MOVIMIENTO Y PONER UN CASE CON EL ID. **//
                //dependiendo que movimiento es instanciamos el objeto 
                switch ($detalle['tipo_movimiento_id']){
                    case 1:
                        $objDetalle = new DetalleMovimientoRecepcionExtendido();
                        $arrQry['detalle'] .= $objDetalle->salvarme($detalle);
                        $objDetalle->idTipoMovimiento = $detalle['tipo_movimiento_id'];
                        break;
                    case 2:
                        $objDetalle = new DetalleMovimientoSAlidaExtendido();
                        $arrQry['detalle'] .= $objDetalle->salvarme($detalle);
                        $objDetalle->idTipoMovimiento = $detalle['tipo_movimiento_id'];
                        break;
                    case 3:
                        $objDetalle = new DetalleMovimientoDevolucionExtendido();
                        $arrQry['detalle'] .= $objDetalle->salvarme($detalle);
                        $objDetalle->idTipoMovimiento = $detalle['tipo_movimiento_id'];
                        break;
                    case 6:
                        $objDetalle = new DetalleMovimientoAnulacionExtendido();
                        $arrQry['detalle'] .= $objDetalle->salvarMe($detalle);
                        $objDetalle->idTipoMovimiento = $detalle['tipo_movimiento_id'];
                        break;
                    case 17:
                        $objDetalle = new DetalleMovimientoReventaExtendido();
                        $arrQry['detalle'] .= $objDetalle->salvarme($detalle);
                        $objDetalle->idTipoMovimiento = $detalle['tipo_movimiento_id'];
                        break;
                    default : 
                        $arrError['detalle'] = 'El movimiento que intenta realizar no fue vinculado con la clase que lo ejecuta(salvarMe)';
                        $arrError['archivo'] = __FILE__;
                        $this->_objFrenteError->generarError($arrError);
                        break;                    
                }
                //set de la coleccion de detalles
                if(count($objDetalle->getArrError()) > 0){
                    $this->_objFrenteError->generarError($objDetalle->getArrError());
                }
                //var_dump($arrQry['detalle']); exit;
                $this->setColObjDetalleMovimiento($objDetalle);
            }
            
            if(count($this->getColObjDetalleMovimiento()) == 0 ){                        
                $arrError['detalle'] = 'No existen detalles para generar movimientos';
                $arrError['archivo'] = __FILE__;
                $this->_objFrenteError->generarError($arrError);
            }
            //si es salida chequeamos si hay promo
            If($arrDato['0']['idLista']==10){
                $indice =1;
            }else{
                $indice =1.07;
            }
            if($arrParametros['tipoMovimientoId']==2){
                $arrQry['promos'] = ' SELECT fn_calcular_promociones('. $arrDato['0']['id_movimiento']  . ',' .  $indice . '); ';
            
            }
            if($arrParametros['tipoMovimientoId']==2){
                $arrQry['promos'].= ' SELECT fn_calcular_promociones_productos('. $arrDato['0']['id_movimiento'] . ',' .  $indice . '); ';
            
            }
            //si hay devolucion actualizamos el remito de origen
            if($arrParametros['tipoMovimientoId']==3){
                $arrQry['totales'] = ' SELECT fn_actualizar_totales_por_devolucion('. $arrDato['0']['de_id_movimiento'] . ',' .  $arrDato['0']['factura_nro'] . '); ';
            
	    }
	    //NEGRADA PARA EL PAPA DE LUCAS 8/10/2025
            if($arrParametros['cliente']==476 && $arrParametros['tipoMovimientoId']==2){
                $arrQry['padre'].= ' SELECT fn_calcular_promociones_papa_lucas('. $arrDato['0']['id_movimiento'] . ',' .  $indice . '); ';
            }
            if(count($this->_objFrenteError->getColObjError()) > 0 ){
                $this->_objFrenteError->cargarError();
            }else{
                //var_dump($this->_db->getArrError());exit;
                /*var_dump($arrQry['detalle']);
                var_dump($arrQry['dato']);
                var_dump($arrQry['trazas']);
                var_dump($arrQry['pedido']);
                var_dump($arrQry['log']);exit;*/
                $qryFinal = $arrParametros['anularPorEdicion'] . $arrQry['detalle'] . $arrQry['promos'] . $arrQry['padre'] . $arrQry['dato'] . $arrQry['totales'] . $arrParametros['qryLiberacion'];
                /*
                    $arrDevolver = array(
                                            "soyError" => true,
                                            "nivel" => 0,
                                            "alertados" => $arrAlertador,
                                            "mensaje" => "$qryFinal"
                                 );
                    echo json_encode($arrDevolver);exit;
                */
                 //echo $qryFinal; exit;
                 
                $this->_db->setQry($qryFinal);
                $this->_db->ejecutarTransaccion();
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
                                        "mensaje" => "Movimiento Generado correctamente!"
                                        );
                }  
                echo json_encode($arrDevolver);exit;
            }
        } catch (Exception $exc) {
            $exc->getTraceAsString();
            $arrError['detalle'] = $exc->getTraceAsString();
            $arrError['archivo'] = __FILE__;
            $this->_objFrenteError->generarError($arrError);
            $this->_objFrenteError->cargarError();
        }
    }
    
    public function cargarMovimiento($id){
        $objMovimiento = new DatoMovimientoExtendido();
        $objMovimiento->cargarme($id);
        $this->setColObjDatoMovimiento($objMovimiento);
    }
    

    public function busqueda ($arrParametros){
        $this->_db->addSelect('id_movimiento');
        $this->_db->addFrom('datos_movimientos dm');
        $this->_db->generarSelect();
        
        $resultado = $this->_db->ejecutar();
        foreach($resultado as $rstdo){
            $this->cargarMovimiento($rstdo['id_movimiento']);
        }
    }
    public function buscarMovimientoPorCodigo($arrParametros){
    
	//SETEO EL PAGINADOR, NUNCA PUEDE HABER MAS DE 1
	$primerRegistro = 0;
	$ultimoRegistro = 11;
	$this->_db->addSelect('trazabilidad_id');
	$this->_db->addFrom('datos_movimientos dm');
	$this->_db->addFrom('INNER JOIN movimientos_trazabilidad USING(id_movimiento)');
	$this->_db->addFrom('INNER JOIN datos_trazabilidad USING(trazabilidad_id)');
	$this->_db->addWhere('trazabilidad_codigo = \'' . $arrParametros[codigoTrazabilidad] . '\'');
	$this->_db->addWhere('dm.tipo_movimiento_id = 1');
	$this->setOrdenMovimientos($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	$this->_db->addGroup('trazabilidad_id, dm.id_movimiento');
	
	$this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
    
	$arr =  $this->_db->ejecutar();
	
	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro){
	    $ultimoRegistro = $this->getTotal();
	}	
	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
	    $this->cargarCodigoTrazabilidad($arr[$i]['trazabilidad_id']);
	}
    
    }

    public function cargarLosDatosLazzy($arrIds){
    
	if(is_array($arrIds)){
	    foreach($arrIds as $id){
		$ids.= $id['id_movimiento'] . ",";
	   }
		$ids=substr($ids, 0, -1);
	}else{
		$ids=$arrIds;
	}
	
        $this->_db->addSelect("*, id_estado AS estado_id, CASE WHEN lista_id = 10 THEN 'Efectivo' ELSE 'Tarjeta' END AS forma_pago");
        $this->_db->addSelect('to_char(movimiento_fecha_hora,\'DD-MM-YYYY HH24:MI\') AS movimiento_fecha_hora'); 
	    $this->_db->addFrom('datos_movimientos');
	    $this->_db->addFrom('INNER JOIN datos_usuarios ON datos_movimientos.movimiento_usuario_id = datos_usuarios.usuario_id');
	    //if($arrParametros['tipo']=='salidas'){        
	    //}
        $this->_db->addWhere("id_movimiento IN (" . $ids . ")" );
        //$this->_db->addWhere("sucursal_id={$_SESSION['sucursalId']} ");
        $this->_db->addOrderBy('id_movimiento DESC');
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        if(count($resultado)==0){
        return;
        }
        
	$FrenteProveedores = NEW FrenteProveedores();
	$FrenteProveedores->cargarProveedoresLazzy($resultado);
	$arrObjProveedores= $FrenteProveedores->getColObjProveedor();
	
	$FrenteClientes = NEW FrenteClientes();
	$FrenteClientes->cargarClientesLazzy($resultado);
	$arrObjClientes= $FrenteClientes->getColObjCliente();	
	
	$FrenteEstados = NEW FrenteEstados();
	$FrenteEstados->cargarEstadosLazzy($resultado);
	$arrObjEstados= $FrenteEstados->getColObjEstados();
	
	
        foreach($resultado AS $datos){
       	    
	    $objDatoMovimiento= NEW DatoMovimientoExtendido();
	    $objDatoMovimiento->cargarmeNew($datos);
	    $objDatoMovimiento->setObjProve($arrObjProveedores[$datos[prove_id]]);
	    $objDatoMovimiento->setObjCliente($arrObjClientes[$datos[cliente_id]]);
	    $objDatoMovimiento->setObjEstado($arrObjEstados[$datos[estado_id]]);
        //$objDatoMovimiento->setObjEstados($arrObjEstados[$datos[estado_id]]);
	    
            $this->setColObjDatoMovimiento($objDatoMovimiento);
        }
    
    }
    
    
    public function buscarMovimiento ($arrParametros){


	$this->_db->addSelect('id_movimiento');
	$this->_db->addFrom('datos_movimientos dm');
	$this->_db->addFrom('INNER JOIN detalle_movimientos USING(id_movimiento)');
	$this->_db->addFrom('INNER JOIN datos_productos USING(producto_id)');
	$this->_db->addFrom('INNER JOIN datos_marcas USING(marca_id)');
	$this->_db->addFrom('LEFT JOIN datos_proveedores ON dm.prove_id = datos_proveedores.prove_id');
	$this->_db->addFrom('LEFT JOIN datos_clientes ON  dm.cliente_id = datos_clientes.cliente_id');
	if($arrParametros['pedidos']!=null){
	    $this->_db->addFrom('INNER JOIN datos_pedidos USING(pedido_id)');
	}
	if($arrParametros['pacientes']!=null){
	    $this->_db->addFrom('INNER JOIN datos_guias USING(guia_id)');
	}
	$this->_db->addFrom('INNER JOIN tipos_movimientos_trazas tmt ON(dm.tipo_movimiento_id = tmt.tipo_movimiento_id)');
	//**ANALIZO EL WHERE**//
	if($arrParametros['stringBuscar']){
	    $this->_db->addWhere('id_movimiento ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
	}
    //echo $this->_objFuncionesComunes->parsearAutocompletar($arrParametros['clientes']);
	if($arrParametros['clientes'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('dm.cliente_id = \'' . $arrParametros['clientes'] . '\'');
	}

	if($arrParametros['categorias'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('datos_clientes.categoria_id = \'' . $arrParametros['categorias'] . '\'');
	}
	if($arrParametros['obraSocial'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('obra_social_id = \'' . $arrParametros['obraSocial'] . '\'');
	}

	if($arrParametros['vendedoresClientes'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('dm.vendedor_id = \'' . $arrParametros['vendedoresClientes'] . '\'');
	}
	if($arrParametros['pacientes'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('datos_guias.paciente_id = \'' . $arrParametros['pacientes'] . '\'');
	}
	if($arrParametros['pm'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('pm  ILIKE \'%' . $arrParametros[pm] . '%\'');
	}
	if($arrParametros['despacho'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('despacho_nro  = \'' . $arrParametros[despacho] . '\'');
	}
	if($arrParametros['proveedores'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('dm.prove_id = \'' . $arrParametros['proveedores'] . '\'');
	}
	if($arrParametros['proveedoresProductos'] !=null && $arrParametros[desdeAlta]==null){

	    $this->_db->addWhere('datos_productos.prove_id = \'' . $arrParametros['proveedoresProductos'] . '\'');
	}
	if($arrParametros['marcas'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('datos_productos.marca_id = \'' . $arrParametros['marcas'] . '\'');
	}
	if($arrParametros['productos'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('detalle_movimientos.producto_id = \'' . $arrParametros['productos'] . '\'');
	}
	if($arrParametros['familias'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('datos_productos.familia_id = \'' . $arrParametros['familias'] . '\'');
	}
	if($arrParametros['estanterias'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('estanteria_id = \'' . $arrParametros['estanterias'] . '\'');
	}
	if($arrParametros['remitos'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('remito_nro = \'' . $arrParametros[remitos] . '\'');
	}
	if($arrParametros['facturas'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('factura_nro = \'' . $arrParametros[facturas] . '\'');
	}
	if($arrParametros['nroTramite'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('nro_tramite_anmat = \'' . $arrParametros[nroTramite] . '\'');
	}
	if($arrParametros['nroOrden'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('orden_compra = \'' . $arrParametros[nroOrden] . '\'');
	}
	if($arrParametros['nroNotaCarga'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('nro_nota_carga = \'' . $arrParametros[nroNotaCarga] . '\'');
	}
	if($arrParametros['estadosDos'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('id_estado = \'' . $arrParametros[estadosDos] . '\'');
	}
	if($arrParametros['codigoTrazabilidad'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('trazabilidad_codigo = \'' . $arrParametros[codigoTrazabilidad] . '\'');
	}
	if($arrParametros['codigoReferencia'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('codigo_referencia = \'' . $arrParametros[codigoReferencia] . '\'');
	}
	if($arrParametros['valor'] !=null ){
            $this->_db->addWhere('codigo_referencia = \'' . $arrParametros['valor'] . '\' ');
        }
	if($arrParametros['tipoMovimiento'] !=null && $arrParametros[desdeAlta]==null)	{
	    $this->_db->addWhere('tipo_movimiento_id = \'' . $arrParametros[tipoMovimiento] . '\'');
	}
	if($arrParametros['fechaDesde'] !=null && $arrParametros[desdeAlta]==null){
            $ano= substr($arrParametros['fechaDesde'],6,4);
	    $mes = substr($arrParametros['fechaDesde'],3,2);
	    $dia = substr($arrParametros['fechaDesde'],0,2);
	    $fechaDesde = $ano."/".$mes ."/".$dia;
	    $this->_db->addWhere('movimiento_fecha_hora::date >= \'' . $fechaDesde . '\'');
	}
	if($arrParametros['fechaHasta'] !=null && $arrParametros[desdeAlta]==null){
	    $ano= substr($arrParametros['fechaHasta'],6,4);
	    $mes = substr($arrParametros['fechaHasta'],3,2);
	    $dia = substr($arrParametros['fechaHasta'],0,2);
	    $fechaHasta = $ano."/".$mes ."/".$dia;
            $this->_db->addWhere('movimiento_fecha_hora::date <= \'' . $fechaHasta . '\'');
	}
    
	if($arrParametros['tipo']=='entradas' || $arrParametros['tipo']=='entradasCodigos' || $arrParametros['tipo']=='busquedaPorCodigo'){

	    $this->_db->addWhere('tmt.tipo_movimiento_alta = TRUE ');
	}
	if($arrParametros['tipo']=='salidas'){

	    $this->_db->addWhere('tmt.tipo_movimiento_alta = FALSE ');
	    $this->_db->addWhere('dm.cliente_id is not null');

	}
	$this->_db->addWhere('anulado = false ');
    $this->_db->addWhere("detalle_movimientos.sucursal_id={$_SESSION['sucursalId']}");
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenMovimientos($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['desdeBusqueda']==false){
	    $this->_db->addLimit(200);
	}	
	$this->_db->addGroup('id_movimiento');
	//*FIN LIMIT*//
	$this->_db->generarSelect();
	//echo $this->_db->getQry();//exit;
    
	$arr =  $this->_db->ejecutar();
	
	//ahora seteamos el total y la pagina
	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro){
	    $ultimoRegistro = $this->getTotal();
	}	
    for($i=$primerRegistro;$i<$ultimoRegistro;$i++)	{     
	    $arrIds[$i]['id_movimiento']= $arr[$i]['id_movimiento'];
	}
	//var_dump($arrIds);exit;
	$this->cargarLosDatosLazzy($arrIds);
    }

    public function getError(){
        return $this->_arrError;
    }

    //**PARA EL FRENTE**//
    public function setOrdenMovimientos($parametros){

	if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){
	//echo "entra";
	    $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'id_movimiento';
	    $this->_ordenColumnas['ordenarOrden']  =   'desc';
	}
    }

    public function getOrdenMovimientos(){

        return $this->_ordenColumnas;
    }

    public function setTotal($total){

        $this->_total = $total;
    }
    public function getTotal(){

        return $this->_total;
    }
    //pagina 1 por defecto amigo
    public function setPagina($pagina=1){

        $this->_pagina = $pagina;
    }
    public function getPagina(){

    	return $this->_pagina;
    }

    public function getDetalles() {
    
    
        foreach($this->_colObjDatoMovimiento AS $objMovimiento){
		
	    if($objMovimiento->getObjTipoMovimiento()->getTipoMovimientoALta() == TRUE){

		if($objMovimiento->getObjTipoMovimiento()->getTipoMovimientoId()==5 || $_SESSION['usuarioId']==10){
		    $cancelable=false;
		}else{
		    $cancelable=true;
		}
		if($_SESSION['usuarioId']==1 || $_SESSION['usuarioId']==3){
            $editable=true;
        }else{
            $editable=false;
        }

                $arr []   =    array
                    (
                    //id si o si un solo string (sin espacios en blanco)
                    "id"            =>  $objMovimiento->getIdMovimiento(),
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  $cancelable,
                        "editable"      =>  $editable,
                        "detalles"      =>  true,
                        "imprimir"      =>  false,
                        "mostradorEntradas"      =>  $editable

                    ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell"          => array(
                        $objMovimiento->getIdMovimiento(),
                        $objMovimiento->getMovimientoFechaHora(),
                        $objMovimiento->getObjProve()->getProveNombre(),
                        $objMovimiento->getTipoComprobante(),
                        $objMovimiento->getRemitoNro(),
                        $this->_objFuncionesComunes->formatoMoneda($objMovimiento->getImporteMovimiento()),
                        $objMovimiento->getObjTipoMovimiento()->getTipoMovimientoNombre(),
                        $objMovimiento->getObs(),
                        $objMovimiento->getUsuarioNombre(),
			),
                    );
            }else{
		    if($objMovimiento->getObjTipoMovimiento()->getTipoMovimientoId()==2 || $objMovimiento->getObjTipoMovimiento()->getTipoMovimientoId()==17 ){
                if($objMovimiento->getIdEstado()==25 || $objMovimiento->getIdEstado()==30 || $objMovimiento->getIdEstado()==31){
                    $mostrador=false;
                    $cancelable=false;
                }else{
                    $mostrador=true;
                    $cancelable=true;
                }
        
                    
                    $imprimir=true;
                }else{
                    $imprimir=false;
                    $cancelable=false;
                    
                    
                }
            if($objMovimiento->getObjTipoMovimiento()->getTipoMovimientoId()==6){
                $editar=true;
                $mostrador=false;
            }else{
                $editar=false;
	    }
	    if($_SESSION['usuarioId']==1 || $_SESSION['usuarioId']==3){
                $cancelable=true;
	    }
	    $pendiente=$this->traerImportePendiente($objMovimiento->getIdMovimiento());
            
                 $arr []   =    array
                    (
                    //id si o si un solo string (sin espacios en blanco)
                    "id"            =>  $objMovimiento->getIdMovimiento(),
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  $cancelable,
                        "editable"      =>  $editar,
                        "detalles"      =>  true,
                        "autogestion"      =>  false,
                        "imprimir"      =>  $imprimir,
                        "mostrador"      =>  $mostrador,

                    ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell"          => array(
                        $objMovimiento->getIdMovimiento(),
                        $objMovimiento->getMovimientoFechaHora(),
                        $objMovimiento->getObjCliente()->getClienteNombre(),
                        $objMovimiento->getFormaPago(),
                        $objMovimiento->getTipoEntrega(),
                        $objMovimiento->getRemitoNro(),
                        $objMovimiento->getObjEstado()->getEstadoNombre(),
			$this->_objFuncionesComunes->formatoMoneda($objMovimiento->getImporteMovimiento()),
			$this->_objFuncionesComunes->formatoMoneda($pendiente),
                        $objMovimiento->getObs(),
                        $objMovimiento->getObjTipoMovimiento()->getTipoMovimientoNombre()
                    ),
                 );
            }
        }		
        return $arr;
    }

    //**PARA EL FRENTE FIN**//

    public function armarDetalles($id){

	$this->cargarMovimiento($id);
	
	foreach ($this->getColObjDatoMovimiento() AS $objMovimiento){
	
	/*echo "<pre>";
	print_r($objMovimiento);
	echo "</pre>";*/
	if($objMovimiento->getObjTipoMovimiento()->getTipoMovimientoALta() == true){
            $entrada=true;
		    $propiedadesTransaccion =   array(
			array(

			    "display"   =>  'Nro',
			    "name"      =>  'id_movimiento',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objMovimiento->getIdMovimiento(),
			),
			array(
			    "display"   =>  'Proveedor',
			    "name"      =>  'prove_id',
			    "editable"  =>  false,
			    "class"	=> 'autocomplete',
			    "value"     =>  $objMovimiento->getObjProve()->getProveNombre(),

			),
			array(
			    "display"   =>  'Remito Nro',
			    "name"      =>  'tipo_movimiento_id',
			    "editable"  =>  false,
			    "class"	=>  '',
			    "value"     =>  $objMovimiento->getRemitoNro(),

			),
			array(
			    "display"   =>  'Fecha',
			    "name"      =>  'movimiento_fecha_hora',
			    "editable"  =>  false,
			    "class"	=>  'datePicker',
			    "value"     =>  $objMovimiento->getMovimientoFechaHora(),
			),
			array(
			    "display"   =>  'Observaciones',
			    "name"      =>  'movimiento_fecha_hora',
			    "editable"  =>  true,
			    "class"	=>  'datePicker',
			    "value"     =>  $objMovimiento->getObs(),
			)
		    );
		}else{
			$propiedadesTransaccion =   array(
			array(

			    "display"   =>  'Nro',
			    "name"      =>  'id_movimiento',
			    "editable"  =>  false,
			    "class"	=>  '',
			    "value"     =>  $objMovimiento->getIdMovimiento(),
			),
			array(
			    "display"   =>  'Cliente',
			    "name"      =>  'cliente_id',
			    "editable"  =>  true,
			    "class"	=>  'autocomplete',
			    "value"     =>  $objMovimiento->getObjCliente()->getClienteNombre(),

			),
			array(
			    "display"   =>  'Vendedor',
			    "name"      =>  'vendedor_id',
			    "editable"  =>  true,
			    "class"	=>  'autocomplete',
			    "value"     =>  $objMovimiento->getObjVendedor()->getVendedorNombre(),

			),
			array(
			    "display"   =>  'Remito',
			    "name"      =>  'tipo_movimiento_id',
			    "editable"  =>  false,
			    "class"	=>  '',
			    "value"     =>  $objMovimiento->getRemitoNro(),

			),
			array(
			    "display"   =>  'Fecha',
			    "name"      =>  'movimiento_fecha_hora',
			    "editable"  =>  true,
			    "class"	=>  'datePicker',
			    "value"     =>  $objMovimiento->getMovimientoFechaHora(),
			),
			array(
			    "display"   =>  'Observaciones',
			    "name"      =>  'movimiento_fecha_hora',
			    "editable"  =>  true,
			    "class"	=>  'datePicker',
			    "value"     =>  $objMovimiento->getObs(),
			)
		    );
		}
	}
	
	$this->_db->addSelect('sum(abs(dem.cantidad)) AS unidades');
	$this->_db->addSelect('dp.producto_nombre || \' \' || dp.producto_presentacion AS producto_nombre');
	$this->_db->addSelect('sum(detalle_total_fact) AS detalle_total_fact');
	$this->_db->addSelect('dem.producto_comision');
    $this->_db->addFrom('datos_movimientos dm');
	$this->_db->addFrom('INNER JOIN detalle_movimientos dem USING (id_movimiento)');
    if($entrada==true){
        $this->_db->addSelect("' - Vencimiento: ' || to_char(dl.lote_vencimiento,'DD-MM-YYYY') AS lote_vencimiento");
        $this->_db->addFrom('INNER JOIN datos_lotes dl ON dem.lote_id = dl.lote_id');
        $group=",dl.lote_vencimiento";
    }
	$this->_db->addFrom('INNER JOIN datos_productos dp ON dem.producto_id = dp.producto_id');
	$this->_db->addWhere('dm.id_movimiento = \'' . $id . '\'');
    $this->_db->addWhere('dem.detalle_anulado=false');
	$this->_db->addGroup('dp.producto_nombre, dp.producto_presentacion,  dem.producto_comision' . $group);
	$this->_db->addOrderBy('dp.producto_nombre');
	$this->_db->generarSelect();
    
	//echo $this->_db->getQry();
	$arrDetalles =  $this->_db->ejecutar();


	foreach($arrDetalles AS $detalle){
        if($detalle['tipo_movimiento_id']==1){
            $importe = $detalle['producto_costo'];
        }else{
            $importe=$detalle['detalle_total_fact'];
        }
            
            $arr []   =    array
		    (
			//id si o si un solo string (sin espacios en blanco)
			"id"            =>  $detalle[detalle_id],
			//define las herramientas
			"herramientas"   =>  array(
			 "editable"      =>  false,
			  ),
			 "bloqueado"     => false,	    
			//las celdas propiamente dichas, en el orden en que estan declaradas las columnas
			"cell"          => array(
			    $detalle[unidades],
			    $detalle[producto_nombre] . " " . $detalle[lote_vencimiento],
			     $this->_objFuncionesComunes->formatoMoneda($importe),
			),
		    );
	}
    
	$detallesTransaccion    =   array(
	    "modelo"    =>  array(
		array(

		    "display"   =>  'cantidad',
		    "name"      =>  'cantidad',
		    "class"	=>  '',
		    "editable"  =>  true,
		),
		array(
		    "display"   =>  'Producto',
		    "name"      =>  'producto_nombre',
		    "class"	=>  'autocomplete',
		    "editable"  =>  false,
		),
                array(
		    "display"   =>  'Importe',
		    "name"      =>  'producto_gtin',
		    "class"	=>  '',
		    "editable"  =>  false,
		),

	    ),
	    "celdas"    =>  $arr
	);
	
	$array_devolver =   array(
	    "propiedades"       =>  $propiedadesTransaccion,
	    "listadoDetalles"   =>  $detallesTransaccion,
	    "imprimir"		=>  true
	);
        
	    return $array_devolver;
    }
    
public function cargarCodigoTrazabilidad($arrParametros){
        
	if($_POST['tipo']!='remitos'){
            //parser del codigo estandar
            $arrTrazas = $this->_objFuncionesComunes->parsearCodigoTrazabilidadEstandar($arrParametros['codigo']);
            
            if($arrTrazas['gtin'] !=NULL){
                $this->_db->addSelect("dp.producto_nombre || '  ' || dp.producto_presentacion as desc");
                $this->_db->addSelect("dp.producto_id");                
                $this->_db->addFrom("datos_productos dp");
                $this->_db->addWhere('dp.producto_gtin = \'' . substr($arrTrazas['gtin'],1) . '\'');
                $this->_db->generarSelect();
                //echo $this->_db->getQry();
                $arrDatos =  $this->_db->ejecutar();	   
                
                
                $arrTrazas['producto_id'] = $arrDatos[0]['producto_id'];
                
                $arrParametros['desc'] = $arrDatos[0]['desc'];
                $arrParametros['codigoEstandar'] = implode("#", $arrTrazas);
                $arrParametros['codigo'] = "L:" . $arrTrazas['lote'] . " V:" . $arrTrazas['venc'] . " S:" . $arrTrazas['serie']  ;
            }
            
	}else{
	    $this->_db->addSelect("factura_id AS serie , cliente_nombre AS desc ");
	    $this->_db->addFrom('datos_facturas');
            $this->_db->addFrom('INNER JOIN datos_clientes USING(cliente_id)');
	    if(substr($arrParametros['codigo'], 0,3) == '+C0' || 
		substr($arrParametros['codigo'], 0,3) == '+c0' ){
                $arrParametros['codigo'] = substr($arrParametros['codigo'],3); 
            }
            $arrTrazas['serie'] = $arrParametros['codigo'];
            $this->_db->addWhere('factura_nro = \'' . $arrParametros['codigo'] . '\'');
            $this->_db->generarSelect();
            //echo $this->_db->getQry();exit;            
            $arrDatos =  $this->_db->ejecutar();	   
            $arrParametros['desc'] = $arrDatos[0]['desc'];
            
            $arrParametros['codigoEstandar'] = $arrDatos;
	}	 
        
        if(count($arrDatos) == 0 ){
	    //echo $codigo;
            $arrParametros['desc']="No existe";
        }
	
        $array_devolver = array(
                //'codigo'      => $arrDatos[0]['serie'],
                'codigo'      => $arrParametros['codigo'],
                'descripcion' => $arrParametros['desc'],
                'valor' => $arrParametros['codigoEstandar'],
                "soyError" => false,
                "nivel" => 0,
                "alertados" => $arrAlertador,
                "mensaje" => ""
                );
        
	//var_dump($array_devolver);
		
	echo json_encode($array_devolver);
    }

    
    public function getDatosUltimoMovimientoTraza($traza){
        
        $this->_db->addSelect('mt.id_movimiento AS de_id_movimiento');
        //$this->_db->addSelect('dt.trazabilidad_id');
        $this->_db->addSelect('dm.cliente_id');
        $this->_db->addSelect('dm.medico_id');
        $this->_db->addSelect('dm.paciente_id');
        $this->_db->addSelect('dm.obra_social_id');
        $this->_db->addSelect('dm.vendedor_id');
        $this->_db->addSelect('dm.distribuidor_id');
        $this->_db->addSelect('dm.remito_nro');
        $this->_db->addSelect('dm.movimiento_finalizado');
        $this->_db->addSelect('mt.finalizado');
        $this->_db->addSelect('mt.renovado');
        $this->_db->addSelect('dp.alquilable');
        $this->_db->addSelect('dm.fecha_vencimiento_alquiler');
        $this->_db->addSelect('dm.tipo_movimiento_id');
        $this->_db->addSelect('dt.en_stock');
        $this->_db->addSelect('dt.lote_id');
        $this->_db->addFrom('datos_trazabilidad dt');
        $this->_db->addFrom('INNER JOIN movimientos_trazabilidad mt USING(trazabilidad_id)');
        $this->_db->addFrom('INNER JOIN datos_productos dp USING(producto_id)');
        $this->_db->addFrom('INNER JOIN datos_movimientos dm USING(id_movimiento)');
        $this->_db->addWhere('trazabilidad_codigo IN (\'' . $traza . '\')');
        $this->_db->addGroup('mt.id_movimiento, dm.cliente_id, dm.medico_id, dm.paciente_id, dm.obra_social_id, dm.vendedor_id, dm.distribuidor_id, dm.remito_nro, dm.movimiento_finalizado, mt.finalizado, mt.renovado , dp.alquilable, dm.fecha_vencimiento_alquiler, dm.tipo_movimiento_id, dt.en_stock, dt.lote_id');
        $this->_db->addOrderBy('mt.id_movimiento DESC LIMIT 1');
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $arrDatos =  $this->_db->ejecutar();
        //var_dump($arrDatos);
        return $arrDatos[0]; 
    }
    public function actualizarObs($arrParametros){
    
        $qry="UPDATE datos_movimientos SET obs='{$arrParametros[campos][observaciones]}' WHERE id_movimiento = $arrParametros[id];";
        $this->_db->setQry($qry);
        $resultado=$this->_db->ejecutar();
        if(count($resultado)==0){
            $arrDevolver = array(
                    "soyError" => true,
                    "nivel" => 1,
                    "mensaje" => "Error al actualizar!!!!"
                    );
        }else{
            $arrDevolver = array(
                    "soyError" => false,
                    "nivel" => 1,
                    "mensaje" => "Movimiento actualizado!!!!"
                    );
        
        }
           echo json_encode($arrDevolver);exit;
    
    }

    public function agregarRoturas($arrParametros){
  // echo "llega";exit;
        $qry="SELECT id_movimiento,movimiento_total_fact, factura_id FROM datos_movimientos WHERE tipo_movimiento_id = 2 AND  remito_nro='$arrParametros[facturas]' AND id_estado<>25 AND anulado=false;";
        $this->_db->setQry($qry);
//echo $qry;exit;
        $resultado = $this->_db->ejecutar();
        $arrLineas = explode ("||",$arrParametros[productos]);
        array_pop($arrLineas);
        
        foreach ($arrLineas AS $linea){
//var_dump($linea);exit;
            list( $productoId,$importe) = explode("#",$linea);
//echo $importe;
//echo $resultado[0][movimiento_total_fact];exit;
                if($importe > $resultado[0][movimiento_total_fact]){
                    $arrDevolver = array(
                    "soyError" => true,
                    "nivel" => 1,
                    "mensaje" => "El importe no puede ser superior al valor de remito!!!!"
                    );
                    echo json_encode($arrDevolver);exit;
                }
            $this->_db->addCamposTabla('id_movimiento');
            $this->_db->addCamposTabla('producto_id, lote_id, cantidad,unidades,producto_pventa,detalle_total_fact, producto_pcosto');
            $this->_db->addCamposValue($resultado[0][id_movimiento]);
            $this->_db->addCamposValue(1586);
            $this->_db->addCamposValue(3514);
            $this->_db->addCamposValue(1);
            $this->_db->addCamposValue(1);
            $this->_db->addCamposValue($importe *-1);
            $this->_db->addCamposValue($importe *-1);
            $this->_db->addCamposValue(-1);
            
            
            $this->_db->addFrom('detalle_movimientos');
            //genero y ejecuto
            $this->_db->generarInsert();

            //echo $this->_db->getQry() .  "<br>";exit;
            $qryDetalle =  $this->_db->getQry();
            
            $qryDetalle.="UPDATE datos_movimientos SET movimiento_total_fact =  movimiento_total_fact - $importe, obs='$arrParametros[observaciones]' WHERE id_movimiento = {$resultado[0][id_movimiento]};";
            
            $this->_db->addCamposTabla('factura_id');
            $this->_db->addCamposTabla('producto_id, cantidad,importe_unitario,importe_detalle,producto_pcosto');
            $this->_db->addCamposValue($resultado[0][factura_id]);
            $this->_db->addCamposValue(299);
            $this->_db->addCamposValue(1);
            $this->_db->addCamposValue($importe *-1);
            $this->_db->addCamposValue($importe *-1);
            $this->_db->addCamposValue(1);
            
            
            $this->_db->addFrom('detalle_facturas');
            //genero y ejecuto
            $this->_db->generarInsert();

            //echo $this->_db->getQry() .  "<br>";exit;
            $qryDetalle.=  $this->_db->getQry();
            
            $qryDetalle.="UPDATE datos_facturas SET factura_total_fact =  factura_total_fact - $importe, factura_faltan_fact = factura_faltan_fact - $importe, observaciones='$arrParametros[observaciones]' WHERE factura_id = {$resultado[0][factura_id]};";
            
            $this->_db->setQry($qryDetalle);
            
            $this->_db->ejecutarTransaccion();
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
                                    "mensaje" => "Movimiento Generado correctamente!"
                                    );
            }  
            echo json_encode($arrDevolver);exit;            
        }

    }
    public function traerDetallesMostrador($id){
        
        $qry="SELECT cliente_nombre, datos_movimientos.lista_id, cliente_id, obs, producto_nombre || ' - ' || producto_presentacion AS producto, producto_id, de.producto_pventa, sum(cantidad) AS cantidad, producto_id, subfamilia_id, remito_nro, impreso, datos_movimientos.obs, tipo_entrega, lista_nombre FROM datos_movimientos INNER JOIN detalle_movimientos de USING(id_movimiento) INNER JOIN datos_clientes USING(cliente_id) INNER JOIN datos_productos USING(producto_id) INNER JOIN datos_listas_precios ON datos_movimientos.lista_id = datos_listas_precios.lista_id  WHERE id_movimiento = $id AND id_estado=24 GROUP BY cliente_nombre, datos_movimientos.lista_id, cliente_id, obs,producto_nombre || ' - ' || producto_presentacion, producto_id, de.producto_pventa, producto_id, subfamilia_id, remito_nro, impreso,tipo_entrega, datos_listas_precios.lista_nombre ; ;";
        //echo $qry;exit;
        $this->_db->setQry($qry);
        $resultado = $this->_db->ejecutar();
        //var_dump($resultado);exit;
        if(count($resultado)==0){
            $arrDevolver = array(
                            "soyError" => true,
                            "nivel" => 0,
                            "alertados" => $arrAlertador,
                            "mensaje" => "El remito " . $resultado[0][remito_nro] . " ya fue impreso, imposible edtiar "
                            );
            echo json_encode($arrDevolver);exit;
        
        }
        foreach($resultado AS $data){
            
            $arrProductos[] = array(
                            "id"=>$data['producto_id'],
                            "idFamilia"=>$data['subfamilia_id'],
                            "label"=>$data['producto'],
                            "precio"=>$data['producto_pventa'],
                            "cantidad"=>ABS($data['cantidad'])
            
                        );
        
        }
        $arrayDevolver = array(
            "cliente"=>array(
                        "id"=>$resultado[0][cliente_id],
                        "idListaPrecio"=>$resultado[0][lista_id],
                        "label"=>$resultado[0][cliente_nombre]
                        ),
            "observaciones"=>$resultado[0][obs],
            "tipoEntrega"=>$resultado[0][tipo_entrega],
            "nroRemito"=>$resultado[0][remito_nro],
            "producto"=>$arrProductos,
            "lista"=>array(
                        "id"=>$resultado[0][lista_id],
                        "label"=>$resultado[0][lista_nombre]
                        )
        
        );
     echo json_encode($arrayDevolver);
    }
    public function editarMovimiento($arrParametros){
        //chequeamos q no este impreso y traemos los productos del mov asi devolvemos y anulamos
        $qry="SELECT remito_nro, impreso, producto_id, abs(cantidad) AS cantidad, lote_id FROM datos_movimientos INNER JOIN detalle_movimientos USING(id_movimiento) WHERE id_movimiento = $arrParametros[idMovimientoAnt] AND impreso=false AND anulado=false;";
        //echo $qry;exit;
        $this->_db->setQry($qry);
        $resultado = $this->_db->ejecutar();
        if(count($resultado)==0){
            $arrDevolver = array(
                            "soyError" => true,
                            "nivel" => 0,
                            "alertados" => $arrAlertador,
                            "mensaje" => "El remito " . $resultado[0][remito_nro] . " ya fue impreso o editado, imposible edtiar "
                            );
            echo json_encode($arrDevolver);exit;
        
        }
        
        //devolvemos al stock
        foreach($resultado AS $datos){
            $objProducto = NEW ProductoExtendido();
            $objProducto->cargarMe($datos['producto_id']);
            if($objProducto->getEsCombo()){
                $qry="SELECT tz.producto_id,tz.lote_id,dpc.cantidad FROM trazabilidad_combos tz INNER JOIN detalle_productos_combos dpc USING(combo_id) WHERE id_movimiento={$arrParametros[idMovimientoAnt]} GROUP BY tz.producto_id, tz.lote_id,dpc.cantidad;";
                $this->_db->setQry($qry);
                $resultado = $this->_db->ejecutar();
                foreach($resultado AS $data){
                  $cantidad = $data['cantidad'] * $datos['cantidad'];
                  $qryAnular.= ' SELECT fn_actualizar_stock_completo('. $cantidad . ',' .  $data['lote_id'] . ',' . $data['producto_id'] . ','  . $_SESSION['sucursalId'] . '); ';
                }
            }else{
                $qryAnular.= ' SELECT fn_actualizar_stock_completo('. $datos[cantidad] . ',' . $datos[lote_id] . ',' . $datos[producto_id] . ','  . $_SESSION['sucursalId'] . '); ';
            }
        }
        //echo $qryAnular;exit;
        $qryAnular.="UPDATE datos_movimientos SET anulado = true,anulado_fecha_hora=now(), anulado_user_id = $_SESSION[usuarioId] WHERE id_movimiento=$arrParametros[idMovimientoAnt];";
        $qryAnular.="UPDATE datos_facturas SET factura_cancelada = true,factura_cancelada_fecha_hora=now(), factura_cancelada_usuario_id = $_SESSION[usuarioId] WHERE id_movimiento=$arrParametros[idMovimientoAnt];";
        $arrParametros['parametros']['anularPorEdicion']=$qryAnular;
        $arrParametros['parametros']['de_id_movimiento']=$arrParametros['idMovimientoAnt'];
        //var_dump($arrParametros['parametros']);exit;
        $this->generarMovimiento($arrParametros['parametros']);
    }
    public function cargarDiferenciaDePrecio($arrParametros){
        //var_dump($arrParametros);
        $qry="SELECT id_movimiento,movimiento_total_fact, factura_id FROM datos_movimientos WHERE tipo_movimiento_id = 2 AND  factura_id='$arrParametros[remito]' AND id_estado=24;";
        $this->_db->setQry($qry);
        //echo $qry;exit;
        $resultado = $this->_db->ejecutar();
        if(count($resultado)==0){
            $arrDevolver = array(
                            "soyError" => true,
                            "nivel" => 0,
                            "alertados" => $arrAlertador,
                            "mensaje" => "Valide el estado del remito " . $resultado[0][remito_nro] 
                            );
            echo json_encode($arrDevolver);exit;
        }
            $this->_db->addCamposTabla('id_movimiento');
            $this->_db->addCamposTabla('producto_id, lote_id, cantidad,unidades,producto_pventa,detalle_total_fact, producto_pcosto');
            $this->_db->addCamposValue($resultado[0][id_movimiento]);
            $this->_db->addCamposValue(1587);
            $this->_db->addCamposValue(3514);
            $this->_db->addCamposValue(1);
            $this->_db->addCamposValue(1);
            $this->_db->addCamposValue($arrParametros['importe'] *-1);
            $this->_db->addCamposValue($arrParametros['importe'] *-1);
            $this->_db->addCamposValue(-1);
            
            
            $this->_db->addFrom('detalle_movimientos');
            //genero y ejecuto
            $this->_db->generarInsert();

            //echo $this->_db->getQry() .  "<br>";exit;
            $qryDetalle =  $this->_db->getQry();
            
            $qryDetalle.="UPDATE datos_movimientos SET movimiento_total_fact =  movimiento_total_fact - $arrParametros[importe], obs='$arrParametros[observaciones]' WHERE id_movimiento = {$resultado[0][id_movimiento]};";
            
            $this->_db->addCamposTabla('factura_id');
            $this->_db->addCamposTabla('producto_id, cantidad,importe_unitario,importe_detalle,producto_pcosto');
            $this->_db->addCamposValue($resultado[0][factura_id]);
            $this->_db->addCamposValue(1587);
            $this->_db->addCamposValue(1);
            $this->_db->addCamposValue($arrParametros['importe'] *-1);
            $this->_db->addCamposValue($arrParametros['importe'] *-1);
            $this->_db->addCamposValue(1);
            
            
            $this->_db->addFrom('detalle_facturas');
            //genero y ejecuto
            $this->_db->generarInsert();

            //echo $this->_db->getQry() .  "<br>";exit;
            $qryDetalle.=  $this->_db->getQry();
            
            $qryDetalle.="UPDATE datos_facturas SET factura_total_fact =  factura_total_fact - $arrParametros[importe], factura_faltan_fact = factura_faltan_fact - $arrParametros[importe], observaciones='$arrParametros[observaciones]' WHERE factura_id = {$resultado[0][factura_id]};";
            //echo $qryDetalle;exit;
        //echo $qryDetalle;exit;
        $this->_db->setQry($qryDetalle);
        $this->_db->ejecutarTransaccion();
        if (count($this->_db->getArrError()) > 0){
            $this->_objFrenteError->generarError($this->_db->getArrError());
            $this->_objFrenteError->setQry($qryFinal);
            $this->_objFrenteError->cargarError();
            //salvar el error
        }else{
            $arrDevolver = 
                    array(
                        "soyError" => false,
                        "nivel" => 0,
                        "alertados" => $arrAlertador,
                        "mensaje" => "Diferencia de precio cargada!"
                    );
            echo json_encode($arrDevolver);exit;
        }
    }
    public function traerDetallesMostradorEntradas($id){

        $qry="SELECT prove_nombre, datos_movimientos.prove_id, obs, producto_nombre || ' - ' || producto_presentacion AS producto, de.producto_id, de.producto_pcosto, cantidad,  remito_nro, datos_movimientos.obs, to_char(lote_vencimiento,'DD-MM-YYYY') AS vencimiento  FROM datos_movimientos INNER JOIN detalle_movimientos de USING(id_movimiento) INNER JOIN datos_proveedores USING(prove_id) INNER JOIN datos_productos USING(producto_id) INNER JOIN datos_lotes USING(lote_id) WHERE id_movimiento = $id AND de.detalle_anulado=false;";
        //echo $qry;exit;
        $this->_db->setQry($qry);
        $resultado = $this->_db->ejecutar();
        //var_dump($resultado);exit;
        if(count($resultado)==0){
            $arrDevolver = array(
                            "soyError" => true,
                            "nivel" => 0,
                            "alertados" => $arrAlertador,
                            "mensaje" => "El remito " . $resultado[0][remito_nro] . " ya fue impreso, imposible edtiar "
                            );
            echo json_encode($arrDevolver);exit;

        }
        foreach($resultado AS $data){

            $arrProductos[] = array(
                            "id"=>$data['producto_id'],
                            "idFamilia"=>$data['subfamilia_id'],
                            "label"=>$data['producto'],
                            "costo"=>$data['producto_pcosto'],
                            "cantidad"=>ABS($data['cantidad']),
                            "vencimiento"=>$data['vencimiento']

                        );

        }
        $arrayDevolver = array(
            "proveedor"=>array(
                        "id"=>$resultado[0][prove_id],
                        "idListaPrecio"=>$resultado[0][lista_id],
                        "label"=>$resultado[0][prove_nombre]
                        ),
            "observaciones"=>$resultado[0][obs],
            "tipoEntrega"=>$resultado[0][tipo_entrega],
            "nroRemito"=>$resultado[0][remito_nro],
            "producto"=>$arrProductos,
            "lista"=>array(
                        "id"=>$resultado[0][lista_id],
                        "label"=>$resultado[0][lista_nombre]
                        )

        );
     echo json_encode($arrayDevolver);
    }
    public function editarEntrada($arrParametros){

        $importeTotal=0;
        $arrLineas = explode ("||",$arrParametros['parametros']['productos']);
        array_pop($arrLineas);
        /*echo "<pre>";
        print_r($arrLineas);
        echo "</pre>";*/
        foreach ($arrLineas AS $linea){
            list( $productoId,$cantidad, $productoPcosto,$vencimiento) = explode("#",$linea);
            //var_dump($productos);exit;exit;
            $arrProductos[]=$productoId;
            //chequeamos q no este impreso y traemos los productos del mov asi devolvemos y anulamos
            $qry="SELECT dem.producto_id, abs(cantidad) AS cantidad, lote_id,detalle_id FROM datos_movimientos INNER JOIN detalle_movimientos dem USING(id_movimiento) INNER JOIN datos_lotes USING(lote_id) WHERE id_movimiento = $arrParametros[idMovimientoAnt] AND dem.producto_id = $productoId AND anulado=false AND detalle_anulado=false AND lote_vencimiento = TO_DATE('$vencimiento', 'DD-MM-YYYY');";
            //echo $qry;exit;
            $this->_db->setQry($qry);
            $resultado = $this->_db->ejecutar();
            if(count($resultado)==0){
                $objFrenteLotes = NEW FrenteLotes();
                $lote=1;
                //$vencimiento='31-12-2040';
                $objFrenteLotes->validarLote($lote, $productoId, $vencimiento,$_SESSION['sucursalId']);

                $arrDetalle['cantidad']=$cantidad;
                $arrDetalle['unidades']=$cantidad;
                $arrDetalle['id_movimiento']=$arrParametros[idMovimientoAnt];
                $arrDetalle['lote_id'] = $objFrenteLotes->getUltimoLoteId();
                $arrDetalle['producto_id'] = $productoId;
		$arrDetalle['costo'] = $productoPcosto;
		$arrDetalle['sucursal_id'] = $_SESSION['sucursalId'];

                $objDetalle = NEW DetalleMovimientoRecepcionExtendido();
                $qryMafia.= $objDetalle->salvarMe($arrDetalle);

            }else{
                if($resultado[0]['cantidad'] != $cantidad){
                    $cantNueva = $resultado[0]['cantidad'] - $cantidad;
                    $cantNueva = $cantNueva *-1;
                    $qryMafia.= ' SELECT fn_actualizar_stock_completo('. $cantNueva . ',' . $resultado[0]['lote_id'] . ',' . $productoId . ','  . $_SESSION['sucursalId'] . ');';
                    $qryMafia.="UPDATE detalle_movimientos SET cantidad= $cantidad,producto_pcosto = $productoPcosto, editado=true,editado_fecha_hora=now(),editado_usuario_id={$_SESSION['usuarioId']} WHERE detalle_id = {$resultado['0']['detalle_id']};";
                }
            }
            $importeDetalle=$productoPcosto*$cantidad;
            $importeTotal=$importeTotal + $importeDetalle;
        }
        $qryMafia.="UPDATE datos_movimientos SET movimiento_total_fact=$importeTotal WHERE id_movimiento=$arrParametros[idMovimientoAnt];";
        //print_r($arrProductos);exit;
        $qryProd="SELECT producto_id, cantidad,lote_id, detalle_id FROM detalle_movimientos WHERE id_movimiento=$arrParametros[idMovimientoAnt] AND detalle_anulado=false;";
        //echo $qryProd;
        $this->_db->setQry($qryProd);
        $resultadoProd = $this->_db->ejecutar();
        foreach ($resultadoProd AS $productos){
             if(!in_array($productos['producto_id'],$arrProductos)){

                    $qryMafia.="UPDATE detalle_movimientos SET detalle_anulado=true, editado_fecha_hora=now(), editado_usuario_id={$_SESSION['usuarioId']} WHERE detalle_id = {$productos['detalle_id']};";
                    $qryMafia.= ' SELECT fn_actualizar_stock_completo('. $productos['cantidad'] *-1  . ',' . $productos['lote_id'] . ',' . $productos['producto_id'] . ','  . $_SESSION['sucursalId'] . ');';
                    //echo $qryMafia;
            }
        }
        //echo $qryMafia;
        $this->_db->setQry($qryMafia);

        $this->_db->ejecutarTransaccion();
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
                                "mensaje" => "Movimiento editado!"
                                );
        }
        echo json_encode($arrDevolver);exit;
    }
    public function traerImportePendiente($idMovimiento){
        $qry="SELECT factura_faltan_fact FROM datos_facturas WHERE id_movimiento=$idMovimiento;";
        echo $qry;
        $this->_db->setQry($qry);
        $resultado = $this->_db->ejecutar();
        return $resultado[0][factura_faltan_fact];
    }
}

/*$p= NEW FrenteMovimiento();
$p->traerDetallesMostrador(2854);*/




