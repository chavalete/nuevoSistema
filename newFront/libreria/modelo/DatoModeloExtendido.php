<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/trazabilidad/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/trazabilidad/libreria/modelo/DatoModelo.php';



class DatoModeloExtendido extends DatoModelo{
    
    private $_db;
    private $_modelo;
    private $_modeloActivoDesc;
    private $_modeloNoActivoDesc;
    private $_modeloActivoMensaje;
    //private $_usuarioPrivilegiadoMensaje;
    
    public function __construct(){
        $this->_db = new FrenteAlmacenamiento();
    }
    public function setModeloActivoDesc($ActivoDesc){
        $this->_modeloActivoDesc = $ActivoDesc;
    }
    public function getModeloActivoDesc(){
        return $this->_modeloActivoDesc;
    }
    public function setModeloNoActivoDesc($NoActivoDesc){
        $this->_modeloNoActivoDesc = $NoActivoDesc;
    }
    public function getModeloNoActivoDesc(){
        return $this->_modeloNoActivoDesc;
    }
    public function setModeloActivoMensaje($ActivoMensaje){
        $this->_modeloActivoMensaje = $ActivoMensaje;
    }
    public function getModeloActivoMensaje(){
        return $this->_modeloActivoMensaje;
    }
        
    public function getDataDB($id){
        
        $this->_db->addSelect('*');
        $this->_db->addSelect('modelo_activo AS modelo_activo');
        $this->_db->addSelect('\'Activo\'  AS modelo_activo_desc');
        $this->_db->addSelect('\'Inactivo\'  AS modelo_no_activo_desc');
        $this->_db->addSelect('CASE WHEN modelo_activo THEN \'Activo\' ELSE \'Inactivo\' END AS modelo_activo_mensaje');
        $this->_db->addFrom('frente.datos_modelo');
        $this->_db->addWhere("modelo_id = '" . $id . "'");  
        $this->_db->addWhere('modelo_activo = true' );
        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $resultado =  $this->_db->ejecutar();
        return $resultado[0];
        
    }
    
    public function cargarMe($dato){        
        //var_dump($dato);exit;        
        if(is_numeric($dato)){
            $parametros = $this->getDataDB($dato);
        }else{
            $parametros = $dato;
        }
        $this->setModeloId($parametros['modelo_id']);
        $this->setModeloNombre($parametros['modelo_nombre']);
        $this->setFechaAlta($parametros['fecha_alta']);
        $this->setUsuarioId($parametros['usuario_id']);
        $this->setModeloActivo($parametros['modelo_activo']);
        $this->setModeloActivoDesc($parametros['modelo_activo_desc']);
        $this->setModeloNoActivoDesc($parametros['modelo_no_activo_desc']);
        $this->setModeloActivoMensaje($parametros['modelo_activo_mensaje']);
        $this->setObservaciones($parametros['observaciones']);
    }
    
    public function salvarMe($parametros){
        //var_dump($parametros);
        $this->cargarMe($parametros);
        $this->_db->addFrom('frente.datos_modelo');
    
        if($this->getModeloId()!=NULL){
            $this->_db->addCamposTabla('modelo_id');
            $this->_db->addCamposValue("'" . $this->getModeloId() . "'");
        }
        if($this->getModeloNombre()!=NULL){
            $this->_db->addCamposTabla('modelo_nombre');
            $this->_db->addCamposValue("'" . $this->getModeloNombre() . "'");
        }
        if($this->getFechaAlta()!=NULL){
            $this->_db->addCamposTabla('fecha_alta');
            $this->_db->addCamposValue("'" . $this->getFechaAlta() . "'");
        }
        if($this->getUsuarioId()!=NULL){
            $this->_db->addCamposTabla('usuario_id');
            $this->_db->addCamposValue("'" . $this->getUsuarioId() . "'");
        }
        if($this->getModeloActivo()!=NULL){
            $this->_db->addCamposTabla('modelo_activo');
            $this->_db->addCamposValue("'" . $this->getModeloActivo() . "'");
        }
        if($this->getObservaciones()!=NULL){
            $this->_db->addCamposTabla('observaciones');
            $this->_db->addCamposValue("'" . $this->getObservaciones() . "'");
        }
        $this->_db->generarInsert();        
        //echo $this->_db->getQry();
        //exit;
        $rs = $this->_db->ejecutar();
        //esta mal revisar
        if($rs > 0){
              return TRUE;
        }else{
             return FALSE;
        }
    }
    public function actualizarMe($arrParametros){    
        
        if($arrParametros['campos']['modelo_nombre']!='null'){
            $this->_db->addCamposUpdate('modelo_nombre= \'' . $arrParametros['campos']['modelo_nombre'] .'\'');
        }
        if($arrParametros['campos']['observaciones']!='null'){
            $this->_db->addCamposUpdate('observaciones= \'' . $arrParametros['campos']['observaciones'] .'\'');
        }
        $this->_db->addFrom('frente.datos_modelo');
        $this->_db->addWhere('modelo_id = \'' . $arrParametros['id'] .'\'');
        //generar qry  
        $this->_db->generarUpdate(); 
        //echo $this->_db->getQry();exit;
        //asigamos el resulta que es un array a una variable
        $rs =  $this->_db->ejecutar();   
        if(count($this->_db->getArrError())>0){
            //var_dump($this->_db->getArrError());
            //echo $this->_db->getQry();//exit;
            return;
        }
        //$this->cargarMe($arrParametros);
        return $rs;
        
    }
    public function cargarColumnasModelo($id){
        
        $this->_db->addSelect('modelo_id');
        $this->_db->addSelect('modelo_nombre');
        $this->_db->addSelect('detalle_modelo_id');
        $this->_db->addFrom('frente.datos_modelo');
        $this->_db->addFrom('INNER JOIN frente.detalle_modelo USING(modelo_id)');
        $this->_db->addWhere('modelo_id = \'' . $id .'\'' );
        $this->_db->addWhere('modelo_activo = true' );
        $this->_db->addOrderBy('orden_nro asc');
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $arr =  $this->_db->ejecutar();
        $this->setModeloId($arr[0][modelo_id]);
        $this->setModeloNombre($arr[0][modelo_nombre]);
        foreach($arr AS $modelo)
        {
            $objDetalle = NEW DetalleModeloExtendido();
            $objDetalle->cargarMe($modelo['detalle_modelo_id']);
            $this->setDetalleModelo($objDetalle);
        }
    }
}



?>
