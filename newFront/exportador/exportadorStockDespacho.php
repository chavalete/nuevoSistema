<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';

if($_GET){

	    $arrParametros['fechaHasta']=$_REQUEST['fechaHasta'];
	    $arrParametros['idProveedor'] = $_REQUEST['idProveedor'];    

	    $db = NEW FrenteAlmacenamiento();
	    $objFuncionesComunes =  new FuncionesComunes();

	    $db->addSelect('producto_nombre || \' \'||  producto_presentacion AS producto,codigo_referencia,count(*) AS cantidad,lote, estanteria_nombre, despacho_nro');
	    $db->addFrom('datos_trazabilidad');
	    $db->addFrom('INNER JOIN movimientos_trazabilidad USING(trazabilidad_id)');
	    $db->addFrom('INNER JOIN datos_movimientos USING(id_movimiento)');
	    $db->addFrom('INNER JOIN datos_estanterias USING(estanteria_id)');
	    $db->addFrom('INNER JOIN datos_productos ON (datos_trazabilidad.producto_id = datos_productos.producto_id)');
	    $db->addFrom('INNER JOIN datos_lotes USING(lote_id)');
	    
	    if($arrParametros[idProveedor]!='undefined'){
		$db->addWhere('prove_id = \'' . $arrParametros[idProveedor] . '\'');
	    }
	    $db->addWhere('en_stock ');
	    $db->addGroup('producto_nombre,producto_presentacion,lote,estanteria_nombre,codigo_referencia, despacho_nro');
	    $db->addOrderBy('producto_nombre');
	    $db->generarSelect();
	    $resultado = $db->ejecutar();
	    echo $db->getQry();exit;
	    $hora = @mktime();
	    
	    $fichero="/tmp/reporteStock$hora.csv";
	    $f = fopen("/tmp/reporteStock$hora.csv","a+");
	    $sep = ";"; //separador
	    $linea="Producto;Codigo Referencia;Cantidad;Lote;Estanteria;Despacho\n";
	    fwrite($f,$linea);
	    foreach($resultado AS $stock){
		$linea = utf8_decode($stock['producto']).$sep.$stock['codigo_referencia'].$sep.$stock['cantidad'] .$sep.$stock['lote'].$sep.$stock['estanteria_nombre'].$sep.$stock['despacho_nro']."\n";
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
