<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/trazabilidad/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/trazabilidad/libreria/modelo/DetalleModelo.php';

class DetalleModeloExtendido extends DetalleModelo
{   
    private $_db;
    private $_sortableMensaje;
    private $_requiredMensaje;
    private $_editableMensaje;
    
    public function __construct(){
        $this->_db = new FrenteAlmacenamiento();
    }
    public function setSortableMensaje($mensaje){
        $this->_sortableMensaje = $mensaje;
    }
    public function getSortableMensaje(){
        return $this->_sortableMensaje;
    }
    public function setRequiredMensaje($mensaje){
        $this->_requiredMensaje = $mensaje;
    }
    public function getRequiredMensaje(){
        return $this->_requiredMensaje;
    }
    public function setEditableMensaje($mensaje){
        $this->_editableMensaje = $mensaje;
    }
    public function getEditableMensaje(){
        return $this->_editableMensaje;
    }
    public function setModeloActivoMensaje($ActivoMensaje){
        $this->_modeloActivoMensaje = $ActivoMensaje;
    }
    public function getModeloActivoMensaje(){
        return $this->_modeloActivoMensaje;
    }
    public function getDataDB($id){
        
        $this->_db->addSelect('*');
        $this->_db->addSelect('CASE WHEN sortable THEN \'Si\' ELSE \'No\' END AS sortable_mensaje');
        $this->_db->addSelect('CASE WHEN required THEN \'Si\' ELSE \'No\' END AS required_mensaje');
        $this->_db->addSelect('CASE WHEN editable THEN \'Si\' ELSE \'No\' END AS editable_mensaje');
        $this->_db->addSelect('CASE WHEN modelo_activo THEN \'Si\' ELSE \'No\' END AS modelo_activo_mensaje');
        $this->_db->addFrom('frente.detalle_modelo');
        $this->_db->addWhere("detalle_modelo_id = '" . $id . "'");        
        $this->_db->addWhere('modelo_activo = true');
        $this->_db->generarSelect();
        
        //echo $this->_db->getQry();exit;
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
    //var_dump($parametros);exit;
        $this->setModeloId($parametros['modelo_id']);
        $this->setDisplay($parametros['display']);
        $this->setName($parametros['name']);
        $this->setAlign($parametros['align']);
        $this->setOrdenNro($parametros['orden_nro']);
        $this->setDetalleModeloId($parametros['detalle_modelo_id']);
        $this->setSortable($parametros['sortable']);
        $this->setClass($parametros['class']);
        $this->setRequired($parametros['required']);
        $this->setEditable($parametros['editable']);
        $this->setModeloActivo($parametros['modelo_activo']);
        $this->setContenedor();
        $this->setSortableMensaje($parametros['sortable_mensaje']);
        $this->setRequiredMensaje($parametros['required_mensaje']);
        $this->setEditableMensaje($parametros['editable_mensaje']);
        $this->setModeloActivoMensaje($parametros['modelo_activo_mensaje']);

    }

	public function salvarMe($parametros){
        $this->cargarMe($parametros);
        $this->_db->addFrom('frente.detalle_modelo');
        
            if($this->getModeloId()!=NULL){
                $this->_db->addCamposTabla('modelo_id');
                $this->_db->addCamposValue("'" . $this->getModeloId() . "'");
            }
            if($this->getDisplay()!=NULL){
                $this->_db->addCamposTabla('display');
                $this->_db->addCamposValue("'" . $this->getDisplay() . "'");
            }
            if($this->getName()!=NULL){
                $this->_db->addCamposTabla('name');
                $this->_db->addCamposValue("'" . $this->getName() . "'");
            }
            if($this->getAlign()!=NULL){
                $this->_db->addCamposTabla('align');
                $this->_db->addCamposValue("'" . $this->getAlign() . "'");
            }
            if($this->getOrdenNro()!=NULL){
                $this->_db->addCamposTabla('orden_nro');
                $this->_db->addCamposValue("'" . $this->getOrdenNro() . "'");
            }
            if($this->getDetalleModeloId()!=NULL){
                $this->_db->addCamposTabla('detalle_modelo_id');
                $this->_db->addCamposValue("'" . $this->getDetalleModeloId() . "'");
            }
            if($this->getSortable()!=NULL){
                $this->_db->addCamposTabla('sortable');
                $this->_db->addCamposValue("'" . $this->getSortable() . "'");
            }
            if($this->getClass()!=NULL){
                $this->_db->addCamposTabla('class');
                $this->_db->addCamposValue("'" . $this->getClass() . "'");
            }
            if($this->getRequired()!=NULL){
                $this->_db->addCamposTabla('required');
                $this->_db->addCamposValue("'" . $this->getRequired() . "'");
            }
            if($this->getEditable()!=NULL){
                $this->_db->addCamposTabla('editable');
                $this->_db->addCamposValue("'" . $this->getEditable() . "'");
            }
            if($this->getModeloActivo()!=NULL){
                $this->_db->addCamposTabla('modelo_activo');
                $this->_db->addCamposValue("'" . $this->getModeloActivo() . "'");
            }
            
            $this->_db->generarInsert();        
            //echo $this->_db->getQry();
            //exit;
            $rs = $this->_db->ejecutar();
            
            if($rs > 0){
                 return TRUE;
            }else{
                return FALSE;
        }
	
    }
    
