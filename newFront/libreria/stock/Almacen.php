<?php
/*
CLASE DE Almacenes
*/

abstract class Almacen
{
    private $_almacenId; /*integer*/
    private $_almacenNombre;/*varchar*/
    private $_sucursalId;
    private $_almacenlActivo;/*boolean default true*/
    private $_almacenFechaAlta;/*date*/
    private $_almacenUsuarioAlta;/*integer*/	
    private $_obs;/*text*/	

    public function __construct()
    {
    }
    public function setAlmacenId($id)
    {
	$this->_almacenId = $id;
    }
    public function getAlmacenId()
    {
	return $this->_almacenId;
    }
    public function setAlmacenNombre($nombre)
    {
	$this->_almacenNombre = $nombre;
    }
    public function getAlmacenNombre()
    {
	return $this->_almacenNombre;
    }
    public function setSucursalId($id)
    {
	$this->_sucursalId = $id;
    }
    public function getSucursalId()
    {
	return $this->_sucursalId;
    }
    public function setAlmacenActivo($almacenActivo)
    {
	$this->_almacenlActivo = $almacenActivo;
    }
    public function getAlmacenActivo()
    {
	return $this->_almacenActivo;
    }
    public function setAlmacenAlta($fechaAlta) {
	$this->_almacenFechaAlta = $fechaAlta;
    }
    public function getAlmacenAlta(){
	return $this->_almacenFechaAlta;
    }
    public function setAlmacenUsuarioAlta($usuarioId){
	$this->_almacenUsuarioAlta = $usuarioId;
    }
    public function getAlmacenUsuarioAlta()
    {
	return $this->_almacenUsuarioAlta;
    }

    public function setObs($observaciones){
	
	$this->_obs = $observaciones;
    }
    public function getObs()
    {
	return $this->_obs;
    }

    abstract public function cargarMe($id);
    
    abstract public function salvarMe($arrParametros);
    
    abstract public function actualizarMe($arrParametros);
}