<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'php-barcode.php';
ob_end_clean();
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/fpdf/fpdf.php';

$db = NEW frenteAlmacenamiento();
$db->addSelect('dc.cliente_nombre');
$db->addSelect('dp.pedido_obs');
$db->addSelect('dp.pedido_nro');
$db->addSelect('dp.pedido_id');
$db->addSelect('dp.pedido_nro_ext');
$db->addSelect('to_char(dp.pedido_fecha_hora, \'DD / MM / YYYY hh:mm\') AS pedido_fecha_hora');
$db->addSelect('dprod.producto_nombre || \' \' || dprod.producto_presentacion AS producto_nombre_completo');
$db->addSelect('dprod.codigo_referencia');
$db->addSelect('ABS(pl.cantidad) AS cantidad');
$db->addSelect('dl.lote');
$db->addSelect('de.estanteria_nombre');
$db->addSelect('dp.pedido_cancelado');
$db->addSelect('dp.pedido_factura_nro');
$db->addSelect('dp.pedido_remito_nro');
$db->addSelect('paciente_nombre');
$db->addFrom('datos_pedidos dp');
$db->addFrom('INNER JOIN detalle_pedidos dep USING (pedido_id)');
$db->addFrom('INNER JOIN pedidos_lotes pl ON (dep.pedido_detalle_id = pl.pedido_detalle_id)');
$db->addFrom('INNER JOIN datos_productos dprod ON (dprod.producto_id=dep.producto_id)');
$db->addFrom('INNER JOIN datos_lotes dl ON (pl.lote_id=dl.lote_id)');
$db->addFrom('INNER JOIN datos_estanterias de ON (pl.estanteria_id=de.estanteria_id)');
$db->addFrom('INNER JOIN datos_clientes dc ON (dp.cliente_id = dc.cliente_id)');
$db->addFrom('LEFT JOIN datos_pacientes USING(paciente_id)');

$db->addWhere('dp.pedido_id = \'' . $_GET['crPedidoId']. '\'');

$db->generarSelect();
$arrParametros =  $db->ejecutar();
//echo $db->getQry();exit;
if(count($arrParametros) ==0){
    echo "<h2>Sin registros para {$_GET['crPedidoId']} ";
    exit;
}

//var_dump($arrCabecera); exit;
$pdf = new FPDF ('P','mm',array(210,297));
$pdf->AddPage();

$fontSize = 10;
$marge    = 10;   // between barcode and hri in pixel
$x        = 105;  // barcode center
$y        = 25;  // barcode center
$height   = 12;   // barcode height in 1D ; module size in 2D
$width    = 1;    // barcode height in 1D ; not use in 2D
$angle    = 0;   // rotation in degrees
$black    = '000000'; // color in hexa
$type     = 'code128';

$code = $arrParametros[0]['pedido_id']; //str_pad($arrParametros[0]['pedido_id'], 12, "0", STR_PAD_LEFT);
$data = Barcode::fpdf($pdf, $black, $x, $y, $angle, $type, array('code'=>$code), $width, $height);  

$pdf->Line(10, 50,200 , 50);
//CABECERA

$pdf->SetFont('Arial','',10);
$pdf->Text(10,55, "Pedido Nro.: "); 
$pdf->SetFont('Arial','B',10);
$pdf->Text(40,55,  utf8_decode($arrParametros[0]['pedido_nro']) );

$pdf->SetFont('Arial','',10);
$pdf->Text(10,60, "Fecha : " . $arrParametros[0]['pedido_fecha_hora'] );

$pdf->Text(10,65, "Remito Nro. : " );
$pdf->SetFont('Arial','B',10);
$pdf->Text(35,65, " " . utf8_decode($arrParametros[0]['pedido_remito_nro']) );

$pdf->SetFont('Arial','',10);
$pdf->Text(10,70, "Factura Nro.: " );
$pdf->SetFont('Arial','B',10);
$pdf->Text(35,70, " " . utf8_decode($arrParametros[0]['pedido_factura_nro']) );


$pdf->Line(10, 75 ,200 , 75);

$pdf->SetFont('Arial','',10);
$pdf->Text(10,80, "Cliente : ");
$pdf->SetFont('Arial','B',10);
$pdf->Text(30,80, $arrParametros[0]['cliente_nombre']);
$pdf->SetFont('Arial','',10);
$pdf->Text(10,85, "Paciente : ");
$pdf->SetFont('Arial','B',10);
$pdf->Text(30,85, $arrParametros[0]['paciente_nombre']);

$pdf->Line(10, 86 ,200 , 86);

$pdf->SetFont('Arial','',10);
$pdf->Text(10,95, "DESCRIPCION" );
$pdf->Text(140,95, "CANT" );
$pdf->Text(155,95, "ESTANT." );
$pdf->Text(180,95, "LOTE" );

$alto = 105;
$i=1;
foreach ($arrParametros as $detalle){
	
    $pdf->SetFont('Arial','',10);
    $pdf->Text(10,$alto, utf8_decode($detalle['producto_nombre_completo'] . ' - ' . $detalle['codigo_referencia']) );

    $pdf->SetFont('Arial','',10);
    $pdf->Text(145,$alto, $detalle['cantidad'] );

    $pdf->SetFont('Arial','',8);
    $pdf->Text(155,$alto, $detalle['estanteria_nombre'] );
    
    $pdf->SetFont('Arial','',8);
    $pdf->Text(180,$alto, $detalle['lote'] );

    $i++;

    $alto = $alto + 5;
}

$pdf->SetFont('Arial','',10);
if($arrParametros[0]['pedido_obs']!=NULL){
    $alto = $alto + 5;
    $pdf->Text(10,$alto, "Observaciones:" );
    $alto = $alto + 5;
    $pdf->Text(20,$alto, " " . utf8_decode($arrParametros[0]['pedido_obs']) );
}

if($arrParametros[0]['pedido_nro_ext']!=NULL){
    $alto = $alto + 5;
    $pdf->Text(10,$alto, "Pedido Nro Ext.: " );
    $pdf->SetFont('Arial','B',10);
    $pdf->Text(45,$alto, " " . utf8_decode($arrParametros[0]['pedido_nro_ext']) );
}

$pdf->Output();
