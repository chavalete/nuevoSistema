<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';
/**
 * Description of FrenteStock
 *
 * @author dMELMAC
 */

class FrenteClientesCuentas
{
    private $_db;
    private $_colRelaciones= Array();
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
    
    public function buscarRelaciones ($arrParametros){
	
        //**ANALIZO EL WHERE**//
        if($arrParametros['stringBuscar']){
            $this->_db->addWhere('relacion_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
            $primerRegistro = 0;
            $ultimoRegistro = 11;
	    }else{
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
        }
        $this->_db->addSelect('relacion_id,relacion_nombre, cliente_nombre, cuenta_nombre, relaciones_clientes_cuentas.observaciones, vendedor_nombre'); 
        $this->_db->addFrom('relaciones_clientes_cuentas');
        $this->_db->addFrom('INNER JOIN datos_clientes USING(cliente_id)');
        $this->_db->addFrom('INNER JOIN datos_cuentas USING(cuenta_id)');
        $this->_db->addFrom('INNER JOIN datos_vendedores ON relaciones_clientes_cuentas.vendedor_id = datos_vendedores.vendedor_id');
        
        if($arrParametros['relaciones'] !=null ){
            $this->_db->addWhere('relacion_id = \'' . $arrParametros['relaciones'] . '\' ');
        }
        if($arrParametros['clientes'] !=null ){
            $this->_db->addWhere('cliente_id = \'' . $arrParametros['clientes'] . '\' ');
        }
        if($arrParametros['cuentas'] !=null ){
            $this->_db->addWhere('cuenta_id = \'' . $arrParametros['cuentas'] . '\' ');
        }
        $this->_db->addWhere('relacion_activa=true');
        $this->setOrden($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //**FIN ORDER BY**/
        //groupBy
        //$this->_db->addGroup('datos_productos.producto_id, lote_id, estanteria_id, almacen_id, sucursal_id, cantidad, valor_stock, costo_stock');
        
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
            $array['relacionId'] = $arr[$i]['relacion_id'];
            $array['relacionNombre'] = $arr[$i]['relacion_nombre'];
            $array['clienteNombre'] = $arr[$i]['cliente_nombre'];
            $array['cuentaNombre'] = $arr[$i]['cuenta_nombre'];
            $array['vendedorNombre'] = $arr[$i]['vendedor_nombre'];
            $array['observaciones'] = $arr[$i]['observaciones'];
            $this->_colRelaciones[]= $array;
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
            $this->_ordenColumnas['ordenarPor']    =   'cliente_nombre';
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
    
        foreach($this->_colRelaciones AS $objConsulta){
                $arr[] = array(
                    //id si o si un solo string (sin espacios en blanco)
                    "id"=>  $objConsulta['relacionId'],
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  true,
                        "editable"      =>  true,
                        "detalles"      =>  false,
                        "imprimir"      =>  false
                        ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell" => array(
                                $objConsulta['relacionNombre'],
                                $objConsulta['clienteNombre'],
                                $objConsulta['cuentaNombre'],
                                $objConsulta['vendedorNombre'],
                                $objConsulta['observaciones']
                              ),
            );
            $i++;
        }
       return $arr;
    }
    public function getDetallesAutocompletar(){

        foreach($this->_colRelaciones AS $relacion){
            $arr[]   =   array(
                    'label' =>  $relacion['relacionNombre'],
                    'value' => $relacion['relacionId']
                    );
        }		
	return $arr;
    }
    public function salvarMe($arrParametros){
    
        if($arrParametros['relacionNombre']!= null){
            $this->_db->addCamposTabla('relacion_nombre');
            $this->_db->addCamposValue('\'' . $arrParametros['relacionNombre'] .'\'');
            $this->_db->addCamposTabla('relacion_usuario_id');
            $this->_db->addCamposValue('\'' . $_SESSION['usuarioId'] .'\'');
            
        }
        if($arrParametros['clientes']!= null){
            $this->_db->addCamposTabla('cliente_id');
            $this->_db->addCamposValue('\'' . $arrParametros['clientes'] .'\'');
        }
        if($arrParametros['cuentas']!= null){
            $this->_db->addCamposTabla('cuenta_id');
            $this->_db->addCamposValue('\'' . $arrParametros['cuentas'] .'\'');
        }
        if($arrParametros['vendedores']!= null){
            $this->_db->addCamposTabla('vendedor_id');
            $this->_db->addCamposValue('\'' . $arrParametros['vendedores'] .'\'');
        }
        if($arrParametros['observaciones']!= null){
            $this->_db->addCamposTabla('observaciones');
            $this->_db->addCamposValue('\'' . $arrParametros['observaciones'] .'\'');
        }
        $this->_db->addFrom('relaciones_clientes_cuentas');
	
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
                    "mensaje" => "Relacion creada"
                    );
        }  
        echo json_encode($arrDevolver);exit;
    }
    public function cancelarRelacion($id){
            
        $this->_db->addCamposUpdate('relacion_activa=false');
        $this->_db->addCamposUpdate('relacion_inactiva_fecha_hora=now()');
        $this->_db->addCamposUpdate('relacion_inactiva_usuario_id =  \'' . $_SESSION['usuarioId'] .'\'');
        
        $this->_db->addFrom('relaciones_clientes_cuentas');
        $this->_db->addWhere('relacion_id = \'' . $id .'\'');
	    
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
    public function actualizarRelacion($arrParametros){
    
        $this->_db->addCamposUpdate('vendedor_id =  \'' . $arrParametros['campos']['vendedores'] .'\'');
        
        $this->_db->addFrom('relaciones_clientes_cuentas');
        $this->_db->addWhere('relacion_id = \'' . $arrParametros['id'] .'\'');
	    
        //generar qry
        
        $this->_db->generarUpdate();
        echo $this->_db->getQry();
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
