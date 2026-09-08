<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/trazabilidad/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'DatoModeloExtendido.php';
require_once 'DetalleModeloExtendido.php';

/**
 * Description of Modelo
 * Para menejo de modelo y en forma de frente
 * @author lucas
 */
class FrenteModeloAdmin{    
    
    private $_colModelo= Array();
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    
    public function __construct(){
        $this->_db = NEW FrenteAlmacenamiento('dmelmac');
        //$this->_objFuncionesComunes = new FuncionesComunes();
    }
    
    public function setColModelos($objModelo){
        $this->_colModelo[$objModelo->getModeloId()] = $objModelo;        
    }
    public function getColModelos(){
        return $this->_colModelo;
    }
    public function cargarModelo($id){
        $this->_colModelo[$id] = NEW DatoModeloExtendido();
        $this->_colModelo[$id]->cargarColumnasModelo($id);
    }
    public function buscarModeloPorNombre($parametros){
        $this->_db->addSelect('*');
        //$this->_db->addSelect('modelo_id');
        $this->_db->addFrom('frente.datos_modelo');
        //$this->_db->addWhere('modelo_nombre = \'' . $parametros[tipo] .'\'' );
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $arr =  $this->_db->ejecutar();
        $this->cargarModeloCompleto($arr[0]['modelo_id']);
    }
    public function cargarModeloCompleto($id){
        $this->_db->addSelect('*');
        $this->_db->addFrom('frente.datos_modelo');
        $this->_db->addFrom('INNER JOIN frente.detalle_modelo USING(modelo_id)');
        $this->_db->addWhere("frente.datos_modelo.modelo_id =   $id  " );
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado=$this->_db->ejecutar();
        $this->cargarDatoModeloLazzy($resultado[0][modelo_id]);
        $this->cargarDetallesModeloLazzy($resultado);
    }
    
