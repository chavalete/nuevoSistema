<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';

if($_GET){

	    $arrParametros['idCliente'] = $_REQUEST['idCliente'];
	    $arrParametros['idMarca'] = $_REQUEST['marcas'];
	    $arrParametros['idSubfamilia'] = $_REQUEST['subfamilias'];
        $arrParametros['idFamilia'] = $_REQUEST['idFamilia'];
        $arrParametros['idProducto'] = $_REQUEST['idProducto'];
        $arrParametros['idVendedor'] = $_REQUEST['idVendedor'];
        $arrParametros['fechaDesde'] = $_REQUEST['fechaDesde'];
        $arrParametros['fechaHasta'] = $_REQUEST['fechaHasta'];
        $arrParametros['tipoReporte'] = $_REQUEST['tipoReporte'];     
        
        
        
        switch($arrParametros['tipoReporte']){
        
            case "porProducto":
                    require_once 'exportadorRentabilidadPorProducto.php';
                    exit;
            case "porCliente":
                    require_once 'exportadorRentabilidadPorCliente.php';
                    exit;
            case "porMarca":
                    require_once 'exportadorRentabilidadPorMarca.php';
                    exit;
            case "porSubfamilia":
                    require_once 'exportadorRentabilidadPorSubfamilia.php';
                    exit;
           case "porFamilia":
                    require_once 'exportadorRentabilidadPorFamilia.php';
                    exit;
            case "porVendedor":
                    require_once 'exportadorRentabilidadPorVendedor.php';
                    exit;
            case "porRemito":
                    require_once 'exportadorRentabilidadPorRemito.php';
                    exit;
        }
        
	    $db = NEW FrenteAlmacenamiento();
	    $objFuncionesComunes =  new FuncionesComunes();

	    
	    
	    $db->addSelect("to_char(factura_fecha,'DD-MM-YYYY') AS fecha, producto_nombre || producto_presentacion AS producto,importe_detalle,importe_unitario, cantidad, de.producto_pcosto, factura_nro, cliente_nombre, de.producto_comision");
        $db->addFrom('datos_facturas');
        $db->addFrom('INNER JOIN detalle_facturas  de USING(factura_id)');
        $db->addFrom('INNER JOIN datos_productos USING(producto_id)');
        $db->addFrom('INNER JOIN datos_clientes USING(cliente_id)');
        $db->addWhere('de.producto_pcosto >= 0 ');
        $db->addWhere('factura_cancelada=false');
        $db->addWhere('rentabilidad=true');
	    
	    if($arrParametros[idCliente]!='undefined'){
            $db->addWhere('cliente_id = \'' . $arrParametros[idCliente] . '\'');
	    }
	    if($arrParametros[idMarca]!='undefined'){
            $db->addWhere('marca_id = \'' . $arrParametros[idMarca] . '\'');
	    }
	    if($arrParametros[idSubfamilia]!='undefined'){
            $db->addFrom('INNER JOIN relaciones_familias_subfamilias USING(producto_id)');
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
	    
	    //$db->addGroup('producto_nombre,producto_presentacion,lote,estanteria_nombre,codigo_referencia,lote_vencimiento, cantidad, datos_lotes.lote_id');
	    $db->addOrderBy('factura_nro');
	    $db->generarSelect();
	    $resultado = $db->ejecutar();
        //echo $db->getQry();exit;
	    $hora = @mktime();
	    
	    $fichero="/tmp/reporteRentabilidad$hora.csv";
	    $f = fopen("/tmp/reporteRentabilidad$hora.csv","a+");
	    $sep = ";"; //separador
	    $linea="Fecha;Cliente;Remito Nro;Producto;Cantidad;Costo Unitario;Precio de venta;Costo Total;Importe Remito;Rentabilidad Absoluta;Rentabilidad Relativa;Costo Total comision;Rentabilidad Absoluta comision ;Rentabilidad Relativa comision;\n";
	    fwrite($f,$linea);
	    $totalGanancia=0;
	    foreach($resultado AS $productos){
            $costoTotal =  $productos['producto_pcosto'] * $productos['cantidad'];
            $utilidadAbs= $productos['importe_detalle'] - $costoTotal;
            $utilidadRel = round(($utilidadAbs / $productos['importe_detalle']) * 100,2);
            
            $costoTotalComision =  ($productos['producto_pcosto'] * $productos['cantidad']) + $productos['producto_comision'] * $productos['cantidad'];
            $utilidadAbsComision= $productos['importe_detalle'] - $costoTotalComision;
            $utilidadRelComision = round(($utilidadAbsComision / $productos['importe_detalle']) * 100,2);
            
            $linea = $productos['fecha'].$sep.utf8_decode($productos['cliente_nombre']).$sep.$productos['factura_nro'].$sep.utf8_decode($productos['producto']).$sep.$productos['cantidad'].$sep.$productos['producto_pcosto'].$sep.$productos['importe_unitario'].$sep.$costoTotal.$sep.$productos['importe_detalle'].$sep.$utilidadAbs.$sep.$utilidadRel.$sep.$costoTotalComision.$sep.$utilidadAbsComision.$sep.$utilidadRelComision ."\n";
            $totalGanancia= $totalGanancia + $utilidadAbs;
            $linea = str_replace(".",",",$linea);
            fwrite($f,$linea);
	    }
	    $linea= $sep.$sep.$sep.$totalCosto.$sep.$sep.$sep.$sep.$sep.$sep.$totalGanancia."\n";
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
