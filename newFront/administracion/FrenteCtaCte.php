<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';


/**
 * Description of FrenteStock
 *
 * @author dMELMAC
 */

class FrenteCtaCte
{
    private $_db;
    private $_colConsultasCtaCte= Array();
    private $_objFuncionesComunes;

    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
        $this->_objFuncionesComunes = new FuncionesComunes();
    }
    
    
    public function consultarCtaCte ($arrParametros){

        $this->_db->addSelect('to_char(fecha,\'DD\MM\YY\') AS fecha');
        $this->_db->addSelect('cliente_nombre');
        $this->_db->addSelect('factura_nro');
        $this->_db->addSelect('factura_total_fact');
        $this->_db->addSelect('importe_total_cobro');
        $this->_db->addSelect('orden_cobro_nro');
        $this->_db->addSelect('importe_deuda');
        $this->_db->addSelect('relacion_nombre');
        $this->_db->addFrom('cta_cte_clientes');
        $this->_db->addFrom('INNER JOIN datos_clientes USING(cliente_id)');
        $this->_db->addFrom('INNER JOIN relaciones_clientes_cuentas USING(relacion_id)');
        $this->_db->addFrom('LEFT JOIN datos_facturas USING(factura_id)');
        $this->_db->addFrom('LEFT JOIN datos_cobros USING(cobro_id)');
        //**ANALIZO EL WHERE**//
        if($arrParametros['stringBuscar']){
            $this->_db->addWhere(' cantidad > 0 AND sucursal_id = ' . $arrParametros[stringBuscar]);
            $primerRegistro = 0;
            $ultimoRegistro = 11;
	    }else{
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
        }

        
        if($arrParametros['clientes'] !=null){
            $this->_db->addWhere('cliente_id = \'' . $arrParametros['clientes'] . '\' ');
        }
        if($arrParametros['relaciones'] !=null){
            $this->_db->addWhere('relacion_id = \'' . $arrParametros['relaciones'] . '\' ');
        }
        if($arrParametros['fechaDesde'] !=null && $arrParametros[desdeAlta]==null){
            $this->_db->addWhere('fecha >= \'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros['desde']); . '\'');
        }
        if($arrParametros['fechaHasta'] !=null && $arrParametros[desdeAlta]==null){
            $this->_db->addWhere('fecha >= \'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros['fechaHasta']); . '\'');
        }
        //**fin where **/
        //**INICIO ORDER BY**//
        //*seteo el orden*/
        $this->_db->addWhere('anulado = false');
        $this->setOrdenCtaCte($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //**FIN ORDER BY**/
        //groupBy
        
        //*INICIO LIMIT*//
        if($arrParametros['tipo']=='ctaCte'){
            $this->_db->addLimit(500);
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
            $array['fecha'] = $arr[$i]['fecha'];
            $array['clienteNombre'] = $arr[$i]['cliente_nombre'];
            $array['relacionNombre'] = $arr[$i]['relacion_nombre'];
            $array['facturaNro'] = $arr[$i]['factura_nro'];
            $array['facturaTotal'] = $arr[$i]['factura_total_fact'];
            $array['ordenNro'] = $arr[$i]['orden_cobro_nro'];
            $array['importeCobro'] = $arr[$i]['importe_total_cobro'];
            $array['importeDeuda'] = $arr[$i]['importe_deuda'];
            $this->_colConsultasCtaCte[]= $array;
        }
        
    }
    
    public function setOrdenCtaCte($parametros){
        if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != "")
        {
            $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
        }else
        {
            //definir default
            $this->_ordenColumnas['ordenarPor']    =   'fecha';
            $this->_ordenColumnas['ordenarOrden']  =   'asc';
        }
    }

    public function getOrdenCtaCte(){
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
        foreach($this->_colConsultasCtaCte AS $objConsulta){
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
			    $objConsulta['fecha'],
			    $objConsulta['clienteNombre'] ,
			    $objConsulta['relacionNombre'] ,
			    $objConsulta['facturaNro'] ,
			    $this->_objFuncionesComunes->formatoMoneda($objConsulta['facturaTotal']),
			    $objConsulta['ordenNro'],
			    $this->_objFuncionesComunes->formatoMoneda($objConsulta['importeCobro']),
			    $this->_objFuncionesComunes->formatoMoneda($objConsulta['importeDeuda']),
                ),
            );
            $i++;
        }
       return $arr;
    }
    public function salvame($arrParametros){
    
    }
}
?>
