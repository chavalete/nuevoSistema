<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

/**
 * Description of UsuariosSubsecciones.php
 *
 * @author Lucas
 */
 
class UsuariosSubSecciones{
    
    private $_db;
    
    private $_relacionSubseccionId;
    private $_usuarioId;
    
    public function setRelacionSubseccionId($relacion){
        $this->_relacionSubseccionId = $relacion;
    }
    public function getRelacionSubseccionId(){
        return $this->_relacionSubseccionId;
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
        $this->_db->addFrom('usuarios_subsecciones');
        $this->_db->addWhere("usuario_id = '" . $id . "'");
        $this->_db->addWhere("sucursal_id = '" . $idSucursal . "'");
        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $arrParametros =  $this->_db->ejecutar();
        return $arrParametros[0];
    }
    
    public function cargarMe($usuarios){
        //var_dump($usuarios);exit;        
        if(is_numeric($usuarios)){
            $parametros = $this->getDataDB($usuarios);
        }else{
            $parametros = $usuarios;
        }
        $this->setRelacionSubseccionId($parametros['relacion_subseccion_id']);
        $this->setUsuarioId($parametros['usuario_id']);
    }
    
    public function salvarMe($parametros){
        $subseccion = $parametros['subseccion'];
        $qry = "ALTER TABLE usuarios_subsecciones ADD ". $subseccion ." boolean default false;";
        echo $qry;
        return $qry;
        
    }
    
    public function salvarUsuarioNuevo($objParametros){
        /*
        $this->setUsuarioId($objParametros->getUsuarioId());
        $this->setRelacionSeccionId($objParametros->getRelacionSeccionId());
        $this->_db->addCamposTabla('seq_usuarios_subseccion_relacion_seccion_id');
        $this->_db->generarProximo();
        $id = $this->_db->ejecutar();
        
        $this->setRelacionSubseccionId($id[0]['nextval']);
        */
        if($this->getUsuarioId()!=NULL){
            $this->_db->addCamposTabla('relacion_subseccion_id');
            $this->_db->addCamposValue("'" . $this->getUsuarioId() . "'");
        }
        if($this->getUsuarioId()!=NULL){
            $this->_db->addCamposTabla('usuario_id');
            $this->_db->addCamposValue("'" . $this->getUsuarioId() . "'");
        }
        if($this->getRelacionSeccionId()!=NULL){
            $this->_db->addCamposTabla('relacion_seccion_id');
            $this->_db->addCamposValue("'" . $this->getRelacionSeccionId() . "'");
        }
        
        $this->_db->addFrom('usuarios_subsecciones');
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
        $this->_db->addFrom('usuarios_subsecciones');
        $this->_db->addWhere('relacion_subseccion_id = ' . $arrParametros[2] );
        //generar qry  
        $this->_db->generarUpdate(); 
        return $this->_db->getQry();
    }
 }


