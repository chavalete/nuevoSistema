<?php
/*
CLASE DE ESTADOS
*/
abstract class Estado
{

    private $_estadoId; /*integer*/
    private $_estadoNombre;/*varchar*/
    private $_estadoActiva;/*boolean default true*/
    private $_estadoFechaAlta;/*date*/
    private $_estadoUsuarioAlta;/*integer*/	
    private $_obs;/*text*/	
    private $_contenedor = Array();/*array para la interfasssse*/

    public function __construct()
    {
    }
    public function setEstadoId($id)
    {
	$this->_estadoId = $id;
    }
    public function getEstadoId()
    {
	return $this->_estadoId;
    }
    public function setEstadoNombre($nombre)
    {
	$this->_estadoNombre = $nombre;
    }
    public function getEstadoNombre()
    {
	return $this->_estadoNombre;
    }
    public function setEstadoActiva($estadoActiva)
    {
	$this->_estadoActiva = $estadoActiva;
    }
    public function getEstadoActiva()
    {
	return $this->_estadoActiva;
    }
    public function setEstadoFechaAlta($fechaAlta)
    {
	$this->_estadoFechaAlta = $fechaAlta;
    }
    public function getEstadoFechaAlta()
    {
	return $this->_estadoFechaAlta;
    }
    public function setEstadoUsuarioAlta($usuarioId)
    {
	$this->_estadoUsuarioAlta = $usuarioId;
    }
    public function getEstadoUsuarioAlta()
    {
	return $this->_estadoUsuarioAlta;
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