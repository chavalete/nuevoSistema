<?php
/**
 * Description of FrenteTransaccionesPendientes
 *
 * @author dMELMAC
 */
//ALMACENAMIENTO
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
//SOAP
//require_once '/var/www/html/melmac/soap/FrenteSoap.php';
//PENDIENTES
require_once 'TransaccionesPendientes.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mensajeria/soap/FrenteSoap.php';

class FrenteTransaccionesPendientes {
    private $_db_dM;
    
    private $_arrError;
    
    private $_colObjTransaccionesPendientes = Array();
    
    //** NECESARIOS PARA EL FRENTE DE FRENTES//
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    private $_codigoSucursal;
    private $_anmatCliente;

    //** FIN NECESIARIOS**//

    public function setArrError($arrError){
        $this->_arrError = $arrError;
    }

    public function getArrError(){
        return $this->_arrError;
    }
    
    public function __construct() {
        $this->_db_dM = new FrenteAlmacenamiento();
    }
    
    public function setColObjTransaccionPendientes($obj){
        $this->_colObjTransaccionesPendientes[$obj->getIdTransaccionAnmatGlobal()]=$obj;
    }
    
    public function getColObjDatoTransaccion(){
        return $this->_colObjTransaccionesPendientes;
    }

    public function buscarTransaccion ($arrParametros){

	$this->_db_dM->addSelect('id_transaccion_anmat_global');
	$this->_db_dM->addFrom('transacciones_pendientes_anmat');
	
	//**ANALIZO EL WHERE**//
	if($arrParametros['stringBuscar']){
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
	}

	//**where faso = true**/
	if($arrParametros['gln_origen'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db_dM->addWhere('gln_origen = \'' . $arrParametros[gln_origen] . '\'');
	}
	if($arrParametros['origenNombre'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db_dM->addWhere('gln_origen = \'' . $arrParametros[origenNombre] . '\'');
	}
	if($arrParametros['numero_serial'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db_dM->addWhere('numero_serial = \'' . $arrParametros[numero_serial] . '\'');
	}
	if($arrParametros['nombre'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db_dM->addWhere('nombre = \'' . $arrParametros['nombre'] . '\'');
	}
	if($arrParametros['nombreProducto'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db_dM->addWhere('gtin = \'' . $arrParametros['nombreProducto'] . '\'');
	}
	if($arrParametros['gtin'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db_dM->addWhere('gtin = \'' . $arrParametros['gtin'] . '\'');
	}
	if($arrParametros['lote'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db_dM->addWhere('lote = \'' . $arrParametros['lote'] . '\'');
	}
	if($arrParametros['razon_social_origen'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db_dM->addWhere('razon_social_origen = \'' . $arrParametros['razon_social_origen'] . '\'');
	}
	if($arrParametros['gln_destino'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db_dM->addWhere('gln_destino = \'' . $arrParametros[gln_destino] . '\'');
	}
        if($arrParametros['remito'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db_dM->addWhere('n_remito = \'' . $arrParametros[remito] . '\'');
	}
	if($arrParametros['facturas'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db_dM->addWhere('n_factura = \'' . $arrParametros[facturas] . '\'');
	}
	if($arrParametros['desdeBusqueda']==true && $arrParametros['alertadas']!=0){
	    if($arrParametros['alertadas']==1){
		$this->_db_dM->addWhere('alertado = \'' . 't' . '\'');
	    }else{
		$this->_db_dM->addWhere('alertado = \'' . 'f' . '\'');
	    }
	}
	
	
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenTransacciones($arrParametros);
	$this->_db_dM->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['desdeBusqueda']==false){
	    $this->_db_dM->addLimit(1000);
	}

	switch($arrParametros['ordenarPor']){

	    case "gln_origen":
		    $this->_db_dM->addGroup('gln_origen, ');
		    break;
	    case "gln_destino":
		    $this->_db_dM->addGroup('gln_destino, ');
		    break;
	    case "f_evento":
		    $this->_db_dM->addGroup('f_evento, ');
		    break;
            case "f_transaccion":
		    $this->_db_dM->addGroup('f_transaccion, ');
		    break;
	}

	$this->_db_dM->addGroup('id_transaccion_anmat_global');
	//*FIN LIMIT*//
	
	$this->_db_dM->generarSelect();

	//echo $this->_db_dM->getQry();exit;
	$arr =  $this->_db_dM->ejecutar();
	
	//Zahora seteamos el total y la pagina
        $arrTransacAnmat = Array();
        
        foreach ($arr as $data){
                $arrTransacAnmat[] = $data['id_transaccion_anmat_global'];
        }
        
        $this->setTotal(count($arrTransacAnmat));
        $this->setPagina($arrParametros['pagina']);

        if($this->getTotal() < $ultimoRegistro)	{
            $ultimoRegistro = $this->getTotal();
        }
        
	for($i=$primerRegistro; $i<$ultimoRegistro;$i++){     
	
	    $arrIds[$i]['id_transaccion_anmat_global']= $arrTransacAnmat[$i];
	}
	$this->cargarLosDatosLazzy($arrIds);
    }
    public function cargarLosDatosLazzy($arrIds){
    
	if(is_array($arrIds)){
	    
	    foreach($arrIds as $id){
	    
		$ids.= $id['id_transaccion_anmat_global'] . ",";
	    }
		$ids=substr($ids, 0, -1);
	}else{
		$ids=$arrIds;
	}
	$this->_db_dM->addSelect('*');
        $this->_db_dM->addFrom('transacciones_pendientes_anmat');
        $this->_db_dM->addWhere("id_transaccion_anmat_global IN (" . $ids . ")" );
        $this->_db_dM->addOrderBy('id_transaccion_anmat_global DESC');
	$this->_db_dM->generarSelect();
        //echo $this->_db_dM->getQry();exit;
        $resultado = $this->_db_dM->ejecutar();
        
        $FrenteGlnOrigen = NEW FrenteGln();
        $FrenteGlnOrigen->cargarGlnOrigenLazzy($resultado);
        $arrObjGlnOrigen= $FrenteGlnOrigen->getColObjGlnOrigen();
        
        $FrenteProductosAnmat = NEW FrenteProductosAnmat();
        $FrenteProductosAnmat->cargarProductosLazzy($resultado);
        $arrObjProductos= $FrenteProductosAnmat->getColProductos();
        
        
        $antIdTransaccionGlobal= NULL;
        
        foreach($resultado AS $datos){
       	    //echo $datos[error_id];exit;
	    if($datos[id_transaccion_anmat_global]!= $antIdTransaccionGlobal){
		
		$objTransaccion= new TransaccionesPendientes();
		$objTransaccion->cargarMe($datos);
		$objTransaccion->setObjGlnOrigen($arrObjGlnOrigen[$datos[gln_origen]]);
		$objTransaccion->setObjProducto($arrObjProductos[$datos[gtin]]);
		
		$this->_db_dM->addSelect('count(*) AS cantidad');
		$this->_db_dM->addFrom('transacciones_pendientes_anmat');
		$this->_db_dM->addWhere("id_transaccion_anmat_global = " . $datos[id_transaccion_anmat_global] . "" );
		$this->_db_dM->generarSelect();
		
		$resultadoCantidad = $this->_db_dM->ejecutar();
		//echo count($resultadoCantidad);exit;
		$objTransaccion->setCantidadRecepcion($resultadoCantidad[0]['cantidad']);
		//var_dump($objTransaccion);exit;
		$this->setColObjTransaccionPendientes($objTransaccion);
	    }
	    $antIdTransaccionGlobal = $datos['id_transaccion_anmat_global'];
        }
    
    }
    public function cargarLosDatosLazzyPorId($id){
    
	
	$this->_db_dM->addSelect('*');
        $this->_db_dM->addFrom('transacciones_pendientes_anmat');
        $this->_db_dM->addWhere("id_transaccion_anmat IN (" . $id . ")" );
        $this->_db_dM->addOrderBy('id_transaccion_anmat_global DESC');
	$this->_db_dM->generarSelect();
        //echo $this->_db_dM->getQry();exit;
        $resultado = $this->_db_dM->ejecutar();
        
	$FrenteGlnDestino = NEW FrenteGln();
        $FrenteGlnDestino->cargarGlnDestinoLazzy($resultado);
        $arrObjGlnDestino= $FrenteGlnDestino->getColObjGlnDestino();
        
        foreach($resultado AS $datos){    
		
	    $objTransaccion= new TransaccionesPendientes();
	    $objTransaccion->cargarMe($datos);
	    $objTransaccion->setObjGlnDestino($arrObjGlnDestino[$datos[gln_destino]]);
	    $objTransaccion->setObjProducto($arrObjProductos[$datos[gtin]]);
	}
	return $objTransaccion;
    }

    public function cargarLosDetallesLazzy($arrIds){
    
	foreach($arrIds as $id){
	    $ids.= $id['id_transaccion'] . ",";
	}
	$ids=substr($ids, 0, -1);
	$this->_db_dM->addSelect('*');
	$this->_db_dM->addFrom('transacciones_pendientes_anmat');
        $this->_db_dM->addWhere("id_transaccion IN (" . $ids . ")" );
	$this->_db_dM->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db_dM->ejecutar();
        
        $FrenteProductosAnmat = NEW FrenteProductosAnmat();
        $FrenteProductosAnmat->cargarProductosLazzy($resultado);
        $arrObjProductos= $FrenteProductosAnmat->getColProductos();

        $FrenteErrorAnmatExtendido = NEW FrenteErrorAnmatExtendido();
        $FrenteErrorAnmatExtendido->cargarErrorAnmatLazzy($resultado);
        $arrErrorAnmatExtendido = $FrenteErrorAnmatExtendido->getColObjErrorAnmat();
        foreach($this->_colObjTransaccionesPendientes AS $objTransaccion){
        
	    foreach($resultado AS $detalle){
	    
		$objDetalleTransaccion= NEW TransaccionesPendientes();
		$objDetalleTransaccion->cargarme($detalle);
		$objDetalleTransaccion->setObjProducto($arrObjProductos[$detalle[producto_gtin]]);
		$objDetalleTransaccion->setObjErrorAnmat($arrErrorAnmatExtendido[$detalle[error_id]]);
		$objTransaccion->colObjDetalleTransaccion[] = $objDetalleTransaccion;
		//var_dump($objTransaccion->colObjDetalleTransaccion);exit;
		$this->setColObjTransaccionPendientes($objTransaccion);
		
		/*echo "<pre>";
		print_r($objTransaccion);
		echo "</pre>";exit;*/
	    }
	}
    
    }
    public function cargarTransaccionCabecera($id){
        $objTransaccion= new TransaccionesPendientes();
        $objTransaccion->cargarMe($id);
        //var_dump($objTransaccion);exit;
        $this->setColObjTransaccionPendientes($objTransaccion);
    }
    public function cargarTransaccionCompleta($idTransaccionAnmatGlobal){
    
        $this->_db_dM->addSelect('*');
	$this->_db_dM->addFrom('transacciones_pendientes_anmat');
        $this->_db_dM->addWhere('id_transaccion_anmat_global= \'' . $idTransaccionAnmatGlobal . '\'');      
        
        
        $this->_db_dM->generarSelect();
	//echo $this->_db_dM->getQry();exit;
        $resultado = $this->_db_dM->ejecutar();
        
        $this->cargarLosDatosLazzy($resultado);
	$this->cargarLosDetallesLazzy($resultado);
    }
    
    public function setOrdenTransacciones($parametros){
	if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){
	    $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
	}else{
	    //definir default
     	    $this->_ordenColumnas['ordenarPor']    =   'id_transaccion_anmat_global';
	    $this->_ordenColumnas['ordenarOrden']  =   'DESC';
	}
    }

    public function getOrdenTransacciones(){
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
        foreach($this->_colObjTransaccionesPendientes AS $objTransaccion){
        //var_dump($objTransaccion);exit;
            $arr []   =    array
                (
                    //id si o si un solo string (sin espacios en blanco)
                    "id" =>  $objTransaccion->getIdTransaccionAnmatGlobal(),
                    //define las herramientas
                    "herramientas"  =>  array(
                            "editable"      =>  false,
                            "cancelable"    =>  false,
                            "detalles"      =>  true,
                            "imprimir"      =>  false,
                            "autogestion"   =>  false,
                            "enviarTransaccion" =>  false
                            ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell" => array(
                        $objTransaccion->getIdTransaccionAnmatGlobal(),
                        $objTransaccion->getFechaEvento(),
                        $objTransaccion->getObjProducto()->getProductoNombre() ." ". $objTransaccion->getObjProducto()->getProductoPresentacion(),
                        "Fact: " . $objTransaccion->getNFactura() . " Rto: ". $objTransaccion->getNRemito(), 
                        $objTransaccion->getDescEvento() ,
                        $objTransaccion->getObjGlnOrigen()->getGlnNombre() . " " . $objTransaccion->getObjGlnOrigen()->getGln(),
                        $objTransaccion->getCantidadRecepcion()
                    ),
                );
	}
	return $arr;
    }

    public function armarDetalles($id){
    
        $this->cargarTransaccionCompleta($id);
        if($_SESSION['usuarioRecepcion']=='t'){
		$alertar = false;
	}else{
		$alertar=true;
	}
	foreach($this->_colObjTransaccionesPendientes AS $objTransaccion){
		$propiedadesTransaccion =   array(
			array(
			    "display"   =>  'Fecha evento',
			    "name"      =>  'fecha_evento',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objTransaccion->getFechaEvento(),

			),
                        array(
			    "display"   =>  'Fecha transaccion',
			    "name"      =>  'fecha_transaccion',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objTransaccion->getFechaTransaccion(),

			),
			array(
			    "display"   =>  'Razon social origen',
			    "name"      =>  'razon_social_origen',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objTransaccion->getRazonSocialOrigen(),

			),
			array(
			    "display"   =>  'GLN Origen',
			    "name"      =>  'gln_origen',
			    "class"	=>'',
			    "editable"  =>  false,
			    "value"     =>  $objTransaccion->getGlnOrigen(),

			),
			array(
			    "display"   =>  'Razon social destino',
			    "name"      =>  'razon_socal_destino',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objTransaccion->getRazonSocialDestino(),

			),
			array(
			    "display"   =>  'GLN Destino',
			    "name"      =>  'gln_destino',
			    "class"	=>'',
			    "editable"  =>  false,
			    "value"     =>  $objTransaccion->getGlnDestino(),

			),
			array(
			    "display"   =>  'Transaccion ANMAT',
			    "name"      =>  'id_transaccion_anmat',
			    "class"	=>'',
			    "editable"  =>  false,
			    "value"     =>  $objTransaccion->getIdTransaccionAnmat(),
			),
			array(
			    "display"   =>  'Movimiento',
			    "name"      =>  'd_evento',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objTransaccion->getDescEvento(),

			),
	            array(
			    "display"   =>  'Estado',
			    "name"      =>  'estado',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objTransaccion->getidEstado(),

			)
		    );
            
	}

	foreach($objTransaccion->colObjDetalleTransaccion AS $detalle){

	    $arr []   =    array
		(
		    //id si o si un solo string (sin espacios en blanco)
		    "id"            =>  $detalle->getIdTransaccionAnmat(),
		    //define las herramientas
		    "herramientas"   =>  array(
			 "editable"      =>  false,
			  ),
			 "bloqueado"     => false,
			 "alertado" => $detalle->getAlertado(),
		    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
		    "cell"          => array(
			$detalle->getNumeroSerial(),
			$detalle->getNombre(),
			$detalle->getGtin(),
			$detalle->getLote(),
                        $detalle->getVencimiento(),
                        $detalle->getObjErrorAnmat()->getErrorDesc(),
		    ),
		);
	}

	$detallesTransaccion    =   array(
	    "bloqueable"=> false,
	    "modelo"    =>  array(
		array(

		    "display"   =>  'Taza',
		    "name"      =>  'numero_serial',
		    "editable"  =>  $arrParametros[edicion],
		    "class"	=>'',
		),
		array(
		    "display"   =>  'Producto',
		    "name"      =>  'nombre',
		    "editable"  =>  false,
		    "class"	=>'',
		),
		array(
		    "display"   =>  'GTIN',
		    "name"      =>  'gtin',
		    "editable"  =>  $arrParametros[edicion],
		    "class"	=>'',
		),
		array(
		    "display"   =>  'Lote',
		    "name"      =>  'lote',
		    "editable"  =>  $arrParametros[edicion],
		    "class"	=>'',

		),
		array(
		    "display"   =>  'Lote Vence',
		    "name"      =>  'vencimiento',
		    "editable"  =>  $arrParametros[edicion],
		    "class"	=>'datePicker',
		) ,
		array(
		    "display"   =>  'Error Desc',
		    "name"      =>  'vencimiento',
		    "editable"  =>  false,
		    "class"	=>'datePicker',
		),

	    ),
	    "celdas"    =>  $arr
	);
	
	$array_devolver =   array(
	    "alertar" => $alertar,
	    "propiedades"       =>  $propiedadesTransaccion,
	    "listadoDetalles"   =>  $detallesTransaccion
	);
	    return $array_devolver;
    }
    
    public function alertar($arrParametros){
    
	$arrParametros['idsToAlert'] = substr($arrParametros['idsToAlert'], 0, -1);
	$arrIds=  explode( ',', $arrParametros['idsToAlert']);
	
	foreach($arrIds as $resultado){

		
		$objTransaccionPendientes = $this->cargarLosDatosLazzyPorId($resultado);
		
		
		$this->setCodigoSucursal($objTransaccionPendientes->getObjGlnDestino()->getGln());
		$this->_anmatCliente = new FrenteSoap('anmat', $this->getCodigoSucursal());
		/*$arrDatosAlertar = Array('p_ids_transac' => $resultado
					    );*/
		$this->_anmatCliente->enviarAlertaTransacc($resultado);
		
		$ultimoEnvop = $this->_anmatCliente->getUltimoEnvio();
		//var_dump($ultimoEnvop);exit;
		$respuesta = $this->_anmatCliente->getUltimaRespuesta();
		//var_dump($this->_anmatCliente->getUltimaRespuesta());
		if($respuesta[0]->return->resultado == true){
		    //actualizamos el pendiente a confirmado
		    $this->_db_dM->addCamposUpdate('id_estado =4');
		    $this->_db_dM->addCamposUpdate('alertado=true');
		    $this->_db_dM->addCamposUpdate('alertado_user_id = ' . $_SESSION['usuarioId']);
		    $this->_db_dM->addCamposUpdate('alertado_fecha_hora =  now()');
		    $this->_db_dM->addFrom('transacciones_pendientes_anmat');
		    $this->_db_dM->addWhere('id_transaccion_anmat = ' . $resultado);
		    $this->_db_dM->generarUpdate();
		    //echo $this->_db_dM->getQry();exit;
		    $updPend = $this->_db_dM->ejecutar();
		    $arrAlertador[$resultado]['alertado'] = true;
		}else{
		    //grabar el error -- Rechazo anmat
		    $objTransaccionPendientes->grabarErrorAnmat($respuesta);
		}
	}
	
	$arrDevolver = array(
		    "soyError" => false,
		    "nivel" => 1,
		    "alertados" => $arrAlertador,
		    "mensaje" =>"Transacciones Enviadas"
		    );

	echo json_encode($arrDevolver);
    }
    public function setCodigoSucursal($gln){
    
	$this->_db_dM->addSelect('codigo_sucursal');
	$this->_db_dM->addFrom('datos_sucursales_usuarios_anmat');
	$this->_db_dM->addWhere('gln_sucursal = \'' . $gln . '\'');
	
	$this->_db_dM->generarSelect();
	$resultado = $this->_db_dM->ejecutar();
	
	if(count($resultado)==0){
	    echo "no existe";
	}else{
	    $this->_codigoSucursal = $resultado[0]['codigo_sucursal'];
	}
    
    }
    public function getCodigoSucursal(){
    
	return $this->_codigoSucursal;
    }
}
