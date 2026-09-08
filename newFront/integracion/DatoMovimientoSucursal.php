<?php
/**
 * Description of datoMovimientoALtaExtendido
 *
 * @author dMELMAC
 */

class DatoMovimientoSucursal
{
    /**
	 * 
	 * @access private
	 */
    private $_db;
    
    /**
     * 
     * @access private
     */


    public function __construct() {
               $this->_db = new FrenteAlmacenamiento();
    }


    public function salvarme($arrParametros) {
        //var_dump($arrParametros);exit;
        //generamos el id de movimiento alta

        //control campos mininos de cabecera
        if($arrParametros['id_movimiento'] == NULL){
            $arrDevolver = array(
	      "soyError" => true,
	      "nivel" => 1,
	      "mensaje" =>"El id_movimiento no puede ser nulo"
	      );
	    echo json_encode($arrDevolver);exit;
        }
        
        if($arrParametros['tipo_movimiento_id'] == NULL){
                $arrDevolver = array(
		  "soyError" => true,
		  "nivel" => 1,
		  "mensaje" =>"El tipo de movimiento no puede ser nulo"
	      );
	    echo json_encode($arrDevolver);exit;
              
        }
        //armo los campos y los valores
        if($arrParametros['movimiento_fecha'] != NULL ){
            $this->_db->addCamposTabla('movimiento_fecha');
            $this->_db->addCamposValue("'" . $arrParametros['movimiento_fecha'] . "'");
        }
        //armo los campos y los valores
        if($arrParametros['remito_nro'] != NULL){
            $this->_db->addCamposTabla('remito_nro');
            $this->_db->addCamposValue("'" . $arrParametros['remito_nro'] . "'");
        }
		//armo los campos y los valores
        $this->_db->addCamposTabla('prove_id');
        $this->_db->addCamposValue($arrParametros['prove_id']);

        if($arrParametros['cliente_id']!=NULL){
            $this->_db->addCamposTabla('cliente_id');
            $this->_db->addCamposValue($arrParametros['cliente_id']);
        }
        if($arrParametros['importeEntrada']>0){
            $this->_db->addCamposTabla('movimiento_total_fact');
            $this->_db->addCamposValue($arrParametros['importeEntrada']);
        }
        if($arrParametros['obs'] != NULL){
            $this->_db->addCamposTabla('obs');
            $this->_db->addCamposValue("'" . $arrParametros['obs']  . "'");
        }
        
        $this->_db->addCamposTabla('id_movimiento');
        $this->_db->addCamposTabla('tipo_movimiento_id');
        $this->_db->addCamposTabla('a_sucursal_id');
        $this->_db->addCamposTabla('de_sucursal_id');
        $this->_db->addCamposTabla('de_id_movimiento');
        $this->_db->addCamposValue($arrParametros['id_movimiento']);
        $this->_db->addCamposValue($arrParametros['tipo_movimiento_id']);
        $this->_db->addCamposValue($arrParametros['aIdSucursal']);
        $this->_db->addCamposValue($arrParametros['deIdSucursal']);
        $this->_db->addCamposValue($arrParametros['de_id_movimiento']);

        $this->_db->addFrom('datos_movimientos_sucursales');
        
        $this->_db->generarInsert();
        
        //echo $this->_db->getQry();exit;
        return $this->_db->getQry();
    }
    


}
