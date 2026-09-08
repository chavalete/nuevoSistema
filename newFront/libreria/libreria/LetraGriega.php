<?php
/*
CLASE DE LETRA GRIEGA
*/
abstract class LetraGriega
{
    private $_letraId; /*integer*/
    private $_letraNombre;/*varchar*/
    private $_letraDirectorio;
    private $_contenedor = Array();/*array para la interfasssse*/

    public function __construct()
    {
    }
    public function setLetraId($id)
    {
	$this->_letraId = $id;
    }
    public function getLetraId()
    {
	return $this->_letraId;
    }
    public function setLetraNombre($nombre)
    {
	$this->_letraNombre = $nombre;
    }
    public function getLetraNombre()
    {
	return $this->_letraNombre;
    }
    public function setLetraDirectorio($directorio)
    {
	$this->_letraDirectorio = $directorio;
    }
    public function getLetraDirectorio()
    {
	return $this->_letraDirectorio;
    }
    public function setContenedor($contenedor)
    {
	$this->_contenedor = $contenedor;
    }
    public function getContenedor()
    {
	return $this->_contenedor;
    }
    abstract public function cargarMe($id);
    
    abstract public function salvarMe();
    
    abstract public function actualizarMe();
}