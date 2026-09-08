<?php

abstract class DatoTransaccion
{
    private $_contenedor;
    
    private $_transaccionId;
    private $_transaccionConfirmada;    // tiene su propio metodo de carga
    private $_errorId;                  // tiene su propio metodo de carga
    private $_reprocesadoAnteriorId; // tiene su propio metodo de carga
    private $_reprocesadoPosteriorId; // tiene su propio metodo de carga
    private $_tipoMovimientoId;
    private $_idEvento;
    private $_fechaEvento;
    private $_horaEvento;
    private $_glnOrigen;
    private $_cuitOrigen;
    private $_glnDestino;
    private $_cuitDestino;
    private $_nroRemito;
    private $_nroFactura;    
    private $_errorDesc;
    private $_fechaHoraTransaccion;
    private $_objTipoMovimientoAnmat;
    private $_objErrorAnmat;
    private $_objGlnOrigen;
    private $_objGlnDestino;
    private $_colObjDetalleTransaccion;
    private $_idMovimiento;
    private $_codigoSucursal;
    private $_apellido;
    private $_nombres;
    private $_nroDocumento;
    private $_sexo;
    private $_tipoDocumento;
    private $_direccion;
    private $_localidad;
    private $_nro;
    private $_piso;
    private $_depto;
    private $_nPostal;
    private $_telefono;
    private $_idObraSocial;
    private $_nroAsociado;
    private $_nroReferencia;
    private $_arrError = array();
    private $_disponeNombre;
    private $_esReversa;

    private $_colDetalleTransaccion = Array(); // tiene su propio metodo de carga
    
    public function __construct() 
    {    
    }

    public function setColObjDetalleTransaccion($objDetalleTransaccion){
        
	$this->_colObjDetalleTransaccion[$objDetalleTransaccion->getDetalleTransaccionId()] = $objDetalleTransaccion;
    }
    
    public function getColObjDetalleTransaccion(){
        
	return $this->_colObjDetalleTransaccion;
    }

    public function setCodigoSucursal($codigoSucursal){

	$this->_codigoSucursal = $codigoSucursal;
    }

    public function getCodigoSucursal(){

	return $this->_codigoSucursal;

    }
    

    public function setTransaccionId($id){
        $this->_transaccionId = $id;
    }    
    public function getTransaccionId(){
        return $this->_transaccionId;
    }
    public function setIdMovimiento($id){
        $this->_idMovimiento = $id;
    }
    
    public function getIdMovimiento(){
        return $this->_idMovimiento;
    }
    
    public function setFechaHoraTransaccion($fechaHoraTransaccion){

	    $this->_fechaHoraTransaccion = $fechaHoraTransaccion;

    }
    public function getFechaHoraTransaccion(){

	    return $this->_fechaHoraTransaccion;
    }


    public function setCodigoTransaccion($codigoTransaccion){

	$this->_codigoTransaccion = $codigoTransaccion;
    }
    public function getCodigoTransaccion(){

	return $this->_codigoTransaccion;
    }

    public function setObjTipoMovimiento($objTipoMovimientoAnmat){

	$this->_objTipoMovimientoAnmat = $objTipoMovimientoAnmat;

    }

    public function getObjTipoMovimiento(){

	return $this->_objTipoMovimientoAnmat;
    }

