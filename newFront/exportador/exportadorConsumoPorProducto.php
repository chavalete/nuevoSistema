<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';

if($_GET){

	    $arrParametros['idCliente'] = $_REQUEST['idCliente'];
	    $arrParametros['idMarca'] = $_REQUEST['marcas'];
	    $arrParametros['idSubfamilia'] = $_REQUEST['subfamilias'];   
        $arrParametros['idProducto'] = $_REQUEST['idProducto'];
        $arrParametros['idVendedor'] = $_REQUEST['idVendedor'];
        $arrParametros['fechaDesde'] = $_REQUEST['fechaDesde'];
        $arrParametros['fechaHasta'] = $_REQUEST['fechaHasta'];
        $arrParametros['tipoReporte'] = $_REQUEST['tipoReporte'];     
        
        
        //var_dump($arrParametros);exit;
        
	    $db = NEW FrenteAlmacenamiento();
	    $objFuncionesComunes =  new FuncionesComunes();

	    
	    
	    $db->addSelect("producto_nombre || ' - ' || producto_presentacion AS producto, sum(cantidad)::integer AS cantidad_vendida,datos_productos.producto_pcosto ");
		$db->addFrom('datos_productos');
		$db->addFrom('INNER JOIN detalle_facturas  de USING(producto_id)');
        $db->addFrom('INNER JOIN  datos_facturas USING(factura_id)');
        $db->addFrom('INNER JOIN datos_clientes  USING(cliente_id)');
        $db->addWhere('de.producto_pcosto >= 0 ');
		$db->addWhere('rentabilidad=true');
        $db->addWhere('factura_cancelada=false');
        //$db->addWhere('datos_facturas.cliente_id NOT IN (12,634,963)');
	    

	    if($arrParametros[idMarca]!='undefined'){
            $db->addWhere('marca_id = \'' . $arrParametros[idMarca] . '\'');
	    }
	    if($arrParametros[idSubfamilia]!='undefined'){
            $db->addFrom('INNER JOIN relaciones_familias_subfamilias USING(producto_id)');
            $db->addWhere('subfamilia_id = \'' . $arrParametros[idSubfamilia] . '\'');
			$db->addWhere('relacion_activa = true');
	    }
	    if($arrParametros[idProducto]!='undefined'){
            $db->addWhere('de.producto_id = \'' . $arrParametros[idProducto] . '\'');
	    }
	    if($arrParametros['fechaDesde'] !=null ){
            $db->addWhere('factura_fecha >= \'' . $objFuncionesComunes->fechaFormatoDb($arrParametros['fechaDesde']) . '\'');
        }
        if($arrParametros['fechaHasta'] !=null ){
            $db->addWhere('factura_fecha <= \'' . $objFuncionesComunes->fechaFormatoDb($arrParametros['fechaHasta']) . '\'');
        }
	    
	    $db->addGroup("producto_nombre || ' - ' || producto_presentacion, datos_productos.producto_pcosto");
	    //$db->addOrderBy("producto_nombre || ' - ' || producto_presentacion");
	    //$db->addOrderBy("sum(importe_detalle-(de.producto_pcosto*cantidad)) DESC");
	    $db->addOrderBy("sum(cantidad) DESC");
	    $db->generarSelect();
	    $resultado = $db->ejecutar();
        //echo $db->getQry();exit;
	    $hora = @mktime();
	    
	    $fichero="/tmp/reporteConsumoPorProducto$hora.csv";
	    $f = fopen("/tmp/reporteConsumoPorProducto$hora.csv","a+");
	    $sep = ";"; //separador
	    $linea="Fecha Reporte: " .  $arrParametros[fechaDesde]  . " - " . $arrParametros[fechaHasta] ." ;\n";
	    fwrite($f,$linea);
	    $linea="Producto;Cantidad;Costo;\n";
	    fwrite($f,$linea);
	    $totalGanancia=0;
	    $totalGananciaComision=0;
	    foreach($resultado AS $productos){
            
            $linea = $productos['producto'].$sep.$productos['cantidad_vendida'].$sep.$objFuncionesComunes->formatoMoneda($productos[producto_pcosto])."\n";
            $linea = str_replace(".",",",$linea);
            fwrite($f,$linea);
	    }
	    if($arrParametros[idMarca]!='undefined'){
            $extWhere=" AND datos_productos.marca_id = {$arrParametros[idMarca]}";

	    }
	    if($arrParametros[idSubfamilia]!='undefined'){
            $extJoin=" INNER JOIN relaciones_familias_subfamilias ON datos_productos.producto_id = relaciones_familias_subfamilias.producto_id";
			$extWhere=" AND subfamilia_id = {$arrParametros[idSubfamilia]} AND relacion_activa =true ";
	    }
	    if($arrParametros[idProducto]!='undefined'){
            $extWhere=" AND datos_productos.producto_id = {$arrParametros[idProducto]}";
	    }

	    $fechaDesde=$objFuncionesComunes->fechaFormatoDb($arrParametros[fechaDesde]);
		$fechaHasta=$objFuncionesComunes->fechaFormatoDb($arrParametros[fechaHasta]);
		//echo $fechaDesde;exit;
	    $qry="SELECT producto_nombre || ' ' || producto_presentacion AS producto, 0 AS cantidad, producto_pcosto FROM datos_productos INNER JOIN datos_marcas USING(marca_id)  $extJoin WHERE datos_productos.producto_id NOT IN (SELECT producto_id FROM detalle_facturas INNER JOIN datos_facturas USING(factura_id) INNER JOIN datos_clientes USING(cliente_id) WHERE factura_fecha>='$fechaDesde' AND factura_fecha<='$fechaHasta' AND factura_cancelada=false AND rentabilidad=true) $extWhere ORDER BY marca_nombre,producto_nombre;";
		//echo $qry;exit;
		$db->setQry($qry);
		$resultasoSinExistencia = $db->ejecutar();

		foreach($resultasoSinExistencia AS $productos){

            $linea = $productos['producto'].$sep.$productos['cantidad'].$sep.$objFuncionesComunes->formatoMoneda($productos[producto_pcosto])."\n";
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
