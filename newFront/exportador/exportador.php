<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';

if($_GET){

if($_REQUEST['idProveedor']==NULL){

	    $arrParametros['fechaDesde']=$_REQUEST['fechaDesde'];
	    $arrParametros['fechaHasta']=$_REQUEST['fechaHasta'];


	    $db = NEW FrenteAlmacenamiento();
	    

	    $db->addSelect('to_char(movimiento_fecha_hora::date, \'DD-MM-YYYY\') AS movimiento_fecha_hora,  cliente_nombre, categoria_nombre, medico_nombre,paciente_nombre,obra_social_nombre,trazabilidad_codigo,producto_nombre || \' \' || producto_presentacion AS producto,codigo_referencia,prove_nombre,  remito_nro, factura_nro');
	    $db->addFrom('datos_movimientos');
	    $db->addFrom('INNER JOIN detalle_movimientos USING (id_movimiento)');
	    $db->addFrom('INNER JOIN movimientos_trazabilidad USING (id_movimiento)');
	    $db->addFrom('INNER JOIN datos_trazabilidad USING (trazabilidad_id)');
	    $db->addFrom('INNER JOIN datos_clientes USING (cliente_id)');
	    $db->addFrom('INNER JOIN datos_categorias ON datos_clientes.categoria_id = datos_categorias.categoria_id');
	    $db->addFrom('INNER JOIN datos_productos ON datos_trazabilidad.producto_id = datos_productos.producto_id');
	    $db->addFrom('INNER JOIN datos_proveedores ON datos_productos.prove_id = datos_proveedores.prove_id');
	    $db->addFrom('LEFT JOIN datos_medicos USING(medico_id)');
	    $db->addFrom('LEFT JOIN datos_pacientes USING(paciente_id)');
	    $db->addFrom('LEFT JOIN datos_obras_sociales USING(obra_social_id)');
	    $db->addWhere('movimiento_fecha_hora::date >= \'' . $arrParametros[fechaDesde] . '\'');
	    $db->addWhere('movimiento_fecha_hora::date <= \'' . $arrParametros[fechaHasta] . '\'');
	    $db->addWhere('datos_movimientos.tipo_movimiento_id  IN (2,3)');
	    $db->addGroup('movimiento_fecha_hora,cliente_nombre,categoria_nombre,trazabilidad_codigo,producto_nombre');
	    $db->addGroup(' ,medico_nombre,paciente_nombre,obra_social_nombre,producto_presentacion,prove_nombre,remito_nro,factura_nro,id_movimiento, codigo_referencia');
	    $db->addOrderBy('trazabilidad_codigo');
	    $db->generarSelect();
	    $resultado = $db->ejecutar();
	    //echo $db->getQry();exit;
	    $hora = @mktime();
	    
	    $fichero="/tmp/reporteSalidas$hora.csv";
	    $f = fopen("/tmp/reporteSalidas$hora.csv","a+");
	    $sep = ";"; //separador
	    $linea="Fecha;Cliente;Categoria;Medico;Paciente;Obra Social;Codigo Trazabilidad;Codigo Referencia;Producto;Proveedor;Remito;Factura\n";
	    fwrite($f,$linea);
	    foreach($resultado AS $movimiento){
		$linea = $movimiento['movimiento_fecha_hora'].$sep.$movimiento['cliente_nombre'].$sep.$movimiento['categoria_nombre'] .$sep.$movimiento['medico_nombre'].$sep.$movimiento['paciente_nombre'].$sep.$movimiento['obra_social_nombre'].$sep.$movimiento['trazabilidad_codigo'].$sep.$movimiento['codigo_referencia'].$sep.$movimiento['producto'].$sep.$movimiento['prove_nombre']
		.$sep.$movimiento['remito_nro'].$sep.$movimiento['factura_nro']."\n";
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
	    $arrParametros['idProveedor']=$_REQUEST['idProveedor'];


	    $db = NEW FrenteAlmacenamiento();
	    $objFuncionesComunes =  new FuncionesComunes();

	    $db->addSelect('producto_nombre || \' \'||  producto_presentacion AS producto,codigo_referencia,sum(cantidad) AS cantidad,prove_nombre, marca_nombre');
	    $db->addFrom('stock_completo');
	    $db->addFrom('INNER JOIN datos_productos USING(producto_id)');
	    $db->addFrom('INNER JOIN datos_proveedores USING(prove_id)');
	    $db->addFrom('INNER JOIN datos_marcas USING(marca_id)');
	    $db->addWhere('prove_id = ' . $objFuncionesComunes->parsearAutocompletar($arrParametros['idProveedor']));
	    $db->addWhere('cantidad >= 0');
	    $db->addGroup('producto_nombre,producto_presentacion,prove_nombre,marca_nombre,codigo_referencia');
	    $db->addOrderBy('producto_nombre');
	    $db->generarSelect();
	    $resultado = $db->ejecutar();
	    //echo $db->getQry();exit;
	    $hora = @mktime();
	    
	    $fichero="/tmp/reporteStock$hora.csv";
	    $f = fopen("/tmp/reporteStock$hora.csv","a+");
	    $sep = ";"; //separador
	    $linea="Producto;Codigo Referencia;Cantidad;Proveedor;Marca\n";
	    fwrite($f,$linea);
	    foreach($resultado AS $stock){
		$linea = $stock['producto'].$sep.$stock['codigo_referencia'].$sep.$stock['cantidad'] .$sep.$stock['prove_nombre'].$sep.$stock['marca_nombre']."\n";
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
