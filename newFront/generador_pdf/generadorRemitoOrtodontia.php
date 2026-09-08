<?php                                                                                                                                                                            
//ALMACENAMIENTO                                                                                                                                                                 
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';                                                                        
require_once 'Barcode.php';                                                                                                                                                      
ob_end_clean();                                                                                                                                                                  
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/fpdf/fpdf.php';                                                                                                  

$db = NEW frenteAlmacenamiento();


$db->addSelect('dc.cliente_nombre');
$db->addSelect('dc.cliente_calle'); 
$db->addSelect('dc.cliente_altura');
$db->addSelect('dc.cliente_piso');  
$db->addSelect('dc.cliente_depto'); 
$db->addSelect('dc.cliente_codigo_postal');
$db->addSelect('dc.cliente_cuit');         
$db->addSelect('dc.cliente_contacto');         
$db->addSelect('dc.cliente_telefono');     
$db->addSelect('abs(cantidad) cantidad');
$db->addSelect('detalle_movimientos.producto_pventa');
$db->addSelect('detalle_movimientos.detalle_total_fact');
$db->addSelect('producto_gtin');     
$db->addSelect('dm.descuento AS descuento_total');     
$db->addSelect('detalle_movimientos.descuento AS descuento_producto');
$db->addSelect('producto_presentacion');     
$db->addSelect('to_char(movimiento_fecha_hora,\'DD-MM-YYYY\') AS movimiento_fecha');     
$db->addSelect('dp.producto_nombre'); 
$db->addSelect('dm.guia_id'); 
$db->addSelect('dm.remito_nro'); 


$db->addFrom('datos_movimientos dm');                            
$db->addFrom('INNER JOIN detalle_movimientos USING(id_movimiento)');
$db->addFrom('INNER JOIN datos_productos dp USING(producto_id)');
$db->addFrom('INNER JOIN datos_clientes dc ON (dm.cliente_id = dc.cliente_id)');                                     
$db->addFrom('LEFT JOIN datos_facturas ON dm.id_movimiento = datos_facturas.id_movimiento');    

if($_GET['crIdMovimiento']!=null){
    $db->addWhere('dm.id_movimiento = \'' . $_GET['crIdMovimiento']. '\'');
}else{
    $db->addWhere('factura_id = \'' . $_GET['crIdFactura']. '\'');
}
$db->generarSelect();                                                                                                
$arrCabecera =  $db->ejecutar();                                                                                     
//echo $db->getQry();exit;                                                                                           
 if(count($arrCabecera) ==0){                                                                                        
    echo "<h2>Sin registros para {$_GET['crIdPedido']} ";                                                            
    exit;                                                                                                            
}                                                                                                                    

//var_dump($arrCabecera); exit;
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


$pdf->SetFont('Arial','',8);
$pdf->Line(10, 31,200 , 31);

$pdf->Text(20,36, "Fecha: ");
$pdf->Text(35,36, "" . utf8_decode($arrCabecera[0]['movimiento_fecha']) );
$pdf->Text(120,36, "Remito Nro: ");
$pdf->Text(140,36, "" . utf8_decode($arrCabecera[0]['remito_nro']) );
$pdf->Text(20,42, "Cliente: " . utf8_decode($arrCabecera[0]['cliente_nombre']) );
$pdf->Text(20,48, "Contacto Cliente: " . $arrCabecera[0]['cliente_contacto']);
$pdf->Text(20,54, utf8_decode("Télefono Cliente: ") . utf8_decode($arrCabecera[0]['cliente_telefono']) );


$pdf->Line(10, 62,200 , 62);
                                                                          
$pdf->Text(12,70, "ITEM" );
$pdf->Text(30,70, "DESCRIPCION" );
$pdf->Text(105,70, "UNIDAD" );
$pdf->Text(130,70, "PRECIO UNITARIO" );
$pdf->Text(160,70, "CANTIDAD." );
$pdf->Text(180,70, "TOTAL." );


