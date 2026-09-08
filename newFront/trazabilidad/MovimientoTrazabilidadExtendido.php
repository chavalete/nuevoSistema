<?php
//Almacenamiento
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
//Libreria/trazabilidad/
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/trazabilidad/MovimientoTrazabilidad.php';

//Trazabilidad
require_once 'TipoMovimientoTrazaExtendido.php';

//cabecera
require_once 'DatoMovimientoExtendido.php';

// detalle movimientos de entrada
require_once 'DetalleMovimientoRecepcionExtendido.php';
require_once 'DetalleMovimientoDevolucionExtendido.php';
require_once 'DetalleMovimientoTraspasoExtendido.php';
require_once 'DetalleMovimientoAnulacionExtendido.php';
require_once 'DetalleMovimientoRenovacionExtendido.php';
require_once 'DetalleMovimientoCustodiaExtendido.php';
require_once 'DetalleMovimientoInicioAlqCustExtendido.php';
require_once 'DetalleMovimientoRoboRoturaExtendido.php';
require_once 'DetalleMovimientoConsolidadoExtendido.php';
require_once 'DetalleMovimientoPorProduccionExtendido.php';
require_once 'DetalleMovimientoDesconsolidadoExtendido.php'; 

//detalle movimientos de salida
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/trazabilidad/DetalleMovimientoSalidaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/trazabilidad/DetalleMovimientoReventaExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/trazabilidad/DetalleMovimientoPedidoExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/trazabilidad/DetalleMovimientoOrdenTrabajoExtendido.php';


class MovimientoTrazabilidadExtendido extends MovimientoTrazabilidad
{
    private $_db;
    
