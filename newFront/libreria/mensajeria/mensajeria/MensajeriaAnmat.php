<?php

abstract class MensajeriaAnmat
{

    private $_fechaHoraMovimiento;
    private $_mensajeriaId;
    private $_idMovimientoSc;
    private $_fEvento;
    private $_hEvento;
    private $_objGlnOrigen;
    private $_objGlnDestino;
    private $_cuitOrigen;
    private $_cuitDestino;
    private $_nRemito;
    private $_nFactura;
    private $_vencimiento;
    private $_objProducto;
    private $_lote;
    private $_numeroSerial;
    private $_idTipoMovimientoSc;
    private $_apellido;
    private $_nombres;
    private $_nDocumento;
    private $_sexo;
    private $_tipoDocumento;
    private $_direccion;
    private $_localidad;
    private $_numero;
    private $_piso;
    private $_dpto;
    private $_nPostal;
    private $_telefono;
    private $_idObraSocial;
    private $_objTipoMovimientoAnmat;
    private $_regristoFinalizado;
    private $_regristoTraspasado;
    private $_idAgente;
    private $_soyReproceso;
    private $_soyRemitoDrogFcia;
    private $_idSucursal;
    private $_fechaHoraTrasmision;
    private $_recepcionInformadaFcia;
    private $_fechaHoraTrasmisionRecepFcia;
    private $_dispensaInformadaFcia;
    private $_esAgrupador;
    private $_referenciaNro;
    private $_nroAsociado;
    private $_disponeNombre;
    private $_noInformar;
    private $_noInformarUsuarioId;
    private $_noInformarFechaHora;  

    private $_colObjDetalleMensajeria = Array(); // tiene su propio metodo de carga

    public function __construct(){
    }

    public function setColObjDetalleMensajeria($objDetalleMensajeria){
        
	$this->_colObjDetalleMensajeria[] = $objDetalleMensajeria;
    }
    
    public function getColObjDetalleMensajeria(){
        
	return $this->_colObjDetalleMensajeria;
    }

