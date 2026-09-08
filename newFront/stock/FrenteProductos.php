<?php
$_SERVER['DOCUMENT_ROOT']='/var/www/html/';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/ProductoExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

/*
CLASE DE PRODUCTOS
*/

class FrenteProductos
{
    private $_colProductos = Array();
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;

    public function __construct() {
	$this->_objFuncionesComunes = new FuncionesComunes();
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
    }
    public function cargarProducto($id){

	$this->_colProductos[$id] = new ProductoExtendido();
	$this->_colProductos[$id]->cargarMe($id);	
    }
    
    public function setColProductos($objProducto){
	$this->_colProductos[$objProducto->getProductoId()] = $objProducto;
    }
    
    public function getColProductos(){
	return $this->_colProductos;
    }
    
    public function cargarProductosLazzy($arrIds){
    
	foreach($arrIds as $id){
	    if($id['producto_id']!=NULL){
		$ids.= "'" . $id['producto_id'] . "',";
	    }else{
		$ids.= "'" . $id['producto_destino_id'] . "',";
	    }
	}
	$ids=substr($ids, 0, -1);
	
        
        $this->_db->addSelect('dp.producto_id');
        $this->_db->addSelect('dp.producto_nombre');
        $this->_db->addSelect('dp.producto_presentacion');
        $this->_db->addSelect('dp.marca_id');
        $this->_db->addSelect('dp.prove_id');
        $this->_db->addSelect('dp.codigo_referencia');
        $this->_db->addSelect('dp.producto_fecha_alta');
        $this->_db->addSelect('dp.producto_usuario_alta');
        $this->_db->addSelect('dp.pm');
        $this->_db->addSelect('dp.producto_gtin');
        $this->_db->addSelect('dp.unidades');
        $this->_db->addSelect('dp.forma');
        $this->_db->addSelect('dp.relacion_monodroga_id');
        $this->_db->addSelect('dp.producto_activo AS producto_activo');
        $this->_db->addSelect('\'Activo\'  AS producto_activo_desc');
        $this->_db->addSelect('\'Inactivo\'  AS producto_no_activo_desc');
        $this->_db->addSelect('CASE WHEN dp.producto_activo THEN \'Activo\' ELSE \'Inactivo\' END AS producto_activo_mensaje');
        $this->_db->addSelect('producto_anexo AS producto_mensajeria');
        $this->_db->addSelect('\'Si\'  AS producto_mensajeria_desc');
        $this->_db->addSelect('\'No\'  AS producto_no_mensajeria_desc');
        $this->_db->addSelect('CASE WHEN producto_anexo THEN \'Si\' ELSE \'No\' END AS producto_mensajeria_mensaje');
        $this->_db->addSelect('unidades');
        $this->_db->addSelect('forma');
        $this->_db->addSelect('ls.producto_pventa');
        $this->_db->addSelect('ls.precio_promocion');
        $this->_db->addSelect('producto_pcosto');
        $this->_db->addSelect('producto_pventa_dolar');
        $this->_db->addSelect('producto_pcosto_dolar');
        $this->_db->addSelect('stock_minimo');
        $this->_db->addSelect('stock_alerta');
	$this->_db->addSelect('producto_comision');
	$this->_db->addSelect('unidad_bulto');
        $this->_db->addSelect('dp.excluir_reporte_stock AS excluir_reporte_stock');
        $this->_db->addSelect('\'Si\'  AS excluir_reporte_stock_si_desc');
        $this->_db->addSelect('\'No\'  AS excluir_reporte_stock_no_desc');
        $this->_db->addSelect('CASE WHEN dp.excluir_reporte_stock THEN \'Si\' ELSE \'No\' END AS excluir_reporte_stock_mensaje');
        $this->_db->addSelect('CASE WHEN actualizar_costo THEN \'Si\' ELSE \'No\' END AS actualizar_precio');
        
        
        
        
        $this->_db->addFrom('datos_productos dp');
        $this->_db->addFrom('INNER JOIN lista_precios_10 ls USING(producto_id)');
       
        $this->_db->addWhere("producto_id IN (" . $ids . ")" );
        $this->_db->AddOrderBy('producto_nombre');
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        $FrenteMarcas = NEW FrenteMarcas();
        $FrenteMarcas->cargarMarcasLazzy($resultado);
        $arrObjMarcas = $FrenteMarcas->getColObjMarcas();
        
        
        
        
	foreach($resultado AS $detalle){
	    
	    $objProducto = NEW ProductoExtendido();
	    $objProducto->cargarme($detalle);
	    $objProducto->setObjMarca($arrObjMarcas[$detalle[marca_id]]);
	    $this->setColProductos($objProducto);
	}
    }

