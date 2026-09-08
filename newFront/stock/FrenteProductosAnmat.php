<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/ProductoExtendidoAnmat.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

/*
CLASE DE PRODUCTOS
*/

class FrenteProductosAnmat
{
    private $_colProductos = Array();
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    private $_db;

    public function __construct() {
	
	$this->_objFuncionesComunes = new FuncionesComunes();
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
    }
    public function cargarProducto($id){

	$this->_colProductos[$id] = new ProductoExtendidoAnmat();
	$this->_colProductos[$id]->cargarMe($id);	
    }
    public function setColProductos($objProductoAnmat){
	$this->_colProductos[$objProductoAnmat->getProductoGtin()] = $objProductoAnmat;
    }
    public function getColProductos(){
	return $this->_colProductos;
    }
    
    public function cargarProductosLazzy($arrIds){
    
	foreach($arrIds as $id){
	    if($id['gtin']==NULL){
		$ids.= "'" . $id['producto_gtin'] . "',";
	    }else{
		$ids.= "'" . $id['gtin'] . "',";
	    }
	}
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
        $this->_db->addFrom('datos_productos_anmat');
        $this->_db->addWhere("producto_gtin IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //	echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
	foreach($resultado AS $detalle){
	
	    $objProductoAnmat = NEW ProductoExtendidoAnmat();
	    $objProductoAnmat->cargarme($detalle);
	    $this->setColProductos($objProductoAnmat);
	    
	}
    }
    public function buscarProductos($arrParametros){



	$db_dM = NEW FrenteAlmacenamiento('dmelmac');

	$db_dM->addSelect('producto_gtin');
	
	$db_dM->addFrom('datos_productos_anmat');

	//**ANALIZO EL WHERE**//

	if($arrParametros['stringBuscar']){
	    $db_dM->addWhere('producto_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 18;
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
	}

	//**where faso = true**/
	if($arrParametros['proveedores'] !=null){
	    $db_dM->addWhere('prove_id = \'' . $arrParametros['proveedores'] . '\'');
	}
	if($arrParametros['categoria'] !=null){
	    $db_dM->addWhere('categoria_id = \'' . $arrParametros['categoria'] . '\'');
	}
	if($arrParametros['productos'] !=null && $arrParametros['desdeAlta']!='false'){
	    $db_dM->addWhere('producto_id = \'' . $arrParametros['productos'] . '\'');
	}
	if($arrParametros['codigoReferencia'] !=null){
	    $db_dM->addWhere('codigo_referencia = \'' . $arrParametros[codigoReferencia] . '\'');
	}

	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenProductos($arrParametros);
	$db_dM->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/

	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='productos'){
	    $db_dM->addLimit(100);
	}
	//*FIN LIMIT*//
	$db_dM->generarSelect();
	//echo $db_dM->getQry();
	$arr =  $db_dM->ejecutar();

	//ahora seteamos el total y la pagina
	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);

	if($this->getTotal() < $ultimoRegistro){
	    $ultimoRegistro = $this->getTotal();
	}	

	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
	    $arrIds[$i]['producto_gtin'] = $arr[$i]['producto_gtin'];
	}
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

	foreach($this->_colProductos AS $producto){
	    $arr []   =    array
	    (
	    //id si o si un solo string (sin espacios en blanco)
	    "id"            =>  $producto->getProductoId(),
	    //define las herramientas
	    "herramientas"   =>  array(
			    "cancelable"    =>  false,
			    "editable"      =>  true,
			    "detalles"      =>  false,
			    ),
	    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
	    "cell"          => 	array(
		    $producto->getProductoNombre(),
		    $producto->getProductoPresentacion(),
		    $producto->getProveNombre(),
		    $producto->getMarcaNombre(),
		    $producto->getCodigoReferencia()

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
		    'value' => $producto->getProductoGtin()
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

}
?>
