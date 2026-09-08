<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'DatoModeloExtendido.php';
require_once 'DetalleModeloExtendido.php';

/**
 * Description of Modelo
 * Para menejo de modelo y en forma de frente
 * @author lucas
 */
class FrenteModelo{    
    
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
        //var_dump($parametros);exit;
        $this->_db->addSelect('modelo_id');
        $this->_db->addFrom('frente.datos_modelo');
        $this->_db->addWhere('modelo_nombre = \'' . $parametros[tipo] .'\'' );
        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $arr =  $this->_db->ejecutar();
        $this->cargarModeloCompleto($arr[0]['modelo_id']);
    }
    
    public function cargarModeloCompleto($id){
    
        $this->_db->addSelect('*');
        $this->_db->addFrom('frente.datos_modelo');
        $this->_db->addFrom('INNER JOIN frente.detalle_modelo USING(modelo_id)');
        $this->_db->addWhere("frente.datos_modelo.modelo_id =   $id  " );
        $this->_db->addWhere('modelo_activo = true');
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
                $ids.= $id['modelo_id'] . ",";
            }
            $ids=substr($ids, 0, -1);
        }else{
            $ids=$arrIds;
        }
        //$ids=substr($ids, 0, -1);
        $this->_db->addSelect('*');
        $this->_db->addSelect('\'Activo\'  AS modelo_activo_desc');
        $this->_db->addSelect('\'Inactivo\'  AS modelo_no_activo_desc');
        $this->_db->addSelect('CASE WHEN activo THEN \'Activo\' ELSE \'Inactivo\' END AS modelo_nombre_mensaje');
        $this->_db->addFrom('frente.datos_modelo');
        $this->_db->addWhere("modelo_id IN (" . $ids . ")" );
        
        $this->_db->generarSelect();
        //echo $ids; exit;
        //echo $this->_db->getQry();exit;
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
        $this->_db->addSelect('modelo_id');
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
        if($arrParametros['modelo_id'] !=null && $arrParametros['desdeAlta']==null){
            $this->_db->addWhere('modelo_id = \'' . $arrParametros['modelo_id'] . '\'');
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
        //echo $this->_db->getQry();exit;
        $resultado =  $this->_db->ejecutar();
        
        //*FIN WHERE*//
        //*INICIO LIMIT*//
        if($arrParametros['tipo']=='modelos '){
        //	 $this->_db->addLimit(100);
        }
        //*FIN LIMIT*//
        
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
        
    public function setOrdenModelo($parametros){
        //echo "hola";exit
        if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){

            $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
        }else{
            //definir default
            $this->_ordenColumnas['ordenarPor']    =   'orden_nro';
            $this->_ordenColumnas['ordenarOrden']  =   'asc';
        }
    }
    public function setOrden($col) {
	$this->_ordenColumnas = $col;
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
                    "editable"      =>  true,
                    "detalles"      =>  true,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $modelo->getModeloId(),
                    $modelo->getModeloNombre(),
                    //$modelo->getDetalleModelo()->getDisplay(),
                    $modelo->getUsuarioId(),
                    $modelo->getModeloActivo(),
                    
                    ),
            );
            }
               // var_dump($arr);
            return $arr;
            }   
        
    public function armarDetalles($id){
    
        $this->cargarModeloLazzy(array($id));
        
        foreach($this->_colModelo AS $objModelo){
        
            $propiedadesModelo =   array(
                array(
                    "display"   =>  'Modelo',
                    "name"      =>  'modelo_nombre',
                    "editable"  =>  false,
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
                    "display"   =>  'Modelo activo',
                    "name"      =>  'modelo_activo',
                    "editable"  =>  false,
                    "class"	=>'',
                    "value"     =>  $objModelo->getModeloActivo(),

                )
                );
        }
            
            $array_devolver =   array(
                "editable_cabecera" => true,
                "propiedades"       =>  $propiedadesModelo,
                "listadoDetalles"   =>  null
            );
                return $array_devolver;
            }
                
    public function getDetallesAutocompletar(){
	
	foreach($this->_colModelos AS $modelo){
	 $arr[]   =   array(
            'label' =>  $modelo->getModeloNombre(),
            'value' =>  $modelo->getModeloId()
            );
	}		
	return $arr;
    }    
   
    public function salvarMe($parametros){
        $DatoModeloExtendido = NEW DatoModeloExtendido();
        $DatoModeloExtendido->salvarMe($parametros);        
        //$DatoModeloExtendido->logMe();
        $this->setColModelos($DatoModeloExtendido);
        return $parametros;
    }
        
    public function actualizarMe($arrParametros){	
        $DatoModeloExtendido = NEW DatoModeloExtendido();
        $DatoModeloExtendido->actualizarMe($arrParametros);
        //$modeloExtendido->logMe();
        $this->setColModelos($DatoModeloExtendido);
        return $arrParametros;
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
}
 
