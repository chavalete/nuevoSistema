<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/trazabilidad/TipoMovimientoTraza.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TipoMovimientoTrazaExtendido
 *
 * @author root
 */
class TipoMovimientoTrazaExtendido extends TipoMovimientoTraza
{
    private $_db;
        
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
    }
    
    public function actualizarMe() {
        echo "lala";
    }
    
    public function salvarMe() {
        echo "lala";
    }

    public function cargarMe($id) {

        $this->_db->addSelect('*');
        $this->_db->addFrom('tipos_movimientos_trazas');
        $this->_db->addWhere(' tipo_movimiento_id = ' . $id );
        
        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $resultado = $this->_db->ejecutar();
        
        //print_r(count($resultado));
        //verificamos que el movimiento exista en la db
        if(count($resultado) == 0){
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"EL tipo de movimeinto que desea utilizar no existe"
		    );
	      echo json_encode($arrDevolver);exit;
            
        }

        $this->setTipoMovimientoId($resultado[0]['tipo_movimiento_id']);
        $this->setTipoMovimientoNombre($resultado[0]['tipo_movimiento_nombre']);
        $this->setTipoMovimientoALta($resultado[0]['tipo_movimiento_alta']);

    }
}

?>
