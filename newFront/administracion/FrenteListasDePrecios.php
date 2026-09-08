<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/administracion/DatoListaPreciosExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/administracion/DetalleListaPreciosExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteListasDePrecios
{
    private $_colLista= Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    private $_listaNombre;
    private $_listaId;
    
    
    public function __construct(){
	$this->_objFuncionesComunes = new FuncionesComunes();
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
    }
    public function setListaNombre($listaNombre){
	$this->_listaNombre = $listaNombre;
    }
    public function getListaNombre(){
	return $this->_listaNombre;
    }
    public function setListaId($listaId){
	$this->_listaId = $listaId;
    }
    public function getListaId(){
	return $this->_listaId;
    }
    public function setColObjLista($objLista){
	$this->_colLista[$objLista->getListaId()] = $objLista;
    }
    public function getColObjLista(){
	return $this->_colLista;
    }
    public function cargarListaLazzy($arrIds){
    	
    	if(is_array($arrIds)){
	    foreach($arrIds as $id){
		if($id['lista_id']!=""){
		    $ids.= "'" . $id['lista_id'] . "',";
		}
	    }
	    $ids=substr($ids, 0, -1);
	    $this->_db->addWhere("lista_id IN (" . $ids . ")" );
	}else{
	    $this->_db->addWhere("lista_id IN (" . $arrIds . ")" );
	}
	
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
        $this->_db->addFrom('datos_listas_precios');
        
        
        if($arrIds[0][desde_cliente]!='true'){
	    $this->_db->addLimit(1);
        };
        //$this->_db->addWhere("lista_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
	//echo $this->_db->getQry();exit;

        $resultado = $this->_db->ejecutar();
        
                
        
	foreach($resultado AS $detalle){
	    $objLista= NEW DatoListaPreciosExtendido();
	    $objLista->cargarMe($detalle);
	    //echo $objLista->getListaId();exit;
	    $this->setColObjLista($objLista);
	    $this->setListaNombre($objLista->getListaNombre());
	    $this->setListaId($objLista->getListaId());
	    
	}
    }
    public function cargarLosDetallesLazzy($arrIds){
	if(is_array($arrIds)){
	    foreach($arrIds as $id){
		$ids.= "'" . $id['producto_id'] . "',";
	    }
	    $ids=substr($ids, 0, -1);
	    $this->_db->addWhere("producto_id IN (" . $ids . ")" );
	}else{
	    $this->_db->addWhere("producto_id IN (" . $arrIds . ")" );
	}
	$this->_db->addSelect('*');
	$this->_db->addFrom('lista_precios_' . $this->getListaId());
        //$this->_db->addWhere("lista_id IN (" . $remitoId . ")" );
        $this->_db->AddOrderBy('producto_id ASC');
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        
        
        $FrenteProductos = NEW FrenteProductos();
        $FrenteProductos->cargarProductosLazzy($resultado);
        $arrObjProductos= $FrenteProductos->getColProductos();
        
        foreach($this->getColObjLista() AS $objLista){    
	    foreach($resultado AS $detalle){
		$objDetalleLista = NEW DetalleListaPreciosExtendido();
		$objDetalleLista->cargarme($detalle);
		$objDetalleLista->setObjProducto($arrObjProductos[$detalle[producto_id]]);
		$this->_colLista[$objLista->getListaId()]->setColObjDetalleLista($objDetalleLista);
	    }
	}
    
    }
    public function buscarListasPorNombre($arrParametros){
    
	$this->_db->addSelect('lista_id');
	$this->_db->addFrom('datos_listas_precios');
	$this->_db->addWhere('lista_nombre ILIKE  \'%' .$arrParametros[stringBuscar].'%\'');
	$this->_db->addWhere('lista_cancelada=false');
	$primerRegistro = 0;
	$ultimoRegistro = 11;
	
	$this->_db->generarSelect();
	//echo $this->_db->getQry();
	
	$arr =  $this->_db->ejecutar();
	//var_dump($arr);exit;
	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
	    $arrIds[$i]['lista_id'] = $arr[$i]['lista_id'];
	}
	$this->cargarListaLazzy($arrIds);
    }
    
    public function buscarListas($arrParametros) {
//var_dump($arrParametros);exit;
	$this->_db->addSelect('producto_id, lista_id');
	if($arrParametros['desdeBusqueda']==false){
	    $this->_db->addFrom('lista_precios_10');
	}else{
	    $this->_db->addFrom('lista_precios_10');
	}
	$this->_db->addFrom('INNER JOIN datos_listas_precios USING(lista_id)');
	$this->_db->addFrom('INNER JOIN datos_productos USING(producto_id)');
	
	//$this->_db->addFrom('INNER JOIN detalle_compras_facturas USING(factura_id)');

	//**ANALIZO EL WHERE**//

	if($arrParametros['stringBuscar']){
	    $this->_db->addWhere('lista_nombre ILIKE  \'%' .$arrParametros[stringBuscar].'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
	}
	//**where faso = true**/
	
	
	if($arrParametros['productos'] !=null && $arrParametros['desdeAlta'] == null){    
	    $this->_db->addWhere('producto_id = \''  . $arrParametros['productos'] . '\'');
	}
	if($arrParametros['nombre'] !=null){
	     $this->_db->addWhere('producto_nombre ILIKE  \'%' . $arrParametros[nombre] .'%\'');
	}
	if($arrParametros['familias'] !=null && $arrParametros['desdeAlta'] == null){
	    $this->_db->addWhere('familia_id = \''  . $arrParametros['familias'] . '\'');
	}
	$this->_db->addWhere('producto_activo=true');
	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenLista($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
		
	//*FIN LIMIT*//
	$this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
	
	$arr =  $this->_db->ejecutar();
	
	
	//var_dump($arr);
	//ahora seteamos el total y la pagina
	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro){
	    
	    $ultimoRegistro = $this->getTotal();
	}	

	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
	    $arrIds[$i]['producto_id'] = $arr[$i]['producto_id'];
	    $arrIds[$i]['lista_id'] = $arr[$i]['lista_id'];
	}
	$this->cargarListaLazzy($arrIds);
	$this->cargarLosDetallesLazzy($arrIds);
    }
    public function setOrdenLista($arrParametros){

	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'producto_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenLista(){

	return $this->_ordenLista;
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

	foreach($this->_colLista AS  $objLista){
        if($_SESSION['usuarioId']>6){
            $editable=false;
        }else{
            $editable=true;
        }
	   foreach ($objLista->getColObjDetalleLista() AS $objDetalleLista){
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $objDetalleLista->getListaId() . "|". $objDetalleLista->getProductoId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  $editable,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $objDetalleLista->getObjProducto()->getProductoNombre() . " " . $objDetalleLista->getObjProducto()->getProductoPresentacion(),
                    $objDetalleLista->getProductoPventa(),
                    $objDetalleLista->getPrecioPromo(),
                    $objDetalleLista->getCantidadPromocion()
                ),
            );
	  }
	}
	return $arr;
    }
    public function getDetallesAutocompletar(){
	foreach($this->_colLista AS $objLista)
	{
	 $arr[]   =   array(
            'label' =>  $objLista->getListaNombre(),
            'value' =>  $objLista->getListaId()
            );
	}		
	return $arr;
    }
    
    public function salvarMe($arrParametros){
	
        $objLista = NEW DatoListaPreciosExtendido;
        $arrQry['dato']= $objLista->salvarMe($arrParametros);
        
        if($arrParametros['listaPadreId']>0){
        
            $this->_db->addFrom('lista_precios_'. $objLista->getListaId());
            $this->_db->addSelect($objLista->getListaId() . ' AS lista_id, producto_id, producto_pventa,0 AS descuento');	$this->_db->addFromTabla('lista_precios_'.$arrParametros['listaPadreId']);
            
            $this->_db->generarCrearTabla();
            
            $arrQry['lista']=$this->_db->getQry();
            
            $qryFinal = $arrQry['dato'] . $arrQry['lista'];
            
            $this->_db->setQry($qryFinal);
            //echo $this->_db->getQry();exit;
            $this->_db->ejecutarTransaccion();
        }else{
            $this->_db->setQry($arrQry['dato']);
            //echo $this->_db->getQry();exit;
            $this->_db->ejecutar();
        }
        
        if(count($this->_db->getArrError()) > 0){
            $arrDevolver = array(
                    "soyError" => true,
                    "nivel" => 1,
                    "alertados" => $arrAlertador,
                    "mensaje" => "Error en la carga!!!!"
                    );
        }else{
        
            $arrDevolver = array(
                    "soyError" => false,
                    "nivel" => 1,
                    "alertados" => $arrAlertador,
                    "mensaje" => "Lista creada!!!!"
                    );
        }  
        echo json_encode($arrDevolver);exit;
    }
    public function addDetalle($objProducto){
    
	
    
	    $this->_db->addSelect('lista_id');
	    $this->_db->addFrom('datos_listas_precios');
	
	    $this->_db->generarSelect();
	
	
	    $arr =  $this->_db->ejecutar();
	    
	    foreach($arr AS $lista){

		$this->_db->addCamposTabla('lista_id');
		$this->_db->addCamposValue('\'' . $lista[lista_id] .'\'');

		$this->_db->addCamposTabla('producto_id');
		$this->_db->addCamposValue('\'' . $objProducto->getProductoId().'\'');
		
		if($lista['lista_id']==11){
			$this->_db->addCamposTabla('producto_pventa');
			$this->_db->addCamposValue($objProducto->getProductoPrecio() * 1.07);
			$this->_db->addCamposTabla('precio_promocion');
			if($objProducto->getPrecioXCaja()>0){
				$this->_db->addCamposValue($objProducto->getPrecioXCaja() *1.07);
			}else{
				$this->_db->addCamposValue('0');
			}
		}else{
			$this->_db->addCamposTabla('producto_pventa');
			$this->_db->addCamposValue($objProducto->getProductoPrecio());
			$this->_db->addCamposTabla('precio_promocion');
			if($objProducto->getPrecioXCaja()>0){
				$this->_db->addCamposValue($objProducto->getPrecioXCaja());
			}else{
				$this->_db->addCamposValue('0');
			}
		}
		$this->_db->addCamposTabla('cantidad_promocion');
		if($objProducto->getUnidades()>0){
            $this->_db->addCamposValue('\'' . $objProducto->getUnidades() .'\'');
		}else{
            $this->_db->addCamposValue('0');
		}

		$this->_db->addFrom('lista_precios_'. $lista[lista_id]);
	    
		$this->_db->generarInsert();

		//echo $this->_db->getQry();exit;
		
		$arrQry['lista'].= $this->_db->getQry();
	    }
	    return $arrQry['lista'];
    
    
    }       
    public function actualizarMe($arrParametros){
    
	$objDetalleLista = NEW DetalleListaPreciosExtendido();
	$objDetalleLista ->actualizarMe($arrParametros);
    }
    
    
}
?>
