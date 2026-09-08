<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';

if($_GET){

    $arrParametros['fechaHasta']=$_REQUEST['fechaHasta'];
    $arrParametros['fechaDesde']=$_REQUEST['fechaDesde'];
    $arrParametros['productoId'] = $_REQUEST['idProducto'];
    $arrParametros['tipoReporte'] = $_REQUEST['tipoReporte'];    

	if($arrParametros['tipoReporte'] !=2){    
	    
	    //var_dump($arrParametros);exit;
	    $db = NEW FrenteAlmacenamiento();
	    $objFuncionesComunes =  new FuncionesComunes();

	    $db->addSelect('trazabilidad_codigo AS serie');
	    $db->addSelect('to_char(fecha_hora,\'DD-MM-YYYY\') AS fecha_hora');
	    $db->addSelect('producto_nombre || \' \'||  producto_presentacion AS producto');
	    $db->addSelect('tipo_movimiento_nombre');
	    $db->addSelect('remito_nro');
	    $db->addSelect('factura_nro');
	    $db->addSelect('nro_nota_carga');
	    $db->addSelect('codigo_referencia');
	    $db->addSelect('estanteria_nombre');
	    $db->addSelect('lote');
	    $db->addSelect('to_char(lote_vencimiento,\'DD-MM-YYYY\') AS lote_vencimiento');
	    $db->addSelect('vw_despacho_trazas.despacho_nro');
	    $db->addFrom('datos_trazabilidad');
	    $db->addFrom('INNER JOIN vw_despacho_trazas USING(trazabilidad_id)');
	    $db->addFrom('INNER JOIN movimientos_trazabilidad USING(trazabilidad_id)');
	    $db->addFrom('INNER JOIN datos_movimientos USING(id_movimiento)');
	    $db->addFrom('INNER JOIN datos_estanterias USING(estanteria_id)');
	    $db->addFrom('INNER JOIN datos_productos USING(producto_id)');
	    $db->addFrom('INNER JOIN datos_lotes USING(lote_id)');
	    $db->addFrom('INNER JOIN tipos_movimientos_trazas ON movimientos_trazabilidad.tipo_movimiento_id = tipos_movimientos_trazas.tipo_movimiento_id');
	    
	    $db->addWhere('movimientos_trazabilidad.tipo_movimiento_id IN (1,2,3) ');
	    
	    if($arrParametros[productoId]!='undefined'){
		$db->addWhere('datos_productos.producto_id = \'' . $arrParametros[productoId] . '\'');
	    }
	    if($arrParametros[fechaDesde]!=null){
		$arrVencimento = explode('-', $arrParametros[fechaDesde]);
		$fechaDesde = $arrVencimento[2] . "-" . $arrVencimento[1] . "-" . $arrVencimento[0];
		$db->addWhere('movimiento_fecha_hora::date >= \'' . $fechaDesde . '\'');
	    }
	    if($arrParametros[fechaHasta]!=null){
		$arrVencimento = explode('-', $arrParametros[fechaHasta]);
		$fechaHasta = $arrVencimento[2] . "-" . $arrVencimento[1] . "-" . $arrVencimento[0];
		$db->addWhere('movimiento_fecha_hora::date <= \'' . $fechaHasta. '\'');
	    }
	    $arrParametros[2] = $arrVencimento[2] . "-" . $arrVencimento[1] . "-" . $arrVencimento[0];
	    //$db->addGroup('producto_nombre,producto_presentacion,lote,estanteria_nombre,codigo_referencia, vw_despacho_trazas.despacho_nro');
	    $db->addOrderBy('id_movimiento ASC');
	    $db->generarSelect();
	    //echo $db->getQry();exit;
	    $resultado = $db->ejecutar();
	    
	    $hora = @mktime();
	    
	    $fichero="/tmp/reporteAuditor$hora.csv";
	    $f = fopen("/tmp/reporteAuditor$hora.csv","a+");
	    $sep = ";"; //separador
	    $linea="Fecha Movimiento;Codigo Trazabilidad;Producto;Tipo Movimiento;Remito Nro;Factura Nro;Nro Nota Carga;Codigo Referencia;Estanteria;Lote;Vencimiento;Despacho\n";
	    fwrite($f,$linea);
	    foreach($resultado AS $stock){
		$linea = utf8_decode($stock['fecha_hora']).$sep.$stock['serie'].$sep.utf8_decode($stock['producto']).$sep.$stock['tipo_movimiento_nombre'].$sep.$stock['remito_nro'].$sep.$stock['factura_nro'].$sep.$stock['nro_nota_carga'].$sep.$stock['codigo_referencia'].$sep.$stock['estanteria_nombre'] .$sep.$stock['lote'].$sep.$stock['lote_vencimiento'].$sep.$stock['despacho_nro']."\n";
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
	}else{
	    //var_dump($arrParametros);exit;
	    $db = NEW FrenteAlmacenamiento();
	    $objFuncionesComunes =  new FuncionesComunes();
	    
	    $db->addSelect('to_char(movimiento_fecha_hora,\'DD-MM-YYYY\') AS fecha_hora');
	    $db->addSelect('producto_nombre || \' \'||  producto_presentacion AS producto');
	    $db->addSelect('remito_nro');
	    $db->addSelect('codigo_referencia');
	    $db->addSelect('CASE WHEN cliente_id = 3 THEN abs(cantidad) * -1 ELSE abs(cantidad) END AS cantidad');
	    $db->addSelect('paciente_nombre');
	    $db->addSelect('cliente_nombre');
	    $db->addSelect('medico_nombre');
 	    $db->addSelect('vendedor_nombre');
	    $db->addSelect('distribuidor_nombre');
	    $db->addSelect('nro_nota_carga');
	    $db->addFrom('datos_movimientos');
	    $db->addFrom('INNER JOIN detalle_movimientos USING(id_movimiento)');
	    $db->addFrom('INNER JOIN datos_productos USING(producto_id)');
	    $db->addFrom('INNER JOIN datos_clientes USING(cliente_id)');
	    $db->addFrom('LEFT JOIN datos_pacientes USING(paciente_id)');
	    $db->addFrom('LEFT JOIN datos_medicos USING(medico_id)');
	    $db->addFrom('LEFT JOIN datos_vendedores USING(vendedor_id)');
	    $db->addFrom('LEFT JOIN datos_distribuidores USING(distribuidor_id)');

	    
	    $db->addWhere('tipo_movimiento_id IN (2,3,7,8,9,10)');
	    
	    if($arrParametros[productoId]!='undefined'){
		$db->addWhere('datos_productos.producto_id = \'' . $arrParametros[productoId] . '\'');
	    }
	    if($arrParametros[fechaDesde]!=null){
		$arrVencimento = explode('-', $arrParametros[fechaDesde]);
		$fechaDesde = $arrVencimento[2] . "-" . $arrVencimento[1] . "-" . $arrVencimento[0];
		$db->addWhere('movimiento_fecha_hora::date >= \'' . $fechaDesde . '\'');
	    }
	    if($arrParametros[fechaHasta]!=null){
		$arrVencimento = explode('-', $arrParametros[fechaHasta]);
		$fechaHasta = $arrVencimento[2] . "-" . $arrVencimento[1] . "-" . $arrVencimento[0];
		$db->addWhere('movimiento_fecha_hora::date <= \'' . $fechaHasta. '\'');
	    }
	    $arrParametros[2] = $arrVencimento[2] . "-" . $arrVencimento[1] . "-" . $arrVencimento[0];
	    //$db->addGroup('producto_nombre,producto_presentacion,lote,estanteria_nombre,codigo_referencia, vw_despacho_trazas.despacho_nro');
	    
	    $db->addWhere('alquilable=false');
	    $db->addOrderBy('id_movimiento ASC');
	    $db->generarSelect();
	    //echo $db->getQry();exit;
	    $resultado = $db->ejecutar();
	    
	    $hora = @mktime();
	    
	    $fichero="/tmp/reporteConsumo$hora.csv";
	    $f = fopen("/tmp/reporteConsumo$hora.csv","a+");
	    $sep = ";"; //separador
	    $linea="Fecha Movimiento;Remito Nro;Nro Nota Carga;Codigo Referencia;Materiales;Cantidad;Paciente Nombre;Cliente Nombre;Medico Nombre;Vendedor Nombre;Distribuidor Nombre\n";
	    fwrite($f,$linea);
	    foreach($resultado AS $stock){
		$linea = utf8_decode($stock['fecha_hora']).$sep.$stock['remito_nro'].$sep.$stock['nro_nota_carga'].$sep.utf8_decode($stock['codigo_referencia']).$sep.$stock['producto'].$sep.$stock['cantidad'].$sep.$stock['paciente_nombre'].$sep.$stock['cliente_nombre'].$sep.$stock['medico_nombre'].$sep.$stock['vendedor_nombre'].$sep.$stock['distribuidor_nombre']."\n";
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
}


?>
