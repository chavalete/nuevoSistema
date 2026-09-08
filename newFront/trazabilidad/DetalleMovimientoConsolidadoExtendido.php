<?php
/*
 * TIPO DE MOVIMIENTO CONSOLIDADO DE CONTENEDORES 
 * ID = 13 y 14
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/trazabilidad/DetalleMovimiento.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/SucursalExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/AlmacenExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/EstanteriaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/ProductoExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/LoteExtendido.php' ;

/**
 * Description of DetalleMovimientoConsolidadoExtendido
 *
 * @author Guillo
 */

class DetalleMovimientoConsolidadoExtendido extends DetalleMovimiento {
    
    private $_db;
    
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
        
    }
    
 
    public function cargarMe($id){
        
        $this->_db->addSelect('*');
        $this->_db->addFrom('detalle_movimientos');
        $this->_db->addWhere(' detalle_id = ' . $id );
       
        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $resultado = $this->_db->ejecutar();
        
        //verificamos que el movimiento exista en la db
        if(count($resultado) == 0){
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"No envio id no existe"
		    );
	    echo json_encode($arrDevolver);exit;
        }
        
        $this->setDetalleId($resultado[0]['detalle_id']);
        $this->setIdMovimiento($resultado[0]['id_movimiento']);
        $this->setCantidad($resultado[0]['cantidad']);
        $this->setUnidades($resultado[0]['unidades']);
        
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
   
        if($arrParametros['tipo_movimiento_id'] == 13){
            $qryDetalle = '';
            if($arrParametros['id_movimiento'] == NULL ){
                $arrDevolver = array(
                      "soyError" => true,
                      "nivel" => 1,
                      "mensaje" =>"No envio id de movimiento"
                    );
                echo json_encode($arrDevolver);exit;
            }
            if($arrParametros['codigos'] == NULL ){
                $arrDevolver = array(
                      "soyError" => true,
                      "nivel" => 1,
                      "mensaje" =>"No se enviaron codigos para el detalle"
                    );
                echo json_encode($arrDevolver);exit;
            }
            //** set del idMovimiento
            $this->setIdMovimiento($arrParametros['id_movimiento']);
            foreach($arrParametros['codigos'] as $codigo){ 
                list($tipo, $gtin, $serie, $vencimiento, $lote, $productoId) = explode("#", $codigo);                            
                $objDatoTraza = new DatoTrazabilidadExtendido();
                $objDatoTraza->cargarPorCodigo($serie);
                //si no trae registros lo damos de baja
                if(count($objDatoTraza->getArrError())>0){
                    $this->setArrError($objDatoTraza->getArrError());
                    return;
                }
                //verifico en qeu condicion esta en_stock
                if($objDatoTraza->getEnStock() == FALSE){
                //echo "entra";
                    $arrDevolver = array(
                      "soyError" => true,
                      "nivel" => 1,
                      "mensaje" =>'El codigo : ' . $objDatoTraza->getTrazabilidadCodigo() . ' ya fue dado de baja con anterioridad, verifique los movimientos del mismo.'
                    );
                    echo json_encode($arrDevolver);exit;
                }
                //echo "-->" .  $objDatoTraza->getObjSucursal()->getSucursalId();exit;
                //armo un array con los datos para generar los detalles

                $arrDetalle[$objDatoTraza->getObjSucursal()->getSucursalId()]
                            [$objDatoTraza->getObjAlmacen()->getAlmacenId()]
                            [$objDatoTraza->getObjEstanteria()->getEstanteriaId()]
                            [$objDatoTraza->getObjProducto()->getProductoId()]
                            [$objDatoTraza->getObjLote()->getLoteId()]['cantidad']
                            += 1;

                $arrDetalle[$objDatoTraza->getObjSucursal()->getSucursalId()]
                            [$objDatoTraza->getObjAlmacen()->getAlmacenId()]
                            [$objDatoTraza->getObjEstanteria()->getEstanteriaId()]
                            [$objDatoTraza->getObjProducto()->getProductoId()]
                            [$objDatoTraza->getObjLote()->getLoteId()]['unidades']
                            += $objDatoTraza->getObjProducto()->getUnidades();
                $this->setColObjDatoTraza($objDatoTraza);

            }
            
            foreach ($arrDetalle AS $su => $sucursal){
                foreach ($sucursal AS $al => $almacen){
                    foreach($almacen AS $es => $estanteria){
                        foreach($estanteria AS $pr => $producto){
                            foreach($producto AS $lo => $lote){

                                //var_dump($lote);exit;
                                $this->setCantidad($lote['cantidad']);
                                $this->setUnidades($lote['unidades']);

                                $this->_db->addCamposTabla('id_movimiento,sucursal_id,almacen_id,estanteria_id');
                                $this->_db->addCamposTabla('producto_id,lote_id,cantidad,unidades');
                                $this->_db->addCamposValue($this->getIdMovimiento());
                                $this->_db->addCamposValue($su);
                                $this->_db->addCamposValue($al);
                                $this->_db->addCamposValue($es);
                                $this->_db->addCamposValue($pr);
                                $this->_db->addCamposValue($lo);
                                $this->_db->addCamposValue( $this->getCantidad() * -1 );
                                $this->_db->addCamposValue( $this->getUnidades() * -1 );

                                $this->_db->addFrom('detalle_movimientos');
                                //genero y ejecuto
                                $this->_db->generarInsert();

                                //echo $this->_db->getQry() .  "<br>";
                                $qryDetalle.=  $this->_db->getQry();
                                $qryDetalle.= ' SELECT fn_actualizar_stock_completo('. $this->getUnidades() * -1 . ',' . $su . ',' . $al . ',' . $es . ',' . $lo . ',' . $pr . '); ';
                            }
                        }
                    }
                }
            }
            return $qryDetalle;
            
        }elseif($arrParametros['tipo_movimiento_id'] == 14){
            
            if($arrParametros['id_movimiento'] == NULL ){
                $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"No envio id de movimiento"
		    );
                echo json_encode($arrDevolver);exit;
            }
            if($arrParametros['sucursal_id'] == NULL ){
                $arrDevolver = array(
                          "soyError" => true,
                          "nivel" => 1,
                          "mensaje" =>"No envio id de sucursal"
                        );
                echo json_encode($arrDevolver);exit;
            }

            if($arrParametros['almacen_id'] == NULL ){
                $arrDevolver = array(
                          "soyError" => true,
                          "nivel" => 1,
                          "mensaje" =>"No envio id de almacen"
                        );
                echo json_encode($arrDevolver);exit;
            }

            if($arrParametros['estanteria_id'] == NULL ){
                $arrDevolver = array(
                          "soyError" => true,
                          "nivel" => 1,
                          "mensaje" =>"No envio id de estanteria"
                        );
                echo json_encode($arrDevolver);exit;
            }
        }
        
        $this->setIdMovimiento($arrParametros['id_movimiento']);
        
        list($tipo, $gtinAnterior, $serie, $vencimiento, $loteAnterior, $productoId) = explode("#", $arrParametros['codigos'][0]);
        foreach ($arrParametros['codigos'] AS $codigo){
            list($tipo, $gtin, $serie, $vencimiento, $lote, $productoId) = explode("#", $codigo);
            $objDatoTraza = new DatoTrazabilidadExtendido();
            $objDatoTraza->cargarPorCodigo($serie);
            //si no trae registros lo damos de baja
            if(count($objDatoTraza->getArrError())>0){
                $this->setArrError($objDatoTraza->getArrError());
                return;
            }
            
            if($gtinAnterior!=$gtin || $loteAnterior != $lote){
                $arrDevolver = array(
                      "soyError" => true,
                      "nivel" => 1,
                      "mensaje" =>"Esta ingresando mas de un lote , verificar los códigos ingresados."
                );
                echo json_encode($arrDevolver);exit;
            }else{
                $gtinAnterior = $gtin;
                $loteAnterior = $lote;
            }
            
            $arrDetalle['unidades']+= $objDatoTraza->getObjProducto()->getUnidades();     
        }
        
        $this->setUnidades($arrDetalle['unidades']);
        
        $objFrenteLotes = new FrenteLotes();
        $arrLote = array('lote' => $lote , 'producto_id' => $productoId);
        $objFrenteLotes->cargarLotePorLoteProducto($arrLote);
        $colObjLotes = $objFrenteLotes->getColObjLote();
        $objLote = array_shift($colObjLotes);
        
        //necesario para generar la traza
        $arrParametros['producto_id'] = $productoId;
        $arrParametros['lote_id'] = $objLote->getLoteId();
        $arrParametros['codigos'] = NULL;
        $arrParametros['es_agrupador'] = TRUE;
        
        $objDatoTraza = new DatoTrazabilidadExtendido();
        $qryDatoTraza = $objDatoTraza->salvarme($arrParametros);
        $this->setColObjDatoTraza($objDatoTraza);
        
        $this->_db->addCamposTabla('id_movimiento, sucursal_id, almacen_id,estanteria_id');
        $this->_db->addCamposTabla('producto_id, lote_id, cantidad, unidades');
        $this->_db->addCamposValue($this->getIdMovimiento());
        $this->_db->addCamposValue($arrParametros['sucursal_id']);
        $this->_db->addCamposValue($arrParametros['almacen_id']);
        $this->_db->addCamposValue($arrParametros['estanteria_id']);
        $this->_db->addCamposValue($arrParametros['producto_id']);
        $this->_db->addCamposValue($arrParametros['lote_id']);
        $this->_db->addCamposValue(1);
        $this->_db->addCamposValue($this->getUnidades());
        $this->_db->addFrom('detalle_movimientos');
        //genero y ejecuto
        $this->_db->generarInsert();
        $qryDetalle =  $qryDatoTraza . $this->_db->getQry();
        $qryDetalle.= ' SELECT fn_actualizar_stock_completo('. $this->getUnidades() . ',' . $arrParametros['sucursal_id'] . ',' . $arrParametros['almacen_id'] . ',' . $arrParametros['estanteria_id'] . ',' . $arrParametros['lote_id'] . ',' . $arrParametros['producto_id'] . '); ';
        return $qryDetalle;
    }
    
    public function actualizarme(){
        echo "sin codificar";
    }
    
}
