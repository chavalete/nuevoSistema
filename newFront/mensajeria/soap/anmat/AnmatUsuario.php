<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
class anmatUsuario
{
    private $_db;
    private $_userNnombre;
    private $_userPass;
    
    public function __construct($codigoSucursal){
        if($codigoSucursal == NULL){
            echo "La clase necesita un id o codigo de Sucursal para cagarce correctamente";
            exit;
        }
        
        $this->_db = new FrenteAlmacenamiento();
        
        $this->_db->addSelect('usuario_anmat');
        $this->_db->addSelect('pass_anmat');
        $this->_db->addFrom('datos_sucursales_usuarios_anmat');
        $this->_db->addWhere('codigo_sucursal = \'' . $codigoSucursal . '\'');
        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $resultado = $this->_db->ejecutar();
        
        $this->_userNnombre = $resultado[0]['usuario_anmat'];
        $this->_userPass= $resultado[0]['pass_anmat'];
    }
    
        
    public function getUsuario(){
        return $this->_userNnombre;
    }
    
    public function getPassword(){
        return $this->_userPass;
    }

}

?>	
