<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';
/**
 * Description of FrenteStock
 *
 * @author dMELMAC
 */

class FrenteFormasPagos
{
    private $_db;
    private $_colFormas = Array();
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
    
    public function buscarFormas ($arrParametros){
	
        //**ANALIZO EL WHERE**//
        if($arrParametros['stringBuscar']){
            $this->_db->addWhere('forma_desc ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
            $primerRegistro = 0;
            $ultimoRegistro = 11;
	    }else{
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
        }
        $this->_db->addSelect('*'); 
        $this->_db->addFrom('datos_formas_pagos');
        
        if($arrParametros['bancos'] !=null ){
            $this->_db->addWhere('datos_productos.producto_id = \'' . $arrParametros['productos'] . '\' ');
        }
        $this->setOrdenBancos($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //**FIN ORDER BY**/
        //groupBy
        //$this->_db->addGroup('datos_productos.producto_id, lote_id, estanteria_id, almacen_id, sucursal_id, cantidad, valor_stock, costo_stock');
        
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
            $array['formaId'] = $arr[$i]['forma_pago_id'];
            $array['formaDesc'] = $arr[$i]['forma_desc'];
            $this->_colFormas[]= $array;
        }
        
    }
    
    public function setOrdenBancos($parametros){
        if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != "")
        {
            $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
        }else
        {
            //definir default
            $this->_ordenColumnas['ordenarPor']    =   'forma_desc';
            $this->_ordenColumnas['ordenarOrden']  =   'asc';
        }
    }
    public function getOrdenBancos(){
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
        foreach($this->_colFormas AS $objConsulta){
                $arr[] = array(
                    //id si o si un solo string (sin espacios en blanco)
                    "id"=>  $objConsulta['bancoId'],
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  false,
                        "editable"      =>  false,
                        "detalles"      =>  false,
                        "imprimir"      =>  false
                        ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell" => array(
                                $objConsulta['bancoNombre']
                              ),
            );
            $i++;
        }
       return $arr;
    }
    public function getDetallesAutocompletar(){

        foreach($this->_colFormas AS $forma){
            $arr[]   =   array(
                    'label' =>  $forma['formaDesc'],
                    'value' => $forma['formaId']
                    );
        }		
	return $arr;
    }
    public function salvarMe($arrParametros){
    
        if($arrParametros['bancoNombre']!= null){
            $this->_db->addCamposTabla('banco_nombre');
            $this->_db->addCamposValue('\'' . $arrParametros['bancoNombre'] .'\'');
        }
        $this->_db->addFrom('datos_bancos');
	
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
                    "mensaje" => "Banco creado"
                    );
        }  
        echo json_encode($arrDevolver);exit;
    }
}
?>
