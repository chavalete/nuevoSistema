<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/ClienteExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteClientes
{
    private $_colClientes = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;

    public function __construct(){
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
	$this->_objFuncionesComunes = new FuncionesComunes();
    }
    public function cargarCliente($id){
	$this->_colClientes[$id] = NEW ClienteExtendido();
	$this->_colClientes[$id]->cargarMe($id);
    }
    public function setColObjCliente($objCliente){
    
	$this->_colClientes[$objCliente->getClienteId()] = $objCliente;
    }
    public function getColObjCliente(){
	return $this->_colClientes;
    }
    public function cargarClientesLazzy($arrIds){
    
	//echo $arrIds;exit;
	$this->_db->addSelect('*');
	$this->_db->addSelect('cliente_loca_id AS localidad_id');
	$this->_db->addSelect('cliente_activo AS cliente_activo');
	$this->_db->addSelect('\'Activo\'  AS cliente_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS cliente_no_activo_desc');
	$this->_db->addSelect('\'Resp Inscp\' AS cliente_resp_insc_desc');
	$this->_db->addSelect('\'Exento\' AS cliente_no_resp_insc_desc');
	$this->_db->addSelect('CASE WHEN cliente_resp_insc THEN \'Resp Insc\' ELSE \'Exento\' END AS cliente_iva_mensaje');
	$this->_db->addSelect('CASE WHEN cliente_activo THEN \'Activo\' ELSE \'Inactivo\' END AS cliente_activo_mensaje');
	$this->_db->addSelect('\'true\'  AS desde_cliente');
	
	
        $this->_db->addFrom('datos_clientes');
        if(is_array($arrIds)){
	    foreach($arrIds as $id){
	    $ids.= "'" . $id['cliente_id'] . "',";
	    
	    }
	    $ids=substr($ids, 0, -1);
	    $this->_db->addWhere("cliente_id IN (" . $ids . ")" );
	}else{
	    $this->_db->addWhere("cliente_id IN (" . $arrIds . ")" );
	}	
	$this->_db->AddOrderBy('cliente_nombre ASC');
	$this->_db->generarSelect();
	
	//echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();

        
        $FrenteCondicionVenta = NEW FrenteCondicionVenta();
        $FrenteCondicionVenta->cargarCondicionLazzy($resultado);
        $arrObjCondicionVenta= $FrenteCondicionVenta->getColObjCondicionVenta();
        
        $FrenteLocalidades = NEW FrenteLocalidades();
        $FrenteLocalidades->cargarLocalidadesLazzy($resultado);
        $arrObjLocalidades= $FrenteLocalidades->getColObjLocalidades();

        $FrenteListasDePrecios = NEW FrenteListasDePrecios();
        $FrenteListasDePrecios->cargarListaLazzy($resultado);
        $arrObjListas= $FrenteListasDePrecios->getColObjLista();

		$FrenteListasDePrecios = NEW FrenteListasDePrecios();
        $FrenteListasDePrecios->cargarListaLazzy($resultado);
        $arrObjListas= $FrenteListasDePrecios->getColObjLista();

		$FrenteIva= NEW FrenteCondicionIva();
        $FrenteIva->cargarCondicionLazzy($resultado);
        $arrObjIva= $FrenteIva->getColObjCondicionIva();
        
        
	foreach($resultado AS $detalle){
	    $objCliente = NEW ClienteExtendido();
	    $objCliente->cargarMe($detalle);
	    
	    $objCliente->setObjCondicionVenta($arrObjCondicionVenta[$detalle[condicion_venta_id]]);
	    $objCliente->setObjLocalidad($arrObjLocalidades[$detalle[localidad_id]]);
	    
	    $objCliente->setObjListaDePrecios($arrObjListas[$detalle['lista_id']]);
	    $this->setColObjCliente($objCliente);
		$objCliente->setObjCondicionIva($arrObjIva[$detalle[condicion_iva_id]]);
	    
		}
    }
    
    
    public function buscarClientes($parametros){
	

	$this->_db->addSelect('cliente_id');

	$this->_db->addFrom('datos_clientes');

	//**ANALIZO EL WHERE**//

	if($parametros['stringBuscar'])	{
	    $this->_db->addWhere('cliente_nombre ILIKE  \'%' . $parametros[stringBuscar] .'%\'');
	    $this->_db->addWhere('cliente_activo=true');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($parametros['pagina'] * $parametros['porPagina'] ) - $parametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $parametros['porPagina']);
	}
	//**where faso = true**/
	if($parametros['clientes'] !=null && $parametros['desdeAlta']==null){
	    $this->_db->addWhere('cliente_id = \'' . $parametros['clientes'] . '\'');
	}
	if($parametros['categorias'] !=null && $parametros['desdeAlta']==null){
	    $this->_db->addWhere('categoria_id = \'' . $parametros['categorias'] . '\'');
	}
	if($parametros['vendedores'] !=null && $parametros['desdeAlta']==null){
	    $this->_db->addWhere('vendedor_id = \'' . $parametros['vendedores'] . '\'');
	}
	$this->_db->addWhere("sucursal_id = {$_SESSION['sucursalId']}");
	$this->_db->addWhere('cliente_activo=true');
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenClientes($parametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($parametros['tipo']=='clientes'){
	    $this->_db->addLimit(300);
	}
	//*FIN LIMIT*//
	$this->_db->generarSelect();
//echo $this->_db->getQry();exit;
	$arr =  $this->_db->ejecutar();
	
	//ahora seteamos el total y la pagina
	$this->setTotal(count($arr));
	$this->setPagina($parametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro){
	    $ultimoRegistro = $this->getTotal();
	}	

	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
	    $arrIds[$i]['cliente_id'] = $arr[$i]['cliente_id'];
	}
	$this->cargarClientesLazzy($arrIds);
    }
    public function setOrdenClientes($parametros){
	
	if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'cliente_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenClientes(){
	
	return $this->_ordenColumnas;
    }

    public function setTotal($total) {
	
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
    
	foreach($this->_colClientes AS $cliente){

	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $cliente->getClienteId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $cliente->getClienteNombre(),
					$cliente->getClienteCuit(),
					$cliente->getObjCondicionIva()->getDescripcion(),
		            $cliente->getClienteTelefono(),
		            $cliente->getClienteDireccion(),
		            $cliente->getObjLocalidad()->getLocalidadNombre(),
		            $cliente->getHorarioEntrega(),
					$cliente->getObjListaDePrecios()->getListaNombre(),
                    $cliente->getObs(),
                    Array
			(
			"value" => $cliente->getClienteActivo(),
			"label" =>$cliente->getClienteActivoMensaje(),
			"select" => 
			    Array 
			    (
				Array(
				"value" =>true,
				"label" =>$cliente->getClienteActivoDesc()

				),
				Array

				(
				"value" =>false,
				"label" =>$cliente->getClienteNoActivoDesc()

				)		
			    )
			),
                ),
            );
	}
	//var_dump($arr);
	return $arr;
    }
    public function armarDetalles($id){
    
	$this->cargarClientesLazzy($id);
	
	foreach($this->_colClientes AS $objCliente){
	
		$propiedadesCliente =   array(
			array(
			    "display"   =>  'Calle',
			    "name"      =>  'cliente_calle',
			    "editable"  =>  true,
			    "class"	=>'',
			    "value"     =>  $objCliente->getClienteCalle(),

			),
			array(
			    "display"   =>  'Altura',
			    "name"      =>  'cliente_altura',
			    "editable"  =>  true,
			    "class"	=>'',
			    "value"     =>  $objCliente->getClienteAltura(),

			),
			array(
			    "display"   =>  'Piso',
			    "name"      =>  'cliente_piso',
			    "editable"  =>  true,
			    "class"	=>'',
			    "value"     =>  $objCliente->getClientePiso(),

			),
			array(
			    "display"   =>  'Departamento',
			    "name"      =>  'cliente_departamento',
			    "editable"  =>  true,
			    "class"	=>'',
			    "value"     =>  $objCliente->getClienteDepto(),

			),
			array(
			    "display"   =>  'Codigo Postal',
			    "name"      =>  'cliente_codigo_postal',
			    "editable"  =>  true,
			    "class"	=>'',
			    "value"     =>  $objCliente->getClienteCodigoPostal(),

			),
			array(
			    "display"   =>  'Localidad',
			    "name"      =>  'localidades',
			    "editable"  =>  true,
			    "class"	=>'autocomplete',
			    "value"     => $objCliente->getObjLocalidad()->getLocalidadNombre(),

			),
			array(
			    "display"   =>  'Horario Entrega',
			    "name"      =>  'horario_entrega',
			    "editable"  =>  true,
			    "class"	=>'',
			    "value"     =>  $objCliente->getHorarioEntrega(),

			),
		    );
	}
	/*
	foreach($objCliente->getColObjDetallePedido() AS $detalle){

    
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
	*/
	$array_devolver =   array(
	    "editable_cabecera" => true,
	    "propiedades"       =>  $propiedadesCliente,
	    "listadoDetalles"   =>  $detallesPedido
	);
	    return $array_devolver;
    }
	    	
    public function getDetallesAutocompletar(){
	
	foreach($this->_colClientes AS $cliente){
	 $arr[]   =   array(
            'label' =>  $cliente->getClienteNombre(),
            'value' =>  $cliente->getClienteId(),
            'idListaPrecio'=>$cliente->getListaId()
            );
	}		
	return $arr;
    }
    public function buscarDireccion($id){
    
	$cliente = NEW ClienteExtendido;
	$cliente->cargarme($id);
	//var_dump($cliente);
	$arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'',
		      "calle" =>$cliente->getClienteCalle(),
		      "altura"=>$cliente->getClienteAltura(),
		      "piso"=>$cliente->getClientePiso(),
		      "depto"=>$cliente->getClienteDepto(),
		      "telfono"=>$cliente->getClienteTelefono(),
		      "codigoPostal"=>$cliente->getClienteCodigoPostal(),
		      "localidad"=>array(
				    'id' =>  $cliente->getObjLocalidad()->getLocalidadId(),
				    'nombre' =>  $cliente->getObjLocalidad()->getLocalidadNombre()
				    )
			);
		echo json_encode($arrDevolver);exit;
    
    }
    public function salvarMe($parametros){
	
	$clienteExtendido = NEW clienteExtendido();
	$clienteExtendido->salvarMe($parametros);
    }
    public function actualizarMe($arrParametros){
	
	$clienteExtendido = NEW ClienteExtendido();
	$clienteExtendido->actualizarMe($arrParametros);
    }

}
?>
