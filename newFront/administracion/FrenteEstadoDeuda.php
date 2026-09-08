<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';
/**
 * Description of FrenteStock
 *
 * @author dMELMAC
 */

class FrenteEstadoDeuda
{
    private $_db;
    private $_colBancos = Array();
    private $_objFuncionesComunes;
    private $_bancoId;
    private $_bancoNombre;
    
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
        $this->_objFuncionesComunes = new FuncionesComunes();
    }
   public function setArrError ($error){
        $this->_arrError = $error;
    }
    public function getArrError (){
        return $this->_arrError;
    } 
    
    public function buscarDeudores ($arrParametros){
	
        //**ANALIZO EL WHERE**//
        if($arrParametros['stringBuscar']){
            $this->_db->addWhere('cliente_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
            $primerRegistro = 0;
            $ultimoRegistro = 11;
	    }else{
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
        }
        $this->_db->addSelect('cliente_id,cliente_nombre,sum(importe_deuda) AS importe_deuda'); 
        $this->_db->addFrom('vw_total_deuda_clientes');
        $this->_db->addFrom('INNER JOIN datos_clientes USING(cliente_id)');
        
        if($arrParametros['clientes'] !=null ){
            $this->_db->addWhere('cliente_id = \'' . $arrParametros['clientes'] . '\' ');
        }
        if($_SESSION['usuarioId']==6){
            $this->_db->addWhere('vendedor_id = 11');
	    }
        $this->setOrdenDeudores($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //**FIN ORDER BY**/
        //groupBy
        $this->_db->addGroup('cliente_id');
        
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
            $array['clienteNombre'] = $arr[$i]['cliente_nombre'];
            $array['importeDeuda'] = $arr[$i]['importe_deuda'];
            $array['clienteId'] = $arr[$i]['cliente_id'];
            $this->_colDeudores[]= $array;
        }
        
    }
    
    public function setOrdenDeudores($parametros){
        if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != "")
        {
            $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
        }else
        {
            //definir default
            $this->_ordenColumnas['ordenarPor']    =   'cliente_nombre';
            $this->_ordenColumnas['ordenarOrden']  =   'asc';
        }
    }
    public function getOrdenDeudores(){
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
        foreach($this->_colDeudores AS $objConsulta){
                $arr[] = array(
                    //id si o si un solo string (sin espacios en blanco)
                    "id"=>  $objConsulta['clienteId'],
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  false,
                        "editable"      =>  false,
                        "detalles"      =>  true,
                        "imprimir"      =>  true
                        ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell" => array(
                                $objConsulta['clienteNombre'],
				$this->_objFuncionesComunes->formatoMoneda($objConsulta['importeDeuda'])
				//money_format('%(#2n', $objConsulta['importeDeuda'])
                              ),
            );
            $i++;
        }
       return $arr;
    }
    public function armarDetalles($id){
    
        //echo $id;exit;
        $this->_db->addSelect('to_char(factura_fecha,\'DD/MM/YY\') AS factura_fecha');
        $this->_db->addSelect('factura_nro');
        $this->_db->addSelect('vw.factura_total_fact');
        $this->_db->addSelect('vw.factura_faltan_fact');
        $this->_db->addSelect('cliente_nombre');
        $this->_db->addFrom('datos_facturas dt');
        $this->_db->addFrom('INNER JOIN vw_pendiente_por_factura vw USING(factura_id)');
        $this->_db->addFrom('INNER JOIN datos_clientes ON dt.cliente_id = datos_clientes.cliente_id');
        $this->_db->addWhere('vw.cliente_id = \'' . $id . '\' ');
	
        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $resultado = $this->_db->ejecutar();
	
       
        
            $propiedades =   array(
                array(
                    "display"   =>  'Cliente',
                    "name"      =>  'cliente_calle',
                    "editable"  =>  true,
                    "class"	=>'',
                    "value"     =>  $resultado[0]['cliente_nombre'],

                ),
		    );
        
        foreach($resultado AS $detalle){
            $arr []   =    
                array(
                    //id si o si un solo string (sin espacios en blanco)
                    "id"            =>  $detalle['factura_id'],
                    //define las herramientas
                    "herramientas"   =>  array(
                    "editable"      =>  false,
                    ),
                                "alertado" => $detalle['detalle_anulado'],
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell"          => array(
                        $detalle['factura_fecha'],
                        $detalle['factura_nro'],
                        $this->_objFuncionesComunes->formatoMoneda($detalle['factura_total_fact']),
                        $this->_objFuncionesComunes->formatoMoneda($detalle['factura_faltan_fact'])
                    ),
                );
                $totalDeuda =$totalDeuda + $detalle['factura_faltan_fact'];
        }
    
        //A LO CAPOCHA GOMES
        $arr []   =    
                array(
                    //id si o si un solo string (sin espacios en blanco)
                    "id"            =>  $detalle['factura_id'],
                    //define las herramientas
                    "herramientas"   =>  array(
                    "editable"      =>  false,
                    ),
                    "alertado" => $detalle['detalle_anulado'],
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell"          => array(
                        "",
                        "",
                        "",
                     "Total deuda:  " .   $this->_objFuncionesComunes->formatoMoneda($totalDeuda)
                    ),
                );
    
        $detalles=   array(
            "modelo"    =>  array(
                array(
                    "display"   =>  'Fecha',
                    "name"      =>  'peso_neto',
                    "class"	=>  '',
                    "editable"  =>  false,
                ),
                array(
                    "display"   =>  'Factura Nro',
                    "name"      =>  'tara',
                    "class"	=>  '',
                    "editable"  =>  false,
                ),
                array(
                    "display"   =>  'Importe Factura IVA inc',
                    "name"      =>  'peso_neto',
                    "class"	=>  '',
                    "editable"  =>  false,
                ),
                array(
                    "display"   =>  'Pendiente de pago',
                    "name"      =>  'peso_neto',
                    "class"	=>  '',
                    "editable"  =>  false,
                ),
            ),
            "celdas"    =>  $arr
        );
        $array_devolver =   array(
            "editable_cabecera" => false,
            "propiedades"       =>  $propiedades,
            "listadoDetalles"   =>  $detalles
            );
        return $array_devolver;
    }
}
?>
