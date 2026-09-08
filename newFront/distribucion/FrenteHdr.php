<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'DatoHojaDeRutaExtendido.php';

/*
 * dMELMAC
 * chava
 */

/**
 * Description of FrentePedidos
 *
 * @author by dMELMAC
 */

class FrenteHdr
{
    private $_db;
    private $_colObjDatoHdr= Array();

    private $_arrError = Array();

    //** NECESARIOS PARA EL FRENTE DE FRENTES//
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    private $_objFrenteError;

    //** FIN NECESIARIOS**//

    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
        $this->_objFrenteError = new FrenteError();
	$this->_objFuncionesComunes = new FuncionesComunes();
    }

    public function setColObjDatoHdr($obj){

        $this->_colObjDatoHdr[$obj->getHojaId()] = $obj;
    }

    public function getColObjDatoHdr(){
        return $this->_colObjDatoHdr;
    }

    public function cargarLosDatosLazzy($arrIds){

	if(is_array($arrIds)){
	    foreach($arrIds as $id){
		$ids.= $id['hoja_id'] . ",";
	   }
		$ids=substr($ids, 0, -1);
	}else{
		$ids=$arrIds;
	}
        $this->_db->addSelect("acompanante, to_char(hoja_fecha,'DD-MM-YYYY') AS hoja_fecha");
        $this->_db->addSelect("vehiculo_nombre AS vehiculo, hoja_id,carga,distribuidor_id,hoja_obs, sum(importe_cobrado) AS total");
        $this->_db->addFrom('datos_hojas_de_ruta');
        $this->_db->addFrom('INNER JOIN detalle_hojas_de_ruta de USING(hoja_id)');
        $this->_db->addFrom('INNER JOIN datos_facturas USING(factura_id)');
        $this->_db->addFrom('INNER JOIN datos_vehiculos USING(vehiculo_id)');
        $this->_db->addWhere("hoja_id IN (" . $ids . ")" );
        $this->_db->addWhere("hoja_estado_id IN (25,23,29)" );
        $this->_db->addGroup("acompanante,to_char(hoja_fecha,'DD-MM-YYYY'),vehiculo_nombre, hoja_id,carga,distribuidor_id,hoja_obs");
        $this->_db->addOrderBy('hoja_id DESC');

        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $resultado = $this->_db->ejecutar();

        $FrenteDistribuidores = NEW FrenteDistribuidores();

        $FrenteDistribuidores->cargarDistribuidoresLazzy($resultado);

        $arrObjDistribuidores= $FrenteDistribuidores->getColObjDistribuidor();


        foreach($resultado AS $datos){

	    $objDatosHdr= NEW DatoHojaDeRutaExtendido();
	    $objDatosHdr->cargarme($datos);
	    $objDatosHdr->setObjDistribuidor($arrObjDistribuidores[$datos[distribuidor_id]]);
	    $this->setColObjDatoHdr($objDatosHdr);
        }

    }

    public function cargarLosDetallesLazzy($arrIds){

	foreach($arrIds as $id){
	    $ids.= $id['hoja_detalle_id'] . ",";
	}

        $ids=substr($ids, 0, -1);
        $this->_db->addSelect('*');
        $this->_db->addFrom('detalle_hojas_de_ruta');
        $this->_db->addWhere("hoja_detalle_id IN (" . $ids . ")" );
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();

        $FrenteEstados = NEW FrenteEstados();
        $FrenteEstados->cargarEstadosLazzy($resultado);
        $arrObjEstados= $FrenteEstados->getColObjEstados();

        $FrenteOldEstados = NEW FrenteEstados();
        $FrenteOldEstados->cargarEstadosLazzy($resultado);
        $arrObjOldEstados= $FrenteOldEstados->getColObjEstados();

        /*$FrentePedidos = NEW FrentePedidos();
        $FrentePedidos->cargarLosDatosLazzy($resultado);
        $arrObjPedidos= $FrentePedidos->getColObjDatoPedido();*/

        $FrenteFacturas = NEW FrenteFacturas();
        $FrenteFacturas->cargarLosDatosLazzy($resultado);
        $arrObjFacturas= $FrenteFacturas->getColObjFacturas();


        foreach($this->getColObjDatoHdr() AS $objHdr){
			foreach($resultado AS $detalle){
				$objDetalleHdr= NEW DetalleHojaDeRutaExtendido();
				$objDetalleHdr->cargarme($detalle);
				$objDetalleHdr->setObjEstado($arrObjEstados[$detalle[hoja_estado_id]]);
				//$objDetalleHdr->setObjOldEstado($arrObjEstado[$detalle[hoja_old_estado_id]]);
				$objDetalleHdr->setObjFactura($arrObjFacturas[$detalle[factura_id]]);
				//var_dump($objDetalleHdr);
				$this->_colObjDatoHdr[$objHdr->getHojaId()]->setColObjDetalleHdr($objDetalleHdr);
			}
		}
    }


    public function cargarHdrCompleta($hojaId){

        $this->_db->addSelect('hoja_id, hoja_detalle_id');
		$this->_db->addFrom('datos_hojas_de_ruta INNER JOIN detalle_hojas_de_ruta USING(hoja_id)');
        $this->_db->addWhere('hoja_id = \'' . $hojaId . '\'');

        $this->_db->generarSelect();
		//echo $this->_db->getQry();exit;
        $resultadoCabeceras = $this->_db->ejecutar();

        $this->cargarLosDatosLazzy($resultadoCabeceras['0']['hoja_id']);
        $this->cargarLosDetallesLazzy($resultadoCabeceras);
    }


    public function buscarHdr($arrParametros){

	$this->_db->addSelect('hoja_id');
	$this->_db->addFrom('datos_hojas_de_ruta');
	$this->_db->addFrom('INNER JOIN detalle_hojas_de_ruta USING(hoja_id)');
	$this->_db->addFrom('INNER JOIN datos_facturas USING(factura_id)');


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
	if($arrParametros['distribuidores'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('distribuidor_id = \'' . $arrParametros[distribuidores] . '\'');
	}
	if($arrParametros['facturas'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('factura_id = \'' . $arrParametros[facturas] . '\'');
	}
	if($arrParametros['estados'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('hoja_estado_id = \'' . $arrParametros[estados] . '\'');
	}
	if($arrParametros['fechaDesde'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('hoja_fecha >= \'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros[fechaDesde]) . '\'');
	}
	if($arrParametros['fechaHasta'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('hoja_fecha <= \'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros[fechaHasta]) . '\'');
	}
	if($arrParametros['hojaAnulada']==true){
		$this->_db->addWhere("hoja_anulada = true" );
	}else{
		$this->_db->addWhere("hoja_anulada = false" );
	}

	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenHdr($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/

	//*INICIO LIMIT*//
	if($arrParametros['desdeBusqueda']==false){
	    $this->_db->addLimit(500);
	}


	$this->_db->addGroup('hoja_id');
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
	    $arrIds[$i]['hoja_id']= $resultado[$i]['hoja_id'];
	}
	//var_dump($arrIds);exit;
	$this->cargarLosDatosLazzy($arrIds);
    }

    public function getError(){
        return $this->_arrError;
    }

    //**PARA EL FRENTE**//
    public function setOrdenHdr($parametros){
	if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){
	    $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'hoja_id';
	    $this->_ordenColumnas['ordenarOrden']  =   'DESC';
	}
    }

    public function getOrdenHdr(){
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

    if($_SESSION['usuarioId']==1 || $_SESSION['usuarioId']==3){
        $editable=true;
    }else{
        $editable=false;
    }

	foreach($this->_colObjDatoHdr AS $objHdr){

		$arr []   =    array
		    (
			//id si o si un solo string (sin espacios en blanco)
			"id"            =>  $objHdr->getHojaId(),
			//define las herramientas
			"herramientas"   =>  array(
			    "editable"      =>  $editable,
			    "cancelable"    =>  true,
			    "detalles"      =>  true,
			    "imprimir"      =>  true,
			    "autogestion"      =>  false,
			    "enviarPedido"      =>  false

			),
			//las celdas propiamente dichas, en el orden en que estan declaradas las columnas
			"cell"          => array(
			    $objHdr->getHojaId(),
			    $objHdr->getHojaFechaHora(),
			    $objHdr->getObjDistribuidor()->getDistribuidorNombre(),
			    $objHdr->getAcompanante(),
			    $objHdr->getVehiculo(),
			    $objHdr->getCarga(),
			    $objHdr->getTotalHdr(),
			    $objHdr->getHojaObs()
			),
		    );

	}
	return $arr;
    }

    public function armarDetalles($hojaId){

	$this->cargarHdrCompleta($hojaId);

	foreach($this->getColObjDatoHdr() AS $objHdr){


		$propiedadesPedido =   array(
			array(
			    "display"   =>  'Codigo Interno',
			    "name"      =>  'hoja_id',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objHdr->getHojaId(),

			),
			array(
			    "display"   =>  'Fecha',
			    "name"      =>  'fecha_hora_pedido',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objHdr->getHojaFechaHora(),

			),
			array(
			    "display"   =>  'Distribuidor',
			    "name"      =>  'cliente_id',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objHdr->getObjDistribuidor()->getDistribuidorNombre(),

			),
			array(
			    "display"   =>  'Acompañanate',
			    "name"      =>  'cliente_id',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objHdr->getAcompanante(),

			),
			array(
			    "display"   =>  'Vehiculo',
			    "name"      =>  'cliente_id',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objHdr->getVehiculo(),

			),
			array(
			    "display"   =>  'Carga',
			    "name"      =>  'cliente_id',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objHdr->getCarga(),

			),
			array(
			    "display"   =>  'Observaciones',
			    "name"      =>  'hoja_obs',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objHdr->getHojaObs(),

			),
		    );
	}
	foreach($objHdr->getColObjDetalleHdr() AS $detalle){


	    $arr []   =    array
		(
		    //id si o si un solo string (sin espacios en blanco)
		    "id"            =>  $detalle->getHojaDetalleId(),
		    //define las herramientas
		    "herramientas"   =>  array(
			 "editable"      =>  false,
			  ),
			 "bloqueado"     => false,
		    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
		    "cell"          => array(
			$detalle->getObjFactura()->getFacturaNro(),
			$detalle->getObjFactura()->getObjCliente()->getClienteNombre(),
			$detalle->getObjEstado()->getEstadoNombre(),
			$detalle->getEstadoFechaHora()
		    ),
		);
	}

	$detallesPedido    =   array(
	    "bloqueable"=> false,
	    "modelo"    =>  array(
		array(

		    "display"   =>  'Remito',
		    "name"      =>  'producto_id',
		    "editable"  =>  false,
		    "class"	=>'',

		),
		array(
		    "display"   =>  'Cliente',
		    "name"      =>  'producto_id',
		    "editable"  =>  false,
		    "class"	=>'',

		),
		array(

		    "display"   =>  'Estado',
		    "name"      =>  'producto_presentacion',
		    "editable"  =>  false,
		    "class"	=>'',

		),
		array(
		    "display"   =>  'Fecha',
		    "name"      =>  'producto_gtin',
		    "editable"  =>  $arrParametros[edicion],
		    "class"	=>'',
		),

	    ),
	    "celdas"    =>  $arr
	);

	$array_devolver =   array(
	    "editable_cabecera" => false,
	    "propiedades"       =>  $propiedadesPedido,
	    "listadoDetalles"   =>  $detallesPedido,
	    "imprimir"		=>true
	);
	    return $array_devolver;
    }
    public function generarHdr($arrParametros){

	if($arrParametros[distribuidores]==null){
	    $arrDevolver = array(
			"soyError" => true,
			"nivel" => 1,
			"mensaje" =>'Debe elegir un distribuidor'
			);
	    echo json_encode($arrDevolver);exit;
	}
	$arrRemitos = explode ("||",$arrParametros[remitos]);
	array_pop($arrRemitos);


	$txtError = NULL;
        foreach ($arrRemitos as $data){
            $this->_db->addSelect('dm.remito_nro');
            $this->_db->addSelect('dehdr.hoja_estado_id');
            $this->_db->addSelect('dm.id_estado');
            $this->_db->addSelect('dhdr.hoja_anulada');
            $this->_db->addSelect('de1.estado_desc AS estado_nombre_pedido');
            $this->_db->addSelect('de2.estado_desc AS estado_nombre_hdr');
            $this->_db->addFrom('detalle_hojas_de_ruta dehdr');
            $this->_db->addFrom('INNER JOIN datos_hojas_de_ruta dhdr USING(hoja_id)');
            $this->_db->addFrom('INNER JOIN datos_movimientos dm USING (id_movimiento)');
            $this->_db->addFrom('INNER JOIN datos_estados de1 ON (dm.id_estado=de1.estado_id)');
            $this->_db->addFrom('INNER JOIN datos_estados de2 ON (dehdr.hoja_estado_id=de2.estado_id)');
            $this->_db->addWhere('dm.remito_nro = \'' . $data . '\'');
            $this->_db->addWhere("dhdr.hoja_anulada = false" );
            $this->_db->addOrderBy('dhdr.hoja_id desc LIMIT 1');
            $this->_db->generarSelect();
            //echo $this->_db->getQry();exit;
            $resultado =  $this->_db->ejecutar();
            if(count($resultado) > 0 ){
                if($txtError==NULL){
                    if($resultado[0]['hoja_estado_id'] == 23 || $resultado[0]['hoja_estado_id'] == 25 || $resultado[0]['hoja_estado_id'] == 26) {
                        $txtError.= "El estado del Remito Nro. " .  $resultado[0]['remito_nro'] . " en la ultima HDR (" .  $resultado[0]['estado_nombre_hdr']  . ") no es correcto para la carga en otra HdR, VERIFICAR. <br>";
                    }
                }
            }
        }
	if($txtError!=NULL){
            $arrDevolver = array(
                        "soyError" => TRUE,
                        "nivel" => 1,
                        "mensaje" => $txtError
                        );
            echo json_encode($arrDevolver);exit;
	    //avisar a ponzi que tiene que frenar y permitir eliminar el pedido que dio mal
        }

	$this->_db->addSelect('seq_datos_hojas_hoja_id');
	$this->_db->generarProximo();
	$resultado = $this->_db->ejecutar();

	//var_dump($resultado);

	$arrDato[] = Array(
			'hojaId' => $resultado[0][nextval],
			'distribuidorId' => $arrParametros['distribuidores'] ,
			'acompanante' => $arrParametros['acompanante'] ,
			'vehiculo' => $arrParametros['vehiculo'] ,
			'carga' => $arrParametros['carga'] ,
			'observaciones' => $arrParametros['observaciones'] ,
			'hojaUsuarioId' => $_SESSION['usuarioId']
		);
	foreach($arrDato as $parametros){
	    $objDato = new DatoHojaDeRutaExtendido();
	    $arrQry['dato'] = $arrQry['dato'] . $objDato->salvarme($parametros);
	    //var_dump($arrQry['dato']);exit;
	}
	$i=0;
	foreach($arrRemitos AS $remito){
	    $this->_db->addSelect('seq_detalle_hoja_detalle_id');
	    $this->_db->generarProximo();
	    $detalleId = $this->_db->ejecutar();

	    $qry="SELECT id_movimiento, factura_id, factura_faltan_fact,id_estado FROM datos_facturas WHERE factura_nro='$remito';";
	    $this->_db->setQry($qry);
	    $resultadoData = $this->_db->ejecutar();

	    if($resultadoData['0']['id_estado']==29){
            $soyDeuda=true;
	    }else{
            $soyDeuda=0;
	    }

	    $arrDetalle[] = Array(
		    'hojaId' => $resultado[0][nextval],
		    'hojaEstadoId' => 23 ,
		    'hojaEstadoUsuarioId'=>$_SESSION['usuarioId'],
		    'remitoId' => $resultadoData[0][factura_id],
		    'idMovimiento'=>$resultadoData[0][id_movimiento],
		    'importe'=>$resultadoData[0][factura_faltan_fact],
		    'soyDeuda'=>$soyDeuda,
		    'detalleHojaId' =>$detalleId[0][nextval]
	    );
	    $objDetalle = new DetalleHojaDeRutaExtendido();
	    $arrQry['detalle'].= $objDetalle->salvarme($arrDetalle[$i]);
	    $arrQry['log'] .= "SELECT fn_log_historico_estados_pedidos({$resultado[0][nextval]},{$resultadoData[0][id_movimiento]},23,{$_SESSION['usuarioId']});";
	    $i++;
	}
	$qryFinal = $arrQry['detalle'] . $arrQry['dato'] . $arrQry['log'];
	//echo $qryFinal;exit;
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
				"alertados" => false,
				"mensaje" => "Hoja de ruta generada!"
				);
	}
	echo json_encode($arrDevolver);exit;
    }

    public function actualizar($arrParametros){


	$arrRemitos = explode ("||",$arrParametros[productos]);
	array_pop($arrRemitos);

        $txtError = NULL;
        foreach ($arrRemitos as $data){
            $arrData = explode("#",$data);

            //SI PAGO NO HAY IMPORTE CARGADO
            if($arrData[0] == 25 || $arrData[0] == 27 || $arrData[0] == 26){
                list( $estadoId,$formaId,$remitoNro) = explode("#",$data);
            }else{
                //var_dump($data);exit;
                list( $estadoId,$formaId,$importe,$remitoNro) = explode("#",$data);

            }


            $this->_db->addSelect('dm.remito_nro');
            $this->_db->addSelect('dm.id_movimiento');
            $this->_db->addSelect('dehdr.hoja_estado_id');
            $this->_db->addSelect('dm.id_estado');
			$this->_db->addSelect('dm.movimiento_total_fact');
            $this->_db->addSelect('dhdr.hoja_anulada');
            $this->_db->addSelect('dehdr.hoja_detalle_id');
            $this->_db->addSelect('de1.estado_desc AS estado_nombre_pedido');
            $this->_db->addSelect('de2.estado_desc AS estado_nombre_hdr');
            $this->_db->addFrom('detalle_hojas_de_ruta dehdr');
            $this->_db->addFrom('INNER JOIN datos_hojas_de_ruta dhdr USING(hoja_id)');
            $this->_db->addFrom('INNER JOIN datos_movimientos dm USING (id_movimiento)');
            $this->_db->addFrom('INNER JOIN datos_estados de1 ON (dm.id_estado=de1.estado_id)');
            $this->_db->addFrom('INNER JOIN datos_estados de2 ON (dehdr.hoja_estado_id=de2.estado_id)');
            $this->_db->addWhere('dm.remito_nro = \'' . $remitoNro . '\'');
            $this->_db->addWhere("dhdr.hoja_anulada = false" );
            $this->_db->addOrderBy('dhdr.hoja_id desc LIMIT 1');
            $this->_db->generarSelect();
            //echo $this->_db->getQry();exit;
            $resultado =  $this->_db->ejecutar();
            if($resultado[0]['hoja_estado_id'] == 25) {
                $txtError.= "El estado del Remito Nro. " .  $remitoNro . " en la HDR es \" " .  $resultado[0]['estado_nombre_hdr']  . " \" , no es posible actualizarlo <br>";
            }
            if($resultado[0]['hoja_estado_id'] == 29 && $estadoId==25){
				$arrParametros['importeCobrado']= $resultado[0]['movimiento_total_fact'];
			}
            $arrParametros['id']= $resultado[0][hoja_detalle_id];
            $arrParametros['hojaEstadoId']= $estadoId;
			$arrParametros['formaId']= $formaId;
            $arrParametros['idMovimiento']= $resultado['0']['id_movimiento'];
            $objDetalleHdr = NEW DetalleHojaDeRutaExtendido();
            $arrQry['detalle'].= $objDetalleHdr->actualizarme($arrParametros);
			$arrParametros['importeCobrado']=null;
            //registro movimiento y de paso actualizo el estado en factura y movimiento
            $arrQry['log'] .= "SELECT fn_log_historico_estados_pedidos({$resultado[0]['hoja_detalle_id']},{$resultado['0']['id_movimiento']},{$estadoId},{$_SESSION['usuarioId']});";
            //ACTUALIZAMOS FACTURA
            if($estadoId==25){
                $qryFactura.="UPDATE datos_facturas SET factura_paga=true, factura_faltan_fact=0 WHERE factura_nro = '$remitoNro';";
            }
            if($estadoId==29){
                $qryFactura.="UPDATE datos_facturas SET factura_faltan_fact=factura_faltan_fact -$importe WHERE factura_nro = '$remitoNro';";
                $qryFactura.="UPDATE detalle_hojas_de_ruta SET importe_cobrado =  $importe WHERE hoja_detalle_id=$arrParametros[id];";
                //echo $qryFactura;exit;

            }
            //SI ES RECHAZO
            if($estadoId==26){
            $qryMovimiento="SELECT * FROM detalle_movimientos WHERE id_movimiento= {$resultado[0][id_movimiento]};";
            //echo $qryMovimiento;exit;
            $this->_db->setQry($qryMovimiento);
            $resultadoMovimiento = $this->_db->ejecutar();
                foreach($resultadoMovimiento AS $detalle){
                    $qryAnulacion.= ' SELECT fn_actualizar_stock_completo('. $detalle['cantidad'] * -1 . ',' . $detalle['lote_id'] . ',' . $detalle['producto_id'] . '); ';
                }
                $qryAnulacion.="UPDATE datos_movimientos SET anulado=true, anulado_fecha_hora=now(),obs='ANULADO POR HDR' WHERE id_movimiento={$resultado[0][id_movimiento]};";
                $qryAnulacion.="UPDATE datos_facturas SET factura_cancelada = true, factura_cancelada_fecha_hora=now() WHERE factura_nro = '$remitoNro';";
            }
        }

	if($txtError!=NULL){
            $arrDevolver = array(
                        "soyError" => TRUE,
                        "nivel" => 1,
                        "mensaje" => $txtError
                        );
            echo json_encode($arrDevolver);exit;
	    //avisar a ponzi que tiene que frenar y permitir eliminar el pedido que dio mal
        }

	$qryFinal = $arrQry['detalle'] . $arrQry['log'] . $qryFactura . $qryAnulacion;
	//echo $qryFinal;exit;
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
				"alertados" => false,
				"mensaje" => "Hoja de ruta actualizada!"
				);
	}
	echo json_encode($arrDevolver);exit;
    }
    public function cancelar($id){
        $this->_db->addCamposUpdate('hoja_anulada = true');
        $this->_db->addCamposUpdate('hoja_anulada_fecha_hora = now()');
        $this->_db->addCamposUpdate('hoja_anulada_usuario_id = \'' . $_SESSION['usuarioId'] .'\'');
        $this->_db->addFrom('datos_hojas_de_ruta');
        $this->_db->addWhere('hoja_id = \'' . $id .'\'');

        $this->_db->generarUpdate();

        $this->_db->ejecutar();

        if(count($this->_db->getArrError()) > 0){
            $arrDevolver = array(
                    "soyError" => true,
                    "nivel" => 1,
                    "alertados" => $arrAlertador,
                    "mensaje" => "Error al cancelar!!!!"
                    );
        }else{

            $arrDevolver = array(
                    "soyError" => false,
                    "nivel" => 1,
                    "alertados" => $arrAlertador,
                    "mensaje" => "Hoja cancelada!!!!"
                    );
        }
        echo json_encode($arrDevolver);exit;
    }
    public function actualizarMe($arrParametros){

        //var_dump($arrParametros);
        $this->_db->addCamposUpdate('hoja_obs = \'' . $arrParametros[campos][hoja_obs] .'\'');
        $this->_db->addFrom('datos_hojas_de_ruta');
        $this->_db->addWhere('hoja_id = \'' . $arrParametros[id] .'\'');

        $this->_db->generarUpdate();

        //echo $this->_db->getQry();exit;
        $this->_db->ejecutar();

        if(count($this->_db->getArrError()) > 0){
            $arrDevolver = array(
                    "soyError" => true,
                    "nivel" => 1,
                    "alertados" => $arrAlertador,
                    "mensaje" => "Error al cancelar!!!!"
                    );
        }else{

            $arrDevolver = array(
                    "soyError" => false,
                    "nivel" => 1,
                    "alertados" => $arrAlertador,
                    "mensaje" => "Hoja cancelada!!!!"
                    );
        }
        echo json_encode($arrDevolver);exit;
    }
}
