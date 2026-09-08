<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/usuarios/Usuario.php';

/**
 * Description of Usuarios
 * Para menejo de usuarios y en forma de frente
 * @author chava
 */
class UsuarioExtendido extends Usuario
{    
    private $_db;
    private $_usuarioActivoDesc;
    private $_usuarioNoActivoDesc;
    private $_usuarioActivoMensaje;
    private $_sucursalId;
    
    public function setSucursalId($id){
        $this->_sucursalId = $id;
    }
    public function getSucursalId(){
        return $this->_sucursalId;
    }
    public function setUsuarioActivoDesc($ActivoDesc){
        $this->_usuarioActivoDesc = $ActivoDesc;
    }
    public function getUsuarioActivoDesc(){
        return $this->_usuarioActivoDesc;
    }
    public function setUsuarioNoActivoDesc($NoActivoDesc){
        $this->_usuarioNoActivoDesc = $NoActivoDesc;
    }
    public function getUsuarioNoActivoDesc(){
        return $this->_usuarioNoActivoDesc;
    }
    public function setUsuarioActivoMensaje($ActivoMensaje){
        $this->_usuarioActivoMensaje = $ActivoMensaje;
    }
    public function getUsuarioActivoMensaje(){
        return $this->_usuarioActivoMensaje;
    }
    
    public function __construct() {
	$this->_db = new FrenteAlmacenamiento();
    }

    public function registrarIngreso($arrDatos){
    
	$this->_db->addCamposUpdate('usuario_ultimo_login = now()::timestamp' );
        $this->_db->addFrom('datos_usuarios');
        $this->_db->addWhere('usuario_id = ?');
               
        $arrDatos = Array(Array($arrDatos['usuarioId']));
        //generar qry
        $this->_db->generarUpdate();   
        //asigamos el resulta que es un array a una variable
        return $this->_db->ejecutar($arrDatos);
    }
    
    public function getUsuarioById($id){
    
        $this->_db->addSelect('*');
        $this->_db->addFrom('datos_usuarios');
        $this->_db->addWhere('usuario_id = $id');
        //generar qry
        $this->_db->generarSelect();   
        //asigamos el resulta que es un array a una variable
        return  $this->_db->ejecutar('traer');
    }

    public function getDataDB($id){
        $this->_db->addSelect('*');
        $this->_db->addSelect('usuario_activo AS usuario_activo');
        $this->_db->addSelect('\'Activo\'  AS usuario_activo_desc');
        $this->_db->addSelect('\'Inactivo\'  AS usuario_no_activo_desc');
        $this->_db->addSelect('CASE WHEN usuario_activo THEN \'Activo\' ELSE \'Inactivo\' END AS usuario_activo_mensaje');
        $this->_db->addFrom('datos_usuarios');
        $this->_db->addWhere("usuario_id = '" . $id . "'");  
        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $resultado =  $this->_db->ejecutar();
        return $resultado[0];
    }

    public function cargarMe($id){
        //var_dump($id);exit;        
        if(is_int($id)){
            $parametros = $this->getDataDB($id);
        }else{
            $parametros = $id;
}
        $this->setUsuarioId($parametros['usuario_id']);
        $this->setNombreUsuario($parametros['usuario_nombre']);
        $this->setPwd($parametros['usuario_password']);
        $this->setNombreCompleto($parametros['usuario_nombre_completo']);
        $this->setFechaAlta($parametros['usuario_fecha_alta']);
        $this->setActivo($parametros['usuario_activo']);
        $this->setSubseccionInicio($parametros['subseccion_inicio']);
        $this->setFechaInactivacion($parametros['usuario_fecha_inactivo']);
        $this->setUltimoAcceso($parametros['usuario_ultimo_login']);
        $this->setEsPrivilegiado($parametros['usuario_administrador']);
        $this->setUsuarioBas($parametros['usuario_bas']);
        $this->setUsuarioActivoDesc($parametros['usuario_activo_desc']);
        $this->setUsuarioNoActivoDesc($parametros['usuario_no_activo_desc']);
        $this->setUsuarioActivoMensaje($parametros['usuario_activo_mensaje']);
        $this->setSucursalId($parametros['sucursal_id']);
    }

