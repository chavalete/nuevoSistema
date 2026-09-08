<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/administracion/DatoLiquidacionExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/administracion/DetalleLiquidacionExtendido.php';;
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteLiquidaciones
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
    public function cargarLiquidacion($id){
        $this->_colLiquidacion[$id] = NEW DatoLiquidacionExtendido();
        $this->_colLiquidacion[$id]->cargarMe($id);
    }
    public function setColObjLiquidaciones($objLiquidacion){
        $this->_colLiquidacion[$objLiquidacion->getLiquidacionId()] = $objLiquidacion;
    }
    public function getColObjLiquidaciones(){
        return $this->_colLiquidacion;
    }
    public function cargarLosDatosLazzy($arrIds){
    	
    	if(is_array($arrIds)){
            foreach($arrIds as $id){
                $ids.= "'" . $id['liquidacion_id'] . "',";
            }
            $ids=substr($ids, 0, -1);
            $this->_db->addWhere("liquidacion_id IN (" . $ids . ")" );
        }else{
            $this->_db->addWhere("liquidacion_id IN (" . $arrIds . ")" );
        }

        $ids=substr($ids, 0, -1);
        $this->_db->addSelect('*');
        $this->_db->addSelect('to_char(liquidacion_fecha,\'DD-MM-YY\') as liquidacion_fecha');
        $this->_db->addSelect('to_char(fecha_desde,\'DD-MM-YY\') as fecha_desde');
        $this->_db->addSelect('to_char(fecha_hasta,\'DD-MM-YY\') as fecha_hasta');
        $this->_db->addSelect('CASE WHEN liquidacion_cancelada THEN \'Cancelada\' ELSE \'OK\' END AS liquidacion_cancelada');
        $this->_db->addFrom('datos_liquidaciones');
        $this->_db->AddOrderBy('liquidacion_id DESC');
        
        $this->_db->generarSelect();

        $resultado = $this->_db->ejecutar();
        
        
        $FrenteVendedores = NEW FrenteVendedores();
        $FrenteVendedores->cargarVendedoresLazzy($resultado);
        $arrObjVendedores= $FrenteVendedores->getColObjVendedores();
        

        foreach($resultado AS $detalle){
            $objLiquidacion = NEW DatoLiquidacionExtendido();
            $objLiquidacion->cargarMe($detalle);
            $objLiquidacion->setObjVendedor($arrObjVendedores[$detalle['vendedor_id']]);
            $this->setColObjLiquidaciones($objLiquidacion);
        }
    }
    public function cargarLosDetallesLazzy($arrIds){
    
        foreach($arrIds as $id){
            $ids.= $id['liquidacion_id'] . ",";
        }
        $ids=substr($ids, 0, -1);
        $this->_db->addSelect('*');
        $this->_db->addFrom('detalle_liquidaciones');
        $this->_db->addWhere("liquidacion_id IN (" . $ids . ")" );
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();

        $FrenteFacturas= NEW FrenteFacturas();
        $FrenteFacturas->cargarLosDatosLazzy($resultado);
        $arrObjFacturas= $FrenteFacturas->getColObjFacturas();
        //var_dump($arrObjFacturas);exit;
        foreach($this->getColObjLiquidaciones() AS $objLiquidacion){
        
            foreach($resultado AS $detalle){
	    
                $objDetalle = NEW DetalleLiquidacionExtendido();
                $objDetalle->cargarme($detalle);
                $objDetalle->setObjFactura($arrObjFacturas[$detalle[factura_id]]);
                //$objDetalle->setObjProductos($arrObjProductos[$detalle['producto_id']]);
                $this->_colLiquidacion[$objLiquidacion->getLiquidacionId()]->setColObjDetalleLiquidacion($objDetalle);
            }  
        }
    }
    
    public function cargarLiquidacionCompleta($id){

        $this->_db->addSelect('liquidacion_id');
        $this->_db->addFrom('datos_liquidaciones INNER JOIN detalle_liquidaciones USING (liquidacion_id)');
        $this->_db->addWhere('liquidacion_id = \'' . $id . '\'');

        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $resultado = $this->_db->ejecutar();

        $this->cargarLosDatosLazzy($resultado);
        $this->cargarLosDetallesLazzy($resultado);
    }
    
    public function buscarLiquidaciones($arrParametros) {

        $this->_db->addSelect('liquidacion_id');
        
        $this->_db->addFrom('datos_liquidaciones');
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
            
            $this->_db->addWhere('factura_nro = \''  . $arrParametros['facturas'] . '\'');
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
        //$this->_db->addWhere('factura_cancelada = false');
	$this->_db->addWhere('liquidacion_cancelada = false');	
        //$this->_db->addGroup('factura_id, factura_nro');
        $this->setOrden($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //$this->_db->AddOrderBy('substring(factura_nro, position(\'-\' in factura_nro)+1)::integer asc');
	
        if($arrParametros['tipo']=='liquidaciones'){
            $this->_db->addLimit(100);
        }
        $this->_db->generarSelect();

        $arr =  $this->_db->ejecutar();
	
        //ahora seteamos el total y la pagina
        $this->setTotal(count($arr));
        $this->setPagina($arrParametros['pagina']);
	
        if($this->getTotal() < $ultimoRegistro){    
            $ultimoRegistro = $this->getTotal();
        }	
        for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
            $arrIds[$i]['liquidacion_id'] = $arr[$i]['liquidacion_id'];
        }
        $this->cargarLosDatosLazzy($arrIds);
    }
    public function setOrden($arrParametros){

        if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){
            $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
        }else{
            //definir default
            //$this->_ordenColumnas['ordenarPor']    = 'substring(factura_nro, 7)';
            $this->_ordenColumnas['ordenarPor']    =   'liquidacion_id';
            $this->_ordenColumnas['ordenarOrden']  =   'desc';
        }
    }
    public function getOrden(){
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

	foreach($this->_colLiquidacion AS  $objLiquidacion){
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $objLiquidacion->getLiquidacionId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  true,
                    "editable"      =>  false,
                    "detalles"      =>  true,
                    "imprimir"      =>  true,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $objLiquidacion->getLiquidacionFecha(),
                    $objLiquidacion->getObjVendedor()->getVendedorNombre(),
                    $objLiquidacion->getLiquidacionFechaDesde(),
                    $objLiquidacion->getLiquidacionFechaHasta(),
                    $this->_objFuncionesComunes->formatoMoneda($objLiquidacion->getLiquidacionImporte()),
                    $objLiquidacion->getLiquidacionCancelada(),
                    $objLiquidacion->getObservaciones()
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
    
        $this->cargarLiquidacionCompleta($id);
	
        foreach($this->_colLiquidacion AS $objLiquidacion){
        
            $propiedades =   array(
                array(
                    "display"   =>  'Fecha',
                    "name"      =>  'cliente_calle',
                    "editable"  =>  true,
                    "class"	=>'',
                    "value"     =>  $objLiquidacion->getLiquidacionFecha(),

                ),
                array(
                    "display"   =>  'Vendedor',
                    "name"      =>  'cliente_altura',
                    "editable"  =>  true,
                    "class"	=>'',
                    "value"     =>  $objLiquidacion->getObjVendedor()->getVendedorNombre(),

                ),
                array(
                    "display"   =>  'Nro',
                    "name"      =>  'cliente_piso',
                    "editable"  =>  true,
                    "class"	=>'',
                    "value"     =>  $objLiquidacion->getLiquidacionId(),

                ),
                array(
                    "display"   =>  'Importe',
                    "name"      =>  'cliente_piso',
                    "editable"  =>  true,
                    "class"	=>'',
                    "value"     =>  $this->_objFuncionesComunes->formatoMoneda($objLiquidacion->getLiquidacionImporte()),
                ),
		    );
        }
        
        
        foreach($objLiquidacion->getColObjDetalleLiquidacion() AS $detalle){
	
            $arr []   =    array
                (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $detalle->getLiquidacionId(),
                //define las herramientas
                "herramientas"   =>  array(
                "editable"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $detalle->getObjFactura()->getFacturaFecha(),
                    $detalle->getObjFactura()->getFacturaNro(),
                    $detalle->getObjFactura()->getObjCliente()->getClienteNombre(),
                    $this->_objFuncionesComunes->formatoMoneda($detalle->getImporteDetalle()),
                    //$this->_objFuncionesComunes->formatoMoneda($detalle->getImporteUnitario()),
                ),
		    );
        }
    
	$detalles=   array(
	    "modelo"    =>  array(
            array(

                "display"   =>  'Fecha',
                "name"      =>  'peso_bruto',
                "class"	=>  '',
                "editable"  =>  false,
            ),
            array(
                "display"   =>  'Remito Nro',
                "name"      =>  'tara',
                "class"	=>  '',
                "editable"  =>  false,
            ),
            array(
                "display"   =>  'Cliente',
                "name"      =>  'peso_neto',
                "class"	=>  '',
                "editable"  =>  false,
            ),
            array(
                "display"   =>  'Importe comision',
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
    
    public function generarLiquidacion($arrParametros){
	
	$fechaDesde = $this->_objFuncionesComunes->fechaFormatoDb($arrParametros[fechaDesde]);
	$fechaHasta = $this->_objFuncionesComunes->fechaFormatoDb($arrParametros[fechaHasta]);
	//buscamos las facturas
	$qryFacturas="SELECT * FROM datos_facturas INNER JOIN detalle_facturas USING(factura_id) WHERE factura_cancelada = false AND en_liquidacion=false AND liquidacion_id IS NULL AND factura_fecha>='$fechaDesde' AND factura_fecha<='$fechaHasta' AND vendedor_id = $arrParametros[vendedorId] AND producto_comision > 0 AND cantidad > 0  ORDER BY factura_id ASC;";
	
	//echo $qryFacturas;exit;
	
	$this->_db->setQry($qryFacturas);
	
	$resultado = $this->_db->ejecutar();
	
	if(count($resultado)==0){
        $arrDevolver = array(
			    "soyError" => false,
			    "nivel" => 1,
			    "alertados" => $arrAlertador,
			    "mensaje" => "No hay facturas pendientes!!!!"
			    );
	echo json_encode($arrDevolver);exit;
	}
	
	$this->_db->addCamposTabla('seq_datos_liquidacion_id');
    $this->_db->generarProximo();

    $id = $this->_db->ejecutar();
    
    $liquidacionId = $id[0]['nextval'];
	
	//var_dump($arrRemitos);exit;
	 $antFacturaId =$resultado[0][factura_id];
	 $total=0;
	 $totalDetalle=0;
	 
	 foreach($resultado AS $facturas){
	 
            if($facturas['factura_id']==$antFacturaId){
                $comision = $facturas['cantidad'] * $facturas['producto_comision'];
                $totalDetalle = $totalDetalle + $comision;
                
                
            }else{
                $arrDetalle['facturaId']=$antFacturaId;
                $arrDetalle['liquidacionId']= $liquidacionId;
                $arrDetalle['importeDetalle'] = $totalDetalle;
                $total = $total + $totalDetalle;
                
                $objDetalle = NEW DetalleLiquidacionExtendido();
                $qry['detalle'].= $objDetalle->salvarMe($arrDetalle);
                
                $qry['factura'].="UPDATE datos_facturas SET en_liquidacion=true, liquidacion_id = $liquidacionId WHERE factura_id = $antFacturaId;";

                $totalDetalle = 0;
                $comision = $facturas['cantidad'] * $facturas['producto_comision'];
                $totalDetalle = $totalDetalle + $comision;
                //$total = $total + $totalDetalle;
            
            }
            $antFacturaId = $facturas['factura_id'];
        }
    $arrDetalle['facturaId']=$antFacturaId;
    $arrDetalle['liquidacionId']= $liquidacionId;
    $arrDetalle['importeDetalle'] = $totalDetalle;
    $objDetalle = NEW DetalleLiquidacionExtendido();
    $qry['detalle'].= $objDetalle->salvarMe($arrDetalle);
    
    $total = $total + $totalDetalle;
    
    $qry['factura'].="UPDATE datos_facturas SET en_liquidacion=true, liquidacion_id = $liquidacionId WHERE factura_id = $antFacturaId;";
    
	$arrDato['liquidacionId']= $liquidacionId;
	$arrDato['vendedorId']= $arrParametros['vendedorId'];
	$arrDato['fechaDesde']= $fechaDesde;
	$arrDato['fechaHasta']= $fechaHasta;
	$arrDato['importeTotal']= $total;
	$arrDato['observaciones']= $arrParametros['observaciones'];
	$objDato = NEW DatoLiquidacionExtendido();
	$qry['dato'] = $objDato->salvarMe($arrDato);

	
	
    $qryFinal = $qry['detalle'] . $qry['dato'] . $qry['factura'];
	
	$this->_db->setQry($qryFinal);
	//echo $this->_db->getQry();exit;
	$this->_db->ejecutarTransaccion();
	
		
	if(count($this->_db->getArrError()) > 0){
	    $arrDevolver = array(
			    "soyError" => true,
			    "nivel" => 1,
			    "alertados" => $arrAlertador,
			    "mensaje" => "Error al generar la liquidacion!!!!"
			    );
	}else{
	    $arrDevolver = array(
			    "soyError" => false,
			    "nivel" => 1,
			    "alertados" => $arrAlertador,
			    "mensaje" => "Liquidacion generada con exito!!!!"
			    );
	}  
	echo json_encode($arrDevolver);exit;
    }
    
    public function cancelar($id){
        
        //cacenlamos liquidacion
        $objLiquidacion = NEW DatoLiquidacionExtendido();
        $qry = $objLiquidacion->actualizarMe($id);
        //levantamos las facturas
        $qry.="UPDATE datos_facturas SET en_liquidacion=false, liquidacion_id = NULL WHERE liquidacion_id = $id;";
        
        $this->_db->setQry($qry);
        //echo $this->_db->getQry();exit;
        $this->_db->ejecutarTransaccion();


        if(count($this->_db->getArrError()) > 0){
            $arrDevolver = array(
            "soyError" => true,
            "nivel" => 1,
            "alertados" => $arrAlertador,
            "mensaje" => "Error al cacnelar!!!"
            );
        }else{
            $arrDevolver = array(
            "soyError" => false,
            "nivel" => 1,
            "alertados" => $arrAlertador,
            "mensaje" => "Liquidacion cancelada con exito!!!!"
            );
        }  
        echo json_encode($arrDevolver);exit;
    }
}
?>
