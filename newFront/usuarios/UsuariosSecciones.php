<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

/**
 * Description of UsuariosSecciones.php
 *
 * @author Lucas
 */

class UsuariosSecciones{
    
    private $_db;
    
    private $_usuarioId;
    private $_relacionSeccionId;
    private $_sucursalId;
    
    public function setSucursalId($id){
        $this->_sucursalId = $id;
    }
    public function getSucursalId(){
        return $this->_sucursalId;
    }
    public function setRelacionSeccionId($relacion){
        $this->_relacionSeccionId = $relacion;
    }
    public function getRelacionSeccionId(){
        return $this->_relacionSeccionId;
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
        $this->_db->addFrom('usuarios_secciones');
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
        //echo "string";exit;
            $parametros = $this->getDataDB($usuarios);
        }else{
        //echo "array";exit;
            $parametros = $usuarios;
        }
        $this->setRelacionSeccionId($parametros['relacion_seccion_id']);
        $this->setUsuarioId($parametros['usuario_id']);
        $this->setSucursalId($parametros['sucursal_id']);
        
    }
    
    public function salvarMe($parametros){
        //var_dump($parametros);exit;
        //echo $parametros['seccion'];exit;
        $seccion = $parametros['seccion'];
        $qry = "ALTER TABLE usuarios_secciones ADD ". $seccion ." boolean default false;";
        //echo $qry;
        return $qry;
    }
    
    public function salvarUsuarioNuevo($usuarioId){
        
        $this->setUsuarioId($usuarioId);
        $this->_db->addCamposTabla('seq_usuarios_secciones_relacion_seccion_id');
        $this->_db->generarProximo();
        $id = $this->_db->ejecutar();
        
        $this->setRelacionSeccionId($id[0]['nextval']);
        
        if($this->getRelacionSeccionId()!=NULL){
            $this->_db->addCamposTabla('relacion_seccion_id');
            $this->_db->addCamposValue("'" . $this->getRelacionSeccionId() . "'");
        }
        if($this->getUsuarioId()!=NULL){
            $this->_db->addCamposTabla('usuario_id');
            $this->_db->addCamposValue("'" . $this->getUsuarioId() . "'");
        }
        $this->_db->addFrom('usuarios_secciones');
        $this->_db->generarInsert();        
        return $this->_db->getQry();
    }
    
    public function actualizarMe($arrParametros){    
        //var_dump($arrParametros[3]);
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
        $this->_db->addFrom('usuarios_secciones');
        $this->_db->addWhere('relacion_seccion_id = ' . $arrParametros[2] );
        //generar qry  
        $this->_db->generarUpdate(); 
        //echo $this->_db->getQry();
        return $this->_db->getQry();//exit;
        
    }
 }

