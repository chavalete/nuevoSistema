<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';
/**
 * Description of FrenteStock
 *
 * @author dMELMAC
 */

class FrenteTotales
{
    private $_db;
    private $_colTotales = Array();
    private $_objFuncionesComunes;
    
    
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
    
    public function buscarTotales ($arrParametros){
	
        //**ANALIZO EL WHERE**//
        if($arrParametros['stringBuscar']){
            $this->_db->addWhere('cuenta_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
            $primerRegistro = 0;
            $ultimoRegistro = 11;
	    }else{
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
        }
        $qry="SELECT lista_id FROM datos_listas_precios WHERE lista_mayorista=true AND lista_cancelada=false;";
        $this->_db->setQry($qry);
        $resultadoListaId=$this->_db->ejecutar();
        $id=$resultadoListaId[0][lista_id];
        $qry="SELECT sum(ls.producto_pventa * cantidad) AS total_venta, sum(producto_pcosto_dolar*cantidad) AS total_costo FROM stock_completo INNER JOIN datos_productos USING(producto_id) INNER JOIN datos_tipos_productos USING(tipo_producto_id) INNER JOIN lista_precios_$id ls USING(producto_id) WHERE cantidad>0 AND excluir_reporte_stock=false;";
	    
	    $this->_db->setQry($qry);
        $resultadoTotales=$this->_db->ejecutar();
        
        
        $this->_db->addSelect('sum(importe_deuda) AS importe_deuda'); 
        $this->_db->addFrom('vw_deuda_producto_ortodontia');
        
        $this->setOrden($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        
        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $resultadoDeuda =  $this->_db->ejecutar();

        $this->setTotal(count($arr));
        $this->setPagina($arrParametros['pagina']);

        if($this->getTotal() < $ultimoRegistro){
            $ultimoRegistro = $this->getTotal();
        }
        
        
            $array['totalVenta'] = $resultadoTotales[0]['total_venta'];
            $array['totalCosto'] = $resultadoTotales[0]['total_costo'];
            $array['totalDeuda'] = $resultadoDeuda[0]['importe_deuda'];
            $this->_colTotales[]= $array;
        
        
    }
    
    public function setOrden($parametros){
        if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != "")
        {
            $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
        }else
        {
            //definir default
            $this->_ordenColumnas['ordenarPor']    =   'importe_deuda';
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
        foreach($this->_colTotales AS $objConsulta){
                $arr[] = array(
                    //id si o si un solo string (sin espacios en blanco)
                    "id"=>  $objConsulta['cuentaId'],
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  false,
                        "editable"      =>  false,
                        "detalles"      =>  false,
                        "imprimir"      =>  true
                        ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell" => array(
                                $this->_objFuncionesComunes->formatoMoneda($objConsulta['totalDeuda']),
                                $this->_objFuncionesComunes->formatoMoneda($objConsulta['totalVenta']),
                                $this->_objFuncionesComunes->formatoMonedaExtranjera($objConsulta['totalCosto'])
                              ),
            );
            $i++;
        }
       return $arr;
    }
    public function getDetallesAutocompletar(){

        foreach($this->_colCuentas AS $cuenta){
            $arr[]   =   array(
                    'label' =>  $cuenta['cuentaNombre'],
                    'value' => $cuenta['cuentaId']
                    );
        }		
	return $arr;
    }
    public function salvarMe($arrParametros){
    
        if($arrParametros['cuentaNombre']!= null){
            $this->_db->addCamposTabla('cuenta_nombre');
            $this->_db->addCamposValue('\'' . $arrParametros['cuentaNombre'] .'\'');
            $this->_db->addCamposTabla('cuenta_usuario_id');
            $this->_db->addCamposValue('\'' . $_SESSION['usuarioId'] .'\'');
            
        }
        if($arrParametros['observaciones']!= null){
            $this->_db->addCamposTabla('observaciones');
            $this->_db->addCamposValue('\'' . $arrParametros['observaciones'] .'\'');
        }
        $this->_db->addFrom('datos_cuentas');
	
        $this->_db->generarInsert();
        //echo $this->_db->getQry();exit;
        $this->_db->ejecutar();
        if(count($this->_db->getArrError()) > 0){
            $arrDevolver = array(
                    "soyError" => true,
                    "nivel" => 1,
                    "alertados" => $arrAlertador,
                    "mensaje" => "Error en la carga!!!!"
                    );
        }else{
            $arrDevolver = array(
                    "soyError" => false,
                    "nivel" => 1,
                    "alertados" => $arrAlertador,
                    "mensaje" => "Cuenta creada"
                    );
        }  
        echo json_encode($arrDevolver);exit;
    }
    public function cancelarCuenta($id){
            
        $this->_db->addCamposUpdate('cuenta_activa=false');
        $this->_db->addCamposUpdate('cuenta_inactiva_fecha_hora=now()');
        $this->_db->addCamposUpdate('cuenta_inactiva_usuario_id =  \'' . $_SESSION['usuarioId'] .'\'');
        
        $this->_db->addFrom('datos_cuentas');
        $this->_db->addWhere('cuenta_id = \'' . $id .'\'');
	    
        //generar qry
        
        $this->_db->generarUpdate();
        $resultado = $this->_db->ejecutar();
        if(count($resultado)== 0){
            $arrDevolver = array(
                    "soyError" => true,
                    "nivel" => 1,
                    "alertados" => $arrAlertador,
                    "mensaje" => "Error en cancelacion!!!!"
                    );
        }else{
            $arrDevolver = array(
                    "soyError" => false,
                    "nivel" => 1,
                    "alertados" => $arrAlertador,
                    "mensaje" => "Cuenta cancelada"
                    );
        }  
        echo json_encode($arrDevolver);exit;
    
    
    }
}
?>
