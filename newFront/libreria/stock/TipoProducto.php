<?php
/*
CLASE DE Tipo de Productos
*/

abstract class TipoProducto
{
    private $_tipoProductoId; /*integer*/
    private $_tipoProductoNombre;/*varchar*/
    
    

    public function __construct()
    {
    }
    public function setTipoProductoId($tipoProductoId) {
	$this->_tipoProductoId = $tipoProductoId;
    }
    public function getTipoProductoId()
    {
	return $this->_tipoProductoId;
    }
    public function setTipoProductoNombre($nombre) {
	$this->_tipoProductoNombre = $nombre;
    }
    public function getTipoProductoNombre(){
	return $this->_tipoProductoNombre;
    }
    
    abstract public function cargarMe($id);
    
    abstract public function salvarMe($arrParametros);
    
    abstract public function actualizarMe($arrParametros);
}