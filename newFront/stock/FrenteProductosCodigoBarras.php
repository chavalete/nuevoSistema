<?php
$_SERVER['DOCUMENT_ROOT']='/var/www/html/';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/ProductoExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

/*
CLASE DE PRODUCTOS
*/

class FrenteProductosCodigoBarras
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
                $this->_db->addWhere('producto_gtin =  \'' . $arrParametros[stringBuscar] .'\'');
            }
            if(strlen($arrParametros[stringBuscar])<7){
                $this->_db->addWhere('codigo_referencia  =  \'' . $arrParametros[stringBuscar] .'\'');
            }
            $this->_db->addWhere('producto_activo=true');
        }else{
            $this->_db->addWhere('producto_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
            $this->_db->addWhere('producto_activo=true');
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
	if($arrParametros['productos'] !=null && $arrParametros['desdeAlta']!='false'){
	    $this->_db->addWhere('producto_id = \'' . $arrParametros['productos'] . '\'');
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
    $this->_db->addWhere('producto_activo=true');
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

	
	    $arr []   =    array
	    (
	    //id si o si un solo string (sin espacios en blanco)
	    "id"            =>  $producto->getProductoId(),
	    //define las herramientas
	    "herramientas"   =>  array(
			    "cancelable"    =>  false,
			    "editable"      =>  true,
			    "detalles"      =>  false,
			    "bloqueado"      =>  false,
			    ),
	    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
	    "cell"          => 	array(
		    $producto->getProductoNombre(),
		    $producto->getProductoPresentacion(),
		    $producto->getProductoGtin(),
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
	    $this->_db->addFrom('vw_stock_completo_agrupado');
	    $this->_db->addWhere('producto_id = \'' . $producto->getProductoId() . '\'');
	
	    $this->_db->generarSelect();
	    
	    //echo $this->_db->getQry();
	    $resultadoCant = $this->_db->ejecutar();
	    
	   
	    $this->_db->addSelect('subfamilia_id');
	    $this->_db->addFrom('relaciones_familias_subfamilias INNER JOIN datos_subfamilias USING(subfamilia_id)');
	    $this->_db->addWhere('producto_id = \'' . $producto->getProductoId() . '\'');
	    $this->_db->addWhere('relacion_activa');
	$this->_db->addWhere('familia_promocion');
	
	    $this->_db->generarSelect();
	    
	    //echo $this->_db->getQry();
	    $resultadoFamilia = $this->_db->ejecutar();
	    
	
	    $arr[]   =   
		array(
		    'label' =>  $producto->getCodigoReferencia() . " - " . $producto->getProductoNombre() . " " . $producto->getProductoPresentacion() . "- (" . $resultadoCant[0][cantidad_total] . ") "   ,
		    'value' => $producto->getProductoId(),
		    'idFamilia'=>$resultadoFamilia[0][subfamilia_id]
		);
	}		
	return $arr;
    }
    public function actualizarCodigo($arrParametros) {
        $this->_db->addCamposUpdate('producto_gtin = \'' . $arrParametros['campos']['producto_gtin'] .'\'');
        $this->_db->addFrom('datos_productos');
	$this->_db->addWhere('producto_id = \'' . $arrParametros['id'] .'\'');

	//generar qry
	$this->_db->generarUpdate();
	//echo $this->_db->getQry();exit;
	//asigamos el resulta que es un array a una variable
	$resultado = $this->_db->ejecutar();

	if(count($resultado)> 0){
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
?>
