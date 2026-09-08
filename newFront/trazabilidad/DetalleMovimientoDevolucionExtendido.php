<?php
/*
 * TIPO DE MOVIMIENTO DEVOLUCION DE PRODUCTOS.
 * ID = 3
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/trazabilidad/DetalleMovimiento.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/SucursalExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/AlmacenExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/EstanteriaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/ProductoExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/LoteExtendido.php' ;

/**
 * Description of DetalleMovimientoDevolucionExtendido
 *
 * @author dMELMAC 
 */
class DetalleMovimientoDevolucionExtendido extends DetalleMovimiento
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
		      "mensaje" =>"EL movimiento que intenta cargar no existe"
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

        
	    public function salvarme($arrParametros){

        //cargo producto por si es combo
        $objProducto = NEW ProductoExtendido();
        $objProducto->cargarMe($arrParametros['producto_id']);
	//var_dump($arrParametros);
	//var_dump($arrParametros);exit;
        //verificar parametro minimos para cualquier operacion de salvado de detalle
        if($arrParametros['id_movimiento'] == NULL ){
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"No envio id de movimiento"
		    );
	    echo json_encode($arrDevolver);exit;
        }

        if($arrParametros['producto_id'] == NULL ){
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"No envio id de producto"
		    );
	    echo json_encode($arrDevolver);exit;
        }
        
        if($arrParametros['lote_id'] == NULL ){
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"No envio id de lote"
		    );
	    echo json_encode($arrDevolver);exit;
        }

        if($arrParametros['cantidad'] == 0 ){
	    $arrError['detalle'] = 'El cantidad no se envio';
            $arrError['archivo'] = __FILE__;
            $this->setArrError($arrError);
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"La cantidad debe de ser > 0"
		    );
	    echo json_encode($arrDevolver);exit;
        }
        
        //** set del idMovimiento 
        $this->setIdMovimiento($arrParametros['id_movimiento']);

	//** set cantidad **//
	$this->setCantidad($arrParametros['cantidad']);
	$this->setUnidades($arrParametros['unidades']);
	$txtDetalleError = NULL;
	//instancio datoTraza la misma cantidad de veces que el valor de getCantidad para generar las trazas
	        
        //Armo el insert  // ACORDATE DE CAMBIAR LOS $arrParametros POR LOS SETER QUE SE DECLARAN ARRIBA 
        $this->_db->addCamposTabla('id_movimiento');
        $this->_db->addCamposTabla('producto_id, lote_id, cantidad, unidades,producto_pventa,detalle_total_fact, sucursal_id');
        $this->_db->addCamposValue($this->getIdMovimiento());
        $this->_db->addCamposValue($arrParametros['producto_id']);
        $this->_db->addCamposValue($arrParametros['lote_id']);
        $this->_db->addCamposValue($this->getCantidad());
        $this->_db->addCamposValue($this->getUnidades());
        $this->_db->addCamposValue($arrParametros['producto_pventa']);
        $this->_db->addCamposValue($arrParametros['producto_pventa']*$arrParametros['cantidad']);
        $this->_db->addCamposValue($_SESSION['sucursalId']);
        $this->_db->addFrom('detalle_movimientos');
        //genero y ejecuto
        $this->_db->generarInsert();
        //print_r($this->_db->getQry());        exit;
        $qryDetalle =  $qryDatoTraza . $this->_db->getQry();

        if($objProducto->getEsCombo()){
          $qry="SELECT tz.producto_id,tz.lote_id,dpc.cantidad FROM trazabilidad_combos tz INNER JOIN detalle_productos_combos dpc USING(combo_id) WHERE id_movimiento={$arrParametros['de_id_movimiento']} GROUP BY tz.producto_id, tz.lote_id,dpc.cantidad;";
          $this->_db->setQry($qry);
          $resultado = $this->_db->ejecutar();
          $antProdu=NULL;
          foreach($resultado AS $data){
            if($antProdu!=$data['producto_id']){
              $cantidad = $data['cantidad'] * $this->getUnidades();
              $qryDetalle.= ' SELECT fn_actualizar_stock_completo('. $cantidad . ',' .  $data['lote_id'] . ',' . $data['producto_id'] . ','  . $_SESSION['sucursalId'] . '); ';
            }
            $antProdu = $data['producto_id'];
          }
        }else{
          $qryDetalle.= ' SELECT fn_actualizar_stock_completo('. $this->getUnidades() . ',' .  $arrParametros['lote_id'] . ',' . $arrParametros['producto_id'] . ','  . $_SESSION['sucursalId'] . '); ';
        }
        $qryDetalle.="UPDATE detalle_movimientos SET cantidad = cantidad + $arrParametros[cantidad]  WHERE id_movimiento = $arrParametros[de_id_movimiento] AND producto_id = $arrParametros[producto_id] AND lote_id = {$arrParametros['lote_id']};";
        //echo $qryDetalle;exit;
        return $qryDetalle;
    }
        
        public function actualizarme(){
            echo "sin codificar";
        }


            

}

?>
