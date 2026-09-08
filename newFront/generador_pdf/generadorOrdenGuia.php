<?php                                                                                                                                                                            
//ALMACENAMIENTO                                                                                                                                                                 
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';

require_once 'Barcode.php';                                                                                                                                                      
ob_end_clean();                                                                                                                                                                  
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/fpdf/fpdf.php';                                                                                                  

$db = NEW frenteAlmacenamiento();
$objFuncionesComunes = new FuncionesComunes();
$db->addSelect('*, datos_guias.observaciones AS obsguia');
$db->addSelect('to_char(guia_fecha,\'DD/MM/YY\') AS guia_fecha');
$db->addFrom('datos_guias');
$db->addFrom('INNER JOIN datos_cuentas USING(cuenta_id)');                                     
$db->addFrom('INNER JOIN datos_clientes USING(cliente_id)');                                     
$db->addFrom('INNER JOIN relaciones_clientes_cuentas USING(relacion_id)');
$db->addFrom('INNER JOIN datos_pacientes USING(paciente_id)');             



if($_GET['crIdGuia']!=null){
    $db->addWhere('guia_id= \'' . $_GET['crIdGuia'] . '\'');
}
$db->generarSelect();                                                                                                
//echo $db->getQry();exit;
$arrPapa =  $db->ejecutar();                                                                                     
//echo $db->getQry();exit;                                                                                           
 if(count($arrPapa) ==0){                                                                                        
    echo "<h2>Sin registros para {$_GET['crIdCobro']} ";                                                            
    exit;                                                                                                            
}                                                                                                                    

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
$pdf->SetFont('Times','',14);
$pdf->Text(23,17, "Fecha: " .utf8_decode($arrPapa[0]['guia_fecha'])) ;
//orden
$pdf->SetFont('Times','',14);
$pdf->Text(23,22, "Orden Nro: " .utf8_decode($arrPapa[0]['guia_id'])) ;
$pdf->Text(23,27, "Tipo de orden: " .utf8_decode($arrPapa[0]['cuenta_nombre'])) ;
$pdf->Text(23,32, "Paciente: " .utf8_decode($arrPapa[0]['paciente_nombre'])) ;

//PARA CLIENTE
//izquierda
$pdf->Line(20, 12 ,20, 50);
//derecha
$pdf->Line(200, 12 ,200, 50);
//abajo
$pdf->Line(20, 50 ,200 , 50);

$pdf->SetFont('Times','',12);

//CLIENTE
$pdf->SetFont('Times','',12);   
$pdf->Text(23,40, "Cliente ; " . utf8_decode($arrPapa[0]['cliente_nombre'])) ;
//DIRECCION
$pdf->SetFont('Times','',12);
$pdf->Text(23,45, utf8_decode($arrPapa[0]['cliente_calle'] . " " . $arrPapa[0]['cliente_altura'] . " - " . $arrPapa[0]['localidad_nombre']) ) ;

//CUIT
$pdf->SetFont('Times','',12);
if($arrPapa[0]['cliente_cuit']!=null){
    $pdf->Text(100,40, "CUIT ; " . utf8_decode($arrPapa[0]['cliente_cuit'])) ;
}
//Cuenta
$pdf->SetFont('Times','',11);
$pdf->Text(100,45, "CUENTA ; " . utf8_decode($arrPapa[0]['relacion_nombre'])) ;

$pdf->SetFont('Times','',14);
$pdf->Text(80,55, utf8_decode("Composición de la guia")) ;

$alto = 63;
$pdf->SetFont('Times','',12);
$importeTotal=0;
foreach ($arrPapa as $detalle){
    
    $pdf->Text(62,$alto, "Maxilar Superior:");
    $pdf->Text(100,$alto, utf8_decode($detalle['maxilar_sup']."."));
    $alto = $alto + 5;
    $pdf->Text(62,$alto, "Maxilar Inferior:");
    $pdf->Text(100,$alto, utf8_decode($detalle['maxilar_inf']."."));
    $alto = $alto + 5;
    $pdf->Text(62,$alto, "Botones:");
    $pdf->Text(100,$alto, utf8_decode($detalle['botones']."."));
    $alto = $alto + 5;
    $pdf->Text(62,$alto, "Arcos:");
    $pdf->Text(100,$alto, utf8_decode($detalle['arcos']."."));
    $alto = $alto + 5;
    $pdf->Text(62,$alto, "Fotos:");
    $pdf->Text(100,$alto, utf8_decode($detalle['fotos']."."));
    $alto = $alto + 5;
    $pdf->Text(62,$alto, "STL:");
    $pdf->Text(100,$alto, utf8_decode($detalle['stl']."."));
    $alto = $alto + 5;
    $pdf->Text(62,$alto, "Estudios medicos:");
    $pdf->Text(100,$alto, utf8_decode($detalle['estudios_medicos']."."));
    $alto = $alto + 5;
    $importeTotal = $importeTotal +  $detalle['importe_pago'];
    
}
$pdf->Line(20, 12 ,20, $alto);
//derecha
$pdf->Line(200, 12 ,200, $alto);
//abajo
$pdf->Line(20, $alto ,200 , $alto);
//total
$alto = $alto + 4;
$pdf->Text(23,$alto, "Observaciones:") ;
$pdf->Text(52,$alto, utf8_decode($arrPapa[0]['obsguia']));

$alto = $alto + 4;
$pdf->Line(20, 12 ,20, $alto);
//derecha
$pdf->Line(200, 12 ,200, $alto);
//abajo
$pdf->Line(20, $alto ,200 , $alto);
/*
$alto = $alto + 4;

$pdf->SetFont('Times','',12);
$pdf->Text(82,$alto, utf8_decode("Detalle de Facturas")) ;
$alto = $alto + 5;
$pdf->SetFont('Times','',10);
$pdf->Text(55,$alto, "Fecha");
$pdf->Text(78,$alto, "Factura Nro");
$pdf->Text(103,$alto, "Importe Total");
$pdf->Text(130,$alto, "Importe Pendiente");
$alto = $alto + 5;

$db->addSelect('to_char(factura_fecha,\'DD/MM/YY\') AS factura_fecha');
$db->addSelect('factura_nro');
$db->addSelect('factura_total_fact');
$db->addSelect('importe_pendiente');
$db->addFrom('datos_cobros_facturas');
$db->addFrom('INNER JOIN datos_facturas USING(factura_id)');
$db->addWhere('cobro_id = \'' . $_GET['crIdCobro'] . '\'');
$db->generarSelect();
$resultadoFacturas = $db->ejecutar();

foreach($resultadoFacturas AS $facturas){

    $pdf->Text(53,$alto, utf8_decode($facturas['factura_fecha']));
    $pdf->Text(74,$alto, utf8_decode($facturas['factura_nro']));
    $pdf->Text(105,$alto, $objFuncionesComunes->formatoMoneda($facturas['factura_total_fact']));
    $pdf->Text(133,$alto, $objFuncionesComunes->formatoMoneda($facturas['importe_pendiente']));
    $alto = $alto + 3;
}*/
$pdf->Line(20, 12 ,20, $alto);
//derecha
$pdf->Line(200, 12 ,200, $alto);
//abajo
$pdf->Line(20, $alto ,200 , $alto);


$pdf->Output();
