<?php
/*
 * TIPO DE MOVIMIENTO SALIDA POR ASOCIACION DE CODIGO
 * ID = 2
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/trazabilidad/DetalleMovimiento.php';
require_once 'DatoTrazabilidadExtendido.php';


/**
 * Description of DetalleMovimientoSalidaExtendido
 *
 * @author dMELMAC
 */
class DetalleMovimientoSalidaExtendido extends DetalleMovimiento
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


        if($arrParametros['id_movimiento'] == NULL ){
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"No envio id de movimiento"
		    );
	    echo json_encode($arrDevolver);exit;
        }

        $this->setIdMovimiento($arrParametros['id_movimiento']);
        foreach($arrParametros['productos'] as $producto){

		    $this->_db->addSelect('ls.producto_pventa');
		    $this->_db->addSelect('ls.cantidad_promocion');
		    $this->_db->addSelect('ls.precio_promocion');
		    $this->_db->addSelect('dp.producto_pcosto');
			$this->_db->addSelect('dp.es_combo');
		    $this->_db->addFrom('lista_precios_'. $arrParametros['idLista'] . ' AS ls');
		    $this->_db->addFrom('INNER JOIN datos_productos dp USING(producto_id)');
		    $this->_db->addWhere('producto_id = \'' . $producto['productoId']. '\'');
		    $this->_db->generarSelect();

		    //echo $this->_db->getQry();exit;
		    $arrPrecio =  $this->_db->ejecutar();

			if($arrPrecio[0]['cantidad_promocion']>0 && $arrPrecio[0]['precio_promocion']>0 && $producto['cantidad']>=$arrPrecio[0]['cantidad_promocion']){
		        $precioProducto = $arrPrecio[0]['precio_promocion'];
		    }else{
		        $precioProducto = $arrPrecio[0][producto_pventa];
		    }

			if ($arrPrecio[0]['es_combo'] == true) {
				$qry="SELECT lote_id FROM stock_completo WHERE producto_id = {$producto['productoId']};";
				$this->_db->setQry($qry);
				$resultadoLoteCombo = $this->_db->ejecutar();
				$this->setCantidad($producto['cantidad']);
			    $this->setUnidades(1);
			    $this->_db->addCamposTabla('id_movimiento');
			    $this->_db->addCamposTabla('producto_id, lote_id, cantidad,unidades,producto_pventa,detalle_total_fact, producto_pcosto, sucursal_id');
			    $this->_db->addCamposValue($this->getIdMovimiento());
			    $this->_db->addCamposValue($producto['productoId']);
			    $this->_db->addCamposValue($resultadoLoteCombo[0][lote_id]);
			    $this->_db->addCamposValue( $this->getCantidad() * -1 );
			    $this->_db->addCamposValue( $this->getUnidades() * -1 );

		        $this->_db->addCamposValue($precioProducto);
			    $this->_db->addCamposValue(abs($this->getCantidad()) * $precioProducto);

			    $this->_db->addCamposValue($arrPrecio[0][producto_pcosto]);
				$this->_db->addCamposValue($_SESSION['sucursalId']);
			    if($producto['subfamiliaId']>0){
		            $this->_db->addCamposTabla('subfamilia_id');
		            $this->_db->addCamposValue($producto['subfamiliaId']);
			    }

			    $this->_db->addFrom('detalle_movimientos');
			    //genero y ejecuto
			    $this->_db->generarInsert();

			    //echo $this->_db->getQry() .  "<br>";exit;
			    $qryDetalle.=  $this->_db->getQry();

				// 2. Buscamos los productos del combos
				//COMBO ID = AL PRODUCTO COMBO EN DATOS PRODUCTOS
                $qryCombo = "SELECT producto_id, cantidad FROM detalle_productos_combos INNER JOIN datos_productos_combos USING(combo_id) WHERE combo_id = {$producto['productoId']} AND activo=true AND cancelado = false;";
                $this->_db->setQry($qryCombo);
                $resultadoCombo = $this->_db->ejecutar();

				if(count($resultadoCombo)==0){
					$arrDevolver = array(
                          "soyError" => true,
                          "nivel" => 1,
                          "mensaje" => "El combo no se encuentra activo o no fue creado"
                        );
                        echo json_encode($arrDevolver);exit;
				}

				foreach ($resultadoCombo as $componente) {
                    // Cantidad del componente = (Cantidad en la receta) * (Cantidad de combos pedidos)
                    $cantHijoPendiente = $componente['cantidad'] * $producto['cantidad'];

                    // Buscamos lotes del hijo ordenados por vencimiento (FIFO)
                    $qryLoteHijo = "SELECT * FROM stock_completo st INNER JOIN datos_estanterias USING (estanteria_id) INNER JOIN datos_lotes USING (lote_id) WHERE st.producto_id = {$componente['producto_id']} AND cantidad > 0 AND st.sucursal_id = {$_SESSION['sucursalId']} ORDER BY lote_vencimiento, lote_id ASC;";
					//echo $qryLoteHijo;
                    $this->_db->setQry($qryLoteHijo);
                    $resultadoLoteHijo = $this->_db->ejecutar();

                    if(count($resultadoLoteHijo) == 0){
                        $arrDevolver = array(
                          "soyError" => true,
                          "nivel" => 1,
                          "mensaje" => "Sin stock de uno de los componentes del combo"
                        );
                        echo json_encode($arrDevolver);exit;
                    }

                    foreach ($resultadoLoteHijo as $loteHijo) {
                        if ($cantHijoPendiente < $loteHijo['cantidad']) {
                            $cantidadHijo = $cantHijoPendiente;
                            $cantHijoPendiente = 0;
                        } else {
                            $cantidadHijo = $loteHijo['cantidad'];
                            $cantHijoPendiente = $cantHijoPendiente - $loteHijo['cantidad'];
                        }

                        $loteHijoId = $loteHijo['lote_id'];

                        // REGISTRO SILENCIOSO: Insertamos en la tabla de trazabilidad_combos en vez de movimientos
                        $qryDetalle .= " INSERT INTO trazabilidad_combos (id_movimiento, combo_id, producto_id, lote_id, cantidad) VALUES ({$this->getIdMovimiento()}, {$producto['productoId']}, {$componente['producto_id']}, {$loteHijoId}, {$cantidadHijo}); ";

                        // Actualizamos el stock real de la Coca / Fernet
                        $qryDetalle .= ' SELECT fn_actualizar_stock_completo('. ($cantidadHijo * -1) . ',' . $loteHijoId . ',' . $componente['producto_id'] . ',' . $_SESSION['sucursalId'] . '); ';
						//echo $qryDetalle;
                        if($cantHijoPendiente == 0){
                            break;
                        }
                    }
                }
			}else{

			    //BUSCARMOS LOTES POR VENCIMIENTO AL MAS PROXIMO
				$qryLote="SELECT * FROM stock_completo st INNER JOIN datos_estanterias USING (estanteria_id) INNER JOIN datos_lotes USING (lote_id) WHERE st.producto_id ={$producto['productoId']} AND cantidad > 0 AND st.sucursal_id = {$_SESSION['sucursalId']} ORDER BY lote_vencimiento, lote_id ASC;";

				//echo $qryLote;
				$this->_db->setQry($qryLote);
				$resultadoLote = $this->_db->ejecutar();
				if(count($resultadoLote)==0){
					$arrDevolver = array(
				      "soyError" => true,
				      "nivel" => 1,
				      "mensaje" =>"Sin stock"
				    );
				    echo json_encode($arrDevolver);exit;
				}
			    $cantPendiente = $producto['cantidad'];
				foreach ($resultadoLote AS $lote){
					if ($cantPendiente < $lote[cantidad]) {
			            $cantidad = $cantPendiente;
						$cantPendiente=0;
					}else{
			            $cantidad  = $lote[cantidad];
			            $cantPendiente =  $cantPendiente  - $lote[cantidad];
					}
					$loteId = $lote[lote_id];
				    $this->setCantidad($cantidad);
				    $this->setUnidades(1);



				    $this->_db->addCamposTabla('id_movimiento');
				    $this->_db->addCamposTabla('producto_id, lote_id, cantidad,unidades,producto_pventa,detalle_total_fact, producto_pcosto, sucursal_id');
				    $this->_db->addCamposValue($this->getIdMovimiento());
				    $this->_db->addCamposValue($producto['productoId']);
				    $this->_db->addCamposValue($loteId);
				    $this->_db->addCamposValue( $this->getCantidad() * -1 );
				    $this->_db->addCamposValue( $this->getUnidades() * -1 );

			        $this->_db->addCamposValue($precioProducto);
				    $this->_db->addCamposValue(abs($this->getCantidad()) * $precioProducto);

				    $this->_db->addCamposValue($arrPrecio[0][producto_pcosto]);
					$this->_db->addCamposValue($_SESSION['sucursalId']);
				    if($producto['subfamiliaId']>0){
			            $this->_db->addCamposTabla('subfamilia_id');
			            $this->_db->addCamposValue($producto['subfamiliaId']);
				    }

				    $this->_db->addFrom('detalle_movimientos');
				    //genero y ejecuto
				    $this->_db->generarInsert();

				    //echo $this->_db->getQry() .  "<br>";exit;
				    $qryDetalle.=  $this->_db->getQry();

				    $qryDetalle.= ' SELECT fn_actualizar_stock_completo('. $this->getCantidad() * -1 . ',' . $loteId . ',' . $producto[productoId] . ','  . $_SESSION['sucursalId'] . '); ';
						if($cantPendiente==0){
							break;
						}
					}
		        }
			}
        //echo $qryDetalle .  "<br>";exit;
        return $qryDetalle;
    }


    public function actualizarMe(){
        echo "sin Codificar";
    }
}
