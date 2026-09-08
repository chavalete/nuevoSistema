<?php

/**
 * Description of DetalleHojaDeRuta
 *
 * @author dMELMAC
 */
abstract class DetalleHojaDeRuta
{
    private $_hojaId;
    private $_objPedido;
    private $_idMovimiento;
    private $_objEstado;
    private $_estadoFechaHora;
    private $_estadoUsuarioId;
    private $_objOldEstado;
    private $_oldEstadoId;
    private $_oldEstadoFechaHora;
    private $_oldEstadoUsuarioId;
    private $_hojaDetalleId;
    private $_objFactura;
    
    public function setHojaId($hojaId){
	$this->_hojaId = $hojaId;
    }
    public function getHojaId(){
	return $this->_hojaId;
    }
    public function setObjFactura($objFactura){
	$this->_objFactura = $objFactura;
    }
    public function getObjFactura(){
	return $this->_objFactura;
    }
    public function setObjPedido($objPedido){
	$this->_objPedido = $objPedido;
    }
    public function getObjPedido(){
	return $this->_objPedido;
    }
    public function setObjEstado($objEstado){
	$this->_objEstado = $objEstado;
    }
    public function getObjEstado(){
	return $this->_objEstado;
    }
    public function setObjOldEstado($objOldEstado){
	$this->_objOldEstado = $objOldEstado;
    }
    public function getObjOldEstado(){
	return $this->_objOldEstado;
    }
    public function setEstadoFechaHora($estadoFechaHora){
	$this->_estadoFechaHora = $estadoFechaHora;
    }
    public function getEstadoFechaHora(){
	return  $this->_estadoFechaHora;
    }
    public function setEstadoUsuarioId($usuarioId){
	$this->_estadoUsuarioId = $usuarioId;
    }
    public function getEstadoUsuarioId(){
	return $this->_estadoUsuarioId;
    }
    public function setOldEstadoFechaHora($oldEstadoFechaHora){
	$this->_oldEstadoFechaHora = $oldEstadoFechaHora;
    }
    public function getOldEstdoFechaHora(){
	return $this->_oldEstadoFechaHora;
    }
    public function setOldEstadoUsuarioId($usuarioId){
	$this->_oldEstadoUsuarioId = $usuarioId;
    }
    public function getOldEstadoUsuarioId(){
	return $this->_oldEstadoUsuarioId;
    }
    public function setHojaDetalleId($hojaDetalleId){
	$this->_hojaDetalleId = $hojaDetalleId;
    }
    public function getHojaDetalleId(){
	return $this->_hojaDetalleId;
    }
    public function setIdMovimiento($idMovimiento){
	$this->_idMovimiento = $idMovimiento;
    }
    public function getIdMovimiento(){
	return $this->_idMovimiento;
    }
    //metodos comunes
    abstract function cargarMe($id);
    abstract function salvarMe($arrParametros);
    abstract function actualizarMe($arrParametros);

//put your code here
}

?>
