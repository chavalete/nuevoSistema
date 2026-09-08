<?php


require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'Barcode.php';
ob_end_clean();
require_once 'fpdf.php';

$db = NEW frenteAlmacenamiento();

$db->addSelect('cliente_nombre');
$db->addSelect('CASE WHEN factura_nro IS NULL THEN \'-------\' ELSE factura_nro END AS factura_nro');
$db->addSelect('CASE WHEN remito_nro IS NULL THEN \'-------\' ELSE remito_nro END AS remito_nro');
$db->addSelect('to_char(movimiento_fecha_hora::date,\'DD/MM/YYYY\') AS movimiento_fecha');
$db->addFrom('datos_movimientos dm');
$db->addFrom('INNER JOIN datos_clientes USING(cliente_id)');
$db->addWhere('id_movimiento = \'' . $_GET['crIdMovimiento']. '\'');
$db->generarSelect();
$arrCabecera =  $db->ejecutar();
//echo $db->getQry();
if(count($arrCabecera) ==0){
    echo "<h2>Sin registros para {$_GET['crIdMovimiento']} ";
    exit;
}


$pdf = new FPDF ('P','mm',array(210,297));

foreach ($arrCabecera as $codigo){
    
    $im     = imagecreatetruecolor(500,500);
    $black  = ImageColorAllocate($im,0x00,0x00,0x00);
    $white  = ImageColorAllocate($im,0xff,0xff,0xff);
    imagefilledrectangle($im, 0, 0,500, 500, $white);

    $code = $codigo['traza'];
    $data = Barcode::gd($im, $black, $x, $y, $angle, $type, array('code'=>$code), $width, $height);
    $file = "/tmp/traza_" . $codigo['traza'] . ".jpeg";
    imagejpeg($im, $file);
    imagedestroy($im);
    $pdf->AddPage();
    //$pdf->Image($file,-33,-31);
    //texto
    // Set font
    $pdf->SetFont('Arial','B',12);
    $pdf->Text(18,13, "Fecha: " . $codigo['movimiento_fecha'] );
    $pdf->SetFont('Arial','B',12);
    $pdf->Text(18,19, "Cliente: " . $codigo['cliente_nombre'] );
    $pdf->SetFont('Arial','B',12);
    $pdf->Text(18,25, "Nro Remito: " . $codigo['remito_nro'] );
    $pdf->Text(18,31, "Nro Factura: " . $codigo['factura_nro'] );
    
    
    
}

$db->addSelect('trazabilidad_codigo');
$db->addSelect('producto_nombre || \' \' || producto_presentacion AS producto_nombre');
$db->addSelect('lote');
$db->addSelect('to_char(lote_vencimiento,\'DD\MM\YYYY\') AS lote_vencimiento');
$db->addFrom('movimientos_trazabilidad');
$db->addFrom('INNER JOIN datos_trazabilidad USING(trazabilidad_id)');
$db->addFrom('INNER JOIN datos_lotes USING(lote_id)');
$db->addFrom('INNER JOIN datos_productos USING(producto_id)');
$db->addFrom('INNER JOIN detalle_movimientos USING(id_movimiento)');
$db->addWhere('id_movimiento = \'' .  $_GET['crIdMovimiento']. '\'');
$db->addGroup('trazabilidad_codigo,producto_nombre,lote,lote_vencimiento,producto_presentacion');
$db->addOrderBy('trazabilidad_codigo');
$db->generarSelect();

//echo $db->getQry();

$arrDetalles =  $db->ejecutar();

$alto= 70;

$pdf->SetFont('Arial','',12);
$pdf->Text(6,64, "Codigo Trazabilidad" );

$pdf->SetFont('Arial','',12);
$pdf->Text(54,64, "Lote" );

$pdf->SetFont('Arial','',12);
$pdf->Text(78,64, "Lote Vencimiento" );

$pdf->SetFont('Arial','',12);
$pdf->Text(133,64, "Producto" );


foreach ($arrDetalles as $detalle){
    
    
    $pdf->SetFont('Arial','',10);
    $pdf->Text(18,$alto, $detalle['trazabilidad_codigo'] );
        
    $pdf->SetFont('Arial','',10);
    $pdf->Text(52,$alto, $detalle['lote'] );

    $pdf->SetFont('Arial','',10);
    $pdf->Text(85,$alto, $detalle['lote_vencimiento'] );

    $pdf->SetFont('Arial','',9);
    $pdf->Text(120,$alto, $detalle['producto_nombre'] );


    $alto = $alto + 6;
    
    
}




$pdf->Output();

?>