<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';
require_once 'EstanteriaExtendido.php';


/**
 * Description of FrenteStock
 *
 * @author dMELMAC
 */

class FrenteControlStock
{
    private $_db;
    private $_colControlStock = Array();
    private $_objFuncionesComunes;

    public function __construct() {
	$this->_objFuncionesComunes = new FuncionesComunes();
    }
       
    public function generarControlStock($arrParametros){

	$this->_db = new FrenteAlmacenamiento();
	
	$arrCodigos = explode ("||",$arrParametros['codigosEntradas']);
	array_pop($arrCodigos);

	$txtCodigos = implode("', '", $arrCodigos);

        $this->_db->addSelect('trazabilidad_codigo, producto_nombre || \' \' || producto_presentacion AS producto, producto_id, estanteria_nombre');
        $this->_db->addFrom('datos_trazabilidad');
	$this->_db->addFrom('INNER JOIN datos_productos USING(producto_id)');
	$this->_db->addFrom('INNER JOIN datos_estanterias USING(estanteria_id)');
	$this->_db->addWhere('trazabilidad_codigo IN (\'' . $txtCodigos . '\')');
	$this->_db->addWhere('datos_trazabilidad.estanteria_id = ' . $this->_objFuncionesComunes->parsearAutocompletar($arrParametros['estanterias']));
	$this->_db->addWhere('en_stock=true ');
	$this->_db->addGroup('trazabilidad_codigo, producto_nombre, producto_presentacion, producto_id, estanteria_nombre');

	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $arr =  $this->_db->ejecutar();
	
	foreach($arr AS $codigo){

	    $arrDatos[$codigo['producto_id']]['codigos'][]=$codigo['trazabilidad_codigo'];
	    $arrDatos[$codigo['producto_id']]['producto']=$codigo['producto'];
	    $arrDatos[$codigo['producto_id']]['estanteria']=$codigo['estanteria_nombre'];
	    $arrDatos[$codigo['producto_id']]['producto_id']=$codigo['producto_id'];
	    
	}
	$this->_db->addCamposTabla('tmp_control_stock_control_id_seq');
	$this->_db->generarProximo();

	$id = $this->_db->ejecutar();
	//var_dump($id);exit;

	foreach ($arrDatos AS $productos){

	    $this->_db = new FrenteAlmacenamiento();


	    $txtCodigosPorProducto = implode("','", $productos[codigos]);
	    $this->_db->addSelect('trazabilidad_codigo');
	    $this->_db->addFrom('datos_trazabilidad');
	    $this->_db->addWhere('trazabilidad_codigo NOT IN (\'' . $txtCodigosPorProducto . '\')');
	    $this->_db->addWhere('datos_trazabilidad.estanteria_id = ' . $this->_objFuncionesComunes->parsearAutocompletar($arrParametros['estanterias']));
	    $this->_db->addWhere('en_stock=true');
	    $this->_db->addWhere('producto_id = ' . $productos[producto_id] .'');

	    $this->_db->generarSelect();
	    //echo $this->_db->getQry();exit;
	    $arrFaltantes =  $this->_db->ejecutar();

	    $this->_db->addFrom('tmp_control_stock');

	    $this->_db->addCamposTabla('consulta_id');
	    $this->_db->addCamposTabla('producto');
	    $this->_db->addCamposTabla('estanteria');
	    $this->_db->addCamposTabla('codigos_leidos');
	    $this->_db->addCamposTabla('total');
	    $this->_db->addCamposTabla('codigos_faltantes');
	    $this->_db->addCamposTabla('cantidad');
	    $this->_db->addCamposTabla('diferencia');


	    $txtCodigosPorProducto = implode(", ", $productos[codigos]);
	    $this->_db->addCamposValue('\'' . $id[0][nextval] . '\'');
	    $this->_db->addCamposValue('\'' . $productos[producto] . '\'');
	    $this->_db->addCamposValue('\'' . $productos[estanteria] . '\'');
	    $this->_db->addCamposValue('\'' . $txtCodigosPorProducto . '\'');
	    $this->_db->addCamposValue('\'' . count($productos[codigos]) . '\'');

	    if($arrFaltantes[0] == null){

		$this->_db->addCamposValue('\' Sin registros \'');
		$this->_db->addCamposValue('\'' . 0 . '\'');
		$this->_db->addCamposValue('\'' . 0 . '\'');
	    }else{
		$txtCodigosFaltantes="";
		foreach($arrFaltantes AS $faltantes){
		    $txtCodigosFaltantes.= $faltantes['trazabilidad_codigo'] . ", ";
		}
		  
		$this->_db->addCamposValue('\'' . $txtCodigosFaltantes . '\'');
		$this->_db->addCamposValue('\'' . count($arrFaltantes) . '\'');

		$leidos = count($productos[codigos]);
		$faltantes = count($arrFaltantes);
		$diferencia = abs($leidos - $faltantes);
		$this->_db->addCamposValue('\'' . $diferencia .'\'');
	    }
    
	    $this->_db->generarInsert();
	    //echo $this->_db->getQry();exit;
	    $resultado = $this->_db->ejecutar();
	}
	    if($resultado==0){
		echo "Ocurrio un error, verifique los datos";
	    }else{
		echo "Control creado!!!";
	    }

    }