$alto = 80;
$i=1;
$cantidadCodigos=0;
$importeBonificacion=0;
foreach ($arrCabecera as $detalle){
    
    
    $pdf->SetFont('Arial','',8);
    $pdf->Text(14,$alto, $i);
    
    $pdf->SetFont('Arial','',8);
    $pdf->Text(30,$alto, utf8_decode($detalle['producto_nombre']) );
    
    $pdf->SetFont('Arial','',8);
    $pdf->Text(104,$alto, $detalle['producto_presentacion']  );
    
    
    $pdf->SetFont('Arial','',8);
    setlocale(LC_MONETARY, 'en_US');
    $pdf->Text(133,$alto, money_format('%(#2n', $detalle['producto_pventa'])  );

    $pdf->SetFont('Arial','',8);
    $pdf->Text(165,$alto, $detalle['cantidad'] );
    
    $pdf->SetFont('Arial','',8);
    //$pdf->Text(135,$alto, money_format('%(#2n', $detalle['producto_pventa'])  );
    $pdf->Text(180,$alto, money_format('%(#2n',$detalle['detalle_total_fact']));
    $total = $total +  $detalle['detalle_total_fact'];
    $i++;
    $alto = $alto + 4; 
    $cantidadCodigos++;

    $importeBonificacion = $importeBonificacion + ($detalle['detalle_total_fact'] * $detalle ['descuento_producto'] / 100);
    
    if($cantidadCodigos==45 || $cantidadCodigos==115){
	$pdf->AddPage();;
	
	$alto= 10;

	$pdf->Text(12,10, "ITEM" );
	$pdf->Text(30,10, "DESCRIPCION" );
	$pdf->Text(105,10, "UNIDAD" );
	$pdf->Text(130,10, "PRECIO UNITARIO" );
	$pdf->Text(160,10, "CANTIDAD." );
	$pdf->Text(180,10, "TOTAL." );
	
	
	$alto = 16;
    
    }
}
//ahroa este if tiene que contemprar que descuento_total y importeBonificacion == 0
if($arrCabecera[0]['descuento_total']==0 && $importeBonificacion ==0){
    //primero preguntas por desceunto total si si
    $pdf->SetFont('Arial','B',8);
    $pdf->Line(10, $alto,200 , $alto);
    $alto = $alto + 5;
    $pdf->Text(160,$alto, "Total:" );
    $pdf->Text(180,$alto, money_format('%(#2n',$total));
    
}else{

    //descuento_total <> 0 mostras
    if ($arrCabecera[0]['descuento_total']!= 0){
    
    $pdf->Line(10, $alto,200 , $alto);
    $alto = $alto + 5;
    $pdf->Text(160,$alto, "Subtotal:" );
    $pdf->Text(180,$alto, money_format('%(#2n',$total));

   $descuento = $total * $arrCabecera[0]['descuento_total'] / 100;

    $alto = $alto + 5;
    $pdf->Text(160,$alto, "Descuento" . $arrCabecera[0]['descuento_total'] ."%:" );
    $pdf->Text(180,$alto, money_format('%(#2n',$descuento));
    
    $alto = $alto + 5;
    $pdf->SetFont('Arial','B',8);
    $pdf->Text(160,$alto, "Total:" );
    $pdf->Text(180,$alto, money_format('%(#2n',$total - $descuento));

    }else{ //tenemos que mostrar el importe bonificado y el total
        $alto = $alto + 5;
        $pdf->SetFont('Arial','B',8);
        $pdf->Text(160,$alto, "Total:" );
        $pdf->Text(180,$alto, money_format('%(#2n',$total));


	$alto = $alto + 5;
        $pdf->Text(160,$alto, utf8_decode("Bonificación"));
        $pdf->Text(180,$alto, money_format('%(#2n',$importeBonificacion));
        
       

    }
}    

//EXTRAS
    $pdf->SetFont('Arial','B',10);
    $pdf->Text(20,250,utf8_decode ("Conformidad"));
    $pdf->SetFont('Arial','',10);
    $pdf->Text(20,260, utf8_decode("FIRMA :"));
    $pdf->Text(20,270, utf8_decode("ACLARACION :"));
    $pdf->Text(20,280, utf8_decode("FECHA :"));
    $pdf->Text(20,290, utf8_decode("SELLO :"));

if($arrCabecera[0]['guia_id']>0){

    $db->addSelect('*');
    $db->addSelect('to_char(guia_fecha,\'DD/MM/YY\') AS guia_fecha');
    $db->addFrom('datos_guias');
    $db->addFrom('INNER JOIN datos_cuentas USING(cuenta_id)');                                     
    $db->addFrom('INNER JOIN datos_clientes USING(cliente_id)');                                     
    $db->addFrom('INNER JOIN relaciones_clientes_cuentas USING(relacion_id)');
    $db->addFrom('INNER JOIN datos_pacientes USING(paciente_id)');             
    $db->addWhere('guia_id= \'' . $arrCabecera[0]['guia_id'] . '\'');
    $db->generarSelect();                                                                                                
    //echo $db->getQry();exit;
    $arrPapa =  $db->ejecutar();                                                                                     
    $pdf->AddPage();
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
}

$pdf->Output();


    

