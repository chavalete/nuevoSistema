<?php
/*
CLASE DE MARCAS
*/
abstract class Marca
{

    private $_marcaId; /*integer*/
    private $_marcaNombre;/*varchar*/
    private $_marcaActiva;/*boolean default true*/
    private $_marcaFechaAlta;/*date*/
    private $_marcaUsuarioAlta;/*integer*/	
    private $_obs;/*text*/	   

    public function __construct()
    {
    }
    public function setMarcaId($id)
    {
	$this->_marcaId = $id;
    }
    public function getMarcaId()
    {
	return $this->_marcaId;
    }
    public function setMarcaNombre($nombre)
    {
	$this->_marcaNombre = $nombre;
    }
    public function getMarcaNombre()
    {
	return $this->_marcaNombre;
    }
    public function setMarcaActiva($marcaActiva)
    {
	$this->_marcaActiva = $marcaActiva;
    }
    public function getMarcaActiva()
    {
	return $this->_marcaActiva;
    }
    public function setMarcaFechaAlta($fechaAlta)
    {
	$this->_marcaFechaAlta = $fechaAlta;
    }
    public function getMarcaFechaAlta()
    {
	return $this->_marcaFechaAlta;
    }
    public function setMarcaUsuarioAlta($usuarioId)
    {
	$this->_marcaUsuarioAlta = $usuarioId;
    }
    public function getMarcaUsuarioAlta()
    {
	return $this->_marcaUsuarioAlta;
    }
    public function setObs($obs)
    {
	$this->_obs = $obs;
    }
    public function getObs()
    {
	return $this->_obs;
    }
    abstract public function cargarMe($id);
    
    abstract public function salvarMe($arrParametros);
    
    abstract public function actualizarMe($arrParametros);
}