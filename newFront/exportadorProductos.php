<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';

if($_GET){

	    
	    $arrParametros['actualizarPrecio'] = $_REQUEST['actualizarPrecio'];
	    
	    $db = NEW FrenteAlmacenamiento();
	    $objFuncionesComunes =  new FuncionesComunes();

	    $db->addSelect("producto_nombre || ' - ' || producto_presentacion AS producto, producto_pcosto, producto_id");
	    $db->addFrom('datos_productos');
	    
	    if($arrParametros['actualizarPrecio'] =='t'){
            $db->addWhere("actualizar_costo=true");
	    }
	    if($arrParametros['actualizarPrecio'] =='f'){
            $db->addWhere("actualizar_costo=false");
	    }
	    
	    $db->addOrderBy('producto_nombre');
	    $db->generarSelect();
	    $resultado = $db->ejecutar();
	    
	    //levantamos las listas de precios
	    
	    $qry="SELECT lista_id, lista_nombre FROM datos_listas_precios WHERE lista_activa=true AND lista_cancelada=false ORDER BY lista_id;";
	    $db->setQry($qry);
	    $resultadoListas=$db->ejecutar();
	    
	    $hora = @mktime();
	    
	    $fichero="/tmp/reporteProductos$hora.csv";
	    $f = fopen("/tmp/reporteProductos$hora.csv","a+");
	    $sep = ";"; //separador
	    $linea="Subfamilia;Producto;Costo;";
	    
	    foreach($resultadoListas AS $lista){
                $linea.=$lista['lista_nombre'] . ";";
	    }
	    $linea.="\n";
	    fwrite($f,$linea);
	    
	    //BUSCAMOS LAS Subfamilias
	    $qrySub="SELECT subfamilia_nombre, producto_id, subfamilia_id, producto_pcosto FROM relaciones_familias_subfamilias INNER JOIN datos_subfamilias USING(subfamilia_id) INNER JOIN datos_productos USING (producto_id) WHERE relacion_activa AND actualizar_costo ORDER BY subfamilia_id;";
	    //echo $qrySub;exit;
	    $db->setQry($qrySub);
	    $resultadoSub=$db->ejecutar();
	    $corte=0;
	    $ant=null;
	    foreach($resultadoSub AS $productos){
            if($productos[subfamilia_id] !=$ant){
                foreach($resultadoListas AS $lista){
                    $qryLista="SELECT ls.producto_pventa, producto_pcosto FROM lista_precios_$lista[lista_id] ls INNER JOIN datos_productos USING(producto_id) WHERE producto_id =$productos[producto_id];";
                    $db->setQry($qryLista);
                    $resultadoPrecio=$db->ejecutar();
                    if($corte==0){
                        $linea = utf8_decode($productos['subfamilia_nombre']).$sep.$sep.$productos['producto_pcosto'].$sep.$resultadoPrecio[0]['producto_pventa'];
                        $corte++;
                    }else{
                        $linea.=$sep.$resultadoPrecio[0]['producto_pventa'];
                    }
                }
                $linea.="\n";
                $corte=0;
                fwrite($f,$linea);
                $ant=$productos[subfamilia_id];
            }
	    }
	    $qry="SELECT producto_nombre || ' - ' || producto_presentacion AS producto, producto_pcosto, producto_id FROM datos_productos WHERE actualizar_costo AND producto_id NOT IN (SELECT producto_id FROM relaciones_familias_subfamilias WHERE relacion_activa) ORDER BY producto_nombre;";
	    
	    $db->setQry($qry);
	    $resultadoProductos=$db->ejecutar();
	    
	    $corte=0;
	    foreach($resultadoProductos AS $productos){
            foreach($resultadoListas AS $lista){
                $qryLista="SELECT ls.producto_pventa, producto_pcosto FROM lista_precios_$lista[lista_id] ls INNER JOIN datos_productos USING(producto_id) WHERE producto_id =$productos[producto_id];";
                $db->setQry($qryLista);
                $resultadoPrecio=$db->ejecutar();
                if($corte==0){
                    $linea = $sep.utf8_decode($productos['producto']).$sep.$productos['producto_pcosto'].$sep.$resultadoPrecio[0]['producto_pventa'];
                    $corte++;
                }else{
                    $linea.=$sep.$resultadoPrecio[0]['producto_pventa'];
                }
            }
            $linea.="\n";
            $corte=0;
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
