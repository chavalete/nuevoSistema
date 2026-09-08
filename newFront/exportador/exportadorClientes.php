<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';

if($_GET){

	    $arrParametros['idCliente']=$_REQUEST['idCliente'];
	    $arrParametros['idVendedor']=$_REQUEST['idVendedor'];

	    $db = NEW FrenteAlmacenamiento();
	    $objFuncionesComunes =  new FuncionesComunes();
	    
	    
	    $db->addSelect('*, CASE WHEN cliente_resp_insc THEN \'Resp Insc\' ELSE \'Exento\' END AS cliente_iva,cliente_calle || \' \' ||  cliente_altura || \' \' || cliente_piso || \' \' || cliente_depto || \' \' ||  cliente_codigo_postal || \' \' || localidad_nombre AS direccion'); 
        $db->addFrom('datos_clientes');
        $db->addFrom('INNER JOIN datos_categorias USING(categoria_id)');
        $db->addFrom('INNER JOIN datos_listas_precios USING(lista_id)');
        $db->addFrom('INNER JOIN datos_localidades ON datos_clientes.cliente_loca_id = localidad_id');
        $db->addFrom('LEFT JOIN datos_vendedores USING(vendedor_id)');
        
        if($arrParametros['idCliente'] !='undefined' ){
            $db->addWhere('cliente_id = \'' . $arrParametros['idCliente'] . '\' ');
        }
        if($arrParametros['idVendedor'] !='undefined' && $arrParametros['idVendedor'] !='false' ){
            $db->addWhere('datos_clientes.vendedor_id = \'' . $arrParametros['idVendedor'] . '\' ');
        }
        $db->addWhere('cliente_activo=true');
        $db->addOrderby('cliente_nombre ASC');
	    //$db->addGroup('producto_nombre,producto_presentacion,lote,estanteria_nombre,codigo_referencia,lote_vencimiento, cantidad, datos_lotes.lote_id');
	    $db->generarSelect();
	    $resultado = $db->ejecutar();
        //echo $db->getQry();exit;
	    $hora = @mktime();
	    
	    $fichero="/tmp/reporteClientes$hora.csv";
	    $f = fopen("/tmp/reporteClientes$hora.csv","a+");
	    $sep = ";"; //separador
	    $linea="Cliente;CUIT;Categoria;Telefono;IVA;Vendedor;Lista de precios;Observaciones;Direcccion\n";
	    fwrite($f,$linea);
	    foreach($resultado AS $stock){
		$linea = utf8_decode($stock['cliente_nombre']).$sep.$stock['cliente_cuit'].$sep.$stock['categoria_nombre'] .$sep.$stock['cliente_telefono'].$sep.$stock['cliente_iva'].$sep.$stock['vendedor_nombre'].$sep.$stock['lista_nombre'].$sep.$stock['observaciones'].$sep.$stock['direccion'].$sep.$stock['44']."\n";
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
