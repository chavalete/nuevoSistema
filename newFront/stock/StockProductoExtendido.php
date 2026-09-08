<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/stock/StockProducto.php';
/*
CLASE DE Stock de Productos
*/

class StockProductoExtendido extends StockProducto{

    private $_db;

    public function __construc(){
		$this->_db = new FrenteAlmacenamiento();
	}

    public function cargarMe($id){
     
    }
	
    public function salvarMe();
	
    public function actualizarMe();
    
}