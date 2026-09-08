<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

if($_GET){

	    $db = NEW FrenteAlmacenamiento();
	    
	    $db->addSelect('id_movimiento_sc,f_evento, h_evento, gln_origen,cuit_origen,gln_destino,cuit_destino,n_remito,n_factura,vencimiento,gtin,lote,   numero_serial,id_evento,apellido,nombres,nro_asociado,n_documento,sexo,tipo_documento,direccion,localidad,numero,piso,dpto,n_postal,telefono,    id_obra_social,id_tipo_movimiento_sc,codigo_sucursal,referencia_nro,es_reversa');
	    $db->addFrom('mensajeria_anmat');
	    
	    if($_REQUEST['referencia']!=NULL){
	    
		$db->addWhere('referencia_nro = \'' . $_REQUEST[referencia] . '\'');
	    }//else{
	//	$db->addWhere('regristo_traspasado = false');
	  //  }
	    $db->addOrderBy('mensajeria_id DESC');
	    $db->generarSelect();
	    $resultado = $db->ejecutar();
	    //echo $db->getQry();exit;
	    $hora = @mktime();
	    
	    $fichero="/tmp/reporteSAP$hora.csv";
	    $f = fopen("/tmp/reporteSAP$hora.csv","a+");
	    $sep = ";"; //separador
	    $linea="id_movimiento_sc;f_evento;h_evento;gln_origen;cuit_origen;gln_destino;cuit_destino;n_remito;n_factura;vencimiento;gtin;lote;numero_serial;id_evento;apellido;nombres;nro_asociado;n_documento;sexo;tipo_documento;direccion;localidad;numero;piso;dpto;n_postal;telefono;id_obra_social;id_tipo_movimiento_sc;codigo_sucursal;referencia_nro;es_reversa\n";
	    fwrite($f,$linea);
	    foreach($resultado AS $movimiento){
		//$linea = $movimiento['id_movimiento_sc']"\n";
		$linea = $movimiento['id_movimiento_sc'].$sep.$movimiento['f_evento'].$sep.$movimiento['h_evento'] .$sep.$movimiento['gln_origen'].$sep.$movimiento['cuit_origen'].$sep.$movimiento['gln_destino'].$sep.$movimiento['ciut_destino'].$sep.$movimiento['n_remito'].$sep.$movimiento['n_factura'].$sep.$movimiento['vencimiento'].$sep.$movimiento['gtin'].$sep.$movimiento['lote'].$sep.$movimiento['numero_serial'].$sep.$movimiento['id_evento'].$sep.$movimiento['apellido'].$sep.$movimiento['nombres'].$sep.$movimiento['nro_asociado'].$sep.$movimiento['n_documento'].$sep.$movimiento['sexo'].$sep.$movimiento['tipo_documento'].$sep.$movimiento['direccion'].$sep.$movimiento['localidad'].$sep.$movimiento['numero'].$sep.$movimiento['piso'].$sep.$movimiento['dpto'].$sep.$movimiento['n_postal'].$sep.$movimiento['telefono'].$sep.$movimiento['id_obra_social'].$sep.$movimiento['id_tipo_movimiento_sc'].$sep.$movimiento['codigo_sucursal'].$sep.$movimiento['referencia_nro'].$sep.$movimiento['es_reversa']."\n";
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
