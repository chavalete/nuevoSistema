<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';


/**
 * Description of FrenteStock
 *
 * @author dMELMAC
 */

class FrenteMayor
{
    private $_db;
    private $_colConsultasCtaCte= Array();
    private $_objFuncionesComunes;

    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
        $this->_objFuncionesComunes = new FuncionesComunes();
    }
    
    
    public function consultarMayor ($arrParametros){

        $this->_db->addSelect('to_char(fecha,\'DD\MM\YY\') AS fecha');
        $this->_db->addSelect('cliente_nombre');
        $this->_db->addSelect('factura_nro');
        $this->_db->addSelect('factura_total_fact');
        $this->_db->addSelect('importe_total_cobro');
        $this->_db->addSelect('orden_cobro_nro');
        $this->_db->addSelect('importe_deuda');
        $this->_db->addSelect('usuario_nombre_completo');;
        $this->_db->addFrom('cta_cte_clientes');
        $this->_db->addFrom('INNER JOIN datos_clientes USING(cliente_id)');
        $this->_db->addFrom('INNER JOIN datos_usuarios USING(usuario_id)');
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
        if($_SESSION['usuarioId']==6){
                $this->_db->addWhere('vendedor_id = 11');
        }

        
        if($arrParametros['clientes'] !='undefined' && $arrParametros['clientes'] !=null){
            $this->_db->addWhere('cta_cte_clientes.cliente_id = \'' . $arrParametros['clientes'] . '\' ');
        }
        if($arrParametros['relaciones'] !=null){
            $this->_db->addWhere('cta_cte_clientes.relacion_id = \'' . $arrParametros['relaciones'] . '\' ');
        }
        if($arrParametros['facturas'] !=null){
            $this->_db->addWhere('factura_nro = \'' . $arrParametros['facturas'] . '\' ');
        }
        if($arrParametros['ordenMro'] !=null){
            $this->_db->addWhere('orden_cobro_nro = \'' . $arrParametros['ordenNro'] . '\' ');
        }
        if($arrParametros['fechaDesde'] !=null && $arrParametros[desdeAlta]==null){
            $this->_db->addWhere('fecha >= \'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros['fechaDesde']) . '\'');
        }
        if($arrParametros['fechaHasta'] !=null && $arrParametros[desdeAlta]==null){
            $this->_db->addWhere('fecha >= \'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros['fechaHasta']) . '\'');
        }
        //**fin where **/
        //**INICIO ORDER BY**//
        //*seteo el orden*/
        $this->_db->addWhere('anulado = false');
        $this->setOrdenMayor($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //**FIN ORDER BY**/
        //groupBy
        
        //*INICIO LIMIT*//
        if($arrParametros['tipo']=='mayor'){
            $this->_db->addLimit(500);
        }
        //*FIN LIMIT*//
        $this->_db->generarSelect();
        echo $this->_db->getQry();
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
            $array['usuarioNombre'] = $arr[$i]['usuario_nombre_completo'];
            $array['id'] = $arr[$i]['cta_cte_id'];
            $this->_colConsultasMayor[]= $array;
        }
        
    }
    
    public function setOrdenMayor($parametros){
        if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != "")
        {
            $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
        }else
        {
            //definir default
            $this->_ordenColumnas['ordenarPor']    =   'operacion_fecha_hora';
            $this->_ordenColumnas['ordenarOrden']  =   'asc';
        }
    }

    public function getOrdenMayor(){
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

        foreach($this->_colConsultasMayor AS $objConsulta){
                $arr[] = array(
                    //id si o si un solo string (sin espacios en blanco)
                    "id"=>  $objConsulta['id'], //asi cuanta desde 1 en adelante
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
			    $objConsulta['usuarioNombre'],
                ),
            );
        }
       return $arr;
    }
    public function armarDetalles($id){
    
        
    
    }
    public function salvarMe($arrParametros){
    
        $this->_db->addSelect('*');
        $this->_db->addFrom('cta_cte_clientes');
        $this->_db->addFrom('INNER JOIN datos_facturas USING(factura_id)');
        $this->_db->addWhere('cta_cte_clientes.cliente_id = \'' . $arrParametros['cliente'] . '\' ');
        $this->_db->addWhere('factura_nro = \'SALDO INICIO\'');
        $this->_db->generarSelect();
        
        //echo $this->_db->getQry();
        
        $resultado = $this->_db->ejecutar();
    
        if(count($resultado)==0){
        
            $this->_db->addCamposTabla('seq_datos_factura_id');
            $this->_db->generarProximo();

            $id = $this->_db->ejecutar();
            
            $facturaId = $id[0]['nextval'];
        
        
            $this->_db->addCamposTabla('fecha');
            $this->_db->addCamposValue('now()');
            $this->_db->addCamposTabla('cliente_id');
            $this->_db->addCamposValue('\'' . $arrParametros['cliente'] .'\'');
            $this->_db->addCamposTabla('factura_id');
            $this->_db->addCamposValue('\'' . $facturaId .'\'');
            $this->_db->addCamposTabla('importe_deuda');
            $this->_db->addCamposValue('\'' . $arrParametros['saldo'] .'\'');
            $this->_db->addCamposTabla('usuario_id');
            $this->_db->addCamposValue('\'' . $_SESSION['usuarioId'] .'\'');
            $this->_db->addFrom('cta_cte_clientes');
            
            $this->_db->generarInsert();
        
            $arrQry['cta']=$this->_db->getQry();
            
            
            $this->_db->addCamposTabla('factura_id');
            $this->_db->addCamposValue('\'' . $facturaId .'\'');
            $this->_db->addCamposTabla('factura_fecha');
            $this->_db->addCamposValue('now()');
            $this->_db->addCamposTabla('cliente_id');
            $this->_db->addCamposValue('\'' . $arrParametros['cliente'] .'\'');
            $this->_db->addCamposTabla('factura_total_fact');
            $this->_db->addCamposValue('\'' . $arrParametros['saldo'] .'\'');
            $this->_db->addCamposTabla('factura_faltan_fact');
            $this->_db->addCamposValue('\'' . $arrParametros['saldo'] .'\'');
            $this->_db->addCamposTabla('factura_total_iva');
            $this->_db->addCamposValue('0');
            $this->_db->addCamposTabla('factura_nro');
            $this->_db->addCamposValue('\'SALDO INICIO\'');
            $this->_db->addCamposTabla('id_movimiento');
            $this->_db->addCamposValue('0');
            $this->_db->addCamposTabla('factura_usuario_id');
            $this->_db->addCamposValue('\'' . $_SESSION['usuarioId'] .'\'');
            $this->_db->addFrom('datos_facturas');
            
            $this->_db->generarInsert();
        
            $arrQry['factura']=$this->_db->getQry();
            
            $qryFinal = $arrQry['factura'] . $arrQry['cta'];
            
            $this->_db->setQry($qryFinal);
            
            
            $this->_db->ejecutarTransaccion();
            
            
            $arrDevolver = array(
                    "soyError" => false,
                    "nivel" => 1,
                    "mensaje" => "Saldo ingresado"
                    );
            
        }else{
            $arrDevolver = array(
                    "soyError" => true,
                    "nivel" => 1,
                    "mensaje" => "EL cliente ya cuenta con saldo"
                    );            
        }
        echo json_encode($arrDevolver);exit;
    }
}
?>
