<?php 

abstract class ObraSocial
{
    private $_obraSocialId;
    private $_obraSocialNombre;
    private $_obs;
    private $_obraSocialActiva;
    private $_obraSocialFechaAlta;
    private $_obraSocialUsuarioAlta;
    private $_contendor = Array();

    public function __construct()
    {
    }
    public function setObraSocialId($id)
    {
	$this->_obraSocialId = $id;
    }
    public function getObraSocialId()
    {
	return $this->_obraSocialId;
    }
    public function setObraSocialNombre($nombre)
    {
	$this->_obraSocialNombre = $nombre;
    }
    public function getObraSocialNombre()
    {
	return $this->_obraSocialNombre;
    }
    public function setObs($obs)
    {
	$this->_obs = $obs;
    }
    public function getObs()
    {
	return $this->_obs;
    }
    public function setObraSocialActiva($activa)
    {
	$this->_obraSocialActiva = $activa;
    }
    public function getObraSocialActiva()
    {
	return $this->_obraSocialActiva;
    }
    public function setObraSocialAlta($fechaAlta)
    {
	$this->_obraSocialfechaAlta = $fechaAlta;
    }
    public function getObraSocialAlta()
    {
	return $this->_obraSocialFechaAlta;
    }
    public function setObraSocialUsuarioAlta($usuarioId)
    {
	$this->_obraSocialUsuarioAlta = $usuarioId;
    }
    public function getObraSocialUsuarioAlta()
    {
	return $this->_obraSocialUsuarioAlta;
    }
    public function setContenedor($contendor)
    {
	$this->_contendor = $contenedor;
    }
    public function getContenedor()
    {
	return $this->_contendor;
    }
    abstract function cargarMe($id);
    abstract function salvarMe($arrParametros);
    abstract function actualizarMe($arrParametros);
}