<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'Barcode.php';
ob_end_clean();
require_once 'fpdf.php';

$db = NEW frenteAlmacenamiento();

$db->addSelect("trazabilidad_codigo");
$db->addSelect("pm");
$db->addSelect("lote AS lote");
$db->addSelect("to_char(lote_vencimiento, 'DD-MM-YYYY') AS venc");
$db->addSelect("despacho_nro");
$db->addSelect("producto_nombre");
$db->addSelect("producto_presentacion  AS producto_detalle");
$db->addSelect("(prove_nombre || '-' || prove_direccion) AS prove_completo");
$db->addSelect("marca_nombre");
$db->addSelect("prove_nombre");
$db->addSelect("codigo_referencia");

    
$db->addFrom("	datos_trazabilidad dt 
		INNER JOIN movimientos_trazabilidad mt USING (trazabilidad_id)
		INNER JOIN datos_movimientos dm USING (id_movimiento)
		INNER JOIN datos_lotes dl USING (lote_id)
		INNER JOIN datos_productos dp USING (producto_id)
		INNER JOIN datos_proveedores dprov ON (dprov.prove_id=dm.prove_id)
		INNER JOIN datos_marcas dmar ON (dp.marca_id = dmar.marca_id)
             ");

$db->addWhere("id_movimiento = " . $_GET['crIdMovimiento']);
$db->addOrderBy('trazabilidad_id ASC');//generar qry
$db->generarSelect();   
//echo $db->getQry();
$codigos = $db->ejecutar();
if(count($codigos) ==0){
    echo "<h2>Sin registros para {$_GET['crIdMovimiento']} ";
    exit;
}

//configuracion para crear el codigo
$fontSize = 10;   // GD1 in px ; GD2 in point
$marge    = 10;   // between barcode and hri in pixel
$x        = 125;  // barcode center
$y        = 125;  // barcode center
$height   = 3;   // barcode height in 1D ; module size in 2D
$width    = 3;		
$angle    = 0; //
$type     = 'datamatrix';
//fin

$pdf = new FPDF ('P','mm',array(100,35));

foreach ($codigos as $codigo){

	$codigo['dato']  = '30711011508';
	$codigo['traza'] = $codigo['trazabilidad_codigo'];
	$codigo['lote']  = $codigo['lote'];
	$codigo['venc']  = $codigo['venc'];
	$codigo['despacho'] = 'Despacho :' . $codigo['despacho_nro'];
	$codigo['prove'] = $codigo['prove_completo'];
	$codigo['prove_nombre'] =  $codigo['prove_nombre'];
	$codigo['producto'] =  $codigo['producto_nombre']  . ' ' . $codigo['producto_detalle'] ; 
	$codigo['marca_nombre'] = $codigo['marca_nombre'];
	$codigo['pm']           = 'PM : ' . $codigo['pm'];
	$codigo['codigo_referencia']           = 'Nro. Lista : ' . $codigo['codigo_referencia'];

	
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
	$pdf->Image($file,-33,-31);
	//texto
	// Set font
	$pdf->SetFont('Arial','B',7);
	$pdf->Text(26.5,7, utf8_decode($codigo['producto']) );
	$pdf->SetFont('Arial','B',6);
	
	$pdf->SetFont('Arial','',7);
	$pdf->Text(26.5,10, $codigo['codigo_referencia']);
	$pdf->Text(26.5,13, utf8_decode($codigo['prove']));

	$pdf->Text(26.5,16 ,'Producto autorizado por ANMAT. ' . $codigo['pm'] );
	$pdf->Text(26.5,19 ,'Importado y distribuido por: Industria S.A.' );
	$pdf->Text(2.5,30.5, $codigo['despacho']);
	
	
	$pdf->Text(26.5,22 ,'Don Pancho 115, Avellaneda , Buenos Aires' );

	$pdf->SetFont('Arial','',7);
	$pdf->Text(26.5,24.5, 'Dir Tecnico Farm: Diego Fulano.  M.P. 19160');
	$pdf->Text(26.5,29.5,'Venta exclusiva a profesionales e instituciones sanitarias');
	
	$pdf->SetFont('Arial','',7);
	
	$pdf->Text(26.5,27,'Marca: ' .  $codigo['marca_nombre']);
	
	$pdf->SetFont('Arial','',7);
	$pdf->Text(3,3, '(01)' . $codigo['dato'] . '(21)' . $codigo['traza'] . '(17)' . $codigo['venc'] . '(10)' . $codigo['lote']);
	$pdf->Text(2.5,24,'Traza:' . $codigo['traza'] );
	$pdf->Text(2.5,26.5,'Lote :' . $codigo['lote']);
	$pdf->Text(2.5,28.5,'Vto. :' . $codigo['venc']);
 
}
$pdf->Output();

?>
