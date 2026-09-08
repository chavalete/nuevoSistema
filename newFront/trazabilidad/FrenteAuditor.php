<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/trazabilidad/MovimientoTrazabilidadExtendido.php';

/*
 * dMELMAC 
 * 
 */

/**
 * Description of FrenteTrazabilidad
 *
 * @author by dMELMAC
 */

class FrenteAuditor
{
    private $_db;
    private $_colObjMovimiento = Array();
    private $_totalAcumulado = 0;
    
    private $_totalParcial = 0;
    
    private $_arrError = Array();

    //** NECESARIOS PARA EL FRENTE DE FRENTES//
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    //** FIN NECESIARIOS**//

    private $_objFuncionesComunes;


    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
	$this->_objFuncionesComunes = NEW FuncionesComunes();
    }
    
    public function setColObjMovimiento($objMovimiento){
        $this->_colObjMovimiento[] = $objMovimiento;
    }

    public function getColObjMovimiento(){
        return $this->_colObjMovimiento;
    }
    
    public function setTotalAcumulado($cantidad){
       
	
        $this->_totalAcumulado = $this->getTotalAcumulado() + $cantidad;
        
    }
    
    public function getTotalAcumulado(){
        return $this->_totalAcumulado;
    }
    
    public function setTotalParcial($total){
        $this->_totalParcial =  $total;
    }
    
    public function getTotalParcial(){
        return $this->_totalParcial;
    }

    public function getParametrosBusquedaInicial(){
    
	$this->_db->addSelect('movimiento_fecha_hora::date AS movimiento_fecha, producto_id');
        $this->_db->addFrom('detalle_movimientos dem');
        $this->_db->addFrom('INNER JOIN datos_movimientos dm USING(id_movimiento)');
        $this->_db->addOrderBy('id_movimiento DESC LIMIT 1');
        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        
        $resultado= $this->_db->ejecutar();
        
        return $resultado;
    
    
    }
    public function getTotalAcumuladoInicio($arrBusquedaInicial){
    
	if($arrBusquedaInicial['desdeBusqueda']!=true){

	
	    $this->_db->addSelect('sum(unidades) AS total');
	    $this->_db->addFrom('datos_movimientos');
	    $this->_db->addFrom('INNER JOIN detalle_movimientos USING(id_movimiento)');
	    $this->_db->addWhere('producto_id = \'' . $arrBusquedaInicial[0][producto_id] . '\'');
	    $this->_db->addWhere('movimiento_fecha_hora::date < \'' . $arrBusquedaInicial[0][movimiento_fecha] . '\'');
	    
	    //echo $this->_db->getQry();
	    
	    
	}else{
	
	    //$arrFecha = $this->getParametrosBusquedaInicial();
	
	    $this->_db->addSelect('sum(unidades) AS total');
	    $this->_db->addFrom('datos_movimientos');
	    $this->_db->addFrom('INNER JOIN detalle_movimientos USING(id_movimiento)');
	    $this->_db->addWhere('producto_id = \'' . $arrBusquedaInicial[productos] . '\'');
	    if($arrBusquedaInicial['fechaDesde'] !=null){
		$this->_db->addWhere('movimiento_fecha_hora::date < \'' . $arrBusquedaInicial[fechaDesde] . '\'');
	    }else{
		//$this->_db->addWhere('movimiento_fecha_hora::date < \'' . $arrFecha[0][movimiento_fecha] . '\'');
		$this->_db->addWhere('movimiento_fecha_hora::date >  now()::date');
	    }       
	}
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultadoAcumulado= $this->_db->ejecutar();
        //var_dump($resultadoAcumulado);exit;
        
        return $resultadoAcumulado[0][total];
    
    
    }
    public function setArrError($arrError){
        $this->_arrError = $arrError;
    }
    
    public function getArrError(){
        return $this->_arrError ;
    }

    
    public function cargarMovimiento($id){
        $objMovimiento = new MovimientoTrazabilidadExtendido();
        $objMovimiento->cargarMe($id);
        if(count($objMovimiento->getArrError()) > 0){
            $this->setArrError($objMovimiento->getArrError());
            return;
        }
        $objMovimiento->setTotal($this->getTotalParcial()  + $objMovimiento->getObjDetalleMovimiento()->getUnidades());
        $this->setTotalParcial($this->getTotalParcial()  + $objMovimiento->getObjDetalleMovimiento()->getUnidades());
        $this->setColObjMovimiento($objMovimiento); 
    }
    
    public function buscarMovimiento ($arrParametros){
   
	if($arrParametros['desdeBusqueda']==false && $arrParametros['value']==null){
	    $arrBusquedaInicial = $this->getParametrosBusquedaInicial();
        }
        if($arrParametros['desdeBusqueda']==false){
        
	    $this->setTotalParcial($this->getTotalAcumuladoInicio($arrBusquedaInicial));
	     
        }elseif($arrParametros['desdeBusqueda']==true){
	    $this->setTotalParcial($this->getTotalAcumuladoInicio());
        }
    
        $this->_db->addSelect('dem.detalle_id, dem.unidades');
        $this->_db->addFrom('detalle_movimientos dem');
        $this->_db->addFrom('INNER JOIN datos_movimientos dm USING(id_movimiento)');
        $this->_db->addFrom('INNER JOIN datos_lotes dl USING(lote_id)');
        $this->_db->addFrom('INNER JOIN movimientos_trazabilidad mt USING(id_movimiento)');
        $this->_db->addFrom('INNER JOIN datos_trazabilidad dt  ON(dt.trazabilidad_id=mt.trazabilidad_id AND dt.producto_id=dem.producto_id AND dt.lote_id=dem.lote_id)');
	

        //**ANALIZO EL WHERE**//
        if($arrParametros['stringBuscar']){
            $this->_db->addWhere('dm.id_movimiento ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
            $primerRegistro = 0;
            $ultimoRegistro = 11;
	}else{
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
        }

        if($arrParametros['productos'] !=null){
            list($productoId, $relacionId) = explode('#', $arrParametros['productos']) ;
            $this->_db->addWhere('dem.producto_id = \'' . $productoId . '\'');
        }
        if($arrParametros['estanterias'] !=null){
            $this->_db->addWhere('dem.estanteria_id = \'' . $arrParametros['estanterias'] . '\'');
        }
        if($arrParametros['almacenes'] !=null){
            $this->_db->addWhere('dem.almacen_id = \'' . $arrParametros['almacenes'] . '\'');
        }
        if($arrParametros['sucursales'] !=null){
            $this->_db->addWhere('dem.sucursal_id = \'' . $arrParametros['sucursales'] . '\'');
        }
        if($arrParametros['lotes'] !=null){
            $this->_db->addWhere('lote = \'' . $arrParametros['lotes'] . '\'');
        }
        if($arrParametros['codigosAuditor'] !=null){
	    $arrTraza = $this->_objFuncionesComunes->parsearCodigoTrazabilidadEstandar($arrParametros['codigosAuditor']);
            //var_dump($arrTraza);
            $this->_db->addWhere('trazabilidad_codigo = \'' . $arrTraza['serie'] . '\'');
        }
        if($arrParametros['fechaDesde'] !=null){
            $ano= substr($arrParametros['fechaDesde'],6,4);
	    $mes = substr($arrParametros['fechaDesde'],3,2);
	    $dia = substr($arrParametros['fechaDesde'],0,2);
	    $fechaDesde = $ano."/".$mes ."/".$dia;
	    $this->_db->addWhere('movimiento_fecha_hora::date >= \'' . $fechaDesde . '\'');
        }

        if($arrParametros['fechaHasta'] !=null){
            $ano= substr($arrParametros['fechaHasta'],6,4);
	    $mes = substr($arrParametros['fechaHasta'],3,2);
	    $dia = substr($arrParametros['fechaHasta'],0,2);
	    $fechaHasta = $ano."/".$mes ."/".$dia;
            $this->_db->addWhere('movimiento_fecha_hora::date <= \'' . $fechaHasta . '\'');
        }

        if($arrParametros['value'] !=null ){
            $this->_db->addWhere('trazabilidad_codigo = \'' . $arrParametros['value'] . '\'');
        }
        if($arrBusquedaInicial != NULL){
        
	    $this->_db->addWhere('dm.movimiento_fecha_hora::date = \'' . $arrBusquedaInicial[0][movimiento_fecha] . '\'');
	    $this->_db->addWhere('dem.producto_id = \'' . $arrBusquedaInicial[0][producto_id] . '\'');
	    
        }
        
   
        
        //**fin where **/
        //**INICIO ORDER BY**//
        //*seteo el orden*/
        
        
        $this->setOrdenMovimientos($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //**FIN ORDER BY**/

        $this->_db->addGroup('dem.detalle_id, dem.unidades,dem.id_movimiento');

        //*INICIO LIMIT*//
        if($arrParametros['tipo']=='auditorStock'){
           // $this->_db->addLimit(100);
        }
        //*FIN LIMIT*//
        $this->_db->generarSelect();
        //4echo $this->_db->getQry();
        $arr =  $this->_db->ejecutar();
        //ahora seteamos el total y la pagina
        $this->setTotal(count($arr));
        $this->setPagina($arrParametros['pagina']);
        if($this->getTotal() < $ultimoRegistro){
            $ultimoRegistro = $this->getTotal();
        }	

        //echo $primerRegistro . " ---" . $ultimoRegistro;
        
        
        for($i=0; $i<$primerRegistro; $i++){
	    
            $this->setTotalParcial($this->getTotalParcial() + $arr[$i]['unidades']);
        }
        
        for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
            $this->cargarMovimiento($arr[$i]['detalle_id']);
        }
    }

    public function setOrdenMovimientos($parametros){
        if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != "")
        {
            $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
        }else
        {
            //definir default
            $this->_ordenColumnas['ordenarPor']    =   'id_movimiento';
            $this->_ordenColumnas['ordenarOrden']  =   'ASC';
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

    public function getDetalles(){
        
        foreach($this->getColObjMovimiento() AS $objMovimiento){
            //var_dump($objMovimiento);exit;
            $this->setTotalAcumulado($objMovimiento->getObjDetalleMovimiento()->getCantidad());
            $arr[] = array(
                    //id si o si un solo string (sin espacios en blanco)
                    "id"=>  $objMovimiento->getObjDetalleMovimiento()->getDetalleId(), //asi cuanta desde 1 en adelante
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  false,
                        "editable"      =>  false,
                        "detalles"      =>  true,
                        "imprimir"      =>  false
                        ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell" => array(
                                $objMovimiento->getMovimientoFechaHora(),//fecha_hora
				$objMovimiento->getObjDetalleMovimiento()->getObjProducto()->getProductoNombre() . " " . $objMovimiento->getObjDetalleMovimiento()->getObjProducto()->getProductoPresentacion(),
                                $objMovimiento->getObjTipoMovimiento()->getTipoMovimientoNombre(),//nombre del movimiento 
                                $objMovimiento->getObjDatoMovimiento()->getRemitoNro() .  $objMovimiento->getObjDatoMovimiento()->getFacturaNro(),//remito/factura
                                $objMovimiento->getObjDetalleMovimiento()->getObjEstanteria()->getEstanteriaNombre(),
                                $objMovimiento->getObjDetalleMovimiento()->getObjLote()->getLote(),
                                $objMovimiento->getObjDetalleMovimiento()->getObjLote()->getLoteVencimiento(),
                                $objMovimiento->getObjDetalleMovimiento()->getUnidades(),
                                $objMovimiento->getTotal()
                ),
            );
        }       
        return $arr;
    }
    
    //**PARA EL FRENTE FIN**//
    public function armarDetalles($id){

	$this->cargarMovimiento($id);
	
	foreach ($this->getColObjMovimiento() AS $objMovimiento){
	
	/*echo "<pre>";
	print_r($objMovimiento);
	echo "</pre>";*/
	if($objMovimiento->getObjTipoMovimiento()->getTipoMovimientoALta() == true){

		    $propiedadesTransaccion =   array(
			array(

			    "display"   =>  'Nro',
			    "name"      =>  'id_movimiento',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objMovimiento->getObjDatoMovimiento()->getIdMovimiento(),
			),
			array(
			    "display"   =>  'Proveedor',
			    "name"      =>  'prove_id',
			    "editable"  =>  false,
			    "class"	=> 'autocomplete',
			    "value"     =>  $objMovimiento->getObjDatoMovimiento()->getObjProve()->getProveNombre(),

			),
			array(
			    "display"   =>  'Tipo Movimiento',
			    "name"      =>  'tipo_movimiento_id',
			    "editable"  =>  false,
			    "class"	=>  '',
			    "value"     =>  $objMovimiento->getObjTipoMovimiento()->getTipoMovimientoNombre(),

			),
			array(
			    "display"   =>  'Fecha',
			    "name"      =>  'movimiento_fecha_hora',
			    "editable"  =>  false,
			    "class"	=>  'datePicker',
			    "value"     =>  $objMovimiento->getMovimientoFechaHora(),
			)
		    );
		}else{
			$propiedadesTransaccion =   array(
			array(

			    "display"   =>  'Nro',
			    "name"      =>  'id_movimiento',
			    "editable"  =>  false,
			    "class"	=>  '',
			    "value"     =>  $objMovimiento->getObjDatoMovimiento()->getIdMovimiento(),
			),
			array(
			    "display"   =>  'Cliente',
			    "name"      =>  'cliente_id',
			    "editable"  =>  true,
			    "class"	=>  'autocomplete',
			    "value"     =>  $objMovimiento->getObjDatoMovimiento()->getObjCliente()->getClienteNombre(),

			),
                        array(
			    "display"   =>  'Paciente',
			    "name"      =>  'paciente_id',
			    "editable"  =>  true,
			    "class"	=>  'autocomplete',
			    "value"     =>  $objMovimiento->getObjDatoMovimiento()->getObjPaciente()->getPacienteNombre(),

			),    
			array(
			    "display"   =>  'Tipo Movimiento',
			    "name"      =>  'tipo_movimiento_id',
			    "editable"  =>  false,
			    "class"	=>  '',
			    "value"     =>  $objMovimiento->getObjTipoMovimiento()->getTipoMovimientoNombre(),

			),
			array(
			    "display"   =>  'Fecha',
			    "name"      =>  'movimiento_fecha_hora',
			    "editable"  =>  true,
			    "class"	=>  'datePicker',
			    "value"     =>  $objMovimiento->getMovimientoFechaHora(),
			)
		    );
		}
	$idMovimiento= $objMovimiento->getObjDatoMovimiento()->getIdMovimiento();
	}
	

	$this->_db->addSelect('trazabilidad_codigo');
	$this->_db->addSelect('producto_nombre || producto_presentacion AS producto_nombre');
	$this->_db->addSelect('lote');
	$this->_db->addSelect('id_movimiento');	
	$this->_db->addSelect('producto_gtin');	
	$this->_db->addSelect('to_char(lote_vencimiento,\'DD-MM-YYYY\') AS lote_vencimiento');
	$this->_db->addFrom('movimientos_trazabilidad');
	$this->_db->addFrom('INNER JOIN datos_trazabilidad USING(trazabilidad_id)');
	$this->_db->addFrom('INNER JOIN datos_lotes USING(lote_id)');
	$this->_db->addFrom('INNER JOIN datos_productos ON datos_lotes.producto_id = datos_productos.producto_id');
	$this->_db->addWhere('id_movimiento = \'' . $idMovimiento . '\'');
	$this->_db->addGroup('trazabilidad_codigo,producto_nombre,lote,lote_vencimiento,producto_presentacion, id_movimiento,producto_gtin');
	$this->_db->addOrderBy('trazabilidad_codigo');
	$this->_db->generarSelect();
    
	//echo $this->_db->getQry();exit;
	$arrDetalles =  $this->_db->ejecutar();


	foreach($arrDetalles AS $detalle){

		$arr []   =    array
		    (
			//id si o si un solo string (sin espacios en blanco)
			"id"            =>  $detalle[id_movimiento],
			//define las herramientas
			"herramientas"   =>  array(
			    "editable"      =>  false
			    
			),
			//las celdas propiamente dichas, en el orden en que estan declaradas las columnas
			"cell"          => array(
			    $detalle[trazabilidad_codigo],
			    $detalle[producto_nombre],
			    $detalle[producto_gtin],
			    $detalle[lote],
			    $detalle[lote_vencimiento]
			),
		    );
		}

    
	$detallesTransaccion    =   array(
	    "modelo"    =>  array(
		array(

		    "display"   =>  'Codigo Unico',
		    "name"      =>  'trazabilidad_codigo',
		    "class"	=>  '',
		    "editable"  =>  false,
		),
		array(
		    "display"   =>  'Producto',
		    "name"      =>  'producto_nombre',
		    "class"	=>  'autocomplete',
		    "editable"  =>  false,
		),
		array(
		    "display"   =>  'GTIN',
		    "name"      =>  'producto_gtin',
		    "class"	=>  '',
		    "editable"  =>  false,
		),
		array(
		    "display"   =>  'Lote',
		    "name"      =>  'Lote',
		    "class"	=>  '',
		    "editable"  =>  false,

		),
		array(
		    "display"   =>  'Lote Vence',
		    "name"      =>  'lote_vencimiento',
		    "class"	=>  'datePicker',
		    "editable"  =>  false,
		),

	    ),
	    "celdas"    =>  $arr
	);
	
	$array_devolver =   array(
	    "propiedades"       =>  $propiedadesTransaccion,
	    "listadoDetalles"   =>  $detallesTransaccion
	);
	
	    return $array_devolver;
    }
}