    public function salvarMe($parametros){
        //var_dump($parametros);
        $this->_db->addCamposTabla('seq_datos_usuarios_id');
        $this->_db->generarProximo();
        $id = $this->_db->ejecutar();
        $parametros['usuario_id'] = $id[0]['nextval'];    
        
        $this->cargarMe($parametros);
        
        //var_dump($this);exit;       
        
        $this->_db->addFrom('datos_usuarios');
        if($this->getUsuarioId()!=NULL){
            $this->_db->addCamposTabla('usuario_id');
            $this->_db->addCamposValue("'" . $this->getUsuarioId() . "'");
        }
        if($this->getNombreUsuario()!=NULL){
            $this->_db->addCamposTabla('usuario_nombre');
            $this->_db->addCamposValue("'" . $this->getNombreUsuario() . "'");
        }
        if($this->getPwd()!=NULL){
            $this->_db->addCamposTabla('usuario_password');
            $this->_db->addCamposValue("md5('" . $this->getPwd() . "')");
        }
        if($this->getNombreCompleto()!=NULL){
            $this->_db->addCamposTabla('usuario_nombre_completo');
            $this->_db->addCamposValue("'" . $this->getNombreCompleto() . "'");
        }
        if($this->getFechaAlta()!=NULL){
            $this->_db->addCamposTabla('usuario_fecha_alta');
            $this->_db->addCamposValue("'" . $this->getFechaAlta() . "'");
        }
        if($this->getActivo()!=NULL){
            $this->_db->addCamposTabla('usuario_activo');
            $this->_db->addCamposValue("'" . $this->getActivo() . "'");
        }
        if($this->getSubseccionInicio()!=NULL){
            $this->_db->addCamposTabla('subseccion_inicio');
            $this->_db->addCamposValue("'" . $this->getSubseccionInicio() . "'");
        }
        if($this->getFechaInactivacion()!=NULL){
            $this->_db->addCamposTabla('usuario_fecha_inactivo');
            $this->_db->addCamposValue("'" . $this->getFechaInactivacion() . "'");
        }
        if($this->getUltimoAcceso()!=NULL){
            $this->_db->addCamposTabla('usuario_ultimo_login');
            $this->_db->addCamposValue("'" . $this->getUltimoAcceso() . "'");
        }
        if($this->getEsPrivilegiado()!=NULL){
            $this->_db->addCamposTabla('usuario_administrador');
            $this->_db->addCamposValue("'" . $this->getEsPrivilegiado() . "'");
        }
        if($this->getUsuarioBas()!=NULL){
            $this->_db->addCamposTabla('usuario_bas');
            $this->_db->addCamposValue("'" . $this->getUsuarioBas() . "'");
        }
        
        $this->_db->generarInsert();        
        

        return $this->_db->getQry();
        //exit;
        
    }
    public function actualizarMe($arrParametros){
        
        if($arrParametros['campos']['usuario_nombre']!= NULL){
            $this->_db->addCamposUpdate('usuario_nombre= \'' . $arrParametros['campos']['usuario_nombre'] .'\'');
        }
        if($arrParametros['campos']['usuario_nombre_completo']!= NULL){
            $this->_db->addCamposUpdate('usuario_nombre_completo= \'' . $arrParametros['campos']['usuario_nombre_completo'] .'\'');
        }
        //var_dump($arrParametros['campos']['usuario_password']);
        if($arrParametros['campos']['usuario_password']!= $this->getPwd()){
            $this->_db->addCamposUpdate('usuario_password= md5(\'' . $arrParametros['campos']['usuario_password'] .'\')');
        }
        if($arrParametros['campos']['usuario_bas']!= $this->getPwd()){
            $this->_db->addCamposUpdate('usuario_bas= \'' . $arrParametros['campos']['usuario_bas'] .'\'');
        }
        if($arrParametros['campos']['usuario_activo'] == 1){
            $this->_db->addCamposUpdate('usuario_activo = true');
        }else{
            $this->_db->addCamposUpdate('usuario_activo = false');
        }
        
        if($arrParametros['campos']['subseccion_inicio'] != NULL){
            $this->_db->addCamposUpdate('subseccion_inicio = \'' . $arrParametros['campos']['subseccion_inicio']  . '\'');
        }
        
        $this->_db->addFrom('datos_usuarios');
        $this->_db->addWhere('usuario_id = \'' . $arrParametros['id'] .'\'');
        //generar qry  
        $this->_db->generarUpdate(); 
        //echo $this->_db->getQry();//exit;
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
