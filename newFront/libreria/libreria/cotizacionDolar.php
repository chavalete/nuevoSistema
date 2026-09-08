<?php
/*
CLASE DE cotizacionDolar
*/
abstract class CotizacionDolar
{

    private $_cotizacionId; /*integer*/
    private $_cotizacion;/*varchar*/
    private $_cotizacionFechahora;/*date*/
    private $_cotizacionUsuarioId;/*integer*/
    private $_cotizacionProcesada;/*integer*/
    private $_cotizacionProcesadaFechaHora;/*integer*/
    private $_cotizacionProcesadaUsuarioId;/*integer*/	
    private $_obs;/*text*/	

    public function __construct()
    {
    }
    public function setCotizacionId($id){
	$this->_cotizacionId= $id;
    }
    public function getCotizacionId(){
	return $this->_cotizacionId;
    }
    public function setCotizacion($cotizacion){
	$this->_cotizacion= $cotizacion;
    }
    public function getCotizacion(){
	return $this->_cotizacion;
    }
    public function setCotizacionFechaHora($fechaHora){
	$this->_cotizacionFechahora= $fechaHora;
    }
    public function getCotizacionFechaHora(){
	return $this->_cotizacionFechahora;
    }
    public function setCotizacionUsuarioId($usuarioId){
	$this->_cotizacionUsuarioId= $usuarioId;
    }
    public function getCotizacionUsuarioId(){
	return $this->_cotizacionUsuarioId;
    }
    public function setObs($obs) {
	$this->_obs = $obs;
    }
    public function getObs(){
	return $this->_obs;
    }
    public function setCotizacionProcesada($cotizacionProcesada){
	$this->_cotizacionProcesada = $cotizacionProcesada;
    }
    public function getCotizacionProcesada(){
	return $this->_cotizacionProcesada;
    }
    public function setCotizacionProcesadaFechaHora($fechaHora){
	$this->_cotizacionProcesadaFechaHora = $fechaHora;
    }
    public function getCotizacionProcesadaFechaHora(){
	return $this->_cotizacionProcesadaFechaHora;
    }
    public function setCotizacionProcesadaUsuarioId($usuarioId){
	$this->_cotizacionProcesadaUsuarioId = $usuarioId;
    }
    public function getCotizacionProcesadaUsuarioId(){
	return $this->_cotizacionProcesadaUsuarioId;
    }
    abstract public function cargarMe($id);
    
    abstract public function salvarMe($arrParamatros);
    
    abstract public function actualizarMe($arrParamatros);
}