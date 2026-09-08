<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/administracion/DatoFacturaElectronicaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/administracion/DetalleFacturaElectronicaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/afipPropia/facturaElectronica.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/afipPropia/ncElectronica.php';

/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteFacturasElectronicas
{
    private $_colFactura = Array();
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    private $_db;

    public function __construct(){
        $this->_objFuncionesComunes = new FuncionesComunes();
        $this->_db = NEW FrenteAlmacenamiento('dmelmac');
    }
    public function cargarFactura($id){
        $this->_colFactura[$id] = NEW DatoFacturaElectronicaExtendido();
        $this->_colFactura[$id]->cargarMe($id);
    }
    public function setColObjFacturas($objFactura){
        $this->_colFactura[$objFactura->getFacturaId()] = $objFactura;
    }
    public function getColObjFacturas(){
        return $this->_colFactura;
    }
    public function cargarLosDatosLazzy($arrIds){
    	$ids = "";
    	if(is_array($arrIds)){
            foreach($arrIds as $id){
                $ids.= "'" . $id['factura_id'] . "',";
            }
            $ids=substr($ids, 0, -1);
            $this->_db->addWhere("factura_id IN (" . $ids . ")" );
        }else{
            $this->_db->addWhere("factura_id IN (" . $arrIds . ")" );
        }

        $this->_db->addSelect('*');
        $this->_db->addSelect('to_char(factura_fecha,\'DD-MM-YY\') as factura_fecha');
        $this->_db->addSelect('CASE WHEN factura_paga THEN \'SI\' ELSE \'NO\' END AS factura_paga');
        $this->_db->addFrom('datos_facturas_electronicas');
        $this->_db->AddOrderBy('factura_id DESC');

        $this->_db->generarSelect();

        $resultado = $this->_db->ejecutar();

        $FrenteClientes= NEW FrenteClientes();
        $FrenteClientes->cargarClientesLazzy($resultado);
        $arrObjClientes= $FrenteClientes->getColObjCliente();


        foreach($resultado AS $detalle){
            $objFactura = NEW DatoFacturaElectronicaExtendido();
            $objFactura->cargarMe($detalle);
            $objFactura->setObjCliente($arrObjClientes[$detalle['cliente_id']]);
            $this->setColObjFacturas($objFactura);
        }
    }
    public function cargarLosDetallesLazzy($arrIds){
        $ids = "";
        foreach($arrIds as $id){
            $ids.= $id['factura_id'] . ",";
        }
        $ids=substr($ids, 0, -1);
        $this->_db->addSelect('*');
        $this->_db->addFrom('detalle_facturas_electronicas');
        $this->_db->addWhere("factura_id IN (" . $ids . ")" );
        $this->_db->generarSelect();

        $resultado = $this->_db->ejecutar();

        $FrenteProductos= NEW FrenteProductos();
        $FrenteProductos->cargarProductosLazzy($resultado);
        $arrObjProductos= $FrenteProductos->getColProductos();

        foreach($this->getColObjFacturas() AS $objFactura){
            foreach($resultado AS $detalle){
                $objDetalle = NEW DetalleFacturaElectronicaExtendido();
                $objDetalle->cargarme($detalle);
                $objDetalle->setObjProductos($arrObjProductos[$detalle['producto_id']]);
                $this->_colFactura[$objFactura->getFacturaId()]->setColObjDetalleFactura($objDetalle);
	        }
	    }
    }

    public function cargarFacturaCompleta($id){
        $this->_db->addSelect('factura_id');
        $this->_db->addFrom('datos_facturas_electronicas INNER JOIN detalle_facturas_electronicas USING (factura_id)');
        $this->_db->addWhere('factura_id = \'' . $id . '\'');

        $this->_db->generarSelect();
        $resultado = $this->_db->ejecutar();

        $this->cargarLosDatosLazzy($resultado);
        $this->cargarLosDetallesLazzy($resultado);
    }

    public function buscarFacturas($arrParametros) {
        $this->_db->addSelect('factura_id');
        $this->_db->addFrom('datos_facturas_electronicas');
        $this->_db->addFrom('INNER JOIN datos_clientes USING (cliente_id)');
        $this->_db->addFrom('LEFT JOIN datos_vendedores USING (vendedor_id)');

        if(isset($arrParametros['stringBuscar']) && $arrParametros['stringBuscar']){
            $this->_db->addWhere('factura_nro ILIKE  \'%' . $arrParametros['stringBuscar'] .'%\'');
            $primerRegistro = 0;
            $ultimoRegistro = 11;
        }else{
            $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
        }

        if($arrParametros['clientes'] !=null && $arrParametros['desdeAlta'] == null){
            $this->_db->addWhere('cliente_id = \''  . $arrParametros['clientes'] . '\'');
        }
        if($arrParametros['facturas'] !=null && $arrParametros['desdeAlta'] == null){
            $this->_db->addWhere('factura_nro = \''  . $arrParametros['facturas'] . '\'');
	    }
	    if($arrParametros['vendedores'] !=null && $arrParametros['desdeAlta'] == null){
            $this->_db->addWhere('datos_clientes.vendedor_id = \''  . $arrParametros['vendedores'] . '\'');
        }
        if($arrParametros['facturaPaga'] !=null){
            $this->_db->addWhere('factura_paga = \''  . $arrParametros['facturaPaga'] . '\'');
        }
        if($arrParametros['fechaDesde'] !=null && $arrParametros['desdeAlta']==null){
            $this->_db->addWhere('factura_fecha >= \'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros['fechaHasta']) . '\'');
        }
        if($arrParametros['fechaHasta'] !=null && $arrParametros['desdeAlta']==null){
            $this->_db->addWhere('factura_fecha <= \'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros['fechaHasta']) . '\'');
        }
        $this->_db->addWhere('factura_cancelada = false');
        if(isset($arrParametros['proforma']) && $arrParametros['proforma']==true){
            $this->_db->addWhere('refacturada=false');
            $this->_db->addWhere("factura_letra ='X'");
        }

        $this->setOrdenFacturas($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas['ordenarPor'] ." ". $this->_ordenColumnas['ordenarOrden']);

        if($arrParametros['tipo']=='facturas'){
            $this->_db->addLimit(100);
        }
        $this->_db->generarSelect();
        $arr =  $this->_db->ejecutar();

        $this->setTotal(count($arr));
        $this->setPagina($arrParametros['pagina']);

        if($this->getTotal() < $ultimoRegistro){
            $ultimoRegistro = $this->getTotal();
        }
        $arrIds = array();
        for($i=$primerRegistro;$i<$ultimoRegistro;$i++){
            $arrIds[$i]['factura_id'] = $arr[$i]['factura_id'];
        }
        $this->cargarLosDatosLazzy($arrIds);
    }
    public function buscarProforma($arrParametros){
        $arrParametros['proforma']=true;
        $this->buscarFacturas($arrParametros);
    }
    public function setOrdenFacturas($arrParametros){
        if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){
            $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
        }else{
            $this->_ordenColumnas['ordenarPor']    = 'factura_id';
            $this->_ordenColumnas['ordenarOrden']  =   'desc';
        }
    }
    public function getOrdenFacturas(){
        return $this->_ordenColumnas;
    }
    public function setTotal($total){
        $this->_total = $total;
    }
    public function getTotal(){
        return $this->_total;
    }
    public function setPagina($pagina=1){
        $this->_pagina = $pagina;
    }
    public function getPagina(){
        return $this->_pagina;
    }
    public function getDetalles(){
        $arr = array();
        foreach($this->_colFactura AS  $objFactura){
	        $arr []   =    array
            (
                "id"            =>  $objFactura->getFacturaId(),
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  false,
                    "detalles"      =>  true,
                    "imprimir"      =>  true,
                ),
                "cell"          => array(
                    $objFactura->getFacturaFecha(),
                    $objFactura->getFacturaNro(),
                    $objFactura->getObjCliente()->getClienteNombre(),
                    $this->_objFuncionesComunes->formatoMoneda($objFactura->getFacturaTotal()),
                    $this->_objFuncionesComunes->formatoMoneda($objFactura->getFacturaIva()),
		            $this->_objFuncionesComunes->formatoMoneda($objFactura->getFacturaFaltanFact()),
		            $objFactura->getDescuento(),
                    $objFactura->getFacturaPaga(),
                    $objFactura->getFacturaObs()
                ),
            );
	    }
	    return $arr;
    }
    public function getDetallesAutocompletar(){
        $arr = array();
        foreach($this->_colFactura AS $factura){
            $arr[]   =   array(
                'label' => $factura->getObjCliente()->getClienteNombre() . "-".  $factura->getFacturaNro(). "-". $this->_objFuncionesComunes->formatoMoneda($factura->getFacturaTotal()),
                'value' =>  $factura->getFacturaId()
                );
        }
        return $arr;
    }
    public function getNroFactura($letra){
        if($letra['stringBuscar']!='R' && $letra['stringBuscar']!='r'){
            $qry="SELECT last_value + 1 AS nro,'" .   $letra['stringBuscar'] . "' AS letra FROM seq_datos_factura_electronica_nro_" . $letra['stringBuscar'] .";";
        }else{
            $qry="SELECT last_value + 1 AS nro, 'R' as letra FROM seq_remito_nro";
        }
        $this->_db->setQry($qry);

        $resultado = $this->_db->ejecutar();
        $arr = array();
        foreach($resultado AS $factura){
            $arr[]   =   array(
                'label' =>  $factura['letra'] . "0002-" . str_pad($factura['nro'],8,"0",STR_PAD_LEFT),
                'value' =>  $factura['letra']
                );
        }
        return $arr;
    }
    public function corregirNumeracion($parametros){
        if(strtolower($parametros['facturaLetra'])=='r'){
            $this->_db->addCamposTabla('seq_remito_nro');
        }else{
            $this->_db->addCamposTabla("seq_datos_factura_electronica_nro_". strtolower($parametros['facturaLetra']));
        }
        $this->_db->addCamposValue($parametros['valor']);
        $this->_db->setearSecuencia();

        $resultado = $this->_db->ejecutar();

        if(count($resultado)>0){
            $arrDevolver = array("soyError" => false, "nivel" => 1, "mensaje" => "Actualizado!!!!");
        }else{
            $arrDevolver = array("soyError" => true, "nivel" => 1, "mensaje" => "Error al actualizar");
        }
	    echo json_encode($arrDevolver);exit;
    }
    public function armarDetalles($id){
        $this->cargarFacturaCompleta($id);
        $propiedades = array();
        foreach($this->_colFactura AS $objFactura){
            $propiedades =   array(
                array("display" => 'Fecha', "name" => 'cliente_calle', "editable" => true, "class" =>'', "value" => $objFactura->getFacturaFecha()),
                array("display" => 'Cliente', "name" => 'cliente_altura', "editable" => true, "class" =>'', "value" => $objFactura->getObjCliente()->getClienteNombre()),
                array("display" => 'Factura Nro', "name" => 'cliente_piso', "editable" => true, "class" =>'', "value" => $objFactura->getFacturaNro()),
                array("display" => 'Factura Total', "name" => 'cliente_piso', "editable" => true, "class" =>'', "value" => $this->_objFuncionesComunes->formatoMoneda($objFactura->getFacturaTotal())),
                array("display" => 'Factura Iva', "name" => 'cliente_piso', "editable" => true, "class" =>'', "value" => $this->_objFuncionesComunes->formatoMoneda($objFactura->getFacturaIva())),
                array("display" => 'Factura Pendiente', "name" => 'cliente_piso', "editable" => true, "class" =>'', "value" => $this->_objFuncionesComunes->formatoMoneda($objFactura->getFacturaFaltanFact())),
		    );
        }

        $arr = array();
        foreach($objFactura->getColObjDetalleFactura() AS $detalle){
            $arr []   =    array
                (
                "id"            =>  $detalle->getDetalleFacturaId(),
                "herramientas"   =>  array("editable" => false),
                "cell"          => array(
                    $detalle->getObjProductos()->getProductoNombre() . " " . $detalle->getObjProductos()->getProductoPresentacion(),
                    $detalle->getCantidad(),
                    $detalle->getRemitoNro(),
                    $this->_objFuncionesComunes->formatoMoneda($detalle->getImporteDetalle()),
                    $this->_objFuncionesComunes->formatoMoneda($detalle->getImporteIva()),
                    $this->_objFuncionesComunes->formatoMoneda($detalle->getImporteUnitario()),
                ),
		    );
        }

	$detalles=   array(
	    "modelo"    =>  array(
            array("display" => 'Producto', "name" => 'peso_bruto', "class" => '', "editable" => false),
            array("display" => 'Cantidad', "name" => 'tara', "class" => '', "editable" => false),
            array("display" => 'Nro. Remito', "name" => 'peso_neto', "class" => '', "editable" => false),
            array("display" => 'Total', "name" => 'peso_neto', "class" => '', "editable" => false),
            array("display" => 'Iva', "name" => 'peso_neto', "class" => '', "editable" => false),
            array("display" => 'Precio Unitario', "name" => 'peso_neto', "class" => '', "editable" => false),
	    ),
	    "celdas"    =>  $arr
	);
	return array(
	    "editable_cabecera" => false,
	    "propiedades"       =>  $propiedades,
	    "listadoDetalles"   =>  $detalles
        );
    }

    public function generarFactura($parametros){
        $qry = array('proforma' => '', 'detalle' => '', 'dato' => '', 'remito' => '', 'cta_cte' => '', 'facturaElectronica' => '', 'pedido' => '');
        $arrData = array('refacturada' => false, 'refacturadaId' => null);

        if(is_array($parametros)==true){
            $arrData = $this->refacturarProforma($parametros);
            $qry['proforma'] =  $arrData['qry'];
            $remitoId=$arrData['remitoId'];
            $idPedido = $arrData['pedidoId'];
        }else{
            $idPedido = $parametros;
            $qryChequeo="SELECT remito_id FROM datos_remitos INNER JOIN detalle_remitos USING(remito_id) WHERE pedido_id = $idPedido AND remito_facturado=false AND remito_cancelado=false;";
            $this->_db->setQry($qryChequeo);
            $resultado = $this->_db->ejecutar();

            if(count($resultado)==0){
                echo json_encode(array("soyError" => true, "nivel" => 1, "mensaje" => "Remito no esta en condiciones de facturar!!!!"));exit;
            }else{
                $remitoId=$resultado[0]['remito_id'];
            }
        }
	    $this->_db->addCamposTabla('seq_datos_factura_electronica_id');
        $this->_db->generarProximo();

        $id = $this->_db->ejecutar();
        $facturaId = $id[0]['nextval'];

        $FrenteRemitos = NEW FrenteRemitos();
        $FrenteRemitos->cargarRemitoCompleto($remitoId);

        $arrDetalle = array();
        foreach ($FrenteRemitos->getColObjRemitos() AS $objRemito){
            if ($objRemito->getObjCliente()->getCondicionIvaId()==1 || $objRemito->getObjCliente()->getCondicionIvaId()==6 ){
                $arrDetalle['facturaLetra']='A';
                $arrDetalle['facturaTipo']='1';
            }else{
                $arrDetalle['facturaLetra']='B';
                $arrDetalle['facturaTipo']='6';
            }

            if($objRemito->getFacturar()==true || $arrData['refacturada']==true){
                $arrDetalle['facturaElectronica']=true;
                $arrDetalle['facturaPuestoVenta']=5;
            }else{
                $arrDetalle['facturaElectronica']=false;
                $arrDetalle['facturaPuestoVenta']=1;
            }
            $arrDetalle['facturar']=$objRemito->getFacturar();
        }

        foreach($FrenteRemitos->getColObjRemitos() AS $objRemito){
            if($objRemito->getDescuento()>0){
                $arrDetalle['descuento']=$objRemito->getDescuento();
            }
            foreach($objRemito->getColObjDetalleRemito() AS $objDetalle){
                $arrDetalle['facturaId'] = $facturaId;
                $arrDetalle['remitoId'] = $remitoId;
                $arrDetalle['productoId'] = $objDetalle->getProductoId();
                $arrDetalle['cantidad'] = $objDetalle->getCantidad();
                $arrDetalle['remitoNro'] = $objRemito->getRemitoNro();
                $arrDetalle['importeUnitario'] = $objDetalle->getImporteUnitario();
                $arrDetalle['productoPventa'] = $objDetalle->getProductoPventa();
                $arrDetalle['productoPcosto'] = $objDetalle->getProductoPcosto();
                if($objDetalle->getDescuento()>0){
                    $arrDetalle['descuento'] = $objDetalle->getDescuento();
                }
                $arrDetalle['importeDetalle'] = $objDetalle->getImporteDetalle() + $objDetalle->getIvaDetalle();
                $arrDetalle['importeIva'] = $objDetalle->getIvaDetalle();

                $objDetalleFact = NEW DetalleFacturaElectronicaExtendido();
                $qry['detalle'].= $objDetalleFact->salvarMe($arrDetalle);
            }
            $arrDetalle['facturaTotal'] = $objRemito->getRemitoTotal() + $objRemito->getRemitoIva();
            $arrDetalle['facturaIva'] = $objRemito->getRemitoIva();
            $arrDetalle['clienteId']= $objRemito->getClienteId();
            $arrDetalle['pedidoId']= $objRemito->getPedidoId();
            $qry['remito'].= $objRemito->actualizarme($arrDetalle);
        }
        $arrDetalle['refacturadaId'] = $arrData['refacturadaId'];
	    $arrDetalle['facturaFecha']= $this->_objFuncionesComunes->fechaFormatoDb($parametros['facturaFecha']);
	    $arrDetalle['observaciones']= $parametros['observaciones'];
	    $objDato = NEW DatoFacturaElectronicaExtendido();
	    $qry['dato'] = $objDato->salvarMe($arrDetalle);

        $this->_db->addCamposTabla('cliente_id');
        $this->_db->addCamposValue('\'' . $arrDetalle['clienteId'] .'\'');
        $this->_db->addCamposTabla('factura_id');
        $this->_db->addCamposValue('\'' . $arrDetalle['facturaId'] .'\'');
        $this->_db->addCamposTabla('importe_deuda');
        $this->_db->addCamposValue('\'' . $arrDetalle['facturaTotal'] .'\'');
        $this->_db->addCamposTabla('usuario_id');
        $this->_db->addCamposValue('\'' . $_SESSION['usuarioId'] .'\'');
        $this->_db->addFrom('cta_cte_clientes');

	    $this->_db->generarInsert();
	    $qry['cta_cte'] = $this->_db->getQry();

	    if($arrDetalle['facturaElectronica']==true){
            $this->_db->addCamposTabla('factura_id');
            $this->_db->addCamposValue('\'' . $arrDetalle['facturaId'] .'\'');
            $this->_db->addFrom('datos_facturas_afip_cae');
            $this->_db->generarInsert();

            $qry['facturaElectronica'] = $this->_db->getQry();
	    }
	    $qry['pedido'] ="UPDATE datos_pedidos SET pedido_facturado=true WHERE pedido_id =$idPedido;";

        $qryFinal = $qry['proforma'] . $qry['detalle'] . $qry['dato'] . $qry['remito'] . $qry['cta_cte'] . $qry['facturaElectronica'] . $qry['pedido'];

	    $this->_db->setQry($qryFinal);
	    $this->_db->ejecutarTransaccion();

	    if(count($this->_db->getArrError()) > 0){
	        $arrDevolver = array("soyError" => true, "nivel" => 1, "mensaje" => "Error en la carga!!!!");
	    }else{
            if($arrDetalle['facturaElectronica']==true){
                $facturaElectronica = new facturaElectronica();
                if(!$facturaElectronica->enviarFactura($arrDetalle['facturaId'])){
                    echo json_encode(array("soyError" => true, "nivel" => 1, "mensaje" => "Error en envio de factura electronica!!!!"));exit;
                }
            }
	        $arrDevolver = array("soyError" => false, "nivel" => 1, "mensaje" => "Factura generada!!!!");
	    }
	    echo json_encode($arrDevolver);exit;
    }
    public function cancelarFactura($id){
	    $this->_db->addCamposUpdate('factura_cancelada = true');
        $this->_db->addCamposUpdate('factura_cancelada_fecha_hora = now()');
        $this->_db->addCamposUpdate('factura_cancelada_usuario_id = \'' . $_SESSION['usuarioId'] .'\'');
        $this->_db->addFrom('datos_facturas_electronicas');
        $this->_db->addWhere('factura_id = \'' . $id .'\'');

        $this->_db->generarUpdate();
        $this->_db->ejecutar();

        if(count($this->_db->getArrError()) > 0){
            $arrDevolver = array("soyError" => true, "nivel" => 1, "mensaje" => "Error al cancelar!!!!");
        }else{
            $arrDevolver = array("soyError" => false, "nivel" => 1, "mensaje" => "Factura cancelada!!!!");
        }
        echo json_encode($arrDevolver);exit;
    }
    public function generarNC($arrParametros){
        $qry="SELECT producto_id, cantidad, producto_iva FROM datos_movimientos INNER JOIN detalle_movimientos USING(id_movimiento) INNER JOIN datos_productos USING(producto_id) WHERE id_movimiento ={$arrParametros['idMovimiento']} AND tipo_movimiento_id =3 AND anulado=false;";

        $this->_db->setQry($qry);
        $resultado = $this->_db->ejecutar();
        $totalFactura=0;
        $totalIva=0;

        $this->_db->addCamposTabla('seq_datos_factura_electronica_id');
        $this->_db->generarProximo();

        $id = $this->_db->ejecutar();
        $facturaId = $id[0]['nextval'];

        $arrQry = array('detalle' => '', 'dato' => '', 'facturaElectronica' => '', 'pendiente' => '', 'cta_cte' => '');
        foreach ($resultado AS $movimiento){
            $qry="SELECT detalle_facturas_electronicas.descuento,importe_unitario, producto_pventa, remito_nro, factura_total_fact, factura_faltan_fact, factura_nro FROM detalle_facturas_electronicas INNER JOIN datos_facturas_electronicas USING(factura_id) WHERE factura_id={$arrParametros['facturaId']} AND producto_id = {$movimiento['producto_id']};";
            $this->_db->setQry($qry);
            $resultadoFactura = $this->_db->ejecutar();

            foreach ($resultadoFactura AS $detalle){
                $arrFinal['facturaId'] = $facturaId;
                $arrFinal['importeUnitario'] = $detalle['importe_unitario'];
                $arrFinal['productoPventa'] = $detalle['producto_pventa'];
                $arrFinal['descuento'] = $detalle['descuento'];
                $arrFinal['cantidad'] = $movimiento['cantidad'];
                $arrFinal['productoId'] = $movimiento['producto_id'];
                $arrFinal['remitoNro'] = $detalle['remito_nro'];
                if($movimiento['producto_iva'] == 21){
                    $arrFinal['importeDetalle'] = ($detalle['importe_unitario'] * $movimiento['cantidad']) * 1.21;
                    $arrFinal['importeIva'] = $arrFinal['importeDetalle'] - ($detalle['importe_unitario'] * $movimiento['cantidad']);
                }else{
                    $arrFinal['importeDetalle'] = ($detalle['importe_unitario'] * $movimiento['cantidad']) * 1.105;
                    $arrFinal['importeIva'] = $arrFinal['importeDetalle'] - ($detalle['importe_unitario'] * $movimiento['cantidad']);
                }
                $totalFactura = $totalFactura + $arrFinal['importeDetalle'];
                $totalIva = $totalIva + $arrFinal['importeIva'];
                $objDetalle = NEW DetalleFacturaElectronicaExtendido();
                $arrQry['detalle'].= $objDetalle->salvarMe($arrFinal);
            }
        }

        $arrNC = array();
        if($arrParametros['facturaPuestoVenta']==5){
            $arrNC['facturaElectronica']=true;
            if($arrParametros['facturaLetra']=='A'){
                $arrNC['facturaTipo']='3';
                $arrNC['facturaLetra']='A';
            }else{
                $arrNC['facturaLetra']='B';
                $arrNC['facturaTipo']='8';
            }
        }else{
            $arrNC['facturaLetra']='X';
            $arrNC['facturaTipo']='8';
        }
        $arrNC['deFacturaId'] = $arrParametros['facturaId'];
        $arrNC['facturaId'] = $facturaId;
        $arrNC['clienteId'] = $arrParametros['clienteId'];
        $arrNC['facturaPuestoVenta'] = $arrParametros['facturaPuestoVenta'];
        $arrNC['idMovimiento'] = $arrParametros['idMovimiento'];
        $arrNC['facturaTotal'] = $totalFactura;
        $arrNC['facturaIva'] = $totalIva;

        $objDato = NEW DatoFacturaElectronicaExtendido();
        $arrQry['dato'] = $objDato->salvarMe($arrNC);

        if($arrNC['facturaElectronica']==true){
            $this->_db->addCamposTabla('factura_id');
            $this->_db->addCamposValue('\'' . $facturaId .'\'');
            $this->_db->addFrom('datos_facturas_afip_cae');
            $this->_db->generarInsert();

            $arrQry['facturaElectronica'] = $this->_db->getQry();
        }
        if($resultadoFactura[0]['factura_total_fact'] == $totalFactura){
            $arrQry['pendiente'] = "UPDATE datos_facturas_electronicas SET factura_faltan_fact = 0, factura_paga=true, a_factura_id = {$arrNC['facturaId']}  WHERE factura_id ={$arrParametros['facturaId']};";
        }else{
            $arrQry['pendiente'] = "UPDATE datos_facturas_electronicas SET factura_faltan_fact = factura_faltan_fact - $totalFactura, a_factura_id = {$arrNC['facturaId']}   WHERE factura_id ={$arrParametros['facturaId']};";
        }

        $this->_db->addCamposTabla('cliente_id');
        $this->_db->addCamposValue('\'' . $arrNC['clienteId'] .'\'');
        $this->_db->addCamposTabla('factura_id');
        $this->_db->addCamposValue('\'' . $arrNC['facturaId'] .'\'');
        $this->_db->addCamposTabla('importe_deuda');
        $this->_db->addCamposValue('\'' . $arrNC['facturaTotal'] * -1 .'\'');
        $this->_db->addCamposTabla('usuario_id');
        $this->_db->addCamposValue('\'' . $_SESSION['usuarioId'] .'\'');
        $this->_db->addCamposTabla('obs');
        $this->_db->addCamposValue("'NC sobre factura:  . {$resultadoFactura[0]['factura_nro']} '");
        $this->_db->addFrom('cta_cte_clientes');

        $this->_db->generarInsert();
        $arrQry['cta_cte'] = $this->_db->getQry();

        $qryFinal = $arrQry['detalle'] . $arrQry['dato'] . $arrQry['facturaElectronica'] . $arrQry['cta_cte'] . $arrQry['pendiente'];
        $this->_db->setQry($qryFinal);
        $this->_db->ejecutarTransaccion();

        if(count($this->_db->getArrError()) > 0){
            echo json_encode(array("soyError" => true, "nivel" => 1, "mensaje" => "Error al generar NC!!!!"));exit;
        }
        if($arrNC['facturaElectronica']==true){
            $ncElectronica = new ncElectronica();
            if(!$ncElectronica->enviarNc($arrNC['facturaId'])){
                echo json_encode(array("soyError" => true, "nivel" => 1, "mensaje" => "Error en envio de NC!!!!"));exit;
            }
        }
    }
    public function refacturarProforma($arrParametros){
        $qryProforma="SELECT df.pedido_id, factura_total_fact,df.cliente_id, remito_id FROM datos_facturas_electronicas df  INNER JOIN datos_remitos USING(pedido_id) WHERE df.factura_id={$arrParametros['facturaId']} AND df.factura_cancelada=false AND df.factura_paga=false;";
        $this->_db->setQry($qryProforma);
        $resultado = $this->_db->ejecutar();

        if(count($resultado)==0){
            echo json_encode(array("soyError" => true, "nivel" => 1, "mensaje" => "No se encontro la proforma a refacturar"));exit;
        }
        $this->_db->addCamposTabla('cliente_id');
        $this->_db->addCamposValue('\'' . $resultado[0]['cliente_id'] .'\'');
        $this->_db->addCamposTabla('factura_id');
        $this->_db->addCamposValue('\'' . $arrParametros['facturaId'] .'\'');
        $this->_db->addCamposTabla('importe_deuda');
        $this->_db->addCamposValue('\'' . $resultado[0]['factura_total_fact'] * -1 .'\'');
        $this->_db->addCamposTabla('usuario_id');
        $this->_db->addCamposValue('\'' . $_SESSION['usuarioId'] .'\'');
        $this->_db->addCamposTabla('obs');
        $this->_db->addCamposValue("'Refacturacion de proforma'");
        $this->_db->addFrom('cta_cte_clientes');

        $this->_db->generarInsert();
        $arr['qry']=$this->_db->getQry();

        $this->_db->addCamposUpdate('factura_cancelada = true');
        $this->_db->addCamposUpdate('factura_cancelada_fecha_hora = now()');
        $this->_db->addCamposUpdate('factura_cancelada_usuario_id = \'' . $_SESSION['usuarioId'] .'\'');
        $this->_db->addFrom('datos_facturas_electronicas');
        $this->_db->addWhere('factura_id = \'' . $arrParametros['facturaId'] .'\'');

        $this->_db->generarUpdate();
        $arr['qry'].=$this->_db->getQry();

        $arr['remitoId'] =  $resultado[0]['remito_id'];
        $arr['pedidoId'] =  $resultado[0]['pedido_id'];
        $arr['refacturada'] = true;
        $arr['refacturadaId'] = $arrParametros['facturaId'];
        return $arr;
    }
}
?>