    public function buscarProductos($arrParametros){

	$this->_db->addSelect('dp.producto_id');
	
	$this->_db->addFrom('datos_productos dp');

	//**ANALIZO EL WHERE**//

	if($arrParametros['stringBuscar']){
	    if(is_numeric($arrParametros[stringBuscar])){
            if(strlen($arrParametros[stringBuscar])==13){
                $this->_db->addWhere('producto_gtin =  \'' . $arrParametros[stringBuscar] .'\'');
            }
            if(strlen($arrParametros[stringBuscar])==7){
                $this->_db->addWhere('producto_gtin =  \'' . $arrParametros[stringBuscar] .'\'');
            }
            if(strlen($arrParametros[stringBuscar])==8){
                $this->_db->addWhere('producto_gtin =  \'' . $arrParametros[stringBuscar] .'\'');
            }
            if(strlen($arrParametros[stringBuscar])==12){
                $this->_db->addWhere('producto_gtin =  \'' . $arrParametros[stringBuscar] .'\'');
            }
            if(strlen($arrParametros[stringBuscar])==14){
                //$this->_db->addWhere('producto_gtin =  \'' . $arrParametros[stringBuscar] .'\'');
                $this->_db->addWhere("(producto_gtin = '" . $arrParametros['stringBuscar'] . "' OR gtin = '" . $arrParametros['stringBuscar'] . "')");
            }
            if(strlen($arrParametros[stringBuscar])==15){
                //$this->_db->addWhere('producto_gtin =  \'' . $arrParametros[stringBuscar] .'\'');
                $this->_db->addWhere("(producto_gtin = '" . $arrParametros['stringBuscar'] . "' OR gtin = '" . $arrParametros['stringBuscar'] . "')");
            }
            if(strlen($arrParametros[stringBuscar])<7){
                $this->_db->addWhere('codigo_referencia  =  \'' . $arrParametros[stringBuscar] .'\'');
            }
            $this->_db->addWhere('producto_activo=true');
            $this->_db->addFrom('LEFT JOIN presentaciones_productos USING(producto_id)');
        }else{
            $this->_db->addWhere('producto_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
            if($arrParametros['tipoBuscar']!='productosAbm'){
                $this->_db->addWhere('producto_activo=true');
            }
        }
	    $primerRegistro = 0;
	    $ultimoRegistro = 18;
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
	}

	//**where faso = true**/
	if($arrParametros['proveedores'] !=null){
	    $this->_db->addWhere('prove_id = \'' . $arrParametros['proveedores'] . '\'');
	}
	if($arrParametros['categoria'] !=null){
	    $this->_db->addWhere('categoria_id = \'' . $arrParametros['categoria'] . '\'');
	}
	if($arrParametros['productosAbm'] !=null && $arrParametros['desdeAlta']!='false'){
	    $this->_db->addWhere('producto_id = \'' . $arrParametros['productosAbm'] . '\'');
	}
	if($arrParametros['familias'] !=null && $arrParametros['desdeAlta']!='false'){
	    $this->_db->addFrom('INNER JOIN relaciones_familias_subfamilias ON dp.producto_id = relaciones_familias_subfamilias.producto_id');
        $this->_db->addWhere('relaciones_familias_subfamilias.familia_id = \'' . $arrParametros['familias'] . '\' ');
        $this->_db->addWhere('relacion_activa = true');
	}
	if($arrParametros['subfamilias'] !=null ){
        $this->_db->addFrom('INNER JOIN relaciones_familias_subfamilias ON dp.producto_id = relaciones_familias_subfamilias.producto_id');
        $this->_db->addWhere('subfamilia_id = \'' . $arrParametros['subfamilias'] . '\' ');
        $this->_db->addWhere('relacion_activa = true');
    }
	if($arrParametros['codigoReferencia'] !=null){
	    $this->_db->addWhere('codigo_referencia = \'' . $arrParametros[codigoReferencia] . '\'');
	}
        if($arrParametros['marcas'] !=0){
	    $this->_db->addWhere(' marca_id = \'' . $arrParametros['marcas'] . '\'');
	}
	if($arrParametros['nombre'] !=null){
	     $this->_db->addWhere('producto_nombre ILIKE  \'%' . $arrParametros[nombre] .'%\'');
	}
        if($arrParametros['gtin'] !=0){
	    $this->_db->addWhere('dp.producto_gtin = \'' . $arrParametros['gtin'] . '\'');
	}
    //$this->_db->addWhere('producto_activo=true');
    if($arrParametros['tipoBuscar']=='productosCombo'){
        $this->_db->addWhere('es_combo=true');
    }
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenProductos($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/

	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='productos'){
	    //$this->_db->addLimit(300);
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
	    $arrIds[$i]['producto_id'] = $arr[$i]['producto_id'];
	}
        //var_dump($arrIds);
	$this->cargarProductosLazzy($arrIds);
    }
    
    
    
    public function setOrdenProductos($parametros) {

	if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'producto_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenProductos(){
	
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

	//echo "llego";exit;
	foreach($this->_colProductos AS $producto){
	
        $colorEstado = "red";
        if($_SESSION['usuarioId']==1 || $_SESSION['usuarioId']==3){
            $editable=true;
        }else{
            $editable=false;
        }

	
	    $arr []   =    array
	    (
	    //id si o si un solo string (sin espacios en blanco)
	    "id"            =>  $producto->getProductoId(),
	    //define las herramientas
	    "herramientas"   =>  array(
			    "cancelable"    =>  false,
			    "editable"      =>  $editable,
			    "detalles"      =>  false,
			    "bloqueado"      =>  true,
			    ),
	    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
	    "cell"          => 	array(
		    $producto->getProductoNombre(),
		    $producto->getProductoPresentacion(),
		    $producto->getUnidades(),
		    $producto->getObjMarca()->getMarcaNombre(),
		    $producto->getProductoGtin(),
		    $producto->getUnidadBulto(),
            $producto->getProductoPcosto(),
            $this->_objFuncionesComunes->formatoMoneda($producto->getProductoPrecio()),
            $this->_objFuncionesComunes->formatoMoneda($producto->getProductoPrecio()*1.07),
            $this->_objFuncionesComunes->formatoMoneda($producto->getProductoPrecio()*1.03),
            $this->_objFuncionesComunes->formatoMoneda($producto->getPrecioXCaja()),
            $this->_objFuncionesComunes->formatoMoneda($producto->getPrecioXCaja()*1.07),
            $this->_objFuncionesComunes->formatoMoneda($producto->getPrecioXCaja()*1.03),
		    //$producto->getProductoPrecio(),
		    $producto->getStockAlerta(),
		    
		    Array
			(
			"value" =>$producto->getProductoActivo(),
			"label" =>$producto->getProductoActivoMensaje(),
			"select" => 
			    Array 
			    (
				Array(
				"value" =>true,
				"label" =>$producto->getProductoActivoDesc()

				),
				Array

				(
				"value" =>false,
				"label" =>$producto->getProductoNoActivoDesc()

				)		
			    )
			)

		),
            );
	}		
	return $arr;
    }
    
    public function getDetallesAutocompletar(){

	foreach($this->_colProductos AS $producto){
	    $arr[]   =   
		array(
		    'label' =>  $producto->getProductoNombre() . " " . $producto->getProductoPresentacion() ,
		    'value' => $producto->getProductoId()
		);
	}		
	return $arr;
    }
    
    public function getDetallesAutocompletarConTotal(){

	foreach($this->_colProductos AS $producto){
	
	    $this->_db = NEW FrenteAlmacenamiento('dmelmac');

	    $this->_db->addSelect('cantidad_total');
	    $this->_db->addFrom('vw_stock_completo_agrupado_combos');
	    $this->_db->addWhere('producto_id = \'' . $producto->getProductoId() . '\'');
        $this->_db->addWhere('sucursal_id = \'' . $_SESSION[sucursalId] . '\'');
	
	    $this->_db->generarSelect();
	    
	    //echo $this->_db->getQry();
	    $resultadoCant = $this->_db->ejecutar();
	    
	   
	    $this->_db->addSelect('subfamilia_id');
	    $this->_db->addFrom('relaciones_familias_subfamilias INNER JOIN datos_subfamilias USING(subfamilia_id)');
	    $this->_db->addFrom('INNER JOIN datos_promociones  ON datos_subfamilias.subfamilia_id = promocion_subfamilia_id');
        $this->_db->addWhere('fecha_vencimiento>=now()::date');
        $this->_db->addWhere('now()::date>=fecha_inicio');
        $this->_db->addWhere('promocion_activa');
	    $this->_db->addWhere('relaciones_familias_subfamilias.producto_id = \'' . $producto->getProductoId() . '\'');
	    $this->_db->addWhere('relacion_activa');
        $this->_db->addWhere('familia_promocion');
	
	    $this->_db->generarSelect();
	    
	    //echo $this->_db->getQry();
	    $resultadoFamilia = $this->_db->ejecutar();
	    
	
	    $arr[]   =   
		array(
		    'label' =>  $producto->getProductoNombre() . " " . $producto->getProductoPresentacion() . "- (" . $resultadoCant[0][cantidad_total] . ") "   . $producto->getUnidadBulto(),
		    'value' => $producto->getProductoId(),
		    'idFamilia'=>$resultadoFamilia[0][subfamilia_id]
		);
	}		
	return $arr;
    }
    public function salvarMe($arrParametros) {
        $productoExtendido = NEW productoExtendido();
        $productoExtendido->salvarMe($arrParametros);
    }

    public function actualizarMe($arrParametros) {
        $productoExtendido = NEW productoExtendido();
        $productoExtendido->actualizarMe($arrParametros);
    }
    public function buscarCosto($id){

        if(!is_numeric($id)){
            $id = $id[0]['ID'];
        }

        $qry="SELECT ls.producto_pventa, producto_pcosto, cantidad_promocion, precio_promocion FROM datos_productos INNER JOIN lista_precios_10 ls USING(producto_id) WHERE producto_id = $id;";
        $this->_db->setQry($qry);
        $resultado = $this->_db->ejecutar();
        
        
        $arrDevolver = array(
                "soyError" => false,
                "nivel" => 1,
                "mensaje" =>'',
                "productoPcosto" =>$resultado[0][producto_pcosto],
                "unidadesPromo" =>$resultado[0][cantidad_promocion],
                "precio" =>$resultado[0][producto_pventa],
                "precioPromo" =>$resultado[0][precio_promocion],
                );
		echo json_encode($arrDevolver);exit;
    
    }
    public function buscarCostoFamilia($id){
    
        $qry="SELECT ls.producto_pventa, producto_pcosto, cantidad_promocion, precio_promocion FROM datos_productos INNER JOIN relaciones_familias_subfamilias USING (producto_id) INNER JOIN lista_precios_10 ls USING(producto_id) WHERE relacion_activa=true AND producto_activo = true AND subfamilia_id = $id  ORDER BY producto_pcosto DESC LIMIT 1;";
        //echo $qry;exit;
        $this->_db->setQry($qry);
        
        $resultado = $this->_db->ejecutar();
        //var_dump($cliente);
        $arrDevolver = array(
                "soyError" => false,
                "nivel" => 1,
                "mensaje" =>'',
                "productoPcosto" =>$resultado[0][producto_pcosto],
                "unidadesPromo" =>$resultado[0][cantidad_promocion],
                "precio" =>$resultado[0][producto_pventa],
                "precioPromo" =>$resultado[0][precio_promocion],
                );
		echo json_encode($arrDevolver);exit;
    
    }
    public function buscarReferencia($id){
    
        $qry="SELECT max(codigo_referencia) AS codigo FROM datos_productos;";
        $this->_db->setQry($qry);
        $resultado = $this->_db->ejecutar();
        $codigoReferencia = $resultado[0][codigo] + 1;
        $arrDevolver = array(
                "soyError" => false,
                "nivel" => 1,
                "mensaje" =>'',
                "codigoReferencia" =>$codigoReferencia,
                );
		echo json_encode($arrDevolver);exit;
    
    }
    public function actualizarComision($arrParametros){
    
    $arrProductos = explode ("||",$arrParametros[listas]);
    array_pop($arrProductos);
    $cantListas=0;
    $qry=null;
    foreach($arrProductos AS $productos){
        list( $marcaId, $importe) = explode("#",$productos);
        
            $qry.="UPDATE datos_productos SET producto_comision = ".$importe." WHERE marca_id = $marcaId;";
            $cantListas++;
        }
//echo $cantListas;exit;
        $this->_db->setQry($qry);
                if($cantListas>1){
                    $this->_db->ejecutarTransaccion();
                }else{
                    $this->_db->ejecutar();
                }
                if(count($this->_db->getArrError()) > 0){
                    $error =true;
                }else{
                    $error =false;
                }
                
            
            
            if($error){
                $arrDevolver = array(
                        "soyError" => true,
                        "nivel" => 1,
                        "mensaje" => "Error al actualizar!!!!"
                        );
                    echo json_encode($arrDevolver);exit;
        
            }else{
                    $arrDevolver = array(
                        "soyError" => false,
                        "nivel" => 1,
                        "mensaje" => "Lista la magia!!!!"
                        );
                    echo json_encode($arrDevolver);exit;
            }
        }
    public function traerPrecioProducto($arrParametros){
        $cantidad=0;
        foreach($arrParametros AS $data){
            if($data[ID_F]!=null){
                $cantidad = $cantidad + $data[CANTIDAD];
                $subfamiliaId=$data[ID_F];
                $productoId = $data[ID];
            }else{
                $productoId = $data[ID];
                $cantidad = $data[CANTIDAD];
            }
            $idLista = $data[idListaPrecio];
        }
        
        //echo $idLista;exit;
        if($subfamiliaId >0){
            $qry="SELECT CASE WHEN $idLista = 10 THEN promocion_precio ELSE promocion_precio * 1.07 END AS promocion_precio, sucursal_id FROM datos_promociones WHERE $cantidad >=promocion_cantidad AND promocion_activa AND fecha_vencimiento>=now()::date AND now()::date>=fecha_inicio AND promocion_cancelada=false AND promocion_subfamilia_id =$subfamiliaId ORDER BY promocion_cantidad DESC LIMIT 1;";
            //echo $qry;exit;
            $this->_db->setQry($qry);
            $resultado = $this->_db->ejecutar();
            if (count($resultado) > 0) {
                $sucursalId = $resultado[0]['sucursal_id'] ?? null;

                // Si coincide con la sesión O si es NULL (promoción global/general)
                if ($sucursalId == $_SESSION['sucursalId'] || $sucursalId === null) {
                    $arrDevolver = array (
                        "precio" => $resultado[0]['promocion_precio'],
                    );

                    echo json_encode($arrDevolver);
                    exit;
                }
            }
        }
        //CHEQUEAMOS PROMO POR PRODUCTO
        $qry="SELECT CASE WHEN $idLista = 10 THEN promocion_precio ELSE promocion_precio * 1.07 END AS promocion_precio,sucursal_id FROM datos_promociones WHERE $cantidad >=promocion_cantidad AND promocion_activa AND fecha_vencimiento>=now()::date AND now()::date>=fecha_inicio AND promocion_cancelada=false AND producto_id =$productoId ORDER BY promocion_cantidad DESC LIMIT 1;";
        $this->_db->setQry($qry);
        //echo $qry;exit;
        $resultado = $this->_db->ejecutar();
        if (count($resultado) > 0) {
            $sucursalId = $resultado[0]['sucursal_id'] ?? null;

            // Si coincide con la sesión O si es NULL (promoción global/general)
            if ($sucursalId == $_SESSION['sucursalId'] || $sucursalId === null) {
                $arrDevolver = array (
                    "precio" => $resultado[0]['promocion_precio'],
                );

                echo json_encode($arrDevolver);
                exit;
            }
        }
        
        $qry="SELECT producto_pventa, cantidad_promocion, precio_promocion FROM lista_precios_$idLista WHERE producto_id = $productoId;";
        $this->_db->setQry($qry);
        //echo $qry;exit;
        $resultado = $this->_db->ejecutar();
        
        if($resultado[0]['cantidad_promocion']>0 && $resultado[0]['precio_promocion']>0 && $cantidad>=$resultado[0]['cantidad_promocion']){
            $precio= $resultado[0]['precio_promocion'];
        }else{
            $precio= $resultado[0]['producto_pventa'];
        }
        
        if(count($resultado)>0){
            $arrDevolver = array(
                    "precio" => $precio,
                    );
                echo json_encode($arrDevolver);exit;
        }
    }
    public function buscarPrecioVenta($arrParametros){
        $this->_db->addSelect('ls.precio_promocion');
        $this->_db->addFrom('lista_precios_10 ls');
        if($arrParametros['subfamiliaId']!=null){
            $this->_db->addFrom('INNER JOIN relaciones_familias_subfamilias rfs  ON  ls.producto_id = rfs.producto_id');
            $this->_db->addWhere("rfs.subfamilia_id = {$arrParametros['subfamiliaId']}");
        }else{
            $this->_db->addWhere("ls.producto_id = {$arrParametros['productoId']}");
        }
        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $resultado = $this->_db->ejecutar();

        if(count($resultado)>0){
            $arrDevolver = array(
                    "precio" => $resultado[0]['precio_promocion'],
                    );
                echo json_encode($arrDevolver);exit;
        }

    }
}
?>
