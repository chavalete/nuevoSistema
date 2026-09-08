<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/mensajeria/transacciones/TipoMovimientoAnmat.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TipoMovimientoTrazaExtendido
 *
 * @author root
 */
class TipoMovimientoAnmatExtendido extends TipoMovimientoAnmat
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
    public function getDataDB($id){
    
	//echo "llego";
        $this->_db->addSelect('*');
        $this->_db->addFrom('tipo_movimientos_anmat');
        $this->_db->addWhere(' movimiento_id = ' . $id );
        
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        //print_r(count($resultado));
        //verificamos que el movimiento exista en la db
        if(count($resultado) == 0){
            $arrError['detalle']= 'El movimiento que decea efectuar no exite';
            $arrError['archivo'] = __FILE__;
            $this->setArrError($arrError);
        }
    }

    public function cargarMe($datos) {
	
	if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}

        $this->setTipoMovimientoId($resultado['movimiento_id']);
        $this->setTipoMovimientoNombre($resultado['movimiento_nombre']);
	$this->setTipoMovimientoDe($resultado['movimiento_de']);

    }
}

?>