     public function cargarDatoModeloLazzy($arrIds){
        //echo "llega";
        //var_dump($arrIds);exit;
        if(is_array($arrIds)){
            foreach($arrIds as $id){
                if($id['modelo_id']!=null){
                    $ids.= $id['modelo_id'] . ",";
                }    
            }
            //echo $ids;exit;
            $ids=substr($ids, 0, -1);
        }else{
            $ids=$arrIds;
        }
        //$ids=substr($ids, 0, -1);
        $this->_db->addSelect('*');
        $this->_db->addSelect('to_char(fecha_alta,\'DD-MM-YYYY\') AS fecha_alta');
        $this->_db->addSelect('\'Activo\'  AS modelo_activo_desc');
        $this->_db->addSelect('\'Inactivo\'  AS modelo_no_activo_desc');
        $this->_db->addSelect('CASE WHEN activo THEN \'Activo\' ELSE \'Inactivo\' END AS modelo_activo_mensaje');
        $this->_db->addFrom('frente.datos_modelo');
        $this->_db->addWhere("modelo_id IN (" . $ids . ")" );
        //$this->_db->addWhere('modelo_activo = true');
        $this->_db->generarSelect();
        //echo $ids; exit;
        //echo $this->_db->getQry();//exit;
        $resultado = $this->_db->ejecutar();
        foreach($resultado   AS $detalle){
            $objModelo = NEW DatoModeloExtendido();
            $objModelo->cargarMe($detalle);
            //echo $objModelo->getModeloNombre();exit;
            $this->setColModelos($objModelo);
        }
    }
    public function cargarDetallesModeloLazzy($arrIds){
    //echo "entra";exit;
        foreach($arrIds as $id){
            $ids.= $id['detalle_modelo_id'] . ",";
        }
        $ids=substr($ids, 0, -1);
        $this->_db->addSelect('*');       
        $this->_db->addSelect('CASE WHEN sortable THEN \'Si\' ELSE \'No\' END AS sortable_mensaje');
        $this->_db->addSelect('CASE WHEN required THEN \'Si\' ELSE \'No\' END AS required_mensaje');
        $this->_db->addSelect('CASE WHEN editable THEN \'Si\' ELSE \'No\' END AS editable_mensaje');
        $this->_db->addSelect('CASE WHEN modelo_activo THEN \'Si\' ELSE \'No\' END AS modelo_activo_mensaje');
        $this->_db->addFrom('frente.detalle_modelo');
        $this->_db->addWhere("detalle_modelo_id IN (" . $ids . ")" );
        $this->_db->AddOrderBy('orden_nro asc');
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        foreach($this->getColModelos() AS $objModelo){
        //echo $objModelo->getModeloNombre();exit;
            foreach($resultado AS $detalle){
                $objDetalleModelo= NEW DetalleModeloExtendido();
                $objDetalleModelo->cargarMe($detalle);
                //echo $objDetalleModelo->getName();exit;
                $this->_colModelo[$objModelo->getModeloId()]->setDetalleModelo($objDetalleModelo);
            }
        }
    }
    
    
    public function buscarModelo($arrParametros){
        $this->_db->addSelect('*');
        $this->_db->addFrom('frente.datos_modelo');
        if($arrParametros['stringBuscar'])	{
            $this->_db->addWhere('modelo_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
            $primerRegistro = 0;
            $ultimoRegistro = 11;
        }else{  
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
        }
        if($arrParametros['modelo_nombre'] !=null && $arrParametros['desdeAlta']==null){
            $this->_db->addWhere('modelo_nombre = \'' . $arrParametros['modelo_nombre'] . '\'');
        }
        if($arrParametros['modelos'] !=null && $arrParametros['desdeAlta']==null){
            $this->_db->addWhere('modelo_id = \'' . $arrParametros['modelos'] . '\'');
        }
        if($arrParametros['usuario_id'] !=null && $arrParametros['desdeAlta']==null){
            $this->_db->addWhere('usuario_id = \'' . $arrParametros['usuario_id'] . '\'');
        }
        //**INICIO ORDER BY**//
        //*seteo el orden*/
        $this->setOrden($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //**FIN ORDER BY**//
        //**INICIO WHERE**//
        $this->_db->generarSelect();
       //echo $this->_db->getQry();//exit;
        $resultado =  $this->_db->ejecutar();
        //*FIN WHERE*//
        //*INICIO LIMIT*//
        if($arrParametros['tipo']=='modelos '){
        //	 $this->_db->addLimit(100);
        }
        //*FIN LIMIT*//
        	$this->_db->generarSelect();

        //ahora seteamos el total y la pagina
        $this->setTotal(count($resultado));
            if($this->getTotal() == 0){
                return ;
            }
        $this->setPagina($arrParametros['pagina']);
        
        if($this->getTotal() < $ultimoRegistro){
            $ultimoRegistro = $this->getTotal();
        }	
        //var_dump($resultado);exit;
        for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
            $arrIds[$i]['modelo_id'] = $resultado[$i]['modelo_id'];
        }
        $this->cargarDatoModeloLazzy($arrIds);
        }
        
    public function setOrden($parametros){
        //echo "hola";exit
        if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){
            $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
        }else{
            //definir default
            $this->_ordenColumnas['ordenarPor']    =   'modelo_nombre';
            $this->_ordenColumnas['ordenarOrden']  =   'asc';
        }
    }

