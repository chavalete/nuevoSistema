<?php
//ALMACENAMIENTO
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
//STOCK
require_once 'PresentacionesProductosExtendido.php';

/**
 * Description of FrentePresentaciones
 *
 * @author Guillo
 */
class FrentePresentacionesProductos {

    
    private $_db;
    private $_colObjPresentacionesProductos;
    private $_colObjPresentacion;


    public function __construct() {
	$this->_db = NEW FrenteAlmacenamiento();
    }
    
    public function setColObjPresentacion($objPresentacion){
	$this->_colObjPresentacion[$objPresentacion->getPresentacionId()] = $objPresentacion;
    }
    public function getColObjPresentacion(){
	return $this->_colObjPresentacion;
    }
    
    public function setColObjPresentacionesProductos($obj){
	$this->_colObjPresentacionesProductos[$obj->getRelacionId()]  = $obj;
    }
    
    public function getColObjPresentacionesProductos(){
	return $this->_colObjPresentacionesProductos;
    }
    
    public function cargarPresentacionesProductos($relacionId){
	$this->_colObjPresentacionesProductos[$relacionId] = new PresentacionesProductosExtendido();
        $this->_colObjPresentacionesProductos[$relacionId]->cargarMe($relacionId);
    }
    
    public function cargarPresentaciones($presentacionId){
	$this->_colObjPresentacion[$presentacionId]= new PresentacionExtendido();
        $this->_colObjPresentacion[$presentacionId]->cargarMe($presentacionId);
    }
    
    public function cargarPresentacionProductosLazzy($arrIds){
    
		foreach($arrIds as $id){
		    if($id['relacion_id']!=NULL){
			$ids.= "'" . $id['relacion_id'] . "',";
		    }
		}
		$ids=substr($ids, 0, -1);
		$this->_db->addSelect('*');
		$this->_db->addSelect('\'Si\'  AS producto_presentacion_unidad_venta_desc');
        $this->_db->addSelect('\'No\'  AS producto_presentacion_no_unidad_venta_desc');
        $this->_db->addSelect('CASE WHEN unidad_venta THEN \'Si\' ELSE \'No\' END AS producto_presentacion_unidad_venta_mensaje');
        $this->_db->addSelect('\'Si\'  AS producto_presentacion_unidad_distribucion_desc');
        $this->_db->addSelect('\'No\'  AS producto_presentacion_no_unidad_distribucion_desc');
        $this->_db->addSelect('CASE WHEN unidad_distribucion THEN \'Si\' ELSE \'No\' END AS producto_presentacion_unidad_distribucion_mensaje');
        $this->_db->addSelect('\'Si\'  AS producto_presentacion_activa_desc');
        $this->_db->addSelect('\'No\'  AS producto_presentacion_no_activa_desc');
        $this->_db->addSelect('CASE WHEN relacion_activa THEN \'Si\' ELSE \'No\' END AS producto_presentacion_activa_mensaje');
        $this->_db->addFrom('presentaciones_productos');
        $this->_db->addWhere("relacion_id IN (" . $ids . ")" );
		$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        $FrenteProductos = NEW FrenteProductos();
        $FrenteProductos->cargarProductosLazzy($resultado);
        $arrObjProductos = $FrenteProductos->getColProductos();
        
        $FrentePresentaciones = NEW FrentePresentaciones();
        $FrentePresentaciones->cargarPresentacionesLazzy($resultado);
        $arrObjPresentaciones = $FrentePresentaciones->getColPresentaciones();
        
	foreach($resultado AS $detalle){
	
	    $objPresentacionProducto= NEW PresentacionesProductosExtendido();
	    $objPresentacionProducto->cargarMe($detalle['relacion_id']);
	    $objPresentacionProducto->setColObjProductos($arrObjProductos[$detalle[producto_id]]);
	    $objPresentacionProducto->setColObjPresentaciones($arrObjPresentaciones[$detalle[presentacion_id]]);
	    $this->setColObjPresentacionesProductos($objPresentacionProducto);
	}
        //var_dump($this->getColObjPresentacionesProductos());exit;
        
    }
    
