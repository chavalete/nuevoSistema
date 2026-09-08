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
		$arrParametros['idSubfamilia'] = $_REQUEST['subfamilias'];
        
        
        //var_dump($arrParametros);exit;
        
	    $db = NEW FrenteAlmacenamiento();
	    $objFuncionesComunes =  new FuncionesComunes();

	    
	    
	    $db->addSelect("sum(importe_detalle) as total_detalle,sum(de.producto_pcosto * cantidad) AS total_costo, familia_nombre, sum(cantidad) AS cantidad_vendida, sum(importe_detalle-(de.producto_pcosto*cantidad)) AS ganancia_neto,  sum(de.producto_comision*cantidad) AS total_comision");
        $db->addFrom('datos_facturas');
	    $db->addFrom('INNER JOIN detalle_facturas  de USING(factura_id)');
	            $db->addFrom('INNER JOIN datos_clientes  USING (cliente_id)');

        $db->addFrom('INNER JOIN datos_productos dp USING(producto_id)');
        $db->addFrom('INNER JOIN relaciones_familias_subfamilias rfs ON dp.producto_id = rfs.producto_id');
        $db->addFrom('INNER JOIN datos_subfamilias USING(subfamilia_id)');
		$db->addFrom('INNER JOIN vw_familias_productos  ON dp.producto_id =vw_familias_productos.producto_id ');
        
        $db->addWhere('de.producto_pcosto >= 0 ');
        $db->addWhere('factura_cancelada=false');
        $db->addWhere('relacion_activa =true');
        $db->addWhere('rentabilidad=true');
	    
	    if($arrParametros[idCliente]!='undefined'){
            $db->addWhere('cliente_id = \'' . $arrParametros[idCliente] . '\'');
	    }
	    if($arrParametros[idMarca]!='undefined'){
            $db->addWhere('marca_id = \'' . $arrParametros[idMarca] . '\'');
	    }
	    if($arrParametros[idSubfamilia]!='undefined'){
            //$db->addFrom('INNER JOIN relaciones_familias_subfamilias USING(producto_id)');
            $db->addWhere('subfamilia_id = \'' . $arrParametros[idSubfamilia] . '\'');
	    }
	    if($arrParametros[idFamilia]!='undefined'){
            $db->addFrom('INNER JOIN vw_familias_productos USING(producto_id)');
            $db->addWhere('vw_familias_productos.familia_id = \'' . $arrParametros[idFamilia] . '\'');
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
	    
	    $db->addGroup("familia_nombre");
	    $db->addOrderBy("familia_nombre");
	    $db->generarSelect();
	    $resultado = $db->ejecutar();
        //echo $db->getQry();exit;
	    $hora = @mktime();
	    
	    $fichero="/tmp/reporteRentabilidad$hora.csv";
	    $f = fopen("/tmp/reporteRentabilidad$hora.csv","a+");
	    $sep = ";"; //separador
	    $linea="Fecha Reporte: " .  $arrParametros[fechaDesde]  . " - " . $arrParametros[fechaHasta] ." ;\n";
	    fwrite($f,$linea);
	    $linea="Familia;Cantidad;Costo Total;Importe Total;Rentabilidad Absoluta;Rentabilidad Relativa;Costo Total comision;Rentabilidad Absoluta comision ;Rentabilidad Relativa comision;\n";
	    fwrite($f,$linea);
	    $totalGanancia=0;
	    $totalGananciaComision=0;
	    foreach($resultado AS $productos){
            $costoTotal =  $productos['total_costo'];
            $utilidadAbs= $productos['total_detalle'] - $costoTotal;
            $utilidadRel = round(($utilidadAbs / $productos['total_detalle']) * 100,2);
            
            $costoTotalComision =  $costoTotal + $productos['total_comision'];
            $utilidadAbsComision= $productos['total_detalle'] - $costoTotalComision;
            $utilidadRelComision = round(($utilidadAbsComision / $productos['total_detalle']) * 100,2);
            
            $linea = $productos['familia_nombre'].$sep.$productos['cantidad_vendida'].$sep.$objFuncionesComunes->formatoMoneda($costoTotal).$sep.$objFuncionesComunes->formatoMoneda($productos['total_detalle']).$sep.$objFuncionesComunes->formatoMoneda($utilidadAbs).$sep.$utilidadRel.$sep.$objFuncionesComunes->formatoMoneda($costoTotalComision).$sep.$objFuncionesComunes->formatoMoneda($utilidadAbsComision).$sep.$utilidadRelComision ."\n";
            $totalGanancia= $totalGanancia + $utilidadAbs;
            $totalGananciaComision= $totalGananciaComision + $utilidadAbsComision;
            $linea = str_replace(".",",",$linea);
            fwrite($f,$linea);
	    }
	    $linea= $sep.$sep.$sep.$sep.$objFuncionesComunes->formatoMoneda($totalGanancia).$sep.$sep.$sep.$objFuncionesComunes->formatoMoneda($totalGananciaComision)."\n";
	    $linea = str_replace(".",",",$linea);
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
