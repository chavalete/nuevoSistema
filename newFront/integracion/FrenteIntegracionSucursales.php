<?php
//ALMACENAMIENTO
$_SERVER['DOCUMENT_ROOT']='/var/www/html';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/FrenteLotes.php';
require_once 'DatoMovimientoSucursal.php';
require_once 'DetalleMovimientoRecepcionSucursal.php';
/**
 * Description of Integracion entre sucursales
 *
 * @author Chava
 */

class FrenteIntegracionSucursales {

    public $_db;
    public $_dbOrigen;
    public $_colObjMovimiento;

    public function setColObjMovimiento($obj){
        $this->_colObjMovimiento = $obj;
    }
    
    public function getColObjMovimiento(){
        return $this->_colObjMovimiento;
    }
    
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
        $this->_objDetalle = NEW  DetalleMovimientoRecepcionSucursal();
        $this->_objDato = NEW  DatoMovimientoSucursal();

    }

    public function buscarSalidas($baseOrigen){
        if($baseOrigen=='reparto'){
            $deIdSucursal =1;
            $extraCond=null;
        }else{
            $extraCond="AND dm.de_sucursal_id = re.sucursal_id";
            $deIdSucursal =NULL;
        }
        $this->_dbOrigen = new FrenteAlmacenamiento("$baseOrigen");
        $this->_dbOrigen->addSelect('dm.id_movimiento');
        $this->_dbOrigen->addSelect('dm.movimiento_fecha_hora::date AS movimiento_fecha');
        $this->_dbOrigen->addSelect('re.prove_id');
        $this->_dbOrigen->addSelect('dm.remito_nro');
        $this->_dbOrigen->addSelect('dm.a_sucursal_id');
        $this->_dbOrigen->addSelect('dm.de_sucursal_id');
        //$this->_dbOrigen->addSelect('dm.obs');
        $this->_dbOrigen->addSelect('dem.producto_id');
        $this->_dbOrigen->addSelect('dp.producto_gtin');
        $this->_dbOrigen->addSelect('dp.producto_nombre || dp.producto_presentacion AS producto_origen');
        $this->_dbOrigen->addSelect('dp.producto_pcosto');
        $this->_dbOrigen->addSelect('abs(dem.cantidad) AS cantidad');
        $this->_dbOrigen->addSelect("dl.lote_vencimiento AS vencimiento");

        $this->_dbOrigen->addFrom('datos_movimientos dm');
        $this->_dbOrigen->addFrom('INNER JOIN detalle_movimientos dem ON dm.id_movimiento = dem.id_movimiento');
        $this->_dbOrigen->addFrom('INNER JOIN datos_lotes dl ON dem.lote_id = dl.lote_id');
        $this->_dbOrigen->addFrom('INNER JOIN datos_productos dp ON dem.producto_id = dp.producto_id');
        $this->_dbOrigen->addFrom("INNER JOIN relacion_cliente_proveedor_sucursales re ON (dm.cliente_id  = re.cliente_id  $extraCond)");

        $this->_dbOrigen->addWhere('dm.anulado = false');
        $this->_dbOrigen->addWhere('dp.producto_activo = true');
        $this->_dbOrigen->addWhere("dm.movimiento_traspasado=false");
	$this->_dbOrigen->addWhere("dm.traspasar_movimiento=true");
	$this->_dbOrigen->addWhere("dm.impreso=true");
	//$this->_dbOrigen->addWhere("dp.producto_gtin IS NOT NULL");
	$this->_dbOrigen->addWhere("dm.cliente_id IN (963)");

        $this->_dbOrigen->addOrderBy('dm.id_movimiento ASC');

        
        $this->_dbOrigen->generarSelect();
        //echo $this->_dbOrigen->getQry();
        $resultado = $this->_dbOrigen->ejecutar();
        
        if(count($resultado)==0){
            echo "sin registros";
            return;
        }
        var_dump($resultado);
        $idMovimientoAnterior = 0;
        foreach ($resultado AS $data){
            $arrMovimientos[] = $data['id_movimiento'];

            if($data['id_movimiento'] != $idMovimientoAnterior){
                $this->_db->addSelect(seq_id_movimiento_sucursal);
                $this->_db->generarProximo();
                $resultadoSeq = $this->_db->ejecutar();

                $arrParametros['idMovimiento'] = $resultadoSeq[0]['nextval'];
                if($deIdSucursal==null){
                    $deIdSucursal = $data['de_sucursal_id'];
                }
                $arrDato = Array(
                    'id_movimiento' => $arrParametros['idMovimiento'],
                    'prove_id' => $data['prove_id'] ,
                    'tipo_movimiento_id' => 1 ,
                    'remito_nro' => $data['remito_nro'],
                    'movimiento_usuario_id' => 1,
                    'importeEntrada' =>     $importeEntrada,
                    'de_id_movimiento'=>$data['id_movimiento'],
                    'movimiento_fecha'=>$data['movimiento_fecha'],
                    'deIdSucursal'=>$deIdSucursal,
                    'aIdSucursal'=>$data['a_sucursal_id'],
                    'obs'=>$data['obs']

                    );

                $arrQry['dato'].= $this->_objDato->salvarme($arrDato);
                //echo$arrQry['dato'];exit;

            }
            $data['idMovimiento']=$arrParametros['idMovimiento'];
            if($baseOrigen=='reparto'){
                $arrQry['detalle'].=$this->getDetallesReparto($data);
            }else{
                $arrQry['detalle'].=$this->getDetallesLocales($data);
            }
            $idMovimientoAnterior = $data['id_movimiento'];
        }
        //echo $qryDetalle;exit;
        $qryFinal = $arrQry['dato'] . $arrQry['detalle'];
        $this->_db->setQry($qryFinal);
        //var_dump($qryFinal);exit;
        $this->_db->ejecutarTransaccion();
        if(count($this->_db->getArrError()) > 0 ){
            echo $qryFinal;
            echo "Error al salvar los movimientos de Origen";exit;
        //var_dump($this->_db->getArrError()); exit;
        }else{
            $this->_dbOrigen->addFrom('datos_movimientos');
            $this->_dbOrigen->addCamposUpdate('movimiento_traspasado = true');
            $this->_dbOrigen->addCamposUpdate('movimiento_traspasado_fecha_hora = now()');
            $this->_dbOrigen->addWhere('id_movimiento IN (' . implode(",", $arrMovimientos) . ')');
            $this->_dbOrigen->generarUpdate();
            //echo $this->_dbOrigen->getQry();
            $rstdoUpd = $this->_dbOrigen->ejecutar();

            echo "MOVIMIENTOS SALVADOS";
        }
    }

    public function getDetallesReparto($data){
        $arrProducto = $this->buscarRelacionProducto($data['producto_gtin']);
        if($arrProducto[0]['unidades']==0){
            $cantidad=0;
        }else{
            $cantidad = $data['cantidad'] * $arrProducto[0]['unidades'];
        }
        //var_dump($arrProducto);exit;
        //aca se llena el array de detalles del movimiento
        $arrDetalle = Array(
                    'id_movimiento' => $data['idMovimiento'],
                    'sucursal_id' => $data['a_sucursal_id'],
                    'almacen_id' => 1,
                    'estanteria_id' => 1,
                    'producto_id' => $arrProducto[0]['producto_id'],
                    'lote_vencimiento'=> $data['vencimiento'],
                    'cantidad' => $cantidad,
                    'unidades' => $cantidad,
                    'cantidad_origen'=> $data['cantidad'],
                    'producto_origen'=>$data['producto_origen'],
                    'gtin_origen'=>$data['producto_gtin'],
                    'unidades_asociadas'=>$arrProducto[0]['unidades']
                    );
        $qry= $this->_objDetalle->salvarme($arrDetalle);
        return $qry;
    }
    public function getDetallesLocales($data){
        //var_dump($arrProducto);exit;
        //aca se llena el array de detalles del movimiento
        $arrDetalle = Array(
                    'id_movimiento' => $data['idMovimiento'],
                    'sucursal_id' => $data['a_sucursal_id'],
                    'almacen_id' => 1,
                    'estanteria_id' => 1,
                    'producto_id' => $data['producto_id'],
                    'lote_vencimiento'=> $data['vencimiento'],
                    'cantidad' => $data['cantidad'],
                    'unidades' => $data['cantidad'],
                    'cantidad_origen'=> $data['cantidad'],
                    'producto_origen'=>$data['producto_origen'],
                    'gtin_origen'=>$data['producto_gtin'],
                    'unidades_asociadas'=>1
                    );
        $qry= $this->_objDetalle->salvarme($arrDetalle);
        return $qry;
    }

    public function buscarRelacionProducto($gtin){
        $qry="SELECT producto_id, unidades FROM presentaciones_productos WHERE gtin='$gtin';";
        $this->_db->setQry($qry);
        $resultado = $this->_db->ejecutar();
        if(count($resultado)==0){
            $resultado[0]['producto_id']=1;
            $resultado[0]['unidades']=0;
        }elseif($resultado[0]['producto_id']==null){
            $resultado[0]['producto_id']=1;
            $resultado[0]['unidades']=0;
        }
        return $resultado;
    }
}


/*$test = new FrenteIntegracionSucursales();
$test->comenzar();*/
