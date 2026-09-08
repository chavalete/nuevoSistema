<?php
//ALMACENAMIENTO
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
//TRAZABILIDAD
require_once 'DatoMovimientoExtendido.php';
require_once 'DetalleMovimientoExtendido.php';

/**
 * Description of FrenteIngresos
 *
 * @author lucas
 */

class FrenteIngresos{
    
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
    
    public function cargarMovimiento($id){
        $objMovimiento = new DatoMovimientoExtendido();
        $objMovimiento->cargarme($id);
        $this->setColObjDatoMovimiento($objMovimiento);
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
        $this->_db->addSelect('*');
        $this->_db->addSelect('CASE WHEN recepcion_liberada THEN \'Liberado\' ELSE \'Pendiente\' END AS recepcion_mensaje');
        $this->_db->addSelect("to_char(movimient_fecha,'DD-MM-YYYY') AS fecha");
        $this->_db->addFrom('datos_movimientos');
        $this->_db->addWhere("id_movimiento IN (" . $ids . ")" );
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
	
        foreach($resultado AS $datos){
            $objDatoMovimiento= NEW DatoMovimientoExtendido();
            $objDatoMovimiento->cargarmeNew($datos);
            $objDatoMovimiento->setObjProve($arrObjProveedores[$datos[prove_id]]);
            $objDatoMovimiento->setObjCliente($arrObjClientes[$datos[cliente_id]]);
            //$objDatoMovimiento->setObjPaciente($arrObjPaciente[$datos[paciente_id]]);
            //$objDatoMovimiento->setObjEstados($arrObjEstados[$datos[estado_id]]);
            $this->setColObjDatoMovimiento($objDatoMovimiento);
            //var_dump($objDatoMovimiento);exit;
        }
    }

    
    public function buscarMovimiento ($arrParametros){

	$this->_db->addSelect('*');
    $this->_db->addSelect('CASE WHEN recepcion_liberada THEN \'Liberado\' ELSE \'Pendiente\' END AS recepcion_mensaje');
    $this->_db->addSelect("to_char(movimiento_fecha,'DD-MM-YYYY') AS fecha");
    $this->_db->addFrom('datos_movimientos_sucursales dm');
    //$this->_db->addFrom('INNER JOIN detalle_movimientos_sucursales USING(id_movimiento)');
	$this->_db->addFrom('INNER JOIN datos_proveedores ON dm.prove_id = datos_proveedores.prove_id');
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
	if($arrParametros['proveedores'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('dm.prove_id = \'' . $arrParametros['proveedores'] . '\'');
	}
	if($arrParametros['productos'] !=null && $arrParametros[desdeAlta]==null){
           $this->_db->addWhere('producto_id = \''  . $arrParametros['productos'] . '\'');
    	}
	if($arrParametros['estado'] !=null){
        $this->_db->addWhere('recepcion_liberada = \''  . $arrParametros['estado'] . '\'');
        }
	if($arrParametros['remitos'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('remito_nro = \'' . $arrParametros[remitos] . '\'');
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
    
    $this->_db->addWhere('tmt.tipo_movimiento_id IN (1)');
    $this->_db->addWhere("dm.a_sucursal_id={$_SESSION['sucursalId']}");

	$this->_db->addWhere('anulado = false ');
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenMovimientos($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['desdeBusqueda']==false){
	    $this->_db->addLimit(500);
	}	
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
        $array['idMovimiento'] = $arr[$i]['de_id_movimiento'];
        $array['fecha'] = $arr[$i]['fecha'];
        $array['proveedor'] = $arr[$i]['prove_nombre'];
        $array['tipo']=$arr[$i]['tipo_movimiento_nombre'];
        $array['remito'] = $arr[$i]['remito_nro'];
        $array['estado'] = $arr[$i]['recepcion_mensaje'];
        $array['obs'] = $arr[$i]['obs'];
        $array['recepcion_liberada'] = $arr[$i]['recepcion_liberada'];
        $this->_colConsultas[]= $array;
        }
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
    
        foreach($this->_colConsultas AS $movimiento){
                if($movimiento['recepcion_liberada']==false && ($_SESSION['usuarioId']==3 || $_SESSION['usuarioId']==1)){
                    $transaccion=true;
                }else{
                    $transaccion=false;
                }
                

            $arr []   =    array
                (
                    //id si o si un solo string (sin espacios en blanco)
                    "id"            =>  $movimiento['idMovimiento'],
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  false,
                        "editable"      =>  true,
                        "detalles"      =>  true,
                        "imprimir"      =>  true,
                        "autogestion"=>false,
                        "enviarTransaccion"  =>  $transaccion

                    ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell"          => array(
                        $movimiento['idMovimiento'],
                        $movimiento['fecha'],
                        $movimiento['proveedor'],
                        $movimiento['tipo'],
                        $movimiento['remito'],
                        $movimiento['estado'],
                        $movimiento['obs'],
                        ),
                );
            }
        return $arr;
    }

    //**PARA EL FRENTE FIN**//

    public function armarDetalles($id){

        $this->_db->addSelect('*');
        $this->_db->addSelect('CASE WHEN recepcion_liberada THEN \'Liberado\' ELSE \'Pendiente\' END AS recepcion_mensaje');
        $this->_db->addSelect("to_char(movimiento_fecha,'DD-MM-YYYY') AS fecha");
        $this->_db->addFrom('datos_movimientos_sucursales dm');
        $this->_db->addFrom('INNER JOIN datos_proveedores ON dm.prove_id = datos_proveedores.prove_id');
        $this->_db->addWhere("de_id_movimiento=$id");
        $this->_db->generarSelect();
        $resultado = $this->_db->ejecutar();
        
        foreach ($resultado  AS $objMovimiento){

                $propiedadesTransaccion =   array(
                array(

                    "display"   =>  'Nro',
                    "name"      =>  'id_movimiento',
                    "editable"  =>  false,
                    "class"	=>'',
                    "value"     =>  $objMovimiento['id_movimiento'],
                ),
                array(
                    "display"   =>  'Proveedor',
                    "name"      =>  'prove_id',
                    "editable"  =>  true,
                    "class"	=> 'autocomplete',
                    "value"     =>  $objMovimiento['prove_nombre'],

                ),
                array(
                    "display"   =>  'Fecha',
                    "name"      =>  'movimiento_fecha_hora',
                    "editable"  =>  false,
                    "class"	=>  'datePicker',
                    "value"     =>  $objMovimiento['fecha'],
                ),
                array(
			    "display"   =>  'Fecha liberación',
			    "name"      =>  'recepcion_liberada_fecha_hora',
			    "editable"  =>  false,
			    "class"	=>  'datePicker',
			    "value"     =>  $objMovimiento['recepcion_liberada_fecha_hora'],
                )
                );
        }
        
        $this->_db->addSelect('dem.unidades AS unidades');
        $this->_db->addSelect('dp.producto_nombre || \' \' || dp.producto_presentacion AS producto_nombre');
        $this->_db->addSelect('dp.producto_gtin');
        $this->_db->addSelect('dp.codigo_referencia');
        $this->_db->addSelect('to_char(dem.lote_vencimiento,\'DD-MM-YYYY\') AS lote_vencimiento');
        $this->_db->addSelect('dem.detalle_id');
        $this->_db->addSelect('dem.producto_recibido');
        $this->_db->addSelect('dem.cantidad_origen');
        $this->_db->addSelect('dem.gtin_origen');
        $this->_db->addSelect('dem.unidades_asociadas');
        $this->_db->addFrom('datos_movimientos_sucursales dm');
        $this->_db->addFrom('INNER JOIN detalle_movimientos_sucursales dem USING (id_movimiento)');
        $this->_db->addFrom('LEFT JOIN datos_productos dp ON dem.producto_id = dp.producto_id');
        $this->_db->addWhere('dm.de_id_movimiento = \'' . $id . '\'');
        //$this->_db->addGroup('dp.producto_nombre, dp.producto_presentacion, dp.producto_gtin, dp.codigo_referencia, dl.lote_vencimiento, dem.detalle_anulado,dem.detalle_id ');
        $this->_db->addOrderBy('dp.producto_nombre || \' \' || dp.producto_presentacion');
        $this->_db->generarSelect();
        
        //echo $this->_db->getQry();
        $arrDetalles =  $this->_db->ejecutar();


        foreach($arrDetalles AS $detalle){
                if($detalle['producto_nombre']==NULL){
                    $editable=true;
                }else{
                    $editable=false;
                }
                $arr []   =    array
                (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $detalle['detalle_id'],
                //define las herramientas
                "herramientas"   =>  array(
                "editable"      =>  true,
                ),
                            "alertado" => $detalle['detalle_anulado'],
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $detalle['cantidad_origen'],
                    $detalle['unidades_asociadas'],
                    $detalle['unidades'],
                    $detalle['producto_nombre'] . " " . $detalle['producto_presentacion'] ,
                    $detalle['producto_recibido'],
                    $detalle['gtin_origen'],
                    $detalle['lote_vencimiento']
                ),
                );
        }
        
        $detallesTransaccion    =   array(
            "modelo"    =>  array(
            array(
                "display"   =>  'Cat. Recbida',
                "name"      =>  'cant_recibida',
                "class"	=>  '',
                "editable"  =>  true,
            ),
            array(
                "display"   =>  'Cat. Asoc',
                "name"      =>  'cant_asoc',
                "class"	=>  '',
                "editable"  =>  true,
            ),
            array(

                "display"   =>  'Cant Ingreso.',
                "name"      =>  'cant_ingreso',
                "class"	=>  '',
                "editable"  =>  true,
            ),
            array(
                "display"   =>  'Prod. Asoc',
                "name"      =>  'productos',
                "class"	=>  'autocomplete',
                "editable"  =>  true,
            ),
            array(
                "display"   =>  'Prod. Origen',
                "name"      =>  'productos_origen',
                "class"	=>  '',
                "editable"  =>  true,
            ),
            array(
                "display"   =>  'Cod. Barras',
                "name"      =>  'producto_gtin',
                "class"	=>  '',
                "editable"  =>  true,
            ),
            array(
                "display"   =>  'Venc.',
                "name"      =>  'lote_vencimiento',
                "class"	=>  'datePicker',
                "editable"  =>  true,
            ),
            array(
		    "display"   =>  'Herr.',
		    "name"      =>  'herramienta',
            ),

            ),
            "celdas"    =>  $arr
        );
        
        $array_devolver =   array(
            "editable_cabecera" => false,
            "propiedades"       =>  $propiedadesTransaccion,
            "listadoDetalles"   =>  $detallesTransaccion,
            "imprimir"		=>  false
        );
            
            return $array_devolver;
        }
        
