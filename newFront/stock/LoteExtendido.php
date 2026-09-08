<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/stock/Lote.php';

class LoteExtendido extends Lote
{
    private $_db;
    
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
    }


    public function getDataDB($id){
        $this->_db->addSelect('lote_id');
	$this->_db->addSelect('lote');
	$this->_db->addSelect('lote_activo');
	$this->_db->addSelect('to_char(lote_vencimiento,\'DD-MM-YYYY\') AS lote_vencimiento');
        $this->_db->addSelect('letra_griega_id');
	$this->_db->addSelect('observaciones');
        $this->_db->addSelect('producto_id');
	$this->_db->addFrom('datos_lotes');
        $this->_db->addWhere(' lote_id = ' . $id );
       
        $this->_db->generarSelect();
        $resultado = $this->_db->ejecutar();
        //echo $this->_db->getQry();
        return $resultado[0];
    }
    
    
    public function cargarMe($datos){
        
	if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}
        
        if(count($resultado) == 0){
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'El lote no existe'
		    );
		echo json_encode($arrDevolver);exit;
        }
        
        $this->setLoteId($resultado['lote_id']);
        $this->setLote($resultado['lote']);
        $this->setLoteActivo($resultado['lote_activo']);
        
        $this->setLoteVencimiento($resultado['lote_vencimiento']);
        //$this->setLoteVencimiento('2007-07-01');
	$this->setLetraGriegaId($resultado['letra_griega_id']);
        $this->setObs($resultado['obvervaciones']);
        $this->setProductoId($resultado['producto_id']);
    }
    
    function salvarMe($arrParametros){
        
        $this->cargarMe($arrParametros);
        
        $this->_db->addCamposTabla('seq_datos_lotes_id');
	$this->_db->generarProximo();
        $resultado = $this->_db->ejecutar();
	$this->setLoteId($resultado[0]['nextval']);
	
        
        $this->_db->addFrom('datos_lotes');
        
        $this->_db->addCamposTabla('lote_id');
        $this->_db->addCamposValue($this->getLoteId());
        
        
        $this->_db->addCamposTabla('lote');
        $this->_db->addCamposValue('\'' . $this->getLote() . '\'');
        
        
        $this->_db->addCamposTabla('producto_id');
        $this->_db->addCamposValue('\'' . $this->getProductoId() . '\'');
        
        if($this->getLoteVencimiento()!=NULL){
            $this->controlVencimientoLote($this->getLoteVencimiento());
            $this->_db->addCamposTabla('lote_vencimiento');
            $this->_db->addCamposValue('\'' . $this->getLoteVencimiento() . '\'');
		//$this->_db->addCamposValue('2027-07-01');
        }
        $this->_db->addCamposTabla('sucursal_id');
        $this->_db->addCamposValue('\'' . $arrParametros['id_sucursal'] . '\'');
        $this->_db->generarInsert();    
	//echo $this->_db->getQry(); exit;
	$resultado = $this->_db->ejecutar();
        
        if(count($this->_db->getArrError()) > 0){
            $arrError = $this->_db->getArrError();
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" => $arrError['detalle']
		    );
		echo json_encode($arrDevolver);exit;
        }
    }
    
    function actualizarMe(){
        //a codificar
    }
    
    private function controlVencimientoLote($fechaVencimiento){
        
	$this->_db->setQry('SELECT \'' . $fechaVencimiento . '\'::date - now()::date AS diferencia');
        $resultado = $this->_db->ejecutar();
        
        if($resultado[0]['diferencia'] <= 0){ // 2 meses = 60 dias
            $arrDevolver = array(
                "soyError" => true,
                "nivel" => 1,
                "mensaje" => "El vencimiento debe ser mayor a la fecha actual"
                // $this->_db->getQry()
            );
            echo json_encode($arrDevolver);exit;
        }
    }
}
