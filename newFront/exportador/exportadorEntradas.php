<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';

if($_GET){

	    
	    $arrParametros['idProveedor'] = $_REQUEST['idProveedor'];
	    $arrParametros['fechaDesde'] = $_REQUEST['fechaDesde'];    
	    $arrParametros['fechaHasta'] = $_REQUEST['fechaHasta'];    

	    $db = NEW FrenteAlmacenamiento();
	    $objFuncionesComunes =  new FuncionesComunes();

	    $db->addSelect("to_char(movimiento_fecha_hora,'DD-MM-YYYY') AS fecha,tipo_comprobante, prove_nombre, remito_nro, sum(producto_pcosto*cantidad) AS importe");
	    $db->addFrom('datos_movimientos');
	    $db->addFrom('INNER JOIN detalle_movimientos USING(id_movimiento)');
	    $db->addFrom('INNER JOIN datos_proveedores USING(prove_id)');
	    $db->addWhere('anulado=false');
	    $db->addWhere('tipo_movimiento_id=1');
	    
	    if($arrParametros['idProveedor']!='undefined'){
            $db->addWhere("datos_movimientos.prove_id = $arrParametros[idProveedor]");
	    }
	    
	    $db->addWhere('movimiento_fecha_hora::date >= \'' . $objFuncionesComunes->fechaFormatoDb($arrParametros['fechaDesde']) . '\'');
	    $db->addWhere('movimiento_fecha_hora::date <= \'' . $objFuncionesComunes->fechaFormatoDb($arrParametros['fechaHasta']) . '\'');
	    $db->addGroup('movimiento_fecha_hora, prove_nombre, remito_nro,tipo_comprobante');
	    $db->addOrderBy('tipo_comprobante,movimiento_fecha_hora');
	    $db->generarSelect();
	    $resultado = $db->ejecutar();
	    //echo $db->getQry();exit;
	    $hora = @mktime();
	    
	    $fichero="/tmp/reporteEntradas$hora.csv";
	    $f = fopen("/tmp/reporteEntradas$hora.csv","a+");
	    $sep = ";"; //separador
	    $linea="Fecha; Proveedor;Tipo Comprobante;Nro.Comprobante;Importe;\n";
	    fwrite($f,$linea);
	    $importeTotal=0;
	    foreach($resultado AS $stock){
		$linea = utf8_decode($stock['fecha']).$sep.$stock['prove_nombre'].$sep.$stock['tipo_comprobante'].$sep.$stock['remito_nro'].$sep.$stock['importe']."\n";
		fwrite($f,$linea);
		$importeTotal = $importeTotal + $stock['importe'];
	    }
	    $linea=$sep.$sep.$sep."Total".$sep.$importeTotal;
	    fwrite($f,$linea);
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
