<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/**
 * Description of UsuariosSubsecciones.php
 *
 * @author Lucas
 */
 
class UsuariosBotonera{
    
    private $_db;
    
    private $_relacionBotonId;
    private $_usuarioId;
    private $_sucursalId;

    public function setSucursalId($id){
        $this->_sucursalId = $id;
    }
    public function getSucursalId(){
        return $this->_sucursalId;
    }
    
    public function setRelacionBotonId($relacion){
        $this->_relacionBotonId = $relacion;
    }
    public function getRelacionBotonId(){
        return $this->_relacionBotonId;
    }
    public function setUsuarioId($usuarios){
        $this->_usuarioId= $usuarios;
    }
    public function getUsuarioId(){
        return $this->_usuarioId;
    }
    
    public function __construct(){
        $this->_db = new FrenteAlmacenamiento();
    }
    
    public function getDataDB($id,$idSucursal){
        $this->_db->addSelect('*');
        $this->_db->addFrom('usuarios_botonera');
        $this->_db->addWhere("usuario_id = '" . $id . "'");
        $this->_db->addWhere("sucursal_id = '" . $idSucursal . "'");
        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $arrParametros =  $this->_db->ejecutar();
        return $arrParametros[0];
    }
    
    public function cargarMe($usuarios){
        //var_dump($usuarios);exit;        
        if(is_integer($usuarios)){
            $parametros = $this->getDataDB($usuarios);
        }else{
            $parametros = $usuarios;
        }
        $this->setRelacionBotonId($parametros['relacion_boton_id']);
        $this->setUsuarioId($parametros['usuario_id']);
        $this->setSucursalId($parametros['sucursal_id']);
    }
    
    public function salvarMe($parametros){
        //var_dump($parametros);exit;
        //echo $parametros['boton'];exit;
        $boton = $parametros['boton'];
        $qry = "ALTER TABLE usuarios_botonera ADD ". $boton ." boolean default true;";
        //echo $qry;
        return $qry;
    }
    
    public function salvarUsuarioNuevo($botonId){
        
        if($this->getUsuarioId()!=NULL){
            $this->_db->addCamposTabla('relacion_boton_id');
            $this->_db->addCamposValue("'" . $this->getUsuarioId() . "'");
        }
        if($this->getUsuarioId()!=NULL){
            $this->_db->addCamposTabla('usuario_id');
            $this->_db->addCamposValue("'" . $this->getUsuarioId() . "'");
        }
        $this->_db->addFrom('usuarios_botonera');
        $this->_db->generarInsert();        
        return $this->_db->getQry();
    }
    
    
    
    public function actualizarMe($arrParametros){    
        
   //var_dump($arrParametros[3]);exit;
        switch($arrParametros[3]){
            case "No":
                //echo "lega";exit;
                $valor = 'false';
                break;
            case "Si":
                $valor = 'true';
                break;
        }
        //var_dump($valor);
        if($arrParametros[0]!=NULL){
            $this->_db->addCamposUpdate($arrParametros[0] . ' = ' . $valor );
        }
        $this->_db->addFrom('usuarios_botonera');
        $this->_db->addWhere('relacion_boton_id = ' . $arrParametros[2] );
        //generar qry  
        $this->_db->generarUpdate(); 
        return $this->_db->getQry();//exit;
    }
        
 }



