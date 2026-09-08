<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
//PEDIDOS
require_once 'DatoOrdenTrabajoExtendido.php';
/*
 * dMELMAC 
 * Guillo
 * chava
 * 
 */

/**
 * Description of FrenteOrdenesTrabajo
 *
 * @author by dMELMAC
 */

class FrenteOrdenesTrabajo
{
    private $_db;
    private $_colObjDatoOrdenTrabajo= Array();
        
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
    public function setColObjDatoOrdenTrabajo($obj){
        $this->_colObjDatoOrdenTrabajo[$obj->getOrdenTrabajoId()] = $obj;
    }
    public function getColObjDatoOrdenTrabajo(){
        return $this->_colObjDatoOrdenTrabajo;
    }
    public function cargarLosDatosLazzy($arrIds){
    
	if(is_array($arrIds)){
	    foreach($arrIds as $id){
		$ids.= $id['orden_id'] . ",";
	   }
		$ids=substr($ids, 0, -1);
	}else{
		$ids=$arrIds;
	}
	$this->_db->addSelect('*');
        $this->_db->addFrom('datos_ordenes_trabajo');
        $this->_db->addWhere("orden_id IN (" . $ids . ")" );
        $this->_db->addOrderBy('orden_id DESC');
        
	$this->_db->generarSelect();
        
        $resultado = $this->_db->ejecutar();
        
        if(count($resultado)==0){
	    return;
        }

        if($_POST['accion']=='prepararOrden'){ 
                if ($resultado[0][orden_preparada]==true)  {
                    $arrDevolver = array(
                                "soyError" => true,
                                "nivel" => 0,
                                "alertados" => $arrAlertador,
                                "mensaje" => "La orden de trabajo ya fue preparada" 
                                );
                    echo json_encode($arrDevolver);exit;
                }
                if ($resultado[0]['orden_estado_id'] != 1 ){
                    $arrDevolver = array(
                                "soyError" => true,
                                "nivel" => 0,
                                "alertados" => $arrAlertador,
                                "mensaje" => "Verifique el estado de la orden de trabajo, no es posible prepararla." 
                                );
                
                    echo json_encode($arrDevolver);exit;
                }
        }
        //var_dump($resultado);
        $FrenteEstados= NEW FrenteEstados();
        $FrenteEstados->cargarEstadosLazzy($resultado);
        $arrObjEstados= $FrenteEstados->getColObjEstados();
        
        //var_dump($resultado);exit;
        
        $FrenteProductos= NEW FrenteProductos();
        $FrenteProductos->cargarProductosLazzy($resultado);
        $arrObjProductos= $FrenteProductos->getColProductos();

        foreach($resultado AS $datos){
       	    
	    $objDatosOrdenTrabajo= NEW DatoOrdenTrabajoExtendido();
	    $objDatosOrdenTrabajo->cargarme($datos);
	    $objDatosOrdenTrabajo->setObjProductoDestino($arrObjProductos[$datos[producto_destino_id]]);
	    $objDatosOrdenTrabajo->setObjEstado($arrObjEstados[$datos[orden_estado_id]]);
	    
            $this->setColObjDatoOrdenTrabajo($objDatosOrdenTrabajo);
        }
    
    }
    
