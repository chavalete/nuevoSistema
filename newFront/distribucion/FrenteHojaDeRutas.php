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

class FrentePedidos
{
    private $_db;
    private $_colObjDatoPedido= Array();
        
    private $_arrError = Array();

    //** NECESARIOS PARA EL FRENTE DE FRENTES//
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    //** FIN NECESIARIOS**//

    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
	$this->_objFuncionesComunes = new FuncionesComunes();

        
    }
    
    public function setColObjDatoPedido($obj){
        $this->_colObjDatoPedido[$obj->getPedidoId()] = $obj;
    }
    
    public function getColObjDatoPedido(){
        return $this->_colObjDatoPedido;
    }
    
    public function cargarLosDatosLazzy($arrIds){
    
	if(is_array($arrIds)){
	    foreach($arrIds as $id){
		$ids.= $id['pedido_id'] . ",";
	   }
		$ids=substr($ids, 0, -1);
	}else{
		$ids=$arrIds;
	}
	$this->_db->addSelect('*');
        $this->_db->addFrom('datos_pedidos');
        $this->_db->addWhere("pedido_id IN (" . $ids . ")" );
        $this->_db->addOrderBy('pedido_id DESC');
        
	$this->_db->generarSelect();
        
        $resultado = $this->_db->ejecutar();
        
        $FrenteClientes = NEW FrenteClientes();
        
        $FrenteClientes->cargarClientesLazzy($resultado);
        
        $arrObjClientes= $FrenteClientes->getColObjCliente();
        
        $FrentePaciente = NEW FrentePacientes();
        $FrentePaciente->cargarPacientesLazzy($resultado);
        $arrObjPaciente= $FrentePaciente->getColObjPaciente();
        
        $FrenteEstados= NEW FrenteEstados();
        $FrenteEstados->cargarEstadosLazzy($resultado);
        $arrObjEstados= $FrenteEstados->getColObjEstados();
        
        $FrenteLocalidades= NEW FrenteLocalidades();
        $FrenteLocalidades->cargarLocalidadesLazzy($resultado);
        $arrObjLocalidades= $FrenteLocalidades->getColObjLocalidades();

        foreach($resultado AS $datos){
       	    
	    $objDatosPedido= NEW DatoPedidoExtendido();
	    $objDatosPedido->cargarme($datos);
	    $objDatosPedido->setObjCliente($arrObjClientes[$datos[cliente_id]]);
	    $objDatosPedido->setObjPaciente($arrObjPaciente[$datos[paciente_id]]);
	    $objDatosPedido->setObjEstado($arrObjEstados[$datos[estado_id]]);
	    $objDatosPedido->setObjLocalidad($arrObjLocalidades[$datos[localidad_id]]);
	    $this->setColObjDatoPedido($objDatosPedido);
        }
    
    }
    
    public function cargarLosDetallesLazzy($arrIds){
    
	foreach($arrIds as $id){
	    $ids.= $id['pedido_detalle_id'] . ",";
	}
	
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
        $this->_db->addFrom('detalle_pedidos');
        $this->_db->addWhere("pedido_detalle_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        $FrenteProductos = NEW FrenteProductos();
        $FrenteProductos->cargarProductosLazzy($resultado);
        $arrObjProductos= $FrenteProductos->getColProductos();
        
        foreach($this->getColObjDatoPedido() AS $objPedido){
        
	    foreach($resultado AS $detalle){
	    
		$objDetallePedido= NEW DetallePedidoExtendido();
		$objDetallePedido->cargarme($detalle);
		$objDetallePedido->setObjProducto($arrObjProductos[$detalle[producto_id]]);
		$this->_colObjDatoPedido[$objPedido->getPedidoId()]->setColObjDetallePedido($objDetallePedido);
	    }
	}
    
    }
    
    
    public function cargarPedidoCompleto($pedidoId){
    
        $this->_db->addSelect('pedido_id, pedido_detalle_id');
	$this->_db->addFrom('datos_pedidos INNER JOIN detalle_pedidos USING(pedido_id)');
        $this->_db->addWhere('pedido_id = \'' . $pedidoId . '\'');      
        
        $this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
        $resultadoCabeceras = $this->_db->ejecutar();
        
        $this->cargarLosDatosLazzy($resultadoCabeceras['0']['pedido_id']);
	$this->cargarLosDetallesLazzy($resultadoCabeceras);
    }


    public function buscarPedido($arrParametros){


	$this->_db->addSelect('pedido_id');
	$this->_db->addFrom('datos_pedidos');
	$this->_db->addFrom('INNER JOIN detalle_pedidos USING(pedido_id)');


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
	if($arrParametros['clientes'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('cliente_id = \'' . $arrParametros[clientes] . '\'');
	}
	if($arrParametros['nroPedido'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('pedido_nro= \'' . $arrParametros[nroPedido] . '\'');
	}
	if($arrParametros['pacientes'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('paciente_id = \'' . $arrParametros[pacientes] . '\'');
	}
	if($arrParametros['estados'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('estado_id = \'' . $arrParametros[estados] . '\'');
	}
	if($arrParametros['fechaDesde'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('pedido_fecha_hora::date >= \'' . $arrParametros[fechaDesde] . '\'');
	}
	if($arrParametros['fechaHasta'] !=null && $arrParametros[desdeAlta]==null){
	    $this->_db->addWhere('pedido_fecha_hora::date <= \'' . $arrParametros[fechaHasta] . '\'');
	}
	
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenPedidos($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['desdeBusqueda']==false){
	    $this->_db->addLimit(500);
	}


	$this->_db->addGroup('pedido_id');
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
	    $arrIds[$i]['pedido_id']= $resultado[$i]['pedido_id'];
	}
	//var_dump($arrIds);exit;
	$this->cargarLosDatosLazzy($arrIds);
    }

    public function getError(){
        return $this->_arrError;
    }

    //**PARA EL FRENTE**//
    public function setOrdenPedidos($parametros){
	if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){
	    $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'pedido_id';
	    $this->_ordenColumnas['ordenarOrden']  =   'DESC';
	}
    }

    public function getOrdenPedidos(){
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

	foreach($this->_colObjDatoPedido AS $objPedido){
	
		$arr []   =    array
		    (
			//id si o si un solo string (sin espacios en blanco)
			"id"            =>  $objPedido->getPedidoId(),
			//define las herramientas
			"herramientas"   =>  array(
			    "editable"      =>  false,
			    "cancelable"    =>  true,
			    "detalles"      =>  true,
			    "imprimir"      =>  false,
			    "autogestion"      =>  false,
			    "enviarPedido"      =>  false
			    
			),
			//las celdas propiamente dichas, en el orden en que estan declaradas las columnas
			"cell"          => array(
			    $objPedido->getPedidoNro(),
			    $objPedido->getPedidoFechaHora(),
			    $objPedido->getObjCliente()->getClienteNombre(),
			    $objPedido->getObjEstado()->getEstadoNombre(),
			    $objPedido->getPedidoObs()
			),
		    );

	}
	return $arr;
    }

    public function armarDetalles($pedidoId){

	$this->cargarPedidoCompleto($pedidoId);
	
	foreach($this->_colObjDatoPedido AS $objPedido){
	
	
		$propiedadesPedido =   array(
			array(
			    "display"   =>  'Nro. Pedido',
			    "name"      =>  'pedido_nro',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getPedidoNro(),

			),
			array(
			    "display"   =>  'Fecha',
			    "name"      =>  'fecha_hora_pedido',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getPedidoFechaHora(),

			),
			array(
			    "display"   =>  'Cliente',
			    "name"      =>  'cliente_id',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getObjCliente()->getClienteNombre(),

			),
			array(
			    "display"   =>  'Paciente',
			    "name"      =>  'paciente_id',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getObjPaciente()->getPacienteNombre(),

			),
			array(
			    "display"   =>  'Estado',
			    "name"      =>  'estado_id',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getObjEstado()->getEstadoNombre(),

			),
			array(
			    "display"   =>  'Localidad',
			    "name"      =>  'localidad_id',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getObjLocalidad()->getLocalidadNombre(),

			),
			array(
			    "display"   =>  'Calle',
			    "name"      =>  'pedido_calle',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getPedidoCalle(),

			),
			array(
			    "display"   =>  'Altura',
			    "name"      =>  'pedido_altura',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getPedidoAltura(),

			),
			array(
			    "display"   =>  'Piso',
			    "name"      =>  'pedido_piso',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getPedidoPiso(),

			),
			array(
			    "display"   =>  'Departamento',
			    "name"      =>  'pedido_depto',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getPedidoDepto(),

			),
			array(
			    "display"   =>  'Telefono',
			    "name"      =>  'pedido_tel',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getPedidoTelefono(),

			),
			array(
			    "display"   =>  'Codigo postal',
			    "name"      =>  'codigo_postal',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getCodigoPostal(),

			),
			array(
			    "display"   =>  'Observaciones',
			    "name"      =>  'pedido_obs',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $objPedido->getPedidoObs(),

			),
		    );
	}
	foreach($objPedido->getColObjDetallePedido() AS $detalle){

    
	    $arr []   =    array
		(
		    //id si o si un solo string (sin espacios en blanco)
		    "id"            =>  $detalle->getPedidoDetalleId(),
		    //define las herramientas
		    "herramientas"   =>  array(
			 "editable"      =>  false,
			  ),
			 "bloqueado"     => false,	    
		    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
		    "cell"          => array(
			$detalle->getObjProducto()->getProductoNombre(),
			$detalle->getObjProducto()->getProductoPresentacion(),
			$detalle->getObjProducto()->getProductoGtin(),
			$detalle->getPedidoCantidad()
		    ),
		);
	}

    
	$detallesPedido    =   array(
	    "bloqueable"=> false,
	    "modelo"    =>  array(
		array(

		    "display"   =>  'Producto',
		    "name"      =>  'producto_id',
		    "editable"  =>  false,
		    "class"	=>'',
		    
		),
		array(

		    "display"   =>  'Presentacion',
		    "name"      =>  'producto_presentacion',
		    "editable"  =>  false,
		    "class"	=>'',
		    
		),
		array(
		    "display"   =>  'PRODUCTO GTIN',
		    "name"      =>  'producto_gtin',
		    "editable"  =>  $arrParametros[edicion],
		    "class"	=>'',
		),
		array(

		    "display"   =>  'Cantidad',
		    "name"      =>  'cantidad',
		    "editable"  =>  $arrParametros[edicion],
		    "class"	=>'',
		),

	    ),
	    "celdas"    =>  $arr
	);
	
	$array_devolver =   array(
	    "editable_cabecera" => false,
	    "propiedades"       =>  $propiedadesPedido,
	    "listadoDetalles"   =>  $detallesPedido
	);
	    return $array_devolver;
    }
    public function salvarme($arrParametros){
    
	
    
	$this->_db->addCamposTabla('seq_datos_pedidos_pedidos_id');
	$this->_db->generarProximo();
	$idPedido = $this->_db->ejecutar();
    
    
	$arrProductos = explode ("||",$arrParametros[productos]);
	array_pop($arrProductos);
	foreach ($arrProductos AS $linea){
		    
	    $arrProductosFinal = explode("#",$linea);
	    //var_dump($arrProductosFinal);
	    
	    $this->_db->addCamposTabla('seq_detalle_pedidos_detalle_id');
	    $this->_db->generarProximo();

	    $id = $this->_db->ejecutar();


	    $this->_db->addFrom('detalle_pedidos');

	    $this->_db->addCamposTabla('pedido_id');
	    $this->_db->addCamposTabla('producto_id');
	    $this->_db->addCamposTabla('cantidad');
	    $this->_db->addCamposTabla('pedido_detalle_id');
	    
	
	    $this->_db->addCamposValue($idPedido[0]['nextval']);
	    $this->_db->addCamposValue('\'' . $arrProductosFinal[0] . '\'');
	    $this->_db->addCamposValue('\'' . $arrProductosFinal[1] . '\'');
	    $this->_db->addCamposValue($id[0]['nextval']);

	    $this->_db->generarInsert();
	    //echo $this->_db->getQry();exit;
	    $resultado = $this->_db->ejecutar();
	    
	    if(count($resultado)==0){
		$arrDevolver = array(
			"soyError" => true,
			"nivel" => 1,
			"mensaje" =>'Error al salvar los detalles'
			);
	    echo json_encode($arrDevolver);exit;
	    }
	}
	
	    $this->_db->addCamposTabla('seq_datos_pedidos_pedido_nro');
	    $this->_db->generarProximo();
	    $pedidoNro= $this->_db->ejecutar();
	    
	    $this->_db->addFrom('datos_pedidos');

	    $this->_db->addCamposTabla('pedido_id');
	    $this->_db->addCamposTabla('cliente_id');
	    $this->_db->addCamposTabla('pedido_nro');
	    $this->_db->addCamposTabla('estado_id');
	    $this->_db->addCamposTabla('pedido_calle');
	    $this->_db->addCamposTabla('pedido_altura');
	    $this->_db->addCamposTabla('pedido_piso');
	    $this->_db->addCamposTabla('pedido_depto');
	    $this->_db->addCamposTabla('pedido_tel');
	    $this->_db->addCamposTabla('codigo_postal');
	    $this->_db->addCamposTabla('localidad_id');
	    //$this->_db->addCamposTabla('paciente_id');
	    $this->_db->addCamposTabla('pedido_usuario_id');
	    
	    
	
	    $this->_db->addCamposValue($idPedido[0]['nextval']);;
	    $this->_db->addCamposValue('\'' . $arrParametros['clientes'] . '\'');
	    $this->_db->addCamposValue('\'' . $pedidoNro[0]['nextval'] . '\'');
	    $this->_db->addCamposValue(7);
	    $this->_db->addCamposValue('\'' . $arrParametros['calles'] . '\'');
	    $this->_db->addCamposValue('\'' . $arrParametros['alturas'] . '\'');
	    $this->_db->addCamposValue('\'' . $arrParametros['piso'] . '\'');
	    $this->_db->addCamposValue('\'' . $arrParametros['depto'] . '\'');
	    $this->_db->addCamposValue('\'' . $arrParametros['telefono'] . '\'');
	    $this->_db->addCamposValue('\'' . $arrParametros['codigoPostal'] . '\'');
	    $this->_db->addCamposValue('\'' . $arrParametros['localidades'] . '\'');
	    //$this->_db->addCamposValue('\'' . $arrParametros['pacientes'] . '\'');
	    $this->_db->addCamposValue('\'' . $_SESSION['usuarioId'] . '\'');
	    
	    $this->_db->generarInsert();
	    //echo $this->_db->getQry();
	    $resultado = $this->_db->ejecutar();
	    if(count($resultado)==0){
		$arrDevolver = array(
			"soyError" => true,
			"nivel" => 1,
			"mensaje" =>'Error al salvar los datos'
			);
	    echo json_encode($arrDevolver);exit;
	    }
	    $arrDevolver = array(
			"soyError" => false,
			"nivel" => 1,
			"mensaje" =>'Pedido Cargado con Exito'
			);
	    echo json_encode($arrDevolver);exit;
    }
}
