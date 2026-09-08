<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';
require_once 'EstanteriaExtendido.php';


/**
 * Description of FrenteStock
 *
 * @author dMELMAC
 */

class FrenteControlStockEstanteria
{
    private $_db;
    private $_colControlStock = Array();
    private $_objFuncionesComunes;

    public function __construct() {
	$this->_objFuncionesComunes = new FuncionesComunes();
    }
       
    public function generarControlStock($arrParametros){

	$this->_db = new FrenteAlmacenamiento();

        $this->_db->addSelect('trazabilidad_codigo, producto_nombre || \' \' || producto_presentacion || \'( REF: \' || codigo_referencia || \')\'  AS producto, datos_trazabilidad.producto_id, estanteria_nombre');
        $this->_db->addFrom('datos_trazabilidad');
	$this->_db->addFrom('INNER JOIN datos_productos USING(producto_id)');
	$this->_db->addFrom('INNER JOIN datos_estanterias USING(estanteria_id)');
        $this->_db->addFrom('INNER JOIN datos_lotes USING(lote_id)');
	
        if($arrParametros['estanterias'] != NULL){
            $this->_db->addWhere('datos_trazabilidad.estanteria_id = ' . $arrParametros['estanterias']);
        }
        
        if($arrParametros['productos'] != NULL){
            $this->_db->addWhere('datos_trazabilidad.producto_id= ' . $arrParametros['productos']);
        }
        
        if($arrParametros['lotes'] != NULL){
            $this->_db->addWhere('datos_lotes.lote = \'' . $arrParametros['lotes'] . '\'');
        }
        
        
	$this->_db->addWhere('en_stock=true ');
	$this->_db->addGroup('trazabilidad_codigo, producto_nombre, producto_presentacion, codigo_referencia ,datos_trazabilidad.producto_id, estanteria_nombre');

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
	
	foreach ($arrDatos AS $productos){

            

	    $txtCodigosPorProducto = implode(", ", $productos[codigos]);
	    $this->_db = new FrenteAlmacenamiento();

	    $this->_db->addFrom('tmp_control_stock');

	    $this->_db->addCamposTabla('consulta_id');
	    $this->_db->addCamposTabla('producto');
	    $this->_db->addCamposTabla('estanteria');
	    $this->_db->addCamposTabla('codigos_leidos');
	    $this->_db->addCamposTabla('Total');


	    $this->_db->addCamposValue('\'' . $id[0][nextval] . '\'');
	    $this->_db->addCamposValue('\'' . $productos[producto] . '\'');
	    $this->_db->addCamposValue('\'' . $productos[estanteria] . '\'');
	    $this->_db->addCamposValue('\'' . $txtCodigosPorProducto . '\'');
	    $this->_db->addCamposValue('\'' . count($productos[codigos]) . '\'');

	    $this->_db->generarInsert();
	    //echo $this->_db->getQry();exit;
	    $resultado = $this->_db->ejecutar();
            
            $this->_db->addFrom('tmp_control_stock');
            $this->_db->addWhere('consulta_id NOT IN (' . $id[0][nextval] . ')');
            $this->_db->generarDelete();
            //echo $this->_db->getQry();exit;
            $resultado = $this->_db->ejecutar();
            
            
	}
	    if(count($resultado)==0){
		$arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al generar el control'
		    );
	    
	    }else{
		$arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'Control generado con exito'
		    );
	    
	   }
	   echo json_encode($arrDevolver);exit;

    }

    public function buscarControlStock ($arrParametros){

	$this->_db = new FrenteAlmacenamiento();


	$this->_db->addSelect('producto, estanteria, codigos_leidos, total, codigos_faltantes, cantidad, diferencia');
        $this->_db->addFrom('tmp_control_stock');
	
        //a cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
        

        //**fin where **/
        //**INICIO ORDER BY**//
        //*seteo el orden*/
        $this->setOrdenStock($arrParametros);
        $this->_db->AddOrderBy('consulta_id DESC');
        //**FIN ORDER BY**/
        //groupBy
        
        //*INICIO LIMIT*//
        if($arrParametros['tipo']=='controlStock'){
            $this->_db->addLimit(100);
        }
        //*FIN LIMIT*//
        $this->_db->generarSelect();
	//echo $this->_db->getQry();
        $arr =  $this->_db->ejecutar();

	$this->setTotal(count($arr));

	$this->setPagina($arrParametros['pagina']);

	if($this->getTotal() < $ultimoRegistro){
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
				$objConsulta['total']
                              ),
            );
            $i++;
        }
       return $arr;
    }
    
}
?>
