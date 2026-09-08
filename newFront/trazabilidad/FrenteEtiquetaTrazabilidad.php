<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'DatoTrazabilidadExtendido.php';

/*
 * dMELMAC 
 * 
 */

/**
 * Description of FrenteEtiquetaTrazabilidad
 *
 * @author by dMELMAC
 */

class FrenteEtiquetaTrazabilidad
{
    private $_db;
    private $_colEtiquetasTrazabilidad = Array();
        
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
    
    public function setColEtiquetasTrazabilidad($obj){
        $this->_colObjEtiquetasTrazabilidad[] = $obj;
    }
    
    public function getColEtiequetasTrazabilidad(){
        return $this->_colObjEtiquetasTrazabilidad;
    }
    
    public function setArrError($arrError){
        $this->_arrError = $arrError;
    }
    
    public function getArrError(){
        return $this->_arrError ;
    }

    public function cargarCodigoTrazabilidad($id){
    
	$objDatoTrazabilidad = new DatoTrazabilidadExtendido();
	$objDatoTrazabilidad->cargarme($id);
	$this->setColEtiquetasTrazabilidad($objDatoTrazabilidad);
    
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
	$this->_db->addWhere('cancelado = false ');
	$this->setOrdenEtiquetas($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	$this->_db->addGroup('trazabilidad_id, dm.id_movimiento');
	
	$this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
    
	$arr =  $this->_db->ejecutar();
	
	if(count($arr) == 0 ){
	    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"No se encontro el codigoTrazabilidad"
		    );
	      echo json_encode($arrDevolver);exit;
	};
	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro){
	    $ultimoRegistro = $this->getTotal();
	}	
	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
	    $this->cargarCodigoTrazabilidad($arr[$i]['trazabilidad_id']);
	}
    
    }



    public function getError(){
        return $this->_arrError;
    }

    //**PARA EL FRENTE**//
    public function setOrdenEtiquetas($parametros){

	if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){
	//echo "entra";
	    $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'trazabilidad_id';
	    $this->_ordenColumnas['ordenarOrden']  =   'desc';
	}
    }

    public function getOrdenEtiquetas(){

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
    
    
        foreach($this->_colObjEtiquetasTrazabilidad AS $objEtiqueta){

		//var_dump($objEtiqueta);exit;
        
                $arr []   =    array
                    (
                    //id si o si un solo string (sin espacios en blanco)
                    "id"            =>  $objEtiqueta->getTrazabilidadId(),
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  false,
                        "editable"      =>  false,
                        "detallesEntradas"      =>  false,
                        "imprimir"      =>  true

                    ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell"          => array(
                        $objEtiqueta->getTrazabilidadFechaAlta(),
                        $objEtiqueta->getTrazabilidadCodigo(),
                        //$objEtiqueta->getObjProducto()->getProductoNombre(),
                        //$objEtiqueta->getObjLote()->getLote()
                        
			),
                    );
        }		
        return $arr;
    }
}
