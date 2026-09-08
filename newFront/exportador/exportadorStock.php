<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';
session_start();
if($_GET){
//var_dump($_GET);exit;
	    $arrParametros['idProducto']=$_REQUEST['idProducto'];
	    $arrParametros['idSubfamilia'] = $_REQUEST['subfamilias'];
        $arrParametros['idMarca'] = $_REQUEST['marcas'];
	    $arrParametros['ordenadoPor'] = $_REQUEST['ordenadoPor'];
        $arrParametros['idFamilia'] = $_REQUEST['idFamilia'];

	    $db = NEW FrenteAlmacenamiento();
	    $objFuncionesComunes =  new FuncionesComunes();

	    $db->addSelect('producto_nombre || \' \'||  producto_presentacion AS producto,sum(cantidad) AS cantidad, producto_pcosto, marca_nombre');
	    $db->addFrom('stock_completo');
	    $db->addFrom('INNER JOIN datos_productos USING(producto_id)');
	    $db->addFrom('INNER JOIN datos_marcas USING(marca_id)');
        //$db->addFrom('INNER JOIN datos_subfamilias USING(subfamilia_id)');
	    $db->addWhere('excluir_reporte_stock = false');
	    $db->addWhere('datos_productos.producto_activo=true');



	    if($arrParametros[idProducto]!='undefined'){
            $db->addWhere('datos_productos.producto_id = \'' . $arrParametros[idProducto] . '\'');
	    }
	    if($arrParametros[idMarca]!='undefined'){
            $db->addWhere('datos_productos.marca_id = \'' . $arrParametros[idMarca] . '\'');
	    }
	    if($arrParametros['idSubfamilia']!='undefined'){
            $db->addWhere("EXISTS (
                SELECT 1 FROM relaciones_familias_subfamilias rfs
                WHERE rfs.producto_id = datos_productos.producto_id
                  AND rfs.subfamilia_id = '{$arrParametros['idSubfamilia']}'
                  AND rfs.relacion_activa = true
            )");
	    }
	    if($arrParametros['idFamilia']!='undefined'){
            $db->addWhere("EXISTS (
                SELECT 1 FROM relaciones_familias_subfamilias rfs
                WHERE rfs.producto_id = datos_productos.producto_id
                  AND rfs.familia_id = '{$arrParametros['idFamilia']}'
                  AND rfs.relacion_activa = true
            )");
        }
	    $db->addWhere('cantidad >= 0 ');
        $db->addWhere("sucursal_id = {$_REQUEST['idSucursal']}");
	    $db->addGroup('producto_nombre || \' \'||  producto_presentacion, producto_pcosto, marca_nombre, producto_nombre');
	    $db->addOrderBy("marca_nombre, producto_nombre");
	    $db->generarSelect();
	    $resultado = $db->ejecutar();
	    echo $db->getQry();exit;
	    $hora = @mktime();

	    $fichero="/tmp/reporteStock$hora.csv";
	    $f = fopen("/tmp/reporteStock$hora.csv","a+");
	    $sep = ";"; //separador
	    //var_dump($_SESSION);exit;
	    if($_SESSION['usuarioId'] ==3){
            $linea="Producto;Cantidad;Costo;Marca\n";
            fwrite($f,$linea);
            $totalCosto=0;
            $totalVenta=0;
            $totalCostoPesos=0;
            foreach($resultado AS $stock){
            $valorStockCosto = $stock['cantidad'] * $stock['producto_pcosto'];
            $linea = utf8_decode($stock['producto']).$sep.$stock['cantidad'].$sep.$valorStockCosto.$sep.$stock['marca_nombre']."\n";
            $totalVenta = $totalVenta + $valorStock;
            $totalCostoPesos = $totalCostoPesos + ($stock['producto_pcosto'] * $stock['cantidad']);
            //echo $totalCosto;exit;
            $linea = str_replace(".",",",$linea);
            fwrite($f,$linea);
            }
            $linea = $sep.$sep."Total: ".$totalCostoPesos."\n";
            fwrite($f,$linea);

            if($arrParametros[idProducto]!='undefined'){
                $extWhere=" AND datos_productos.producto_id = {$arrParametros[idProducto]}";
            }
            if($arrParametros[idMarca]!='undefined'){
                $extWhere=" AND datos_marcas.marca_id = {$arrParametros[idMarca]}";
            }
            if($arrParametros['idSubfamilia']!='undefined'){
                $extJoin=" INNER JOIN relaciones_familias_subfamilias ON datos_productos.producto_id = relaciones_familias_subfamilias.producto_id";
                $extWhere=" AND subfamilia_id = {$arrParametros[idSubfamilia]} AND relacion_activa =true ";
	    }
            if($arrParametros['idFamilia']!='undefined'){
                $extJoin=" INNER JOIN relaciones_familias_subfamilias ON datos_productos.producto_id = relaciones_familias_subfamilias.producto_id";
                $extWhere=" AND relaciones_familias_subfamilias.familia_id = {$arrParametros[idFamilia]} AND relacion_activa =true ";
            }

            $qry="SELECT producto_nombre || ' ' || producto_presentacion AS producto, 0 AS cantidad FROM datos_productos INNER JOIN datos_marcas USING (marca_id) $extJoin WHERE datos_productos.producto_id NOT IN (SELECT producto_id FROM stock_completo) $extWhere ORDER BY marca_nombre;";

            //echo $qry;exit;

            $db->setQry($qry);
            $resultasoSinExistencia = $db->ejecutar();

            foreach($resultasoSinExistencia AS $stock){
            $valorStock = $stock['cantidad'] * $stock['producto_pventa'];
            $linea = utf8_decode($stock['producto']).$sep.$stock['cantidad']."\n";
            $totalVenta = $totalVenta + $valorStock;
            $totalCosto = $totalCosto + ($stock['producto_pcosto_dolar'] * $stock['cantidad']);
            $totalCostoPesos = $totalCostoPesos + ($stock['producto_pcosto'] * $stock['cantidad']);
            //echo $totalCosto;exit;
            $linea = str_replace(".",",",$linea);
            fwrite($f,$linea);
            }
            }else{
            if($_SESSION['usuarioId']==11){
                $linea="Producto;Cantidad;Marca;Costo\n";
            }else{
                $linea="Producto;Cantidad;Marca\n";
            }
            fwrite($f,$linea);
            $totalCosto=0;
            $totalVenta=0;
            $totalCostoPesos=0;
            foreach($resultado AS $stock){
            if($_SESSION['usuarioId']==10){
                $linea = utf8_decode($stock['producto']).$sep.$stock['cantidad'].$sep.$stock['marca_nombre'].$sep.$stock['producto_pcosto']."\n";
            }else{
                $linea = utf8_decode($stock['producto']).$sep.$stock['cantidad'].$sep.$stock['marca_nombre']."\n";
            }

            //echo $totalCosto;exit;
            $linea = str_replace(".",",",$linea);
            fwrite($f,$linea);
            }

            if($arrParametros[idProducto]!='undefined'){
                $extWhere=" AND datos_productos.producto_id = {$arrParametros[idProducto]}";
            }
            if($arrParametros[idMarca]!='undefined'){
                $extWhere=" AND datos_marcas.marca_id = {$arrParametros[idMarca]}";
            }
            if($arrParametros['idSubfamilia']!='undefined'){
                $extJoin=" INNER JOIN relaciones_familias_subfamilias ON datos_productos.producto_id = relaciones_familias_subfamilias.producto_id";
                $extWhere=" AND subfamilia_id = {$arrParametros[idSubfamilia]} AND relacion_activa =true ";
            }
            if($arrParametros['idFamilia']!='undefined'){
                $extJoin=" INNER JOIN relaciones_familias_subfamilias ON datos_productos.producto_id = relaciones_familias_subfamilias.producto_id";
                $extWhere=" AND relaciones_familias_subfamilias.familia_id = {$arrParametros[idFamilia]} AND relacion_activa =true ";
            }
            $qry="SELECT producto_nombre || ' ' || producto_presentacion AS producto, 0 AS cantidad, producto_pcosto, marca_nombre FROM datos_productos INNER JOIN datos_marcas USING (marca_id) $extJoin WHERE datos_productos.producto_id NOT IN (SELECT producto_id FROM stock_completo) $extWhere ORDER BY marca_nombre;";

            $db->setQry($qry);
            $resultasoSinExistencia = $db->ejecutar();

            foreach($resultasoSinExistencia AS $stock){

            if($_SESSION['usuarioId']==10){
                $linea = utf8_decode($stock['producto']).$sep.$stock['cantidad'].$sep.$stock['marca_nombre'].$sep.$stock['[producto_pcosto]']."\n";
            }else{
                $linea = utf8_decode($stock['producto']).$sep.$stock['cantidad'].$sep.$stock['marca_nombre']."\n";
            }

            //echo $totalCosto;exit;
            $linea = str_replace(".",",",$linea);
            fwrite($f,$linea);

            }
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
