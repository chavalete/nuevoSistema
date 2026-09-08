<?php
/*
CLASE DE Stock de Productos
*/

abstract class StockProducto
{
    private $_productoId; /*integer*/
    private $_sucursalId;
    private $_almacenId;
    private $_estanteriaId;
    private $_loteId;
    private $_cantidad;

    public function __construct()
    {
    }
    public function setProcutoId($productoId)
    {
	$this->_producoId = $productId;
    }
    public function getProductoId()
    {
	return $this->_productoId;
    }
    public function setSucursalId($sucursalId)
    {
	$this->_sucursalId = $sucursalId;
    }
    public function getSucursalId()
    {
	return $this->_sucursalId;
    }
    public function setLoteId($loteId)
    {
	$this->_loteId;
    }
    public function getLoteId()
    {
	return $this->_loteId;
    }
    public function setAlmacenId($almacenId)
    {
	$this->_almacenId = $almacenId;
    }
    public function getAlmacenId()
    {
	return $this->_almacenId;
    }
    public function setEstanteriaId($estanteriaId)
    {
	$this->_estanteriaId = $estanteriaId;
    }
    public function getEstanteriaId()
    {
	return $this->_estanteriaId;
    }
    public function setCantidad($cantidad)
    {
	$this->_cantidad = $cantidad;
    }
    public function getCantidad()
    {
	return $this->_cantidad;
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