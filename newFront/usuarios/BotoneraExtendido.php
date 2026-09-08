<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/usuarios/Botonera.php';
require 'UsuariosBotonera.php';
/**
 * Description of botoneraExtendido
 *
 * @author guillo
 */

class BotoneraExtendido extends Botonera {
    
    private $_db;
    private $_arrBotonera;
    
    public function getArrBotonera(){
        return $this->_arrBotonera;
    }

    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
    }
    
    public function cargarPorUsuario($usuarioId,$idSucursal){
        $objUsuariosBotonera= new UsuariosBotonera();
        $arrUsuarioBotonera = $objUsuariosBotonera->getDataDB($usuarioId,$idSucursal);
        
        $this->_db->addSelect("c.column_name AS nombre_columna");
        $this->_db->addFrom("information_schema.columns c");
        $this->_db->addWhere("UPPER(c.table_name) = upper( 'usuarios_botonera')");
        $this->_db->addOrderBy("c.ordinal_position");
        //generar qry
        $this->_db->generarSelect();   
        //asigamos el resulta que es un array a una variable
        
        $resultado = $this->_db->ejecutar();
        
        foreach($resultado AS $botonera){
            $arr[$botonera['nombre_columna']] =  $arrUsuarioBotonera[$botonera['nombre_columna']];                                    
        }
        $this->_arrBotonera = $arr;
    }


    public function cargarMe($id){}
    public function salvarMe($arrParametros){}
    public function actualizarMe($arrParametros){}    
        

}
