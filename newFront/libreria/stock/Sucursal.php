<?php
/*
CLASE DE Sucursales
*/

abstract class Sucursal
{
    private $_sucursalId; /*integer*/
    private $_sucursalNombre;/*varchar*/
    private $_sucursalActiva;/*boolean default true*/
    private $_sucursalFechaAlta;/*date*/
    private $_sucursalUsuarioAlta;/*integer*/	
    private $_obs;/*text*/	
    private $_contenedor = Array();/*array para la interfasssse*/

    public function __construct()
    {
    }
    public function setSucursalId($id)
    {
	$this->_sucursalId = $id;
    }
    public function getSucursalId()
    {
	return $this->_sucursalId;
    }
    public function setSucursalNombre($nombre)
    {
	$this->_sucursalNombre = $nombre;
    }
    public function getSucursalNombre()
    {
	return $this->_sucursalNombre;
    }
    public function setSucursalActiva($sucursalActiva)
    {
	$this->_sucursalActiva = $sucursalActiva;
    }
    public function getSucursalActiva()
    {
	return $this->_sucursalActiva;
    }
    public function setSucursalAlta($fechaAlta)
    {
	$this->_sucursalFechaAlta = $fechaAlta;
    }
    public function getSucursalAlta()
    {
	return $this->_sucursalFechaAlta;
    }
    public function setSucursalUsuarioAlta($usuarioId)
    {
	$this->_sucursalUsuarioAlta = $usuarioId;
    }
    public function getSucursalUsuarioAlta()
    {
	return $this->_sucursalUsuarioAlta;
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