    public function buscarControlStock ($arrParametros){

	$this->_db = new FrenteAlmacenamiento();


	$this->_db->addSelect('producto, estanteria, codigos_leidos, total, codigos_faltantes, cantidad, diferencia');
        $this->_db->addFrom('tmp_control_stock');
	
        //**ANALIZO EL WHERE**//
        if($arrParametros['tipo']=='controlStock'){

            //$this->_db->addWhere(' cantidad > 0 AND sucursal_id = ' . $arrParametros[stringBuscar]);
            $primerRegistro = 0;
            $ultimoRegistro = 11;
	    }else{
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
        }

        //**fin where **/
        //**INICIO ORDER BY**//
        //*seteo el orden*/
        $this->setOrdenStock($arrParametros);
        $this->_db->AddOrderBy('consulta_id');
        //**FIN ORDER BY**/
        //groupBy
        
        //*INICIO LIMIT*//
        if($arrParametros['tipo']=='controStock'){
            $this->_db->addLimit(100);
        }
        //*FIN LIMIT*//
        $this->_db->generarSelect();
	//echo $this->_db->getQry();
        $arr =  $this->_db->ejecutar();

	$this->setTotal(count($arr));

	$this->setPagina($arrParametros['pagina']);

	if($this->getTotal() < $ultimoRegistro)
	{
	    $ultimoRegistro = $this->getTotal();
	}

        for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
	    $array['producto'] = $arr[$i]['producto'];
	    $array['estanteria'] = $arr[$i]['estanteria'];
	    $array['codigos_leidos'] = $arr[$i]['codigos_leidos'];
	    $array['total'] = $arr[$i]['total'];
	    $array['codigos_faltantes'] = $arr[$i]['codigos_faltantes'];
	    $array['cantidad'] = $arr[$i]['cantidad'];
	    $array['diferencia']  = $arr[$i]['diferencia'];
	    $this->_colControlStock[]= $array;
        }
    }
    
    public function setOrdenStock($parametros){
        if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != "")
        {
            $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
        }else
        {
            //definir default
            $this->_ordenColumnas['ordenarPor']    =   'consulta_id';
            $this->_ordenColumnas['ordenarOrden']  =   'desc';
        }
    }

    public function getOrdenStock(){
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

        foreach($this->_colControlStock AS $objConsulta){
                $arr[] = array(
                    //id si o si un solo string (sin espacios en blanco)
                    "id"=>  "1", //asi cuanta desde 1 en adelante
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  false,
                        "editable"      =>  false,
                        "detalles"      =>  false,
                        "imprimir"      =>  false
                        ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell" => array(
                                $objConsulta['estanteria'],
				$objConsulta['producto'],
				$objConsulta['codigos_leidos'],
				$objConsulta['total'],
                                $objConsulta['codigos_faltantes'],
                                $objConsulta['cantidad'],
                                $objConsulta['cantidad']
                              ),
            );
            $i++;
        }
       return $arr;
    }
    
}
?>