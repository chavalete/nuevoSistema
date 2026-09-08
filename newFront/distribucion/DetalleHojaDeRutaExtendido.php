<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/distribucion/DetalleHojaDeRuta.php';

class DetalleHojaDeRutaExtendido extends DetalleHojaDeRuta
{

    private $_db;
    private $_productoGtin;
    private $_codigoTransaccion;
    private $_importe;

    public function setImporte($importe){
        $this->_importe = $importe;
    }
    public function getImporte(){
        return $this->_importe;
    }
    public function setProductoGtin($productoGtin){

	$this->_productoGtin = $productoGtin;
    }
    public function getProductoGtin(){

	return $this->_productoGtin;
    }

    public function __construct() {

	$this->_db = new FrenteAlmacenamiento();
    }
    public function getArrError(){
        return $this->_arrError;
    }
    public function setArrError($arrError){
        $this->_arrError = $arrError;
    }
    function getDataDB($id){

	$this->_db->addSelect('hoja_id');
	$this->_db->addSelect('pedido_id');
	$this->_db->addSelect('hoja_estado_id');
	$this->_db->addSelect('hoja_estado_usuario_id');
        $this->_db->addFrom('detalle_hojas_de_ruta');
        $this->_db->addWhere('hoja_detalle_id = ' . $id );

        $this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();

        return $resultado[0];

    }
    public function cargarme($datos){

	//var_dump($datos);exit;
        if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}
	$this->setHojaId($resultado['hoja_id']);
    $this->setEstadoFechaHora($resultado['hoja_estado_fecha']);
    $this->setOldEstadoFechaHora($resultado['hoja_old_estado_fecha']);
	$this->setHojaDetalleId($resultado['hoja_detalle_id']);
	$this->setImporte($resultado['importe_cobrado']);
    }

    public function salvarme($arrParametros){

	$this->_db->addCamposTabla('hoja_id');
        $this->_db->addCamposTabla('factura_id');
        $this->_db->addCamposTabla('id_movimiento');
        $this->_db->addCamposTabla('hoja_estado_id');
        $this->_db->addCamposTabla('hoja_estado_usuario_id');
        $this->_db->addCamposTabla('hoja_detalle_id');
        $this->_db->addCamposTabla('importe_cobrado');
        $this->_db->addCamposTabla('soy_deuda');
        $this->_db->addCamposValue($arrParametros['hojaId']);
        $this->_db->addCamposValue($arrParametros['remitoId']);
        $this->_db->addCamposValue($arrParametros['idMovimiento']);
        $this->_db->addCamposValue($arrParametros['hojaEstadoId']);
        $this->_db->addCamposValue($arrParametros['hojaEstadoUsuarioId']);
        $this->_db->addCamposValue($arrParametros['detalleHojaId']);
        $this->_db->addCamposValue($arrParametros['importe']);
        $this->_db->addCamposValue("'" . $arrParametros['soyDeuda'] . "'");

        $this->_db->addFrom('detalle_hojas_de_ruta');

        $this->_db->generarInsert();

        //echo $this->_db->getQry();exit;
        return $this->_db->getQry();

    }
    public function actualizarme($arrParametros){

	//var_dump($arrParametros);
	$this->_db->addCamposUpdate('hoja_old_estado_id = hoja_estado_id');
	$this->_db->addCamposUpdate('hoja_old_estado_usuario_id = hoja_estado_usuario_id');
	$this->_db->addCamposUpdate('hoja_old_estado_fecha = hoja_estado_fecha');
	$this->_db->addCamposUpdate('hoja_estado_id = \'' . $arrParametros['hojaEstadoId'] .'\'');
    $this->_db->addCamposUpdate('forma_pago_id = \'' . $arrParametros['formaId'] .'\'');
	$this->_db->addCamposUpdate('hoja_estado_fecha = now()');
	$this->_db->addCamposUpdate('hoja_estado_usuario_id = \'' . $_SESSION['usuarioId'] .'\'');
	if($arrParametros['importeCobrado']!=null){
        $this->_db->addCamposUpdate('importe_cobrado = \'' . $arrParametros['importeCobrado'] .'\'');
    }

	$this->_db->addFrom('detalle_hojas_de_ruta');
	$this->_db->addWhere('hoja_detalle_id = \'' . $arrParametros['id'] .'\'');

	//generar qry
	$this->_db->generarUpdate();
	//echo $this->_db->getQry();exit;
	//asigamos el resulta que es un array a una variable
	return $this->_db->getQry();
    }
}

