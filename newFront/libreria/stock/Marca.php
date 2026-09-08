<?php
/*
CLASE DE MARCAS DE PRODUCTOS
*/

abstract class Marca
{
    private $_marcaId; /*integer*/
    private $_marcaNombre;/*varchar*/
    private $_marcaActiva;/*boolean default true*/
    private $_marcaFechaAlta;/*date*/
    private $_marcaUsuarioAlta;/*integer*/	
    private $_obs;/*text*/	
    private $_contenedor = Array();/*array para la interfasssse*/

	public function __construct()
	{
	}
	public function setEstanteriaId($id)
	{
	    $this->_marcaId = $id;
	}
	public function getEstanteriaId()
	{
	    return $this->_marcaId;
	}
	public function setEstanteriaNombre($nombre)
	{
	    $this->_marcaNombre = $nombre;
	}
	public function getEstanteriaNombre()
	{
	    return $this->_marcaNombre;
	}
	public function setEstanteriaActiva($marcaActiva)
	{
	    $this->_marcaActiva = $estareriaActiva;
	}
	public function getEstanteriaActiva()
	{
	    return $this->_marcaActiva;
	}
	public function setMarcaAlta($fechaAlta)
	{
	    $this->_marcaFechaAlta = $fechaAlta;
	}
	public function getAlmacenAlta()
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
	public function setContenedor($contenedor)
	{
	    $this->_contenedor = $contenedor;
	}
	public function getContenedor()
	{
	    return $this->_contenedor;
	}
	abstract function cargarMe($id);
	
	abstract function salvarMe($arrParametros);
	
	abstract  function actualizarMe($arrParametros);
}