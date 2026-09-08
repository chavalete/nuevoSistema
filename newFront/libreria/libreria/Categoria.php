<?php
/*
CLASE DE CATEGORIAS
*/
abstract class Categoria
{

    private $_categoriaId; /*integer*/
    private $_categoriaNombre;/*varchar*/
    private $_categoriaActiva;/*boolean default true*/
    private $_categoriaFechaAlta;/*date*/
    private $_categoriaUsuarioAlta;/*integer*/	
    private $_obs;/*text*/	
    private $_contenedor = Array();/*array para la interfasssse*/

    public function __construct()
    {
    }
    public function setCategoriaId($id)
    {
	$this->_categoriaId = $id;
    }
    public function getCategoriaId()
    {
	return $this->_categoriaId;
    }
    public function setCategoriaNombre($nombre)
    {
	$this->_categoriaNombre = $nombre;
    }
    public function getCategoriaNombre()
    {
	return $this->_categoriaNombre;
    }
    public function setCategoriaActiva($categoriaActiva)
    {
	$this->_categoriaActiva = $categoriaActiva;
    }
    public function getCategoriaActiva()
    {
	return $this->_categoriaActiva;
    }
    public function setCategoriaFechaAlta($fechaAlta)
    {
	$this->_categoriaFechaAlta = $fechaAlta;
    }
    public function getCategoriaFechaAlta()
    {
	return $this->_categoriaFechaAlta;
    }
    public function setCategoriaUsuarioAlta($usuarioId)
    {
	$this->_categoriaUsuarioAlta = $usuarioId;
    }
    public function getCategoriaUsuarioAlta()
    {
	return $this->_categoriaUsuarioAlta;
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