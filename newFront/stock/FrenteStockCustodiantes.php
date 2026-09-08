<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/CustodianteExtendido.php';


/**
 * Description of FrenteStock
 *
 * @author dMELMAC
 */

class FrenteStockCustodiantes
{
    private $_db;
    private $_colConsultasStock = Array();
    private $_objFuncionesComunes;

    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
	$this->_objFuncionesComunes = new FuncionesComunes();
    }
    
    public function cargarObjCustodia($id){
        $objCustodia = new CustodianteExtendido();
        $objCustodia->cargarMe($id);
        return $objCustodia;
    }
    
    public function buscarStockCustodiantes ($arrParametros){
    
	$this->_db->addSelect('custodiante_id,custodiante_nombre_completo, count(trazabilidad_id) AS cantidad');
        $this->_db->addFrom('movimientos_trazabilidad');
	$this->_db->addFrom('INNER JOIN datos_movimientos USING(id_movimiento)');
	$this->_db->addFrom('INNER JOIN datos_custodiantes USING(custodiante_id)');
        $this->_db->addWhere('custodiante_id > 0 ');
        $this->_db->addWhere('finalizado = false ');
        //**ANALIZO EL WHERE**//
        if($arrParametros['stringBuscar']){
            $this->_db->addWhere('custodiante_nombre_completo ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
            $primerRegistro = 0;
            $ultimoRegistro = 11;
	    }else{
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
        }

        if($arrParametros['custudia'] !=null ){
            $this->_db->addWhere('custodiante_id = \'' . $arrParametros['custodiantes'] . '\' ');
        }
	if($arrParametros['valor'] !=null ){
            $this->_db->addWhere('codigo_referencia = \'' . $arrParametros['valor'] . '\' ');
        }
	if($arrParametros['value'] !=null ){
            $this->_db->addWhere('codigo_referencia = \'' . $arrParametros['value'] . '\' ');
        }
        //**fin where **/
        //**INICIO ORDER BY**//
        //*seteo el orden*/
        $this->setOrdenStockCustodiantes($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //**FIN ORDER BY**/
        //groupBy
        $this->_db->addGroup('custodiante_nombre_completo, custodiante_id');
        
        //*INICIO LIMIT*//
        if($arrParametros['tipo']=='stockEnCustodia'){
            $this->_db->addLimit(100);
        }
        //*FIN LIMIT*//
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $arr =  $this->_db->ejecutar();

	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);

	if($this->getTotal() < $ultimoRegistro){

	    $ultimoRegistro = $this->getTotal();
	}

        for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
            $array['objCustodia'] = $this->cargarObjCustodia($arr[$i]['custodiante_id']);
	    $array['cantidad'] = $arr[$i]['cantidad'];
            $this->_colConsultasStock[]= $array;
        }
        
    }
    
    public function setOrdenStockCustodiantes($parametros){
        if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != "")
        {
            $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
        }else
        {
            //definir default
            $this->_ordenColumnas['ordenarPor']    =   'custodiante_nombre_completo';
            $this->_ordenColumnas['ordenarOrden']  =   'asc';
        }
    }

    public function getOrdenStockCustodiantes(){
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
        $i = 1;
        foreach($this->_colConsultasStock AS $objConsulta){
                $arr[] = array(
                    //id si o si un solo string (sin espacios en blanco)
                    "id"=>  $objConsulta['objCustodia']->getCustodianteId(), //asi cuanta desde 1 en adelante
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  false,
                        "editable"      =>  false,
                        "detalles"      =>  true,
                        "imprimir"      =>  false
                        ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell" => array(
				$objConsulta['objCustodia']->getCustodianteNombreCompleto(),
                                $objConsulta['cantidad']
                              ),
            );
            $i++;
        }
       return $arr;
    }
    public function armarDetalles($id){

    
	$this->_db->addSelect('custodiante_nombre_completo, trazabilidad_codigo, codigo_referencia, producto_nombre || producto_presentacion AS producto');
        $this->_db->addFrom('movimientos_trazabilidad');
        $this->_db->addFrom('INNER JOIN datos_trazabilidad USING(trazabilidad_id)');
	$this->_db->addFrom('INNER JOIN datos_movimientos USING(id_movimiento)');
	$this->_db->addFrom('INNER JOIN datos_custodiantes USING(custodiante_id)');
	$this->_db->addFrom('INNER JOIN datos_productos USING(producto_id)');
        $this->_db->addWhere('custodiante_id = '. $id .' ');
        $this->_db->addWhere('finalizado = false ');
        $this->_db->addWhere('datos_movimientos.tipo_movimiento_id  = 8 ');
        
        
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado =  $this->_db->ejecutar();
	
	

	$propiedadesTransaccion =   array(
		array(

		    "display"   =>  'Custodiante',
		    "name"      =>  'id_movimiento',
		    "editable"  =>  false,
		    "class"	=>'',
		    "value"     => $resultado[0][custodiante_nombre_completo],
		)
	    );

	foreach($resultado AS $detalle){

		$arr []   =    array
		    (
			//id si o si un solo string (sin espacios en blanco)
			"id"            =>  $detalle[id_movimiento],
			//define las herramientas
			"herramientas"   =>  array(
			    "editable"      =>  false
			    
			),
			//las celdas propiamente dichas, en el orden en que estan declaradas las columnas
			"cell"          => array(
			    $detalle[trazabilidad_codigo],
			    $detalle[producto],
			    $detalle[codigo_referencia]
			),
		    );
		}

    
	$detallesTransaccion    =   array(
	    "modelo"    =>  array(
		array(

		    "display"   =>  'Codigo Unico',
		    "name"      =>  'trazabilidad_codigo',
		    "class"	=>  '',
		    "editable"  =>  false,
		),
		array(
		    "display"   =>  'Producto',
		    "name"      =>  'producto_nombre',
		    "class"	=>  'autocomplete',
		    "editable"  =>  false,
		),
                array(
		    "display"   =>  'Cod. Referencia',
		    "name"      =>  'codigo_referencia',
		    "class"	=>  '',
		    "editable"  =>  false,
		),
	    ),
	    "celdas"    =>  $arr
	);
	
	$array_devolver =   array(
	    "propiedades"       =>  $propiedadesTransaccion,
	    "listadoDetalles"   =>  $detallesTransaccion,
	    "imprimir"		=>  false
	);
	
	    return $array_devolver;
    }
    
}
?>
