<?php
/*
CLASE DE ESTADOS
*/
abstract class Provincia
{

    private $_provinciaId; /*integer*/
    private $_provinciaNombre;/*varchar*/
    private $_provinciaActiva;/*boolean default true*/
    private $_provinciaFechaAlta;/*date*/
    private $_provinciaUsuarioAlta;/*integer*/	
    private $_obs;/*text*/	
    private $_contenedor = Array();/*array para la interfasssse*/

    public function __construct()
    {
    }
    public function setProvinciaId($id)
    {
	$this->_provinciaId = $id;
    }
    public function getProvinciaId()
    {
	return $this->_provinciaId;
    }
    public function setProvinciaNombre($nombre)
    {
	$this->_provinciaNombre = $nombre;
    }
    public function getProvinciaNombre()
    {
	return $this->_provinciaNombre;
    }
    public function setProvinciaActiva($provinciaActiva)
    {
	$this->_provinciaActiva = $provinciaActiva;
    }
    public function getProvinciaActiva()
    {
	return $this->_provinciaActiva;
    }
    public function setProvinciaFechaAlta($fechaAlta)
    {
	$this->_provinciaFechaAlta = $fechaAlta;
    }
    public function getProvinciaFechaAlta()
    {
	return $this->_provinciaFechaAlta;
    }
    public function setProvinciaUsuarioAlta($usuarioId)
    {
	$this->_provinciaUsuarioAlta = $usuarioId;
    }
    public function getProvinciaUsuarioAlta()
    {
	return $this->_provinciaUsuarioAlta;
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
    
    abstract public function salvarMe($arrParamatros);
    
    abstract public function actualizarMe($arrParamatros);
}