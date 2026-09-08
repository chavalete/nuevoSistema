<?php
/**
 * class DatoICobro
 * 
 */
abstract class DatoCobro
{
    private $_cobroId;
    private $_cobroFecha;
    private $_cobroClienteId;
    private $_cobroObs;
    private $_ordenCobroNro;
    private $_cobroUsuarioId;
    private $_objCliente;
    private $_colObjDetalleCobro;
    private $_cobroTotal;
    private $_cobroCancelada;
    private $_cobroCanceladaFechaHora;
    private $_cobroCanceladaUsuarioId;
    private $_arrError;
    
    public function setColObjDetalleCobro($objDetalleCobro){
        $this->_colObjDetalleCobro[] = $objDetalleCobro;
    }
    public function getColObjDetalleCobro(){
        return $this->_colObjDetalleCobro;
    }
    public function setCobroId($cobroId){
        $this->_cobroId = $cobroId;
    }
    public function getCobroId(){
        return $this->_cobroId;
    }
    public function setCobroFecha($cobroFecha){
        $this->_cobroFecha= $cobroFecha;
    }
    public function getCobroFecha(){
        return $this->_cobroFecha;
    }
    public function setOrdenCobroNro($ordenNro){
        $this->_ordenCobroNro = $ordenNro;
    }
    public function getOrdenCobroNro(){
        return $this->_ordenCobroNro;
    }
    public function setCobroClienteId($cobroClienteId){
        $this->_cobroClienteId= $cobroClienteId;
    }
    public function getCobroClienteId(){
        return $this->_cobroClienteId;
    }
    public function setObjCliente($objCliente){
        $this->_objCliente = $objCliente;
    }
    public function getObjCliente(){
        return $this->_objCliente;
    }
    public function setCobroObs($cobroObs){
	//echo $cobroObs;exit;
        $this->_cobroObs = $cobroObs;
    }
    public function getCobroObs(){
        return $this->_cobroObs;
    }
    public function setCobroUsuarioId($cobroUsuarioId){
        $this->_cobroUsuarioId = $cobroUsuarioId;
    }
    public function getCobroUsuarioId(){
        return $this->_cobroUsuarioId;
    }
    public function setArrError ($error){
        $this->_arrError = $error;
    }
    public function getArrError (){
        return $this->_arrError;
    }    
    public function setCobroTotal($cobroTotal){
        $this->_cobroTotal= $cobroTotal;
    }
    public function getCobroTotal(){
        return $this->_cobroTotal;
    }
    public function setCobroCancelada($cobroCancelada){
        $this->_cobroCancelada=  $cobroCancelada;
    }
    public function getCobroCancelada(){
        return $this->_cobroCancelada;
    }
    public function setCobroCanceladaFechaHora($cobroCanceladaFechaHora){
        $this->_cobroCanceladaFechaHora=  $cobroCanceladaFechaHora;
    }
    public function getCobroCanceladaFechaHora(){
        return $this->_cobroCanceladaFechaHora;
    }
    public function setCobroCanceladaUsuarioId($cobroCanceladaUsuarioId){
        $this->_cobroCanceladaUsuarioId=  $cobroCanceladaUsuarioId;
    }
    public function getCobroCanceladaUsuarioId(){
        return $this->_cobroCanceladaUsuarioId;
    }
    
    //metodos comunes
    abstract function cargarMe($id);
    abstract function salvarMe($arrParametros);
    abstract function actualizarMe($arrParametros);

}

?>
