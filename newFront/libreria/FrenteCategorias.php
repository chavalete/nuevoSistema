<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/CategoriaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteCategorias
{
    private $_colCategorias = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    public function __construct(){

	$this->_objFuncionesComunes = new FuncionesComunes();
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
    }
    public function cargarCategoria($id){

	$this->_colCategorias[$id] = NEW CategoriaExtendido();
	$this->_colCategorias[$id]->cargarMe($id);
    }
    public function setColObjCategorias($objCategoria){
    
	$this->_colCategorias[$objCategoria->getCategoriaId()] = $objCategoria;
    }
    public function getColObjCategorias(){
	return $this->_colCategorias;
    }
    public function cargarCategoriasLazzy($arrIds){
    
	foreach($arrIds as $id){
	    $ids.= "'" . $id['categoria_id'] . "',";
	}
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
	$this->_db->addSelect('categoria_activa AS categoria_activo');
	$this->_db->addSelect('\'Activo\'  AS categoria_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS categoria_no_activo_desc');
	$this->_db->addSelect('CASE WHEN categoria_activa THEN \'Activo\' ELSE \'Inactivo\' END AS categoria_activo_mensaje');
        $this->_db->addFrom('datos_categorias');
        $this->_db->addWhere("categoria_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
	
        $resultado = $this->_db->ejecutar();
        
	foreach($resultado AS $detalle){
	    $objCategoria= NEW CategoriaExtendido();
	    $objCategoria->cargarMe($detalle);
	    $this->setColObjCategorias($objCategoria);
	    
	}
    }
    
    public function buscarCategorias($arrParametros){

	$this->_db->addSelect('categoria_id');
	
	$this->_db->addFrom('datos_categorias');

	//**ANALIZO EL WHERE**//

	if($arrParametros['stringBuscar']){
	    $this->_db->addWhere('categoria_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
	}
	if($arrParametros['categorias'] !=null && $arrParametros['desdeAlta']==null){
	    $this->_db->addWhere('categoria_id = \'' . $arrParametros['categorias'] . '\'');
	}

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenCategorias($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='categorias'){

	    $this->_db->addLimit(100);
	}
	//*FIN LIMIT*//
	$this->_db->generarSelect();

	$arr =  $this->_db->ejecutar();
	
	//ahora seteamos el total y la pagina
	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro){
	    
	    $ultimoRegistro = $this->getTotal();
	}	

	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
	    $arrIds[$i]['categoria_id'] = $arr[$i]['categoria_id'];
	}
	$this->cargarCategoriasLazzy($arrIds);
    }
    public function setOrdenCategorias($arrParametros){

	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'categoria_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenCategorias(){

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

	foreach($this->_colCategorias AS $categoria){

	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $categoria->getCategoriaId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $categoria->getCategoriaNombre(),
		    $categoria->getObs(),
                    Array
			(
			"value" => $categoria->getCategoriaActiva(),
			"label" =>$categoria->getCategoriaActivoMensaje(),
			"select" => 
			    Array 
			    (
				Array(
				"value" =>true,
				"label" =>$categoria->getCategoriaActivoDesc()

				),
				Array

				(
				"value" =>false,
				"label" =>$categoria->getCategoriaNoActivoDesc()

				)		
			    )
			)
                ),
            );
	}		
	return $arr;
    }
    public function getDetallesAutocompletar(){

	foreach($this->_colCategorias AS $categoria){
	 $arr[]   =   array(
            'label' =>  $categoria->getCategoriaNombre(),
            'value' => $categoria->getCategoriaId()
            );
	}		
	return $arr;
    }
    public function salvarMe($arrParametros){
	$categoriaExtendido = NEW CategoriaExtendido();
	$categoriaExtendido->salvarMe($arrParametros);
    }
    public function actualizarMe($arrParametros){
	$categoriaExtendido = NEW CategoriaExtendido();
	$categoriaExtendido->actualizarMe($arrParametros);
    }
}
?>