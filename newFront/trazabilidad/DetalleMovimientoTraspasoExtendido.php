<?php
/*
 * TIPO DE MOVIMIENTO TRASPADO DE PRODUCTOS.
 * ID = 4 y 5
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/trazabilidad/DetalleMovimiento.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/SucursalExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/AlmacenExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/EstanteriaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/ProductoExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/LoteExtendido.php' ;

/**
 * Description of DetalleMovimientoTraspasoExtendido
 *
 * @author root
 */
class DetalleMovimientoTraspasoExtendido extends DetalleMovimiento
{
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
        $qryDetalle = '';
	if($arrParametros['tipo_movimiento_id'] == 4){
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
		
                $objDatoTraza = new DatoTrazabilidadExtendido();
                $objDatoTraza->cargarPorCodigo($codigo);
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
                
                $cantidadAgrupada = 1;
                if($objDatoTraza->getEsAgrupador()){
                    //traigo la cantidad que agrupa.
                    $this->_db->addSelect('mtt.trazabilidad_id');
                    $this->_db->addFrom('datos_movimientos dm ');
                    $this->_db->addFrom('INNER JOIN movimientos_trazabilidad mt USING (id_movimiento) INNER JOIN movimientos_trazabilidad mtt ON (dm.de_id_movimiento=mtT.id_movimiento)');
                    $this->_db->addWhere('mt.trazabilidad_id='.$objDatoTraza->getTrazabilidadId());
                    $this->_db->generarSelect();
                    $resultado = $this->_db->ejecutar();
                    $cantidadAgrupada=count($resultado);
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
                            += $objDatoTraza->getObjProducto()->getUnidades() * $cantidadAgrupada ; //si es agrupador buscar las cantida que agrupa
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
        }elseif($arrParametros['tipo_movimiento_id'] == 5){
            if(count($arrParametros['codigos']) > 0 ){
		$this->setIdMovimiento($arrParametros['id_movimiento']);
                //controlamos que no esten datos de alta con anterioridad en el sistema
                $arrSeries= array();
                foreach ($arrParametros['codigos'] AS $codigo){
                    $arrSeries[]= $codigo; 
                }
                
                $txtCodigos = implode("','", $arrSeries);
                
                $this->_db->addSelect('trazabilidad_codigo');
                $this->_db->addFrom('datos_trazabilidad');
                $this->_db->addWhere("trazabilidad_codigo IN('$txtCodigos') AND en_stock = FALSE AND cancelado = FALSE  ");
                $this->_db->generarSelect();
                //echo $this->_db->getQry();exit;
                $resultado = $this->_db->ejecutar();
                if(count($this->_db->getArrError()) > 0 ){
                    $this->setArrError($this->_db->getArrError());
                    return;
                }
                if(count($resultado) > 0){
                    $txtCodigos = '';
                    foreach($resultado as $codigos){
                        $txtCodigos.= $codigos['trazabilidad_codigo'] . ", ";
                    }
                    $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>$txtCodigos . " ya fue dado de BAJA, verifique los movimientos"
		    );
		      echo json_encode($arrDevolver);exit;
                    
                }
                //cargo la coleccion de trazas
                //var_dump($arrParametros['codigos']);exit;
                foreach($arrParametros['codigos'] as $trazaCodigo){
                    $objTraza = '';
                    $this->_db->addSelect('trazabilidad_id');
                    $this->_db->addFrom('datos_trazabilidad');
                    $this->_db->addWhere("trazabilidad_codigo IN('" . $trazaCodigo ."')  AND cancelado = FALSE  ");
                    $this->_db->generarSelect();
                    $resultado = $this->_db->ejecutar();
                    $objTraza = new DatoTrazabilidadExtendido();
                    $objTraza->cargarme($resultado[0]['trazabilidad_id']);
                    if(count($objTraza->getArrError()) > 0 ){
                        $this->setArrError($objTraza->getArrError());
                        return;
                    }
                    
                    
                    if($objTraza->getEsAgrupador()){
                        //traigo la cantidad que agrupa.
                        $this->_db->addSelect('mtt.trazabilidad_id');
                        $this->_db->addFrom('datos_movimientos dm ');
                        $this->_db->addFrom('INNER JOIN movimientos_trazabilidad mt USING (id_movimiento) INNER JOIN movimientos_trazabilidad mtt ON (dm.de_id_movimiento=mtT.id_movimiento)');
                        $this->_db->addWhere('mt.trazabilidad_id='.$objTraza->getTrazabilidadId());
                        $this->_db->generarSelect();
                        //echo $this->_db->getQry();exit;
                        $resultado = $this->_db->ejecutar();
                        $cantidadAgrupada+=count($resultado);
                    }  else {
                        $cantidadAgrupada=1;
                    }
                    
                    $this->setColObjDatoTraza($objTraza);
                }

                //traigo los datos necesarios para guardar el detalle de la devolucion
                $this->_db->addSelect('count(*) AS cantidad');
                $this->_db->addSelect('dp.unidades');
                $this->_db->addSelect('dt.producto_id');
                $this->_db->addSelect('dt.lote_id');
                $this->_db->addFrom('datos_trazabilidad dt');
                $this->_db->addFrom(' INNER JOIN datos_productos dp USING (producto_id)');
                $this->_db->addWhere("trazabilidad_codigo IN('$txtCodigos')  AND cancelado = FALSE  ");
                $this->_db->addGroup(' dt.producto_id, dt.lote_id, dp.unidades');
                $this->_db->generarSelect();
                //echo $this->_db->getQry();exit;
                $resultado = $this->_db->ejecutar();

                if(count($this->_db->getArrError()) > 0 ){
                    $this->setArrError($this->_db->getArrError());
                    return;
                }
		foreach($resultado AS $datos){
			$this->_db->addCamposTabla('id_movimiento,sucursal_id,almacen_id,estanteria_id,producto_id,lote_id,cantidad,unidades');
			$this->_db->addCamposValue($arrParametros['id_movimiento']);
			$this->_db->addCamposValue($arrParametros['sucursal_id']);
			$this->_db->addCamposValue($arrParametros['almacen_id']);
			$this->_db->addCamposValue($arrParametros['estanteria_id']);
			$this->_db->addCamposValue($datos['producto_id']);
			$this->_db->addCamposValue($datos['lote_id']);
			$this->_db->addCamposValue($datos['cantidad']);
                        $this->_db->addCamposValue($datos['cantidad'] * $datos['unidades'] * $cantidadAgrupada);
			$this->_db->addFrom('detalle_movimientos');
			$this->_db->generarInsert();
			//echo $this->_db->getQry();exit;
			
                        $qryDetalle.= $this->_db->getQry();
                        $qryDetalle.= ' SELECT fn_actualizar_stock_completo('. $datos['cantidad'] *  $datos['unidades'] * $cantidadAgrupada . ',' . $arrParametros['sucursal_id'] . ',' . $arrParametros['almacen_id'] . ',' . $arrParametros['estanteria_id'] . ',' . $datos['lote_id'] . ',' . $datos['producto_id'] . '); ';

		}

                $this->_db->addCamposUpdate('sucursal_id = ' . $arrParametros['sucursal_id']);
                $this->_db->addCamposUpdate('almacen_id = ' . $arrParametros['almacen_id']);
                $this->_db->addCamposUpdate('estanteria_id = ' . $arrParametros['estanteria_id']);
                $this->_db->addFrom('datos_trazabilidad');
                $this->_db->addWhere("trazabilidad_codigo IN('$txtCodigos')");
                $this->_db->generarUpdate();
                $qryDetalle.= $this->_db->getQry();
                
            }else{
                $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"No se recibieron codigos"
		    );
                echo json_encode($arrDevolver);exit;
            }
        }
        return $qryDetalle;
    }
    
    public function actualizarme(){
            echo "sin codificar";
    }

}

?>
