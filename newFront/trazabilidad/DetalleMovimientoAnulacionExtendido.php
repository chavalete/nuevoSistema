<?php

/*
 * TIPO DE MOVIMIENTO SALIDA POR ASOCIACION DE CODIGO
 * ID = 2
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/trazabilidad/DetalleMovimiento.php';
require_once 'DatoTrazabilidadExtendido.php';
/**
 * Description of DetalleMovimientoAnulacionExtendido
 *
 * @author root
 */
class DetalleMovimientoAnulacionExtendido extends DetalleMovimiento
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
        //echo $this->_db->getQry();
        $resultado = $this->_db->ejecutar();
        
        //verificamos que el movimiento exista en la db
        if(count($resultado) == 0){
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"EL movimiento que intenta cargar no existe"
		    );
		  echo json_encode($arrDevolver);exit;
        }
        
        $this->setDetalleId($resultado[0]['detalle_id']);
        $this->setIdMovimiento($resultado[0]['id_movimiento']);
        $this->setCantidad($resultado[0]['cantidad']);

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
	//var_dump($arrParametros);
            if($arrParametros['id_movimiento'] == NULL ){
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"No envio el id de movimiento para el detalle"
		    );
		  echo json_encode($arrDevolver);exit;
        }
        //** set del idMovimiento
        $this->setIdMovimiento($arrParametros['id_movimiento']);
        
        //traigo los codigos relacionados a la operacion Extendido()triunfo
        $this->_db->addSelect('st.almacen_id, st.sucursal_id,st.estanteria_id,lote_id,sum(st.cantidad) AS cantidadStock, de.cantidad AS cantidadAnular, de.producto_id');
        $this->_db->addFrom('detalle_movimientos de INNER JOIN stock_completo st USING(lote_id)');
        $this->_db->addWhere('id_movimiento =' . $arrParametros['de_id_movimiento']);
        $this->_db->addGroup(' de.cantidad, lote_id, st.estanteria_id, st.almacen_id, st.sucursal_id, de.producto_id');      
        $this->_db->addOrderBy('de.producto_id');
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        
        foreach($resultado as $producto){
	    
            if($producto['cantidadstock'] >= $producto['cantidadanular']){
            //var_dump($producto);
            $cantidad = $producto['cantidadanular'];
            }else{
            $arrDevolver = array(
                  "soyError" => true,
                  "nivel" => 1,
                  "mensaje" =>"No hay stock suficiente del lote a anular"
                );
              echo json_encode($arrDevolver);exit;
            }


            $this->_db->addCamposTabla('id_movimiento,
                        sucursal_id,
                        almacen_id,
                        estanteria_id');
            $this->_db->addCamposTabla('producto_id,
                        lote_id,
                        cantidad,
                        unidades');
            $this->_db->addCamposValue($this->getIdMovimiento());
            $this->_db->addCamposValue($producto['sucursal_id']);
            $this->_db->addCamposValue($producto['almacen_id']);
            $this->_db->addCamposValue($producto['estanteria_id']);
            $this->_db->addCamposValue($producto['producto_id']);
            $this->_db->addCamposValue($producto['lote_id']);
            $this->_db->addCamposValue( $cantidad * -1 );
            $this->_db->addCamposValue( $cantidad * -1 );
            $this->_db->addFrom('detalle_movimientos');
            //genero y ejecuto
            $this->_db->generarInsert();
            $qryDetalle.=  $this->_db->getQry();

            $objProducto = NEW ProductoExtendido();
            $objProducto->cargarMe($producto['producto_id']);

            if($objProducto->getEsCombo()){
                $qry="SELECT tz.producto_id,tz.lote_id,dpc.cantidad FROM trazabilidad_combos tz INNER JOIN detalle_productos_combos dpc USING(combo_id) WHERE id_movimiento={$arrParametros['de_id_movimiento']} GROUP BY tz.producto_id, tz.lote_id,dpc.cantidad;";
                $this->_db->setQry($qry);
                $resultado = $this->_db->ejecutar();
                foreach($resultado AS $data){
                  $cantidad = $data['cantidad'] * $cantidad;
                  $qryDetalle.= ' SELECT fn_actualizar_stock_completo('. $cantidad*-1 . ',' .  $data['lote_id'] . ',' . $data['producto_id'] . ','  . $_SESSION['sucursalId'] . '); ';
                }
            }else{
                $qryDetalle.= ' SELECT fn_actualizar_stock_completo('. $cantidad * -1 . ',' . $producto['lote_id'] . ',' . $producto['producto_id'] . ','  . $_SESSION['sucursalId'] . '); ';
            }
        }
        //echo $qryDetalle;exit;
	return $qryDetalle;
    }
    
   
    public function actualizarMe(){
        echo "sin Codificar";
    }


}




