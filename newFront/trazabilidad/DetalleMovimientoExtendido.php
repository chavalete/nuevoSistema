<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/trazabilidad/DetalleMovimiento.php';
require_once 'DatoTrazabilidadExtendido.php';
/*
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/SucursalExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/AlmacenExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/EstanteriaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/ProductoExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/LoteExtendido.php' ;
*/
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DetallemovimientoAltaExtendido
 *
 * @author root
 */
class DetalleMovimientoExtendido extends DetalleMovimiento
{
    private $_db;
    
    public function __construct() {
        
    
    }
    
    public function cargarme($id){
        $this->_db = new FrenteAlmacenamiento();
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
		      "mensaje" =>" El movimiento que intenta cargar no existe"
		    );
	    echo json_encode($arrDevolver);exit;
        }
        
        $this->setDetalleId($resultado[0]['detalle_id']);
        $this->setIdMovimiento($resultado[0]['id_movimiento']);
        //seters de los objetos qeu faltan (suc, alm, est, prod, lote)
        $this->setCantidad($resultado[0]['cantidad']);
    }
    
    public function salvarme($arrParametros){


        $this->_db = new FrenteAlmacenamiento();
        //verificar parametro minimos de alta
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
            $arrError['detalle'] = 'El campo almacen_id no se envio';
            $arrError['archivo'] = $_SERVER['SCRIPT_NAME'];
            $this->setArrError($arrError);
            return;
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
		      "mensaje" =>"No envio id producto"
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
        if($arrParametros['cantidad'] == NULL && count($arrParametros['arr_codigos']) == 0 ){
            $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" =>"La cantidad debe ser > 0"
		    );
	    echo json_encode($arrDevolver);exit;
        }
        
        //Si trae codigos de origen 
        if(count($arrParametros['arr_codigos']) > 0 ){
            //** seteo la cantidad **//
            $this->setCantidad(count($arrParametros['arr_codigos']));
            //recorro el array de codigos del detalle y salvo el datoTraza
            foreach ($arrParametros['arr_codigos'] as $codigo){
                $arrParametros['codigo'] = $codigo;
                $objDatoTraza = new DatoTrazabilidadExtendido();
                $objDatoTraza->salvarme($arrParametros);
                
                if(count($objDatoTraza->getArrError()) > 0 ){
                   $this->setArrError($objDatoTraza->getArrError());
                    return;
                }
                
                //guardo el objeto en la coleccion 
                $this->setColObjDatoTraza($objDatoTraza);
            }
        //No trae codigos de origen     
        }else{
            //** seteo la cantidad **//
            $this->setCantidad($arrParametros['cantidad']);
            //instancio datoTraza la misma cantidad de veces qeu el valo de getCantidad para generar las trazas
            for($i=0;$i < $this->getCantidad();$i++){
                $objDatoTraza = new DatoTrazabilidadExtendido();
                $objDatoTraza->salvarme($arrParametros);
                
                if(count($objDatoTraza->getArrError()) > 0 ){
                    $this->setArrError($objDatoTraza->getArrError());
                    return;
                }
                
                //guardo el objeto en la coleccion
                $this->setColObjDatoTraza($objDatoTraza);   
            }
        }
        
        
        
        
        
        //si llego todo bien seteo los propiedades del objeto
        //** cantidad y colObjTraza ya fueron seteados **
        $this->setIdMovimiento($arrParametros['id_movimiento']);
        
        
        
        //Armo el insert  // ACORDATE DE CAMBIAR LOS $arrParametros POR LOS SETER QUE SE DECLARAN ARRIBA 
        $this->_db->addCamposTabla('id_movimiento, sucursal_id, almacen_id,estanteria_id');
        $this->_db->addCamposTabla('producto_id, lote_id, cantidad');
        $this->_db->addCamposValue($arrParametros['id_movimiento']);
        $this->_db->addCamposValue($arrParametros['sucursal_id']);
        $this->_db->addCamposValue($arrParametros['almacen_id']);
        $this->_db->addCamposValue($arrParametros['estanteria_id']);
        $this->_db->addCamposValue($arrParametros['producto_id']);
        $this->_db->addCamposValue($arrParametros['lote_id']);
        $this->_db->addCamposValue($this->getCantidad());
        $this->_db->addFrom('detalle_movimientos');
        //genero y ejecuto
        $this->_db->generarInsert();
        //print_r($this->_db->getQry());        
        $resultado = $this->_db->ejecutar();        
        
    }
    public function actualizarme(){
        
    }
}

?>