    public function cargarPresentacionLazzy($arrIds){
    
	foreach($arrIds as $id){
	    if($id['presentacion_id']!=NULL){
		$ids.= "'" . $id['presentacion_id'] . "',";
	    }
	}
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
        $this->_db->addFrom('datos_presentaciones');
        $this->_db->addWhere("presentacion_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
	foreach($resultado AS $detalle){
	    $objPresentacion= NEW PresentacionExtendido();
	    $objPresentacion->cargarMe($detalle['relacion_id']);
	    $this->setColObjPresentacion($objPresentacion);
	}
        //var_dump($this->getColObjPresentacionesProductos());
        
    }
    
    public function buscarPresentacionesProductos($arrParametros){

	$arrParametros['stringBuscar'] = strtoupper($arrParametros['stringBuscar']);
	if(substr($arrParametros['stringBuscar'], 0,3) == '+E0'  ||  substr($arrParametros['stringBuscar'], 0,3) == ']E0' ){
		$arrParametros['stringBuscar'] = substr($arrParametros['stringBuscar'], 3);
	}
    
	$this->_db->addSelect('*,presentaciones_productos.unidades');
	
	$this->_db->addFrom('presentaciones_productos');
	$this->_db->addFrom('LEFT JOIN datos_productos USING(producto_id)');

	//**ANALIZO EL WHERE**//

	if($arrParametros['stringBuscar']){
	    if(strlen($arrParametros['stringBuscar'])==13){
                $arrParametros['stringBuscar'] = "0" . $arrParametros['stringBuscar'];
            }
	    $this->_db->addWhere('gtin   = \'' . $arrParametros['stringBuscar'] .'\'');
            $primerRegistro = 0;
            $ultimoRegistro = 18;
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
	}

	//**where faso = true*
	if($arrParametros['productos'] !=null){
	    $this->_db->addWhere('producto_id = \'' . $arrParametros['productos'] . '\'');
	}
	if($arrParametros['presentaciones'] !=null){
	    $this->_db->addWhere('presentacion_id = \'' . $arrParametros['presentaciones'] . '\'');
	}
	if($arrParametros['gtin'] !=null){
	    $this->_db->addWhere('gtin = \'' . $arrParametros['gtin'] . '\'');
	}
        if($arrParametros['unidades'] !=null){
	    $this->_db->addWhere(' unidades = \'' . $arrParametros['unidades'] . '\'');
	}
	

	//**fin where 

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenPresentacionesProductos($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/

	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='relaciones'){
	    $this->_db->addLimit(100);
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
	    $array['relacion_id'] = $arr[$i]['relacion_id'];
		$array['producto'] = $arr[$i]['producto_nombre'] . $arr[$i]['producto_presentacion'];
        $array['gtin'] = $arr[$i]['gtin'];
		$array['nombre_reparto'] = $arr[$i]['nombre_reparto'];
        $array['unidades'] = $arr[$i]['unidades'];
		$array['activa'] = $arr[$i]['activa'];
        $this->_colConsulta[]= $array;
		}
    }
    
    public function getDetalles(){

	
	//echo "llego";exit;
	foreach($this->_colConsulta AS $obj){
	    $arr []   =    array
	    (
	    //id si o si un solo string (sin espacios en blanco)
	    "id"            =>  $obj['relacion_id'],
	    //define las herramientas
	    "herramientas"   =>  array(
			    "cancelable"    =>  false,
			    "editable"      =>  true,
			    "detalles"      =>  false,
			    ),
	    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
	    "cell"          => 	array(


		    $obj['producto'],
			$obj['nombre_reparto'],
			$obj['gtin'],
			$obj['unidades'],
			),
            );
	}		
	return $arr;
    }    
    
    public function setOrdenPresentacionesProductos($parametros) {

	if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'producto_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenPresentacionesProductos(){
	
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
        
    
    public function getDetallesAutocompletar(){

	foreach($this->_colObjPresentacionesProductos AS $presentacion){
            $objFrenteProductos = new FrenteProductos();
            $arrProductos[] = array ('producto_id' => $presentacion->getProductoId());
            $objFrenteProductos->cargarProductosLazzy($arrProductos);
            $colObjProducto = $objFrenteProductos->getColProductos();
            
            $objPresentaciones = new PresentacionesProductosExtendido();
            $objPresentaciones->cargarMe($presentacion->getRelacionId());
            
            //$objPresentaciones->getDesc() . " " .  
            
            $arr[]=   
                array(
                    'label' => $colObjProducto[$presentacion->getProductoId()]->getProductoNombre() . " " . $colObjProducto[$presentacion->getProductoId()]->getProductoPresentacion() . " (" . $presentacion->getUnidades() . " )"  ,
                    'value' => $presentacion->getProductoId() . "#" . $presentacion->getRelacionId()
                );
            
	}		
	return $arr;
    }
    public function salvarme($arrParametros){
	$objPresentacionProducto = NEW PresentacionesProductosExtendido();
	$objPresentacionProducto->salvarme($arrParametros);
    }
    public function actualizarme($arrParametros){
	$objPresentacionProducto = NEW PresentacionesProductosExtendido();
	$objPresentacionProducto->actualizarme($arrParametros);
    }
}
