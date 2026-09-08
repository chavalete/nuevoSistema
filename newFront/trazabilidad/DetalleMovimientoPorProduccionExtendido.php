<?php
/*
 * TIPO DE MOVIMIENTO RECEPCION ENTRADA DE PRODUCTOS
 * ID = 1
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/trazabilidad/DetalleMovimiento.php';
require_once 'DatoTrazabilidadExtendido.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/SucursalExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/AlmacenExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/EstanteriaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/ProductoExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/LoteExtendido.php' ;

/**
 * Description of DetallemovimientoAltaExtendido
 *
 * @author dMELMAC
 */

class DetalleMovimientoPorProduccionExtendido extends DetalleMovimiento
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
		      "mensaje" =>"El id de movimiento no existe"
		    );
	    echo json_encode($arrDevolver);exit;
        }
        
        $this->setDetalleId($resultado[0]['detalle_id']);
        $this->setIdMovimiento($resultado[0]['id_movimiento']);
        //$this->setTrazabilidadCodigo($resultado[0]['trazabilidad_codigo']);
        //seters de los objetos qeu faltan (suc, alm, est, prod, lote)
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

        if($arrParametros['cantidad'] == NULL && count($arrParametros['codigos']) == 0 ){
	    $arrError['detalle'] = 'El cantidad no se envio';
            $arrError['archivo'] = __FILE__;
            $this->setArrError($arrError);
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"La cantidad debe de ser mayor a 0"
		    );
	    echo json_encode($arrDevolver);exit;
        }
        
        if($arrParametros['unidades'] == NULL && count($arrParametros['codigos']) == 0 ){
	    $arrError['detalle'] = 'La unidades no se envio';
            $arrError['archivo'] = __FILE__;
            $this->setArrError($arrError);
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"La unidades deben de ser > 0"
		    );
	    echo json_encode($arrDevolver);exit;
        }
        
        //** set del idMovimiento 
        $this->setIdMovimiento($arrParametros['id_movimiento']);
        $qryDatoTraza = NULL;                
        //Si trae codigos de origen
        if(count($arrParametros['codigos']) > 0 ){
        //echo "entra";
            //** set cantidad **//
            $this->setCantidad(count($arrParametros['codigos']));
            $this->setUnidades(count($arrParametros['codigos']) * $arrParametros['cantidadPresentacion']);
            //recorro el array de codigos asi les genero datoTrazabilidad c/codigo de origen(generada por el proveedor)
            $txtDetalleError = NULL;
            //var_dump($arrParametros[codigos]);
            foreach ($arrParametros['codigos'] as $codigo){
                $arrParametros['codigo'] = $codigo;
                $objDatoTraza = new DatoTrazabilidadExtendido();
                $qryDatoTraza .= $objDatoTraza->salvarme($arrParametros);
                if(count($objDatoTraza->getArrError()) > 0 ){
                   $arrDatoTrazaError = $objDatoTraza->getArrError();
                   $txtDetalleError .= $arrError['detalle'];
                }
                $this->setColObjDatoTraza($objDatoTraza);
            }
            if($txtDetalleError != NULL){
                $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" => $txtDetalleError
		    );
                echo json_encode($arrDevolver);exit;
            }
        //No trae codigos de origen     
        }else{
            //** set cantidad **//
            $this->setCantidad($arrParametros['cantidad']);
            $this->setUnidades($arrParametros['unidades']);
            $txtDetalleError = NULL;
            //instancio datoTraza la misma cantidad de veces que el valor de getCantidad para generar las trazas
            for($i=0;$i < $this->getCantidad();$i++){
                $objDatoTraza = new DatoTrazabilidadExtendido();
                $qryDatoTraza .= $objDatoTraza->salvarme($arrParametros);
                if(count($objDatoTraza->getArrError()) > 0 ){
                   $arrDatoTrazaError = $objDatoTraza->getArrError();
                   $txtDetalleError .= $arrError['detalle'];
                }
                //set el objeto en la coleccion
                $this->setColObjDatoTraza($objDatoTraza);   
            }
            if($txtDetalleError != NULL){
                $arrError['detalle'] = $txtDetalleError;
                $arrError['archivo'] = __FILE__;
                $this->setArrError($arrError);
                return;
            }
        }
        //Armo el insert  // ACORDATE DE CAMBIAR LOS $arrParametros POR LOS SETER QUE SE DECLARAN ARRIBA 
        $this->_db->addCamposTabla('id_movimiento, sucursal_id, almacen_id,estanteria_id');
        $this->_db->addCamposTabla('producto_id, lote_id, cantidad, unidades');
        $this->_db->addCamposValue($this->getIdMovimiento());
        $this->_db->addCamposValue($arrParametros['sucursal_id']);
        $this->_db->addCamposValue($arrParametros['almacen_id']);
        $this->_db->addCamposValue($arrParametros['estanteria_id']);
        $this->_db->addCamposValue($arrParametros['producto_id']);
        $this->_db->addCamposValue($arrParametros['lote_id']);
        $this->_db->addCamposValue($this->getCantidad());
        $this->_db->addCamposValue($this->getUnidades());
        $this->_db->addFrom('detalle_movimientos');
        
    //genero y ejecuto
        $this->_db->generarInsert();
        //print_r($this->_db->getQry());        exit;
        $qryDetalle =  $qryDatoTraza . $this->_db->getQry();
        
        $qryDetalle.= ' SELECT fn_actualizar_stock_completo('. $this->getUnidades() . ',' . $arrParametros['sucursal_id'] . ',' . $arrParametros['almacen_id'] . ',' . $arrParametros['estanteria_id'] . ',' . $arrParametros['lote_id'] . ',' . $arrParametros['producto_id'] . '); ';
        
        return $qryDetalle;
    }
    public function actualizarme(){
        echo "sin codificar";
    }
}
