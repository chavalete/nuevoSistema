<?php
$_SERVER['DOCUMENT_ROOT']='/var/www/html';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

class migrarProductos{
	private $_db;


	public function migrar(){
		$this->_db = NEW FrenteAlmacenamiento();
		$qry="SELECT sl.producto_id_asc, sl.cantidad, st.lote_id FROM stock_limoneta sl INNER JOIN stock_completo st ON sl.producto_id_asc = st.producto_id  WHERE sl.cantidad>0;";
		echo $qry;
		$this->_db->setQry($qry);
		$resultado = $this->_db->ejecutar();
		foreach ($resultado AS $producto){
			$qryStock="UPDATE stock_completo SET cantidad = {$producto['cantidad']} WHERE producto_id = {$producto['producto_id']} AND lote_id = {$producto['lote_id']};";
			echo $qryStock;exit;

		}
	}
}

$obj= NEW migrarProductos();
$obj->migrar();


?>
