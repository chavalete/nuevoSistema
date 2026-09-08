<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';
/**
 * Description of FrenteStock
 *
 * @author dMELMAC
 */

class FrenteImputaciones
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
    
    public function buscar ($arrParametros){
	
        //**ANALIZO EL WHERE**//
        if($arrParametros['stringBuscar']){
            $this->_db->addWhere('descripcion ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
            $primerRegistro = 0;
            $ultimoRegistro = 11;
	    }else{
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
        }
        $this->_db->addSelect('*'); 
        $this->_db->addFrom('datos_imputaciones');
        
        $this->setOrden($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //**FIN ORDER BY**/
        //groupBy
        //$this->_db->addGroup('datos_productos.producto_id, lote_id, estanteria_id, almacen_id, sucursal_id, cantidad, valor_stock, costo_stock');
        
        //*INICIO LIMIT*//
        /*if($arrParametros['tipo']=='cheques'){
            $this->_db->addLimit(100);
        }*/
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
            $array['id'] = $arr[$i]['imputacion_id'];
            $array['descripcion'] = $arr[$i]['descripcion'];
            $this->_colImputaciones[]= $array;
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
            $this->_ordenColumnas['ordenarPor']    =   'descripcion';
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
    public function getDetallesAutocompletar(){

        foreach($this->_colImputaciones AS $cheque){
            $arr[]   =   array(
                    'label' =>  $cheque['descripcion'],
                    'value' => $cheque['id']
                    );
        }		
	return $arr;
    }
    public function salvarMe($arrParametros){

    
        if($arrParametros['descripcion']!= null){
            $this->_db->addCamposTabla('descripcion');
            $this->_db->addCamposValue('\'' . $arrParametros['descripcion'] .'\'');
        }
        
        $this->_db->addCamposTabla('imputacion_fecha_hora');
        $this->_db->addCamposValue('now()');
        
        $this->_db->addCamposTabla('imputacion_usuario_id');
        $this->_db->addCamposValue($_SESSION['usuarioId']);
	
        $this->_db->addFrom('datos_imputaciones');
	
        $this->_db->generarInsert();
        
        
        $resultado =  $this->_db->ejecutar();
        if($resultado==1){
            $arrDevolver = array(
                "soyError" => false,
                "nivel" => 0,
                "mensaje" =>"Creada"
                );
        }else{

            $arrDevolver = array(
                "soyError" => true,
                "nivel" => 1,
                "mensaje" =>"Error" 
                );
        }
        echo json_encode($arrDevolver);exit;
        
    
    }
}
?>
