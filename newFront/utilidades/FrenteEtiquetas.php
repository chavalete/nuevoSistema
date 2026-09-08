<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

/**
 * Description of FrenteStock
 *
 * @author dMELMAC
 */

class FrenteEtiquetas
{
    private $_db;
    private $_colConsultas= Array();
    private $_objFuncionesComunes;

    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
        $this->_objFuncionesComunes = new FuncionesComunes();
    }
    
    
    
    public function consultar ($arrParametros){

        $this->_db->addSelect('*');
        $this->_db->addFrom('datos_productos');
	    //**ANALIZO EL WHERE**//
        if($arrParametros['stringBuscar']){
            $this->_db->addWhere(' cantidad > 0 AND sucursal_id = ' . $arrParametros[stringBuscar]);
            $primerRegistro = 0;
            $ultimoRegistro = 11;
	    }else{
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) ;
        }

        if($arrParametros['productos'] !=null ){
            $this->_db->addWhere('datos_productos.producto_id = \'' . $arrParametros['productos'] . '\' ');
        }
    
	if($arrParametros['fechaHasta'] !=null){
            $this->_db->addWhere('lote_vencimiento <= \'' . $arrParametros[fechaHasta] . '\'');
        }
        if($arrParametros['productoFamilia'] !=null){
            $this->_db->addWhere('producto_nombre ILIKE  \'%' . $arrParametros[productoFamilia] .'%\'');
        }
        //**fin where **/
        //**INICIO ORDER BY**//
        //*seteo el orden*/
        $this->setOrden($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //**FIN ORDER BY**/
        //groupBy
        //$this->_db->addGroup('datos_productos.producto_id, lote_id, estanteria_id, almacen_id, sucursal_id, cantidad, valor_stock, costo_stock,limonMoreno.producto_pventa');
        
        //*INICIO LIMIT*//
        if($arrParametros['tipo']=='consultaStock'){
            //$this->_db->addLimit(100);
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
            $array['producto'] = $arr[$i]['producto_nombre'];
            
            $this->_colConsultas[]= $array;
        }
        
    }
    
    public function setOrden($parametros){
        if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != "")
        {
            $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
        }else
        {
            //definir default
            $this->_ordenColumnas['ordenarPor']    =   'producto_nombre';
            $this->_ordenColumnas['ordenarOrden']  =   'asc';
        }
    }

    public function getOrden(){
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
        setlocale(LC_MONETARY, 'en_US');
        foreach($this->_colConsultas AS $objConsulta){
                $arr[] = array(
                    //id si o si un solo string (sin espacios en blanco)
                    "id"=>  $i, //asi cuanta desde 1 en adelante
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  false,
                        "editable"      =>  false,
                        "detalles"      =>  false,
                        "imprimir"      =>  false
                        ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell" => array(
                                $objConsulta['producto']
                              ),
            );
            $i++;
        }
       return $arr;
    }
    
}
?>
