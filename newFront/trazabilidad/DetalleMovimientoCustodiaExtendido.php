<?php
/*
 * TIPO DE MOVIMIENTO SALIDA POR ASOCIACION DE CODIGO
 * ID = 8
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/trazabilidad/DetalleMovimiento.php';
require_once 'DatoTrazabilidadExtendido.php';


/**
 * Description of DetalleMovimientoSalidaExtendido
 *
 * @author dMELMAC
 */
class DetalleMovimientoCustodiaExtendido extends DetalleMovimiento
{
    private $_db;
    
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
    }
    
    public function cargarMe($id){
        $this->_db->addSelect('*');
        //$this->_db->addSelect('');
        $this->_db->addFrom('detalle_movimientos');
        $this->_db->addWhere(' detalle_id = ' . $id );
       
        $this->_db->generarSelect();
        $resultado = $this->_db->ejecutar();
        
        //verificamos que el movimiento exista en la db
        if(count($resultado) == 0){
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"El movimiento que intenta cargar no existe"
		    );
	    echo json_encode($arrDevolver);exit;
        }
        
        $this->setDetalleId($resultado[0]['detalle_id']);
        $this->setIdMovimiento($resultado[0]['id_movimiento']);
        $this->setCantidad( $resultado[0]['cantidad']);

        $this->setObjSucursal(new SucursalExtendido());
        $this->getObjSucursal()->cargarMe($resultado[0]['sucursal_id']);

        $this->setObjAlmacen(new AlmacenExtendido());
        $this->getObjAlmacen()->cargarMe($resultado[0]['almacen_id']);

        $this->setObjEstanteria(new EstanteriaExtendido());
        $this->getObjEstanteria()->cargarMe($resultado[0]['estanteria_id']);

        $this->setObjProducto(new ProductoExtendido());
        $this->getObjProducto()->cargarMe($resultado[0]['producto_id']);

        $this->setObjLote(new LoteExtendido());
        $this->getObjLote()->cargarMe($resultado[0]['lote_id']);

    }
    

    public function salvarMe($arrParametros){
        //var_dump($arrParametros);//
        if($arrParametros['id_movimiento'] == NULL ){
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"No envio id de movimiento"
		    );
	    echo json_encode($arrDevolver);exit;
        }
        
        if(count($arrParametros['codigos']) == 0 ){
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"No envio codigos"
		    );
	    echo json_encode($arrDevolver);exit;
        }
        //** set del idMovimiento
        $this->setIdMovimiento($arrParametros['id_movimiento']);
        foreach($arrParametros['codigos'] as $codigo){ 
            $objDatoTraza = new DatoTrazabilidadExtendido();
            $objDatoTraza->cargarPorCodigo($codigo);
            //si no trae registros lo damos de baja
            if(count($objDatoTraza->getArrError())>0){
                $this->setArrError($objDatoTraza->getArrError());
                return;
            }
            //verifico en qeu condicion esta en_stock
            if($objDatoTraza->getEnStock() == true){
                $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>'El codigo : ' . $objDatoTraza->getTrazabilidadCodigo() . ' ya fue dado de alta , verifique los movimientos del mismo.'
		    );
                echo json_encode($arrDevolver);exit;
            }
            
            //echo "-->" .  $objDatoTraza->getObjSucursal()->getSucursalId();exit;
            
            //armo un array con los datos para generar los detalles
            $arrDetalle[$objDatoTraza->getObjSucursal()->getSucursalId()]
                        [$objDatoTraza->getObjAlmacen()->getAlmacenId()]
                        [$objDatoTraza->getObjEstanteria()->getEstanteriaId()]
                        [$objDatoTraza->getObjProducto()->getProductoId()]
                        [$objDatoTraza->getObjLote()->getLoteId()]
                        [] = $codigo;
            $this->setColObjDatoTraza($objDatoTraza);
        }
        
        foreach ($arrDetalle AS $su => $sucursal){
            
            foreach ($sucursal AS $al => $almacen){
                
                foreach($almacen AS $es => $estanteria){
                    
                    foreach($estanteria AS $pr => $producto){
                        
                        foreach($producto AS $lo => $lote){
                            //$this->setCantidad(count($lote));
                            $this->_db->addCamposTabla('id_movimiento,
                                                        sucursal_id, 
                                                        almacen_id,
                                                        estanteria_id');
                            $this->_db->addCamposTabla('producto_id, 
                                                        lote_id, 
                                                        cantidad,
                                                        unidades');
                            $this->_db->addCamposValue($this->getIdMovimiento());
                            $this->_db->addCamposValue($su);
                            $this->_db->addCamposValue($al);
                            $this->_db->addCamposValue($es);
                            $this->_db->addCamposValue($pr);
                            $this->_db->addCamposValue($lo);
                            $this->_db->addCamposValue( '0' );
                            $this->_db->addCamposValue( '0' );
                            $this->_db->addFrom('detalle_movimientos');
                            //genero y ejecuto
                            $this->_db->generarInsert();
                            
                            //echo $this->_db->getQry() .  "<br>";
                            $txtQry = $txtQry . ' ' . $this->_db->getQry();
                        }
                    }
                }
            }
        }
        return $txtQry;
    }
    
   
    public function actualizarMe(){
        echo "sin Codificar";
    }
}