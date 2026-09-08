<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
//STOCK
require_once 'LoteExtendido.php';

/**
 * Description of FrenteLotes
 *
 * guillo dMELMAC
 */
class FrenteLotes {
    
    private $_db;
    private $_colObjLotes;
    private $_ultimoLoteId;

    public function __construct() {
	$this->_db = NEW FrenteAlmacenamiento();
    }
    
    public function setColObjLote($objLote){
	$this->_colObjLotes[$objLote->getLoteId()] = $objLote;
    }
    public function getColObjLote(){
	return $this->_colObjLotes;
    }
    
    public function setUltimoLoteId($id){
	$this->_ultimoLoteId = $id;
    }
    public function getUltimoLoteId(){
	return $this->_ultimoLoteId;
    }
    
    public function cargarLotes($id){
	$this->_colObjLotes[$id] = new LoteExtendido();
	$this->_colObjLotes[$id]->cargarMe($id);
        $this->_ultimoLoteId = $id;
        
    }
    
    public function salvarLotes($arrParametros){
	$lote = new LoteExtendido();
	$lote->salvarMe($arrParametros);
        $this->cargarLotes($lote->getLoteid());
    }
    
    
    public function cargarLotesLazzy($arrIds){
    
	foreach($arrIds as $id){
	    if($id['lote_id']!=NULL){
		$ids.= "'" . $id['lote_id'] . "',";
	    }
	}
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
        $this->_db->addFrom('datos_lotes');
        $this->_db->addWhere("lote_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
	foreach($resultado AS $detalle){
	    $objlote= NEW LoteExtendido();
	    $objlote->cargarMe($detalle);
	    $this->setColObjLote($objlote);
        }
    }
        public function validarLote($lote, $productoId, $vencimiento,$idSucursal){

        $arrVencimiento = explode("-", $vencimiento);
	$vencimiento = $arrVencimiento[2] . "-" . $arrVencimiento[1] . "-" . $arrVencimiento[0];
                
            
        $this->_db->addSelect('lote_id');
	$this->_db->addFrom('datos_lotes');
        $this->_db->addWhere('lote = \'' . $lote .'\'' );
        $this->_db->addWhere('producto_id = \'' . $productoId .'\'' );
        $this->_db->addWhere('lote_vencimiento = \'' . $vencimiento .'\'' );
        $this->_db->addWhere('sucursal_id = \'' . $idSucursal .'\'' );

        
        $this->_db->generarSelect();

        //echo $this->_db->getQry();
        $resultado = $this->_db->ejecutar();
        
                
        //verificamos que el movimiento exista en la db
        if(count($resultado) == 0){
            $arrParametros = Array('lote' => $lote, 'producto_id' => $productoId, 'lote_vencimiento' => $vencimiento,'id_sucursal'=>$idSucursal);
            $this->salvarLotes($arrParametros);
        }else{
            $this->cargarLotes($resultado[0][lote_id]);
        }
        
    }
    
    public function cargarLotePorLoteProducto($arrParametros){
        $this->_db->addSelect('lote_id');
	$this->_db->addFrom('datos_lotes');
        $this->_db->addWhere('lote = \'' . $arrParametros['lote'] .'\'' );
        $this->_db->addWhere('producto_id = \'' . $arrParametros['producto_id'] .'\'' );
        
        $this->_db->generarSelect();

        //echo $this->_db->getQry();
        $resultado = $this->_db->ejecutar();
        
        if(count($resultado) == 0){
            $arrDevolver = array(
                "soyError" => true,
                "nivel" => 1,
                "mensaje" =>'El lote no existe'
                );
            echo json_encode($arrDevolver);exit;
        }
        $this->cargarLotes($resultado[0][lote_id]);
    }
}
