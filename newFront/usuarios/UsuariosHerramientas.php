<?php
/**
 * Description of UsuariosSubsecciones.php
 *
 * @author Lucas
 */
 
 
 
 
class UsuariosHerramientas{
    
    private $_db;
    private $_herramientaRelacionId;
    private $_relacionSeccionId;
    private $_usuarioId;
    
    public function setHerramientasRelacionId($relacion){
        $this->_herramientaRelacionId = $relacion;
    }
    public function getHerramientasRelacionId(){
        return $this->_herramientaRelacionId;
    }
    public function setRelacionSeccionId($id){
        $this->_relacionSeccionId = $id;
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
    
    public function getDataDB($id){
        $this->_db->addSelect('*');
        $this->_db->addFrom('usuarios_herramientas');
        $this->_db->addWhere("usuario_id = '" . $id . "'");        
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
        
        $this->setHerramientasRelacionId($parametros['herramientas_relacion_id']);
        $this->setRelacionSeccionId($parametros['relacion_seccion_id']);
        $this->setUsuarioId($parametros['usuario_id']);
    }
    
    public function salvarme($parametros){
        //var_dump($parametros);exit;
        //echo $parametros['boton'];exit;
        $herramienta = $parametros['herramienta'];
        $qry = "ALTER TABLE usuarios_herramientas ADD ". $herramienta ." boolean default false;";
        //echo $qry;
        return $qry;
    }
    
    public function salvarUsuarioNuevo($objParametros){
        
        $this->setUsuarioId($objParametros->getUsuarioId());
        
        $this->setRelacionSeccionId($objParametros->getRelacionSeccionId());
        
        $this->_db->addCamposTabla('seq_usuarios_herramientas_herramientas_relacion_id');
        $this->_db->generarProximo();
        $id = $this->_db->ejecutar();
        
        $this->setHerramientasRelacionId($id[0]['nextval']);
        
        if($this->getHerramientasRelacionId()!=NULL){
            $this->_db->addCamposTabla('herramientas_relacion_id');
            $this->_db->addCamposValue("'" . $this->getHerramientasRelacionId() . "'");
        }
        if($this->getUsuarioId()!=NULL){
            $this->_db->addCamposTabla('usuario_id');
            $this->_db->addCamposValue("'" . $this->getUsuarioId() . "'");
        }
        if($this->getRelacionSeccionId()!=NULL){
            $this->_db->addCamposTabla('relacion_seccion_id');
            $this->_db->addCamposValue("'" . $this->getRelacionSeccionId() . "'");
        }
        
        $this->_db->addFrom('usuarios_herramientas');
        $this->_db->generarInsert();        
        return $this->_db->getQry();
    
    }
    
    public function actualizarMe($arrParametros){    
        
        if($arrParametros['campos']['herramientas_relacion_id']!=NULL){
            $this->_db->addCamposUpdate('herramientas_relacion_id= \'' . $arrParametros['campos']['herramientas_relacion_id'] .'\'');
        }
        
        $this->_db->addFrom('usuarios_herramientas');
        $this->_db->addWhere('usuario_id= \'' . $arrParametros['id'] .'\'');
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
}
