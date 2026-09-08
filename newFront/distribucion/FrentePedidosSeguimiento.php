<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/**
 * Description of FrenteSeguimientoHisotirco de pedidos
 *
 * @author dMELMAC
 */

class FrentePedidosSeguimiento
{
    private $_db;
    private $_colPedidos = Array();
    private $_objFuncionesComunes;

    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
	$this->_objFuncionesComunes = new FuncionesComunes();
    }
    
    public function consultarPedidos ($arrParametros){

    
        $this->_db->addSelect('id_movimiento ,remito_nro, to_char(fecha_hora,\'DD-MM-YYYY HH24:MI\') AS fecha_hora,cliente_nombre,estado_desc');
        $this->_db->addFrom('movimientos_pedidos');
        $this->_db->addFrom('INNER JOIN datos_movimientos USING (id_movimiento)');
        $this->_db->addFrom('INNER JOIN datos_clientes USING (cliente_id)');
        $this->_db->addFrom('INNER JOIN datos_estados ON movimientos_pedidos.estado_id = datos_estados.estado_id');
	//**ANALIZO EL WHERE**//
        

        if($arrParametros['facturas'] !=null ){
            $this->_db->addWhere('remito_nro = \'' . $arrParametros['facturas'] . '\' ');
        }
        //**fin where **/
        //**INICIO ORDER BY**//
        //*seteo el orden*/
       
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
        $this->setOrdenPedidos($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //**FIN ORDER BY**/
        //groupBy
        
        //*INICIO LIMIT*//
        if($arrParametros['tipo']=='seguimientoHistorico'){
            $this->_db->addLimit(100);
        }
        //*FIN LIMIT*//
        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $this->_colPedidos =  $this->_db->ejecutar();
	
	if($this->getTotal() < $ultimoRegistro){
	    $ultimoRegistro = $this->getTotal();
	}
    }
    
    public function setOrdenPedidos($parametros){
        if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != "")
        {
            $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
        }else
        {
            //definir default
            $this->_ordenColumnas['ordenarPor']    =   'movimiento_pedidos_id';
            $this->_ordenColumnas['ordenarOrden']  =   'asc';
        }
    }

    public function getOrdenPedidos(){
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
        
        foreach($this->_colPedidos AS $dato){
		
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
                                $dato['remito_nro'],
                                $dato['fecha_hora'],
                                $dato['cliente_nombre'],
                                $dato['estado_desc']
                              ),
            );
            $i++;
            
        }
       return $arr;
    }
    
}
?>
