<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/administracion/DatoFacturaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/administracion/DetalleFacturaExtendido.php';;
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteFacturas
{
    private $_colFactura = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    
    public function __construct(){
        $this->_objFuncionesComunes = new FuncionesComunes();
        $this->_db = NEW FrenteAlmacenamiento('dmelmac');
    }
    public function cargarFactura($id){
        $this->_colFactura[$id] = NEW DatoFacturaExtendido();
        $this->_colFactura[$id]->cargarMe($id);
    }
    public function setColObjFacturas($objFactura){
        $this->_colFactura[$objFactura->getFacturaId()] = $objFactura;
    }
    public function getColObjFacturas(){
        return $this->_colFactura;
    }
    public function cargarLosDatosLazzy($arrIds){
    	
    	if(is_array($arrIds)){
            foreach($arrIds as $id){
                $ids.= "'" . $id['factura_id'] . "',";
            }
            $ids=substr($ids, 0, -1);
            $this->_db->addWhere("factura_id IN (" . $ids . ")" );
        }else{
            $this->_db->addWhere("factura_id IN (" . $arrIds . ")" );
        }

        $ids=substr($ids, 0, -1);
        $this->_db->addSelect('*, id_estado AS estado_id');
        $this->_db->addSelect('to_char(factura_fecha,\'DD-MM-YY\') as factura_fecha');
        $this->_db->addSelect('CASE WHEN factura_paga THEN \'SI\' ELSE \'NO\' END AS factura_paga');
        $this->_db->addFrom('datos_facturas');
        $this->_db->AddOrderBy('factura_id DESC');
        
        $this->_db->generarSelect();

        $resultado = $this->_db->ejecutar();
        
        $FrenteClientes= NEW FrenteClientes();
        $FrenteClientes->cargarClientesLazzy($resultado);
        $arrObjClientes= $FrenteClientes->getColObjCliente();
        
        $FrenteEstados = NEW FrenteEstados();
        $FrenteEstados->cargarEstadosLazzy($resultado);
        $arrObjEstados= $FrenteEstados->getColObjEstados();
        
        

        foreach($resultado AS $detalle){
            $objFactura = NEW DatoFacturaExtendido();
            $objFactura->cargarMe($detalle);
            $objFactura->setObjCliente($arrObjClientes[$detalle['cliente_id']]);
            $objFactura->setObjEstado($arrObjEstados[$detalle['estado_id']]);
            $this->setColObjFacturas($objFactura);
        }
    }
    public function cargarLosDetallesLazzy($arrIds){
    
        foreach($arrIds as $id){
            $ids.= $id['factura_id'] . ",";
        }
        $ids=substr($ids, 0, -1);
        $this->_db->addSelect('*');
        $this->_db->addFrom('detalle_facturas');
        $this->_db->addWhere("factura_id IN (" . $ids . ")" );
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();

        $FrenteProductos= NEW FrenteProductos();
        $FrenteProductos->cargarProductosLazzy($resultado);
        $arrObjProductos= $FrenteProductos->getColProductos();
        
        foreach($this->getColObjFacturas() AS $objFactura){
        
            foreach($resultado AS $detalle){
	    
            $objDetalle = NEW DetalleFacturaExtendido();
            $objDetalle->cargarme($detalle);
            //$objDetalle->setObjProducto($arrObjProductos[$detalle[producto_id]]);
            $objDetalle->setObjProductos($arrObjProductos[$detalle['producto_id']]);
            $this->_colFactura[$objFactura->getFacturaId()]->setColObjDetalleFactura($objDetalle);
	    }
	}
    
    }
    
    public function cargarFacturaCompleta($id){

        $this->_db->addSelect('factura_id');
        $this->_db->addFrom('datos_facturas INNER JOIN detalle_facturas USING (factura_id)');
        $this->_db->addWhere('factura_id = \'' . $id . '\'');

        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $resultado = $this->_db->ejecutar();

        $this->cargarLosDatosLazzy($resultado);
        $this->cargarLosDetallesLazzy($resultado);
    }
    
    public function buscarFacturas($arrParametros) {

        $this->_db->addSelect('factura_id');
        
        $this->_db->addFrom('datos_facturas');
        //$this->_db->addFrom('INNER JOIN  detalle_facturas USING (factura_id)');

        if($arrParametros['stringBuscar']){
            $this->_db->addWhere('factura_nro ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
            $primerRegistro = 0;
            $ultimoRegistro = 11;
            
        }else{
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
        }
	
        if($arrParametros['clientes'] !=null && $arrParametros['desdeAlta'] == null){
            
            $this->_db->addWhere('cliente_id = \''  . $arrParametros['clientes'] . '\'');
        }
        if($arrParametros['facturas'] !=null && $arrParametros['desdeAlta'] == null){
            
            $this->_db->addWhere('factura_id = \''  . $arrParametros['facturas'] . '\'');
        }
        if($arrParametros['facturaPaga'] !=null){
            $this->_db->addWhere('factura_paga = \''  . $arrParametros['facturaPaga'] . '\'');
        }
        if($arrParametros['fechaDesde'] !=null && $arrParametros[desdeAlta]==null){
            $this->_db->addWhere('factura_fecha >= \'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros['fechaHasta']) . '\'');
        }
        if($arrParametros['fechaHasta'] !=null && $arrParametros[desdeAlta]==null){
            $this->_db->addWhere('factura_fecha <= \'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros['fechaHasta']) . '\'');
        }
        $this->_db->addWhere('factura_cancelada = false');
        $this->_db->addWhere("sucursal_id = {$_SESSION['sucursalId']}");
	
        //$this->_db->addGroup('factura_id, factura_nro');
        $this->setOrdenFacturas($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //$this->_db->AddOrderBy('substring(factura_nro, position(\'-\' in factura_nro)+1)::integer asc');
	
        if($arrParametros['tipo']=='facturas'){
            $this->_db->addLimit(100);
        }
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
            $arrIds[$i]['factura_id'] = $arr[$i]['factura_id'];
        }
        $this->cargarLosDatosLazzy($arrIds);
    }
    public function setOrdenFacturas($arrParametros){

        if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){
            $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
        }else{
            //definir default
            //$this->_ordenColumnas['ordenarPor']    = 'substring(factura_nro, 7)';
            $this->_ordenColumnas['ordenarPor']    =   'factura_id';
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
    //pagina 1 por defecto amigo
    public function setPagina($pagina=1){
        $this->_pagina = $pagina;
    }
    public function getPagina(){
        return $this->_pagina;
    }
    public function getDetalles(){

	foreach($this->_colFactura AS  $objFactura){
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $objFactura->getFacturaId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  false,
                    "detalles"      =>  true,
                    "imprimir"      =>  true,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $objFactura->getFacturaFecha(),
                    $objFactura->getFacturaNro(),
                    $objFactura->getObjCliente()->getClienteNombre(),
                    $objFactura->getObjEstado()->getEstadoNombre(),
                    $this->_objFuncionesComunes->formatoMoneda($objFactura->getFacturaTotal()),
                    $this->_objFuncionesComunes->formatoMoneda($objFactura->getFacturaFaltanFact()),
                    $objFactura->getFacturaPaga()
                ),
            );
	    $total++;
	}
	return $arr;
    }
    public function getDetallesAutocompletar(){
        foreach($this->_colFactura AS $factura){
        
            $arr[]   =   array(
                'label' =>  $factura->getFacturaNro(),
                'value' =>  $factura->getFacturaId()
                );
        }		
        return $arr;
    }
    public function getDetallesAutocompletarFacturasPendientes(){
        foreach($this->_colFactura AS $factura){

            if($factura->getFacturaPaga()=="NO"){
                $arr[]   =   array(
                    'label' =>  $factura->getFacturaNro(),
                    'value' =>  $factura->getFacturaId()
                    );
            }
        }		
        return $arr;
    }
    public function getNroFactura($letra){
    
        $qry="SELECT last_value + 1 AS nro,'" .   $letra['stringBuscar'] . "' AS letra FROM seq_datos_factura_nro_" . $letra['stringBuscar'] .";";
        $this->_db->setQry($qry);
        
        $resultado = $this->_db->ejecutar();
        
        foreach($resultado AS $factura){
            $arr[]   =   array(
                'label' =>  $factura['letra'] . "0002-" . str_pad($factura['nro'],8,"0",STR_PAD_LEFT),
                'value' =>  $factura['letra']
                );
        }		
        return $arr;
    }
    public function armarDetalles($id){
    
        $this->cargarFacturaCompleta($id);
	
        foreach($this->_colFactura AS $objFactura){
        
            $propiedades =   array(
                array(
                    "display"   =>  'Fecha',
                    "name"      =>  'cliente_calle',
                    "editable"  =>  true,
                    "class"	=>'',
                    "value"     =>  $objFactura->getFacturaFecha(),

                ),
                array(
                    "display"   =>  'Cliente',
                    "name"      =>  'cliente_altura',
                    "editable"  =>  true,
                    "class"	=>'',
                    "value"     =>  $objFactura->getObjCliente()->getClienteNombre(),

                ),
                array(
                    "display"   =>  'Remito Nro',
                    "name"      =>  'cliente_piso',
                    "editable"  =>  true,
                    "class"	=>'',
                    "value"     =>  $objFactura->getFacturaNro(),

                ),
                array(
                    "display"   =>  'Total IVA Inc',
                    "name"      =>  'cliente_piso',
                    "editable"  =>  true,
                    "class"	=>'',
                    "value"     =>  $this->_objFuncionesComunes->formatoMoneda($objFactura->getFacturaTotal()),
                ),
                array(
                    "display"   =>  'Remito Pendiente',
                    "name"      =>  'cliente_piso',
                    "editable"  =>  true,
                    "class"	=>'',
                    "value"     =>  $this->_objFuncionesComunes->formatoMoneda($objFactura->getFacturaFaltanFact()),
                ),
		    );
        }
        
        
        foreach($objFactura->getColObjDetalleFactura() AS $detalle){
	
            $arr []   =    array
                (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $detalle->getDetalleFacturaId(),
                //define las herramientas
                "herramientas"   =>  array(
                "editable"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $detalle->getObjProductos()->getProductoNombre() . " " . $detalle->getObjProductos()->getProductoPresentacion(),
                    $detalle->getCantidad(),
                    $this->_objFuncionesComunes->formatoMoneda($detalle->getImporteDetalle()),
                    $this->_objFuncionesComunes->formatoMoneda($detalle->getImporteUnitario()),
                ),
		    );
        }
    
	$detalles=   array(
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
                "display"   =>  'Total IVA Inc',
                "name"      =>  'peso_neto',
                "class"	=>  '',
                "editable"  =>  false,
            ),
            array(
                "display"   =>  'Precio Unitario',
                "name"      =>  'peso_neto',
                "class"	=>  '',
                "editable"  =>  false,
            ),
	    ),
	    "celdas"    =>  $arr
	);
	$array_devolver =   array(
	    "editable_cabecera" => false,
	    "propiedades"       =>  $propiedades,
	    "listadoDetalles"   =>  $detalles
        );
    return $array_devolver;
    }
    
    public function generarFactura($arrParametros){
	
	
	$arrRemitos = explode ("||",$arrParametros[remitos]);
	array_pop($arrRemitos);

	$this->_db->addCamposTabla('seq_datos_factura_id');
    $this->_db->generarProximo();

    $id = $this->_db->ejecutar();
    
    $facturaId = $id[0]['nextval'];
	
	//var_dump($arrRemitos);exit;
	foreach ($arrRemitos AS $remito){
 	    
 	    $remitoId = str_replace("#","", $remito);

        $FrenteRemitos = NEW FrenteRemitos();
        $FrenteRemitos->cargarRemitoCompleto($remitoId);
        
        foreach($FrenteRemitos->getColObjRemitos() AS $objRemito){
            
            if($objRemito->getClienteId()!=$arrParametros['clientes']){
                $arrDevolver = array(
			    "soyError" => true,
			    "nivel" => 1,
			    "mensaje" => "El remito Nro  " . $objRemito->getRemitoNro() . "  pertenece al cliente " . $objRemito->getObjCliente()->getClienteNombre()
			    );
                echo json_encode($arrDevolver);exit;
            }
            foreach($objRemito->getColObjDetalleRemito() AS $objDetalle){
                $arrDetalle['facturaId'] = $facturaId;
                $arrDetalle['remitoId'] = $remitoId;
                $arrDetalle['productoId'] = $objDetalle->getProductoId();
                $arrDetalle['cantidad'] = $objDetalle->getCantidad();
                $arrDetalle['remitoNro'] = $objRemito->getRemitoNro();
                $arrDetalle['importeUnitario'] = $objDetalle->getImporteUnitario();
                $arrDetalle['importeDetalle'] = ($objDetalle->getImporteUnitario() * $objDetalle->getCantidad()) * 1.21;
                $arrDetalle['importeIva'] = $arrDetalle['importeDetalle'] - ($objDetalle->getImporteUnitario() * $objDetalle->getCantidad());
                $arrDetalle['facturaTotal'] = $arrDetalle['facturaTotal'] + $arrDetalle['importeDetalle'];
                $arrDetalle['facturaIva'] = $arrDetalle['facturaIva'] +$arrDetalle['importeIva'];
                //var_dump($arrDetalle);exit;
                $objDetalle = NEW DetalleFacturaExtendido();
                $qry['detalle'].= $objDetalle->salvarMe($arrDetalle);
            }
            $qry['remito'].= $objRemito->actualizarme($arrDetalle);
        }
	}
	$arrDetalle['clienteId']= $arrParametros['clientes'];
	$arrDetalle['facturaLetra']= $arrParametros['facturaLetra'];
	$arrDetalle['facturaFecha']= $this->_objFuncionesComunes->fechaFormatoDb($arrParametros['facturaFecha']);
	$arrDetalle['observaciones']= $arrParametros['observaciones'];
	$objDato = NEW DatoFacturaExtendido();
	$qry['dato'] = $objDato->salvarMe($arrDetalle);

	//CTA CTE
    $this->_db->addCamposTabla('fecha');
    $this->_db->addCamposValue('\'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros['facturaFecha']) .'\'');
    $this->_db->addCamposTabla('cliente_id');
    $this->_db->addCamposValue('\'' . $arrDetalle['clienteId'] .'\'');
    $this->_db->addCamposTabla('factura_id');
    $this->_db->addCamposValue('\'' . $arrDetalle['facturaId'] .'\'');
    $this->_db->addCamposTabla('importe_deuda');
    $this->_db->addCamposValue('\'' . $arrDetalle['facturaTotal'] .'\'');
    $this->_db->addFrom('cta_cte_clientes');
	
	$this->_db->generarInsert();
	
	$qry['cta_cte'] = $this->_db->getQry();
	
    $qryFinal = $qry['detalle'] . $qry['dato'] . $qry['remito'] . $qry['cta_cte'];
	
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
    public function cancelarFactura($id){
	    
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
    public function buscarImporte($id){
    
        $obj = NEW DatoFacturaExtendido();
        $obj->cargarme($id);
        //var_dump($obj);exit;
        $arrDevolver = array(
                "soyError" => false,
                "nivel" => 1,
                "mensaje" =>'',
                "importe" =>$obj->getFacturaFaltanFact(),
                );
		echo json_encode($arrDevolver);exit;
    
    }
    
    
}
?>
