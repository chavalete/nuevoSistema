<?php                                                                                                                                                                            
//ALMACENAMIENTO                                                                                                                                                                 
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';

require_once 'Barcode.php';                                                                                                                                                      
ob_end_clean();                                                                                                                                                                  
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/fpdf/fpdf.php';                                                                                                  

$db = NEW frenteAlmacenamiento();
$objFuncionesComunes = new FuncionesComunes();
$qry="SELECT lista_id FROM datos_listas_precios WHERE lista_mayorista=true AND lista_cancelada=false;";
$db->setQry($qry);
$resultadoListaId=$db->ejecutar();
$id=$resultadoListaId[0][lista_id];
$qry="SELECT sum(ls.producto_pventa * cantidad) AS total_venta, sum(producto_pcosto_dolar*cantidad) AS total_costo FROM stock_completo INNER JOIN datos_productos USING(producto_id) INNER JOIN datos_tipos_productos USING(tipo_producto_id) INNER JOIN lista_precios_$id ls USING(producto_id) WHERE cantidad>0 AND excluir_reporte_stock=false;";

$db->setQry($qry);
$resultadoTotales=$db->ejecutar();


$db->addSelect('sum(importe_deuda) AS importe_deuda'); 
$db->addFrom('vw_deuda_producto_ortodontia');


$db->generarSelect();
//echo $db->getQry();exit;
$resultadoDeuda =  $db->ejecutar();    

//var_dump($arrPapa); exit;
$pdf = new FPDF ('P','mm',array(210,297));
$pdf->AddPage();                          

//CABECERA
$fontSize = 10;
$marge    = 10;   // between barcode and hri in pixel
$x        = 100;  // barcode center                  
$y        = 20;  // barcode center                   
$height   = 10;   // barcode height in 1D ; module size in 2D
$width    = 0.9;    // barcode height in 1D ; not use in 2D  
$angle    = 0;   // rotation in degrees                      
$black    = '000000'; // color in hexa                       
$type     = 'code128';                                       


$pdf->Image('ortodontia_negro_azul.jpg', 115, 10, 65, 25, 'jpg');

//arriba
$pdf->Line(20, 12 ,200 , 12);
//izquierda
$pdf->Line(20, 12 ,20, 35);
//derecha
$pdf->Line(200, 12 ,200, 35);
//abajo
$pdf->Line(20, 35 ,200 , 35);
//medio
$pdf->Line(100, 12 ,100, 35);
//fecha
$pdf->SetFont('Times','',12);
$today = getdate();
$pdf->Text(23,22, "FECHA: " . $today['mday'] . "/" . $today['mon'] . "/" . $today['year']);
//orden
$pdf->SetFont('Times','',12);
$pdf->Text(23,27, "REPORTE DE VALOR STOCK Y DEUDA");


$pdf->SetFont('Times','',14);
$pdf->Text(85,40, utf8_decode("Composición")) ;

$alto = 50;
$pdf->SetFont('Times','',12);
$importeTotal=0;

    
$pdf->Text(23,$alto, "TOTAL STOCK VENTA");

$pdf->Text(80,$alto, "TOTAL STOCK COSTO");
$pdf->Text(150,$alto, "TOTAL DEUDA");

$alto = $alto + 10;
$pdf->Text(30,$alto, $objFuncionesComunes->formatoMoneda($resultadoTotales[0]['total_venta']));
$pdf->Text(90 ,$alto, $objFuncionesComunes->formatoMonedaExtranjera($resultadoTotales[0]['total_costo']));
$pdf->Text(153 ,$alto, $objFuncionesComunes->formatoMoneda($resultadoDeuda[0]['importe_deuda']));

$alto = $alto + 10;

$pdf->Line(20, 12 ,20, $alto);
//derecha
$pdf->Line(200, 12 ,200, $alto);
//abajo
$pdf->Line(20, $alto ,200 , $alto);


$pdf->Output();
