    <?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';
/**
 * Description of FrenteStock
 *
 * @author dMELMAC
 */

class FrenteSubFamiliaProductos
{
    private $_db;
    private $_colSubFamilia = Array();
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
    
    public function buscarFamilia ($arrParametros){
	
        //**ANALIZO EL WHERE**//
        if($arrParametros['stringBuscar']){
            $this->_db->addWhere('subfamilia_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
            $primerRegistro = 0;
            $ultimoRegistro = 11;
	    }else{
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
        }
        $this->_db->addSelect('*'); 
        $this->_db->addFrom('relaciones_familias_subfamilias');
        $this->_db->addFrom('INNER JOIN tipos_familia_productos USING(familia_id)');
        $this->_db->addFrom('INNER JOIN datos_subfamilias USING(subfamilia_id)');
        $this->_db->addFrom('INNER JOIN datos_productos USING(producto_id)');
        
        if($arrParametros['subfamilias'] !=null ){
            $this->_db->addWhere('subfamilia_id = \'' . $arrParametros['subfamilias'] . '\' ');
        }
        $this->_db->addWhere('subfamilia_activa =true');
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
            $array['producto'] = $arr[$i]['producto_nombre'] . " - " . $arr[$i]['producto_presentacion'];
            $array['subfamiliaNombre'] = $arr[$i]['subfamilia_nombre'];
            $array['familiaNombre'] = $arr[$i]['familia_nombre'];
            $array['observaciones'] = $arr[$i]['observaciones'];
            $this->_colSubFamilia[]= $array;
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
        foreach($this->_colSubFamilia AS $objConsulta){
                $arr[] = array(
                    //id si o si un solo string (sin espacios en blanco)
                    "id"=>  $objConsulta['subfamiliaId'],
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  true,
                        "editable"      =>  false,
                        "detalles"      =>  false,
                        "imprimir"      =>  false
                        ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell" => array(
                                $objConsulta['producto'],
                                $objConsulta['SubfamiliaNombre'],
                                $objConsulta['familiaNombre'],
                                $objConsulta['observaciones']
                              ),
            );
            $i++;
        }
       return $arr;
    }
    public function getDetallesAutocompletar(){

        foreach($this->_colFamilia AS $familia){
            $arr[]   =   array(
                    'label' =>  $familia['subfamiliaNombre'],
                    'value' => $familia['subfamiliaId']
                    );
        }		
	return $arr;
    }
    public function salvarMe($arrParametros){
    
        if($arrParametros['subfamiliaNombre']!= null){
            $this->_db->addCamposTabla('subfamilia_nombre');
            $this->_db->addCamposValue('\'' . $arrParametros['subfamiliaNombre'] .'\'');
            $this->_db->addCamposTabla('subfamilia_usuario_id');
            $this->_db->addCamposValue('\'' . $_SESSION['usuarioId'] .'\'');
            
        }
        if($arrParametros['familiaNombre']!= null){
            $this->_db->addCamposTabla('familia_id');
            $this->_db->addCamposValue('\'' . $arrParametros['familiaN'] .'\'');
        }
        if($arrParametros['observaciones']!= null){
            $this->_db->addCamposTabla('observaciones');
            $this->_db->addCamposValue('\'' . $arrParametros['observaciones'] .'\'');
        }
        $this->_db->addFrom('tipos_subfamilia_productos');
	
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
                    "mensaje" => "Familia creada"
                    );
        }  
        echo json_encode($arrDevolver);exit;
    }
    public function cancelarFamilia($id){
            
        $this->_db->addCamposUpdate('familia_activa=false');
        $this->_db->addCamposUpdate('familia_inactiva_fecha_hora=now()');
        $this->_db->addCamposUpdate('familia_inactiva_usuario_id =  \'' . $_SESSION['usuarioId'] .'\'');
        
        $this->_db->addFrom('tipos_familia_productos');
        $this->_db->addWhere('familia_id = \'' . $id .'\'');
	    
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
                    "mensaje" => "Familia cancelada"
                    );
        }  
        echo json_encode($arrDevolver);exit;
    
    
    }
}
?>
