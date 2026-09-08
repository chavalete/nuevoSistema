<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/administracion/DatoListaPreciosExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteListasDePreciosAgrupadas
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
        $this->_db->addSelect('to_char(lista_fecha_hora,\'DD-MM-YY HH24:MI\') AS lista_fecha_hora');
        $this->_db->addSelect('lista_mayorista');
        $this->_db->addSelect('\'Si\'  AS lista_mayorista_desc');
        $this->_db->addSelect('\'No\'  AS lista_no_mayorista_desc');
        $this->_db->addSelect('CASE WHEN lista_mayorista THEN \'Si\' ELSE \'No\' END AS lista_mayorista_mensaje');
        $this->_db->addFrom('datos_listas_precios');
        
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
    
    
    public function buscarListasPorNombre($arrParametros){
    
	$this->_db->addSelect('lista_id');
	$this->_db->addFrom('datos_listas_precios');
	$this->_db->addWhere('lista_nombre ILIKE  \'%' .$arrParametros[stringBuscar].'%\'');
	if($arrParametros['stringBuscar']){
	    $this->_db->addWhere('lista_nombre ILIKE  \'%' .$arrParametros[stringBuscar].'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
	}
	
	if($arrParametros['listaDePrecios'] !=null && $arrParametros['desdeAlta'] == null){    
	    $this->_db->addWhere('lista_id = \''  . $arrParametros['listaDePrecios'] . '\'');
	}
	$this->_db->addwhere('lista_cancelada=false');	
	$this->_db->generarSelect();
	//echo $this->_db->getQry();
	
	$arr =  $this->_db->ejecutar();
	//var_dump($arr);exit;
	$this->setTotal(count($arr));
        
	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
	    $arrIds[$i]['lista_id'] = $arr[$i]['lista_id'];
	}
	$this->cargarListaLazzy($arrIds);
    }
    
    public function setOrdenLista($arrParametros){

	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'lista_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'desc';
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
	   
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $objLista->getListaId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  true,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $objLista->getListaFechaHora(),
                    $objLista->getListaNombre(),
                    Array
                    (
                    "value" => $objLista->getListaMayorista(),
                    "label" =>$objLista->getListaMayoristaMensaje(),
                    "select" => 
                        Array 
                        (
                            Array(
                            "value" =>true,
                            "label" =>$objLista->getListaMayoristaDesc()

                            ),
                            Array

                            (
                            "value" =>false,
                            "label" =>$objLista->getListaMayoristaNoDesc()

                            )		
                        )
                    ),
                    $objLista->getObs()
                ),
            );
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
    
    public function actualizarMe($arrParametros){
        $objDetalleLista = NEW DetalleListaPreciosExtendido();
        $objDetalleLista ->actualizarMe($arrParametros);
    }
    public function cancelar($id){
        $qry="UPDATE datos_listas_precios SET lista_cancelada=true,lista_cancelada_fecha_hora=now(),lista_cancelada_usuario_id=".$_SESSION['usuarioId']." WHERE lista_id = ".$id.";";
        $this->_db->setQry($qry);
       
        $resultado=$this->_db->ejecutar();
        if(count($resultado)==0){
            $arrDevolver = array(
                    "soyError" => true,
                    "nivel" => 1,
                    "mensaje" => "Error al actualizar!!!!"
                    );
        }else{
            $arrDevolver = array(
                    "soyError" => false,
                    "nivel" => 1,
                    "mensaje" => "Lista Cancelada!!!!"
                    );
        
        }
           echo json_encode($arrDevolver);exit;
        
    }
    public function marcarListaMayorista($arrParametros){

	$qry="UPDATE datos_listas_precios SET lista_nombre ='{$arrParametros[campos][lista_nombre]}' WHERE lista_id = $arrParametros[id];";
        
        //echo $qry;
        
        $this->_db->setQry($qry);
       
        $resultado=$this->_db->ejecutar();

        
        if($arrParametros['campos']['lista_mayorista']==1){
        
            //$qry="SELECT * FROM datos_listas_precios WHERE lista_mayorista=true AND lista_cancelada=false;";
	    $qry="SELECT count(*) FROM datos_listas_precios WHERE lista_mayorista AND lista_cancelada=false HAVING count(*)>=1;";
            $this->_db->setQry($qry);
        //echo $qry;exit;
            $resultado=$this->_db->ejecutar();
            //echo count($resultado);exit;
            if(count($resultado)>0){
                $arrDevolver = array(
                        "soyError" => true,
                        "nivel" => 1,
                        "mensaje" => "No puede haber dos listas mayorista"
                        );
                echo json_encode($arrDevolver);exit;
            }
        }
        if($arrParametros['campos']['lista_mayorista']==1){
            $valor = 'true';
        }else{
            $valor = 'false';
        }
        
        $qry="UPDATE datos_listas_precios SET lista_mayorista =$valor WHERE lista_id = $arrParametros[id];";
        
        $this->_db->setQry($qry);
       
        $resultado=$this->_db->ejecutar();
        if(count($resultado)==0){
            $arrDevolver = array(
                    "soyError" => true,
                    "nivel" => 1,
                    "mensaje" => "Error al actualizar!!!!"
                    );
        }else{
            $arrDevolver = array(
                    "soyError" => false,
                    "nivel" => 1,
                    "mensaje" => "Lista mayorista seteada!!!!"
                    );
        
        }
           echo json_encode($arrDevolver);exit;
        
    
    }
    
    
}
?>
