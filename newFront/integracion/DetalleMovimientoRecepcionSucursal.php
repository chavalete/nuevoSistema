<?php
/*
 * TIPO DE MOVIMIENTO RECEPCION ENTRADA DE PRODUCTOS
 * ID = 1
 */


/**
 * Description of DetallemovimientoAltaExtendido
 *
 * @author dMELMAC
 */

class DetalleMovimientoRecepcionSucursal
{
    private $_db;
    
    public function __construct() {
               $this->_db = new FrenteAlmacenamiento();
    }
    public function setArrError ($error){
        $this->_arrError = $error;
    }
    
    public function salvarme($arrParametros){
    
	//var_dump($arrParametros);
	//var_dump($arrParametros);exit;
        //verificar parametro minimos para cualquier operacion de salvado de detalle
        if($arrParametros['id_movimiento'] == NULL ){
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"No envio id de movimiento"
		    );
	    echo json_encode($arrDevolver);exit;
        }

        if($arrParametros['producto_id'] == NULL ){
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"No envio id de producto"
		    );
	    echo json_encode($arrDevolver);exit;
        }
        
        if($arrParametros['lote_vencimiento'] == NULL ){
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"No llego vencimiento"
		    );
	    echo json_encode($arrDevolver);exit;
        }

        $txtDetalleError = NULL;
        //instancio datoTraza la misma cantidad de veces que el valor de getCantidad para generar las trazas
	        
        //Armo el insert  // ACORDATE DE CAMBIAR LOS $arrParametros POR LOS SETER QUE SE DECLARAN ARRIBA 
        $this->_db->addCamposTabla('id_movimiento');
        $this->_db->addCamposTabla('producto_id, lote_vencimiento, cantidad, unidades, sucursal_id,cantidad_origen,producto_recibido,gtin_origen,unidades_asociadas');
        $this->_db->addCamposValue($arrParametros['id_movimiento']);
        $this->_db->addCamposValue($arrParametros['producto_id']);
        $this->_db->addCamposValue("'" . $arrParametros['lote_vencimiento'] . "'");
        $this->_db->addCamposValue($arrParametros['cantidad']);
        $this->_db->addCamposValue($arrParametros['unidades']);
        $this->_db->addCamposValue($arrParametros['sucursal_id']);
        $this->_db->addCamposValue($arrParametros['cantidad_origen']);
        $this->_db->addCamposValue("'" . $arrParametros['producto_origen'] . "'");
        $this->_db->addCamposValue("'" . $arrParametros['gtin_origen'] . "'");
        $this->_db->addCamposValue($arrParametros['unidades_asociadas']);
        $this->_db->addFrom('detalle_movimientos_sucursales');
        //genero y ejecuto
        $this->_db->generarInsert();
        //print_r($this->_db->getQry());        
        $qryDetalle =  $qryDatoTraza . $this->_db->getQry();
        
        return $qryDetalle;
    }
    public function actualizarme(){
        echo "sin codificar";
    }
}
