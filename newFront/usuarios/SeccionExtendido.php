<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/usuarios/Seccion.php';
require_once 'UsuariosSecciones.php';

/**
 * Description of seccionExtendido
 *
 * @author Guillo
 */

class SeccionExtendido extends Seccion {
    
    private $_db;
    
    private $_arrSecciones;
    
    public function getArrSecciones(){
        return $this->_arrSecciones;
    }


    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();        
    }
    
    public function cargarPorUsuario($usuarioId,$sucursalId){
    
        $objUsuariosSecciones = new UsuariosSecciones();
        $arrUsuarioSecciones = $objUsuariosSecciones->getDataDB($usuarioId,$sucursalId);
        
        $this->_db->addSelect("c.column_name AS nombre_columna");
        $this->_db->addFrom("information_schema.columns c");
        $this->_db->addWhere("UPPER(c.table_name) = upper( 'usuarios_secciones')");
        $this->_db->addOrderBy("c.ordinal_position");
        //generar qry
        $this->_db->generarSelect();   
        //asigamos el resulta que es un array a una variable
        
        $resultado = $this->_db->ejecutar();
        
        foreach($resultado AS $seccion){
                $arr[$seccion['nombre_columna']] = $arrUsuarioSecciones[$seccion['nombre_columna']];
        }
        
        $this->_arrSecciones = $arr;
        
    }


    public function cargarMe($id){}
    public function salvarMe($arrParametros){}
    public function actualizarMe($arrParametros){}    
        
    
}
