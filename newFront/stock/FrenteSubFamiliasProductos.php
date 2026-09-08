    <?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';
/**
 * Description of FrenteStock
 *
 * @author dMELMAC
 */

class FrenteSubFamiliasProductos
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
    
    public function buscar ($arrParametros){
	
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
        $this->_db->addSelect('familia_promocion AS familia_promocion');
        $this->_db->addSelect('\'Si\'  AS familia_promo_si_desc');
        $this->_db->addSelect('\'No\'  AS familia_promo_no_desc');
        $this->_db->addSelect('CASE WHEN familia_promocion THEN \'Si\' ELSE \'No\' END AS familia_promocion_mensaje');
        $this->_db->addFrom('datos_subfamilias');
        
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
            $array['subfamiliaId'] = $arr[$i]['subfamilia_id'];
            $array['subfamiliaNombre'] = $arr[$i]['subfamilia_nombre'];
            $array['observaciones'] = $arr[$i]['subfamilia_observaciones'];
            $array['familia_promo_no_desc']=$arr[$i]['familia_promo_no_desc'];
            $array['familia_promo_si_desc']=$arr[$i]['familia_promo_si_desc'];
            $array['familia_promocion']=$arr[$i]['familia_promocion'];
            $array['familia_promocion_mensaje']=$arr[$i]['familia_promocion_mensaje'];
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
            $this->_ordenColumnas['ordenarPor']    =   'subfamilia_nombre';
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
                        "editable"      =>  true,
                        "detalles"      =>  false,
                        "imprimir"      =>  false
                        ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell" => array(
                                $objConsulta['subfamiliaNombre'],
                                Array
                                (
                                "value" =>$objConsulta['familia_promocion'],
                                "label" =>$objConsulta['familia_promocion_mensaje'],
                                "select" =>
                                    Array
                                    (
                                        Array(
                                        "value" =>true,
                                        "label" =>$objConsulta['familia_promo_si_desc']

                                        ),
                                        Array

                                        (
                                        "value" =>false,
                                        "label" =>$objConsulta['familia_promo_no_desc']

                                        )
                                    )
                                ),
                                $objConsulta['observaciones']
                              ),
            );
            $i++;
        }
       return $arr;
    }
    public function getDetallesAutocompletar(){

        foreach($this->_colSubFamilia AS $familia){
            $arr[]   =   array(
                    'label' =>  $familia['subfamiliaNombre'],
                    'value' => $familia['subfamiliaId']
                    );
        }		
	return $arr;
    }
    public function salvarMe($arrParametros){
    
        if($arrParametros['subFamiliaNombre']!= null){
            $this->_db->addCamposTabla('subfamilia_nombre');
            $this->_db->addCamposValue('\'' . $arrParametros['subFamiliaNombre'] .'\'');
            $this->_db->addCamposTabla('subfamilia_usuario_id');
            $this->_db->addCamposValue('\'' . $_SESSION['usuarioId'] .'\'');
            
        }
        if($arrParametros['observaciones']!= null){
            $this->_db->addCamposTabla('subfamilia_observaciones');
            $this->_db->addCamposValue('\'' . $arrParametros['observaciones'] .'\'');
        }
        $this->_db->addFrom('datos_subfamilias');
	
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
                    "mensaje" => "Subfamilia creada"
                    );
        }  
        echo json_encode($arrDevolver);exit;
    }
    public function cancelarSubFamilia($id){
            
        $this->_db->addCamposUpdate('subfamilia_activa=false');
        $this->_db->addCamposUpdate('subfamilia_inactiva_fecha_hora=now()');
        $this->_db->addCamposUpdate('subfamilia_inactiva_usuario_id =  \'' . $_SESSION['usuarioId'] .'\'');
        
        $this->_db->addFrom('datos_subfamilias');
        $this->_db->addWhere('subfamilia_id = \'' . $id .'\'');
	    
        //generar qry
        
        $this->_db->generarUpdate();
        //echo $this->_db->getQry();exit;
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
                    "mensaje" => "subfamilia cancelada"
                    );
        }  
        echo json_encode($arrDevolver);exit;
    
    
    }
    public function actualizar ($arrParametros){
        if($arrParametros['campos']['familia_promocion'] == 1){
            $this->_db->addCamposUpdate('familia_promocion = true');
        }else{
            $this->_db->addCamposUpdate('familia_promocion = false');
	}
	$this->_db->addCamposUpdate('subfamilia_nombre = \'' . $arrParametros['campos']['subfamilia_nombre'] .'\'');
        $this->_db->addFrom('datos_subfamilias');
        $this->_db->addWhere('subfamilia_id = \'' . $arrParametros['id'] .'\'');

        //generar qry
        $this->_db->generarUpdate();
        //echo $this->_db->getQry();exit;
        //asigamos el resulta que es un array a una variable
        $resultado = $this->_db->ejecutar();

        if(count($resultado)> 0){
            $arrDevolver = array(
                "soyError" => false,
                "nivel" => 0,
                "mensaje" =>"Actualizado"
                );
        }else{

            $arrDevolver = array(
                "soyError" => true,
                "nivel" => 1,
                "mensaje" =>"Error en la actualizacion"
                );
        }
        echo json_encode($arrDevolver);

    }
}
?>
