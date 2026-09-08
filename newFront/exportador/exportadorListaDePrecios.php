<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';

if($_GET){

	    
	    $arrParametros['idLista'] = $_REQUEST['idLista'];    

	    $db = NEW FrenteAlmacenamiento();
	    $objFuncionesComunes =  new FuncionesComunes();

	    $db->addSelect('producto_nombre || \' \'||  producto_presentacion AS producto,codigo_referencia, limonMoreno.producto_pventa AS precio');
	    $db->addFrom('lista_precios_'. $arrParametros['idLista'] . ' limonMoreno');
	    $db->addFrom('INNER JOIN datos_productos USING(producto_id)');
	    $db->addWhere('producto_activo=true');	    
	    $db->addOrderBy('producto_nombre');
	    $db->generarSelect();
	    $resultado = $db->ejecutar();
	    //echo $db->getQry();exit;
	    $hora = @mktime();
	    
	    $fichero="/tmp/reportelista$hora.csv";
	    $f = fopen("/tmp/reportelista$hora.csv","a+");
	    $sep = ";"; //separador
	    $linea="Producto; Codigo Referencia;Precio de venta;\n";
	    fwrite($f,$linea);
	    foreach($resultado AS $stock){
		$linea = utf8_decode($stock['producto']).$sep.$stock['codigo_referencia'].$sep.$stock['precio']."\n";
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
