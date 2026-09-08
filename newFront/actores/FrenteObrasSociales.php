<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/ObraSocialExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteObrasSociales
{
    private $_colObrasSociales = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    
    public function __construct()
    {
    }
    public function cargarObraSocial($id)
    {
	$this->_colObrasSociales[$id] = NEW ObraSocialExtendido();
	$this->_colObrasSociales[$id]->cargarMe($id);
    }
    public function buscarObrasSociales($arrParametros)
    {
	$db_dM = NEW FrenteAlmacenamiento('dmelmac');

	$db_dM->addSelect('obra_social_id');
	
	$db_dM->addFrom('datos_obras_sociales');

	//**ANALIZO EL WHERE**//

	if($arrParametros['stringBuscar'])
	{
	    $db_dM->addWhere('obra_social_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
	}
	if($arrParametros['obrasSociales'] !=null)
	{
	    $db_dM->addWhere('obra_social_id = \'' . $arrParametros[obrasSociales] . '\'');
	}

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenObrasSociales($arrParametros);
	$db_dM->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='abmObrasSociales')
	{
	    $db_dM->addLimit(100);
	}
	//*FIN LIMIT*//
	$db_dM->generarSelect();

	$arr =  $db_dM->ejecutar();
	
	//ahora seteamos el total y la pagina
	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro)
	{
	    $ultimoRegistro = $this->getTotal();
	}	

	for($i=$primerRegistro;$i<$ultimoRegistro;$i++)
	{   
	    $this->cargarObraSocial($arr[$i]['obra_social_id']);
	}
    }
    public function setOrdenObrasSociales($arrParametros)
    {
	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != "")
	{
	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else
	{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'obra_social_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenObrasSociales()
    {
	return $this->_ordenColumnas;
    }

    public function setTotal($total)
    {
	$this->_total = $total;
    }
    public function getTotal()
    {
	return $this->_total;
    }
    //pagina 1 por defecto amigo
    public function setPagina($pagina=1)
    {
	$this->_pagina = $pagina;
    }
    public function getPagina()
    {
	return $this->_pagina;
    }

    public function getDetalles()
    {
	foreach($this->_colObrasSociales AS $obraSocial)
	{
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $obraSocial->getObraSocialId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $obraSocial->getObraSocialNombre(),
		    $obraSocial->getObs(),
                    Array
			(
			"value" => $obraSocial->getObraSocialActiva(),
			"label" =>$obraSocial->getObraSocialActivoMensaje(),
			"select" => 
			    Array 
			    (
				Array(
				"value" =>true,
				"label" =>$obraSocial->getObraSocialActivoDesc()

				),
				Array

				(
				"value" =>false,
				"label" =>$obraSocial->getObraSocialNoActivoDesc()

				)		
			    )
			)
                ),
            );
	}

	return $arr;

    }
    public function getDetallesAutocompletar()
    {
	foreach($this->_colObrasSociales AS $obraSocial)
	{
	 $arr[]   =   array(
            'label' =>  $obraSocial->getObraSocialNombre(),
            'value' =>  $obraSocial->getObraSocialId()
            );
	}		
	return $arr;
    }
    public function salvarMe($arrParametros)
    {
	$obraSocialExtendido = NEW ObraSocialExtendido();
	$obraSocialExtendido->salvarMe($arrParametros);
    }
    public function actualizarMe($arrParametros)
    {
	$obraSocialExtendido = NEW ObraSocialExtendido();
	$obraSocialExtendido->actualizarMe($arrParametros);
    }
}
?>