    public function setObjErrorAnmat($objErrorAnmat){
	$this->_objErrorAnmat = $objErrorAnmat;
    }
    public function getObjErrorAnmat(){

	return $this->_objErrorAnmat;
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


    public function setConfirmacionTransaccion($codigoTransaccion){
        $this->_codigoTransaccion=$codigoTransaccion;
        $this->_transaccionConfirmada = 't';
    }   
    public function setReprocesadoAnteriorId($reprocesadoAnteriorId){
        
	$this->_reprocesadoAnteriorId = $reprocesadoAnteriorId;
	
    }
    public function getReprocesadoAnteriorId(){
	
	return $this->_reprocesadoAnteriorId;
    }
    public function setReprocesadoPosteriorId($ReprocesadoPosteriorId){
        $this->_reprocesadoPosteriorId = $reprocesadoPosteriorId;
    }
    public function setErrorId($errorId){
        
	$this->_errorId = $errorId;
    }
    public function getErrorId(){
        
	return $this->_errorId;
    }
    public function setTipoMovimientoId($id){
    
	$this->_tipoMovimientoId = $id;
    }
    public function getTipoMovimientoId(){
	return $this->_tipoMovimientoId;
    }
    public function setFechaEvento($fecha){

	$this->_fechaEvento = $fecha;
    }
    public function getFechaEvento(){
	return $this->_fechaEvento;
    }
    public function setHoraEvento($hora){
	$this->_horaEvento = $hora;
    }
    public function getHoraEvento(){
	return $this->_horaEvento;
    }
    public function setGlnOrigen($gln){
	$this->_glnOrigen = $gln;
    }
    public function getGlnOrigen(){
	return $this->_glnOrigen;
    }
    public function setCuitOrigen($cuit){
	$this->_cuitOrigen = $cuit;
    }
    public function getCuitOrigen(){
	return $this->_cuitOrigen;
    }
    public function setGlnDestino($gln){
	$this->_glnDestino = $gln;
    }
    public function getGlnDestino(){
	return $this->_glnDestino;
    }
    public function setCuitDestino($cuit){
	$this->_cuitDestino = $cuit;
    }
    public function getCuitDestino(){
	return $this->_cuitDestino;
    }
    public function setNroRemito($nroRemito){
	$this->_nroRemito = $nroRemito;
    }
    public function getNroRemito(){
	return $this->_nroRemito;
    }
    public function setNroFactura($nroFactura){
	$this->_nroFactura = $nroFactura;
    }
    public function getNroFactura(){
	return $this->_nroFactura;
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
    public function setNroDocumento($nroDocumento){

	$this->_nroDocumento = $nroDocumento;
    }
    public function getNroDocumento(){

	return $this->_nroDocumento;
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

	return  $this->_direccion;
    }
    public function setLocalidad($localidad){

	$this->_localidad = $localidad;
    }
    public function getLocalidad(){

	return $this->_localidad;
    }
    public function setNro($nro){

	$this->_nro = $nro;
    }
    public function getNro(){

	return $this->_nro;
    }
    public function setPiso($piso){

	$this->_piso = $piso;
    }
    public function getPiso(){

	return $this->_piso;
    }
    public function setDepto($depto){

	$this->_depto = $depto;
    }
    public function getDepto(){

	return $this->_depto;
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
    public function setIdObraSocial($obraSocial){

	$this->_idObraSocial = $obraSocial;
    }
    public function getIdObraSocial(){

	return $this->_idObraSocial;
    }
    public function setNroAsociado($nroAsociado){

	$this->_nroAsociado = $nroAsociado;
    }
    public function getNroAsociado(){

	return $this->_nroAsociado;
    }
    public function setNroReferencia($nroReferencia){

	$this->_nroReferencia = $nroReferencia;
    }
    public function getNroReferencia(){

	return $this->_nroReferencia;
    }
    public function setArrError($arrError){
        $this->_arrError= $arrError;
    }

    public function getArrError(){
        return $this->_arrError;
    }
    public function setDisponeNombre($disponeNombre){

	$this->_disponeNombre = $disponeNombre;
    }
    public function getDisponeNombre(){

	return $this->_disponeNombre;
    }
    public function setIdEvento($idEvento){
    
	$this->_idEvento = $idEvento;
    }
    public function getIdEvento(){
    
	return $this->_idEvento;
    }
    public function setEsReversa($esReversa){
    
	$this->_esReversa = $esReversa;
    }
    public function getEsReversa(){
    
	return $this->_esReversa;
    }
    abstract function cargarMe($id);

    abstract function salvarMe($arrDatos);
    
    abstract function actualizarMe($arrDatos);
}
