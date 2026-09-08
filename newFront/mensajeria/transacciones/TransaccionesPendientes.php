<?php
/**
 * Description of TransaccionesPendientes
 *
 * @author dMELMAC
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/GlnOrigenExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/GlnDestinoExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/stock/ProductoExtendidoAnmat.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/mensajeria/errores/ErrorAnmatExtendido.php';


class TransaccionesPendientes {
    
    private $_db_dM;
    private $_arrError;
    
    private $_idTransaccion;
    private $_idTransaccionAnmat;
    private $_idTransaccionAnmatGlobal;
    private $_fEvento;
    private $_fTransaccion;
    private $_gtin;
    private $_lote;
    private $_numeroSerial;
    private $_nombre;
    private $_dEvento;
    private $_idEstado;
    private $_glnOrigen;
    private $_razonSocialOrigen;
    private $_glnDestino;
    private $_razonSocialDestino;
    private $_nRemito;
    private $_nFactura;
    private $_vencimiento;
    private $_objGlnOrigen;
    private $_objGlnDestino;
    private $_objProducto;
    private $_cantidadRecepcion;
    private $_alertado;
    private $_objErrorAnmat;
    
    

    public function __construct() {
        $this->_db_dM = new FrenteAlmacenamiento();
    }
    
    public function setArrError($arrError){
        $this->_arrError = $arrError;
    }

    public function getArrError(){
        return $this->_arrError;
    }
    
    public function getIdTransaccion(){
        return $this->_idTransaccion;
    }
    
    public function getidTransaccionAnmat(){
        return $this->_idTransaccionAnmat;
    }
    
    public function getidTransaccionAnmatGlobal(){
        return $this->_idTransaccionAnmatGlobal;
    }

    public function getFechaEvento(){
        return $this->_fEvento;
    }

    
    public function getFechaTransaccion(){
        return $this->_fTransaccion;
    }
    
    public function getIdEstado (){
        return $this->_estado;
    }
    
    public function getGtin(){
        return $this->_gtin;
    }
    
    public function getLote (){
        return  $this->_lote;
    }
    public function getNumeroSerial(){
        return $this->_numeroSerial;
    }
    
    public function getNombre (){
        return $this->_nombre;
    }
    public function getDescEvento (){
        return $this->_dEvento; ;
    }
    public function getGlnOrigen(){
        return $this->_glnOrigen;
    }
    public function getRazonSocialOrigen(){
        return $this->_razonSocialOrigen;;
    }
    public function getGlnDestino(){
        return $this->_glnDestino; ;
    }
    public function getRazonSocialDestino(){
        return $this->_razonSocialDestino;
    }
    
    public function getnRemito(){
        return $this->_nRemito;
    }
    
    public function getnFactura(){
        return $this->_nFactura;
    }

    public function getVencimiento(){
        return $this->_vencimiento;
    }

    public function setObjGlnOrigen($objGlnOrigen){

	$this->_objGlnOrigen= $objGlnOrigen;
    }
    public function getObjGlnOrigen(){

	return $this->_objGlnOrigen;
    }
    public function setObjGlnDestino($objGlnDestino){

	$this->_objGlnDestino= $objGlnDestino;
    }
    public function getObjGlnDestino(){

	return $this->_objGlnDestino;
    }
    public function setObjProducto($objProducto){
    
	$this->_objProducto = $objProducto;
    }

    public function getObjProducto(){

	return $this->_objProducto;
    }
    public function setCantidadRecepcion($cantidad){
    
	$this->_cantidadRecepcion = $cantidad;
    }
    public function getCantidadRecepcion(){
    
	return $this->_cantidadRecepcion;
    }
    public function setAlertado($alertado){
    
	$this->_alertado = $alertado;
    }
    public function getAlertado(){
    
	return $this->_alertado;
    }
    public function setObjErrorAnmat($objErrorAnmat){

	$this->_objErrorAnmat = $objErrorAnmat;
    }
    public function getObjErrorAnmat(){

	return $this->_objErrorAnmat;
    }
    public function salvarMe($obj){
        $this->_db_dM->addSelect('seq_transacciones_pendientes_anmat_id_transac');
        $this->_db_dM->generarProximo();
        $resultado = $this->_db_dM->ejecutar();
        $this->_idTransaccion = $resultado[0]['nextval'];
        
        $this->_db_dM->addCamposTabla('id_transaccion');
        $this->_db_dM->addCamposTabla('id_transaccion_anmat');
        $this->_db_dM->addCamposTabla('id_transaccion_anmat_global');
        $this->_db_dM->addCamposTabla('f_evento');
        $this->_db_dM->addCamposTabla('f_transaccion');
        $this->_db_dM->addCamposTabla('gtin');
        $this->_db_dM->addCamposTabla('lote');
        $this->_db_dM->addCamposTabla('numero_serial');
        $this->_db_dM->addCamposTabla('nombre');
        $this->_db_dM->addCamposTabla('d_evento');
        $this->_db_dM->addCamposTabla('id_estado');
        $this->_db_dM->addCamposTabla('gln_origen');
        $this->_db_dM->addCamposTabla('razon_social_origen');
        $this->_db_dM->addCamposTabla('gln_destino');
        $this->_db_dM->addCamposTabla('razon_social_destino');
        $this->_db_dM->addCamposTabla('n_remito');
        $this->_db_dM->addCamposTabla('n_factura');
        $this->_db_dM->addCamposTabla('vencimiento');
        
        $this->_db_dM->addCamposValue($this->_idTransaccion);
        $this->_db_dM->addCamposValue($obj->_id_transaccion);
        $this->_db_dM->addCamposValue($obj->_id_transaccion_global);
        $this->_db_dM->addCamposValue("'" . $obj->_f_evento . "'");
        $this->_db_dM->addCamposValue("'" . $obj->_f_transaccion . "'");
        $this->_db_dM->addCamposValue("'" . $obj->_gtin . "'");
        $this->_db_dM->addCamposValue("'" . $obj->_lote . "'");
        $this->_db_dM->addCamposValue("'" . $obj->_numero_serial . "'");
        $this->_db_dM->addCamposValue("'" . $obj->_nombre . "'");
        $this->_db_dM->addCamposValue("'" . $obj->_d_evento . "'");
        $this->_db_dM->addCamposValue("'" . $obj->_id_estado . "'");
        $this->_db_dM->addCamposValue("'" . $obj->_gln_origen . "'");
        $this->_db_dM->addCamposValue("'" . $obj->_razon_social_origen . "'");
        $this->_db_dM->addCamposValue("'" . $obj->_gln_destino . "'");
        $this->_db_dM->addCamposValue("'" . $obj->_razon_social_destino . "'");
        $this->_db_dM->addCamposValue("'" . $obj->_n_remito . "'");
        $this->_db_dM->addCamposValue("'" . $obj->_n_factura . "'");
        $this->_db_dM->addCamposValue("'" . $obj->_vencimiento . "'");
        
        $this->_db_dM->addFrom('transacciones_pendientes_anmat');
        
        $this->_db_dM->generarInsert();
        //echo $this->_db_dM->getQry();
        $resultadoInsert = $this->_db_dM->ejecutar();
        
        if(count($this->_db_dM->getArrError()) > 0){
            $arrError['detalle'] = "Error al intentar insertar el id_transaccion_anmat = " . $obj->_id_transaccion . " - " . $this->_db_dM->getArrError();
            $arrError['archivo'] =  __FILE__;
            $this->setArrError($arrError);
        }
    }
    
    public function cargarMe($datos){
        
        if(is_numeric($datos)){
	
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}
	
        $this->_idTransaccion = $datos['id_transaccion'];
        $this->_idTransaccionAnmat = $datos['id_transaccion_anmat'];
        $this->_idTransaccionAnmatGlobal = $datos['id_transaccion_anmat_global'];
        $this->_fEvento = $datos['f_evento'];
        $this->_fTransaccion = $datos['f_transaccion'];
        $this->_gtin = $datos['gtin'];
        $this->_lote = $datos['lote'];
        $this->_numeroSerial = $datos['numero_serial'];
        $this->_nombre = $datos['nombre'];
        $this->_dEvento = $datos['d_evento'];
        $this->_idEstado= $datos['id_estado'];
        $this->_glnOrigen = $datos['gln_origen'];
        $this->_razonSocialOrigen = $datos['razon_social_origen'];
        $this->_glnDestino = $datos['gln_destino'];
        $this->_razonSocialDestino = $datos['razon_social_destino'];
        $this->_nRemito = $datos['n_remito'];
        $this->_nFactura = $datos['n_factura'];
        $this->_vencimiento = $datos['vencimiento'];
        $this->setCantidadRecepcion(count($datos));
        $this->setAlertado($datos['alertado']);
        //var_dump($this->getObjGlnOrigen());exit;
    }
    
    public function getDataDB($id){
	
	$this->_db_dM->addSelect('id_transaccion');
        $this->_db_dM->addSelect('id_transaccion_anmat');
        $this->_db_dM->addSelect('id_transaccion_anmat_global');
        $this->_db_dM->addSelect('f_evento');
        $this->_db_dM->addSelect('f_transaccion');
        $this->_db_dM->addSelect('gtin');
        $this->_db_dM->addSelect('lote');
        $this->_db_dM->addSelect('numero_serial');
        $this->_db_dM->addSelect('nombre');
        $this->_db_dM->addSelect('d_evento');
        $this->_db_dM->addSelect('id_estado');
        $this->_db_dM->addSelect('gln_origen');
        $this->_db_dM->addSelect('razon_social_origen');
        $this->_db_dM->addSelect('gln_destino');
        $this->_db_dM->addSelect('razon_social_destino');
        $this->_db_dM->addSelect('n_remito');
        $this->_db_dM->addSelect('n_factura');
        $this->_db_dM->addSelect('vencimiento');
        $this->_db_dM->addSelect('alertado');
        
        
        $this->_db_dM->addFrom('transacciones_pendientes_anmat');
        $this->_db_dM->addWhere("id_transaccion_anmat_global =" . $id);
        
        $this->_db_dM->generarSelect();
        //echo $this->_db_dM->getQry();
        
        $resultado = $this->_db_dM->ejecutar();
        //var_dump($resultado);exit;
        if(count($resultado) == 0){
            $arrError['detalle'] = "No existe la transaccion pendiente que intenta cargar(" . $id . ")";
            $arrError['archivo'] = __FILE__;
            $this->setArrError($arrError);
            return;
        }
    }
    public function cargarPorIdAnmat($id){
        $this->_db_dM->addSelect('id_transaccion');
        $this->_db_dM->addSelect('id_transaccion_anmat');
        $this->_db_dM->addSelect('id_transaccion_anmat_global');
        $this->_db_dM->addSelect('f_evento');
        $this->_db_dM->addSelect('f_transaccion');
        $this->_db_dM->addSelect('gtin');
        $this->_db_dM->addSelect('lote');
        $this->_db_dM->addSelect('numero_serial');
        $this->_db_dM->addSelect('nombre');
        $this->_db_dM->addSelect('d_evento');
        $this->_db_dM->addSelect('id_estado');
        $this->_db_dM->addSelect('gln_origen');
        $this->_db_dM->addSelect('razon_social_origen');
        $this->_db_dM->addSelect('gln_destino');
        $this->_db_dM->addSelect('razon_social_destino');
        $this->_db_dM->addSelect('n_remito');
        $this->_db_dM->addSelect('n_factura');
        $this->_db_dM->addSelect('vencimiento');
        $this->_db_dM->addSelect('alertado');
        $this->_db_dM->addSelect('error_id');
        
        $this->_db_dM->addFrom('transacciones_pendientes_anmat');
        $this->_db_dM->addWhere("id_transaccion_anmat =" . $id);
        
        $this->_db_dM->generarSelect();
        //echo $this->_db_dM->getQry();
        $resultado = $this->_db_dM->ejecutar();
        
        if(count($resultado) == 0){
            $arrError['detalle'] = "No existe la transaccion pendiente que intenta cargar(" . $id . ")";
            $arrError['archivo'] = __FILE__;
            $this->setArrError($arrError);
            return;
        }
        
        $this->_idTransaccion = $resultado[0]['id_transaccion'];
        $this->_idTransaccionAnmat = $resultado[0]['id_transaccion_anmat'];
        $this->_idTransaccionAnmatGlobal = $resultado[0]['id_transaccion_anmat_global'];
        $this->_fEvento = $resultado[0]['f_evento'];
        $this->_fTransaccion = $resultado[0]['f_transaccion'];
        $this->_gtin = $resultado[0]['gtin'];
        $this->_lote = $resultado[0]['lote'];
        $this->_numeroSerial = $resultado[0]['numero_serial'];
        $this->_nombre = $resultado[0]['nombre'];
        $this->_dEvento = $resultado[0]['d_evento'];
        $this->_idEstado= $resultado[0]['id_estado'];
        $this->_glnOrigen = $resultado[0]['gln_origen'];
        $this->_razonSocialOrigen = $resultado[0]['razon_social_origen'];
        $this->_glnDestino = $resultado[0]['gln_destino'];
        $this->_razonSocialDestino = $resultado[0]['razon_social_destino'];
        $this->_nRemito = $resultado[0]['n_remito'];
        $this->_nFactura = $resultado[0]['n_factura'];
        $this->_vencimiento = $resultado[0]['vencimiento'];
        $this->setObjGlnDestino(new GlnDestinoExtendido());
	$this->getObjGlnDestino()->cargarme($resultado[0]['gln_destino']);
	$this->setAlertado($resultado[0]['alertado']);
	$this->setObjErrorAnmat(new ErrorAnmatExtendido());
	$this->getObjErrorAnmat()->cargarme($resultado[0]['error_id']);
        
    }
    public function grabarErrorAnmat($error){
        //llamo a la clase que maneja el error y lo guarda
        $errorANMAT = new ErrorAnmatExtendido();
        $errorANMAT->salvarme($error[0]->return->errores);
        //guado el id del error
        $this->_db_dM->addCamposUpdate('error_id = ' . $errorANMAT->getErrorId() /* seq de error */);
        $this->_db_dM->addCamposUpdate('alertado_fecha_hora = current_timestamp');
        $this->_db_dM->addFrom('transacciones_pendientes_anmat');
        $this->_db_dM->addWhere('id_transaccion = ' . $this->getIdTransaccion());
        $this->_db_dM->generarUpdate();
        
        $this->_db_dM->ejecutar();
        
        if(count($this->_db_dM->getArrError()) > 0 ){
            $this->setArrError($this->_db_dM->getArrError());            
        }
    }
    public function actualizarMe(){
        
    }
}

