<?php
/*
CLASE DE ESTANTERIAS
*/

abstract class Estanteria
{
    private $_estanteriaId; /*integer*/
    private $_estanteriaNombre;/*varchar*/
    private $_almacenlId;
    private $_estanteriaActiva;/*boolean default true*/
    private $_estanteriaFechaAlta;/*date*/
    private $_estanteriaUsuarioAlta;/*integer*/	
    private $_obs;/*text*/	
    private $_contenedor = Array();/*array para la interfasssse*/
    private $_objAlmacen;

    public function __construct()
    {
    }
    public function setEstanteriaId($id){
	$this->_estanteriaId = $id;
    }
    public function getEstanteriaId(){
	return $this->_estanteriaId;
    }
    public function setEstanteriaNombre($nombre){
	$this->_estanteriaNombre = $nombre;
    }
    public function getEstanteriaNombre(){
	return $this->_estanteriaNombre;
    }
    public function setAlmacenId($id){
	$this->_almacenId = $id;
    }
    public function getAlmacenId(){
	return $this->_almacenId;
    }
    public function setEstanteriaActiva($almacenActiva){
	$this->_estanteriaActiva = $estareriaActiva;
    }
    public function getEstanteriaActiva(){
	return $this->_estanteriaActiva;
    }
    public function setAlmacenAlta($fechaAlta){
	$this->_almacenFechaAlta = $fechaAlta;
    }
    public function getAlmacenAlta(){
	return $this->_almacenFechaAlta;
    }
    public function setEstanteriaUsuarioAlta($usuarioId){
	$this->_estanteriaUsuarioAlta = $usuarioId;
    }
    public function getEstaneriaUsuarioAlta(){
	return $this->_estanteriaUsuarioAlta;
    }
    public function setObs($obs){
	$this->_obs = $obs;
    }
    public function getObs(){
	return $this->_obs;
    }
    public function setObjAlmacen($objAlmacen){    
	$this->_objAlmacen = $objAlmacen;
    }
    public function getObjAlmacen(){
	return $this->_objAlmacen;
    }
    
    abstract public function cargarMe($id);
    
    abstract public function salvarMe($arrParametros);
    
    abstract public function actualizarMe($arrParametros);
}