    public function actualizarMe($arrParametros){    
    
    $this->setDisplay($arrParametros["campos"]["display"]);
    $this->setName($arrParametros["campos"]["name"]);
    $this->setAlign($arrParametros["campos"]["align"]);
    $this->setOrdenNro($arrParametros["campos"]["orden_nro"]);
    $this->setDetalleModeloId($arrParametros["id"]);
    if($arrParametros["campos"]["sortable"]=='Si' ||$arrParametros["campos"]["sortable"]=='si'){
        $this->setSortable('true');
    }else{
       $this->setSortable('false'); 
    }
    
    if($arrParametros["campos"]["required"]=='Si'||$arrParametros["campos"]["required"]=='si'){
        $this->setRequired('true');
    }else{
       $this->setRequired('false'); 
    }
    if($arrParametros["campos"]["editable"]=='Si'||$arrParametros["campos"]["editable"]=='si'){
        $this->setEditable('true');
    }else{
       $this->setEditable('false'); 
    }
    
    if($arrParametros["campos"]["modelo_activo"]=='Si'||$arrParametros["campos"]["modelo_activo"]=='si'){
        $this->setModeloActivo('true');
    }else{
       $this->setModeloActivo('false'); 
    }
    
    if($this->getDisplay()!=NULL){
        $this->_db->addCamposUpdate("display = '". $this->getDisplay() ."'");
    }
    if($this->getName()!=NULL){
        $this->_db->addCamposUpdate("name = '". $this->getName() ."'");
    }
    if($this->getAlign()!=NULL){
        $this->_db->addCamposUpdate("align = '". $this->getAlign() ."'");
    }
    if($this->getOrdenNro()!=NULL){
        $this->_db->addCamposUpdate("orden_nro = '". $this->getOrdenNro() ."'");
    } 
    if($this->getSortable()!=NULL){
        $this->_db->addCamposUpdate("sortable = ". $this->getSortable() ."");
    }
    if($this->getClass()!=NULL){
        $this->_db->addCamposUpdate("class = ' ". $this->getClass() ."'");
    }
    if($this->getRequired()!=NULL){
        $this->_db->addCamposUpdate("required = '" . $this->getRequired() ."'");
    } 
    if($this->getEditable()!=NULL){
        $this->_db->addCamposUpdate("editable = '" . $this->getEditable() ."'");
    } 
    if($this->getModeloActivo()!=NULL){
        $this->_db->addCamposUpdate("modelo_activo = '" . $this->getModeloActivo() ."'");
    }
    
    $this->_db->addFrom('frente.detalle_modelo');
    $this->_db->addWhere('detalle_modelo_id = \'' . $this->getDetalleModeloId() .'\'');
        
    //generar qry  
    $this->_db->generarUpdate(); 

    //echo $this->_db->getQry();exit;
    //echo $this->_db->getQry();exit;
    //asignamos el resulta que es un array a una variable
    $resultado =  $this->_db->ejecutar();   
    if(count($resultado)==0){
	     $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'Error al actualizar los detalles'
		    );
	}else{
	    $arrDevolver = array(
		      "soyError" => false,
		      "nivel" => 1,
		      "mensaje" =>'Detalles  actualizados'
		    );
	}
	echo json_encode($arrDevolver);exit;
    }
    
    public function cargarColumnasModelo($id){
    
        $this->_db->addSelect('modelo_id');
        $this->_db->addSelect('display');
        $this->_db->addSelect('name');
        $this->_db->addSelect('sortable');
        $this->_db->addSelect('align');
        $this->_db->addSelect('editable');
        $this->_db->addSelect('class');
        $this->_db->addSelect('detalle_modelo_id');
        $this->_db->addSelect('required');
        $this->_db->addFrom('frente.detalle_modelo');
        $this->_db->addWhere('detalle_modelo_id = \'' . $id .'\'' );
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $arrDetalle =  $this->_db->ejecutar();

        foreach($arrDetalle AS $detalle){
            $this->setModeloId($detalle['modelo_id']);
            $this->setDisplay($detalle['display']);
            $this->setName($detalle['name']);
            $this->setSortable($detalle['sortable']);
            $this->setAlign($detalle['align']);
            $this->setEditable($detalle['editable']);
            $this->setDetalleId($detalle['detalle_modelo_id']);
            $this->setClass($detalle['class']);
            $this->setRequired($detalle['required']);
            $this->setContenedor();
        }
   }
}
