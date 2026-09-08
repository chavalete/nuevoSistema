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
class DetalleMovimientoReventaExtendido extends DetalleMovimiento
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
	$this->_db->addSelect('lista_id, vendedor_id'); 

	$this->_db->addFrom('datos_clientes');                                     

	$this->_db->addWhere('cliente_id = \'' . $arrParametros['clienteId']. '\'');                                                 
	$this->_db->generarSelect();    

	//echo $db->getQry();exit;
	$arrLista =  $this->_db->ejecutar();
        
        $this->setIdMovimiento($arrParametros['id_movimiento']);
        foreach($arrParametros['productos'] as $producto){ 

        
	    $this->_db->addSelect('ls.producto_pventa'); 
	    $this->_db->addSelect('dp.producto_comision');
	    $this->_db->addSelect('dp.producto_pcosto');
	    $this->_db->addFrom('lista_precios_'. $arrLista[0]['lista_id'] . ' AS ls');
	    $this->_db->addFrom('INNER JOIN datos_productos dp USING(producto_id)');
	    $this->_db->addWhere('producto_id = \'' . $producto['productoId']. '\'');                                                 
	    $this->_db->generarSelect();    

	    //echo $this->_db->getQry();exit;
	    $arrPrecio =  $this->_db->ejecutar();
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
		    $this->_db->addCamposTabla('producto_id, lote_id, cantidad,unidades,producto_pventa,detalle_total_fact, producto_comision, producto_pcosto, sucursal_id');
		    $this->_db->addCamposValue($this->getIdMovimiento());
		    $this->_db->addCamposValue($producto['productoId']);
		    $this->_db->addCamposValue($loteId);
		    $this->_db->addCamposValue( $this->getCantidad() * -1 );
		    $this->_db->addCamposValue( $this->getUnidades() * -1 );
		    $this->_db->addCamposValue($producto[precio]);
		    $this->_db->addCamposValue(abs($this->getCantidad()) * $producto['precio']);
		    if($arrLista['0']['vendedor_id']!=11){
	            $this->_db->addCamposValue($arrPrecio[0][producto_comision]);
	        }else{
	            $this->_db->addCamposValue(0);
	        }
		    $this->_db->addCamposValue($producto['costo']);
		    $this->_db->addCamposValue($_SESSION['sucursalId']);
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
	//echo $qryDetalle;exit;
        return $qryDetalle;
    }
    
   
    public function actualizarMe(){
        echo "sin Codificar";
    }
}