    public function setFechaHoraMovimiento($fechaHoraMovimiento){

	$this->_fechaHoraMovimiento = $fechaHoraMovimiento;
    }
    public function getFechaHoraMovimiento(){

	return $this->_fechaHoraMovimiento;
    }
    public function setMensajeriaId($mensajeriaId){

	$this->_mensajeriaId = $mensajeriaId;
    }
    public function getMensajeriaId(){

	return $this->_mensajeriaId;
    }
    public function setIdMovimientoSc($idMovimientoSc){

	$this->_idMovimientoSc = $idMovimientoSc;
    }
    public function getIdMovimientoSc(){

	return $this->_idMovimientoSc;
    }
    public function setFechaEvento($fEvento){

	$this->_fEvento = $fEvento;
    }
    public function getFechaEvento(){

	return $this->_fEvento;
    }
    public function setHoraEvento($horaEvento){

	$this->_hEvento = $horaEvento;
    }
    public function getHoraEvento(){

	return $this->_hEvento;
    }
    public function setObjGlnOrigen($objGlnOrigen){

	$this->_objGlnOrigen = $objGlnOrigen;
    }
    public function getObjGlnOrigen(){

	return $this->_objGlnOrigen;
    }
    public function setObjGlnDestino($objGlnDestino){

	$this->_objGlnDestino = $objGlnDestino;
    }
    public function getObjGlnDestino(){

	return $this->_objGlnDestino;
    }
    public function setCuitOrigen($cuitOrigen){

	$this->_cuitOrigen;
    }
    public function getCuitOrigen(){

	return $this->_cuitOrigen;
    }
    public function setCuitDestino($cuitDestino){

	$this->_cuitDestino = $cuitDestino;
    }
    public function getCuitDestino(){

	return $this->_cuitDestino;
    }
    public function setNroRemito($nroRemito){

	$this->_nRemito = $nroRemito;
    }
    public function getNroRemito(){

	return $this->_nRemito;
    }
    public function setNroFactura($nroFactura){

	$this->_nFactura = $nroFactura;
    }
    public function getNroFactura(){

	return $this->_nFactura;
    }
    public function setVencimiento($vencimiento){

	$this->_vencimiento = $vencimiento;
    }
    public function getVencimiento(){

	return $this->_vencimiento;
    }
    public function setObjProducto($objProducto){

	$this->_objProducto = $objProducto;
    }
    public function getObjProducto(){

	return $this->_objProducto;
    }
    public function setLote($lote){

	$this->_lote = $lote;
    }
    public function getLote(){

	return $this->_lote;
    }
    public function setNroSerial($numeroSerial){

	$this->_numeroSerial = $numeroSerial;
    }
    public function getNroSerial(){

	return $this->_numeroSerial;
    }
    public function setObjTipoMovimientoAnmat($objTipoMovimientoAnmat){

	$this->_objTipoMovimientoAnmat = $objTipoMovimientoAnmat;
    }
    public function getObjTipoMovimientoAnmat(){

	return $this->_objTipoMovimientoAnmat;
    }
    public function setIdTipoMovimientoSc($idTipoMovimientoSc){

	$this->_idTipoMovimientoSc = $idTipoMovimientoSc;
    }
    public function getIdTipoMovimientoSc(){

	return $this->_idTipoMovimientoSc;
    }
    public function setApellido($apellido){

	$this->_apellido = $apellido;
    }
    public function getApellido(){

	return $this->_apellido;
    }
    public function setNombres($nombres){

	$this->_nombres = $nombres;
    }
    public function getNombres(){

	return $this->_nombres;
    }
    public function setNroDocumento($nDocumento){

	$this->_nDocumento = $nDocumento;
    }
    public function getNroDocumento(){

	return $this->_nDocumento;
    }
    public function setSexo($sexo){

	$this->_sexo = $sexo;
    }
    public function getSexo(){

	return $this->_sexo;
    }
    public function setTipoDocumento($tipoDocumento){

	$this->_tipoDocumento = $tipoDocumento;
    }
    public function getTipoDocumento(){

	return $this->_tipoDocumento;
    }
    public function setDireccion($direccion){

	$this->_direccion = $direccion;
    }
    public function getDireccion(){

	return $this->_direccion;
    }
    public function setLocalidad($localidad){

	$this->_localidad = $localidad;
    }
    public function getLocalidad(){

	return $this->_localidad;
    }
    public function setNumero($numero){

	$this->_numero = $numero;
    }
    public function getNumero(){

	return $this->_numero;
    }
    public function setPiso($piso){

	$this->_piso = $piso;
    }
    public function getPiso(){

	return $this->_piso;
    }
    public function setDpto($dpto){

	$this->_dpto = $dpto;
    }
    public function getDpto(){

	return $this->_dpto;
    }
    public function setNroPostal($nroPostal){

	$this->_nPostal = $nroPostal;
    }
    public function getNroPostal(){

	return $this->_nPostal;
    }
    public function setTelefono($telefono){

	$this->_telefono = $telefono;
    }
    public function getTelefono(){

	return $this->_telefono;
    }
    public function setIdObraSocial($idObraSocial){

	$this->_idObraSocial = $idObraSocial;
    }
    public function getIdObraSocial(){

	return $this->_idObraSocial;
    }
    public function setRegristoFinalizado($regristoFinalizado){

	$this->_regristoFinalizado = $regristoFinalizado;
    }
    public function getRegristoFinalizado(){

	return $this->_regristoFinalizado;
    }
    public function setRegristoTraspasado($regristroTraspasado){

	$this->_regristoTraspasado = $regristroTraspasado;
    }
    public function getRegristoTraspasado(){

	return $this->_regristoTraspasado;
    }
    public function setIdAgente($idAgente){

	$this->_idAgente = $idAgente;
    }
    public function getIdAgente(){

	return $this->_idAgente;
    }
    public function setSoyReproceso($soyReproceso){

	$this->_soyReproceso = $soyReproceso;
    }
    public function getSoyReproceso(){

	return $this->_soyReproceso;
    }
    public function setSoyRemitoDrogFcia($soyRemitoDrogFcia){

	$this->_soyRemitoDrogFcia = $soyRemitoDrogFcia;
    }
    public function getSoyRemitoDrogFcia(){

	return $this->_soyRemitoDrogFcia;
    }
    public function setIdSucursal($idSucursal){

	$this->_idSucursal = $idSucursal;
    }
    public function getIdSucursal(){

	return $this->_idSucursal;
    }
    public function setFechaHoraTrasmision($fechaHoraTrasmision){

	$this->_fechaHoraTrasmision = $fechaHoraTrasmision;
    }
    public function getFechaHoraTrasmision(){

	return $this->_fechaHoraTrasmision;
    }
    public function setRepecionInformadaFcia($recepcionInformadaFcia){

	$this->_recepcionInformadaFcia = $recepcionInformadaFcia;
    }
    public function getRecepcionInformadaFcia(){

	return $this->_recepcionInformadaFcia;
    }
    public function setFechaHoraTrasmisionRecepFcia($fechaHoraMovimientoRecepFcia){

	$this->_fechaHoraTrasmisionRecepFcia = $fechaHoraMovimientoRecepFcia;
    }
    public function getFechaHoraTrasmisionRecepFcia(){

	return $this->_fechaHoraTrasmisionRecepFcia;
    }
    public function setDispensaInformadaFcia($dispensaInformadaFcia){

	$this->_dispensaInformadaFcia = $dispensaInformadaFcia;
    }
    public function getDispensaInformadaFcia(){

	return $this->_dispensaInformadaFcia;
    }
    public function setEsAgrupador($esAgrupador){

	$this->_esAgrupador = $esAgrupador;
    }
    public function getEsAgrupador(){

	return $this->_esAgrupador;
    }
    public function setReferenciaNro($referenciaNro){

	$this->_referenciaNro = $referenciaNro;
    }
    public function getReferenciaNro(){

	return $this->_referenciaNro;
    }
    public function setNroAsociado($nroAsociado){

	$this->_nroAsociado = $nroAsociado;
    }
    public function setDisponeNombre($disponeNombre){

	$this->_disponeNombre = $disponeNombre;
    }
    public function getDisponeNombre(){

	return $this->_disponeNombre;
    }
    public function setNoInformar($noInformar){

	$this->_noInformar = $noInformar;
    }
    public function getNoInformar(){

	return $this->_noInformar;
    }
    public function setNoInformarUsuarioId($noInformarUsuarioId){

	$this->_noInformarUsuarioId = $noInformarUsuarioId;
    }
    public function getNoInformarUsuarioId(){

	return $this->_noInformarUsuarioId;
    }
    public function setNoInformarFechaHora($noInformarFechaHora){

	$this->_noInformarFechaHora = $noInformarFechaHora;
    }
    public function getNoInformarFechaHora(){

	return $this->_noInformarFechaHora;
    }
    public function setArrError($arrError){
        $this->_arrError = $arrError;
    }
    
    public function getArrError(){
        return $this->_arrError ;
    }
    public function setObjTipoMovimiento($objTipoMovimientoAnmat){

	$this->_objTipoMovimientoAnmat = $objTipoMovimientoAnmat;

    }
    public function getObjTipoMovimiento(){

	return $this->_objTipoMovimientoAnmat;
    }    
    public function cargarme($id){}
    public function actualizarme($arrParametros){}
    public function salvarme($arrParametros){}  
}
 




 