    public function getOrden(){
	return $this->_ordenColumnas;
    }
    public function setTotal($total) {
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
        
	foreach($this->getColModelos() AS $modelo){
	
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $modelo->getModeloId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  false,
                    "detalles"      =>  true,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $modelo->getModeloId(),
                    $modelo->getModeloNombre(),
                    $modelo->getModeloActivoMensaje(),
                    $modelo->getObservaciones(),
                    
                    ),
            );
            }
               // var_dump($arr);
            return $arr;
            }   
        
    public function armarDetalles($id){
    //llamar al metodo q cargar el modelo completo
    $this->cargarModeloCompleto($id);
        foreach($this->_colModelo AS $objModelo){
            $propiedadesModelo =   array(
                    array(
                        "display"   =>  'Modelo',
                        "name"      =>  'modelo_nombre',
                        "editable"  =>  true,
                        "class"	=>'',
                        "value"     =>  $objModelo->getModeloNombre(),
                    ),
                    array(
                        "display"   =>  'Fecha alta',
                        "name"      =>  'fecha_alta',
                        "editable"  =>  false,
                        "class"	=>'',
                        "value"     =>  $objModelo->getFechaAlta(),
                    ),
                    array(
                        "display"   =>  'Activo',
                        "name"      =>  'modelo_activo',
                        "editable"  =>  false,
                        "class"	=>'',
                        "value"     =>  $objModelo->getModeloActivoMensaje(),
                    ),
                    array(
                        "display"   =>  'Observaciones',
                        "name"      =>  'observaciones',
                        "editable"  =>  true,
                        "class"	=>'',
                        "value"     =>  $objModelo->getObservaciones(),
                    )
                );
        }
        
        foreach($this->_colModelo AS $objModelo){
            foreach ($objModelo->getDetalleModelo() AS $objDetalleModelo){
        
                $arr []   =    array
                    (
                    //id si o si un solo string (sin espacios en blanco)
                    "id"            =>  $objDetalleModelo->getDetalleModeloId(),
                    //define las herramientas
                    "herramientas"   =>  array(
                    "editable"      =>  true,
                    ),
                    "bloqueado"     => false,	    
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell"          => array(
                                $objDetalleModelo->getDisplay(),
                                $objDetalleModelo->getName(),
                                $objDetalleModelo->getAlign(),
                                $objDetalleModelo->getOrdenNro(),                               
                                $objDetalleModelo->getSortableMensaje(),
                                $objDetalleModelo->getClass(),
                                $objDetalleModelo->getRequiredMensaje(),
                                $objDetalleModelo->getEditableMensaje(),
                                $objDetalleModelo->getModeloActivoMensaje(),
                                ),
                    );
            }
        }
            
            $detallesModelo    =   array(
                "bloqueable"=> false,
                "modelo"    =>  array(
                        array(

                    "display"   =>  'Display',
                    "name"      =>  'display',
                    "editable"  =>  true,
                    "class"	=>'',
                ),
                array(

                    "display"   =>  'Name',
                    "name"      =>  'name',
                    "editable"  =>  true,
                    "class"	=>'',
                    
                ),
                array(

                    "display"   =>  'Align',
                    "name"      =>  'align',
                    "editable"  =>  false,
                    "class"	=>'',
                    
                ),
                array(
                    "display"   =>  'Orden Número',
                    "name"      =>  'orden_nro',
                    "editable"  =>  true,
                    "class"	=>'',
                ),
                array(

                    "display"   =>  'Sortable',
                    "name"      =>  'sortable',
                    "editable"  =>  true,
                    "class"	=>'',
                ),
                        array(

                    "display"   =>  'Class',
                    "name"      =>  'class',
                    "editable"  =>  false,
                    "class"	=>'',
                ),
                        array(

                    "display"   =>  'Required',
                    "name"      =>  'required',
                    "editable"  =>  true,
                    "class"	=>'',
                ),
                        array(

                    "display"   =>  'Editable',
                    "name"      =>  'editable',
                    "editable"  =>  true,
                    "class"	=>'',
                ),
                        array(

                    "display"   =>  'Modelo activo',
                    "name"      =>  'modelo_activo',
                    "editable"  =>  true,
                    "class"	=>'',
                ),
                array(
                    "display"   =>  'Herr.',
                    "name"      =>  'herramienta',
                ),
            ),
                
                "celdas"    =>  $arr
            );
                        
            $array_devolver =   array(
                "editable_cabecera" => true,
                "propiedades"       =>  $propiedadesModelo,
                "listadoDetalles"   =>  $detallesModelo
            );
                return $array_devolver;
            }
                
    public function getDetallesAutocompletar(){
    //var_dump($this->_colModelo);
    foreach($this->_colModelo AS $modelo){
    $arr[]   =   array(
            'label' =>  $modelo->getModeloNombre(),
            'value' =>  $modelo->getModeloId()
            );
    }
    //var_dump($arr);
    return $arr;
    }

    public function getDetallesAutocompletarSubseccion(){
    //var_dump($this->_colModelo);
    foreach($this->_colModelo AS $modelo){
    $arr[]   =   array(
            'label' =>  $modelo->getModeloNombre(),
            'value' =>  $modelo->getModeloNombre()
            );
    }
    //var_dump($arr);
    return $arr;
    }    
   
    public function salvarMe($parametros){
        //var_dump($parametros);exit;
        if($parametros['modelos'] != null && $parametros['modeloNuevo']  != null){
        $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'No se puede guardar un modelo y un modelo nuevo'
		    );
        echo json_encode($arrDevolver);exit;
        }
        if($parametros['modelos'] == null && $parametros['modeloNuevo']  == null){
        $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Debe ingresar un modelo o un modelo nuevo'
		    );
        echo json_encode($arrDevolver);exit;
        }
        if (is_numeric($parametros['modelos'])){
            $parametros['modelo_id']= $parametros['modelos'];  
        }elseif($parametros['modeloNuevo']!=null){
            $parametros['modelo_id'] = $this->getSeqModeloId();
            $parametros['usuario_id'] = $_SESSION['usuarioId'];
            $parametros['modelo_nombre'] = $parametros['modeloNuevo'];
        }
        $arrLineaDetalle = explode("||",$parametros['detalles']); 
        array_pop($arrLineaDetalle);
        foreach($arrLineaDetalle as $linea){
            //var_dump($linea);exit;
            $arrLinea = explode("#",$linea);
            list($parametros['display'],$parametros['name'],$parametros['orden_nro'],$parametros['class']) = $arrLinea;
            //var_dump($parametros);exit;
            $DetalleModeloExtendido = NEW DetalleModeloExtendido();
            $rs = $DetalleModeloExtendido->salvarMe($parametros);
            
        }
        if($parametros['modeloNuevo']!= null){ 
            
            $DatoModeloExtendido = NEW DatoModeloExtendido();
            $rs = $DatoModeloExtendido->salvarMe($parametros);
        }
            if($rs == 1){
                $arrDevolver = array(
                "soyError" => false,
                "nivel" => 0,
                "mensaje" =>'Modelos gravados correctamente'
                );
            }else{
                $arrDevolver = array(
                "soyError" => true,
                "nivel" => 1,
                "mensaje" =>'Error al guardar el modelo'
                );
                //var_dump($arrLineaDetalle);exit;
            }
            echo json_encode($arrDevolver);exit;
            //var_dump($arrLineaDetalle);exit;
            //$DatoModeloExtendido->logMe();
            $this->setColModelos($DatoModeloExtendido);
            return $parametros;
        }
    public function actualizarMe($arrParametros){	
        //var_dump($arrParametros);exit;
        $DatoModeloExtendido = NEW DatoModeloExtendido();
        $rs = $DatoModeloExtendido->actualizarMe($arrParametros);
        //var_dump($rs);exit;
        if($rs == 1){
            $arrDevolver = array(
            "soyError" => false,
            "nivel" => 0,
            "mensaje" =>'Modelo actualizado correctamente'
            );
        }else{
            $arrDevolver = array(
            "soyError" => true,
            "nivel" => 1,
            "mensaje" =>'Error al actualizar'
            );
        }
        echo json_encode($arrDevolver);//exit;
    }
    //funcion q se llame actualizaDetalles recibe parametros
    //llama al objeto DetalleModeloExtendido y ejecutar la funcion acutalizarme
    public function actualizarDetalles($parametros){
        $DetalleModeloExtendido = NEW DetalleModeloExtendido();
        $DetalleModeloExtendido->actualizarMe($parametros);
    }
    
    public function getDetallesModelo(){
	foreach($this->_colModelo AS $modelo)
    {
	    foreach ($modelo->getDetalleModelo() AS $detalle)
	    {
		    $arr[] = $detalle->getContenedor();
	    }
	}
	return $arr;
    }
    
    public function getSeqModeloId(){
    $this->_db->addCamposTabla('seq_datos_modelo_modelo_id');
	$this->_db->generarProximo();
	$id = $this->_db->ejecutar();
	//echo $this->_db->getQry();exit;
    return $id[0]['nextval'];
    
    }
}
 