    private $_miTotal; // para acumular el total en la fila
    
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
    }
    
    public function setTotal($total){
        $this->_miTotal = $total;
    }
    
    public function getTotal(){
        return $this->_miTotal;
    }
    
    public function cargarMe($id) {
        $this->_db->addSelect(' to_char(dm.movimiento_fecha_hora::date,\'DD-MM-YYYY\') AS movimiento_fecha_hora,
                                to_char(mt.fecha_hora,\'DD-MM-YYYY HH24:MI\') AS movimiento_traza_fecha_hora,
                                dm.tipo_movimiento_id, 
                                dm.id_movimiento, 
                                dem.detalle_id ');
        $this->_db->addFrom('detalle_movimientos dem
                            INNER JOIN datos_movimientos dm USING (id_movimiento) 
                            INNER JOIN movimientos_trazabilidad mt USING (id_movimiento) ');
                            
        $this->_db->addWhere('detalle_id = ' . $id);
        $this->_db->generarSelect();
        //echo $this->_db->getQry();        exit();
        $resultado = $this->_db->ejecutar();
        
        $this->setMovimientoFechaHora($resultado[0]['movimiento_traza_fecha_hora']);
        
        $this->setObjTipoMovimiento(new TipoMovimientoTrazaExtendido());
        $this->getObjTipoMovimiento()->cargarMe($resultado[0]['tipo_movimiento_id']);
        if(count($this->getObjTipoMovimiento()->getArrError()) > 0){
            $this->setArrError($this->getObjTipoMovimiento()->getArrError());
            return;
        }
        
        $this->setObjDatoMovimiento(new DatoMovimientoExtendido());
        $this->getObjDatoMovimiento()->cargarMe($resultado[0]['id_movimiento']);
        if(count($this->getObjDatoMovimiento()->getArrError()) > 0){
            //print_r($this->getObjDatoMovimiento()->getArrError());
            $this->setArrError($this->getObjDatoMovimiento()->getArrError());
            return;
        }
        
        switch ($this->getObjTipoMovimiento()->getTipoMovimientoId()){
            case 1 :
                $this->setObjDetalleMovimiento(new DetalleMovimientoRecepcionExtendido());
                $this->getObjDetalleMovimiento()->cargarMe($resultado[0]['detalle_id']);
                if(count($this->getObjDetalleMovimiento()->getArrError()) > 0){
                    $this->setArrError($this->getObjdetalleMovimiento()->getArrError());
                    return;
                }
                break;
            case 2 :
                $this->setObjDetalleMovimiento(new DetalleMovimientoSalidaExtendido());
                $this->getObjDetalleMovimiento()->cargarMe($resultado[0]['detalle_id']);
                if(count($this->getObjDetalleMovimiento()->getArrError()) > 0){
                    $this->setArrError($this->getObjdetalleMovimiento()->getArrError());
                    return;
                }
                break;
            case 3 :
                $this->setObjDetalleMovimiento(new DetalleMovimientoDevolucionExtendido());
                $this->getObjDetalleMovimiento()->cargarMe($resultado[0]['detalle_id']);
                if(count($this->getObjDetalleMovimiento()->getArrError()) > 0){
                    $this->setArrError($this->getObjdetalleMovimiento()->getArrError());
                    return;
                }
                break;
            case 4 :
                $this->setObjDetalleMovimiento(new DetalleMovimientoTraspasoExtendido());
                $this->getObjDetalleMovimiento()->cargarMe($resultado[0]['detalle_id']);
                if(count($this->getObjDetalleMovimiento()->getArrError()) > 0){
                    $this->setArrError($this->getObjdetalleMovimiento()->getArrError());
                    return;
                }
                break;
            case 5 :
                $this->setObjDetalleMovimiento(new DetalleMovimientoTraspasoExtendido());
                $this->getObjDetalleMovimiento()->cargarMe($resultado[0]['detalle_id']);
                if(count($this->getObjDetalleMovimiento()->getArrError()) > 0){
                    $this->setArrError($this->getObjdetalleMovimiento()->getArrError());
                    return;
                }
                break;
            case 6 :
                $this->setObjDetalleMovimiento(new DetalleMovimientoAnulacionExtendido());
                $this->getObjDetalleMovimiento()->cargarMe($resultado[0]['detalle_id']);
                if(count($this->getObjDetalleMovimiento()->getArrError()) > 0){
                    $this->setArrError($this->getObjdetalleMovimiento()->getArrError());
                    return;
                }
                break;
           case 7 :
                $this->setObjDetalleMovimiento(new DetalleMovimientoRenovacionExtendido());
                $this->getObjDetalleMovimiento()->cargarMe($resultado[0]['detalle_id']);
                if(count($this->getObjDetalleMovimiento()->getArrError()) > 0){
                    $this->setArrError($this->getObjdetalleMovimiento()->getArrError());
                    return;
                }
                break;
          case 8 :
                $this->setObjDetalleMovimiento(new DetalleMovimientoCustodiaExtendido());
                $this->getObjDetalleMovimiento()->cargarMe($resultado[0]['detalle_id']);
                if(count($this->getObjDetalleMovimiento()->getArrError()) > 0){
                    $this->setArrError($this->getObjdetalleMovimiento()->getArrError());
                    return;
                }
                break;
          case 9 :
                $this->setObjDetalleMovimiento(new DetalleMovimientoInicioAlqCustExtendido());
                $this->getObjDetalleMovimiento()->cargarMe($resultado[0]['detalle_id']);
                if(count($this->getObjDetalleMovimiento()->getArrError()) > 0){
                    $this->setArrError($this->getObjdetalleMovimiento()->getArrError());
                    return;
                }
                break;
          case 10 :
                $this->setObjDetalleMovimiento(new DetalleMovimientoRoboRoturaExtendido());
                $this->getObjDetalleMovimiento()->cargarMe($resultado[0]['detalle_id']);
                if(count($this->getObjDetalleMovimiento()->getArrError()) > 0){
                    $this->setArrError($this->getObjdetalleMovimiento()->getArrError());
                    return;
                }
                break;
          case 11 :
                $this->setObjDetalleMovimiento(new DetalleMovimientoDesconsolidadoExtendido());
                $this->getObjDetalleMovimiento()->cargarMe($resultado[0]['detalle_id']);
                if(count($this->getObjDetalleMovimiento()->getArrError()) > 0){
                    $this->setArrError($this->getObjdetalleMovimiento()->getArrError());
                    return;
                }
                break;
          case 12 :
                $this->setObjDetalleMovimiento(new DetalleMovimientoDesconsolidadoExtendido());
                $this->getObjDetalleMovimiento()->cargarMe($resultado[0]['detalle_id']);
                if(count($this->getObjDetalleMovimiento()->getArrError()) > 0){
                    $this->setArrError($this->getObjdetalleMovimiento()->getArrError());
                    return;
                }
                break;
          case 13 :
                $this->setObjDetalleMovimiento(new DetalleMovimientoConsolidadoExtendido());
                $this->getObjDetalleMovimiento()->cargarMe($resultado[0]['detalle_id']);
                if(count($this->getObjDetalleMovimiento()->getArrError()) > 0){
                    $this->setArrError($this->getObjdetalleMovimiento()->getArrError());
                    return;
                }
                break;
          case 14 :
                $this->setObjDetalleMovimiento(new DetalleMovimientoConsolidadoExtendido());
                $this->getObjDetalleMovimiento()->cargarMe($resultado[0]['detalle_id']);
                if(count($this->getObjDetalleMovimiento()->getArrError()) > 0){
                    $this->setArrError($this->getObjdetalleMovimiento()->getArrError());
                    return;
                }
                break;
	  case 15 :
                $this->setObjDetalleMovimiento(new DetalleMovimientoOrdenTrabajoExtendido());
                $this->getObjDetalleMovimiento()->cargarMe($resultado[0]['detalle_id']);
                if(count($this->getObjDetalleMovimiento()->getArrError()) > 0){
                    $this->setArrError($this->getObjdetalleMovimiento()->getArrError());
                    return;
                }
                break;
	  case 16 :
                $this->setObjDetalleMovimiento(new DetalleMovimientoPorProduccionExtendido());
                $this->getObjDetalleMovimiento()->cargarMe($resultado[0]['detalle_id']);
                if(count($this->getObjDetalleMovimiento()->getArrError()) > 0){
                    $this->setArrError($this->getObjdetalleMovimiento()->getArrError());
                    return;
                }
                break;
    case 17 :
                $this->setObjDetalleMovimiento(new DetalleMovimientoReventaExtendido());
                $this->getObjDetalleMovimiento()->cargarMe($resultado[0]['detalle_id']);
                if(count($this->getObjDetalleMovimiento()->getArrError()) > 0){
                    $this->setArrError($this->getObjdetalleMovimiento()->getArrError());
                    return;
                }
                break;
        }
    }
    
    public function actualizarMe() {
        echo "lala";
    }
    
    public function salvarMe() {
        echo "lala";
    }
}
