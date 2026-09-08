<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/administracion/DatoCobroExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/administracion/DetalleCobroExtendido.php';;
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteCobros
{
    private $_colCobro = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    
    public function __construct(){
        $this->_objFuncionesComunes = new FuncionesComunes();
        $this->_db = NEW FrenteAlmacenamiento('dmelmac');


    }
    public function validarUsuarios(){
        if($_SESSION['usuarioId']!=1 && $_SESSION['usuarioId']!=3  && $_SESSION['usuarioId']!=5  && $_SESSION['usuarioId']!=8  && $_SESSION['usuarioId']!=9){
            $arrDevolver = array(
			    "soyError" => true,
			    "nivel" => 1,
			    "mensaje" => "Usted no tiene permisos para realizar el cobro"
			    );
                echo json_encode($arrDevolver);exit;
        }
    }
    public function cargarCobro($id){
        $this->_colCobro[$id] = NEW DatoCobroExtendido();
        $this->_colCobro[$id]->cargarMe($id);
    }
    public function setColObjCobros($objCobro){
        $this->_colCobro[$objCobro->getCobroId()] = $objCobro;
    }
    public function getColObjCobros(){
        return $this->_colCobro;
    }
    public function cargarLosDatosLazzy($arrIds){
    	
    	if(is_array($arrIds)){
            foreach($arrIds as $id){
                $ids.= "'" . $id['cobro_id'] . "',";
            }
            $ids=substr($ids, 0, -1);
            $this->_db->addWhere("cobro_id IN (" . $ids . ")" );
        }else{
            $this->_db->addWhere("cobro_id IN (" . $arrIds . ")" );
        }

        $ids=substr($ids, 0, -1);
        $this->_db->addSelect('*, datos_cobros.observaciones AS obscobro');
        $this->_db->addSelect('to_char(fecha_cobro,\'DD-MM-YY\') as fecha_cobro');
        $this->_db->addFrom('datos_cobros');
        $this->_db->AddOrderBy('cobro_id DESC');
        
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        $FrenteClientes= NEW FrenteClientes();
        $FrenteClientes->cargarClientesLazzy($resultado);
        $arrObjClientes= $FrenteClientes->getColObjCliente();
        

        foreach($resultado AS $detalle){
            $objCobro = NEW DatoCobroExtendido();
            $objCobro->cargarMe($detalle);
            $objCobro->setObjCliente($arrObjClientes[$detalle['cliente_id']]);
            $this->setColObjCobros($objCobro);
        }
    }
    public function cargarLosDetallesLazzy($arrIds){
    
        foreach($arrIds as $id){
            $ids.= $id['cobro_id'] . ",";
        }
        $ids=substr($ids, 0, -1);
        $this->_db->addSelect('*');
        $this->_db->addFrom('detalle_cobros');
        $this->_db->addFrom('INNER JOIN datos_formas_pagos USING(forma_pago_id)');
        $this->_db->addFrom('LEFT JOIN datos_bancos USING(banco_id)');
        $this->_db->addWhere("cobro_id IN (" . $ids . ")" );
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();

        $FrenteFacturas= NEW FrenteFacturas();
        $FrenteFacturas->cargarLosDatosLazzy($resultado);
        $arrObjFacturas= $FrenteFacturas->getColObjFacturas();
        
        foreach($this->getColObjCobros() AS $objCobro){
        
            foreach($resultado AS $detalle){
	    
            $objDetalle = NEW DetalleCobroExtendido();
            $objDetalle->cargarme($detalle);
            //$objDetalle->setObjProducto($arrObjProductos[$detalle[producto_id]]);
            $objDetalle->setObjFactura($arrObjFacturas[$detalle['factura_id']]);
            $this->_colCobro[$objCobro->getCobroId()]->setColObjDetalleCobro($objDetalle);
	    }
	}
    
    }
    
    public function cargarCobroCompleto($id){

        $this->_db->addSelect('cobro_id');
        $this->_db->addFrom('datos_cobros INNER JOIN detalle_cobros USING (cobro_id)');
        $this->_db->addWhere('cobro_id = \'' . $id . '\'');

        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $resultado = $this->_db->ejecutar();

        $this->cargarLosDatosLazzy($resultado);
        $this->cargarLosDetallesLazzy($resultado);
    }
    
    public function buscarCobros($arrParametros) {

        $this->_db->addSelect('cobro_id');
        
        $this->_db->addFrom('datos_cobros');
        $this->_db->addFrom('INNER JOIN  datos_clientes USING (cliente_id)');
        if($arrParametros['formas']!=null){
            $this->_db->addFrom('INNER JOIN  detalle_cobros USING (cobro_id)');
        
        }

        if($arrParametros['stringBuscar']){
            $this->_db->addWhere('orden_cobro_nro::text ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
            $primerRegistro = 0;
            $ultimoRegistro = 11;
            
        }else{
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
        }
	
        if($arrParametros['clientes'] !=null ){
            $this->_db->addWhere('cliente_id = \''  . $arrParametros['clientes'] . '\'');
        }
        if($arrParametros['formas'] !=null ){
            $this->_db->addWhere('forma_pago_id= \''  . $arrParametros['formas'] . '\'');
        }
        if($arrParametros['ordenNro'] !=null && $arrParametros['desdeAlta'] == null){
            $this->_db->addWhere('orden_cobro_nro = \''  . $arrParametros['ordenNro'] . '\'');
        }
        if($arrParametros['fechaDesde'] !=null && $arrParametros[desdeAlta]==null){
            $this->_db->addWhere('fecha_cobro >= \'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros['fechaDesde']) . '\'');
        }
        if($arrParametros['fechaHasta'] !=null && $arrParametros[desdeAlta]==null){
            $this->_db->addWhere('fecha_cobro <= \'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros['fechaHasta']) . '\'');
	}
        if($arrParametros['facturas'] !=null ){
            $this->_db->addFrom('INNER JOIN  datos_cobros_facturas USING (cobro_id)');
            $this->_db->addFrom('INNER JOIN  datos_facturas USING (factura_id)');
            $this->_db->addWhere('factura_nro = \''  . $arrParametros['facturas'] . '\'');
        }

        $this->_db->addWhere('cobro_cancelado = false');
        $this->_db->addWhere("datos_cobros.sucursal_id = {$_SESSION['sucursalId']}");
        //$this->_db->addGroup('factura_id, factura_nro');
        $this->setOrdenCobros($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //$this->_db->AddOrderBy('substring(factura_nro, position(\'-\' in factura_nro)+1)::integer asc');
	
        if($arrParametros['tipo']=='cobros'){
            $this->_db->addLimit(500);
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
            $arrIds[$i]['cobro_id'] = $arr[$i]['cobro_id'];
        }
        $this->cargarLosDatosLazzy($arrIds);
    }
    public function setOrdenCobros($arrParametros){

        if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){
            $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
        }else{
            //definir default
            $this->_ordenColumnas['ordenarPor']    = 'cobro_id';
            //$this->_ordenColumnas['ordenarPor']    =   'factura_nro';
            $this->_ordenColumnas['ordenarOrden']  =   'desc';
        }
    }
    public function getOrdenCobros(){
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
	if($_SESSION['usuarioId']==11){
            $cancelar=false;
        }else{
            $cancelar=true;
	}
	if($_SESSION['usuarioId']==1 || $_SESSION['usuarioId']==3 || $_SESSION['usuarioId']==11){
            $editable=true;
        }else{
            $editable=false;
        }
	foreach($this->_colCobro AS  $objCobro){
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $objCobro->getCobroId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  $cancelar,
                    "editable"      =>  $editable,
                    "detalles"      =>  true,
                    "imprimir"      =>  true,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $objCobro->getCobroFecha(),
                    $objCobro->getOrdenCobroNro(),
                    $objCobro->getObjCliente()->getClienteNombre(),
                    $this->_objFuncionesComunes->formatoMoneda($objCobro->getCobroTotal()),
                    $objCobro->getCobroObs()
                ),
            );
	    $total++;
	}
	return $arr;
    }
    public function getDetallesAutocompletar(){
        foreach($this->_colCobro AS $factura){
            $arr[]   =   array(
                'label' =>  $factura->getOrdenCobroNro(),
                'value' =>  $factura->getOrdenCobroNro()
                );
        }		
        return $arr;
    }
    public function armarDetalles($id){
    
        $this->cargarCobroCompleto($id);
	
        foreach($this->_colCobro AS $objCobro){
        
            $propiedades =   array(
                array(
                    "display"   =>  'Fecha',
                    "name"      =>  'cliente_calle',
                    "editable"  =>  true,
                    "class"	=>'',
                    "value"     =>  $objCobro->getCobroFecha(),

                ),
                array(
                    "display"   =>  'Cliente',
                    "name"      =>  'cliente_altura',
                    "editable"  =>  true,
                    "class"	=>'',
                    "value"     =>  $objCobro->getObjCliente()->getClienteNombre(),

                ),
                array(
                    "display"   =>  'Cobro Nro',
                    "name"      =>  'cliente_piso',
                    "editable"  =>  true,
                    "class"	=>'',
                    "value"     =>  $objCobro->getOrdenCobroNro(),

                ),
                array(
                    "display"   =>  'Cobro Total',
                    "name"      =>  'cliente_piso',
                    "editable"  =>  true,
                    "class"	=>'',
                    "value"     =>  $this->_objFuncionesComunes->formatoMoneda($objCobro->getCobroTotal()),
                ),
                array(
                    "display"   =>  'Observaciones',
                    "name"      =>  'cliente_piso',
                    "editable"  =>  true,
                    "class"	=>'',
                    "value"     =>  $objCobro->getCobroObs(),
                ),
		    );
        }
        foreach($objCobro->getColObjDetalleCobro() AS $detalle){
	
            $arr []   =    array
                (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $detalle->getDetalleCobroId(),
                //define las herramientas
                "herramientas"   =>  array(
                "editable"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $detalle->getFormaPagoNombre(),
                    $this->_objFuncionesComunes->formatoMoneda($detalle->getImporteDetalle())                  
                ),
		    );
        }
    
	$detalles=   array(
	    "modelo"    =>  array(
            array(
                "display"   =>  'Forma de pago',
                "name"      =>  'peso_neto',
                "class"	=>  '',
                "editable"  =>  false,
            ),
            array(
                "display"   =>  'Importe',
                "name"      =>  'tara',
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
    
    public function validarCobro($arrParametros){
        $arrDetalle = explode ("||",$arrParametros[detalle]);
        array_pop($arrDetalle);
        foreach($arrDetalle AS $cobro){
            list($formaPagoId,$importe) = explode("#",$cobro);
            $importePago = $importePago + $importe;
        }
        $this->_db->addSelect('*');
	//$this->_db->addFrom('vw_total_deuda_clientes');
	$this->_db->addFrom('vw_pendiente_por_factura');
        $this->_db->addWhere('factura_id = \''  . $arrParametros['remitoId'] . '\'');
	$this->_db->addWhere('factura_faltan_fact+1 >='. $importePago);
	//$this->_db->addWhere("CASE WHEN tipo_entrega = 'Envio' THEN factura_faltan_fact + 1 > " . $importePago . " ELSE factura_faltan_fact + 1 >= " . $importePago . " AND factura_faltan_fact - 1 <= " . $importePago . " END");
        $this->_db->generarSelect();
        
        //echo $this->_db->getQry();
        
        $resultado = $this->_db->ejecutar();
        
        if(count($resultado)==0){
        
            $arrDevolver = array(
			    "soyError" => true,
			    "nivel" => 1,
			    "mensaje" => "El pago  " . $importePago . "  no coincide con el total del remito "
			    );
                echo json_encode($arrDevolver);exit;
        }
    }
    public function generarCobro($arrParametros){
        $this->validarUsuarios();
        //var_dump($arrParametros);exit;
        $this->validarCobro($arrParametros);
        
        $arrDetalle = explode ("||",$arrParametros[detalle]);
        array_pop($arrDetalle);
        
        $this->_db->addCamposTabla('seq_datos_cobros_id');
        $this->_db->generarProximo();

        $id = $this->_db->ejecutar();
        
        $cobroId = $id[0]['nextval'];
        //var_dump($arrRemitos);exit;
        $qryCliente="SELECT cliente_id, factura_total_fact FROM datos_facturas WHERE factura_id = $arrParametros[remitoId] AND factura_paga=false AND factura_faltan_fact>0 AND factura_cancelada = false;";
        $this->_db->setQry($qryCliente);
        $resultadoCliente = $this->_db->ejecutar();
        if(count($resultadoCliente)==0){
            $arrDevolver = array(
			    "soyError" => true,
			    "nivel" => 1,
			    "mensaje" => "El remito no esta pendiente de pago"
			    );
                echo json_encode($arrDevolver);exit;
        
        }
        foreach ($arrDetalle AS $detalle){
        
                list($formaPagoId,$importe) = explode("#",$detalle);
        
        
                $arrDetalle['remitoId'] = $arrParametros[remitoId];
                $arrDetalle['clienteId'] = $resultadoCliente[0][cliente_id];
                $arrDetalle['cobroId'] = $cobroId;
                switch("$formaPagoId"){
                    case "Efectivo":
                                $formaPagoId="2";
                                break;
                    case "Transferencia":
                                $formaPagoId="1";
                                break;
                    case "Tarjeta-Credito":
                                $formaPagoId="3";
                                break;
                    case "Tarjeta-Debito":
                                $formaPagoId="6";
                                break;
                    case "Cuenta-DNI":
                                $formaPagoId="7";
                                break;
                }   
                
                
                $arrDetalle['formaPagoId'] = $formaPagoId;
                $arrDetalle['importeDetalle'] = $importe;
                //var_dump($arrDetalle);exit;
                
                $importeTotal = $importeTotal + $importe;
                $objDetalle = NEW DetalleCobroExtendido();
                $qry['detalle'].= $objDetalle->salvarMe($arrDetalle);          
                
                
                //echo "no entra";exit;
        }
        if(intval($importeTotal)> intval($resultadoCliente[0][factura_total_fact]+1)){
            $arrDevolver = array(
			    "soyError" => true,
			    "nivel" => 1,
			    "mensaje" => "El importe del pago es superior al de remito"
			    );
                echo json_encode($arrDevolver);exit;
        
        }
        $ordenNro = $id[0]['nextval'];
        $arrDato['clienteId']= $resultadoCliente[0][cliente_id];
        $arrDato['remitoId']= $arrParametros['remitoId'];
        $arrDato['cobroId']= $cobroId;
        $arrDato['observaciones']= $arrParametros['observaciones'];
        $arrDato['importeTotal']= $importeTotal;
        $arrDato['tipo']= $arrParametros['tipo'];
        
        
        $objDato = NEW DatoCobroExtendido();
        $qry['dato'] = $objDato->salvarMe($arrDato);
    
        
        //echo $qry['dato'];exit;
        if($arrParametros['tipo']=='cobros'){
            $qry['facturas'] = $this->actualizarPendientes($arrDato);
        }
    
        
        
        //echo $qry['detalle'];exit;
        
        $qryFinal =  $qry['dato'] . $qry['detalle'] . $qry['facturas'];
        
        //echo $qryFinal;exit;
        
        $this->_db->setQry($qryFinal);
        
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
                    "mensaje" => "Cobro cargado!!!!"
                    );
        }  
        echo json_encode($arrDevolver);exit;
    }
    
    public function actualizarPendientes($arrDatos){
    
        $this->_db->addSelect('*');
        $this->_db->addFrom('vw_pendiente_por_factura');
        $this->_db->addWhere('cliente_id = \'' . $arrDatos['clienteId'] .'\'');
        if($arrDatos['remitoId']!='undefined' && $arrDatos['remitoId']!=null){
            $this->_db->addWhere('factura_id = \'' . $arrDatos['remitoId'] .'\'');
        }
        
        $this->_db->generarSelect();
        
        //echo $this->_db->getQry();exit;
        
        $resultado = $this->_db->ejecutar();
        
        if(count($resultado)==0){
            $arrDevolver = array(
			    "soyError" => true,
			    "nivel" => 1,
			    "mensaje" => "No hay facturas pendientes"
			    );
            echo json_encode($arrDevolver);exit;
        }
            $pendienteFactura = $resultado[0]['factura_faltan_fact'];
            $importePago = $arrDatos['importeTotal'];

            // Si la diferencia absoluta es menor o igual a 1, el estado es 25, sino 31
            if (abs($importePago - $pendienteFactura) <= 1) {
                $idEstado = 25;
                $facturaPaga = 'true';
                $pendiente = 0;
            } else {
                $idEstado = 31;
                $facturaPaga = 'false';
                $pendiente = $pendienteFactura - $importePago;
            }

            $this->_db->addCamposTabla('cobro_id');
            $this->_db->addCamposValue('\'' . $arrDatos['cobroId'] .'\'');
            $this->_db->addCamposTabla('factura_id');
            $this->_db->addCamposValue('\'' . $arrDatos['remitoId'] .'\'');
            $this->_db->addCamposTabla('importe_cancelado');
            $this->_db->addCamposValue('\'' . $importePago .'\'');
            $this->_db->addCamposTabla('importe_pendiente');
            $this->_db->addCamposValue('\'' . $pendiente .'\'');

            $this->_db->addFrom('datos_cobros_facturas');

            $this->_db->generarInsert();


            $qryPendiente.=$this->_db->getQry();



            
            $this->_db->addCamposUpdate('factura_faltan_fact =' . $pendiente);
            $this->_db->addCamposUpdate('factura_paga = '. $facturaPaga );
            $this->_db->addCamposUpdate('id_estado = ' . $idEstado);
            $this->_db->addFrom('datos_facturas');
            $this->_db->addWhere('factura_id = \'' . $arrDatos['remitoId'] .'\'');
            
            //generar qry
            $this->_db->generarUpdate();   
            
            //echo $this->_db->getQry();exit;
            
            $qryPendiente.=$this->_db->getQry();
            
            $this->_db->addCamposUpdate('id_estado =' . $idEstado);
            $this->_db->addFrom('datos_movimientos');
            $this->_db->addWhere('factura_id = \'' . $arrDatos['remitoId'] .'\'');
            
            //generar qry
            $this->_db->generarUpdate();   
            
            //echo $this->_db->getQry();exit;
            
            $qryPendiente.=$this->_db->getQry();
        
        return $qryPendiente;
    }
    
    
    
    public function cancelarCobro($id){
        $this->validarUsuarios();
        $qry="SELECT * FROM datos_cobros_facturas WHERE cobro_id = ".$id.";";
        $this->_db->setQry($qry);
        $resultado= $this->_db->ejecutar();
        
        if(count($resultado)==0){
            $arrDevolver = array(
                    "soyError" => true,
                    "nivel" => 1,
                    "alertados" => $arrAlertador,
                    "mensaje" => "No existe la orden a cancelar!!!!"
                    );
            echo json_encode($arrDevolver);exit;
        }
        foreach($resultado AS $detalle){
        
            $qryAnulacion.="UPDATE datos_facturas SET factura_paga=false,id_estado=24, factura_faltan_fact = factura_faltan_fact +".$detalle['importe_cancelado']." WHERE factura_id =".$detalle['factura_id'].";";
            $qryAnulacion.="UPDATE datos_movimientos SET id_estado=24 WHERE factura_id =".$detalle['factura_id'].";";
        }
        
        
        $this->_db->addCamposUpdate('cobro_cancelado = true');
        $this->_db->addCamposUpdate('cobro_cancelado_fecha_hora = now()');
        $this->_db->addCamposUpdate('cobro_cancelado_usuario_id = \'' . $_SESSION['usuarioId'] .'\'');
        $this->_db->addFrom('datos_cobros');
        $this->_db->addWhere('cobro_id = \'' . $id .'\'');
        
        $this->_db->generarUpdate();
        
        $qryAnulacion.=$this->_db->getQry();
        
        //echo $qryAnulacion;exit;
        
        $this->_db->setQry($qryAnulacion);
        
        $this->_db->ejecutarTransaccion();
            
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
                    "mensaje" => "Cobro cancelado!!!!"
                    );
        }  
        echo json_encode($arrDevolver);exit;
        
    }
    public function devolucionDeCaja($arrParametros){
        $this->validarUsuarios();
        $qry="SELECT id_movimiento,movimiento_total_fact, factura_id, cliente_id FROM datos_movimientos WHERE tipo_movimiento_id = 2 AND  factura_id='$arrParametros[remito]' AND anulado=false AND id_estado=25;";
        $this->_db->setQry($qry);
        //echo $qry;exit;
        $resultado = $this->_db->ejecutar();

        if(count($resultado)==0){
            $arrDevolver = array(
            "soyError" => true,
            "nivel" => 1,
            "mensaje" => "El remito no esta en condiciones para cargar la devolucion de efectivo!!!!"
            );
            echo json_encode($arrDevolver);exit;
        }
        
        if($arrParametros['importe'] > $resultado[0][movimiento_total_fact]){
            $arrDevolver = array(
            "soyError" => true,
            "nivel" => 1,
            "mensaje" => "El importe no puede ser superior al valor de remito!!!!"
            );
            echo json_encode($arrDevolver);exit;
        }
        $this->_db->addCamposTabla('seq_datos_cobros_id');
        $this->_db->generarProximo();

        $id = $this->_db->ejecutar();
        
        $cobroId = $id[0]['nextval'];
        $arrDetalle['remitoId'] = $arrParametros['remito'];
        $arrDetalle['clienteId'] = $resultado[0][cliente_id];
        $arrDetalle['cobroId'] = $cobroId;
        $arrDetalle['formaPagoId'] = $arrParametros['formaPagoId'];
        $arrDetalle['importeDetalle'] = $arrParametros['importe']*-1;
        //var_dump($arrDetalle);exit;
        
        $importeTotal = $importeTotal + $importe;
        $objDetalle = NEW DetalleCobroExtendido();
        $qryDetalle = $objDetalle->salvarMe($arrDetalle);
        
        $ordenNro = $id[0]['nextval'];
        $arrDato['clienteId']= $resultado[0][cliente_id];
        $arrDato['remitoId']= $arrParametros['remito'];
        $arrDato['cobroId']= $cobroId;
        $arrDato['observaciones']= $arrParametros['observaciones'];
        $arrDato['importeTotal']= $arrParametros['importe']*-1;
        
        
        $objDato = NEW DatoCobroExtendido();
        $qryDetalle.= $objDato->salvarMe($arrDato);
        
        $this->_db->addCamposTabla('cobro_id');
        $this->_db->addCamposValue('\'' . $cobroId .'\'');
        $this->_db->addCamposTabla('factura_id');
        $this->_db->addCamposValue('\'' . $arrParametros['remito'] .'\'');
        $this->_db->addCamposTabla('importe_cancelado');
        $this->_db->addCamposValue('\'' . $arrParametros['importe']*-1 .'\'');
        
        $this->_db->addFrom('datos_cobros_facturas');
        
        $this->_db->generarInsert();
    
        
        $qryDetalle.=$this->_db->getQry();
        
        //solo para diferencia de precio
        if($arrParametros['formaPagoId']==4){
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
            $qryDetalle.=  $this->_db->getQry();
            
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
            
        }
        //echo $qryDetalle;exit;
        $this->_db->setQry($qryDetalle);
        $this->_db->ejecutarTransaccion();
        if (count($this->_db->getArrError()) > 0){
            $arrDevolver = array(
                                "soyError" => true,
                                "nivel" => 0,
                                "alertados" => $arrAlertador,
                                "mensaje" => "Error al cargar la devolucion!"
                                );
            //salvar el error
        }else{
            $arrDevolver = array(
                                "soyError" => false,
                                "nivel" => 0,
                                "alertados" => $arrAlertador,
                                "mensaje" => "Devolucion de efectivo generada!"
                                );
        }  
        echo json_encode($arrDevolver);exit;            
    }
    public function actualizar($arrParametros){
        $objCobro = NEW DatoCobroExtendido();
        $arrParametros['campos']['cobro_fecha'] = $this->_objFuncionesComunes->fechaFormatoDb($arrParametros['campos']['cobro_fecha']);
        if($objCobro->actualizar($arrParametros)){
            $arrDevolver = array(
                                "soyError" => false,
                                "nivel" => 0,
                                "alertados" => $arrAlertador,
                                "mensaje" => "actualziado!"
                                );
        }else{
            $arrDevolver = array(
                                "soyError" => true,
                                "nivel" => 0,
                                "alertados" => $arrAlertador,
                                "mensaje" => "Error!"
                                );
        }
        echo json_encode($arrDevolver);exit;
    }
}
?>
