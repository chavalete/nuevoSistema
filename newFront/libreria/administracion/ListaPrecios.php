<?php
/**
 * class dato lsita de precio
 * 
 */
abstract class ListaPrecios 
{
    
    private $_listaId;
    private $_listaFechaHora;
    private $_listaNombre;
    private $_listaPadreId;
    private $_listaUsuarioId;
    private $_listaActiva;
    private $_listaCancelada;
    private $_listaCanceladaUsuarioId;
    private $_listaCanceladaFechaHora;
    private $_observaciones;
    private $_arrError;
    
    
    public function setListaId($listaId){
	$this->_listaId = $listaId;
    }
    public function getListaId(){
	return $this->_listaId;
    }
    public function setListaFechaHora($listaFechahora){
	$this->_listaFechaHora = $listaFechahora;
    }
    public function getListaFechaHora(){
	return $this->_listaFechaHora;
    }
    public function setListaNombre($listaNombre){
	$this->_listaNombre = $listaNombre;
    }
    public function getListaNombre(){
	return $this->_listaNombre;
    }
    public function setListaPadreId($listaPadreId){
	$this->_listaPadreId = $listaPadreId;
    }
    public function getListaPadreId(){
	return $this->_listaPadreId;
    }
    public function setListaUsuarioId($listaUsuarioId){
	$this->_listaUsuarioId = $listaUsuarioId;
    }    
    public function getListaUsuarioId(){
	return $this->_listaUsuarioId;
    }
    public function setListaActiva($listaActiva){
	$this->_listaActiva =  $listaActiva;
    }
    public function getListaActiva(){
	return $this->_listaActiva;
    }
    public function setListaCancelada($listaCancelada){
	$this->_listaCancelada = $listaCancelada;
    }
    public function getListaCancelada(){
	return $this->_listaCancelada;
    }
    public function setListaCanceladaUsuarioId($listaCanceladaUsuarioId){
	$this->_listaCanceladaUsuarioId = $listaCanceladaUsuarioId;
    }
    public function getListaCanceladaUsuarioId(){
	return $this->_listaCanceladaUsuarioId;
    }
    public function setListaCanceladaFechaHora($listaCanceladaFechaHora){
	$this->_listaCanceladaFechaHora = $listaCanceladaFechaHora;
    }
    public function getListaCanceladaFechaHora(){
	return $this->_listaCanceladaFechaHora;
    }
    public function setObs($obs){
	$this->_observaciones = $obs;
    }
    public function getObs(){
	return $this->_observaciones;
    }
    public function setArrError ($error){
        $this->_arrError = $error;
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