    public function liberarRecepcion($id){
    
    $qry="SELECT *,to_char(lote_vencimiento,'DD-MM-YYYY') AS vencimiento, dms.prove_id,detalle_movimientos_sucursales.producto_id, producto_activo, producto_nombre  FROM datos_movimientos_sucursales dms INNER JOIN detalle_movimientos_sucursales USING(id_movimiento) LEFT JOIN datos_productos USING(producto_id) WHERE anulado = false AND de_id_movimiento=$id AND recepcion_liberada=false;";
    $this->_db->setQry($qry);
    //echo $qry;exit;
    $resultado = $this->_db->ejecutar();

    foreach ($resultado AS $detalle){
        if($detalle['producto_id']==1){
            $arrDevolver = array(
                "soyError" => true,
                "nivel" => 1,
                "mensaje" =>"La recepcion tiene productos sin codificar, revise"
                );

        echo json_encode($arrDevolver);exit;
        }
        if($detalle['producto_activo']==false){
            $arrDevolver = array(
                "soyError" => true,
                "nivel" => 1,
                "mensaje" =>"El producto" . $detalle['producto_nombre'] . "No se encuentra activo"
                );

        echo json_encode($arrDevolver);exit;
        }


        $arrParametros['productos'].=$detalle['producto_id']."#".$detalle['cantidad']."#".$detalle['producto_pcosto']."#".$detalle['vencimiento']."||";
        $arrParametros['proveedores']=$detalle['prove_id'];
        $arrParametros['remitos']=$detalle['remito_nro'];
        $arrParametros['observaciones']=$detalle['obs'];
        $arrParametros['idSucursal']=$detalle['a_sucursal_id'];
        $arrParametros['tipo']="entradas";
    }
    exit;
    /*echo "<pre>";
    print_r($arrParametros);
    echo "</pre>";exit;
    */
    //estado_id = busca el id
    $this->_db->addCamposUpdate('recepcion_liberada = true');
    //$this->_db->addCamposUpdate('estado_id = 22');
	$this->_db->addCamposUpdate('recepcion_liberada_fecha_hora=now()');
	$this->_db->addCamposUpdate('recepcion_liberada_usuario_id = \'' . $_SESSION['usuarioId'] .'\'');

	$this->_db->addFrom('datos_movimientos_sucursales');
	$this->_db->addWhere('de_id_movimiento = \'' . $id .'\'');

	$this->_db->generarUpdate();
	
	$arrParametros['qryLiberacion'] = $this->_db->getQry();
    $FrenteMovimiento = NEW FrenteMovimiento();
    $FrenteMovimiento->generarMovimiento($arrParametros);
    }
    public function actualizar($arrParametros){
        //VALIDAMOS SI EXITE LA RELACION O NO, SINO LA CREAMOS
        $qry="SELECT * FROM presentaciones_productos WHERE gtin='{$arrParametros['campos']['producto_gtin']}';";
        //echo $qry;
        $this->_db->setQry($qry);
        $resultado = $this->_db->ejecutar();
        if(count($resultado)==0){
            if($arrParametros['campos']['producto_gtin']==null){
                $txtError = "No tiene codigo de barras asociado";
            }
            if($arrParametros['campos']['cant_asoc']==0){
                $txtError.= "No tiene cantidad asociada al producto";
            }
            if($arrParametros['campos']['cant_ingreso']==0){
                $txtError.= "La cantidad no unidades q ingresan no puede ser 0";
            }
            if($txtError!=NULL){
                $arrDevolver = array(
                    "soyError" => true,
                    "nivel" => 1,
                    "mensaje" =>$txtError
                    );
                echo json_encode($arrDevolver);exit;
            }

            $this->_db->addCamposTabla('seq_relacion_id_productos_presentaciones');
            $this->_db->generarProximo();

            $id = $this->_db->ejecutar();

            $this->_db->addCamposTabla('relacion_id');
            $this->_db->addCamposValue('\'' . $id[0][nextval] . '\'');

            $this->_db->addCamposTabla('relacion_usuario_id');
            $this->_db->addCamposValue('\'' . $_SESSION['usuarioId'] . '\'');

            if($arrParametros['campos']['producto_gtin'] != null){
                $this->_db->addCamposTabla('gtin');
                $this->_db->addCamposValue('\'' . $arrParametros['campos']['producto_gtin'] . '\'');
            }
            if($arrParametros['campos']['cant_asoc'] != null){
                $this->_db->addCamposTabla('unidades');
                $this->_db->addCamposValue('\'' . $arrParametros['campos']['cant_asoc'] . '\'');
            }
            if($arrParametros['campos']['productos'] != null){
                $this->_db->addCamposTabla('producto_id');
                $this->_db->addCamposValue('\'' . $arrParametros['campos']['productos'] . '\'');
            }
            if($arrParametros['campos']['productos_origen'] != null){
                $this->_db->addCamposTabla('nombre_reparto');
                $this->_db->addCamposValue('\'' . $arrParametros['campos']['productos_origen'] . '\'');
            }
            $this->_db->addFrom('presentaciones_productos');
            $this->_db->generarInsert();
            $qryPresentaciones = $this->_db->getQry();
            /*$resultado = $this->_db->ejecutar();

            if(count($this->_db->getArrError()) > 0){
                $arrError = $this->_db->getArrError();
                $arrDevolver = array(
                  "soyError" => true,
                  "nivel" => 1,
                  "mensaje" => "Error al generar la relacion"
                );
            echo json_encode($arrDevolver);exit;*/
	}
	$this->_db->addCamposUpdate('unidades= \'' . $arrParametros['campos']['cant_asoc'] . '\'');
        $this->_db->addFrom('presentaciones_productos');
	//$this->_db->addWhere('relacion_id = \'' . $resultado[0][relacion_id] .'\'');
	if($resultado[0][relacion_id]>0){
            $this->_db->addWhere('relacion_id = \'' . $resultado[0][relacion_id] .'\'');
        }else{
            $this->_db->addWhere('relacion_id = \'' . $id[0][nextval] .'\'');
        }
        $this->_db->generarUpdate();
        $qryPresentaciones.= $this->_db->getQry();

        $this->_db->addCamposUpdate('unidades_asociadas = \'' . $arrParametros['campos']['cant_asoc'] .'\'');
        if (isset($arrParametros['campos']['productos']) && is_numeric($arrParametros['campos']['productos'])) {
            $this->_db->addCamposUpdate('producto_id = \'' . $arrParametros['campos']['productos'] . '\'');
        }
        $this->_db->addCamposUpdate('cantidad = \'' . $arrParametros['campos']['cant_ingreso'] .'\'');
        $this->_db->addCamposUpdate('unidades= \'' . $arrParametros['campos']['cant_ingreso'] . '\'');
        $this->_db->addCamposUpdate('gtin_origen= \'' . $arrParametros['campos']['producto_gtin'] . '\'');
        //$this->_db->addCamposUpdate('lote_vencimiento= \'' . $arrParametros['campos']['lote_vencimiento'] . '\'');
        $this->_db->addCamposUpdate("lote_vencimiento = TO_DATE('" . $arrParametros['campos']['lote_vencimiento'] . "', 'DD-MM-YYYY')");
        $this->_db->addFrom('detalle_movimientos_sucursales');
        $this->_db->addWhere('detalle_id = \'' . $arrParametros['id'] .'\'');

        //generar qry
        $this->_db->generarUpdate();
        //asigamos el resulta que es un array a una variable
        $qryPresentaciones.= $this->_db->getQry();

        //echo $qryPresentaciones;exit;

        $this->_db->setQry($qryPresentaciones);
        $resultado = $this->_db->ejecutarTransaccion();

        if(count($this->_db->getArrError()) > 0){
            $arrDevolver = array(
                "soyError" => true,
                "nivel" => 0,
                "mensaje" =>"Error al actualizar"
                );
        }else{
            $arrDevolver = array(
                "soyError" => false,
                "nivel" => 1,
                "mensaje" =>"Listo la magia!"
                );
        }
        echo json_encode($arrDevolver);
    }
    public function actualizarObs($arrParametros){
        $this->_db->addCamposUpdate("obs ='{$arrParametros['campos']['obs']}'");
        $this->_db->addFrom('datos_movimientos_sucursales');
        $this->_db->addWhere('de_id_movimiento = \'' . $arrParametros['id'] .'\'');

        //generar qry
        $this->_db->generarUpdate();
        //asigamos el resulta que es un array a una variable
        $resultado = $this->_db->ejecutar();

        if ($resultado !== false && $resultado > 0) {
            $arrDevolver = array(
                "soyError" => false,
                "nivel" => 0,
                "mensaje" =>"Actualizado"
                );
        }else{

            $arrDevolver = array(
                "soyError" => true,
                "nivel" => 1,
                "mensaje" =>"Error en la actualizacion"
                );
        }
        echo json_encode($arrDevolver);

    }
}

//ALTER TABLE stock_completo ADD CONSTRAINT control_cantidad  CHECK (cantidad >= 0 )




    

