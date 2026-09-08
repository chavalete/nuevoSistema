<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/CotizacionDolarExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteCotizacionDolar
{
    private $_colCotizaciones= Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    public function __construct(){

	$this->_objFuncionesComunes = new FuncionesComunes();
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
    }
    public function cargarCotizaciones($id){

	$this->_colCotizaciones[$id] = NEW CotizacionDolarExtendido();
	$this->_colCotizaciones[$id]->cargarMe($id);
    }
    public function setColObjCotizaciones($objCotizaciones){
    
	$this->_colCotizaciones[$objCotizaciones->getCotizacionId()] = $objCotizaciones;
    }
    public function getColObjCotizaciones(){
	return $this->_colCotizaciones;
    }
    public function cargarCotizacionesLazzy($arrIds){
    
	foreach($arrIds as $id){
	    $ids.= "'" . $id['cotizacion_id'] . "',";
	}
        $ids=substr($ids, 0, -1);
        $this->_db->addSelect("*,CASE WHEN cotizacion_procesada THEN 'Si' ELSE 'No' END AS procesada");
        $this->_db->addFrom('datos_cotizacion_dolar');
        $this->_db->addWhere("cotizacion_id IN (" . $ids . ")" );
        $this->_db->AddOrderBy('cotizacion_id DESC');
        $this->_db->generarSelect();
	
        $resultado = $this->_db->ejecutar();
        
	foreach($resultado AS $detalle){
	    $objCotizaciones= NEW CotizacionDolarExtendido();
	    $objCotizaciones->cargarMe($detalle);
	    $this->setColObjCotizaciones($objCotizaciones);
	    
	}
    }
    
    public function buscarCotizaciones($arrParametros){

	$this->_db->addSelect('cotizacion_id');
	
	$this->_db->addFrom('datos_cotizacion_dolar');

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
	
	if($arrParametros['cotizacion'] !=null && $arrParametros['desdeAlta']==null){
	    $this->_db->addWhere('cotizacion = \'' . $arrParametros['cotizacion'] . '\'');
	}

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenCotizaciones($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='actualizacionDePrecios'){

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
	    $arrIds[$i]['cotizacion_id'] = $arr[$i]['cotizacion_id'];
	}
	$this->cargarCotizacionesLazzy($arrIds);
    }
    public function setOrdenCotizaciones($arrParametros){

	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'cotizacion_id';
	    $this->_ordenColumnas['ordenarOrden']  =   'desc';
	}
    }
    public function getOrdenCotizaciones(){

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

	foreach($this->_colCotizaciones AS $cotizacion){

	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $cotizacion->getCotizacionId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  false,
                    "detalles"      =>  false,
                    "enviarTransaccion"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
		    $cotizacion->getCotizacion(),
                    $cotizacion->getCotizacionFechaHora(),
                    $cotizacion->getObs()
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
        $objCotizacionDolar = NEW CotizacionDolarExtendido();
        $qryFinal=$objCotizacionDolar->salvarMe($arrParametros);
    
	
        //$this->_db->addCamposUpdate('producto_pventa = producto_pventa_dolar *' . $objCotizacionDolar->getCotizacion());
        $this->_db->addCamposUpdate('producto_pcosto = producto_pcosto_dolar *' .   $objCotizacionDolar->getCotizacion());
        $this->_db->addWhere('producto_pcosto_dolar > 0');
        //$this->_db->addWhere('producto_pventa_dolar > 0');
        
        $this->_db->addFrom('datos_productos');
        //generar qry
        $this->_db->generarUpdate();
        
        $qryFinal.=$this->_db->getQry();
        
        
        $this->_db->addCamposUpdate('cotizacion_procesada=true');
        $this->_db->addCamposUpdate('cotizacion_procesada_fecha_hora=now()');
        $this->_db->addCamposTabla('cotizacion_usuario_id');
        $this->_db->addCamposValue($_SESSION['usuarioId']);
        $this->_db->addWhere('cotizacion_id =' .  $objCotizacionDolar->getCotizacionId());
        
        $this->_db->addFrom('datos_cotizacion_dolar');
        //generar qry
        $this->_db->generarUpdate();

        //echo $this->_db->getQry();exit;
        //asigamos el resulta que es un array a una variable
        $qryFinal.= $this->_db->getQry();
        
        //echo $qryFinal;exit;
        $this->_db->setQry($qryFinal);
        $this->_db->ejecutarTransaccion();
        
        if (count($this->_db->getArrError()) > 0){
                        $this->_objFrenteError->generarError($this->_db->getArrError());
                        $this->_objFrenteError->setQry($qryFinal);
                        $this->_objFrenteError->cargarError();
                        //salvar el error
                    }else{
                        $arrDevolver = array(
                                            "soyError" => false,
                                            "nivel" => 0,
                                            "alertados" => $arrAlertador,
                                            "mensaje" => "Precios actualizados!, debe salir y vovler a ingresar al sistema"
                                            );
                    echo json_encode($arrDevolver);
                    }  
                    
        
    }
}
?>
