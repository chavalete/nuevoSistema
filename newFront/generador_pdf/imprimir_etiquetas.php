<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
 include('php-barcode.php');
ob_end_clean();
require('fpdf.php');

$db = NEW frenteAlmacenamiento();

$db = NEW frenteAlmacenamiento();
$db->addSelect("'30711011508000' AS gln");
$db->addSelect("trazabilidad_codigo AS traza");
$db->addSelect("dp.pm");
$db->addSelect("lote AS lote");
$db->addSelect("to_char(lote_vencimiento, 'DD-MM-YYYY') AS venc");
$db->addSelect("to_char(lote_vencimiento, 'YYMMDD') AS venc_cast");
$db->addSelect("despacho_nro");
$db->addSelect("producto_nombre");
$db->addSelect("producto_presentacion  AS producto_detalle");
$db->addSelect("prove_direccion");
$db->addSelect("marca_nombre");
$db->addSelect("prove_nombre");
$db->addSelect("codigo_referencia");
$db->addSelect("categoria_id");
    
$db->addFrom("	datos_trazabilidad dt 
		INNER JOIN movimientos_trazabilidad mt USING (trazabilidad_id)
		INNER JOIN datos_movimientos dm USING (id_movimiento)
		INNER JOIN datos_lotes dl USING (lote_id)
		INNER JOIN datos_productos dp USING (producto_id)
		INNER JOIN datos_proveedores dprov ON (dprov.prove_id=dm.prove_id)
		INNER JOIN datos_marcas dmar ON (dp.marca_id = dmar.marca_id)
             ");

$db->addWhere("trazabilidad_id = " . $_GET['crIdTrazabilidad']);
$db->addOrderBy('trazabilidad_id ASC');//generar qry
$db->generarSelect();   
//echo $db->getQry();
$codigos = $db->ejecutar();

if(count($codigos) ==0){
    echo "<h2>Sin registros para {$_GET['crIdTrazabilidad']} ";
    exit;
}

$fontSize = 10;
$marge    = 10;   // between barcode and hri in pixel
$x        = 12;  // barcode center
$y        = 13;  // barcode center
$height   = 1;   // barcode height in 1D ; module size in 2D
$width    = 1.2;    // barcode height in 1D ; not use in 2D
$angle    = 0;   // rotation in degrees
$black    = '000000'; // color in hexa
$type     = 'datamatrix';

$pdf = new FPDF ('P','mm',array(100,35));

foreach($codigos AS $codigo){
    $pdf->AddPage();
    $code = $codigo['traza'];
    
    $data = Barcode::fpdf($pdf, $black, $x, $y, $angle, $type, array('code'=>$code), $width, $height);  

    $pdf->SetFont('Arial','B',7);
    $pdf->Text(26.5,7, utf8_decode($codigo['producto_nombre']) . " " .utf8_decode($codigo['producto_detalle']));
    
    $pdf->SetFont('Arial','',7);
    $pdf->Text(26.5,10, "Nro. Lista:" . $codigo['codigo_referencia']);
    $pdf->Text(26.5,13,'Marca: ' .  $codigo['marca_nombre']);
    $pdf->Text(26.5,16,'Proveedor: ' . utf8_decode($codigo['prove_nombre']));
    $pdf->Text(26.5,19, utf8_decode($codigo['prove_direccion']));
    $pdf->Text(26.5,22,'Producto autorizado por ANMAT. PM:' . $codigo['pm'] );
    
    $pdf->SetFont('Arial','',7);
    $pdf->Text(26.5,25, 'Dir. Tecnico Farm.: Diego Bergati M.P. 19166');
    $pdf->Text(26.5,28,'Venta exclusiva a profesionales e instituciones sanitarias');

    $pdf->SetFont('Arial','',7);
    if($codigo['categoria_id']==8){
	$pdf->Text(26.5,31,'Importado y distribuido por: Soluciones Hospitalarias S.A.' );  
	
    }else{
	$pdf->Text(26.5,31,'Distribuido por: Soluciones Hospitalarias S.A.' );  
    }
    
    $pdf->Text(26.5,34 ,'Don Bosco 1159, San Isidro, Buenos Aires' );
    
    $pdf->SetFont('Arial','',5);
    $code_rhi = '(01)' . $codigo['gln'] . '(17)' . $codigo['venc_cast']   . '(10)' . $codigo['lote'] . '(21)'  . $codigo['traza'];
    
    $pdf->SetFont('Arial','',7);
    $pdf->Text(2.5,3, $code_rhi) ;
    $pdf->Text(2.5,24,'Traza:' . $codigo['traza'] );
    $pdf->Text(2.5,26.5,'Lote :' . $codigo['lote']);
    $pdf->Text(2.5,29,'Vto. :' . $codigo['venc']);
    $pdf->Text(2.5,31.5,'Despacho:');
    $pdf->Text(2.5,33.5, $codigo['despacho_nro']);
 
}

$pdf->Output();
 
 
