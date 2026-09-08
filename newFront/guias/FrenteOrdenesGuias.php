<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';
/**
 * Description of FrenteStock
 *
 * @author dMELMAC
 */

class FrenteOrdenesGuias
{
    private $_db;
    private $_colOrdenes= Array();
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
    
    public function buscarOrden ($arrParametros){
	
        //**ANALIZO EL WHERE**//
        if($arrParametros['stringBuscar']){
            $this->_db->addWhere('guia_id ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
            $primerRegistro = 0;
            $ultimoRegistro = 11;
	    }else{
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
        }
        $this->_db->addSelect('*, datos_guias.observaciones'); 
        $this->_db->addFrom('datos_guias');
        $this->_db->addFrom('INNER JOIN datos_clientes USING(cliente_id)');
        $this->_db->addFrom('INNER JOIN datos_cuentas USING(cuenta_id)');
        $this->_db->addFrom('INNER JOIN datos_pacientes USING(paciente_id)');
        
        if($arrParametros['pacientes'] !=null ){
            $this->_db->addWhere('paciente_id = \'' . $arrParametros['pacientes'] . '\' ');
        }
        if($arrParametros['clientes'] !=null ){
            $this->_db->addWhere('cliente_id = \'' . $arrParametros['clientes'] . '\' ');
        }
        if($arrParametros['cuentas'] !=null ){
            $this->_db->addWhere('cuenta_id = \'' . $arrParametros['cuentas'] . '\' ');
        }
        if($arrParametros['guiaNro'] !=null ){
            $this->_db->addWhere('guia_id = \'' . $arrParametros['guiaNro'] . '\' ');
        }
        $this->_db->addWhere('guia_cancelada=false');
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
            $array['guiaNro'] = $arr[$i]['guia_id'];
            $array['guiaFecha'] = $arr[$i]['guia_fecha'];
            $array['cuentaNombre'] = $arr[$i]['cuenta_nombre'];
            $array['clienteNombre'] = $arr[$i]['cliente_nombre'];
            $array['pacienteNombre'] = $arr[$i]['paciente_nombre'];
            $array['maxSup'] = $arr[$i]['maxilar_sup'];
            $array['maxInf'] = $arr[$i]['maxilar_inf'];
            $array['botones'] = $arr[$i]['botones'];
            $array['arcos'] = $arr[$i]['arcos'];
            $array['fotos'] = $arr[$i]['fotos'];
            $array['stl'] = $arr[$i]['stl'];
            $array['estudios'] = $arr[$i]['estudios_medicos'];
            $array['observaciones'] = $arr[$i]['observaciones'];
            $array['guiaFinalizada'] = $arr[$i]['guia_finalizada'];
            $array['guia_cancelada'] = $arr[$i]['guia_cancelada'];
            $array['guiaCancelada'] = $arr[$i]['guia_cancelada'];
            $array['guiaEnRemito'] = $arr[$i]['guia_en_remito'];
            $this->_colOrdenes[]= $array;
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
            $this->_ordenColumnas['ordenarPor']    =   'guia_id';
            $this->_ordenColumnas['ordenarOrden']  =   'desc';
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
    
    
        foreach($this->_colOrdenes AS $objConsulta){
            if($objConsulta['guiaFinalizada']==false){
                $edtiar=true;
                $enviar=true;
            }else{
                $edtiar=false;
                $enviar=false;
            }
            if($objConsulta['guiaEnRemito']==false){
                $cancelar=true;
            }else{
                $cancelar=false;
            }
        
        
        
                $arr[] = array(
                    //id si o si un solo string (sin espacios en blanco)
                    "id"=>  $objConsulta['guiaNro'],
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  $cancelar,
                        "editable"      =>  $edtiar,
                        "detalles"      =>  false,
                        "imprimir"      =>  true,
                        "enviarTransaccion"      =>  $enviar,
                        ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell" => array(
                                $objConsulta['guiaNro'],
                                $objConsulta['guiaFecha'],
                                $objConsulta['cuentaNombre'],
                                $objConsulta['clienteNombre'],
                                $objConsulta['pacienteNombre'],
                                $objConsulta['maxSup'],
                                $objConsulta['maxInf'],
                                $objConsulta['botones'],
                                $objConsulta['arcos'],
                                Array
                                (
                                "value" => $objConsulta['fotos'],
                                "label" =>$objConsulta['fotos'],
                                "select" => 
                                    Array 
                                    (
                                        Array(
                                        "value" =>"Si",
                                        "label" =>"Si"

                                        ),
                                        Array

                                        (
                                        "value" =>"No",
                                        "label" =>"No"

                                        ),
                                        Array

                                        (
                                        "value" =>"Pendiente",
                                        "label" =>"Pendiente"

                                        )
                                    )
                                ),
                                Array
                                (
                                "value" => $objConsulta['stl'],
                                "label" =>$objConsulta['stl'],
                                "select" => 
                                    Array 
                                    (
                                        Array(
                                        "value" =>"DIGITAL",
                                        "label" =>"DIGITAL"

                                        ),
                                        Array

                                        (
                                        "value" =>"No",
                                        "label" =>"No"

                                        ),
                                        Array

                                        (
                                        "value" =>"Modelos",
                                        "label" =>"Modelos"

                                        )
                                    )
                                ),
                                Array
                                (
                                "value" => $objConsulta['estudios'],
                                "label" =>$objConsulta['estudios'],
                                "select" => 
                                    Array 
                                    (
                                        Array(
                                        "value" =>"Si",
                                        "label" =>"Si"

                                        ),
                                        Array

                                        (
                                        "value" =>"No",
                                        "label" =>"No"

                                        ),
                                        Array

                                        (
                                        "value" =>"Pendiente",
                                        "label" =>"Pendiente"

                                        )
                                    )
                                ),
                                $objConsulta['observaciones']
                              ),
            );
        }
       return $arr;
    }
    public function getDetallesAutocompletar(){

        foreach($this->_colOrdenes AS $guia){
            $arr[]   =   array(
                    'label' =>  $guia['guiaNro'],
                    'value' => $guia['guiaNro']
                    );
        }		
	return $arr;
    }
    public function salvarMe($arrParametros){
    
        if($arrParametros['relacionId']!= null){
            $this->_db->addCamposTabla('relacion_id');
            $this->_db->addCamposValue('\'' . $arrParametros['relacionId'] .'\'');
            
            $qry="SELECT cliente_id FROM relaciones_clientes_cuentas WHERE relacion_id=$arrParametros[relacionId] AND relacion_activa=true;";
            $this->_db->setQry($qry);
            $resultado = $this->_db->ejecutar();
            $arrParametros['clientes'] = $resultado['0']['cliente_id'];
            
        }
        if($arrParametros['clientes']!= null){
            $this->_db->addCamposTabla('cliente_id');
            $this->_db->addCamposValue('\'' . $arrParametros['clientes'] .'\'');
        }
        if($arrParametros['cuentas']!= null){
            $this->_db->addCamposTabla('cuenta_id');
            $this->_db->addCamposValue('\'' . $arrParametros['cuentas'] .'\'');
        }
        if($arrParametros['pacienteId']!= null){
            $this->_db->addCamposTabla('paciente_id');
            $this->_db->addCamposValue('\'' . $arrParametros['pacienteId'] .'\'');
        }
        if($arrParametros['maxSup']!='0'){
            $this->_db->addCamposTabla('maxilar_sup');
            $this->_db->addCamposValue('\'' . $arrParametros['maxSup'] .'\'');
        }
        if($arrParametros['maxInf']> '0'){
            $this->_db->addCamposTabla('maxilar_inf');
            $this->_db->addCamposValue('\'' . $arrParametros['maxInf'] .'\'');
        }
        if($arrParametros['botones']!= '0'){
            $this->_db->addCamposTabla('botones');
            $this->_db->addCamposValue('\'' . $arrParametros['botones'] .'\'');
        }
        if($arrParametros['arcos']!= '0'){
            $this->_db->addCamposTabla('arcos');
            $this->_db->addCamposValue('\'' . $arrParametros['arcos'] .'\'');
        }
        if($arrParametros['stl']!= '0'){
            $this->_db->addCamposTabla('stl');
            $this->_db->addCamposValue('\'' . $arrParametros['stl'] .'\'');
        }
        if($arrParametros['estudios']!= '0'){
            $this->_db->addCamposTabla('estudios_medicos');
            $this->_db->addCamposValue('\'' . $arrParametros['estudios'] .'\'');
        }
        if($arrParametros['observaciones']!= null){
            $this->_db->addCamposTabla('observaciones');
            $this->_db->addCamposValue('\'' . $arrParametros['observaciones'] .'\'');
        }
        $this->_db->addCamposTabla('guia_usuario_id');
        $this->_db->addCamposValue('\'' . $_SESSION['usuarioId'] .'\'');
        $this->_db->addFrom('datos_guias');
	
        $this->_db->generarInsert();
        //echo $this->_db->getQry();
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
                    "mensaje" => "Guia creada"
                    );
        }  
        echo json_encode($arrDevolver);exit;
    }
    public function cancelarGuia($id){
            
        $this->_db->addCamposUpdate('guia_cancelada=true');
        $this->_db->addCamposUpdate('guia_cancelada_fecha_hora=now()');
        $this->_db->addCamposUpdate('guia_cancelada_usuario_id =  \'' . $_SESSION['usuarioId'] .'\'');
        
        $this->_db->addFrom('datos_guias');
        $this->_db->addWhere('guia_id = \'' . $id .'\'');
	    
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
    function actualizarMe($arrParametros){
    
        $this->_db->addCamposUpdate('fotos = \'' . $arrParametros['campos']['fotos'] .'\'');
        $this->_db->addCamposUpdate('stl = \'' . $arrParametros['campos']['stl'] .'\'');
        $this->_db->addCamposUpdate('estudios_medicos= \'' . $arrParametros['campos']['estudios_medicos'] .'\'');       

        
        $this->_db->addFrom('datos_guias');
        $this->_db->addWhere('guia_id = \'' . $arrParametros['id'] .'\'');
            
        //generar qry
        $this->_db->generarUpdate();   
        //echo $this->_db->getQry();
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
    public function finalizarGuia($id){
            
        $this->_db->addCamposUpdate('guia_finalizada=true');
        $this->_db->addCamposUpdate('guia_finalizada_fecha_hora=now()');
        $this->_db->addCamposUpdate('guia_finalizada_usuario_id =  \'' . $_SESSION['usuarioId'] .'\'');
        
        $this->_db->addFrom('datos_guias');
        $this->_db->addWhere('guia_id = \'' . $id .'\'');
	    
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
                    "mensaje" => "Guia Finalizada"
                    );
        }  
        echo json_encode($arrDevolver);exit;
    
    
    }
    public function guiaEnRemito($id){
        $qry="UPDATE datos_guias SET en_remito=true WHERE guia_id=$id;";
        return $qry;
    }
}
?>
