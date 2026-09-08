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
$db->addSelect('producto_pventa');
$db->addSelect('producto_gtin');     
$db->addSelect('producto_presentacion');     
$db->addSelect('to_char(movimiento_fecha_hora,\'DD-MM-YYYY\') AS movimiento_fecha');     
$db->addSelect('dp.producto_nombre'); 

$db->addFrom('datos_movimientos dm');                            
$db->addFrom('INNER JOIN detalle_movimientos USING(id_movimiento)');
$db->addFrom('INNER JOIN datos_productos dp USING(producto_id)');                            
$db->addFrom('INNER JOIN datos_clientes dc ON (dm.cliente_id = dc.cliente_id)');                                     

$db->addWhere('id_movimiento = \'' . $_GET['crIdMovimiento']. '\'');                                                 
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
$pdf->Text(20,42, "Cliente: " . utf8_decode($arrCabecera[0]['cliente_nombre']) );
$pdf->Text(20,48, "Contacto Cliente: " . $arrCabecera[0]['cliente_contacto']);
$pdf->Text(20,54, utf8_decode("Télefono Cliente: ") . utf8_decode($arrCabecera[0]['cliente_telefono']) );


$pdf->Line(10, 62,200 , 62);
                                                                          
$pdf->Text(12,70, "Item" );
$pdf->Text(30,70, "DESCRIPCION" );
$pdf->Text(105,70, "UNIDAD" );
$pdf->Text(130,70, "Precio Unitario" );
$pdf->Text(160,70, "CANTIDAD." );
$pdf->Text(180,70, "TOTAL." );

$alto = 80;
$i=1;
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
    $pdf->Text(133,$alto, money_format('%(#2n', $detalle['producto_pventa'])  );
    $pdf->Text(180,$alto, money_format('%(#2n',$detalle['cantidad'] * $detalle['producto_pventa']));
    
    $i++;
    $alto = $alto + 5;
}

if($arrCabecera[0]['pedido_obs']!=NULL){
    $alto = $alto + 5;
    $pdf->Text(20,$alto, "Observaciones:" );
    $alto = $alto + 5;
    $pdf->Text(20,$alto, " " . utf8_decode($arrCabecera[0]['pedido_obs']) );
}

//EXTRAS
    $pdf->SetFont('Arial','B',10);
    $pdf->Text(20,250, utf8_decode("Conformidad"));
    $pdf->SetFont('Arial','',10);
    $pdf->Text(20,260, utf8_decode("FIRMA :"));
    $pdf->Text(20,270, utf8_decode("ACLARACION :"));
    $pdf->Text(20,280, utf8_decode("FECHA :"));
    $pdf->Text(20,290, utf8_decode("SELLO :"));



$pdf->Output();


    