    public function cargarLosDetallesLazzy($arrIds){
    
	foreach($arrIds as $id){
	    $ids.= $id['orden_detalle_id'] . ",";
	}
	
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
        $this->_db->addFrom('detalle_ordenes_trabajo');
        $this->_db->addWhere("orden_detalle_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        $FrenteProductos = NEW FrenteProductos();
        $FrenteProductos->cargarProductosLazzy($resultado);
        $arrObjProductos= $FrenteProductos->getColProductos();
        
        foreach($this->getColObjDatoOrdenTrabajo() AS $objOrdenTrabajo){      
	    foreach($resultado AS $detalle){
		$objDetalleOrdenTrabajo= NEW DetalleOrdenTrabajoExtendido();
		$objDetalleOrdenTrabajo->cargarme($detalle);
		$objDetalleOrdenTrabajo->setObjProducto($arrObjProductos[$detalle[producto_id]]);
		$this->_colObjDatoOrdenTrabajo[$objOrdenTrabajo->getOrdenTrabajoId()]->setColObjDetalleOrdenTrabajo($objDetalleOrdenTrabajo);
	    }
	}
    
    }

    public function cargarLosDetallesLazzyPorLote($arrIds){
    
	foreach($arrIds as $id){
	    $ids.= $id['orden_detalle_id'] . ",";
	}	
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
        $this->_db->addFrom('ordenes_lotes');
        $this->_db->addWhere("orden_detalle_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        $FrenteProductos = NEW FrenteProductos();
        $FrenteProductos->cargarProductosLazzy($resultado);
        $arrObjProductos= $FrenteProductos->getColProductos();
        
        $FrenteLotes= NEW FrenteLotes();
        $FrenteLotes->cargarLotesLazzy($resultado);
        $arrObjLotes= $FrenteLotes->getColObjLote();
        
        $FrenteEstanterias = NEW FrenteEstanterias();
        $FrenteEstanterias->cargarEstanteriasLazzy($resultado);
        $arrObjEstanterias = $FrenteEstanterias->getColEstanterias();
        
        foreach($this->getColObjDatoOrdenTrabajo() AS $objOrdenTrabajo){
	    foreach($resultado AS $detalle){
		$objDetalleOrdenTrabajo= NEW DetalleOrdenTrabajoPorLoteExtendido();
		$objDetalleOrdenTrabajo->cargarme($detalle);
		$objDetalleOrdenTrabajo->setObjProducto($arrObjProductos[$detalle[producto_id]]);
                $objDetalleOrdenTrabajo->setObjLote($arrObjLotes[$detalle['lote_id']]);
                $objDetalleOrdenTrabajo->setObjEstanteria($arrObjEstanterias[$detalle['estanteria_id']]);
                $this->_colObjDatoOrdenTrabajo[$objOrdenTrabajo->getOrdenTrabajoId()]->setColObjDetalleOrdenTrabajo($objDetalleOrdenTrabajo);
	    }
	}
    }
    
    
    public function cargarOrdenTrabajoCompleta($ordenId){
    
        $this->_db->addSelect('orden_id, orden_detalle_id');
	$this->_db->addFrom('datos_ordenes_trabajo INNER JOIN detalle_ordenes_trabajo USING (orden_id)');

	if(is_string($ordenId)){
	    if(substr($ordenId, 0,3) == '+C0' || substr($ordenId, 0,3) == '+c0' ){
                $ordenId = substr($$ordenId,3); 
            }        
            $this->_db->addWhere('orden_id = \'' . $ordenId . '\'');
	}else{
            if(substr($ordenId['codigo'], 0,3) == '+C0' || substr($ordenId['codigo'], 0,3) == '+c0' ){
                $ordenId['codigo'] = substr($ordenId['codigo'],3); 
            }
            $this->_db->addWhere('orden_id = \'' . $ordenId['codigo'] . '\'');      
	}
     
        $this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
        $resultadoCabeceras = $this->_db->ejecutar();
        
        if(count($resultadoCabeceras)==null){
	    $arrDevolver = array(
                                "soyError" => true,
                                "nivel" => 0,
                                "alertados" => $arrAlertador,
                                "mensaje" => "No existe la orden ingresada" 
                                );
	    echo json_encode($arrDevolver);exit;
        }
        $this->cargarLosDatosLazzy($resultadoCabeceras['0']['orden_id']);

	if($_POST['accion']=='prepararOrden' || $_POST['accion']=='validarOrden' ){
            $this->cargarLosDetallesLazzyPorLote($resultadoCabeceras);
	}else{
	    $this->cargarLosDetallesLazzy($resultadoCabeceras);
	}
    }


    public function buscarOrdenTrabajo($arrParametros){


	$this->_db->addSelect('orden_id');
	$this->_db->addFrom('datos_ordenes_trabajo');
	$this->_db->addFrom('INNER JOIN detalle_ordenes_trabajo USING(orden_id)');


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
	if($arrParametros['productos'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('producto_id = \'' . $arrParametros[productos] . '\'');
	}
	if($arrParametros['nroOrden'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('orden_nro= \'' . $arrParametros[nroOrden] . '\'');
	}
	if($arrParametros['estados'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('orden_estado_id= \'' . $arrParametros[estados] . '\'');
	}else{
	    $this->_db->addWhere('orden_cancelada = false ');
	}
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenOrdenesTrabajo($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['desdeBusqueda']==false){
	    $this->_db->addLimit(500);
	}


	$this->_db->addGroup('orden_id');
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
	    $arrIds[$i]['orden_id']= $resultado[$i]['orden_id'];
	}
	//var_dump($arrIds);exit;
	$this->cargarLosDatosLazzy($arrIds);
    }

    
    //**PARA EL FRENTE**//
    public function setOrdenOrdenesTrabajo($parametros){
	if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){
	    $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'orden_id';
	    $this->_ordenColumnas['ordenarOrden']  =   'DESC';
	}
    }

    public function getOrdenOrdenesTrabajo(){
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

	foreach($this->_colObjDatoOrdenTrabajo AS $objOrdenTrabajo){
	
		$arr []   =    array
		    (
			//id si o si un solo string (sin espacios en blanco)
			"id"            =>  $objOrdenTrabajo->getOrdenTrabajoId(),
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
			    $objOrdenTrabajo->getOrdenTrabajoNro(),
			    $objOrdenTrabajo->getOrdenTrabajoFechaHora(),
			    $objOrdenTrabajo->getObjProductoDestino()->getProductoNombre() . " " . $objOrdenTrabajo->getObjProductoDestino()->getProductoPresentacion(),
			    $objOrdenTrabajo->getObjEstado()->getEstadoNombre(),
			    $objOrdenTrabajo->getOrdenTrabajoObs()
			),
		    );

	}
	return $arr;
    }

    public function armarDetalles($ordenId){

	$this->cargarOrdenTrabajoCompleta($ordenId);
	
	foreach($this->_colObjDatoOrdenTrabajo AS $objOrdenTrabajo){
	
	
		$propiedadesOrden =   array(
			array(
			    "display"   =>  'Nro. Orden',
			    "name"      =>  'orden_nro',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objOrdenTrabajo->getOrdenTrabajoNro(),

			),
			array(
			    "display"   =>  'Fecha',
			    "name"      =>  'fecha_hora_orden',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objOrdenTrabajo->getOrdenTrabajoFechaHora(),

			),
			array(
			    "display"   =>  'Estado',
			    "name"      =>  'orden_estado_id',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objOrdenTrabajo->getObjEstado()->getEstadoNombre(),

			)
		    );
	}
	foreach($objOrdenTrabajo->getColObjDetalleOrdenTrabajo() AS $detalle){

    
	    $arr []   =    array
		(
		    //id si o si un solo string (sin espacios en blanco)
		    "id"            =>  $detalle->getOrdenTrabajoDetalleId(),
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
			$detalle->getOrdenTrabajoCantidad(),
		    ),
		);
	}

    
	$detallesOrden    =   array(
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
	    "propiedades"       =>  $propiedadesOrden,
	    "listadoDetalles"   =>  $detallesOrden
	);
	    return $array_devolver;
    }
    
    public function generarOrdenTrabajo($arrParametros){
               
        $this->_db->addCamposTabla('seq_datos_ordenes_trabajo_id');
	$this->_db->generarProximo();
        $resultado = $this->_db->ejecutar();
	$arrParametros['idOrdenTrabajo'] = $resultado[0]['nextval'];        
        $arrParametros['estadoId'] = 1; //En preparación
        
	$arrProductos = explode ("||",$arrParametros['materiaPrima']);
        
	array_pop($arrProductos);
	
        foreach ($arrProductos AS $linea){
            
            $loteAutomatico = 1;
            $loteId='NULL';
            $estanteriaId = 15;
   
	    $arrProductosFinal = explode("#",$linea);
            if($arrProductosFinal[2] != null){
                $loteAutomatico = 0;
                $arrLote = array('lote' => $arrProductosFinal[2], 'producto_id' => $arrProductosFinal[0]); 
                $objFrenteLotes = new FrenteLotes();
                $objFrenteLotes->cargarLotePorLoteProducto($arrLote);
                $loteId = $objFrenteLotes->getUltimoLoteId();
            }
            
            $arrProductosFinal['idOrdenTrabajo'] = $arrParametros['idOrdenTrabajo'];
            //var_dump($arrProductosFinal);exit;
            
            $this->_db->addCamposTabla('seq_detalles_ordenes_detalle_id');
            $this->_db->generarProximo();
            $resultado = $this->_db->ejecutar();
            $arrProductosFinal['ordenDetalleId'] = $resultado[0]['nextval'];        
        
            $objDetalleOrdenTrabajo = new DetalleOrdenTrabajoExtendido();
            $qryOrden['detalle'] .= $objDetalleOrdenTrabajo->salvarme($arrProductosFinal);
            
            
           $qryOrden['detalle'] .= 'SELECT fn_movimiento_stock_por_ordenes(' . $loteAutomatico .' , ' . $arrProductosFinal['idOrdenTrabajo'] . ', ' . $arrProductosFinal['ordenDetalleId']  . ', ' . $arrProductosFinal['1'] * -1 . ', ' . $arrProductosFinal['0'] . ',' . $loteId . ',' . $estanteriaId .'); ';
           //echo $qryPedido['detalle']; exit;
            if (count($objDetalleOrdenTrabajo->getArrError()) > 0){
                $this->_objFrenteError->generarError($objDetalleOrdenTrabajo->getArrError());
                $this->_objFrenteError->cargarError();
            }	
        }
        
	$objDatoOrden = new DatoOrdenTrabajoExtendido();
        $qryOrden['dato'] = $objDatoOrden->salvarme($arrParametros);
        if (count($objDatoOrden->getArrError()) > 0){
            $this->_objFrenteError->generarError($objDatoOrden->getArrError());
            $this->_objFrenteError->cargarError();
        }
        
        //$arrQry['log'] = "SELECT fn_log_historico_estados_ordenes(0,{$arrParametros['idOrdenTrabajo']},1,{$_SESSION['usuarioId']})";
        $qryFinal = $qryOrden['detalle'] . $qryOrden['dato'] . $arrQry['log'];
        
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
                                "mensaje" => "Ordenes generadas con exito"
                                );
        
        }
        echo json_encode($arrDevolver);exit;
    }

    public function cancelarOrdenTrabajo($id){
        //cargar el obj dato del pedido
        $objDatoOrden = new DatoOrdenTrabajoExtendido();
        $objDatoOrden->cargarme($id);
        
        //chequear que no este validado
        if($objDatoOrden->getOrdenTrabajoPreparado() == TRUE || $objDatoOrden->getOrdenTrabajoCancelado()==true){
            $arrDevolver = array(
                                "soyError" => false,
                                "nivel" => 0,
                                "alertados" => $arrAlertador,
                                "mensaje" => " La orden nro " . $objDatoOrden->getOrdenTrabajoNro() . " ya fue validada o cancelada con anterioridad, NO ES POSIBLE REALIZAR LA ANULACIÓN." 
                                );
            echo json_encode($arrDevolver);exit;
        }
        
        $qryCancelacion = $objDatoOrden->cancelarMe($id);
        
        $this->_db->addSelect('ABS(pl.cantidad)  AS cantidad, da.sucursal_id, da.almacen_id, de.estanteria_id, pl.lote_id, pl.producto_id');
        $this->_db->addFrom('ordenes_lotes pl');
        $this->_db->addFrom('INNER JOIN datos_estanterias de USING (estanteria_id)');
        $this->_db->addFrom('INNER JOIN datos_almacenes da USING (almacen_id)');
        $this->_db->addWhere('orden_id = ' . $id );
        
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        foreach ($resultado as $detalle){
            $qryCancelacion .= 'SELECT fn_actualizar_stock_completo(' . $detalle['cantidad'] . ',' . $detalle['sucursal_id'] . ',' . $detalle['almacen_id'] . ',' . $detalle['estanteria_id'] . ',' . $detalle['lote_id'] . ',' . $detalle['producto_id'] . ');';
        }
        //$qryCancelacion.= "SELECT fn_log_historico_estados_ordenes(0,{$id},3,{$_SESSION['usuarioId']})";
        //echo $qryCancelacion;exit;
        
        //echo $qryCancelacion;
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
                                "mensaje" => "La orden nro. " . $objDatoOrden->getOrdenTrabajoNro() . " fue cancelada con EXITO."
                                );
        }  
        echo json_encode($arrDevolver);exit;
    }
    public function actualizarEstado($arrParametros){
    
	$objDatoOrden = new DatoOrdenTrabajoExtendido();
	$objDatoOrden->actualizar();
    
    }
    public function prepararOrdenTrabajo($arrParametros){
        //var_dump($arrParametros);
	$this->cargarOrdenTrabajoCompleta($arrParametros);
	
	foreach($this->_colObjDatoOrdenTrabajo AS $objOrdenTrabajo){

            
		$propiedadesOrden =   array(
			array(
			    "display"   =>  'Nro. Orden',
			    "name"      =>  'orden_nro',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objOrdenTrabajo->getOrdenTrabajoNro(),

			),
			array(
			    "display"   =>  'Fecha',
			    "name"      =>  'fecha_hora_orden',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objOrdenTrabajo->getOrdenTrabajoFechaHora(),

			),
			array(
			    "display"   =>  'Estado',
			    "name"      =>  'orden_estado_id',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objOrdenTrabajo->getObjEstado()->getEstadoNombre(),

			)
		    );
	}
	foreach($objOrdenTrabajo->getColObjDetalleOrdenTrabajo() AS $detalle){

    
	    $arr []   =    array
		(
		    //id si o si un solo string (sin espacios en blanco)
		    "id"            =>  $detalle->getOrdenTrabajoDetalleId(),
		    //define las herramientas
		    "herramientas"   =>  array(
			 "editable"      =>  false,
			  ),
			 "bloqueado"     => false,	    
		    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
		    "cell"          => array( 
			$detalle->getObjProducto()->getProductoNombre() . " " . $detalle->getObjProducto()->getProductoPresentacion(),
			$detalle->getObjLote()->getLote(),
			abs($detalle->getOrdenTrabajoCantidad()),
			$detalle->getObjEstanteria()->getEstanteriaNombre()
		    ),
		);
	}

    
	$detallesOrden    =   array(
	    "bloqueable"=> false,
	    "modelo"    =>  array(
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
	    "propiedades"       =>  $propiedadesOrden,
	    "listadoDetalles"   =>  $detallesOrden
	);
	    return $array_devolver;
    }
    
    public function validarOrden($arrParametros){
        $this->cargarOrdenTrabajoCompleta($arrParametros['id']);
        
        $arrCodigos = explode("||", $arrParametros['codigos']);
        array_pop($arrCodigos);
        
        $frenteTrazabilidad = new FrenteTrazabilidad();
        $arrFinal = array();
        
        $i=0;
        foreach($arrCodigos AS $codigo){
            list($tipo, $gtin, $serie, $vencimiento, $lote, $productoId, $relacion) = explode("#", $codigo);

            if($serie != NULL){
                if($llaveGtinSerieAnterior != $serie ){
                echo "entra";
                    $frenteTrazabilidad->cargarPorCodigo($serie);
                    $objProducto = $frenteTrazabilidad->getObjProducto();
                    $objLote = $frenteTrazabilidad->getObjLote();
                    $objPresentacion = $frenteTrazabilidad->getObjPresentaciones();
        
                    $arrFinal[$objProducto->getProductoId()][$objLote->getLoteId()]['lote'] = $objLote->getLote();
                    $arrFinal[$objProducto->getProductoId()][$objLote->getLoteId()]['cantidad'] = 0  ;
                    $arrFinal[$objProducto->getProductoId()][$objLote->getLoteId()]['ingreso'] += 1 * $objPresentacion->getUnidades() ;
                    
                }else{
                    $arrSeriesRepetidas[] = $serie;
                }
                $llaveGtinSerieAnterior = $gtin.$serie;
            }else{
            $i++;
                //ver como cargar los datos para armar el mismo array
                $objFrenteProducto = new FrenteProductos();
                $objFrenteProducto->cargarProducto($productoId);
                $colObjProducto = $objFrenteProducto->getColProductos();
                $objProducto = $colObjProducto[$productoId];
                
                $objFrenteLote = new FrenteLotes();
                $arrLote = array('lote' => $lote, 'producto_id' => $productoId); 
                $objFrenteLote->cargarLotePorLoteProducto($arrLote);
                $colObjLote = $objFrenteLote->getColObjLote();
                $objLote = array_shift($colObjLote);
                                                
                $objFrentePresentacion = new FrentePresentacionesProductos();
                $objFrentePresentacion->cargarPresentacionesProductos($relacion);
                $colObjPresentacion = $objFrentePresentacion->getColObjPresentacionesProductos();
                $objPresentacion = $colObjPresentacion[$relacion];
                
                $arrFinal[$objProducto->getProductoId()][$objLote->getLoteId()]['lote'] = $objLote->getLote();
                $arrFinal[$objProducto->getProductoId()][$objLote->getLoteId()]['cantidad'] = 0  ;
                $arrFinal[$objProducto->getProductoId()][$objLote->getLoteId()]['ingreso'] += 1 * $objPresentacion->getUnidades() ;
                $arrFinal[$objProducto->getProductoId()][$objLote->getLoteId()]['envases'] += 1;
                $arrFinal[$objProducto->getProductoId()][$objLote->getLoteId()]['unidades'] = $objPresentacion->getUnidades();

            }
        }
        foreach ($this->getColObjDatoOrdenTrabajo() as $objOrdenTrabajo){
            foreach($objOrdenTrabajo->getColObjDetalleOrdenTrabajo() AS $objDetalleOrdenTrabajo){
        	if($arrFinal[$objDetalleOrdenTrabajo->getObjProducto()->getProductoId()][$objDetalleOrdenTrabajo->getObjLote()->getLoteId()]['ingreso'] == NULL){
                   $arrFinal[$objDetalleOrdenTrabajo->getObjProducto()->getProductoId()][$objDetalleOrdenTrabajo->getObjLote()->getLoteId()]['ingreso'] = 0;
                }
                $arrFinal[$objDetalleOrdenTrabajo->getObjProducto()->getProductoId()][$objDetalleOrdenTrabajo->getObjLote()->getLoteId()]['lote'] = $objDetalleOrdenTrabajo->getObjLote()->getLote(); 
                $arrFinal[$objDetalleOrdenTrabajo->getObjProducto()->getProductoId()][$objDetalleOrdenTrabajo->getObjLote()->getLoteId()]['cantidad'] += $objDetalleOrdenTrabajo->getOrdenTrabajoCantidad(); 
                
            }
        }
        
        $arrTitulo = array ('Lote','Envases', 'Cant.', 'Ingr.' );
        $validado = TRUE; 
        
        foreach($arrFinal as $arrProductos){
            foreach ($arrProductos as $arrLotes){ 
            
			if($arrLotes['ingreso'] > abs($arrLotes['cantidad'])){
			
			    $diferencia = $arrLotes['ingreso'] - abs($arrLotes['cantidad']);
			
			}            
                        if(abs($arrLotes['cantidad']) > $arrLotes['ingreso'] || $diferencia > $arrLotes['unidades']){
                        
                            $validado = FALSE;
                            $color = "red";
                        }else {
                            $color = "blue";

                        }
                        $datos[] = array('color' => $color , $arrLotes['lote'] , $arrLotes['envases'] , abs($arrLotes['cantidad']), $arrLotes['ingreso']);
                
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
        $arrMovimiento['tipo'] = 'ordenTrabajo';
        $arrMovimiento['cliente'] = 248;
        $arrMovimiento['tipoMovimientoId'] = 15;
        $arrMovimiento['codigos'] = $arrParametros['codigos'];
        
        $objDatoOrden= array_shift($this->getColObjDatoOrdenTrabajo());
        
        //$arrMovimiento['cliente'] = $objDatoOrden->getObjCliente()->getClienteId();
        $arrMovimiento['ordenTrabajoId'] = $objDatoOrden->getOrdenTrabajoId();
        	
    
	//var_dump($arrMovimiento);exit;

        $frenteMovimiento = new FrenteMovimiento();
        $frenteMovimiento->generarMovimiento($arrMovimiento);
    }
}

