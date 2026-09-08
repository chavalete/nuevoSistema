<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';

if($_GET){

	    $arrParametros['fechaHasta']=$_REQUEST['fechaHasta'];
	    $arrParametros['idProveedor'] = $_REQUEST['idProveedor'];   
    	    $arrParametros['idLista'] = $_REQUEST['idLista'];     
	
	    $db = NEW FrenteAlmacenamiento();
	    $objFuncionesComunes =  new FuncionesComunes();

	    $db->addSelect('producto_nombre || \' \'||  producto_presentacion AS producto,codigo_referencia,sum(cantidad) AS cantidad, valor_stock ,costo_stock , producto_pcosto_dolar, producto_pcosto, ls.producto_pventa, stock_minimo, tipo_producto_nombre');
	    $db->addFrom('stock_completo');
	    $db->addFrom('INNER JOIN datos_productos USING(producto_id)');
	    $db->addFrom('INNER JOIN datos_tipos_productos USING(tipo_producto_id)');
	    $db->addFrom('INNER JOIN lista_precios_'.$arrParametros['idLista'].' ls USING(producto_id)');
	    


	    if($arrParametros[idProveedor]!='undefined'){
		$db->addWhere('prove_id = \'' . $arrParametros[idProveedor] . '\'');
	    }
	    
	    $db->addWhere('cantidad >= 0 ');
	    $db->addGroup('producto_nombre,producto_presentacion,codigo_referencia,tipo_producto_nombre, valor_stock, costo_stock, producto_pcosto_dolar, producto_pcosto, ls.producto_pventa, stock_minimo');
	    $db->addOrderBy('producto_nombre');
	    $db->generarSelect();
	    $resultado = $db->ejecutar();
	    //echo $db->getQry();exit;
	    $hora = @mktime();
	    
	    $fichero="/tmp/reporteStock$hora.csv";
	    $f = fopen("/tmp/reporteStock$hora.csv","a+");
	    $sep = ";"; //separador
	    $linea="Producto;Tipo Producto; Codigo Referencia;Cantidad;Stock Minimo;Precio de compra en dolares unitario;recio de compra en pesos unitario;Total de precio de compra;Precio de venta en pesos unitario;Total de precio de venta\n";
	    fwrite($f,$linea);
	    foreach($resultado AS $stock){
		$valorStock = $stock['cantidad'] * $stock['producto_pventa'];
		$linea = utf8_decode($stock['producto']).$sep.$stock['tipo_producto_nombre'].$sep.$stock['codigo_referencia'].$sep.$stock['cantidad'] .$sep.$stock['stock_minimo'].$sep.$stock['producto_pcosto_dolar'].$sep.$stock['producto_pcosto'].$sep.$stock['costo_stock'].$sep.$stock['producto_pventa'].$sep.$valorStock."\n";

                $linea = str_replace(".",",",$linea);
		fwrite($f,$linea);
	    }
		fclose($f); 
	    
		
		header("Content-Description: File Transfer");
		header( "Content-Disposition: filename=".basename($fichero) );
		header("Content-Length: ".filesize($fichero));
		header("Content-Type: application/force-download");
		ob_clean();
		flush();
		readfile($fichero);
		exit; 
}


?>
