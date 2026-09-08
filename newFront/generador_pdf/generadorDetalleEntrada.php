<?php

//ALMACENAMIENTO
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'Barcode.php';
ob_end_clean();
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/fpdf/fpdf.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/includes/FuncionesComunes.php'; 

$db = NEW frenteAlmacenamiento();
$objFuncionesComunes =  new FuncionesComunes();
$db->addSelect('dm.id_movimiento');
$db->addSelect('dm.movimiento_fecha_hora::date AS movimiento_fecha');
$db->addSelect('dprov.prove_nombre');
$db->addSelect('tmt.tipo_movimiento_nombre');
$db->addSelect('dcate.categoria_nombre');
$db->addSelect('dm.factura_nro');
$db->addSelect('dm.remito_nro');
//$db->addSelect('');
$db->addFrom('datos_movimientos dm');
$db->addFrom('LEFT JOIN datos_proveedores dprov ON (dm.prove_id=dprov.prove_id)');
$db->addFrom('INNER JOIN tipos_movimientos_trazas tmt ON (tmt.tipo_movimiento_id=dm.tipo_movimiento_id)');
$db->addFrom('LEFT JOIN datos_categorias dcate ON (dprov.categoria_id = dcate.categoria_id)');
$db->addFrom('');
$db->addWhere('id_movimiento = \'' . $_GET['crIdMovimiento']. '\'');
$db->generarSelect();
$arrCabecera =  $db->ejecutar();
//echo $db->getQry();//exit;
 if(count($arrCabecera) ==0){
    echo "<h2>Sin registros para {$_GET['crIdMovimiento']} ";
    exit;
}

//var_dump($arrCabecera); exit;
$pdf = new FPDF ('P','mm',array(210,297));
$pdf->AddPage();

//CABECERA
$fontSize = 10;
$marge    = 10;   // between barcode and hri in pixel
$x        = 110;  // barcode center
$y        = 20;  // barcode center
$height   = 10;   // barcode height in 1D ; module size in 2D
$width    = 0.9;    // barcode height in 1D ; not use in 2D
$angle    = 0;   // rotation in degrees
$black    = '000000'; // color in hexa
$type     = 'code128';

$code = $arrCabecera[0]['id_movimiento'];
//$data = Barcode::fpdf($pdf, $black, $x, $y, $angle, $type, array('code'=>$code), $width, $height);  


$pdf->SetFont('Arial','',10);
$pdf->Text(20,34, $arrCabecera[0]['tipo_movimiento_nombre'] . " Nro.: " . $arrCabecera[0]['id_movimiento'] );
$pdf->Text(20,38, "Fecha : " . $arrCabecera[0]['movimiento_fecha'] );


$pdf->Line(10, 43 ,200 , 43);

$pdf->Text(20,47, "Proveedor : ");
$pdf->SetFont('Arial','B',10);
$pdf->Text(40,47, "" . utf8_decode($arrCabecera[0]['prove_nombre']) );

$pdf->SetFont('Arial','',10);
$pdf->Text(20,52, "Categoria: " . utf8_decode($arrCabecera[0]['categoria_nombre']) );

$pdf->Text(20,57, "Factura Nro: " . utf8_decode($arrCabecera[0]['factura_nro']) );
$pdf->Text(20,62, "Remito Nro: " . utf8_decode($arrCabecera[0]['remito_nro']) );

$pdf->Line(10, 67,200 , 67);


//DETALLES
$db->addSelect('dprod.producto_nombre || \' \' || dprod.producto_presentacion AS producto_nombre');
$db->addSelect('CASE WHEN dem.producto_pcosto >0 THEN dem.producto_pcosto ELSE dem.producto_pventa END AS valor ');
//$d//b->addSelect('to_char(dl.lote_vencimiento, \'DD/MM/YYYY\') AS lote_vencimiento');
$db->addSelect('cantidad');
$db->addSelect('dprod.codigo_referencia');

$db->addFrom('detalle_movimientos dem');
$db->addFrom('INNER JOIN datos_productos dprod USING(producto_id)');
//$db->addFrom('INNER JOIN datos_lotes dl USING(lote_id)');
$db->addWhere('id_movimiento = \'' . $arrCabecera[0]['id_movimiento']  . '\'');
//$db->addGroup('dl.lote, to_char(dl.lote_vencimiento, \'DD/MM/YYYY\') ,dprod.producto_gtin, dprod.producto_nombre || \' \' || dprod.producto_presentacion,dprod.codigo_referencia');
$db->addOrderBy('producto_nombre ASC');
$db->generarSelect();

//echo $db->getQry();//exit;

$arrDetalles =  $db->ejecutar();

$pdf->SetFont('Arial','',10);
$pdf->Text(10,80, "DESCRIPCION" );
$pdf->Text(115,80, "CANTIDAD." );
$pdf->Text(150,80, "PRECIO." );
$pdf->Text(180,80, "IMPORTE." );

$alto = 90;
$i=1;
$importeTotal = 0;
foreach ($arrDetalles as $detalle){
    $pdf->SetFont('Arial','',10);
    $pdf->Text(10,$alto, utf8_decode($detalle['codigo_referencia'] . ' - ' . $detalle['producto_nombre']) );

    $pdf->SetFont('Arial','',10);
    $pdf->Text(120,$alto, $detalle['cantidad'] );

    $pdf->Text(149,$alto, $objFuncionesComunes->formatoMoneda($detalle['valor']) );
    
    $importe = $detalle['cantidad'] *  $detalle['valor'];
    
    $pdf->Text(178,$alto, $objFuncionesComunes->formatoMoneda($importe) );
    
    $importeTotal = $importeTotal + $importe;
    $i++;

    $alto = $alto + 5;
    if($i==40 || $i==95){
	$pdf->AddPage();;
	
	$alto= 10;

	$pdf->Text(20,10, "DESCRIPCION" );

	$pdf->Text(180,10, "CANTIDAD." );
	
	$alto = 16;
    
    }


}
$pdf->Line(10, $alto ,200 , $alto);

$pdf->Text(168,$alto + 10, "Total: " . $objFuncionesComunes->formatoMoneda($importeTotal ));

if($arrCabecera[0]['pedido_obs']!=NULL){
    $alto = $alto + 5;
    $pdf->Text(20,$alto, "Observaciones:" );
    $alto = $alto + 5;
    $pdf->Text(20,$alto, " " . utf8_decode($arrCabecera[0]['pedido_obs']) );
}



$pdf->Output();
