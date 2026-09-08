<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/usuarios/Subseccion.php';
require_once 'UsuariosSubsecciones.php';

/**
 * Description of seccionExtendido
 *
 * @author Guillo
 */

class SubseccionExtendido extends Subseccion {
    
    private $_db;
    
    private $_arrSubsecciones;
                    
    public function getArrSubsecciones(){
        return $this->_arrSubsecciones;
    }


    public function __construct() {

        $this->_db = new FrenteAlmacenamiento();
    }
    
    public function cargarPorUsuario($usuarioId,$idSucursal){
        $objUsuariosSubsecciones = new UsuariosSubSecciones();
        $arrUsuarioSubsecciones = $objUsuariosSubsecciones->getDataDB($usuarioId,$idSucursal);
        
        $this->_db->addSelect("c.column_name AS nombre_columna");
        $this->_db->addFrom("information_schema.columns c");
        $this->_db->addWhere("UPPER(c.table_name) = upper( 'usuarios_subsecciones')");
        $this->_db->addOrderBy("c.ordinal_position");
        //generar qry
        $this->_db->generarSelect();   
        //asigamos el resulta que es un array a una variable
        
        $resultado = $this->_db->ejecutar();
        
        foreach($resultado AS $subseccion){
            $arr[$subseccion['nombre_columna']] = $arrUsuarioSubsecciones[$subseccion['nombre_columna']];
        }
        $this->_arrSubsecciones = $arr;
    }

    public function cargarMe($id){}
    public function salvarMe($arrParametros){}
    public function actualizarMe($arrParametros){}    
        
    
}
