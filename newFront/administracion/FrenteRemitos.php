<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/administracion/DatoRemitoExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/administracion/DetalleRemitoExtendido.php';;
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteRemitos
{
    private $_colRemitos= Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    
    public function __construct(){
        $this->_objFuncionesComunes = new FuncionesComunes();
        $this->_db = NEW FrenteAlmacenamiento('dmelmac');
    }
    public function cargarRemitos($id){
        $this->_colRemitos[$id] = NEW DatoRemitoExtendido();
        $this->_colRemitos[$id]->cargarMe($id);
    }
    public function setColObjRemitos($objRemito){
        //var_dump($objRemito);exit;
        $this->_colRemitos[$objRemito->getRemitoId()] = $objRemito;
    }
    public function getColObjRemitos(){
        return $this->_colRemitos;
    }
    public function cargarLosDatosLazzy($arrIds){
    	
    	if(is_array($arrIds)){
            foreach($arrIds as $id){
            $ids.= "'" . $id['remito_id'] . "',";
            }
            $ids=substr($ids, 0, -1);
            $this->_db->addWhere("remito_id IN (" . $ids . ")" );
        }else{
            $this->_db->addWhere("remito_id IN (" . $arrIds . ")" );
        }

        $ids=substr($ids, 0, -1);
        $this->_db->addSelect('*');
        $this->_db->addSelect('to_char(remito_fecha,\'DD-MM-YY\') as remito_fecha');
        $this->_db->addSelect('CASE WHEN remito_facturado THEN \'SI\' ELSE \'NO\' END AS remito_facturado');
        $this->_db->addFrom('datos_remitos');
        $this->_db->AddOrderBy('substring(remito_nro,7) DESC');
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        $FrenteClientes= NEW FrenteClientes();
        $FrenteClientes->cargarClientesLazzy($resultado);
        $arrObjClientes= $FrenteClientes->getColObjCliente();
        
        foreach($resultado AS $detalle){
            $objRemito= NEW DatoRemitoExtendido();
            $objRemito->cargarMe($detalle);
            $objRemito->setObjCliente($arrObjClientes[$detalle['cliente_id']]);
            $this->setColObjRemitos($objRemito);
        }
    }
    public function cargarLosDetallesLazzy($arrIds){
    
        foreach($arrIds as $id){
            $ids.= $id['remito_id'] . ",";
        }
        $ids=substr($ids, 0, -1);
        $this->_db->addSelect('*');
        $this->_db->addFrom('detalle_remitos');
        $this->_db->addWhere("remito_id IN (" . $ids . ")" );
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        

        $FrenteProductos= NEW FrenteProductos();
        $FrenteProductos->cargarProductosLazzy($resultado);
        $arrObjProductos= $FrenteProductos->getColProductos();
        
        foreach($this->getColObjRemitos() AS $objRemito){
            foreach($resultado AS $detalle){
                $objDetalle = NEW DetalleRemitoExtendido();
                $objDetalle->cargarme($detalle);
                $objDetalle->setObjProductos($arrObjProductos[$detalle[producto_id]]);
                $this->_colRemitos[$objRemito->getRemitoId()]->setColObjDetalleRemito($objDetalle);
            }
        }
    }
    
    public function cargarRemitoCompleto($id){

        //echo $id;exit;
        $this->_db->addSelect('remito_id');
        $this->_db->addFrom('datos_remitos INNER JOIN detalle_remitos USING (remito_id)');
        $this->_db->addWhere('remito_id = \'' . $id . '\'');
        
        
        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $resultadoEnvio = $this->_db->ejecutar();
        
        $this->cargarLosDatosLazzy($resultadoEnvio);
        $this->cargarLosDetallesLazzy($resultadoEnvio);
    }
    
    public function buscarRemitos($arrParametros) {

	$this->_db->addSelect('remito_id');
	
	$this->_db->addFrom('datos_remitos');
	//$this->_db->addFrom('INNER JOIN  detalle_remitos USING (remito_id)');

	//**ANALIZO EL WHERE**//

	if($arrParametros['stringBuscar']){
	    $this->_db->addWhere('remito_nro ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
	}
	//**where faso = true**/
	if($arrParametros['clientes'] !=null && $arrParametros['desdeAlta'] == null){
	    
	    $this->_db->addWhere('cliente_id = \''  . $arrParametros['clientes'] . '\'');
	}
	if($arrParametros['remitos'] !=null && $arrParametros['desdeAlta'] == null){
	    $this->_db->addWhere('remito_id = \''  . $arrParametros['remitos'] . '\'');
	}
	if($arrParametros['remitoFacturado'] !=null){
            $this->_db->addWhere('remito_facturado = \''  . $arrParametros['remitoFacturado'] . '\'');
	}
	
	
	if($arrParametros['fechaDesde'] !=null && $arrParametros[desdeAlta]==null){
        $ano= substr($arrParametros['fechaDesde'],6,4);
	    $mes = substr($arrParametros['fechaDesde'],3,2);
	    $dia = substr($arrParametros['fechaDesde'],0,2);
	    $fechaDesde = $ano."/".$mes ."/".$dia;
	    $this->_db->addWhere('remito_fecha >= \'' . $fechaDesde . '\'');
	}
	if($arrParametros['fechaHasta'] !=null && $arrParametros[desdeAlta]==null){
	    $ano= substr($arrParametros['fechaHasta'],6,4);
	    $mes = substr($arrParametros['fechaHasta'],3,2);
	    $dia = substr($arrParametros['fechaHasta'],0,2);
	    $fechaHasta = $ano."/".$mes ."/".$dia;
        $this->_db->addWhere('remito_fecha <= \'' . $fechaHasta . '\'');
	}
	$this->_db->addWhere('remito_cancelado = false');
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	//$this->_db->addGroup('factura_id, factura_nro');
	$this->setOrdenRemitos($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//$this->_db->AddOrderBy('substring(factura_nro, position(\'-\' in factura_nro)+1)::integer asc');
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='facturas'){

	    $this->_db->addLimit(100);
	}
	//*FIN LIMIT*//
	$this->_db->generarSelect();
	//echo $this->_db->getQry();
	
	$arr =  $this->_db->ejecutar();
	
	//ahora seteamos el total y la pagina
	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro){
	    
	    $ultimoRegistro = $this->getTotal();
	}	

	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
	    $arrIds[$i]['remito_id'] = $arr[$i]['remito_id'];
	}
	$this->cargarLosDatosLazzy($arrIds);
    }
    public function setOrdenRemitos($arrParametros){

        if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){

            $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
        }else{
            //definir default
            $this->_ordenColumnas['ordenarPor']    = 'substring(remito_nro, 7)';
            //$this->_ordenColumnas['ordenarPor']    =   'factura_nro';
            $this->_ordenColumnas['ordenarOrden']  =   'desc';
        }
    }
    public function getOrdenRemitos(){
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

    public function getDetalles(){

	foreach($this->_colRemitos AS  $objRemito){
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $objRemito->getRemitoId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  false,
                    "detalles"      =>  true,
                    "imprimir"      =>  true,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $objRemito->getRemitoFecha(),
                    $objRemito->getRemitoNro(),
                    $objRemito->getObjCliente()->getClienteNombre(),
                    money_format('%.2n', $objRemito->getRemitoTotal()),
                    $objRemito->getRemitoFacturado()
                    
                ),
            );
	    $total++;
	}
	return $arr;
    }
    public function getDetallesAutocompletar(){
        foreach($this->_colRemitos AS $remito){
            $arr[]   =   array(
                'label' =>  $remito->getRemitoNro(),
                'value' =>  $remito->getRemitoId()
            );
        }		
        return $arr;
    }
    public function getRemitosPendientes($remito){
    
        $this->_db->addSelect('remito_id, remito_nro');
        $this->_db->addFrom('datos_remitos');
        $this->_db->addWhere('remito_nro ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
        $this->_db->addWhere('remito_facturado=false');
        $this->_db->generarSelect();
        
        $resultado = $this->_db->ejecutar();
        
        foreach($resultado AS $remito){
            $arr[]   =   array(
                'label' =>  $remito['remito_nro'],
                'value' =>  $remito['remito_id']
                );
        }		
        return $arr;
    }
    public function armarDetalles($id){

        $this->cargarRemitoCompleto($id);
	
        foreach($this->_colRemitos AS $objRemito){
        
            $propiedadesRemito =   array(
                    array(
                        "display"   =>  'Fecha',
                        "name"      =>  'cliente_calle',
                        "editable"  =>  false,
                        "class"	=>'',
                        "value"     =>  $objRemito->getRemitoFecha(),

                    ),
                    array(
                        "display"   =>  'Cliente',
                        "name"      =>  'cliente_altura',
                        "editable"  =>  false,
                        "class"	=>'',
                        "value"     =>  $objRemito->getObjCliente()->getClienteNombre(),

                    ),
                    array(
                        "display"   =>  'Remito Nro',
                        "name"      =>  'remito_nro',
                        "editable"  =>  false,
                        "class"	=>'',
                        "value"     =>  $objRemito->getRemitoNro(),

                    ),
                    array(
                        "display"   =>  'Importe',
                        "name"      =>  'remito_nro',
                        "editable"  =>  false,
                        "class"	=>'',
                        "value"     =>  money_format('%.2n', $objRemito->getRemitoTotal())

                    )
                );
        }
        foreach($objRemito->getColObjDetalleRemito() AS $detalle){
	
	    $arr []   =    array
		    (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $detalle->getRemitoDetalleId(),
                //define las herramientas
                "herramientas"   =>  array(
                "editable"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $detalle->getObjProductos()->getProductoNombre(),
                    $detalle->getCantidad(),
                    money_format('%.2n', $detalle->getImporteUnitario()),
                    money_format('%.2n', $detalle->getImporteDetalle()),
                    $detalle->getDescuento(),
                ),
		    );
        }
    
        $detallesRemito=   array(
            "modelo"    =>  array(
                array(

                    "display"   =>  'Producto',
                    "name"      =>  'peso_bruto',
                    "class"	=>  '',
                    "editable"  =>  false,
                ),
                array(
                    "display"   =>  'Cantidad',
                    "name"      =>  'tara',
                    "class"	=>  '',
                    "editable"  =>  false,
                ),
                array(
                    "display"   =>  'Importe Unitario',
                    "name"      =>  'peso_neto',
                    "class"	=>  '',
                    "editable"  =>  false,
                ),
                array(
                    "display"   =>  'Importe Detalle',
                    "name"      =>  'peso_neto',
                    "class"	=>  '',
                    "editable"  =>  false,
                ),
                array(
                    "display"   =>  'Descuento',
                    "name"      =>  'peso_neto',
                    "class"	=>  '',
                    "editable"  =>  false,
                ),
            ),
            "celdas"    =>  $arr
        );
        $array_devolver =   array(
            "editable_cabecera" => false,
            "imprimir"		=>  false,
            "propiedades"       =>  $propiedadesRemito,
            "listadoDetalles"   =>  $detallesRemito
        );
	    return $array_devolver;
    }
    
    public function salvarMe($arrParametros){
		
	$this->_db->addCamposTabla('seq_datos_remitos_id');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();
	
	$this->_db->addSelect('lista_id');
	$this->_db->addFrom('datos_clientes');
	$this->_db->addWhere('cliente_id <= \'' . $arrParametros['clienteId'] . '\'');
	$this->_db->generarSelect();
	$resultadoLista = $this->_db->ejecutar();
	
	$this->_db->addSelect('*');
	$this->_db->addFrom('datos_movimientos INNER JOIN detalle_movimientos USING(id_movimiento)');
	$this->_db->addFrom('INNER JOIN lista_precios_' . $resultadoLista[0][lista_id] .'USING(producto_id)');
	$this->_db->addWhere('id_movimiento = \'' . $arrParametros['idMovimiento'] . '\'');
	$this->_db->generarSelect();
	
	$resultadoMovimiento = $this->_db->ejecutar();
	
	$importeTotal=0;
	foreach ($resultadoMovimiento AS $movimiento){
	
        $arrParametros['remitoId'] = $id[0]['nextval'];
        $arrParametros['productoId'] = $movimiento['producto_id'];
        $arrParametros['cantidad'] = $movimiento['cantidad'];
	    $arrParametros['importeDetalle'] =  $arrParametros['cantidad'] + $movimiento['producto_pventa'];
	    $arrParametros['importeUnitario'] =  $movimiento['producto_pventa'];
	    
	    $arrParametros['clienteId'] = $movimiento['cliente_id'];
	    $arrParametros['remitoNro'] = $movimiento['remito_nro'];
	    $arrParametros['idMovimiento'] = $movimiento['id_movimiento'];
	    
	    $importeTotal = $importeTotal + $arrParametros['importeDetalle'];
	    $objDetalle = new DetalleRemitoExtentido();
	    $arrQry['detalle'] .= $objDetalle->salvarMe($arrDetalle);
	}
	$arrParametros['remitoTotal'] = $importeTotal;
	//HASTA ACA LLEGUE
	$obj= New FacturaVentaExtendido();
	$objFactura->setFacturaVentaId($id[0]['nextval']);
	$arrQry['dato']=$objFactura->salvarMe($arrParametros);
	
	$qryFinal = $arrQry['detalle'] . $arrQry['dato'];
	
	$this->_db->setQry($qryFinal);
	//echo $this->_db->getQry();exit;
	$this->_db->ejecutarTransaccion();
	
		
	if(count($this->_db->getArrError()) > 0){
	    $arrDevolver = array(
			    "soyError" => true,
			    "nivel" => 1,
			    "alertados" => $arrAlertador,
			    "mensaje" => "Error en la carga!!!!"
			    );
	}else{
	    $arrDevolver = array(
			    "soyError" => false,
			    "nivel" => 1,
			    "alertados" => $arrAlertador,
			    "mensaje" => "Factura cargada!!!!"
			    );
	}  
	echo json_encode($arrDevolver);exit;
    }
    public function cancelarFacturaVenta($id){
	
	
	    
	$this->_db->addCamposUpdate('factura_cancelada = true');
	$this->_db->addCamposUpdate('factura_cancelada_fecha_hora = now()');
	$this->_db->addCamposUpdate('factura_cancelada_usuario_id = \'' . $_SESSION['usuarioId'] .'\'');
	$this->_db->addFrom('datos_facturas');
	$this->_db->addWhere('factura_id = \'' . $id .'\'');
	
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
			    "mensaje" => "Factura cancelada!!!!"
			    );
	}  
	echo json_encode($arrDevolver);exit;
	
    }
    
    
    public function actualizarMe($arrParametros){

	$compraExtendido = NEW CompraExtendido();
	$compraExtendido->actualizarMe($arrParametros);
    }
}
?>
