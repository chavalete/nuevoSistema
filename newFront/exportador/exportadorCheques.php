<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';

if($_GET){

	    $arrParametros['idImputacion']=$_REQUEST['idImputacion'];
	    $arrParametros['idEstado']=$_REQUEST['idEstado'];

	    $db = NEW FrenteAlmacenamiento();
	    $objFuncionesComunes =  new FuncionesComunes();
	    
	    
	    $db->addSelect('datos_cheques.observaciones, cheque_id, to_char(fecha_alta,\'DD-MM-YY\') AS fecha_alta, cliente_nombre, banco_nombre, cheque_nro, importe, to_char(fecha_acreditacion,\'DD-MM-YY\') AS fecha_acreditacion, descripcion, to_char(datos_cheques.imputacion_fecha_hora,\'DD-MM-YY HH24:MI\') AS imputacion_fecha_hora, estado_desc, forma_desc'); 
        $db->addFrom('datos_cheques');
        $db->addFrom('INNER JOIN datos_clientes USING(cliente_id)');
        $db->addFrom('INNER JOIN datos_bancos USING(banco_id)');
        $db->addFrom('INNER JOIN datos_estados USING(estado_id)');
        $db->addFrom('LEFT JOIN datos_imputaciones USING(imputacion_id)');
        $db->addFrom('LEFT JOIN datos_formas_pagos USING(forma_pago_id)');
        
        if($arrParametros['idImputacion'] !='undefined' ){
            $db->addWhere('imputacion_id = \'' . $arrParametros['idImputacion'] . '\' ');
        }
        if($arrParametros['idEstado'] !='undefined' ){
            $db->addWhere('estado_id = \'' . $arrParametros['idEstado'] . '\' ');
        }
	    //$db->addGroup('producto_nombre,producto_presentacion,lote,estanteria_nombre,codigo_referencia,lote_vencimiento, cantidad, datos_lotes.lote_id');
	    $db->addOrderby('fecha_acreditacion::date ASC');
	    $db->generarSelect();
	    $resultado = $db->ejecutar();
        //echo $db->getQry();exit;
	    $hora = @mktime();
	    
	    $fichero="/tmp/reporteCheques$hora.csv";
	    $f = fopen("/tmp/reporteCheques$hora.csv","a+");
	    $sep = ";"; //separador
	    $linea="Fecha Alta ;Cliente;Banco;Nro Cheque;Tipo cheque;Importe;Fecha Acreditacion;Imputacion;Imputado Fecha Hora;Estado;Observaciones\n";
	    fwrite($f,$linea);
	    foreach($resultado AS $stock){
		$linea = utf8_decode($stock['fecha_alta']).$sep.$stock['cliente_nombre'].$sep.$stock['banco_nombre'] .$sep.$stock['cheque_nro'].$sep.$stock['forma_desc'].$sep.$stock['importe'].$sep.$stock['fecha_acreditacion'].$sep.$stock['descripcion'].$sep.$stock['imputacion_fecha_hora'].$sep.$stock['estado_desc'].$sep.$stock['observaciones']."\n";
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
