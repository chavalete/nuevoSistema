<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/ProveedorExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteProveedores
{
    private $_colProve = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    
    public function __construct(){
	$this->_objFuncionesComunes = new FuncionesComunes();
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
    }
    public function cargarProveedor($id){
	$this->_colProve[$id] = NEW ProveedorExtendido();
	$this->_colProve[$id]->cargarMe($id);
    }
    public function setColObjProveedores($objProveedor){
    
	$this->_colProve[$objProveedor->getProveId()] = $objProveedor;
    }
    public function getColObjProveedor(){
	return $this->_colProve;
    }
    public function cargarProveedoresLazzy($arrIds){
    
	foreach($arrIds as $id){
	    $ids.= "'" . $id['prove_id'] . "',";
	}
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
	$this->_db->addSelect('prove_activo AS prove_activo');
	$this->_db->addSelect('\'Activo\'  AS prove_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS prove_no_activo_desc');
	$this->_db->addSelect('CASE WHEN prove_activo THEN \'Activo\' ELSE \'Inactivo\' END AS prove_activo_mensaje');
        $this->_db->addFrom('datos_proveedores');
        $this->_db->addWhere("prove_id IN (" . $ids . ")" );
	$this->_db->generarSelect();

        $resultado = $this->_db->ejecutar();
        
        //echo $this->_db->getQry();
        
        
	foreach($resultado AS $detalle){
	    $objProveedor= NEW ProveedorExtendido();
	    $objProveedor->cargarMe($detalle);
	    $this->setColObjProveedores($objProveedor);
	    
	}
    }
    public function buscarProveedores($arrParametros) {

	$this->_db->addSelect('prove_id');
	
	$this->_db->addFrom('datos_proveedores');


	//**ANALIZO EL WHERE**//

	if($arrParametros['stringBuscar']){
	    $this->_db->addWhere('prove_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
	}
	//**where faso = true**/
	if($arrParametros['proveedores'] !=null && $arrParametros['desdeAlta'] == null){
	    
	    $this->_db->addWhere('prove_id = \''  . $arrParametros['proveedores'] . '\'');
	}
	if($arrParametros['categorias'] !=null){

	    $this->_db->addWhere('categoria_id = \'' . $arrParametros['categorias'] . '\'');
	}
	if($arrParametros['proveCuit'] !=null){

	    $this->_db->addWhere('prove_cuit = \'' . $arrParametros['proveCuit'] . '\'');
	}
	if($arrParametros['gln'] !=null){

	    $this->_db->addWhere('prove_gln = \'' . $arrParametros['gln'] . '\'');
	}
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenProveedores($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='proveedores'){

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
	    $arrIds[$i]['prove_id'] = $arr[$i]['prove_id'];
	}
	$this->cargarProveedoresLazzy($arrIds);
    }
    public function setOrdenProveedores($arrParametros){

	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){

	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'prove_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenProveedores(){

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

	foreach($this->_colProve AS $proveedor){
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $proveedor->getProveId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $proveedor->getProveNombre(),
                    $proveedor->getProveContacto(),
                    $proveedor->getProveCuit(),
                    $proveedor->getProveTelefono(),
		    $proveedor->getProveDireccion(),
		    $proveedor->getObs(),
                    Array
			(
			"value" => $proveedor->getProveActivo(),
			"label" =>$proveedor->getProveActivoMensaje(),
			"select" => 
			    Array 
			    (
				Array(
				"value" =>true,
				"label" =>$proveedor->getProveActivoDesc()

				),
				Array

				(
				"value" =>false,
				"label" =>$proveedor->getProveNoActivoDesc()

				)		
			    )
			)
                ),
            );
	    $total++;
	}
	return $arr;
    }
    public function getDetallesAutocompletar(){
	foreach($this->_colProve AS $proveedor)
	{
	 $arr[]   =   array(
            'label' =>  $proveedor->getProveNombre(),
            'value' =>  $proveedor->getProveId()
            );
	}		
	return $arr;
    }
    public function salvarMe($arrParametros){

	$proveedorExtendido = NEW ProveedorExtendido();
	$proveedorExtendido->salvarMe($arrParametros);
    }
    public function actualizarMe($arrParametros){

	$proveedorExtendido = NEW proveedorExtendido();
	$proveedorExtendido->actualizarMe($arrParametros);
    }
}
?>
