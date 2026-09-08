<?php
/**
 * class DatoHojaDeRuta
 * 
 */
abstract class DatoHojaDeRuta
{
    private $_hojaId;
    private $_hojaFechaHora;
    private $_objDistribuidor;
    private $_hojaAnulada;
    private $_hojaAnuladaFechaHora;
    private $_hojaAnuladaUsuarioId;
    private $_hojaUsuarioId;
    private $_hojaObs;
    private $_colObjDetalleHdr;
    private $_acompanante;
    private $_vehiculo;
    private $_carga;
    
    private $_colObjDetalleHojaDeRuta;
   
    public function setHojaID($hojaId){
   	$this->_hojaId= $hojaId;
    }
    public function getHojaId(){
	return $this->_hojaId;
    }
    public function setHojaFechaHora($hojaFechaHora){
	$this->_hojaFechaHora = $hojaFechaHora;
    }
    public function getHojaFechaHora(){
	return $this->_hojaFechaHora;
    }
    public function setObjDistribuidor($objDistribuidor){
	$this->_objDistribuidor = $objDistribuidor;
    }
    public function getObjDistribuidor(){
	return $this->_objDistribuidor;
    }
    public function setHojaAnulada($hojaAnulada){
	$this->_hojaAnulada = $hojaAnulada;
    }
    public function getHojaAnulada(){
	return $this->_hojaAnulada;
    }
    public function setHojaAnuladaFechaHora($hojaAnuladaFechaHora){
	$this->_hojaAnuladaFechaHora = $hojaAnuladaFechaHora;
    }
    public function getHojaAnuladaFechaHora(){
	return $this->_hojaAnuladaFechaHora;
    }
    public function setHojaAnuladaUsuarioId($hojaAnuladaUsuarioId){
	$this->_hojaAnuladaUsuarioId =$hojaAnuladaUsuarioId;
    }
    public function getHojaAnuladaUsuarioId(){
	return $this->_hojaAnuladaUsuarioId;
    }
    public function setHojaUsuarioId($hojaUsuarioId){
	$this->_hojaUsuarioId = $hojaUsuarioId;
    }
    public function getHojaUsuarioId(){
	return $this->_hojaUsuarioId;
    }
    public function setHojaObs($hojaObs){
	$this->_hojaObs = $hojaObs;
    }
    public function getHojaObs(){
	return $this->_hojaObs;
    }
    public function setColObjDetalleHdr($objDetalleHdr){
	$this->_colObjDetalleHdr[$objDetalleHdr->getHojaDetalleId()] = $objDetalleHdr;
    }
    
    public function getColObjDetalleHdr(){
        return $this->_colObjDetalleHdr;
    }
    public function setAcompanante($acompanate){
        $this->_acompanante = $acompanate;
    }
    public function getAcompanante(){
        return $this->_acompanante;
    }
    public function setVehiculo($vehiculo){
        $this->_vehiculo = $vehiculo;
    }
    public function getVehiculo(){
        return $this->_vehiculo;
    }
    public function setCarga($nombre){
        $this->_carga = $nombre;
    }
    public function getCarga(){
        return $this->_carga;
    }
    //metodos comunes
    abstract function cargarMe($id);
    abstract function salvarMe($arrParametros);
    abstract function actualizarMe($arrParametros);

}

?>
