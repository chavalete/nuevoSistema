<?php
/**
 * class DatoPedido
 * 
 */
abstract class DatoPedido 
{
    private $_pedidoId;
    private $_pedidoFechaHora;
    private $_objCliente;
    private $_pedidoNro;
    private $_pedidoNroExt;
    private $_objEstado;
    private $_pedidoCalle;
    private $_pedidoAltura;
    private $_pedidoPiso;
    private $_pedidodDepto;
    private $_pedidoTelefono;
    private $_codigoPostal;
    private $_objLocalidad;
    private $_objPaciente;
    private $_pedidoObs;
    private $_pedidoUsuarioId;
    private $_pedidoPreparado;
    private $_pedidoPreparadoFechaHora;
    private $_pedidoPreparadoUsuarioId;
    private $_pedidoCancelado;
    private $_pedidoCanceladoFechaHora;
    private $_pedidoCanceladoUsuarioId;
    private $_arrError;
    private $_colObjDetallePedido;
   
    public function setPedidoId($pedidoId){
   	$this->_pedidoId = $pedidoId;
    }
    public function getPedidoId(){
	return $this->_pedidoId;
    }
    public function setPedidoFechaHora($pedidoFechaHora){
	$this->_pedidoFechaHora = $pedidoFechaHora;
    }
    public function getPedidoFechaHora(){
	return $this->_pedidoFechaHora;
    }
    public function setObjCliente($objCliente){
	$this->_objCliente = $objCliente;
    }
    public function getObjCliente(){
	return $this->_objCliente;
    }
    public function setPedidoNro($pedidoNro){
	$this->_pedidoNro = $pedidoNro;
    }
    public function getPedidoNro(){
	return $this->_pedidoNro;
    }
    public function setPedidoNroExt($pedidoNroExt){
	$this->_pedidoNroExt = $pedidoNroExt;
    }
    public function getPedidoNroExt(){
	return $this->_pedidoNroExt;
    }
    public function setObjEstado($objEstado){
	$this->_objEstado = $objEstado;
    }
    public function getObjEstado(){
	return $this->_objEstado;
    }
    public function setPedidoCalle($pedidoCalle){
	$this->_pedidoCalle = $pedidoCalle;
    }
    public function getPedidoCalle(){
	return $this->_pedidoCalle;
    }
    public function setPedidoAltura($pedidoAltura){
	$this->_pedidoAltura = $pedidoAltura;
    }
    public function getPedidoAltura(){
	return $this->_pedidoAltura;
    }
    public function setPedidoPiso($pedidoPiso){
	$this->_pedidoPiso = $pedidoPiso;
    }
    public function getPedidoPiso(){
	return $this->_pedidoPiso;
    }
    public function setPedidoDepto($pedidoDepto){
	$this->_pedidodDepto = $pedidoDepto;
    }
    public function getPedidoDepto(){
	return $this->_pedidodDepto;
    }
    public function setPedidoTelefono($pedidoTelefono){
	$this->_pedidoTelefono = $pedidoTelefono;
    }
    public function getPedidoTelefono(){
	return $this->_pedidoTelefono;
    }
    public function setCodigoPostal($codigoPostal){
	$this->_codigoPostal = $codigoPostal;
    }
    public function getCodigoPostal(){
	return $this->_codigoPostal;
    }
    public function setObjLocalidad($objLocalidad){
	$this->_objLocalidad = $objLocalidad;
    }
    public function getObjLocalidad(){
	return $this->_objLocalidad;
    }
    public function setObjPaciente($objPaciente){
	$this->_objPaciente = $objPaciente;
    }
    public function getObjPaciente(){
	return $this->_objPaciente;
    }
    public function setPedidoObs($pedidoObs){
	$this->_pedidoObs = $pedidoObs;
    }
    public function getPedidoObs(){
	return $this->_pedidoObs;
    }
    public function setPedidoUsuarioId($pedidoUsuarioId){
	$this->_pedidoUsuarioId = $pedidoUsuarioId;
    }
    public function getPedidoUsuarioId(){
	return $this->_pedidoUsuarioId;
    }
    public function setPedidoPreparado($pedidoPreparado){
	$this->_pedidoPreparado = $pedidoPreparado;
    }
    public function getPedidoPreparado(){
	return $this->_pedidoPreparado;
    }
    public function setPedidoPreparadoFechaHora($pedidoPreparadoFechaHora){
	$this->_pedidoPreparadoFechaHora = $pedidoPreparadoFechaHora;
    }
    public function getPedidoPreparadoFechaHora(){
	return $this->_pedidoPreparadoFechaHora;
    }
    public function setPedidoPreparadoUsuarioId($pedidoPreparadoUsuarioId){
	$this->_pedidoPreparadoUsuarioId = $pedidoPreparadoUsuarioId;
    }
    public function getPedidoPreparadoUsuarioId(){
	return $this->_pedidoPreparadoUsuarioId;
    }
    public function setPedidoCancelado($pedidoCancelado){
	$this->_pedidoCancelado = $pedidoCancelado;
    }
    public function getPedidoCancelado(){
	return $this->_pedidoCancelado;
    }
    public function setPedidoCanceladoFechaHora($pedidoCanceladoFechaHora){
	$this->_pedidoCanceladoFechaHora = $pedidoCanceladoFechaHora;
    }
    public function getPedidoCanceladoFechaHora(){
	return $this->_pedidoCanceladoFechaHora;
    }
    public function setPedidoCanceladoUsuarioId($pedidoCanceladoUsuarioId){
	$this->_pedidoCanceladoUsuarioId = $pedidoCanceladoUsuarioId;
    }
    public function getPedidoCanceladoUsuarioId(){
	return $this->_pedidoCanceladoUsuarioId;
    }
    public function setArrError ($error){
        $this->_arrError = $error;
    }
    public function setColObjDetallePedido($objDetallePedido){
        
	$this->_colObjDetallePedido[] = $objDetallePedido;
    }
    
    public function getColObjDetallePedido(){
        
	return $this->_colObjDetallePedido;
    }
    
    public function getArrError (){
        return $this->_arrError;
    }    
    //metodos comunes
    abstract function cargarMe($id);
    abstract function salvarMe($arrParametros);
    abstract function actualizarMe($arrParametros);

}

?>
