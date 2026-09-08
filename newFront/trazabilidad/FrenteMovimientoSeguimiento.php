<?php
//ALMACENAMIENTO
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
//TRAZABILIDAD
require_once 'DatoMovimientoExtendido.php';
require_once 'DatoTrazabilidadExtendido.php';
//STOCK
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/EstanteriaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/LoteExtendido.php';
//ERROR
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/error_2.0/FrenteError.php';

/**
 * Description of FrenteTrazabilidad
 *
 * @author by dMELMAC
 */

class FrenteMovimientoSeguimiento
{
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
    
    /*
    public function setArrError($arrError){
        $this->_arrError = $arrError;
    }
    
    public function getArrError(){
        return $this->_arrError ;
    }
     */
    
    
    public function cargarMovimiento($id){
        $objMovimiento = new DatoMovimientoExtendido();
        $objMovimiento->cargarme($id);
        $this->setColObjDatoMovimiento($objMovimiento);
    }

    public function busqueda ($arrParametros){
        $this->_db->addSelect('id_movimiento');
        $this->_db->addFrom('datos_movimientos dm');
        $this->_db->generarSelect();
        
        $resultado = $this->_db->ejecutar();
        foreach($resultado as $rstdo){
            $this->cargarMovimiento($rstdo['id_movimiento']);
        }
    }
    public function buscarMovimientoPorCodigo($arrParametros){
    
	//SETEO EL PAGINADOR, NUNCA PUEDE HABER MAS DE 1
	$primerRegistro = 0;
	$ultimoRegistro = 11;
	$this->_db->addSelect('trazabilidad_id');
	$this->_db->addFrom('datos_movimientos dm');
	$this->_db->addFrom('INNER JOIN movimientos_trazabilidad USING(id_movimiento)');
	$this->_db->addFrom('INNER JOIN datos_trazabilidad USING(trazabilidad_id)');
	$this->_db->addWhere('trazabilidad_codigo = \'' . $arrParametros[codigoTrazabilidad] . '\'');
	$this->_db->addWhere('dm.tipo_movimiento_id = 1');
	$this->setOrdenMovimientos($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	$this->_db->addGroup('trazabilidad_id, dm.id_movimiento');
	
	$this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
    
	$arr =  $this->_db->ejecutar();
	
	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro){
	    $ultimoRegistro = $this->getTotal();
	}	
	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
	    $this->cargarCodigoTrazabilidad($arr[$i]['trazabilidad_id']);
	}
    
    }

    public function buscarMovimiento ($arrParametros){


	$this->_db->addSelect('id_movimiento');
	$this->_db->addFrom('datos_movimientos dm');
	$this->_db->addFrom('INNER JOIN detalle_movimientos USING(id_movimiento)');
	$this->_db->addFrom('INNER JOIN datos_productos USING(producto_id)');
	$this->_db->addFrom('INNER JOIN datos_marcas USING(marca_id)');
	$this->_db->addFrom('INNER JOIN datos_proveedores ON  datos_productos.prove_id = datos_proveedores.prove_id');
	$this->_db->addFrom('INNER JOIN datos_clientes ON  dm.cliente_id = datos_clientes.cliente_id');
	$this->_db->addFrom('INNER JOIN tipos_movimientos_trazas tmt ON(dm.tipo_movimiento_id = tmt.tipo_movimiento_id)');
	$this->_db->addFrom('LEFT JOIN datos_pacientes USING(paciente_id)');
	//**ANALIZO EL WHERE**//
	if($arrParametros['idMovimiento']){
	    $this->_db->addWhere('id_movimiento = ' . $arrParametros['idMovimiento']);
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
	}
    //echo $this->_objFuncionesComunes->parsearAutocompletar($arrParametros['clientes']);
	if($arrParametros['clientes'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('dm.cliente_id = \'' . $arrParametros['clientes'] . '\'');
	}

	if($arrParametros['custodiantes'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('custodiante_id = \'' . $arrParametros['custodiantes'] . '\'');
	}
	if($arrParametros['obraSocial'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('obra_social_id = \'' . $arrParametros['obraSocial'] . '\'');
	}

	if($arrParametros['medicos'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('medico_id = \'' . $arrParametros['medicos'] . '\'');
	}
	if($arrParametros['vendedores'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('vendedor_id = \'' . $arrParametros['vendedores'] . '\'');
	}
	if($arrParametros['pacientes'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('paciente_id = \'' . $arrParametros['pacientes'] . '\'');
	}
	if($arrParametros['pm'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('pm  ILIKE \'%' . $arrParametros[pm] . '%\'');
	}
	if($arrParametros['despacho'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('despacho_nro  = \'' . $arrParametros[despacho] . '\'');
	}
	if($arrParametros['proveedores'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('dm.prove_id = \'' . $arrParametros['proveedores'] . '\'');
	}
	if($arrParametros['proveedoresProductos'] !=null && $arrParametros[desdeAlta]==null){

	    $this->_db->addWhere('datos_productos.prove_id = \'' . $arrParametros['proveedoresProductos'] . '\'');
	}
	if($arrParametros['marcas'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('datos_productos.marca_id = \'' . $arrParametros['marcas'] . '\'');
	}
	if($arrParametros['productos'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('detalle_movimientos.producto_id = \'' . $arrParametros['productos'] . '\'');
	}
	if($arrParametros['estanterias'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('estanteria_id = \'' . $arrParametros['estanterias'] . '\'');
	}
	if($arrParametros['remitos'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('remito_nro = \'' . $arrParametros[remitos] . '\'');
	}
	if($arrParametros['facturas'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('factura_nro = \'' . $arrParametros[facturas] . '\'');
	}
	if($arrParametros['nroOrden'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('orden_compra = \'' . $arrParametros[nroOrden] . '\'');
	}
	if($arrParametros['nroNotaCarga'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('nro_nota_carga = \'' . $arrParametros[nroNotaCarga] . '\'');
	}
	if($arrParametros['codigoTrazabilidad'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('trazabilidad_codigo = \'' . $arrParametros[codigoTrazabilidad] . '\'');
	}
	if($arrParametros['codigoReferencia'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('codigo_referencia = \'' . $arrParametros[codigoReferencia] . '\'');
	}
	if($arrParametros['valor'] !=null ){
            $this->_db->addWhere('codigo_referencia = \'' . $arrParametros['valor'] . '\' ');
        }
	if($arrParametros['tipoMovimiento'] !=null && $arrParametros[desdeAlta]==null)	{
	    $this->_db->addWhere('tipo_movimiento_id = \'' . $arrParametros[tipoMovimiento] . '\'');
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
    
	if($arrParametros['tipo']=='listadoDePendientes'){

	    $this->_db->addWhere('tmt.tipo_movimiento_id  IN (2,7,8,9)');
	    $this->_db->addWhere('dm.cliente_id NOT IN (1,2,3)');
	}
	
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenMovimientos($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['desdeBusqueda']==false){
	    $this->_db->addLimit(50);
	}	
	$this->_db->addGroup('id_movimiento');
	//*FIN LIMIT*//
	$this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
    
	$arr =  $this->_db->ejecutar();
	
	//ahora seteamos el total y la pagina
	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro){
	    $ultimoRegistro = $this->getTotal();
	}	
	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
	    $this->cargarMovimiento($arr[$i]['id_movimiento']);
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
       
        foreach($this->_colObjDatoMovimiento AS $objMovimiento){
		
		    //$objMovimiento->getMovimientoFinalizado();exit;
		    if($objMovimiento->getMovimientoFinalizado() ==false){
			
			$imprimir=false;
			$enviar=true;
		    }else{
			
			$imprimir=true;
			$enviar=false;
		    }
		    $arr []   =    array
			(
			//id si o si un solo string (sin espacios en blanco)
			"id"            =>  $objMovimiento->getIdMovimiento(),
			//define las herramientas
			"herramientas"   =>  array(
			    "cancelable"    =>  false,
			    "editable"      =>  false,
			    "detalles"      =>  true,
			    "imprimir"      =>  $imprimir,
			    "enviarTransaccion"      =>  $enviar			    

			),
			//las celdas propiamente dichas, en el orden en que estan declaradas las columnas
			"cell"          => array(
			    $objMovimiento->getIdMovimiento(),
			    $objMovimiento->getMovimientoFechaHora(),
                            $objMovimiento->getPedidoId(),
			    $objMovimiento->getObjCliente()->getClienteNombre(),
			    $objMovimiento->getObjPaciente()->getPacienteNombre(),
			    $objMovimiento->getNroNotaCarga(),
			    $objMovimiento->getFacturaNro() .  $objMovimiento->getRemitoNro(),
			    $objMovimiento->getFechaVencimientoAlquiler(),
			    $objMovimiento->getObjTipoMovimiento()->getTipoMovimientoNombre()
			),
		    );
	}
	return $arr;
    }

    //**PARA EL FRENTE FIN**//

    public function armarDetalles($id){

	$this->cargarMovimiento($id);
	
	foreach ($this->getColObjDatoMovimiento() AS $objMovimiento){
	
	/*echo "<pre>";
	print_r($objMovimiento);
	echo "</pre>";*/
	
	if($objMovimiento->getMovimientoFinalizado() == true){
	    $alertar=true;	
            $editar = false;
	}else{
	    $alertar=false;
            $editar = true;
	}
        
        //Para saber si dejamos edirar el cliente, solo para los movimientos que vienen de "En Custodia".
        $editarCliente = false;
        if($objMovimiento->getDeIdMovimiento() > 0 && $objMovimiento->getMovimientoFinalizado() == false ){
            $objDeIdMovimiento = new DatoMovimientoExtendido();
            $objDeIdMovimiento->cargarme($objMovimiento->getDeIdMovimiento());
            if($objDeIdMovimiento->getObjTipoMovimiento()->getTipoMovimientoId() === 8){
                $editarCliente = true;           
            }
            
        }
        
	$propiedadesTransaccion =   array(
			array(

			    "display"   =>  'Nro',
			    "name"      =>  'id_movimiento',
			    "editable"  =>  false,
			    "class"	=>  '',
			    "value"     =>  $objMovimiento->getIdMovimiento(),
			),
			array(
			    "display"   =>  'Cliente',
			    "name"      =>  'cliente_id',
			    "editable"  =>   $editarCliente,
			    "class"	=>  'autocomplete',
			    "value"     =>  $objMovimiento->getObjCliente()->getClienteNombre(),

			),
			array(
			    "display"   =>  'Domicilio de Entrega',
			    "name"      =>  'domicilio_entrega',
			    "editable"  =>  $editar,
			    "class"	=>  '',
			    "value"     =>  $objMovimiento->getDomicilioEntrega(),

			),
			array(
			    "display"   =>  'Medico',
			    "name"      =>  'medicos',
			    "editable"  =>  $editar,
			    "class"	=>  'autocomplete',
			    "value"     =>  $objMovimiento->getObjMedico()->getMedicoNombre(),

			),
			array(
			    "display"   =>  'Afiliado',
			    "name"      =>  'pacientes',
			    "editable"  =>  $editar,
			    "class"	=>  'autocomplete',
			    "value"     =>  $objMovimiento->getObjPaciente()->getPacienteNombre(),

			),
			array(
			    "display"   =>  'Vendedor',
			    "name"      =>  'vendedores',
			    "editable"  =>  $editar,
			    "class"	=>  'autocomplete',
			    "value"     =>  $objMovimiento->getObjVendedor()->getVendedorNombre(),

			),
			array(
			    "display"   =>  'Distribuidor',
			    "name"      =>  'distribuidores',
			    "editable"  =>  $editar,
			    "class"	=>  'autocomplete',
			    "value"     =>  $objMovimiento->getObjDistribuidor()->getDistribuidorNombre(),

			),
			array(
			    "display"   =>  'Custodia',
			    "name"      =>  'custodiantes',
			    "editable"  =>  $editar,
			    "class"	=>  'autocomplete',
			    "value"     =>  $objMovimiento->getObjCustodia()->getCustodianteNombreCompleto(),

			),
			array(
			    "display"   =>  'Dias de Alquiler',
			    "name"      =>  'cantDiasAlquiler',
			    "editable"  =>  $editar,
			    "class"	=>  '',
			    "value"     =>  $objMovimiento->getCantDiasAlquiler(),

			),
			array(
			    "display"   =>  'Fecha de Inicio',
			    "name"      =>  'fecha_inicio_alquiler',
			    "editable"  =>  true,
			    "class"	=>  'datePicker',
			    "value"     =>  $objMovimiento->getFechaInicioAlquiler(),

			),
			array(
			    "display"   =>  'Nro. Remito',
			    "name"      =>  'nro_remito',
			    "editable"  =>  $editar,
			    "class"	=>'',
			    "value"     =>  $objMovimiento->getRemitoNro(),

			),
			array(
			    "display"   =>  'Nro. Nota de Carga',
			    "name"      =>  'nro_nota_carga',
			    "editable"  =>  $editar,
			    "class"	=>'',
			    "value"     =>  $objMovimiento->getNroNotaCarga(),

			),
			array(
			    "display"   =>  'Obs',
			    "name"      =>  'obs',
			    "editable"  =>  $editar,
			    "class"	=>'',
			    "value"     =>  $objMovimiento->getObs(),

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
		}
		

	$this->_db->addSelect('trazabilidad_codigo');
	
	$this->_db->addSelect('producto_nombre || producto_presentacion AS producto_nombre');
	$this->_db->addSelect('lote');
	$this->_db->addSelect('id_movimiento');
	$this->_db->addSelect('codigo_referencia');
	$this->_db->addSelect('renovado');
	$this->_db->addSelect('to_char(lote_vencimiento,\'DD-MM-YYYY\') AS lote_vencimiento');
	$this->_db->addFrom('movimientos_trazabilidad');
	$this->_db->addFrom('INNER JOIN datos_trazabilidad USING(trazabilidad_id)');
	$this->_db->addFrom('INNER JOIN datos_lotes USING(lote_id)');
	$this->_db->addFrom('INNER JOIN datos_productos ON datos_trazabilidad.producto_id = datos_productos.producto_id');
	$this->_db->addWhere('id_movimiento = \'' . $id . '\'');
	$this->_db->addGroup('renovado,codigo_referencia,trazabilidad_codigo,producto_nombre,lote,lote_vencimiento,producto_presentacion, id_movimiento, alquilable');
	$this->_db->addOrderBy('trazabilidad_codigo, alquilable desc');
	$this->_db->generarSelect();
    
	//echo $this->_db->getQry();exit;
	$arrDetalles =  $this->_db->ejecutar();

	foreach($arrDetalles AS $detalle){

		$arr []   =    array
		    (
			//id si o si un solo string (sin espacios en blanco)
			"id"            =>  $detalle[trazabilidad_codigo],
			//define las herramientas
			"herramientas"   =>  array(
			    "editable"      =>  false,
			  ),
			 "bloqueado"     => false,
			 "alertado" => $detalle['renovado'],
			    
			//las celdas propiamente dichas, en el orden en que estan declaradas las columnas
			"cell"          => array(
			    $detalle[trazabilidad_codigo],
			    $detalle[producto_nombre],
			    $detalle[codigo_referencia],
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
		    "display"   =>  'Codigo Referencia',
		    "name"      =>  'codigo_referencia',
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
	    "listadoDetalles"   =>  $detallesTransaccion,
	    "editable_cabecera" => true,
	    "alertar" => $alertar
	);
	return $array_devolver;
    }
    
    public function cargarCodigoTrazabilidad($arrParametros){
    
    
	if(substr($arrParametros[codigo],0,2) ==21){
	
	    $codigo = substr($arrParametros[codigo],2,10);
	
	    $this->_db->addSelect('numero_serial');
	    $this->_db->addSelect('producto AS producto_nombre');
	    $this->_db->addFrom('detalle_remesas');
	    $this->_db->addWhere('trim(numero_serial) = \'' . $codigo . '\'');
	    $this->_db->generarSelect();
	
	    $arrDatos =  $this->_db->ejecutar();
	    
		
		//var_dump($arrDatos);exit;
	}else{
    
	    $this->_db->addSelect('trazabilidad_codigo');
	    $this->_db->addSelect('codigo_referencia AS producto_nombre');
	    $this->_db->addFrom('datos_trazabilidad');
	    $this->_db->addFrom('INNER JOIN datos_productos USING(producto_id)');
	    $this->_db->addWhere('trazabilidad_codigo = \'' . $arrParametros[codigo] . '\'');
	    $this->_db->addGroup('trazabilidad_codigo,codigo_referencia');
	    $this->_db->addOrderBy('trazabilidad_codigo');
	    $this->_db->generarSelect();
	    //echo $this->_db->getQry();exit;
	    $arrDatos =  $this->_db->ejecutar();
	}
	
	if($arrDatos[0]==null){

	    $arrDatos[0][producto_nombre]="No existe";
	
	}
	
	$array_devolver = array(
		'codigo'      => $arrParametros['codigo'],
		'descripcion' => $arrDatos[0]['producto_nombre']
		);
		
	echo json_encode($array_devolver);
    
    }
    public function actualizarme($arrParametros){

	$datoMovimientoExtendido= NEW DatoMovimientoExtendido();
	$datoMovimientoExtendido->actualizarme($arrParametros);
    }
    public function finalizarMovimiento($id){
	
	$datoMovimientoExtendido = NEW DatoMovimientoExtendido();
	$datoMovimientoExtendido->finalizarMovimiento($id);
    }
}

//ALTER TABLE stock_completo ADD CONSTRAINT control_cantidad  CHECK (cantidad >= 0 )

