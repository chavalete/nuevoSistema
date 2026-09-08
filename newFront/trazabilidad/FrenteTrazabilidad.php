<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FrenteTrazabilidad
 *
 * @author root
 */
class FrenteTrazabilidad
{
    private $_db;
    private $_objFrenteError ;
    private $_colObjDatoTrazabilidad;


    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
        $this->_objFrenteError = new FrenteError();
    }
    
    public function setColObjDatoTrazabilidad($obj){
        $this->_colObjDatoTrazabilidad[] = $obj;
    }

    public function getColObjDatoTrazabilidad(){
        return $this->_colObjDatoTrazabilidad;
    }

    public function cargarPorCodigo($codigo){
        $objTraza = new DatoTrazabilidadExtendido();
        $objTraza->cargarPorCodigo($codigo);
        $this->setColObjDatoTrazabilidad($objTraza);
    }
    
    public function cargarLasTrazasLazzy($arrIds){
    
	foreach($arrIds as $id){
	    $ids.= $id['trazabilidad_id'] . ",";
	}
	
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
        $this->_db->addFrom('datos_trazabilidad');
        $this->_db->addWhere("trazabilidad_id IN (" . $ids . ")" );
	$this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        $frenteProductos = NEW FrenteProductos();
        $frenteProductos->cargarProductosLazzy($resultado);
        $arrObjProductos= $frenteProductos->getColProductos();
        
        $frenteSucursales = new FrenteSucursales();
        $frenteSucursales->cargarSucursal($resultado[0]['sucursal_id']);
        $arrObjSucursal[$resultado[0]['sucursal_id']] = $frenteSucursales;
        
        $frenteAlmacenes = new FrenteAlmacenes;
        $frenteAlmacenes->cargarAlmacenesLazzy($resultado);
        $arrObjAlmacen = $frenteAlmacenes->getColAlmacenes();
        
        $frentEstanterias = new FrenteEstanterias();
        $frentEstanterias->cargarEstanteriasLazzy($resultado);
        $arrObjEstanterias = $frentEstanterias->getColEstanterias();
        
        $frenteLotes = new FrenteLotes();
        $frenteLotes->cargarLotesLazzy($resultado);
        $arrObjLotes = $frenteLotes->getColObjLote();
        
        $frentePresentacion= new FrentePresentacionesProductos();
        $frentePresentacion->cargarPresentacionLazzy($resultado);
        $arrObjPresentacion= $frentePresentacion->getColObjPresentacion();
        
        
        
        foreach($resultado AS $arrTraza){
            $objTraza= NEW DatoTrazabilidadExtendido();
            $objTraza->cargarme($arrTraza);
            $objTraza->setObjProducto($arrObjProductos[$arrTraza['producto_id']]);
            $objTraza->setObjSucursal($arrObjSucursal[$arrTraza['sucursal_id']]);
            $objTraza->setObjAlmacen($arrObjAlmacen[$arrTraza['almacen_id']]);
            $objTraza->setObjEstanteria($arrObjEstanterias[$arrTraza['estanteria_id']]);
            $objTraza->setObjLote($arrObjLotes[$arrTraza['lote_id']]);
            $objTraza->setObjPresentaciones($arrObjLotes[$arrTraza['lote_id']]);
            $objTraza->setObjPresentaciones($arrObjLotes[$arrTraza['presentacion_id']]);
            $this->setColObjDatoTrazabilidad($objTraza);
        }
    }
}

?>
